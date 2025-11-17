<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  // Add booking limit columns if they don't exist
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT NOT NULL DEFAULT 1");
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT NOT NULL DEFAULT 0");
  
  //Update Technician
  if(isset($_POST['update_tech']))
    {
            $t_id = $_GET['t_id'];
            $t_name=$_POST['t_name'];
            $t_id_no = $_POST['t_id_no'];
            $t_category=$_POST['t_category'];
            $t_status=$_POST['t_status'];
            $t_specialization=$_POST['t_specialization'];
            $t_experience=$_POST['t_experience'];
            $t_booking_limit = isset($_POST['t_booking_limit']) ? intval($_POST['t_booking_limit']) : 1;
            
            // Validate booking limit (1-5)
            if($t_booking_limit < 1 || $t_booking_limit > 5) {
                $t_booking_limit = 1;
            }
            
            $t_pic=$_FILES["t_pic"]["name"];
            move_uploaded_file($_FILES["t_pic"]["tmp_name"],"../vendor/img/".$_FILES["t_pic"]["name"]);
            $query="update tms_technician set t_name=?, t_id_no=?, t_specialization=?, t_category=?, t_experience=?, t_pic=?, t_status=?, t_booking_limit=? where t_id = ?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssssssii', $t_name, $t_id_no, $t_specialization, $t_category, $t_experience, $t_pic, $t_status, $t_booking_limit, $t_id);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Technician Updated";
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
                     <li class="breadcrumb-item active">Update Technician</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Update Technician
                     </div>
                     <div class="card-body">
                         <!--Update Technician Form-->
                         <?php
            $aid=$_GET['t_id'];
            $ret="select * from tms_technician where t_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>
                         
                         <form method="POST" enctype="multipart/form-data">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician Name</label>
                                 <input type="text" value="<?php echo $row->t_name;?>" required class="form-control" id="exampleInputEmail1" name="t_name">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician ID Number</label>
                                 <input type="text" value="<?php echo $row->t_id_no;?>" class="form-control" id="exampleInputEmail1" name="t_id_no">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Specialization</label>
                                 <input type="text" value="<?php echo $row->t_specialization;?>" class="form-control" id="exampleInputEmail1" name="t_specialization">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Years of Experience</label>
                                 <input type="text" value="<?php echo $row->t_experience;?>" class="form-control" id="exampleInputEmail1" name="t_experience">
                             </div>

                             <div class="form-group">
                                 <label for="t_category">
                                     <i class="fas fa-tools"></i> Service Category
                                 </label>
                                 <input type="text" class="form-control" value="<?php echo htmlspecialchars($row->t_category);?>" readonly style="background-color: #e9ecef;">
                                 <input type="hidden" name="t_category" value="<?php echo htmlspecialchars($row->t_category);?>">
                                 <small class="form-text text-muted">
                                     <i class="fas fa-info-circle text-primary"></i> Service category is set during technician creation and cannot be changed here. This ensures consistency in technician assignments.
                                 </small>
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Technician Status</label>
                                 <select class="form-control" name="t_status" id="exampleFormControlSelect1">
                                     <option>Booked</option>
                                     <option>Available</option>
                                 </select>
                             </div>
                             
                             <div class="form-group">
                                 <label for="t_booking_limit">
                                     <i class="fas fa-layer-group text-warning"></i> Maximum Concurrent Bookings <span class="text-danger">*</span>
                                 </label>
                                 <select class="form-control" name="t_booking_limit" id="t_booking_limit" required>
                                     <option value="1" <?php echo ($row->t_booking_limit == 1) ? 'selected' : ''; ?>>1 Booking at a time</option>
                                     <option value="2" <?php echo ($row->t_booking_limit == 2) ? 'selected' : ''; ?>>2 Bookings at a time</option>
                                     <option value="3" <?php echo ($row->t_booking_limit == 3) ? 'selected' : ''; ?>>3 Bookings at a time</option>
                                     <option value="4" <?php echo ($row->t_booking_limit == 4) ? 'selected' : ''; ?>>4 Bookings at a time</option>
                                     <option value="5" <?php echo ($row->t_booking_limit == 5) ? 'selected' : ''; ?>>5 Bookings at a time</option>
                                 </select>
                                 <small class="text-muted">
                                     <i class="fas fa-info-circle"></i> Current active bookings: <strong><?php echo isset($row->t_current_bookings) ? $row->t_current_bookings : 0; ?></strong>
                                 </small>
                             </div>
                             
                             <div class="card form-group" style="width: 30rem">
                                 <img src="../vendor/img/<?php echo $row->t_pic;?>" class="card-img-top">
                                 <div class="card-body">
                                     <h5 class="card-title">Technician Picture</h5>
                                     <input type="file" class="btn btn-success" id="exampleInputEmail1" name="t_pic">
                                 </div>
                             </div>
                             <hr>
                             <button type="submit" name="update_tech" class="btn btn-success">Update Technician</button>
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