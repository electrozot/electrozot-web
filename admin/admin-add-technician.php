<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  // Ensure technician columns exist
  try {
    $colChk = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tms_technician' AND COLUMN_NAME = 't_pwd'");
    if($colChk){
      $hasPwdCol = $colChk->fetch_object();
      if(!$hasPwdCol || intval($hasPwdCol->c) === 0){
        $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_pwd VARCHAR(200) NOT NULL DEFAULT ''");
      }
    }
    // Add service pincode column
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_service_pincode VARCHAR(20) DEFAULT ''");
    // Add EZ ID column
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_ez_id VARCHAR(20) DEFAULT NULL");
  } catch(Exception $e) { /* ignore */ }
  
  // Create technician_services table if it doesn't exist
  $table_check = $mysqli->query("SHOW TABLES LIKE 'tms_technician_services'");
  if($table_check->num_rows == 0) {
      $create_table = "CREATE TABLE IF NOT EXISTS tms_technician_services (
          ts_id INT AUTO_INCREMENT PRIMARY KEY,
          t_id INT NOT NULL,
          sc_id INT NOT NULL,
          service_type ENUM('Installation', 'Repair', 'Servicing', 'Maintenance', 'Other') NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY unique_tech_service (t_id, sc_id, service_type)
      )";
      $mysqli->query($create_table);
  }
  
  //Add Technician
  // Add phone column if it doesn't exist
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(15) DEFAULT NULL");
  
  if(isset($_POST['add_tech']))
    {
            $t_name=$_POST['t_name'];
            $t_phone = isset($_POST['t_phone']) ? $_POST['t_phone'] : '';
            $t_ez_id = isset($_POST['t_ez_id']) ? $_POST['t_ez_id'] : '';
            $t_id_no = $_POST['t_id_no'];
            $t_category=$_POST['t_category'];
            $t_experience=$_POST['t_experience'];
            $t_status=$_POST['t_status'];
            $t_pwd = isset($_POST['t_pwd']) ? $_POST['t_pwd'] : '';
            $t_specialization=$_POST['t_specialization'];
            $t_service_pincode = isset($_POST['t_service_pincode']) ? $_POST['t_service_pincode'] : '';
            $t_pic=$_FILES["t_pic"]["name"];
	        move_uploaded_file($_FILES["t_pic"]["tmp_name"],"../vendor/img/".$_FILES["t_pic"]["name"]);
            $query="insert into tms_technician (t_name, t_phone, t_ez_id, t_experience, t_id_no, t_specialization, t_category, t_pic, t_status, t_pwd, t_service_pincode) values(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssssssssss', $t_name, $t_phone, $t_ez_id, $t_experience, $t_id_no, $t_specialization, $t_category, $t_pic, $t_status, $t_pwd, $t_service_pincode);
            $stmt->execute();
            
            if($stmt)
            {
                $tech_id = $mysqli->insert_id;
                
                // Insert technician service categories and types (only if table exists)
                $ts_table_check = $mysqli->query("SHOW TABLES LIKE 'tms_technician_services'");
                if($ts_table_check && $ts_table_check->num_rows > 0) {
                    if(isset($_POST['service_categories']) && is_array($_POST['service_categories'])) {
                        foreach($_POST['service_categories'] as $sc_id) {
                            if(isset($_POST['service_types_'.$sc_id]) && is_array($_POST['service_types_'.$sc_id])) {
                                foreach($_POST['service_types_'.$sc_id] as $service_type) {
                                    $insert_service = "INSERT INTO tms_technician_services (t_id, sc_id, service_type) VALUES (?, ?, ?)";
                                    $stmt_service = $mysqli->prepare($insert_service);
                                    $stmt_service->bind_param('iis', $tech_id, $sc_id, $service_type);
                                    $stmt_service->execute();
                                }
                            }
                        }
                    }
                }
                
                $succ = "Technician Added Successfully";
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
                                 <label for="exampleInputEmail1">Technician Name <span class="text-danger">*</span></label>
                                 <input type="text" required class="form-control" id="exampleInputEmail1" name="t_name">
                             </div>
                             <div class="form-group">
                                 <label for="t_phone">
                                     <i class="fas fa-mobile-alt"></i> Mobile Number <span class="text-danger">*</span>
                                     <small class="text-muted">(Used for login)</small>
                                 </label>
                                 <input type="tel" class="form-control" id="t_phone" name="t_phone" placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}" maxlength="10" required>
                                 <small class="form-text text-muted">This mobile number will be used for technician login</small>
                             </div>
                             <div class="form-group">
                                 <label for="t_ez_id">
                                     <i class="fas fa-id-badge"></i> EZ ID <span class="text-danger">*</span>
                                 </label>
                                 <input type="text" class="form-control" id="t_ez_id" name="t_ez_id" placeholder="e.g., EZ0001" required>
                                 <small class="form-text text-muted">Unique Electrozot ID for the technician (e.g., EZ0001, EZ0023)</small>
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician ID Number (Optional)</label>
                                 <input type="text" class="form-control" id="exampleInputEmail1" name="t_id_no" placeholder="e.g., TECH001">
                                 <small class="form-text text-muted">Optional ID for internal reference</small>
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
                                 <label for="t_service_pincode">
                                     <i class="fas fa-map-marker-alt"></i> Service Pincode
                                     <small class="text-muted">(Area where technician provides service)</small>
                                 </label>
                                 <input type="text" class="form-control" id="t_service_pincode" name="t_service_pincode" 
                                        placeholder="e.g., 560001" pattern="[0-9]{6}" maxlength="6" required>
                                 <small class="form-text text-muted">Enter 6-digit pincode for service area</small>
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Category (Legacy)</label>
                                 <select class="form-control" name="t_category" id="exampleFormControlSelect1">
                                     <option>Electrical</option>
                                     <option>Plumbing</option>
                                     <option>HVAC</option>
                                     <option>Appliance</option>
                                     <option>General</option>
                                 </select>
                                 <small class="form-text text-muted">Keep for backward compatibility</small>
                             </div>
                             
                             <?php
                             // Check if service categories table exists
                             $table_check = $mysqli->query("SHOW TABLES LIKE 'tms_service_categories'");
                             if($table_check && $table_check->num_rows > 0) {
                             ?>
                             <div class="card mb-3" style="border: 2px solid #667eea;">
                                 <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                     <h5 class="mb-0"><i class="fas fa-tools"></i> Service Specializations</h5>
                                     <small>Select categories and service types this technician can handle</small>
                                 </div>
                                 <div class="card-body">
                                     <div class="row">
                                         <?php
                                         $cat_query = "SELECT * FROM tms_service_categories WHERE sc_status='Active' ORDER BY sc_name ASC";
                                         $cat_stmt = $mysqli->prepare($cat_query);
                                         $cat_stmt->execute();
                                         $cat_res = $cat_stmt->get_result();
                                         $service_types = ['Installation', 'Repair', 'Servicing', 'Maintenance', 'Other'];
                                         
                                         if($cat_res->num_rows > 0) {
                                             while($cat_row = $cat_res->fetch_object()) {
                                         ?>
                                         <div class="col-md-6 mb-3">
                                             <div class="card" style="border: 1px solid #e2e8f0;">
                                                 <div class="card-body p-3">
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input category-checkbox" 
                                                                id="cat_<?php echo $cat_row->sc_id; ?>" 
                                                                name="service_categories[]" 
                                                                value="<?php echo $cat_row->sc_id; ?>"
                                                                onchange="toggleServiceTypes(<?php echo $cat_row->sc_id; ?>)">
                                                         <label class="custom-control-label font-weight-bold" for="cat_<?php echo $cat_row->sc_id; ?>">
                                                             <i class="fas fa-wrench text-primary"></i> <?php echo $cat_row->sc_name; ?>
                                                         </label>
                                                     </div>
                                                     <div id="types_<?php echo $cat_row->sc_id; ?>" style="display: none; margin-left: 25px; margin-top: 10px;">
                                                         <small class="text-muted d-block mb-2">Select service types:</small>
                                                         <?php foreach($service_types as $type) { ?>
                                                         <div class="custom-control custom-checkbox">
                                                             <input type="checkbox" class="custom-control-input" 
                                                                    id="type_<?php echo $cat_row->sc_id; ?>_<?php echo $type; ?>" 
                                                                    name="service_types_<?php echo $cat_row->sc_id; ?>[]" 
                                                                    value="<?php echo $type; ?>">
                                                             <label class="custom-control-label" for="type_<?php echo $cat_row->sc_id; ?>_<?php echo $type; ?>">
                                                                 <?php echo $type; ?>
                                                             </label>
                                                         </div>
                                                         <?php } ?>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                         <?php 
                                             }
                                         } else {
                                             echo '<div class="col-12"><div class="alert alert-warning">No service categories found. Please add categories first.</div></div>';
                                         }
                                         ?>
                                     </div>
                                     <div class="alert alert-info mt-3">
                                         <i class="fas fa-info-circle"></i> <strong>Tip:</strong> Select the categories and types this technician specializes in. This helps match them with appropriate service requests.
                                     </div>
                                 </div>
                             </div>
                             
                             <script>
                             function toggleServiceTypes(catId) {
                                 var checkbox = document.getElementById('cat_' + catId);
                                 var typesDiv = document.getElementById('types_' + catId);
                                 var typeCheckboxes = typesDiv.querySelectorAll('input[type="checkbox"]');
                                 
                                 if(checkbox.checked) {
                                     typesDiv.style.display = 'block';
                                 } else {
                                     typesDiv.style.display = 'none';
                                     // Uncheck all type checkboxes
                                     typeCheckboxes.forEach(function(cb) {
                                         cb.checked = false;
                                     });
                                 }
                             }
                             </script>
                             <?php
                             } else {
                             ?>
                             <div class="alert alert-warning">
                                 <h5><i class="fas fa-exclamation-triangle"></i> Service Categories System Not Set Up</h5>
                                 <p>The service categories system needs to be set up first.</p>
                                 <a href="setup-service-categories.php" class="btn btn-primary">
                                     <i class="fas fa-cog"></i> Run Setup Now
                                 </a>
                             </div>
                             <?php } ?>

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