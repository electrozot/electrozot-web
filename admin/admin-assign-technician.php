<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  include('check-technician-availability.php'); // Include availability checker
  check_login();
  $aid=$_SESSION['a_id'];
  
  //Assign Technician
  if(isset($_POST['assign_technician']))
    {
            // Get sb_id from POST (hidden field) or GET
            $sb_id = isset($_POST['sb_id']) ? intval($_POST['sb_id']) : (isset($_GET['sb_id']) ? intval($_GET['sb_id']) : 0);
            $sb_technician_id = isset($_POST['sb_technician_id']) ? intval($_POST['sb_technician_id']) : 0;
            $sb_status = isset($_POST['sb_status']) ? trim($_POST['sb_status']) : '';
            
            // Get service deadline from form
            $service_deadline_date = isset($_POST['service_deadline_date']) ? $_POST['service_deadline_date'] : null;
            $service_deadline_time = isset($_POST['service_deadline_time']) ? $_POST['service_deadline_time'] : null;
            
            // Validation
            if($sb_id <= 0) {
                $err = "Booking ID is missing. Please try again.";
            } elseif($sb_technician_id <= 0) {
                $err = "Please select a technician.";
            } elseif(empty($sb_status)) {
                $err = "Please select a booking status.";
            } elseif(empty($service_deadline_date) || empty($service_deadline_time)) {
                $err = "Please set service deadline date and time.";
            } else {
                // STEP 1: Check if the new technician is available (not engaged with another booking)
                $new_tech_engagement = checkTechnicianEngagement($sb_technician_id, $mysqli);
                
                // Get the previously assigned technician (if any)
                $get_old_tech = "SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?";
                $old_tech_stmt = $mysqli->prepare($get_old_tech);
                $old_tech_stmt->bind_param('i', $sb_id);
                $old_tech_stmt->execute();
                $old_tech_result = $old_tech_stmt->get_result();
                $old_booking = $old_tech_result->fetch_object();
                $old_tech_id = $old_booking ? $old_booking->sb_technician_id : null;
                
                // STEP 2: Validate technician availability
                // If new technician is engaged with a different booking, reject the assignment
                if($new_tech_engagement['is_engaged'] && $new_tech_engagement['booking_id'] != $sb_id) {
                    $err = "Technician is currently engaged with Booking #" . $new_tech_engagement['booking_id'] . 
                           " (Status: " . $new_tech_engagement['booking_status'] . "). " .
                           "Please wait until they complete or reject that booking.";
                } else {
                
                // If technician is being changed (not first assignment)
                if($old_tech_id && $old_tech_id != $sb_technician_id) {
                    // Create cancelled booking table if not exists
                    $create_table = "CREATE TABLE IF NOT EXISTS tms_cancelled_bookings (
                        cb_id INT AUTO_INCREMENT PRIMARY KEY,
                        cb_booking_id INT NOT NULL,
                        cb_technician_id INT NOT NULL,
                        cb_cancelled_by VARCHAR(50) DEFAULT 'Admin',
                        cb_reason VARCHAR(255) DEFAULT 'Technician reassigned by admin',
                        cb_cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        INDEX(cb_booking_id),
                        INDEX(cb_technician_id)
                    )";
                    $mysqli->query($create_table);
                    
                    // Record the cancellation for old technician
                    $cancel_reason = "Admin reassigned booking to another technician";
                    $insert_cancel = "INSERT INTO tms_cancelled_bookings (cb_booking_id, cb_technician_id, cb_cancelled_by, cb_reason) 
                                     VALUES (?, ?, 'Admin', ?)";
                    $cancel_stmt = $mysqli->prepare($insert_cancel);
                    $cancel_stmt->bind_param('iis', $sb_id, $old_tech_id, $cancel_reason);
                    $cancel_stmt->execute();
                    
                    // Free up the old technician
                    $free_tech = "UPDATE tms_technician SET t_status='Available' WHERE t_id=?";
                    $free_stmt = $mysqli->prepare($free_tech);
                    $free_stmt->bind_param('i', $old_tech_id);
                    $free_stmt->execute();
                }
                
                // Add deadline columns if they don't exist
                $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_deadline_date DATE DEFAULT NULL");
                $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_deadline_time TIME DEFAULT NULL");
                
                // Update the booking with new technician and service deadline
                $query="UPDATE tms_service_booking SET sb_technician_id=?, sb_status=?, sb_service_deadline_date=?, sb_service_deadline_time=? WHERE sb_id=?";
                $stmt = $mysqli->prepare($query);
                
                if(!$stmt) {
                    $err = "Database error: " . $mysqli->error;
                } else {
                    $stmt->bind_param('isssi', $sb_technician_id, $sb_status, $service_deadline_date, $service_deadline_time, $sb_id);
                    $result = $stmt->execute();
                    
                    if($result && $stmt->affected_rows > 0) {
                        // Update technician status based on booking status
                        if($sb_status == 'Completed' || $sb_status == 'Cancelled' || $sb_status == 'Rejected') {
                            // Free the technician if booking is completed, cancelled, or rejected
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Available', 
                                              t_is_available=1, 
                                              t_current_booking_id=NULL 
                                          WHERE t_id=?";
                        } else if($sb_status == 'In Progress' || $sb_status == 'Approved' || $sb_status == 'Assigned') {
                            // Mark technician as booked if booking is in progress, approved, or assigned
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Booked', 
                                              t_is_available=0, 
                                              t_current_booking_id=? 
                                          WHERE t_id=?";
                        } else {
                            // For pending status, mark as booked but with pending status
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Booked', 
                                              t_is_available=0, 
                                              t_current_booking_id=? 
                                          WHERE t_id=?";
                        }
                        
                        if($update_tech && $sb_technician_id) {
                            $tech_stmt = $mysqli->prepare($update_tech);
                            if($tech_stmt) {
                                // Check if query needs booking_id parameter
                                if(strpos($update_tech, 't_current_booking_id=?') !== false) {
                                    $tech_stmt->bind_param('ii', $sb_id, $sb_technician_id);
                                } else {
                                    $tech_stmt->bind_param('i', $sb_technician_id);
                                }
                                $tech_stmt->execute();
                            }
                        }
                        
                        $succ = "Technician Assigned Successfully";
                        // Redirect to prevent form resubmission
                        header("Location: admin-assign-technician.php?sb_id=" . $sb_id . "&success=1");
                        exit();
                    } else {
                        $err = "Failed to assign technician. " . ($stmt->error ? $stmt->error : "No rows were updated.");
                    }
                }
                } // End of availability check
            }
    }
