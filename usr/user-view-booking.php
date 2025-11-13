<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['u_id'];
?>
 <!DOCTYPE html>
 <html lang="en">
 <?php include("vendor/inc/head.php");?>

 <body id="page-top">
     <!--Start Navigation Bar-->
     <?php include("vendor/inc/nav.php");?>
     <!--Navigation Bar-->

     <div id="wrapper">
         <!-- Sidebar -->
         <?php include("vendor/inc/sidebar.php");?>
         <!--End Sidebar-->
         <div id="content-wrapper">

             <div class="container-fluid">
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="user-dashboard.php">Dashboard</a>
                     </li>
                     <li class="breadcrumb-item">Booking</li>
                     <li class="breadcrumb-item ">View My Booking</li>
                 </ol>
                 <!-- My Bookings-->
                 <div class="card mb-3 shadow-lg" style="border: none; border-radius: 15px;">
                     <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                         <h5 class="m-0 font-weight-bold text-white">
                             <i class="fas fa-calendar-check"></i> My Bookings
                         </h5>
                     </div>
                     <div class="card-body p-4">
                         <?php
                         $bookings_query = "SELECT 
                                             sb.*,
                                             s.s_name, s.s_category, s.s_price,
                                             t.t_name, t.t_phone
                                           FROM tms_service_booking sb
                                           LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                                           LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                                           WHERE sb.sb_user_id = ?
                                           ORDER BY sb.sb_created_at DESC";
                         $bookings_stmt = $mysqli->prepare($bookings_query);
                         $bookings_stmt->bind_param('i', $aid);
                         $bookings_stmt->execute();
                         $bookings_result = $bookings_stmt->get_result();
                         
                         if($bookings_result->num_rows > 0):
                             while($booking = $bookings_result->fetch_object()):
                                 $status_color = '';
                                 $status_icon = '';
                                 switch($booking->sb_status) {
                                     case 'Pending':
                                         $status_color = 'warning';
                                         $status_icon = 'clock';
                                         break;
                                     case 'Confirmed':
                                         $status_color = 'info';
                                         $status_icon = 'check-circle';
                                         break;
                                     case 'In Progress':
                                         $status_color = 'primary';
                                         $status_icon = 'spinner';
                                         break;
                                     case 'Completed':
                                         $status_color = 'success';
                                         $status_icon = 'check-double';
                                         break;
                                     case 'Cancelled':
                                         $status_color = 'danger';
                                         $status_icon = 'times-circle';
                                         break;
                                     default:
                                         $status_color = 'secondary';
                                         $status_icon = 'question';
                                 }
                         ?>
                             <a href="user-booking-details.php?booking_id=<?php echo $booking->sb_id; ?>" class="booking-card-link" style="text-decoration: none;">
                                 <div class="booking-card mb-3">
                                     <div class="booking-card-header">
                                         <div class="booking-id">
                                             <i class="fas fa-receipt"></i> Booking #<?php echo $booking->sb_id; ?>
                                         </div>
                                         <div class="booking-status">
                                             <span class="badge badge-<?php echo $status_color; ?> p-2">
                                                 <i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo $booking->sb_status; ?>
                                             </span>
                                         </div>
                                     </div>
                                     <div class="booking-card-body">
                                         <div class="row">
                                             <div class="col-md-6">
                                                 <p class="mb-2">
                                                     <i class="fas fa-wrench text-primary"></i>
                                                     <strong><?php echo $booking->s_name; ?></strong>
                                                 </p>
                                                 <p class="mb-2 text-muted">
                                                     <i class="fas fa-tag"></i> <?php echo $booking->s_category; ?>
                                                 </p>
                                             </div>
                                             <div class="col-md-6">
                                                 <p class="mb-2">
                                                     <i class="fas fa-calendar text-info"></i>
                                                     <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?>
                                                 </p>
                                                 <p class="mb-2">
                                                     <i class="fas fa-clock text-warning"></i>
                                                     <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                                                 </p>
                                             </div>
                                         </div>
                                         <?php if($booking->t_name): ?>
                                             <div class="technician-info mt-2">
                                                 <i class="fas fa-user-cog"></i> Technician: <strong><?php echo $booking->t_name; ?></strong>
                                                 <?php if($booking->t_phone): ?>
                                                     | <i class="fas fa-phone"></i> <?php echo $booking->t_phone; ?>
                                                 <?php endif; ?>
                                             </div>
                                         <?php else: ?>
                                             <div class="alert alert-warning mt-2 mb-0 py-2">
                                                 <small><i class="fas fa-info-circle"></i> Technician not assigned yet</small>
                                             </div>
                                         <?php endif; ?>
                                     </div>
                                     <div class="booking-card-footer">
                                         <span class="booking-price">₹<?php echo number_format($booking->sb_total_price, 2); ?></span>
                                         <span class="view-details-btn">
                                             <i class="fas fa-arrow-right"></i> View Details
                                         </span>
                                     </div>
                                 </div>
                             </a>
                         <?php 
                             endwhile;
                         else:
                         ?>
                             <div class="text-center py-5">
                                 <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                 <h4>No Bookings Yet</h4>
                                 <p class="text-muted">You haven't made any bookings yet.</p>
                                 <a href="usr-book-service-simple.php" class="btn btn-primary btn-lg mt-3">
                                     <i class="fas fa-plus-circle"></i> Book a Service
                                 </a>
                             </div>
                         <?php endif; ?>
                     </div>
                 </div>
                 
                 <style>
                 .booking-card {
                     background: white;
                     border-radius: 15px;
                     border: 2px solid #e9ecef;
                     transition: all 0.3s ease;
                     overflow: hidden;
                 }
                 
                 .booking-card:hover {
                     transform: translateY(-3px);
                     box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
                     border-color: #667eea;
                 }
                 
                 .booking-card-header {
                     background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                     padding: 15px 20px;
                     display: flex;
                     justify-content: space-between;
                     align-items: center;
                 }
                 
                 .booking-id {
                     font-size: 18px;
                     font-weight: 700;
                     color: #667eea;
                 }
                 
                 .booking-card-body {
                     padding: 20px;
                 }
                 
                 .technician-info {
                     background: #f8f9fa;
                     padding: 10px 15px;
                     border-radius: 10px;
                     font-size: 14px;
                     color: #495057;
                 }
                 
                 .booking-card-footer {
                     background: #f8f9fa;
                     padding: 15px 20px;
                     display: flex;
                     justify-content: space-between;
                     align-items: center;
                     border-top: 2px solid #e9ecef;
                 }
                 
                 .booking-price {
                     font-size: 24px;
                     font-weight: 900;
                     color: #28a745;
                 }
                 
                 .view-details-btn {
                     color: #667eea;
                     font-weight: 700;
                 }
                 
                 .booking-card-link:hover .view-details-btn {
                     color: #764ba2;
                 }
                 </style>
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
                     <a class="btn btn-danger" href="user-logout.php">Logout</a>
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

 </body>

 </html>