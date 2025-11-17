<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  // Ensure is_popular column exists
  $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS is_popular TINYINT(1) DEFAULT 0");
  
  // Ensure columns exist
  $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_subcategory VARCHAR(200) NULL");
  $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_gadget_name VARCHAR(200) NULL");
  
  //Add Service
  if(isset($_POST['add_service']))
    {
            $s_name=$_POST['s_name'];
            $s_description = $_POST['s_description'];
            $s_category=$_POST['s_category'];
            $s_subcategory=$_POST['s_subcategory'];
            $s_gadget_name=$_POST['s_gadget_name'];
            $s_price=$_POST['s_price'];
            $s_duration=$_POST['s_duration'];
            $s_status=$_POST['s_status'];
            $is_popular = isset($_POST['is_popular']) ? 1 : 0;
            $query="insert into tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status, is_popular) values(?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssssdssi', $s_name, $s_description, $s_category, $s_subcategory, $s_gadget_name, $s_price, $s_duration, $s_status, $is_popular);
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
                                 <label for="serviceCategory">Service Category <span class="text-danger">*</span></label>
                                 <select class="form-control" name="s_category" id="serviceCategory" required>
                                     <option value="">-- Select Category --</option>
                                     <option value="Basic Electrical Work">Basic Electrical Work</option>
                                     <option value="Electronic Repair">Electronic Repair</option>
                                     <option value="Installation & Setup">Installation & Setup</option>
                                     <option value="Servicing & Maintenance">Servicing & Maintenance</option>
                                     <option value="Plumbing Work">Plumbing Work</option>
                                 </select>
                             </div>
                             <div class="form-group">
                                 <label for="serviceSubcategory">Service Subcategory <span class="text-danger">*</span></label>
                                 <select class="form-control" name="s_subcategory" id="serviceSubcategory" required disabled>
                                     <option value="">-- Select Category First --</option>
                                 </select>
                                 <small class="form-text text-muted">Select a category first to see subcategories</small>
                             </div>
                             <div class="form-group">
                                 <label for="serviceGadget">Gadget/Device Name</label>
                                 <input type="text" class="form-control" id="serviceGadget" name="s_gadget_name" placeholder="e.g., LED TV, Washing Machine, AC (Optional)">
                                 <small class="form-text text-muted">Specify the device/gadget this service is for (optional)</small>
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
                             <div class="form-group">
                                 <div class="custom-control custom-checkbox">
                                     <input type="checkbox" class="custom-control-input" id="popularCheckbox" name="is_popular" value="1">
                                     <label class="custom-control-label" for="popularCheckbox">
                                         <i class="fas fa-star text-warning"></i> Mark as Popular Service (Show on Homepage)
                                     </label>
                                     <small class="form-text text-muted">Check this to display this service in the "Our Popular Services" section on the homepage.</small>
                                 </div>
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
         
         <!-- Cascading Dropdown Script -->
         <script>
         $(document).ready(function() {
             // Category to Subcategory mapping
             var categorySubcategories = {
                 'Basic Electrical Work': ['Wiring & Fixtures', 'Safety & Power'],
                 'Electronic Repair': ['Major Appliances', 'Small Gadgets'],
                 'Installation & Setup': ['Appliance Setup', 'Tech & Security'],
                 'Servicing & Maintenance': ['Routine Care'],
                 'Plumbing Work': ['Fixtures & Taps']
             };
             
             $('#serviceCategory').on('change', function() {
                 var category = $(this).val();
                 var subcategorySelect = $('#serviceSubcategory');
                 
                 // Clear and disable subcategory
                 subcategorySelect.html('<option value="">-- Select Subcategory --</option>');
                 
                 if(category && categorySubcategories[category]) {
                     // Enable and populate subcategories
                     subcategorySelect.prop('disabled', false);
                     
                     $.each(categorySubcategories[category], function(index, subcategory) {
                         subcategorySelect.append('<option value="' + subcategory + '">' + subcategory + '</option>');
                     });
                 } else {
                     subcategorySelect.prop('disabled', true);
                     subcategorySelect.html('<option value="">-- Select Category First --</option>');
                 }
             });
         });
         </script>

 </body>

 </html>

