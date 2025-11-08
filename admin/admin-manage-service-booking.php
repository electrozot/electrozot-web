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
                         <a href="#">Service Bookings</a>
                     </li>
                     <li class="breadcrumb-item active">Manage</li>
                 </ol>

                 <!--Service Bookings-->
                 <div class="card mb-3">
                     <div class="card-header">
                         <i class="fas fa-table"></i>
                         Service Bookings
                     </div>
                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                 <thead>
                                     <tr>
                                         <th>#</th>
                                         <th>Customer Name</th>
                                         <th>Service</th>
                                         <th>Booking Date</th>
                                         <th>Booking Time</th>
                                         <th>Assigned Technician</th>
                                         <th>Status</th>
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php
                  $ret="SELECT sb.*, u.u_fname, u.u_lname, s.s_name, t.t_name as tech_name 
                        FROM tms_service_booking sb
                        LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                        LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                        WHERE sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                        ORDER BY sb.sb_created_at DESC"; 
                  $stmt= $mysqli->prepare($ret) ;
                  $stmt->execute();
                  $res=$stmt->get_result();
                  $cnt=1;
                  while($row=$res->fetch_object())
                {
                ?>
                                     <tr>
                                         <td><?php echo $cnt;?></td>
                                         <td><?php echo $row->u_fname;?> <?php echo $row->u_lname;?></td>
                                         <td><?php echo $row->s_name;?></td>
                                         <td><?php echo date('M d, Y', strtotime($row->sb_booking_date));?></td>
                                         <td><?php echo date('h:i A', strtotime($row->sb_booking_time));?></td>
                                         <td><?php echo $row->tech_name ? $row->tech_name : '<span class="badge badge-warning">Not Assigned</span>';?></td>
                                         <td>
                                             <?php 
                                             if($row->sb_status == "Pending"){ 
                                                 echo '<span class="badge badge-warning">'.$row->sb_status.'</span>'; 
                                             } elseif($row->sb_status == "Approved"){ 
                                                 echo '<span class="badge badge-info">'.$row->sb_status.'</span>'; 
                                             } elseif($row->sb_status == "In Progress"){ 
                                                 echo '<span class="badge badge-primary">'.$row->sb_status.'</span>'; 
                                             } elseif($row->sb_status == "Completed"){ 
                                                 echo '<span class="badge badge-success">'.$row->sb_status.'</span>'; 
                                             } else { 
                                                 echo '<span class="badge badge-danger">'.$row->sb_status.'</span>'; 
                                             }
                                             ?>
                                         </td>
                                         <td>
                                             <a href="admin-assign-technician.php?sb_id=<?php echo $row->sb_id;?>" class="badge badge-success">Assign Technician</a>
                                             <a href="admin-view-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="badge badge-info">View Details</a>
                                         </td>
                                     </tr>
                                     <?php $cnt = $cnt+1; }?>

                                 </tbody>
                             </table>
                         </div>
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

