<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  //Assign Technician
  if(isset($_POST['assign_technician']))
    {
            $sb_id = $_GET['sb_id'];
            $sb_technician_id = $_POST['sb_technician_id'];
            $sb_status = $_POST['sb_status'];
            
            $query="UPDATE tms_service_booking SET sb_technician_id=?, sb_status=? WHERE sb_id=?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('isi', $sb_technician_id, $sb_status, $sb_id);
            $stmt->execute();
                if($stmt)
                {
                    // Update technician status to Booked
                    $update_tech = "UPDATE tms_technician SET t_status='Booked' WHERE t_id=?";
                    $tech_stmt = $mysqli->prepare($update_tech);
                    $tech_stmt->bind_param('i', $sb_technician_id);
                    $tech_stmt->execute();
                    
                    $succ = "Technician Assigned Successfully";
                }
                else 
                {
                    $err = "Please Try Again Later";
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
                 <?php if(isset($succ)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Success!", "<?php echo $succ;?>!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "Failed");
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
            $sb_id=$_GET['sb_id'];
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
                             <div class="form-group">
                                 <label for="sb_technician_id">Select Technician *</label>
                                 <select class="form-control" name="sb_technician_id" id="sb_technician_id" required>
                                     <option value="">Select Technician</option>
                                     <?php
                                     $tech_query = "SELECT * FROM tms_technician WHERE t_category = ? AND (t_status = 'Available' OR t_id = ?)";
                                     $tech_stmt = $mysqli->prepare($tech_query);
                                     $tech_stmt->bind_param('si', $booking_data->s_category, $booking_data->sb_technician_id);
                                     $tech_stmt->execute();
                                     $tech_result = $tech_stmt->get_result();
                                     while($tech = $tech_result->fetch_object()) {
                                         $selected = ($tech->t_id == $booking_data->sb_technician_id) ? 'selected' : '';
                                         echo '<option value="'.$tech->t_id.'" '.$selected.'>'.$tech->t_name.' - '.$tech->t_specialization.' ('.$tech->t_status.')</option>';
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

