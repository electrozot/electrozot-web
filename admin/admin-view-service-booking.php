<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  include('vendor/inc/image-visibility-helper.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  $sb_id=$_GET['sb_id'];
  $ret="SELECT sb.*, u.u_fname, u.u_lname, u.u_email, u.u_phone, s.s_name, s.s_category, s.s_price, 
        t.t_name as tech_name, t.t_id_no as tech_id
        FROM tms_service_booking sb
        LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
        LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
        WHERE sb.sb_id=?";
  $stmt= $mysqli->prepare($ret) ;
  $stmt->bind_param('i',$sb_id);
  $stmt->execute();
  $res=$stmt->get_result();
  $booking = $res->fetch_object();
  
  // Get payment collection details
  $mysqli->query("CREATE TABLE IF NOT EXISTS tms_payment_collection (
      pc_id INT AUTO_INCREMENT PRIMARY KEY,
      pc_booking_id INT NOT NULL,
      pc_amount DECIMAL(10,2) NOT NULL,
      pc_method ENUM('QR','TechQR','Cash') NOT NULL,
      pc_collected_by INT NOT NULL,
      pc_collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      pc_status ENUM('Collected','Verified') DEFAULT 'Collected',
      INDEX(pc_booking_id),
      INDEX(pc_collected_by)
  )");
  
  $payment_query = "SELECT pc.*, t.t_name as collector_name 
                    FROM tms_payment_collection pc
                    LEFT JOIN tms_technician t ON pc.pc_collected_by = t.t_id
                    WHERE pc.pc_booking_id = ?";
  $payment_stmt = $mysqli->prepare($payment_query);
  $payment_stmt->bind_param('i', $sb_id);
  $payment_stmt->execute();
  $payment_result = $payment_stmt->get_result();
  $payment_data = $payment_result->fetch_object();
  
  // Ensure price tracking columns exist
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_price_set_by_tech TINYINT(1) DEFAULT 0");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_tech_decided_price DECIMAL(10,2) DEFAULT NULL");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_final_price DECIMAL(10,2) DEFAULT NULL");
  
  // Ensure hold system columns exist
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_on_hold TINYINT(1) DEFAULT 0");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_reason TEXT NULL");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_start_date TIMESTAMP NULL");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_end_date TIMESTAMP NULL");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_high_priority TINYINT(1) DEFAULT 0");
  $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_priority_reason VARCHAR(255) NULL");
  
  // Handle hold/unhold actions
  if(isset($_POST['hold_action'])) {
      $action = $_POST['hold_action'];
      
      if($action == 'hold') {
          $reason = $_POST['hold_reason'] ?? 'Admin hold - requires attention';
          $hold_start = date('Y-m-d H:i:s');
          $hold_end = date('Y-m-d H:i:s', strtotime('+4 days'));
          
          $update = "UPDATE tms_service_booking 
                     SET sb_is_on_hold = 1,
                         sb_hold_reason = ?,
                         sb_hold_start_date = ?,
                         sb_hold_end_date = ?,
                         sb_was_on_hold = 1,
                         sb_status = 'On Hold'
                     WHERE sb_id = ?";
          $stmt_hold = $mysqli->prepare($update);
          $stmt_hold->bind_param('sssi', $reason, $hold_start, $hold_end, $sb_id);
          if($stmt_hold->execute()) {
              $_SESSION['success'] = "Booking put on hold successfully";
              header("Location: admin-view-service-booking.php?sb_id=$sb_id");
              exit;
          }
          
      } elseif($action == 'unhold') {
          $update = "UPDATE tms_service_booking 
                     SET sb_is_on_hold = 0,
                         sb_hold_reason = NULL,
                         sb_hold_start_date = NULL,
                         sb_hold_end_date = NULL,
                         sb_is_high_priority = 1,
                         sb_priority_reason = 'Admin unholded - high priority',
                         sb_status = 'In Progress'
                     WHERE sb_id = ?";
          $stmt_unhold = $mysqli->prepare($update);
          $stmt_unhold->bind_param('i', $sb_id);
          if($stmt_unhold->execute()) {
              $_SESSION['success'] = "Booking unholded and marked as high priority";
              header("Location: admin-view-service-booking.php?sb_id=$sb_id");
              exit;
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
                 <?php if(isset($_SESSION['success'])) {?>
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <?php } ?>
                 <?php if(isset($_SESSION['error'])) {?>
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
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
                     <li class="breadcrumb-item active">View Details</li>
                 </ol>

                 <div class="card mb-3">
                     <div class="card-header">
                         <i class="fas fa-info-circle"></i>
                         Service Booking Details
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-md-6">
                                 <h5>Customer Information</h5>
                                 <table class="table table-bordered">
                                     <tr>
                                         <th>Name:</th>
                                         <td><?php echo $booking->u_fname;?> <?php echo $booking->u_lname;?></td>
                                     </tr>
                                     <tr>
                                         <th>Email:</th>
                                         <td><?php echo $booking->u_email;?></td>
                                     </tr>
                                     <tr>
                                         <th>Phone:</th>
                                         <td><?php echo $booking->u_phone;?></td>
                                     </tr>
                                 </table>
                                 <hr>
                                 <h5><i class="fas fa-check-circle text-success"></i> Completion Evidence & Bill</h5>
                                 <?php if($booking->sb_status == 'Completed'): ?>
                                   <div class="alert alert-success">
                                     <i class="fas fa-info-circle"></i> <strong>Service Completed Successfully</strong>
                                   </div>
                                   <table class="table table-bordered">
                                     <tr>
                                       <th width="40%">Completed At:</th>
                                       <td><strong><?php echo isset($booking->sb_completed_at) ? date('M d, Y h:i A', strtotime($booking->sb_completed_at)) : '—';?></strong></td>
                                     </tr>
                                     <tr>
                                       <th>Bill Amount Charged:</th>
                                       <td><strong style="font-size:1.3rem;color:#28a745;">₹<?php 
                                       // Show payment amount if collected, otherwise bill amount
                                       if($payment_data && $payment_data->pc_amount > 0) {
                                           echo number_format($payment_data->pc_amount, 2);
                                       } elseif(isset($booking->sb_bill_amount) && $booking->sb_bill_amount > 0) {
                                           echo number_format($booking->sb_bill_amount, 2);
                                       } else {
                                           echo '0.00';
                                       }
                                       ?></strong></td>
                                     </tr>
                                     <?php if($payment_data): ?>
                                     <tr>
                                       <th>Payment Method:</th>
                                       <td>
                                         <?php 
                                         if($payment_data->pc_method == 'QR') {
                                             echo '<span class="badge badge-primary" style="font-size:0.95rem;padding:8px 15px;"><i class="fas fa-qrcode"></i> Company QR Code</span>';
                                         } elseif($payment_data->pc_method == 'TechQR') {
                                             echo '<span class="badge badge-warning" style="font-size:0.95rem;padding:8px 15px;"><i class="fas fa-user-circle"></i> Technician QR</span>';
                                         } else {
                                             echo '<span class="badge badge-success" style="font-size:0.95rem;padding:8px 15px;"><i class="fas fa-money-bill-wave"></i> Cash Payment</span>';
                                         }
                                         ?>
                                       </td>
                                     </tr>
                                     <tr>
                                       <th>Payment Collected By:</th>
                                       <td><strong><?php echo htmlspecialchars($payment_data->collector_name); ?></strong></td>
                                     </tr>
                                     <tr>
                                       <th>Payment Collected At:</th>
                                       <td><strong><?php echo date('M d, Y h:i A', strtotime($payment_data->pc_collected_at)); ?></strong></td>
                                     </tr>
                                     <?php else: ?>
                                     <tr>
                                       <th>Payment Status:</th>
                                       <td><span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Payment Not Recorded</span></td>
                                     </tr>
                                     <?php endif; ?>
                                   </table>
                                   
                                   <?php 
                                   // Check if images should be visible (40 days for admin)
                                   $show_images_admin = !empty($booking->sb_completed_date) && isImageVisible($booking->sb_completed_date, 'admin');
                                   ?>
                                   
                                   <h6 class="mt-3"><i class="fas fa-camera"></i> Service Completion Photo</h6>
                                   <div class="border rounded p-3 mb-3" style="background:#f8f9fa;">
                                     <?php if(!empty($booking->sb_completion_image) && $show_images_admin): ?>
                                       <?php 
                                       // Fix path - remove leading ../ if present, images are in root uploads folder
                                       $service_img_path = str_replace('../', '', $booking->sb_completion_image);
                                       $service_img_url = '../' . $service_img_path;
                                       ?>
                                       <a href="<?php echo $service_img_url; ?>" target="_blank">
                                         <img src="<?php echo $service_img_url; ?>" alt="Service Completion" style="max-width:100%;height:auto;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);cursor:pointer;" onerror="this.parentElement.innerHTML='<span class=\'text-danger\'><i class=\'fas fa-exclamation-circle\'></i> Image not found: <?php echo htmlspecialchars($service_img_path); ?></span>';" />
                                       </a>
                                       <p class="text-muted mt-2 mb-0"><small><i class="fas fa-info-circle"></i> Click image to view full size</small></p>
                                       <p class="text-muted mb-0"><small>Path: <?php echo htmlspecialchars($service_img_path); ?></small></p>
                                     <?php elseif(!empty($booking->sb_completion_image) && !$show_images_admin): ?>
                                       <span class="text-muted"><i class="fas fa-clock"></i> Image has been archived (older than 40 days)</span>
                                     <?php else: ?>
                                       <span class="text-muted"><i class="fas fa-exclamation-triangle"></i> No service image uploaded</span>
                                     <?php endif; ?>
                                   </div>
                                   
                                   <h6><i class="fas fa-file-invoice"></i> Bill/Receipt Photo</h6>
                                   <div class="border rounded p-3" style="background:#f8f9fa;">
                                     <?php if(!empty($booking->sb_bill_attachment) && $show_images_admin): ?>
                                       <?php 
                                       // Fix path - remove leading ../ if present, images are in root uploads folder
                                       $bill_img_path = str_replace('../', '', $booking->sb_bill_attachment);
                                       $bill_img_url = '../' . $bill_img_path;
                                       ?>
                                       <a href="<?php echo $bill_img_url; ?>" target="_blank">
                                         <img src="<?php echo $bill_img_url; ?>" alt="Bill/Receipt" style="max-width:100%;height:auto;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);cursor:pointer;" onerror="this.parentElement.innerHTML='<span class=\'text-danger\'><i class=\'fas fa-exclamation-circle\'></i> Image not found: <?php echo htmlspecialchars($bill_img_path); ?></span>';" />
                                       </a>
                                       <p class="text-muted mt-2 mb-0"><small><i class="fas fa-info-circle"></i> Click image to view full size</small></p>
                                       <p class="text-muted mb-0"><small>Path: <?php echo htmlspecialchars($bill_img_path); ?></small></p>
                                       <a href="<?php echo $bill_img_url; ?>" download class="btn btn-sm btn-primary mt-2">
                                         <i class="fas fa-download"></i> Download Bill
                                       </a>
                                     <?php elseif(!empty($booking->sb_bill_attachment) && !$show_images_admin): ?>
                                       <span class="text-muted"><i class="fas fa-clock"></i> Image has been archived (older than 40 days)</span>
                                     <?php else: ?>
                                       <span class="text-muted"><i class="fas fa-exclamation-triangle"></i> No bill attachment uploaded</span>
                                     <?php endif; ?>
                                   </div>
                                 <?php elseif($booking->sb_status == 'Not Done'): ?>
                                   <div class="alert alert-danger">
                                     <i class="fas fa-times-circle"></i> <strong>Service Not Completed</strong>
                                   </div>
                                   <table class="table table-bordered">
                                     <tr>
                                       <th width="40%">Marked Not Done At:</th>
                                       <td><?php echo isset($booking->sb_not_done_at) ? date('M d, Y h:i A', strtotime($booking->sb_not_done_at)) : '—';?></td>
                                     </tr>
                                     <tr>
                                       <th>Reason:</th>
                                       <td><strong><?php echo isset($booking->sb_not_done_reason) ? htmlspecialchars($booking->sb_not_done_reason) : 'No reason provided';?></strong></td>
                                     </tr>
                                   </table>
                                 <?php else: ?>
                                   <div class="alert alert-info">
                                     <i class="fas fa-clock"></i> Service not completed yet. Current status: <strong><?php echo $booking->sb_status; ?></strong>
                                   </div>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6">
                                 <h5>Service Information</h5>
                                 <table class="table table-bordered">
                                     <tr>
                                         <th>Service Name:</th>
                                         <td><?php echo $booking->s_name;?></td>
                                     </tr>
                                     <tr>
                                         <th>Category:</th>
                                         <td><?php echo $booking->s_category;?></td>
                                     </tr>
                                     <?php if($booking->s_price !== null && $booking->s_price > 0): ?>
                                     <tr>
                                         <th>Service Price:</th>
                                         <td>
                                             <strong style="color: #28a745;">₹<?php echo number_format($booking->s_price, 2);?></strong>
                                             <span class="badge badge-success ml-2">
                                                 <i class="fas fa-check"></i> Fixed Price
                                             </span>
                                         </td>
                                     </tr>
                                     <?php endif; ?>
                                 </table>
                             </div>
                         </div>
                         <hr>
                         <div class="row">
                             <div class="col-md-6">
                                 <h5>Booking Details</h5>
                                 <table class="table table-bordered">
                                     <tr>
                                         <th>Booking Date:</th>
                                         <td><?php echo date('M d, Y', strtotime($booking->sb_booking_date));?></td>
                                     </tr>
                                     <tr>
                                         <th>Booking Time:</th>
                                         <td><?php echo date('h:i A', strtotime($booking->sb_booking_time));?></td>
                                     </tr>
                                     <tr>
                                         <th>Service Address:</th>
                                         <td><?php echo $booking->sb_address;?></td>
                                     </tr>
                                     <tr>
                                         <th>Status:</th>
                                         <td>
                                             <?php 
                                             if($booking->sb_status == "Pending"){ 
                                                 echo '<span class="badge badge-warning">'.$booking->sb_status.'</span>'; 
                                             } elseif($booking->sb_status == "Approved"){ 
                                                 echo '<span class="badge badge-info">'.$booking->sb_status.'</span>'; 
                                             } elseif($booking->sb_status == "In Progress"){ 
                                                 echo '<span class="badge badge-primary">'.$booking->sb_status.'</span>'; 
                                             } elseif($booking->sb_status == "Completed"){ 
                                                 echo '<span class="badge badge-success">'.$booking->sb_status.'</span>'; 
                                             } elseif($booking->sb_status == "On Hold"){ 
                                                 echo '<span class="badge badge-warning" style="background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);"><i class="fas fa-pause-circle"></i> '.$booking->sb_status.'</span>'; 
                                             } else { 
                                                 echo '<span class="badge badge-danger">'.$booking->sb_status.'</span>'; 
                                             }
                                             ?>
                                             <?php if($booking->sb_is_high_priority == 1): ?>
                                             <br><span class="badge badge-danger mt-1" style="background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%);"><i class="fas fa-fire"></i> HIGH PRIORITY</span>
                                             <?php endif; ?>
                                         </td>
                                     </tr>
                                     <?php if($booking->sb_is_on_hold == 1): ?>
                                     <tr>
                                         <th>Hold Information:</th>
                                         <td>
                                             <div class="alert alert-warning mb-0" style="background: #fff3cd; border-left: 4px solid #ffa502;">
                                                 <strong><i class="fas fa-pause-circle"></i> Booking is On Hold</strong><br>
                                                 <small><strong>Reason:</strong> <?php echo htmlspecialchars($booking->sb_hold_reason); ?></small><br>
                                                 <small><strong>Hold Until:</strong> <?php echo date('M d, Y h:i A', strtotime($booking->sb_hold_end_date)); ?></small>
                                             </div>
                                         </td>
                                     </tr>
                                     <?php endif; ?>
                                     <tr>
                                         <th>Booking Price:</th>
                                         <td>
                                             <?php 
                                                 // Check all price fields: sb_bill_amount > sb_final_price > sb_tech_decided_price > sb_total_price
                                                 $display_price = 0;
                                                 $is_tech_set = false;
                                                 
                                                 if(!empty($booking->sb_bill_amount) && $booking->sb_bill_amount > 0) {
                                                     $display_price = $booking->sb_bill_amount;
                                                     $is_tech_set = true;
                                                 } elseif(!empty($booking->sb_final_price) && $booking->sb_final_price > 0) {
                                                     $display_price = $booking->sb_final_price;
                                                     $is_tech_set = true;
                                                 } elseif(!empty($booking->sb_tech_decided_price) && $booking->sb_tech_decided_price > 0) {
                                                     $display_price = $booking->sb_tech_decided_price;
                                                     $is_tech_set = true;
                                                 } else {
                                                     $display_price = $booking->sb_total_price;
                                                 }
                                                 
                                                 // Show price
                                                 if($display_price > 0) {
                                                     echo '<strong style="color: #28a745;">₹' . number_format($display_price, 2) . '</strong>';
                                                 } else {
                                                     echo '<span style="color: #6c757d;">₹0.00</span>';
                                                 }
                                             ?>
                                             <?php if($is_tech_set): ?>
                                             <br><small class="badge badge-info"><i class="fas fa-user-cog"></i> Price set by technician</small>
                                             <?php elseif($display_price == 0 && $booking->sb_status != 'Completed'): ?>
                                             <br><small class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Price not set yet</small>
                                             <?php endif; ?>
                                         </td>
                                     </tr>
                                     <?php if($booking->sb_status == 'Completed'): ?>
                                     <tr>
                                         <th>Final Charged Price (Bill Amount):</th>
                                         <td>
                                             <?php 
                                                 // Use same logic as booking price
                                                 $final_price = 0;
                                                 if(!empty($booking->sb_bill_amount) && $booking->sb_bill_amount > 0) {
                                                     $final_price = $booking->sb_bill_amount;
                                                 } elseif(!empty($booking->sb_final_price) && $booking->sb_final_price > 0) {
                                                     $final_price = $booking->sb_final_price;
                                                 } elseif(!empty($booking->sb_tech_decided_price) && $booking->sb_tech_decided_price > 0) {
                                                     $final_price = $booking->sb_tech_decided_price;
                                                 } else {
                                                     $final_price = $booking->sb_total_price;
                                                 }
                                             ?>
                                             <strong style="color: #007bff; font-size: 1.2rem;">
                                                 ₹<?php echo number_format($final_price, 2);?>
                                             </strong>
                                             <?php if($final_price > 0 && ($booking->sb_bill_amount > 0 || $booking->sb_final_price > 0 || $booking->sb_tech_decided_price > 0)): ?>
                                             <br>
                                             <span class="badge badge-info mt-1">
                                                 <i class="fas fa-user-cog"></i> Price set by Technician - Same as Booking Price
                                             </span>
                                             <br>
                                             <small class="text-muted">
                                                 <i class="fas fa-info-circle"></i> For this booking, the technician decided the price based on actual work done
                                             </small>
                                             <?php elseif($booking->s_price !== null && $booking->s_price > 0): ?>
                                             <br>
                                             <span class="badge badge-success mt-1">
                                                 <i class="fas fa-check"></i> Fixed price applied
                                             </span>
                                             <?php endif; ?>
                                         </td>
                                     </tr>
                                     <?php 
                                     // Show technician decided price if available
                                     $tech_price = 0;
                                     if(!empty($booking->sb_bill_amount) && $booking->sb_bill_amount > 0) {
                                         $tech_price = $booking->sb_bill_amount;
                                     } elseif(!empty($booking->sb_tech_decided_price) && $booking->sb_tech_decided_price > 0) {
                                         $tech_price = $booking->sb_tech_decided_price;
                                     } elseif(!empty($booking->sb_final_price) && $booking->sb_final_price > 0) {
                                         $tech_price = $booking->sb_final_price;
                                     }
                                     
                                     if($tech_price > 0): 
                                     ?>
                                     <tr>
                                         <th>Technician Decided Price:</th>
                                         <td>
                                             <span class="badge badge-warning" style="font-size: 1rem; padding: 8px 12px;">
                                                 ₹<?php echo number_format($tech_price, 2);?>
                                             </span>
                                             <br>
                                             <small class="text-muted">
                                                 <i class="fas fa-info-circle"></i> This price was specifically set by the technician for this booking only
                                             </small>
                                         </td>
                                     </tr>
                                     <?php endif; ?>
                                     <?php endif; ?>
                                 </table>
                             </div>
                             <div class="col-md-6">
                                 <h5>Assigned Technician</h5>
                                 <?php if($booking->tech_name): ?>
                                 <table class="table table-bordered">
                                     <tr>
                                         <th>Technician Name:</th>
                                         <td><?php echo $booking->tech_name;?></td>
                                     </tr>
                                     <tr>
                                         <th>Technician ID:</th>
                                         <td><?php echo $booking->tech_id;?></td>
                                     </tr>
                                 </table>
                                 <?php if($booking->sb_status == 'Rejected' || $booking->sb_status == 'Cancelled'): ?>
                                 <div class="alert alert-warning">
                                     <i class="fas fa-exclamation-triangle"></i> This booking was <?php echo strtolower($booking->sb_status);?>. You can reassign to a different technician.
                                 </div>
                                 <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-warning">Reassign Technician</a>
                                 <?php elseif($booking->sb_status != 'Completed'): ?>
                                 <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-info btn-sm">Change Technician</a>
                                 <small class="text-muted d-block mt-2">
                                     <i class="fas fa-info-circle"></i> Use this if technician is not responding
                                 </small>
                                 <?php endif; ?>
                                 <?php else: ?>
                                 <p class="text-warning">No technician assigned yet.</p>
                                 <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-success">Assign Technician</a>
                                 <?php endif; ?>
                             </div>
                         </div>
                         <?php if($booking->sb_description): ?>
                         <hr>
                         <div class="row">
                             <div class="col-md-12">
                                 <h5>Additional Notes</h5>
                                 <p><?php echo $booking->sb_description;?></p>
                             </div>
                         </div>
                         <?php endif; ?>
                         <hr>
                         <a href="admin-manage-service-booking.php" class="btn btn-secondary">Back to List</a>
                         
                         <?php if($booking->sb_status == 'Completed'): ?>
                         <a href="admin-view-bill.php?booking_id=<?php echo $booking->sb_id;?>" class="btn btn-info" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                             <i class="fas fa-file-invoice"></i> View Bill
                         </a>
                         <?php endif; ?>
                         
                         <?php if(!$booking->tech_name): ?>
                         <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-success">Assign Technician</a>
                         <?php elseif($booking->sb_status == 'Rejected' || $booking->sb_status == 'Cancelled'): ?>
                         <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-warning">Reassign Technician</a>
                         <?php endif; ?>
                         
                         <?php if($booking->sb_status != 'Cancelled' && $booking->sb_status != 'Completed'): ?>
                             <?php if($booking->sb_is_on_hold == 1): ?>
                             <!-- Unhold Button -->
                             <button type="button" class="btn btn-success" onclick="unholdBooking()">
                                 <i class="fas fa-play-circle"></i> Unhold Booking
                             </button>
                             <?php else: ?>
                             <!-- Hold Button -->
                             <button type="button" class="btn" style="background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%); color: white;" onclick="holdBooking()">
                                 <i class="fas fa-pause-circle"></i> Put on Hold
                             </button>
                             <?php endif; ?>
                             
                             <a href="admin-cancel-service-booking.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-warning" onclick="return confirm('Are you sure you want to CANCEL this booking? The technician will be freed up.')">
                                 <i class="fas fa-ban"></i> Cancel Booking
                             </a>
                         <?php endif; ?>
                         
                         <a href="admin-delete-service-booking.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-danger" onclick="return confirm('Delete this booking permanently? This cannot be undone!')">
                             <i class="fas fa-trash"></i> Delete Permanently
                         </a>
                         
                         <!-- Hidden forms for hold/unhold actions -->
                         <form id="holdForm" method="POST" style="display: none;">
                             <input type="hidden" name="hold_action" value="hold">
                             <input type="hidden" name="hold_reason" id="holdReason">
                         </form>
                         
                         <form id="unholdForm" method="POST" style="display: none;">
                             <input type="hidden" name="hold_action" value="unhold">
                         </form>
                     </div>
                 </div>
             </div>
             <!-- /.container-fluid -->

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
     <script src="vendor/datatables/jquery.dataTables.js"></script>
     <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

     <!-- Custom scripts for all pages-->
     <script src="vendor/js/sb-admin.min.js"></script>

     <!-- Demo scripts for this page-->
     <script src="vendor/js/demo/datatables-demo.js"></script>
     
     <script>
     function holdBooking() {
         const reason = prompt('Enter reason for holding this booking:', 'Admin hold - requires attention');
         if(reason && reason.trim() !== '') {
             document.getElementById('holdReason').value = reason;
             document.getElementById('holdForm').submit();
         }
     }
     
     function unholdBooking() {
         if(confirm('Unhold this booking? It will be marked as HIGH PRIORITY and returned to In Progress status.')) {
             document.getElementById('unholdForm').submit();
         }
     }
     </script>
 </body>

 </html>

