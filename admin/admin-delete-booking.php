<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  // Delete legacy user booking: snapshot then clear booking fields
  if(isset($_POST['delete_booking']))
    {
            $u_id = $_GET['u_id'];
            
            // Snapshot current user row into recycle bin
            $snap_sql = "SELECT * FROM tms_user WHERE u_id=?";
            if($snap_stmt = $mysqli->prepare($snap_sql)) {
              $snap_stmt->bind_param('i', $u_id);
              $snap_stmt->execute();
              $snap_res = $snap_stmt->get_result();
              $snap = $snap_res->fetch_assoc();
              if($snap) {
                $payload = json_encode($snap);
                if($rb_ins = $mysqli->prepare("INSERT INTO tms_recycle_bin (rb_type, rb_table, rb_object_id, rb_payload, rb_deleted_by) VALUES ('legacy_booking','tms_user', ?, ?, ?)")) {
                  $rb_ins->bind_param('isi', $u_id, $payload, $aid);
                  $rb_ins->execute();
                }
              }
            }
            
            // Clear booking fields to mark as deleted
            $query="UPDATE tms_user SET t_booking_date = NULL, t_booking_status = NULL WHERE u_id=?";
            $stmt = $mysqli->prepare($query);
            if($stmt){
              $stmt->bind_param('i', $u_id);
              $stmt->execute();
              if($stmt->affected_rows >= 0){
                $succ = "Booking deleted and sent to Recycle Bin";
              } else {
                $err = "Failed to delete booking";
              }
            } else {
              $err = "Please Try Again Later";
            }
    }
?>
 <!DOCTYPE html>
 <html lang="en">
 <?php include('vendor/inc/head.php');?>

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
                 <?php if(isset($succ)) {?>
                 <!--This code for injecting an alert-->
                 <script>
                 setTimeout(function() {
                         swal("Success!", "<?php echo $succ;?>!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <!--This code for injecting an alert-->
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "Failed");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <p>
                 </p>
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Bookings</a>
                     </li>
                     <li class="breadcrumb-item active">Approve</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Approve Booking
                     </div>
                     <div class="card-body">
                         <!--Add User Form-->
                         <?php
            $aid=$_GET['u_id'];
            $ret="select * from tms_user where u_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>
                         <!-- Author By: MH RONY
        Author Website: https://developerrony.com
        Github Link: https://github.com/dev-mhrony
        Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
        -->
                         <form method="POST">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">First Name</label>
                                 <input type="text" readonly value="<?php echo $row->u_fname;?>" required class="form-control" id="exampleInputEmail1" name="u_fname">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Last Name</label>
                                 <input type="text" readonly class="form-control" value="<?php echo $row->u_lname;?>" id="exampleInputEmail1" name="u_lname">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Contact</label>
                                 <input type="text" readonly class="form-control" value="<?php echo $row->u_phone;?>" id="exampleInputEmail1" name="u_phone">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Address</label>
                                 <input type="text" readonly class="form-control" value="<?php echo $row->u_addr;?>" id="exampleInputEmail1" name="u_addr">
                             </div>

                             <div class="form-group" style="display:none">
                                 <label for="exampleInputEmail1">Category</label>
                                 <input type="text" readonly class="form-control" id="exampleInputEmail1" value="User" name="u_category">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Email address</label>
                                 <input type="email" readonly value="<?php echo $row->u_email;?>" class="form-control" name="u_email"">
            </div>
            <div class=" form-group">
                                 <label for="exampleInputEmail1">Vehicle Category</label>
                                 <input type="email" readonly placeholder="<?php echo $row->u_car_type;?>" class="form-control" name="u_car_type">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Vehicle Registration NUmber</label>
                                 <input type="email" readonly placeholder="<?php echo $row->u_car_regno;?>" class="form-control" name="u_car_regno">
                             </div>


                             <div class="form-group">
                                 <label for="exampleInputEmail1">Booking Date</label>
                                 <input type="text" readonly placeholder="<?php echo $row->t_booking_date;?>" class="form-control" name="t_booking_date">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Booking Status</label>
                                 <input type="text" readonly placeholder="<?php echo $row->t_booking_status;?>" class="form-control" id="exampleInputEmail1" name="t_booking_status">
                             </div>


                             <button type="submit" name="delete_booking" class="btn btn-danger">Delete Booking</button>
                         </form>
                         <!-- End Form-->
                         <?php }?>
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
         <!--INject Sweet alert js-->
         <script src="vendor/js/swal.js"></script>

 </body>

 </html>