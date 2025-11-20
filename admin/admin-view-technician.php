<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
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
                         <a href="#">Technicians</a>
                     </li>
                     <li class="breadcrumb-item active">View Technicians</li>
                 </ol>

                 <!-- DataTables Example -->
                 <div class="card mb-3">
                     <div class="card-header">
                         <i class="fas fa-tools"></i>
                         Technicians
                     </div>
                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                                 <thead>
                                     <tr>
                                         <th>#</th>
                                         <th>Name</th>
                                         <th>ID Number</th>
                                         <th>Specialization</th>
                                         <th>Experience</th>
                                         <th>Category</th>
                                         <th>Status</th>
                                         <th>Actions</th>
                                     </tr>
                                 </thead>
                                 <?php

                    $ret="SELECT * FROM tms_technician "; 
                    $stmt= $mysqli->prepare($ret) ;
                    $stmt->execute() ;//ok
                    $res=$stmt->get_result();
                    $cnt=1;
                    while($row=$res->fetch_object())
                {
                ?>
                                 
                                 <tbody>
                                     <tr>
                                         <td><?php echo $cnt;?></td>
                                         <td><?php echo $row->t_name;?></td>
                                         <td><?php echo $row->t_id_no;?></td>
                                         <td><?php echo $row->t_specialization;?></td>
                                         <td><?php echo $row->t_experience;?> years</td>
                                         <td><?php echo $row->t_category;?></td>
                                         <td><?php if($row->t_status == "Available"){ echo '<span class = "badge badge-success">'.$row->t_status.'</span>'; } else { echo '<span class = "badge badge-danger">'.$row->t_status.'</span>';}?></td>
                                         <td>
                                             <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal<?php echo $row->t_id;?>" title="View Availability">
                                                 <i class="fas fa-eye"></i> View Details
                                             </button>
                                         </td>
                                     </tr>
                                 </tbody>
                                 <?php $cnt = $cnt+1; }?>

                             </table>
                         </div>
                     </div>
                 </div>

                 <!-- Modals for each technician -->
                 <?php
                    $ret="SELECT * FROM tms_technician "; 
                    $stmt= $mysqli->prepare($ret) ;
                    $stmt->execute();
                    $res=$stmt->get_result();
                    while($row=$res->fetch_object())
                    {
                        // Get concurrent booking capacity from technician record
                        $total_slots = isset($row->t_booking_limit) ? $row->t_booking_limit : 1;
                        $current_bookings = isset($row->t_current_bookings) ? $row->t_current_bookings : 0;
                        $available_slots = max(0, $total_slots - $current_bookings);
                        
                        // Get today's bookings count (excluding cancelled/rejected)
                        $today = date('Y-m-d');
                        $today_query = "SELECT COUNT(*) as today_count FROM tms_service_booking 
                                       WHERE sb_technician_id = ? 
                                       AND DATE(sb_booking_date) = ?
                                       AND sb_status NOT IN ('Cancelled', 'Rejected', 'Rejected by Technician')";
                        $today_stmt = $mysqli->prepare($today_query);
                        $today_stmt->bind_param('is', $row->t_id, $today);
                        $today_stmt->execute();
                        $today_result = $today_stmt->get_result();
                        $today_data = $today_result->fetch_object();
                        $today_bookings = $today_data ? $today_data->today_count : 0;
                        
                        // Get this month's bookings count (excluding cancelled/rejected)
                        $month_query = "SELECT COUNT(*) as month_count FROM tms_service_booking 
                                       WHERE sb_technician_id = ? 
                                       AND MONTH(sb_booking_date) = MONTH(CURDATE()) 
                                       AND YEAR(sb_booking_date) = YEAR(CURDATE())
                                       AND sb_status NOT IN ('Cancelled', 'Rejected', 'Rejected by Technician')";
                        $month_stmt = $mysqli->prepare($month_query);
                        $month_stmt->bind_param('i', $row->t_id);
                        $month_stmt->execute();
                        $month_result = $month_stmt->get_result();
                        $month_data = $month_result->fetch_object();
                        $month_bookings = $month_data ? $month_data->month_count : 0;
                        
                        // Get completed bookings count (all time)
                        $completed_query = "SELECT COUNT(*) as completed_count FROM tms_service_booking 
                                          WHERE sb_technician_id = ? 
                                          AND sb_status = 'Completed'";
                        $completed_stmt = $mysqli->prepare($completed_query);
                        $completed_stmt->bind_param('i', $row->t_id);
                        $completed_stmt->execute();
                        $completed_result = $completed_stmt->get_result();
                        $completed_data = $completed_result->fetch_object();
                        $completed_bookings = $completed_data ? $completed_data->completed_count : 0;
                        
                        // Get total bookings (all time, excluding cancelled/rejected)
                        $total_query = "SELECT COUNT(*) as total_count FROM tms_service_booking 
                                       WHERE sb_technician_id = ?
                                       AND sb_status NOT IN ('Cancelled', 'Rejected', 'Rejected by Technician')";
                        $total_stmt = $mysqli->prepare($total_query);
                        $total_stmt->bind_param('i', $row->t_id);
                        $total_stmt->execute();
                        $total_result = $total_stmt->get_result();
                        $total_data = $total_result->fetch_object();
                        $total_bookings = $total_data ? $total_data->total_count : 0;
                ?>
                 <!-- View Modal -->
                 <div class="modal fade" id="viewModal<?php echo $row->t_id;?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                     <div class="modal-dialog modal-lg" role="document">
                         <div class="modal-content">
                             <div class="modal-header bg-info text-white">
                                 <h5 class="modal-title" id="viewModalLabel">
                                     <i class="fas fa-user-cog"></i> Technician Details - <?php echo $row->t_name;?>
                                 </h5>
                                 <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                     <span aria-hidden="true">×</span>
                                 </button>
                             </div>
                             <div class="modal-body">
                                 <div class="row">
                                     <div class="col-md-6">
                                         <h6 class="font-weight-bold text-primary mb-3">
                                             <i class="fas fa-info-circle"></i> Basic Information
                                         </h6>
                                         <table class="table table-sm table-borderless">
                                             <tr>
                                                 <td class="font-weight-bold">Name:</td>
                                                 <td><?php echo $row->t_name;?></td>
                                             </tr>
                                             <tr>
                                                 <td class="font-weight-bold">ID Number:</td>
                                                 <td><?php echo $row->t_id_no;?></td>
                                             </tr>
                                             <tr>
                                                 <td class="font-weight-bold">Specialization:</td>
                                                 <td><?php echo $row->t_specialization;?></td>
                                             </tr>
                                             <tr>
                                                 <td class="font-weight-bold">Experience:</td>
                                                 <td><?php echo $row->t_experience;?> years</td>
                                             </tr>
                                             <tr>
                                                 <td class="font-weight-bold">Category:</td>
                                                 <td><span class="badge badge-primary"><?php echo $row->t_category;?></span></td>
                                             </tr>
                                         </table>
                                     </div>
                                     <div class="col-md-6">
                                         <h6 class="font-weight-bold text-success mb-3">
                                             <i class="fas fa-calendar-check"></i> Availability & Slots
                                         </h6>
                                         <div class="card border-success mb-3">
                                             <div class="card-body">
                                                 <h5 class="card-title">Current Status</h5>
                                                 <p class="mb-2">
                                                     <?php if($row->t_status == "Available"){ 
                                                         echo '<span class="badge badge-success" style="font-size: 1.1rem; padding: 8px 15px;">
                                                                 <i class="fas fa-check-circle"></i> Available
                                                               </span>'; 
                                                     } else { 
                                                         echo '<span class="badge badge-danger" style="font-size: 1.1rem; padding: 8px 15px;">
                                                                 <i class="fas fa-times-circle"></i> Unavailable
                                                               </span>';
                                                     }?>
                                                 </p>
                                                 <hr>
                                                 <h5 class="card-title mt-3">Concurrent Booking Capacity</h5>
                                                 <div class="row text-center">
                                                     <div class="col-6">
                                                         <div class="p-2 bg-light rounded">
                                                             <h4 class="text-primary mb-0"><?php echo $total_slots;?></h4>
                                                             <small class="text-muted">Max Capacity</small>
                                                         </div>
                                                     </div>
                                                     <div class="col-6">
                                                         <div class="p-2 bg-light rounded">
                                                             <h4 class="<?php echo $available_slots > 0 ? 'text-success' : 'text-danger';?> mb-0">
                                                                 <?php echo $available_slots;?>
                                                             </h4>
                                                             <small class="text-muted">Available</small>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="mt-3">
                                                     <div class="progress" style="height: 25px;">
                                                         <div class="progress-bar <?php echo $available_slots > 3 ? 'bg-success' : ($available_slots > 0 ? 'bg-warning' : 'bg-danger');?>" 
                                                              role="progressbar" 
                                                              style="width: <?php echo ($available_slots/$total_slots)*100;?>%;" 
                                                              aria-valuenow="<?php echo $available_slots;?>" 
                                                              aria-valuemin="0" 
                                                              aria-valuemax="<?php echo $total_slots;?>">
                                                             <?php echo $available_slots;?> / <?php echo $total_slots;?>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                         
                                         <div class="alert alert-info mt-2">
                                             <i class="fas fa-info-circle"></i> 
                                             <strong>Current Active Bookings:</strong> <?php echo $current_bookings;?> booking(s)
                                         </div>
                                         
                                         <?php if($current_bookings >= $total_slots): ?>
                                         <div class="alert alert-danger mt-2">
                                             <i class="fas fa-exclamation-triangle"></i> 
                                             <strong>At Full Capacity!</strong> No slots available.
                                         </div>
                                         <?php elseif($available_slots == 1): ?>
                                         <div class="alert alert-warning mt-2">
                                             <i class="fas fa-exclamation-circle"></i> 
                                             <strong>Almost Full!</strong> Only 1 slot remaining.
                                         </div>
                                         <?php endif; ?>
                                     </div>
                                 </div>
                                 
                                 <!-- Booking Statistics Section -->
                                 <div class="row mt-4">
                                     <div class="col-12">
                                         <h6 class="font-weight-bold text-primary mb-3">
                                             <i class="fas fa-chart-bar"></i> Booking Statistics
                                         </h6>
                                     </div>
                                 </div>
                                 
                                 <div class="row">
                                     <div class="col-md-3">
                                         <div class="card border-info mb-3">
                                             <div class="card-body text-center p-3">
                                                 <i class="fas fa-calendar-day fa-2x text-info mb-2"></i>
                                                 <h3 class="mb-1 text-info"><?php echo $today_bookings;?></h3>
                                                 <p class="mb-0 text-muted small">
                                                     <strong>Today's Bookings</strong><br>
                                                     <small><?php echo date('d M Y');?></small>
                                                 </p>
                                             </div>
                                         </div>
                                     </div>
                                     
                                     <div class="col-md-3">
                                         <div class="card border-primary mb-3">
                                             <div class="card-body text-center p-3">
                                                 <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                                                 <h3 class="mb-1 text-primary"><?php echo $month_bookings;?></h3>
                                                 <p class="mb-0 text-muted small">
                                                     <strong>This Month</strong><br>
                                                     <small><?php echo date('F Y');?></small>
                                                 </p>
                                             </div>
                                         </div>
                                     </div>
                                     
                                     <div class="col-md-3">
                                         <div class="card border-success mb-3">
                                             <div class="card-body text-center p-3">
                                                 <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                                 <h3 class="mb-1 text-success"><?php echo $completed_bookings;?></h3>
                                                 <p class="mb-0 text-muted small">
                                                     <strong>Completed</strong><br>
                                                     <small>All Time</small>
                                                 </p>
                                             </div>
                                         </div>
                                     </div>
                                     
                                     <div class="col-md-3">
                                         <div class="card border-secondary mb-3">
                                             <div class="card-body text-center p-3">
                                                 <i class="fas fa-clipboard-list fa-2x text-secondary mb-2"></i>
                                                 <h3 class="mb-1 text-secondary"><?php echo $total_bookings;?></h3>
                                                 <p class="mb-0 text-muted small">
                                                     <strong>Total Bookings</strong><br>
                                                     <small>All Time</small>
                                                 </p>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="modal-footer">
                                 <button class="btn btn-secondary" type="button" data-dismiss="modal">
                                     <i class="fas fa-times"></i> Close
                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>
                 <?php }?>
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