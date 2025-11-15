<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  $sb_id=$_GET['sb_id'];
  $ret="SELECT sb.*, u.u_fname, u.u_lname, u.u_email, u.u_phone, s.s_name, s.s_category, s.s_price, t.t_name as tech_name, t.t_id_no as tech_id
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
                                 <h5>Completion Evidence & Bill</h5>
                                 <?php if($booking->sb_status == 'Completed'): ?>
                                   <table class="table table-bordered">
                                     <tr>
                                       <th>Completed At:</th>
                                       <td><?php echo isset($booking->sb_completed_at) ? date('M d, Y h:i A', strtotime($booking->sb_completed_at)) : '—';?></td>
                                     </tr>
                                     <tr>
                                       <th>Service Image:</th>
                                       <td>
                                         <?php if(!empty($booking->sb_completion_image)): ?>
                                           <img src="../<?php echo $booking->sb_completion_image; ?>" alt="Completion Image" style="max-width:100%;height:auto;border-radius:8px;" />
                                         <?php else: ?>
                                           <span class="text-muted">No image uploaded</span>
                                         <?php endif; ?>
                                       </td>
                                     </tr>
                                     <tr>
                                       <th>Bill Amount:</th>
                                       <td>₹<?php echo isset($booking->sb_bill_amount) ? number_format($booking->sb_bill_amount, 2) : '0.00';?></td>
                                     </tr>
                                     <tr>
                                       <th>Bill Attachment:</th>
                                       <td>
                                         <?php if(!empty($booking->sb_bill_attachment)): ?>
                                           <a href="../<?php echo $booking->sb_bill_attachment; ?>" target="_blank" class="btn btn-sm btn-primary">View Attachment</a>
                                         <?php else: ?>
                                           <span class="text-muted">No attachment uploaded</span>
                                         <?php endif; ?>
                                       </td>
                                     </tr>
                                   </table>
                                 <?php else: ?>
                                   <p class="text-muted">Service not completed yet.</p>
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
                                     <tr>
                                         <th>Service Price:</th>
                                         <td>$<?php echo number_format($booking->s_price, 2);?></td>
                                     </tr>
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
                                             } else { 
                                                 echo '<span class="badge badge-danger">'.$booking->sb_status.'</span>'; 
                                             }
                                             ?>
                                         </td>
                                     </tr>
                                     <tr>
                                         <th>Total Price:</th>
                                         <td>$<?php echo number_format($booking->sb_total_price, 2);?></td>
                                     </tr>
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
                         <?php if(!$booking->tech_name): ?>
                         <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-success">Assign Technician</a>
                         <?php elseif($booking->sb_status == 'Rejected' || $booking->sb_status == 'Cancelled'): ?>
                         <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-warning">Reassign Technician</a>
                         <?php endif; ?>
                         
                         <?php if($booking->sb_status != 'Cancelled' && $booking->sb_status != 'Completed'): ?>
                         <a href="admin-cancel-service-booking.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to DELETE this booking? This action cannot be undone!')">
                             <i class="fas fa-trash"></i> Delete Booking
                         </a>
                         <?php endif; ?>
                         
                         <a href="admin-delete-service-booking.php?sb_id=<?php echo $booking->sb_id;?>" class="btn btn-danger" onclick="return confirm('Delete this booking permanently?')">
                             <i class="fas fa-trash"></i> Delete
                         </a>
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
     <script src="js/sb-admin.min.js"></script>

     <!-- Demo scripts for this page-->
     <script src="js/demo/datatables-demo.js"></script>
 </body>

 </html>

