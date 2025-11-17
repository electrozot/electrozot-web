<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  //Add Booking
  if(isset($_POST['book']))
    {
            $u_id = $_GET['u_id'];
            // Map to correct tms_user columns
            $t_booking_date = isset($_POST['t_booking_date']) ? $_POST['t_booking_date'] : '';
            $t_booking_status  = isset($_POST['t_booking_status']) ? $_POST['t_booking_status'] : '';
            
            $query="UPDATE tms_user SET t_booking_date=?, t_booking_status=? WHERE u_id=?";
            $stmt = $mysqli->prepare($query);
            if($stmt){
              $rc=$stmt->bind_param('ssi', $t_booking_date, $t_booking_status, $u_id);
              $stmt->execute();
            }
            if($stmt && $stmt->affected_rows >= 0)
            {
                $succ = "User Booking Added";
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
                     <li class="breadcrumb-item active">Add</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Add Booking
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
                         
                         <form method="POST">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">First Name</label>
                                 <input type="text" value="<?php echo $row->u_fname;?>" required class="form-control" id="exampleInputEmail1" name="u_fname">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Last Name</label>
                                 <input type="text" class="form-control" value="<?php echo $row->u_lname;?>" id="exampleInputEmail1" name="u_lname">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Contact <span class="text-danger">*</span></label>
                                 <input type="tel" class="form-control" value="<?php echo $row->u_phone;?>" id="exampleInputEmail1" name="u_phone" required maxlength="10" pattern="[0-9]{10}" title="Enter exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" placeholder="10-digit mobile number">
                                 <small class="form-text text-muted">Enter exactly 10 digits</small>
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Address</label>
                                 <input type="text" class="form-control" value="<?php echo $row->u_addr;?>" id="exampleInputEmail1" name="u_addr">
                             </div>

                             <div class="form-group" style="display:none">
                                 <label for="exampleInputEmail1">Category</label>
                                 <input type="text" class="form-control" id="exampleInputEmail1" value="User" name="u_category">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Email address</label>
                                 <input type="email" value="<?php echo $row->u_email;?>" class="form-control" name="u_email">
            </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Booking Date</label>
                                 <input type="date" class="form-control" id="exampleInputEmail1" name="t_booking_date">
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Booking Status</label>
                                 <select class="form-control" name="t_booking_status" id="exampleFormControlSelect1">
                                     <option>Approved</option>
                                     <option>Pending</option>
                                 </select>
                             </div>

                             <button type="submit" name="book" class="btn btn-success">Confirm Booking</button>
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