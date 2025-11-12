<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  // Ensure technician password column exists
  try {
    $colChk = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tms_technician' AND COLUMN_NAME = 't_pwd'");
    if($colChk){
      $hasPwdCol = $colChk->fetch_object();
      if(!$hasPwdCol || intval($hasPwdCol->c) === 0){
        $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_pwd VARCHAR(200) NOT NULL DEFAULT ''");
      }
    }
  } catch(Exception $e) { /* ignore */ }
  //Add Technician
  if(isset($_POST['add_tech']))
    {

            $t_name=$_POST['t_name'];
            $t_id_no = $_POST['t_id_no'];
            $t_category=$_POST['t_category'];
            $t_experience=$_POST['t_experience'];
            $t_status=$_POST['t_status'];
            $t_pwd = isset($_POST['t_pwd']) ? $_POST['t_pwd'] : '';
            $t_specialization=$_POST['t_specialization'];
            $t_pic=$_FILES["t_pic"]["name"];
	        move_uploaded_file($_FILES["t_pic"]["tmp_name"],"../vendor/img/".$_FILES["t_pic"]["name"]);
            $query="insert into tms_technician (t_name, t_experience, t_id_no, t_specialization, t_category, t_pic, t_status, t_pwd ) values(?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('ssssssss', $t_name, $t_experience, $t_id_no, $t_specialization, $t_category, $t_pic, $t_status, $t_pwd);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Technician Added";
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
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Technicians</a>
                     </li>
                     <li class="breadcrumb-item active">Add Technician</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Add Technician
                     </div>
                     <div class="card-body">
                         <!--Add Technician Form-->
                         <form method="POST" enctype="multipart/form-data">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician Name</label>
                                 <input type="text" required class="form-control" id="exampleInputEmail1" name="t_name">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician ID Number</label>
                                 <input type="text" class="form-control" id="exampleInputEmail1" name="t_id_no">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Years of Experience</label>
                                 <input type="text" class="form-control" id="exampleInputEmail1" name="t_experience" placeholder="e.g., 5">
                             </div>
                             <div class="form-group">
                                 <label for="t_pwd">Technician Password</label>
                                 <input type="password" class="form-control" id="t_pwd" name="t_pwd" placeholder="Set login password" required>
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Specialization</label>
                                 <input type="text" class="form-control" id="exampleInputEmail1" name="t_specialization" placeholder="e.g., Electrical Repairs, Plumbing">
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Category</label>
                                 <select class="form-control" name="t_category" id="exampleFormControlSelect1">
                                     <option>Electrical</option>
                                     <option>Plumbing</option>
                                     <option>HVAC</option>
                                     <option>Appliance</option>
                                     <option>General</option>

                                 </select>
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Technician Status</label>
                                 <select class="form-control" name="t_status" id="exampleFormControlSelect1">
                                     <option>Booked</option>
                                     <option>Available</option>

                                 </select>
                             </div>
                             <div class="form-group col-md-12">
                                 <label for="exampleInputEmail1">Technician Picture</label>
                                 <input type="file" class="btn btn-success" id="exampleInputEmail1" name="t_pic">
                             </div>

                             <button type="submit" name="add_tech" class="btn btn-success">Add Technician</button>
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