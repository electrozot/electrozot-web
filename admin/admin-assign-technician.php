<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  //Assign Technician
  if(isset($_POST['assign_technician']))
    {
            // Get sb_id from POST (hidden field) or GET
            $sb_id = isset($_POST['sb_id']) ? $_POST['sb_id'] : (isset($_GET['sb_id']) ? $_GET['sb_id'] : null);
            $sb_technician_id = $_POST['sb_technician_id'];
            $sb_status = $_POST['sb_status'];
            
            if(!$sb_id) {
                $err = "Booking ID is missing. Please try again.";
            } else {
                // First, get the previously assigned technician (if any) to free them up
                $get_old_tech = "SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?";
                $old_tech_stmt = $mysqli->prepare($get_old_tech);
                $old_tech_stmt->bind_param('i', $sb_id);
                $old_tech_stmt->execute();
                $old_tech_result = $old_tech_stmt->get_result();
                $old_booking = $old_tech_result->fetch_object();
                
                // Free up the old technician if they were assigned
                if($old_booking && $old_booking->sb_technician_id) {
                    $free_tech = "UPDATE tms_technician SET t_status='Available' WHERE t_id=?";
                    $free_stmt = $mysqli->prepare($free_tech);
                    $free_stmt->bind_param('i', $old_booking->sb_technician_id);
                    $free_stmt->execute();
                }
                
                // Update the booking with new technician
                $query="UPDATE tms_service_booking SET sb_technician_id=?, sb_status=? WHERE sb_id=?";
                $stmt = $mysqli->prepare($query);
                
                if(!$stmt) {
                    $err = "Database error: " . $mysqli->error;
                } else {
                    $stmt->bind_param('isi', $sb_technician_id, $sb_status, $sb_id);
                    $result = $stmt->execute();
                    
                    if($result && $stmt->affected_rows > 0) {
                        // Update technician status based on booking status
                        if($sb_status == 'Completed' || $sb_status == 'Cancelled') {
                            // Free the technician if booking is completed or cancelled
                            $update_tech = "UPDATE tms_technician SET t_status='Available' WHERE t_id=?";
                        } else if($sb_status == 'In Progress' || $sb_status == 'Approved') {
                            // Mark technician as booked if booking is in progress or approved
                            $update_tech = "UPDATE tms_technician SET t_status='Booked' WHERE t_id=?";
                        } else {
                            // For pending status, keep technician available (or don't change status)
                            $update_tech = null;
                        }
                        
                        if($update_tech && $sb_technician_id) {
                            $tech_stmt = $mysqli->prepare($update_tech);
                            if($tech_stmt) {
                                $tech_stmt->bind_param('i', $sb_technician_id);
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
                         <form method="POST">
                             <input type="hidden" name="sb_id" value="<?php echo $sb_id; ?>">
                             <div class="form-group">
                                 <label for="sb_technician_id">Select Technician *</label>
                                 <select class="form-control" name="sb_technician_id" id="sb_technician_id" required>
                                     <option value="">Select Technician</option>
                                     <?php
                                     // Handle NULL technician_id
                                     $current_tech_id = $booking_data->sb_technician_id ? $booking_data->sb_technician_id : 0;
                                     
                                     if($current_tech_id > 0) {
                                         $tech_query = "SELECT * FROM tms_technician WHERE t_category = ? AND (t_status = 'Available' OR t_id = ?)";
                                         $tech_stmt = $mysqli->prepare($tech_query);
                                         $tech_stmt->bind_param('si', $booking_data->s_category, $current_tech_id);
                                     } else {
                                         $tech_query = "SELECT * FROM tms_technician WHERE t_category = ? AND t_status = 'Available'";
                                         $tech_stmt = $mysqli->prepare($tech_query);
                                         $tech_stmt->bind_param('s', $booking_data->s_category);
                                     }
                                     
                                     $tech_stmt->execute();
                                     $tech_result = $tech_stmt->get_result();
                                     
                                     if($tech_result->num_rows == 0) {
                                         echo '<option value="" disabled>No technicians available for this category</option>';
                                     } else {
                                         while($tech = $tech_result->fetch_object()) {
                                             $selected = ($tech->t_id == $current_tech_id) ? 'selected' : '';
                                             echo '<option value="'.$tech->t_id.'" '.$selected.'>'.$tech->t_name.' - '.$tech->t_specialization.' ('.$tech->t_status.')</option>';
                                         }
                                     }
                                     ?>
                                 </select>
                                 <small class="form-text text-muted">Showing technicians matching service category: <?php echo $booking_data->s_category;?></small>
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
                             <hr>
                             <button type="submit" name="assign_technician" class="btn btn-success">Assign Technician</button>
                             <a href="admin-manage-service-booking.php" class="btn btn-secondary">Cancel</a>
                         </form>
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

