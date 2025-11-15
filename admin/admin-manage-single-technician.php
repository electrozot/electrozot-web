<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
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
            $t_pic=$_FILES["t_pic"]["name"];
            move_uploaded_file($_FILES["t_pic"]["tmp_name"],"../vendor/img/".$_FILES["t_pic"]["name"]);
            $query="update tms_technician set t_name=?, t_id_no=?, t_specialization=?, t_category=?, t_experience=?, t_pic=?, t_status=? where t_id = ?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssssssi', $t_name, $t_id_no, $t_specialization, $t_category, $t_experience, $t_pic, $t_status, $t_id);
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
                                     <i class="fas fa-tools"></i> Service Category <span class="text-danger">*</span>
                                 </label>
                                 <select class="form-control" name="t_category" id="t_category" required onchange="showCategoryDetails(this)">
                                     <option value="">Select Service Category...</option>
                                     <?php
                                     // Get service categories from database
                                     $services_query = "SELECT s_name, s_description FROM tms_service WHERE s_status = 'Active' ORDER BY s_name";
                                     $services_result = $mysqli->query($services_query);
                                     if($services_result) {
                                         while($service = $services_result->fetch_object()) {
                                             $selected = ($row->t_category == $service->s_name) ? 'selected' : '';
                                             echo '<option value="'.htmlspecialchars($service->s_name).'" data-description="'.htmlspecialchars($service->s_description).'" '.$selected.'>';
                                             echo htmlspecialchars($service->s_name);
                                             echo '</option>';
                                         }
                                     }
                                     ?>
                                 </select>
                                 <small class="form-text text-muted">
                                     Select the service category this technician specializes in
                                 </small>
                                 
                                 <!-- Category Details Display -->
                                 <div id="categoryDetails" class="alert alert-info mt-2" style="display:<?php echo !empty($row->t_category) ? 'block' : 'none'; ?>;">
                                     <strong><i class="fas fa-info-circle"></i> This category includes:</strong>
                                     <p class="mb-0 mt-2" id="categoryDescription">
                                         <?php
                                         if(!empty($row->t_category)) {
                                             $desc_query = "SELECT s_description FROM tms_service WHERE s_name = ? AND s_status = 'Active'";
                                             $desc_stmt = $mysqli->prepare($desc_query);
                                             $desc_stmt->bind_param('s', $row->t_category);
                                             $desc_stmt->execute();
                                             $desc_result = $desc_stmt->get_result();
                                             if($desc_row = $desc_result->fetch_object()) {
                                                 echo htmlspecialchars($desc_row->s_description);
                                             }
                                         }
                                         ?>
                                     </p>
                                 </div>
                             </div>
                             
                             <script>
                             function showCategoryDetails(select) {
                                 const selectedOption = select.options[select.selectedIndex];
                                 const detailsDiv = document.getElementById('categoryDetails');
                                 const descriptionP = document.getElementById('categoryDescription');
                                 
                                 if(select.value && selectedOption.dataset.description) {
                                     descriptionP.textContent = selectedOption.dataset.description;
                                     detailsDiv.style.display = 'block';
                                 } else {
                                     detailsDiv.style.display = 'none';
                                 }
                             }
                             </script>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Technician Status</label>
                                 <select class="form-control" name="t_status" id="exampleFormControlSelect1">
                                     <option>Booked</option>
                                     <option>Available</option>
                                 </select>
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