<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  // Ensure is_popular column exists
  $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS is_popular TINYINT(1) DEFAULT 0");
  
  //Update Service
  if(isset($_POST['update_service']))
    {
            $s_id = $_GET['s_id'];
            $s_name=$_POST['s_name'];
            $s_description = $_POST['s_description'];
            $s_category=$_POST['s_category'];
            $s_price=$_POST['s_price'];
            $s_duration=$_POST['s_duration'];
            $s_status=$_POST['s_status'];
            $is_popular = isset($_POST['is_popular']) ? 1 : 0;
            $query="update tms_service set s_name=?, s_description=?, s_category=?, s_price=?, s_duration=?, s_status=?, is_popular=? where s_id = ?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssdssii', $s_name, $s_description, $s_category, $s_price, $s_duration, $s_status, $is_popular, $s_id);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Service Updated";
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
                     <li class="breadcrumb-item active">Update Service</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Update Service
                     </div>
                     <div class="card-body">
                         <!--Update Service Form-->
                         <?php
            $aid=$_GET['s_id'];
            $ret="select * from tms_service where s_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute();
            $res=$stmt->get_result();
            while($row=$res->fetch_object())
        {
        ?>
                         <form method="POST">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Name</label>
                                 <input type="text" value="<?php echo $row->s_name;?>" required class="form-control" id="exampleInputEmail1" name="s_name">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Description</label>
                                 <textarea class="form-control" required name="s_description" rows="4"><?php echo $row->s_description;?></textarea>
                             </div>
                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Category</label>
                                 <select class="form-control" name="s_category" id="exampleFormControlSelect1" required>
                                     <option value="Electrical" <?php echo ($row->s_category == 'Electrical') ? 'selected' : ''; ?>>Electrical</option>
                                     <option value="Plumbing" <?php echo ($row->s_category == 'Plumbing') ? 'selected' : ''; ?>>Plumbing</option>
                                     <option value="HVAC" <?php echo ($row->s_category == 'HVAC') ? 'selected' : ''; ?>>HVAC</option>
                                     <option value="Appliance" <?php echo ($row->s_category == 'Appliance') ? 'selected' : ''; ?>>Appliance</option>
                                     <option value="General" <?php echo ($row->s_category == 'General') ? 'selected' : ''; ?>>General</option>
                                 </select>
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Price</label>
                                 <input type="number" step="0.01" value="<?php echo $row->s_price;?>" required class="form-control" id="exampleInputEmail1" name="s_price">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Service Duration</label>
                                 <input type="text" value="<?php echo $row->s_duration;?>" required class="form-control" id="exampleInputEmail1" name="s_duration">
                             </div>
                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Status</label>
                                 <select class="form-control" name="s_status" id="exampleFormControlSelect1" required>
                                     <option value="Active" <?php echo ($row->s_status == 'Active') ? 'selected' : ''; ?>>Active</option>
                                     <option value="Inactive" <?php echo ($row->s_status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                 </select>
                             </div>
                             <div class="form-group">
                                 <div class="custom-control custom-checkbox">
                                     <input type="checkbox" class="custom-control-input" id="popularCheckbox" name="is_popular" value="1" <?php echo (isset($row->is_popular) && $row->is_popular == 1) ? 'checked' : ''; ?>>
                                     <label class="custom-control-label" for="popularCheckbox">
                                         <i class="fas fa-star text-warning"></i> Mark as Popular Service (Show on Homepage)
                                     </label>
                                     <small class="form-text text-muted">Check this to display this service in the "Our Popular Services" section on the homepage.</small>
                                 </div>
                             </div>
                             <hr>
                             <button type="submit" name="update_service" class="btn btn-success">Update Service</button>
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
         <script src="vendor/js/swal.js"></script>

 </body>

 </html>

