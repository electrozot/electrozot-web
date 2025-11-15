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
            $t_id_no = $t_ez_id; // Use EZ ID as the ID number
            $t_category=$_POST['t_category'];
            $t_experience=$_POST['t_experience'];
            $t_status=$_POST['t_status'];
            $t_pwd = isset($_POST['t_pwd']) ? $_POST['t_pwd'] : '';
            $t_specialization=$_POST['t_specialization'];
            $t_service_pincode = isset($_POST['t_service_pincode']) ? $_POST['t_service_pincode'] : '';
            
            // Check for duplicate EZ ID
            $check_ez = $mysqli->prepare("SELECT t_id FROM tms_technician WHERE t_ez_id = ?");
            $check_ez->bind_param('s', $t_ez_id);
            $check_ez->execute();
            $check_ez->store_result();
            
            if($check_ez->num_rows > 0) {
                $err = "EZ ID already exists! Please use a unique EZ ID.";
                $check_ez->close();
            } else {
                $check_ez->close();
                
                // Check for duplicate Mobile Number
                $check_phone = $mysqli->prepare("SELECT t_id FROM tms_technician WHERE t_phone = ?");
                $check_phone->bind_param('s', $t_phone);
                $check_phone->execute();
                $check_phone->store_result();
                
                if($check_phone->num_rows > 0) {
                    $err = "Mobile Number already exists! Please use a unique mobile number.";
                    $check_phone->close();
                } else {
                    $check_phone->close();
                    
                    // Proceed with insertion
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
                 <div class="card shadow-sm">
                     <div class="card-header bg-primary text-white">
                         <h5 class="mb-0"><i class="fas fa-user-plus"></i> Add New Technician</h5>
                     </div>
                     <div class="card-body p-4">
                         <!--Add Technician Form-->
                         <form method="POST" enctype="multipart/form-data">
                             
                             <!-- Basic Information Section -->
                             <div class="row">
                                 <div class="col-12">
                                     <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user"></i> Basic Information</h6>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Technician Name <span class="text-danger">*</span></label>
                                         <input type="text" required class="form-control" name="t_name" placeholder="Enter full name">
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label><i class="fas fa-mobile-alt text-success"></i> Mobile Number <span class="text-danger">*</span></label>
                                         <div class="input-group">
                                             <div class="input-group-prepend">
                                                 <span class="input-group-text">+91</span>
                                             </div>
                                             <input type="tel" class="form-control" name="t_phone" placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}" maxlength="10" required>
                                         </div>
                                         <small class="text-success"><i class="fas fa-info-circle"></i> This number will be used for technician login</small>
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label><i class="fas fa-id-badge text-primary"></i> EZ ID <span class="text-danger">*</span></label>
                                         <input type="text" class="form-control" name="t_ez_id" placeholder="EZ0001" required style="text-transform: uppercase;">
                                         <small class="text-muted">Unique company identification number</small>
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-4">
                                     <div class="form-group">
                                         <label>Password <span class="text-danger">*</span></label>
                                         <input type="password" class="form-control" name="t_pwd" placeholder="Login password" required>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- Professional Details Section -->
                             <div class="row mt-3">
                                 <div class="col-12">
                                     <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-briefcase"></i> Professional Details</h6>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Service Category <span class="text-danger">*</span></label>
                                         <select class="form-control" name="t_category" required onchange="showCategoryDetails(this)">
                                             <option value="">Select Category...</option>
                                             <?php
                                             $services_query = "SELECT s_name, s_description FROM tms_service WHERE s_status = 'Active' ORDER BY s_name";
                                             $services_result = $mysqli->query($services_query);
                                             if($services_result) {
                                                 while($service = $services_result->fetch_object()) {
                                                     echo '<option value="'.htmlspecialchars($service->s_name).'" data-description="'.htmlspecialchars($service->s_description).'">';
                                                     echo htmlspecialchars($service->s_name);
                                                     echo '</option>';
                                                 }
                                             }
                                             ?>
                                         </select>
                                         <div id="categoryDetails" class="alert alert-info mt-2 py-2" style="display:none; font-size: 0.875rem;">
                                             <strong>Includes:</strong> <span id="categoryDescription"></span>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Specialization</label>
                                         <input type="text" class="form-control" name="t_specialization" placeholder="e.g., Electrical Repairs">
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-4">
                                     <div class="form-group">
                                         <label>Experience (Years)</label>
                                         <input type="number" class="form-control" name="t_experience" placeholder="e.g., 5" min="0">
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-4">
                                     <div class="form-group">
                                         <label>Service Pincode <span class="text-danger">*</span></label>
                                         <input type="text" class="form-control" name="t_service_pincode" placeholder="6-digit pincode" pattern="[0-9]{6}" maxlength="6" required>
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-4">
                                     <div class="form-group">
                                         <label>Status <span class="text-danger">*</span></label>
                                         <select class="form-control" name="t_status" required>
                                             <option value="Available">Available</option>
                                             <option value="Booked">Booked</option>
                                         </select>
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-12">
                                     <div class="form-group">
                                         <label>Profile Picture</label>
                                         <input type="file" class="form-control-file" name="t_pic" accept="image/*">
                                         <small class="text-muted">Upload technician photo (optional)</small>
                                     </div>
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
                             
                             <?php
                             // Check if service categories table exists
                             $table_check = $mysqli->query("SHOW TABLES LIKE 'tms_service_categories'");
                             if($table_check && $table_check->num_rows > 0) {
                             ?>
                             <!-- Additional Services Section (Optional) -->
                             <div class="row mt-4">
                                 <div class="col-12">
                                     <h6 class="border-bottom pb-2 mb-3">
                                         <i class="fas fa-list-check"></i> Additional Services 
                                         <small class="text-muted">(Optional - Skip if not needed)</small>
                                     </h6>
                                     <div class="alert alert-light border">
                                         <p class="mb-2"><strong>What is this?</strong></p>
                                         <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                             If this technician can handle multiple service types (like both Installation AND Repair), 
                                             select them below. Otherwise, you can skip this section.
                                         </p>
                                     </div>
                                 </div>
                             </div>
                             
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
                                     <div class="card border">
                                         <div class="card-body p-3">
                                             <div class="custom-control custom-switch mb-2">
                                                 <input type="checkbox" class="custom-control-input" 
                                                        id="cat_<?php echo $cat_row->sc_id; ?>" 
                                                        name="service_categories[]" 
                                                        value="<?php echo $cat_row->sc_id; ?>"
                                                        onchange="toggleServiceTypes(<?php echo $cat_row->sc_id; ?>)">
                                                 <label class="custom-control-label font-weight-bold" for="cat_<?php echo $cat_row->sc_id; ?>">
                                                     <?php echo $cat_row->sc_name; ?>
                                                 </label>
                                             </div>
                                             
                                             <div id="types_<?php echo $cat_row->sc_id; ?>" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
                                                 <small class="d-block mb-2 font-weight-bold">Can do:</small>
                                                 <div class="row">
                                                     <?php foreach($service_types as $type) { ?>
                                                     <div class="col-6">
                                                         <div class="custom-control custom-checkbox">
                                                             <input type="checkbox" class="custom-control-input" 
                                                                    id="type_<?php echo $cat_row->sc_id; ?>_<?php echo $type; ?>" 
                                                                    name="service_types_<?php echo $cat_row->sc_id; ?>[]" 
                                                                    value="<?php echo $type; ?>">
                                                             <label class="custom-control-label" for="type_<?php echo $cat_row->sc_id; ?>_<?php echo $type; ?>">
                                                                 <?php echo $type; ?>
                                                             </label>
                                                         </div>
                                                     </div>
                                                     <?php } ?>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <?php 
                                     }
                                 } else {
                                     echo '<div class="col-12"><div class="alert alert-info">No additional service categories available.</div></div>';
                                 }
                                 ?>
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
                                     typeCheckboxes.forEach(function(cb) {
                                         cb.checked = false;
                                     });
                                 }
                             }
                             </script>
                             <?php } ?>

                             
                             <!-- Submit Button -->
                             <div class="row mt-4">
                                 <div class="col-12">
                                     <hr>
                                     <button type="submit" name="add_tech" class="btn btn-success btn-lg px-5">
                                         <i class="fas fa-plus-circle"></i> Add Technician
                                     </button>
                                     <a href="admin-manage-technician.php" class="btn btn-secondary btn-lg ml-2">
                                         <i class="fas fa-times"></i> Cancel
                                     </a>
                                 </div>
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