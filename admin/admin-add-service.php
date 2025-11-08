<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  //Add Service
  if(isset($_POST['add_service']))
    {
            $s_name=$_POST['s_name'];
            $s_description = $_POST['s_description'];
            $s_category=$_POST['s_category'];
            $s_price=$_POST['s_price'];
            $s_duration=$_POST['s_duration'];
            $s_status=$_POST['s_status'];
            $query="insert into tms_service (s_name, s_description, s_category, s_price, s_duration, s_status) values(?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssdss', $s_name, $s_description, $s_category, $s_price, $s_duration, $s_status);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Service Added";
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
         <?php include("vendor/inc/sidebar.php");?>
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
                         <a href="#">Services</a>
                     </li>
                     <li class="breadcrumb-item active">Add Service</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Add Service
                     </div>
                     <div class="card-body">
                         <!--Add Service Form-->
                         <form method="POST">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Name</label>
                                 <input type="text" required class="form-control" id="exampleInputEmail1" name="s_name" placeholder="Enter Service Name">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Description</label>
                                 <textarea class="form-control" required name="s_description" rows="4" placeholder="Enter Service Description"></textarea>
                             </div>
                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Category</label>
                                 <select class="form-control" name="s_category" id="exampleFormControlSelect1" required>
                                     <option value="">Select Category</option>
                                     <option>Electrical</option>
                                     <option>Plumbing</option>
                                     <option>HVAC</option>
                                     <option>Appliance</option>
                                     <option>General</option>
                                 </select>
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Price</label>
                                 <input type="number" step="0.01" required class="form-control" id="exampleInputEmail1" name="s_price" placeholder="Enter Service Price">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Duration</label>
                                 <input type="text" required class="form-control" id="exampleInputEmail1" name="s_duration" placeholder="e.g., 2-3 hours">
                             </div>
                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Status</label>
                                 <select class="form-control" name="s_status" id="exampleFormControlSelect1" required>
                                     <option>Active</option>
                                     <option>Inactive</option>
                                 </select>
                             </div>
                             <hr>
                             <button type="submit" name="add_service" class="btn btn-success">Add Service</button>
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
         <script src="vendor/js/swal.js"></script>

 </body>

 </html>

