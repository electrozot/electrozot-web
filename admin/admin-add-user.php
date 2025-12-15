<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  //Add USer
  if(isset($_POST['add_user']))
    {

            $u_fname=$_POST['u_fname'];
            $u_lname = $_POST['u_lname'];
            $u_phone=$_POST['u_phone'];
            $u_addr=$_POST['u_addr'];
            $u_email=$_POST['u_email'];
            $u_pwd=$_POST['u_pwd'];
            $u_category=$_POST['u_category'];
            
            // Check if mobile number already exists
            $check_phone = $mysqli->prepare("SELECT u_id FROM tms_user WHERE u_phone = ?");
            $check_phone->bind_param('s', $u_phone);
            $check_phone->execute();
            $check_phone->store_result();
            
            if($check_phone->num_rows > 0) {
                $err = "This mobile number is already registered. Each mobile number can only have one account.";
            } else {
                $query="insert into tms_user (u_fname, u_lname, u_phone, u_addr, u_category, u_email, u_pwd) values(?,?,?,?,?,?,?)";
                $stmt = $mysqli->prepare($query);
                $rc=$stmt->bind_param('sssssss', $u_fname,  $u_lname, $u_phone, $u_addr, $u_category, $u_email, $u_pwd);
                $stmt->execute();
                    if($stmt)
                    {
                        $succ = "User Added";
                    }
                    else 
                    {
                        $err = "Please Try Again Later";
                    }
            }
            $check_phone->close();
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
                         <a href="#">Users</a>
                     </li>
                     <li class="breadcrumb-item active">Add User</li>
                 </ol>
                 <hr>
                 <div class="card shadow mb-4">
                     <div class="card-header py-3 d-flex align-items-center">
                         <h6 class="m-0 font-weight-bold text-primary">
                             <i class="fas fa-user-plus"></i> Add New User
                         </h6>
                     </div>
                     <div class="card-body">
                         <!--Add User Form-->
                         <form method="POST" id="addUserForm">
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="u_fname">First Name <span class="text-danger">*</span></label>
                                         <input type="text" required class="form-control" id="u_fname" name="u_fname" placeholder="Enter first name">
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="u_lname">Last Name</label>
                                         <input type="text" class="form-control" id="u_lname" name="u_lname" placeholder="Enter last name">
                                     </div>
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="u_phone">Contact Number <span class="text-danger">*</span></label>
                                         <input type="tel" required class="form-control" id="u_phone" name="u_phone" placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}" maxlength="10">
                                         <small class="form-text text-muted">Enter 10-digit mobile number</small>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="u_email">Email Address <span class="text-danger">*</span></label>
                                         <input type="email" required class="form-control" id="u_email" name="u_email" placeholder="Enter email address">
                                     </div>
                                 </div>
                             </div>

                             <div class="form-group">
                                 <label for="u_addr">Address</label>
                                 <textarea class="form-control" id="u_addr" name="u_addr" rows="3" placeholder="Enter complete address"></textarea>
                             </div>

                             <div class="form-group" style="display:none">
                                 <label for="u_category">Category</label>
                                 <input type="text" class="form-control" id="u_category" value="User" name="u_category">
                             </div>

                             <div class="form-group">
                                 <label for="u_pwd">Password <span class="text-danger">*</span></label>
                                 <input type="password" required class="form-control" name="u_pwd" id="u_pwd" placeholder="Enter password" minlength="6">
                                 <small class="form-text text-muted">Password must be at least 6 characters</small>
                             </div>

                             <hr>
                             <div class="form-group mb-0">
                                 <button type="submit" name="add_user" class="btn btn-success">
                                     <i class="fas fa-save"></i> Add User
                                 </button>
                                 <a href="admin-manage-user.php" class="btn btn-secondary ml-2">
                                     <i class="fas fa-times"></i> Cancel
                                 </a>
                             </div>
                         </form>
                         <!-- End Form-->
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