<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['u_id'];
?>
 <!DOCTYPE html>
 <html lang="en">

 <!--Head-->
 <?php include ('vendor/inc/head.php');?>
 <!--End Head-->

 <body id="page-top">
     <!--Navbar-->
     <?php include ('vendor/inc/nav.php');?>
     <!--End Navbar-->

     <div id="wrapper">

         <!-- Sidebar -->
         <?php include('vendor/inc/sidebar.php');?>
         <!--End Sidebar-->
         <div id="content-wrapper">

             <div class="container-fluid">
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="user-dashboard.php">Dashboard</a>
                     </li>
                     <li class="breadcrumb-item active">Overview</li>
                 </ol>

                 <!-- Icon Cards-->
                 <div class="row">
                     <div class="col-xl-3 col-sm-6 mb-3">
                         <div class="card text-white o-hidden h-100 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 15px;">
                             <div class="card-body">
                                 <div class="card-body-icon" style="opacity: 0.3;">
                                     <i class="fas fa-user-circle" style="font-size: 5rem;"></i>
                                 </div>
                                 <div class="mr-5" style="position: relative; z-index: 2;">
                                     <h4 class="mb-0" style="font-weight: 900;">My Profile</h4>
                                     <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">View & Update</p>
                                 </div>
                             </div>
                             <a class="card-footer text-white clearfix small z-1" href="user-view-profile.php" style="background: rgba(0,0,0,0.2); border: none;">
                                 <span class="float-left">View Profile</span>
                                 <span class="float-right">
                                     <i class="fas fa-arrow-circle-right"></i>
                                 </span>
                             </a>
                         </div>
                     </div>
                     <div class="col-xl-3 col-sm-6 mb-3">
                         <div class="card text-white o-hidden h-100 shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; border-radius: 15px;">
                             <div class="card-body">
                                 <div class="card-body-icon" style="opacity: 0.3;">
                                     <i class="fas fa-calendar-alt" style="font-size: 5rem;"></i>
                                 </div>
                                 <div class="mr-5" style="position: relative; z-index: 2;">
                                     <h4 class="mb-0" style="font-weight: 900;">My Bookings</h4>
                                     <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">View All</p>
                                 </div>
                             </div>
                             <a class="card-footer text-white clearfix small z-1" href="user-view-booking.php" style="background: rgba(0,0,0,0.2); border: none;">
                                 <span class="float-left">View Details</span>
                                 <span class="float-right">
                                     <i class="fas fa-arrow-circle-right"></i>
                                 </span>
                             </a>
                         </div>
                     </div>
                     <div class="col-xl-3 col-sm-6 mb-3">
                         <div class="card text-white o-hidden h-100 shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; border-radius: 15px;">
                             <div class="card-body">
                                 <div class="card-body-icon" style="opacity: 0.3;">
                                     <i class="fas fa-wrench" style="font-size: 5rem;"></i>
                                 </div>
                                 <div class="mr-5" style="position: relative; z-index: 2;">
                                     <h4 class="mb-0" style="font-weight: 900;">Book Service</h4>
                                     <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">New Booking</p>
                                 </div>
                             </div>
                             <a class="card-footer text-white clearfix small z-1" href="usr-book-service-simple.php" style="background: rgba(0,0,0,0.2); border: none;">
                                 <span class="float-left">Book Now</span>
                                 <span class="float-right">
                                     <i class="fas fa-arrow-circle-right"></i>
                                 </span>
                             </a>
                         </div>
                     </div>
                     <div class="col-xl-3 col-sm-6 mb-3">
                         <div class="card text-white o-hidden h-100 shadow-lg" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; border-radius: 15px;">
                             <div class="card-body">
                                 <div class="card-body-icon" style="opacity: 0.3;">
                                     <i class="fas fa-comment-dots" style="font-size: 5rem;"></i>
                                 </div>
                                 <div class="mr-5" style="position: relative; z-index: 2;">
                                     <h4 class="mb-0" style="font-weight: 900;">Feedback</h4>
                                     <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">Share Experience</p>
                                 </div>
                             </div>
                             <a class="card-footer text-white clearfix small z-1" href="user-give-feedback.php" style="background: rgba(0,0,0,0.2); border: none;">
                                 <span class="float-left">Give Feedback</span>
                                 <span class="float-right">
                                     <i class="fas fa-arrow-circle-right"></i>
                                 </span>
                             </a>
                         </div>
                     </div>
                 </div>

                 <!-- Mobile Quick Actions -->
                 <div class="mobile-quick-actions d-md-none mb-4">
                     <a href="usr-book-service-simple.php" class="quick-action-btn">
                         <i class="fas fa-plus-circle"></i>
                         <span>Quick Book</span>
                     </a>
                     <a href="user-track-booking.php" class="quick-action-btn">
                         <i class="fas fa-map-marker-alt"></i>
                         <span>Track</span>
                     </a>
                     <a href="user-view-booking.php" class="quick-action-btn">
                         <i class="fas fa-list"></i>
                         <span>My Orders</span>
                     </a>
                 </div>

                 <style>
                 .mobile-quick-actions {
                     display: flex;
                     gap: 10px;
                     padding: 0 15px;
                 }
                 
                 .quick-action-btn {
                     flex: 1;
                     background: white;
                     border-radius: 15px;
                     padding: 20px 10px;
                     text-align: center;
                     text-decoration: none;
                     box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                     transition: all 0.3s ease;
                 }
                 
                 .quick-action-btn:hover {
                     text-decoration: none;
                     transform: translateY(-3px);
                     box-shadow: 0 6px 20px rgba(0,0,0,0.15);
                 }
                 
                 .quick-action-btn i {
                     display: block;
                     font-size: 28px;
                     margin-bottom: 8px;
                     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                     -webkit-background-clip: text;
                     -webkit-text-fill-color: transparent;
                     background-clip: text;
                 }
                 
                 .quick-action-btn span {
                     display: block;
                     font-size: 12px;
                     font-weight: 700;
                     color: #495057;
                 }
                 
                 @media (max-width: 768px) {
                     .card.shadow-lg {
                         border-radius: 15px !important;
                         margin-bottom: 15px !important;
                     }
                     
                     .card-body {
                         padding: 15px !important;
                     }
                     
                     .row > div[class*="col-"] {
                         padding-left: 10px !important;
                         padding-right: 10px !important;
                     }
                 }
                 
                 /* Service Cards Dashboard */
                 .service-card-dashboard {
                     background: white;
                     border-radius: 15px;
                     padding: 20px;
                     box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                     transition: all 0.3s ease;
                     border: 3px solid transparent;
                     height: 100%;
                     display: flex;
                     flex-direction: column;
                 }
                 
                 .service-card-dashboard:hover {
                     transform: translateY(-5px);
                     box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
                     border-color: #667eea;
                 }
                 
                 .service-icon-dash {
                     width: 60px;
                     height: 60px;
                     border-radius: 15px;
                     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                     display: flex;
                     align-items: center;
                     justify-content: center;
                     color: white;
                     font-size: 28px;
                     margin-bottom: 15px;
                 }
                 
                 .service-name-dash {
                     font-size: 18px;
                     font-weight: 700;
                     color: #333;
                     margin-bottom: 10px;
                 }
                 
                 .service-category-dash {
                     font-size: 14px;
                     color: #667eea;
                     font-weight: 600;
                     margin-bottom: 15px;
                 }
                 
                 .service-details-dash {
                     display: flex;
                     justify-content: space-between;
                     align-items: center;
                     margin-bottom: 15px;
                     padding-top: 15px;
                     border-top: 2px solid #f0f0f0;
                 }
                 
                 .service-price-dash {
                     font-size: 24px;
                     font-weight: 900;
                     color: #28a745;
                 }
                 
                 .service-duration-dash {
                     font-size: 12px;
                     color: #6c757d;
                     font-weight: 600;
                 }
                 
                 .book-now-btn {
                     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                     color: white;
                     padding: 12px;
                     border-radius: 10px;
                     text-align: center;
                     font-weight: 700;
                     margin-top: auto;
                 }
                 
                 .service-card-link:hover .book-now-btn {
                     background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
                 }
                 </style>

                 <!--Available Services-->
                 <div class="card mb-3 shadow-lg" style="border: none; border-radius: 15px;">
                     <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                         <h6 class="m-0 font-weight-bold text-white">
                             <i class="fas fa-wrench"></i> Available Services - Click to Book
                         </h6>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <?php
                             $services_query = "SELECT * FROM tms_service WHERE s_status = 'Active' ORDER BY s_category, s_name";
                             $services_result = $mysqli->query($services_query);
                             
                             if($services_result->num_rows > 0):
                                 while($service = $services_result->fetch_object()):
                             ?>
                                 <div class="col-md-4 col-sm-6 mb-3">
                                     <a href="usr-book-service-simple.php?service_id=<?php echo $service->s_id; ?>" class="service-card-link" style="text-decoration: none;">
                                         <div class="service-card-dashboard">
                                             <div class="service-icon-dash">
                                                 <i class="fas fa-tools"></i>
                                             </div>
                                             <h5 class="service-name-dash"><?php echo $service->s_name; ?></h5>
                                             <p class="service-category-dash">
                                                 <i class="fas fa-tag"></i> <?php echo $service->s_category; ?>
                                             </p>
                                             <div class="service-details-dash">
                                                 <span class="service-price-dash">₹<?php echo number_format($service->s_price, 0); ?></span>
                                                 <span class="service-duration-dash">
                                                     <i class="fas fa-clock"></i> <?php echo $service->s_duration; ?>
                                                 </span>
                                             </div>
                                             <div class="book-now-btn">
                                                 <i class="fas fa-calendar-plus"></i> Book Now
                                             </div>
                                         </div>
                                     </a>
                                 </div>
                             <?php 
                                 endwhile;
                             else:
                             ?>
                                 <div class="col-12 text-center py-5">
                                     <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                                     <h4>No Services Available</h4>
                                     <p class="text-muted">Please check back later</p>
                                 </div>
                             <?php endif; ?>
                         </div>
                     </div>
                     <div class="card-footer small text-muted">
                         <?php
              date_default_timezone_set("Africa/Nairobi");
              echo "Generated At : " . date("h:i:sa");
            ?>
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