?>
 <!DOCTYPE html>
 <html lang="en">

 <?php include('vendor/inc/head.php');?>

 <body id="page-top">

     <?php include("vendor/inc/nav.php");?>

     <div id="wrapper">

         <!-- Sidebar -->
         <?php include('vendor/inc/sidebar.php');?>

         <div id="content-wrapper">

             <div class="container-fluid">
                 <?php if(isset($succ) || isset($_GET['success'])) {?>
                 <script>
                 setTimeout(function() {
                         swal("Success!", "Technician Assigned Successfully!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "error");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Service Bookings</a>
                     </li>
                     <li class="breadcrumb-item active">Assign Technician</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Assign Technician to Service Booking
                     </div>
                     <div class="card-body">
                         <?php
            $sb_id = isset($_GET['sb_id']) ? $_GET['sb_id'] : null;
            
            if(!$sb_id) {
                echo '<div class="alert alert-danger">Booking ID is missing. <a href="admin-manage-service-booking.php">Go back to bookings</a></div>';
            } else {
                $ret="SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name, s.s_category, t.t_name as assigned_tech
                      FROM tms_service_booking sb
                      LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                      LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                      LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                      WHERE sb.sb_id=?";
                $stmt= $mysqli->prepare($ret) ;
                $stmt->bind_param('i',$sb_id);
                $stmt->execute();
                $res=$stmt->get_result();
                $booking_data = $res->fetch_object();
                
                if(!$booking_data) {
                    echo '<div class="alert alert-danger">Booking not found. <a href="admin-manage-service-booking.php">Go back to bookings</a></div>';
                } else {
        ?>
                         <div class="row mb-3">
                             <div class="col-md-6">
                                 <h5>Booking Details</h5>
                                 <p><strong>Customer:</strong> <?php echo $booking_data->u_fname;?> <?php echo $booking_data->u_lname;?></p>
                                 <p><strong>Phone:</strong> <?php echo $booking_data->u_phone;?></p>
                                 <p><strong>Service:</strong> <?php echo $booking_data->s_name;?></p>
                                 <p><strong>Category:</strong> <?php echo $booking_data->s_category;?></p>
                                 <p><strong>Booking Date:</strong> <?php echo date('M d, Y', strtotime($booking_data->sb_booking_date));?></p>
                                 <p><strong>Booking Time:</strong> <?php echo date('h:i A', strtotime($booking_data->sb_booking_time));?></p>
                                 <p><strong>Address:</strong> <?php echo $booking_data->sb_address;?></p>
                                 <?php if($booking_data->sb_description): ?>
                                 <p><strong>Notes:</strong> <?php echo $booking_data->sb_description;?></p>
                                 <?php endif; ?>
                                 <p><strong>Status:</strong> 
                                     <span class="badge badge-<?php echo ($booking_data->sb_status == 'Pending') ? 'warning' : (($booking_data->sb_status == 'Approved') ? 'info' : 'success');?>">
                                         <?php echo $booking_data->sb_status;?>
                                     </span>
                                 </p>
                                 <?php if($booking_data->assigned_tech): ?>
                                 <p><strong>Currently Assigned:</strong> <?php echo $booking_data->assigned_tech;?></p>
                                 <?php endif; ?>
                             </div>
                         </div>
                         <hr>
                         
                         <?php 
                         // Check if technician is already assigned and booking is not rejected
                         $is_assigned = !empty($booking_data->sb_technician_id);
                         $is_rejected = ($booking_data->sb_status == 'Rejected' || $booking_data->sb_status == 'Cancelled');
                         $can_reassign = $is_rejected || !$is_assigned;
                         
                         // Show warning if trying to reassign when not rejected
                         if($is_assigned && !$is_rejected):
                         ?>
                         <div class="alert alert-info">
                             <h5><i class="fas fa-info-circle"></i> Technician Already Assigned</h5>
                             <p><strong><?php echo $booking_data->assigned_tech;?></strong> is currently assigned to this booking.</p>
                             <p class="mb-0">
                                 <strong>Note:</strong> You can only reassign a technician if:
                                 <ul>
                                     <li>The technician rejects the booking</li>
                                     <li>The booking is cancelled</li>
                                     <li>You manually change the technician (use the option below if technician is not responding)</li>
                                 </ul>
                             </p>
                         </div>
                         
                         <div class="form-check mb-3">
                             <input class="form-check-input" type="checkbox" id="force_reassign" onclick="toggleReassignment()">
                             <label class="form-check-label" for="force_reassign">
                                 <strong>Allow Technician Change</strong> (Check this if the assigned technician is not responding or unable to complete the service)
                             </label>
                         </div>
                         <?php endif; ?>
                         
                         <form method="POST" id="assignForm">
                             <input type="hidden" name="sb_id" value="<?php echo $sb_id; ?>">
                             
                             <fieldset id="formFieldset" <?php echo ($is_assigned && !$is_rejected) ? 'disabled' : ''; ?>>
                             <div class="form-group">
                                 <label for="sb_technician_id">Select Technician *</label>
                                 <select class="form-control" name="sb_technician_id" id="sb_technician_id" required>
                                     <option value="">Select Technician</option>
                                     <?php
                                     // Handle NULL technician_id
                                     $current_tech_id = $booking_data->sb_technician_id ? $booking_data->sb_technician_id : 0;
                                     
                                     // Get available technicians using the new availability checker
                                     // This ensures only technicians who are NOT engaged with other bookings are shown
                                     $available_techs = getAvailableTechnicians($booking_data->s_category, $mysqli, $sb_id);
                                     
                                     // Also try matching by service name if category doesn't match
                                     if(empty($available_techs)) {
                                         $available_techs = getAvailableTechnicians($booking_data->s_name, $mysqli, $sb_id);
                                     }
                                     
                                     if(empty($available_techs)) {
                                         // NO AVAILABLE TECHNICIANS - Show clear message
                                         echo '<option value="" disabled style="color: red;">⚠️ No available technicians for: '.$booking_data->s_category.'</option>';
                                         echo '<option value="" disabled>All technicians are currently engaged with other bookings</option>';
                                     } else {
                                         // Show only available technicians
                                         foreach($available_techs as $tech) {
                                             $selected = ($tech['t_id'] == $current_tech_id) ? 'selected' : '';
                                             $status_note = '';
                                             
                                             // Show match type and skills
                                             if(isset($tech['match_type']) && $tech['match_type'] == 'skill') {
                                                 $status_note = ' ✓ SKILL MATCH';
                                             } elseif(isset($tech['match_type']) && $tech['match_type'] == 'category') {
                                                 $status_note = ' - Category Match';
                                             } else {
                                                 $status_note = '';
                                             }
                                             
                                             if($tech['t_id'] == $current_tech_id) {
                                                 $status_note .= ' (Currently Assigned)';
                                             } elseif($tech['current_booking']) {
                                                 $status_note .= ' (Assigned to this booking)';
                                             }
                                             
                                             echo '<option value="'.$tech['t_id'].'" '.$selected.'>';
                                             echo htmlspecialchars($tech['t_name']) . ' - ' . htmlspecialchars($tech['t_specialization']) . $status_note;
                                             
                                             // Show matched skills if available
                                             if(!empty($tech['skills'])) {
                                                 echo ' | Skills: ' . htmlspecialchars(substr($tech['skills'], 0, 40));
                                                 if(strlen($tech['skills']) > 40) echo '...';
                                             }
                                             
                                             echo '</option>';
                                         }
                                     }
                                     ?>
                                 </select>
                                 <small class="form-text text-muted">
                                     <strong>Service:</strong> <?php echo $booking_data->s_name;?> 
                                     | <strong>Category:</strong> <?php echo $booking_data->s_category;?>
                                     <?php
                                     // Count truly available technicians (not engaged with other bookings)
                                     $tech_count = count($available_techs);
                                     
                                     if($tech_count == 0) {
                                         echo '<br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> No available technicians! All are currently engaged with other bookings.</span>';
                                     } else {
                                         echo '<br><span class="text-success"><i class="fas fa-check-circle"></i> '.$tech_count.' technician(s) available (not engaged)</span>';
                                     }
                                     ?>
                                 </small>
                                 
                                 <?php if($tech_count == 0): ?>
                                 <div class="alert alert-warning mt-2">
                                     <strong><i class="fas fa-info-circle"></i> No Technicians Available</strong><br>
                                     There are no technicians matching "<strong><?php echo $booking_data->s_name;?></strong>" or category "<strong><?php echo $booking_data->s_category;?></strong>" currently available.<br><br>
                                     <strong>Solutions:</strong>
                                     <ul class="mb-0">
                                         <li>Add a new technician with matching category: <a href="admin-add-technician.php" class="alert-link">Add Technician</a></li>
                                         <li>Update existing technician's category: <a href="admin-manage-technician.php" class="alert-link">Manage Technicians</a></li>
                                         <li>Wait for assigned technicians to become available</li>
                                     </ul>
                                 </div>
                                 <?php endif; ?>
                             </div>
                             <div class="form-group">
                                 <label for="sb_status">Booking Status *</label>
                                 <select class="form-control" name="sb_status" id="sb_status" required>
                                     <option value="Pending" <?php echo ($booking_data->sb_status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                     <option value="Approved" <?php echo ($booking_data->sb_status == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                     <option value="In Progress" <?php echo ($booking_data->sb_status == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                     <option value="Completed" <?php echo ($booking_data->sb_status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                     <option value="Cancelled" <?php echo ($booking_data->sb_status == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                 </select>
                             </div>
                             
                             <div class="alert alert-info">
                                 <i class="fas fa-clock"></i> <strong>Set Service Completion Deadline</strong><br>
                                 <small>This deadline will be shown to the technician and customer</small>
                             </div>
                             
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="service_deadline_date">Service Deadline Date *</label>
                                         <input type="date" class="form-control" name="service_deadline_date" id="service_deadline_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($booking_data->sb_service_deadline_date) ? $booking_data->sb_service_deadline_date : date('Y-m-d', strtotime('+1 day')); ?>">
                                         <small class="form-text text-muted">When should service be completed?</small>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="service_deadline_time">Deadline Time *</label>
                                         <input type="time" class="form-control" name="service_deadline_time" id="service_deadline_time" required value="<?php echo isset($booking_data->sb_service_deadline_time) ? $booking_data->sb_service_deadline_time : '18:00'; ?>">
                                         <small class="form-text text-muted">Completion time</small>
                                     </div>
                                 </div>
                             </div>
                             </fieldset>
                             
                             <hr>
                             <button type="submit" name="assign_technician" class="btn btn-success" id="submitBtn">
                                 <?php echo $is_assigned ? 'Change Technician' : 'Assign Technician'; ?>
                             </button>
                             <a href="admin-manage-service-booking.php" class="btn btn-secondary">Cancel</a>
                         </form>
                         
                         <script>
                         function toggleReassignment() {
                             var checkbox = document.getElementById('force_reassign');
                             var fieldset = document.getElementById('formFieldset');
                             var submitBtn = document.getElementById('submitBtn');
                             
                             if(checkbox.checked) {
                                 fieldset.disabled = false;
                                 submitBtn.disabled = false;
                             } else {
                                 fieldset.disabled = true;
                                 submitBtn.disabled = true;
                             }
                         }
                         
                         // Initialize button state on page load
                         <?php if($is_assigned && !$is_rejected): ?>
                         document.getElementById('submitBtn').disabled = true;
                         <?php endif; ?>
                         </script>
                         <?php 
                         } // End if booking_data exists
                         } // End if sb_id exists
                         ?>
                     </div>
                 </div>

                 <hr>

                 <!-- Sticky Footer -->
                 <?php include("vendor/inc/footer.php");?>

             </div>
             <!-- /.content-wrapper -->

         </div>
         <!-- /#wrapper -->
         <!-- Scroll to Top Button-->
         <a class="scroll-to-top rounded" href="#page-top">
             <i class="fas fa-angle-up"></i>
         </a>

         <!-- Logout Modal-->
         <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
             <div class="modal-dialog" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                         <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">×</span>
                         </button>
                     </div>
                     <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                     <div class="modal-footer">
                         <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                         <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Bootstrap core JavaScript-->
         <script src="vendor/jquery/jquery.min.js"></script>
         <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

         <!-- Core plugin JavaScript-->
         <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

         <!-- Page level plugin JavaScript-->
         <script src="vendor/chart.js/Chart.min.js"></script>
         <script src="vendor/datatables/jquery.dataTables.js"></script>
         <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

         <!-- Custom scripts for all pages-->
         <script src="vendor/js/sb-admin.min.js"></script>

         <!-- Demo scripts for this page-->
         <script src="vendor/js/demo/datatables-demo.js"></script>
         <script src="vendor/js/demo/chart-area-demo.js"></script>
         <script src="vendor/js/swal.js"></script>

 </body>

 </html>

