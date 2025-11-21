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
                // START TRANSACTION to prevent race conditions
                $mysqli->begin_transaction();
                
                try {
                    // STEP 1: Lock technician row and check availability (prevents concurrent assignments)
                    $check_tech_query = "SELECT t_id, t_name, t_current_bookings, t_booking_limit 
                                        FROM tms_technician 
                                        WHERE t_id = ? FOR UPDATE";
                    $tech_lock_stmt = $mysqli->prepare($check_tech_query);
                    $tech_lock_stmt->bind_param('i', $sb_technician_id);
                    $tech_lock_stmt->execute();
                    $tech_result = $tech_lock_stmt->get_result();
                    $tech_data = $tech_result->fetch_object();
                    
                    if(!$tech_data) {
                        throw new Exception("Technician not found");
                    }
                    
                    // Check if technician has available slots
                    if($tech_data->t_current_bookings >= $tech_data->t_booking_limit) {
                        throw new Exception("Technician {$tech_data->t_name} is at capacity ({$tech_data->t_current_bookings}/{$tech_data->t_booking_limit}). Please select another technician.");
                    }
                    
                    // STEP 2: Get the previously assigned technician (if any) and lock booking row
                    $get_old_tech = "SELECT sb_id, sb_technician_id, sb_status FROM tms_service_booking WHERE sb_id = ? FOR UPDATE";
                    $old_tech_stmt = $mysqli->prepare($get_old_tech);
                    $old_tech_stmt->bind_param('i', $sb_id);
                    $old_tech_stmt->execute();
                    $old_tech_result = $old_tech_stmt->get_result();
                    $old_booking = $old_tech_result->fetch_object();
                    
                    if(!$old_booking) {
                        throw new Exception("Booking not found");
                    }
                    
                    // STEP 3: Validate booking status
                    if(in_array($old_booking->sb_status, ['Completed', 'Cancelled'])) {
                        throw new Exception("Cannot assign technician to {$old_booking->sb_status} booking");
                    }
                    
                    $old_tech_id = $old_booking->sb_technician_id;
                    
                    // STEP 4: If technician is being changed (not first assignment)
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
                    
                    // Decrement old technician's booking count
                    $decrement_old = "UPDATE tms_technician SET t_current_bookings = GREATEST(t_current_bookings - 1, 0) WHERE t_id=?";
                    $decrement_stmt = $mysqli->prepare($decrement_old);
                    $decrement_stmt->bind_param('i', $old_tech_id);
                    $decrement_stmt->execute();
                    
                    // Free up the old technician
                    $free_tech = "UPDATE tms_technician SET t_status='Available' WHERE t_id=?";
                    $free_stmt = $mysqli->prepare($free_tech);
                    $free_stmt->bind_param('i', $old_tech_id);
                    $free_stmt->execute();
                    }
                    
                    // STEP 5: Add deadline columns if they don't exist
                $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_deadline_date DATE DEFAULT NULL");
                $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_deadline_time TIME DEFAULT NULL");
                
                // Ensure timestamp columns exist for notification system
                    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
                    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL DEFAULT NULL");
                    
                    // STEP 6: Auto-set status based on technician assignment
                    $auto_status = $sb_technician_id > 0 ? 'Approved' : 'Pending';
                    
                    // STEP 7: Update the booking with new technician, service deadline, and assignment timestamp
                    $query="UPDATE tms_service_booking SET sb_technician_id=?, sb_status=?, sb_service_deadline_date=?, sb_service_deadline_time=?, sb_assigned_at=NOW(), sb_updated_at=NOW() WHERE sb_id=?";
                    $stmt = $mysqli->prepare($query);
                    
                    if(!$stmt) {
                        throw new Exception("Database error: " . $mysqli->error);
                    }
                    
                    $stmt->bind_param('isssi', $sb_technician_id, $auto_status, $service_deadline_date, $service_deadline_time, $sb_id);
                    $result = $stmt->execute();
                    
                    if(!$result || $stmt->affected_rows == 0) {
                        throw new Exception("Failed to update booking");
                    }
                    // STEP 8: Update technician status based on booking status
                    if($sb_status == 'Completed' || $sb_status == 'Cancelled' || $sb_status == 'Rejected') {
                        // Free the technician if booking is completed, cancelled, or rejected
                        // Decrement booking count
                        $update_tech = "UPDATE tms_technician 
                                      SET t_status='Available', 
                                          t_is_available=1, 
                                          t_current_booking_id=NULL,
                                          t_current_bookings = GREATEST(t_current_bookings - 1, 0)
                                      WHERE t_id=?";
                    } else if($sb_status == 'In Progress' || $sb_status == 'Approved' || $sb_status == 'Assigned') {
                        // Mark technician as booked if booking is in progress, approved, or assigned
                        // Increment booking count only if this is a new assignment (not reassignment to same tech)
                        if(!$old_tech_id || $old_tech_id != $sb_technician_id) {
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Booked', 
                                              t_is_available=0, 
                                              t_current_booking_id=?,
                                              t_current_bookings = t_current_bookings + 1
                                          WHERE t_id=?";
                        } else {
                            // Same technician, just update status without incrementing
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Booked', 
                                              t_is_available=0, 
                                              t_current_booking_id=?
                                          WHERE t_id=?";
                        }
                    } else {
                        // For pending status, mark as booked but with pending status
                        // Increment booking count only if this is a new assignment
                        if(!$old_tech_id || $old_tech_id != $sb_technician_id) {
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Booked', 
                                              t_is_available=0, 
                                              t_current_booking_id=?,
                                              t_current_bookings = t_current_bookings + 1
                                          WHERE t_id=?";
                        } else {
                            $update_tech = "UPDATE tms_technician 
                                          SET t_status='Booked', 
                                              t_is_available=0, 
                                              t_current_booking_id=?
                                          WHERE t_id=?";
                        }
                    }
                    
                    if($update_tech && $sb_technician_id) {
                        $tech_stmt = $mysqli->prepare($update_tech);
                        if(!$tech_stmt) {
                            throw new Exception("Failed to prepare technician update");
                        }
                        
                        // Check if query needs booking_id parameter
                        if(strpos($update_tech, 't_current_booking_id=?') !== false) {
                            $tech_stmt->bind_param('ii', $sb_id, $sb_technician_id);
                        } else {
                            $tech_stmt->bind_param('i', $sb_technician_id);
                        }
                        
                        if(!$tech_stmt->execute()) {
                            throw new Exception("Failed to update technician status");
                        }
                    }
                    
                    // COMMIT TRANSACTION - All operations successful
                    $mysqli->commit();
                    
                    // Auto-update all technician statuses after assignment
                    include_once('auto-update-technician-status.php');
                    
                    $succ = "Technician Assigned Successfully";
                    // Redirect to prevent form resubmission
                    header("Location: admin-assign-technician.php?sb_id=" . $sb_id . "&success=1");
                    exit();
                    
                } catch(Exception $e) {
                    // ROLLBACK on any error
                    $mysqli->rollback();
                    $err = $e->getMessage();
                }
            } // End of else (validation passed)
    } // End of if(isset($_POST['assign_technician']))
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
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong><i class="fas fa-check-circle"></i> Success!</strong> Technician Assigned Successfully!
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <strong><i class="fas fa-exclamation-circle"></i> Failed!</strong> <?php echo htmlspecialchars($err);?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
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
                         $is_rejected = ($booking_data->sb_status == 'Rejected' || $booking_data->sb_status == 'Rejected by Technician' || $booking_data->sb_status == 'Cancelled' || $booking_data->sb_status == 'Not Done');
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
                                     
                                     // SPECIAL CASE: For Custom Service / Other - Show ALL available technicians
                                     $is_custom_service_booking = (stripos($booking_data->s_name, 'Custom Service') !== false || 
                                                                   stripos($booking_data->s_name, 'Other') !== false ||
                                                                   stripos($booking_data->sb_description, 'Custom Service:') !== false);
                                     
                                     if($is_custom_service_booking) {
                                         // For custom services, show ALL technicians with available capacity
                                         // Admin will decide based on the custom service description
                                         $all_techs_query = "SELECT t.t_id, t.t_name, t.t_experience, t.t_current_bookings, t.t_booking_limit,
                                                                    (t.t_booking_limit - t.t_current_bookings) as available_slots,
                                                                    t.t_skills
                                                             FROM tms_technician t
                                                             WHERE t.t_status != 'Inactive'
                                                             ORDER BY 
                                                                 CASE WHEN t.t_current_bookings < t.t_booking_limit THEN 0 ELSE 1 END,
                                                                 t.t_experience DESC,
                                                                 t.t_name ASC";
                                         $all_techs_result = $mysqli->query($all_techs_query);
                                         $available_techs = [];
                                         
                                         while($tech = $all_techs_result->fetch_assoc()) {
                                             $available_techs[] = [
                                                 't_id' => $tech['t_id'],
                                                 't_name' => $tech['t_name'],
                                                 't_experience' => $tech['t_experience'],
                                                 'available_slots' => $tech['available_slots'],
                                                 'slot_available' => ($tech['available_slots'] > 0),
                                                 'match_type' => 'custom_service',
                                                 'slot_message' => ($tech['available_slots'] > 0) ? 'Available' : 'At capacity',
                                                 't_skills' => $tech['t_skills']
                                             ];
                                         }
                                     } else {
                                         // REGULAR SERVICES: Use skill-based matcher with time slot checking
                                         require_once('vendor/inc/improved-technician-matcher.php');
                                         
                                         if($booking_data->sb_service_id && $booking_data->sb_booking_date && $booking_data->sb_booking_time) {
                                             // Best method: Match by service ID with time slot availability
                                             $available_techs = getAvailableTechniciansWithSkillAndSlot(
                                                 $mysqli, 
                                                 $booking_data->sb_service_id, 
                                                 $booking_data->sb_booking_date,
                                                 $booking_data->sb_booking_time,
                                                 $sb_id
                                             );
                                         } else {
                                             $available_techs = [];
                                         }
                                     }
                                     
                                     if(empty($available_techs)) {
                                         // NO AVAILABLE TECHNICIANS - Show clear message
                                         echo '<option value="" disabled style="color: red;">⚠️ No available technicians for: '.$booking_data->s_name.'</option>';
                                         echo '<option value="" disabled>No technicians with required skills or all are busy at this time</option>';
                                     } elseif($is_custom_service_booking) {
                                         // CUSTOM SERVICE: Show all technicians grouped by availability
                                         $available_custom = array_filter($available_techs, function($t) { 
                                             return $t['slot_available']; 
                                         });
                                         $busy_custom = array_filter($available_techs, function($t) { 
                                             return !$t['slot_available']; 
                                         });
                                         
                                         // Show available technicians (can take more bookings)
                                         if(!empty($available_custom)) {
                                             echo '<optgroup label="✅ Available Technicians - Has Capacity ('.count($available_custom).')">';
                                             foreach($available_custom as $tech) {
                                                 $selected = ($tech['t_id'] == $current_tech_id) ? 'selected' : '';
                                                 $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
                                                 $slots = $tech['available_slots'];
                                                 $skills = !empty($tech['t_skills']) ? ' | Skills: '.htmlspecialchars($tech['t_skills']) : '';
                                                 echo '<option value="'.$tech['t_id'].'" '.$selected.'>';
                                                 echo htmlspecialchars($tech['t_name']) . ' ('.$exp.', '.$slots.' slot'.($slots!=1?'s':'').' free)'.$skills;
                                                 echo '</option>';
                                             }
                                             echo '</optgroup>';
                                         }
                                         
                                         // Show busy technicians (at capacity) as disabled
                                         if(!empty($busy_custom)) {
                                             echo '<optgroup label="🔴 At Capacity - Cannot Take More Bookings ('.count($busy_custom).')">';
                                             foreach($busy_custom as $tech) {
                                                 $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
                                                 $skills = !empty($tech['t_skills']) ? ' | Skills: '.htmlspecialchars($tech['t_skills']) : '';
                                                 echo '<option value="'.$tech['t_id'].'" disabled>';
                                                 echo htmlspecialchars($tech['t_name']) . ' ('.$exp.') - At capacity'.$skills;
                                                 echo '</option>';
                                             }
                                             echo '</optgroup>';
                                         }
                                     } else {
                                         // REGULAR SERVICES: Group technicians by availability and match type
                                         $available_exact = array_filter($available_techs, function($t) { 
                                             return $t['slot_available'] && $t['match_type'] === 'exact_skill'; 
                                         });
                                         $busy_exact = array_filter($available_techs, function($t) { 
                                             return !$t['slot_available'] && $t['match_type'] === 'exact_skill'; 
                                         });
                                         $available_category = array_filter($available_techs, function($t) { 
                                             return $t['slot_available'] && $t['match_type'] === 'category_only'; 
                                         });
                                         $busy_category = array_filter($available_techs, function($t) { 
                                             return !$t['slot_available'] && $t['match_type'] === 'category_only'; 
                                         });
                                         
                                         // Show available technicians with exact skill match (BEST)
                                         if(!empty($available_exact)) {
                                             echo '<optgroup label="✅ Available Now - Has Required Skill ('.count($available_exact).')">';
                                             foreach($available_exact as $tech) {
                                                 $selected = ($tech['t_id'] == $current_tech_id) ? 'selected' : '';
                                                 $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
                                                 $slots = $tech['available_slots'];
                                                 echo '<option value="'.$tech['t_id'].'" '.$selected.'>';
                                                 echo htmlspecialchars($tech['t_name']) . ' ('.$exp.', '.$slots.' slot'.($slots!=1?'s':'').' free) - '.$tech['slot_message'];
                                                 echo '</option>';
                                             }
                                             echo '</optgroup>';
                                         }
                                         
                                         // Show available with category match only
                                         if(!empty($available_category)) {
                                             echo '<optgroup label="⚠️ Available Now - Category Match Only ('.count($available_category).')">';
                                             foreach($available_category as $tech) {
                                                 $selected = ($tech['t_id'] == $current_tech_id) ? 'selected' : '';
                                                 $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
                                                 $slots = $tech['available_slots'];
                                                 echo '<option value="'.$tech['t_id'].'" '.$selected.'>';
                                                 echo htmlspecialchars($tech['t_name']) . ' ('.$exp.', '.$slots.' slot'.($slots!=1?'s':'').' free) - '.$tech['slot_message'];
                                                 echo '</option>';
                                             }
                                             echo '</optgroup>';
                                         }
                                         
                                         // Show busy technicians with skill (as disabled options for reference)
                                         if(!empty($busy_exact)) {
                                             echo '<optgroup label="🔴 Busy at This Time - Has Required Skill ('.count($busy_exact).')">';
                                             foreach($busy_exact as $tech) {
                                                 $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
                                                 echo '<option value="'.$tech['t_id'].'" disabled>';
                                                 echo htmlspecialchars($tech['t_name']) . ' ('.$exp.') - '.$tech['slot_message'];
                                                 echo '</option>';
                                             }
                                             echo '</optgroup>';
                                         }
                                         
                                         // Show busy category matches
                                         if(!empty($busy_category)) {
                                             echo '<optgroup label="🔴 Busy at This Time - Category Match ('.count($busy_category).')">';
                                             foreach($busy_category as $tech) {
                                                 $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
                                                 echo '<option value="'.$tech['t_id'].'" disabled>';
                                                 echo htmlspecialchars($tech['t_name']) . ' ('.$exp.') - '.$tech['slot_message'];
                                                 echo '</option>';
                                             }
                                             echo '</optgroup>';
                                         }
                                     }
                                     ?>
                                 </select>
                                 <small class="form-text text-muted">
                                     <strong>Service:</strong> <?php echo $booking_data->s_name;?> 
                                     | <strong>Category:</strong> <?php echo $booking_data->s_category;?>
                                     <br><strong>Booking Time:</strong> <?php echo date('M d, Y', strtotime($booking_data->sb_booking_date));?> at <?php echo date('h:i A', strtotime($booking_data->sb_booking_time));?>
                                     <?php
                                     // Count available technicians for this time slot
                                     $available_count = count(array_filter($available_techs, function($t) { return $t['slot_available']; }));
                                     $busy_count = count(array_filter($available_techs, function($t) { return !$t['slot_available']; }));
                                     $tech_count = count($available_techs);
                                     
                                     if($is_custom_service_booking) {
                                         // Custom service message
                                         echo '<br><span class="text-info"><i class="fas fa-info-circle"></i> <strong>Custom Service:</strong> Showing all technicians with available capacity. Review the service description above and assign based on technician skills.</span>';
                                         if($available_count > 0) {
                                             echo '<br><span class="text-success"><i class="fas fa-check-circle"></i> '.$available_count.' technician(s) have capacity to take this booking';
                                             if($busy_count > 0) echo ' ('.$busy_count.' at capacity)';
                                             echo '</span>';
                                         } else {
                                             echo '<br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> All technicians are at capacity!</span>';
                                         }
                                     } else {
                                         // Regular service message
                                         if($tech_count == 0) {
                                             echo '<br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> No technicians with required skills found!</span>';
                                         } else if($available_count == 0) {
                                             echo '<br><span class="text-warning"><i class="fas fa-clock"></i> '.$busy_count.' technician(s) have the skill but are busy at this time</span>';
                                         } else {
                                             echo '<br><span class="text-success"><i class="fas fa-check-circle"></i> '.$available_count.' technician(s) available for this time slot';
                                             if($busy_count > 0) echo ' ('.$busy_count.' busy)';
                                             echo '</span>';
                                         }
                                     }
                                     ?>
                                 </small>
                                 
                                 <?php if($is_custom_service_booking): ?>
                                     <?php if($available_count == 0): ?>
                                     <div class="alert alert-warning mt-2">
                                         <strong><i class="fas fa-exclamation-triangle"></i> All Technicians at Capacity</strong><br>
                                         All technicians are currently at their booking limit. No one can take additional bookings right now.<br><br>
                                         <strong>Options:</strong>
                                         <ul class="mb-0">
                                             <li>Wait for a technician to complete their current bookings</li>
                                             <li>Increase booking limit for a technician: <a href="admin-manage-technician.php" class="alert-link">Manage Technicians</a></li>
                                             <li>Add a new technician: <a href="admin-add-technician.php" class="alert-link">Add Technician</a></li>
                                         </ul>
                                     </div>
                                     <?php else: ?>
                                     <div class="alert alert-info mt-2">
                                         <strong><i class="fas fa-lightbulb"></i> Custom Service Assignment Tips</strong><br>
                                         <ul class="mb-0">
                                             <li>Review the <strong>custom service description</strong> in the orange box above</li>
                                             <li>Check each technician's <strong>skills</strong> listed in the dropdown</li>
                                             <li>Assign to the technician whose skills best match the customer's request</li>
                                             <li>All listed technicians have capacity to take this booking</li>
                                         </ul>
                                     </div>
                                     <?php endif; ?>
                                 <?php else: ?>
                                     <?php if($tech_count == 0): ?>
                                     <div class="alert alert-warning mt-2">
                                         <strong><i class="fas fa-info-circle"></i> No Technicians with Required Skills</strong><br>
                                         No technicians found with the detailed service skill: "<strong><?php echo $booking_data->s_name;?></strong>"<br><br>
                                         <strong>Solutions:</strong>
                                         <ul class="mb-0">
                                             <li>Add the skill to an existing technician: <a href="admin-manage-technician.php" class="alert-link">Manage Technicians</a></li>
                                             <li>Add a new technician with this skill: <a href="admin-add-technician.php" class="alert-link">Add Technician</a></li>
                                             <li>Change the booking time if technicians are busy</li>
                                         </ul>
                                     </div>
                                     <?php elseif($available_count == 0 && $busy_count > 0): ?>
                                     <div class="alert alert-info mt-2">
                                         <strong><i class="fas fa-clock"></i> All Skilled Technicians Busy at This Time</strong><br>
                                         <?php echo $busy_count;?> technician(s) have the required skill but are busy at <?php echo date('h:i A', strtotime($booking_data->sb_booking_time));?> on <?php echo date('M d', strtotime($booking_data->sb_booking_date));?><br><br>
                                         <strong>Options:</strong>
                                         <ul class="mb-0">
                                             <li>Change the booking time to a different slot</li>
                                             <li>Wait for a technician to complete their current booking</li>
                                             <li>Assign a technician from category match (if available)</li>
                                         </ul>
                                     </div>
                                     <?php endif; ?>
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
         <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
         <script src="vendor/js/sb-admin.min.js"></script>
         
         <script>
         // Toggle reassignment form enable/disable
         function toggleReassignment() {
             const checkbox = document.getElementById('force_reassign');
             const fieldset = document.getElementById('formFieldset');
             
             if(checkbox && fieldset) {
                 fieldset.disabled = !checkbox.checked;
             }
         }
         
         // Auto-hide alerts after 5 seconds
         $(document).ready(function() {
             setTimeout(function() {
                 $('.alert').fadeOut('slow');
             }, 5000);
         });
         </script>

 </body>

 </html>

