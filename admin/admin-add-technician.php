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
            
            // Validate phone number is exactly 10 digits
            if(!empty($t_phone) && !preg_match('/^[0-9]{10}$/', $t_phone)) {
                $err = "Phone number must be exactly 10 digits";
            } else {
            
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
            } // Close phone validation
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
                                             <input type="tel" class="form-control" name="t_phone" placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}" maxlength="10" required title="Enter exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                         </div>
                                         <small class="text-success"><i class="fas fa-info-circle"></i> This number will be used for technician login (exactly 10 digits)</small>
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
                                         <label>Service Type <span class="text-danger">*</span></label>
                                         <select class="form-control" name="t_category" required onchange="showCategoryDetails(this)">
                                             <option value="">Select Service Type...</option>
                                             <option value="Wiring & Fixtures" data-description="Home wiring, switches, lights, fixtures">Wiring & Fixtures</option>
                                             <option value="Safety & Power" data-description="Circuit breakers, inverters, stabilizers, grounding">Safety & Power</option>
                                             <option value="Major Appliances" data-description="AC, refrigerator, washing machine, microwave, geyser">Major Appliances</option>
                                             <option value="Small Gadgets" data-description="TV, fans, heaters, coolers, music systems">Small Gadgets</option>
                                             <option value="Appliance Setup" data-description="Installation of appliances and devices">Appliance Setup</option>
                                             <option value="Tech & Security" data-description="CCTV, WiFi, smart devices">Tech & Security</option>
                                             <option value="Routine Care" data-description="AC servicing, filter cleaning, maintenance">Routine Care</option>
                                             <option value="Fixtures & Taps" data-description="Plumbing fixtures, taps, pipes">Fixtures & Taps</option>
                                         </select>
                                         <div id="categoryDetails" class="alert alert-info mt-2 py-2" style="display:none; font-size: 0.875rem;">
                                             <strong>Includes:</strong> <span id="categoryDescription"></span>
                                         </div>
                                         <small class="text-muted">Select the main service type this technician specializes in</small>
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
                                 // Use the 8 service types directly
                                 $service_types_list = [
                                     ['id' => 1, 'name' => 'Wiring & Fixtures', 'desc' => 'Electrical wiring, switches, lights'],
                                     ['id' => 2, 'name' => 'Safety & Power', 'desc' => 'Circuit breakers, inverters, stabilizers'],
                                     ['id' => 3, 'name' => 'Major Appliances', 'desc' => 'AC, refrigerator, washing machine'],
                                     ['id' => 4, 'name' => 'Small Gadgets', 'desc' => 'TV, fans, heaters, coolers'],
                                     ['id' => 5, 'name' => 'Appliance Setup', 'desc' => 'Installation services'],
                                     ['id' => 6, 'name' => 'Tech & Security', 'desc' => 'CCTV, WiFi, smart devices'],
                                     ['id' => 7, 'name' => 'Routine Care', 'desc' => 'Servicing and maintenance'],
                                     ['id' => 8, 'name' => 'Fixtures & Taps', 'desc' => 'Plumbing fixtures and repairs']
                                 ];
                                 
                                 foreach($service_types_list as $service_type) {
                                 ?>
                                 <div class="col-md-6 mb-3">
                                     <div class="card border">
                                         <div class="card-body p-3">
                                             <div class="custom-control custom-checkbox">
                                                 <input type="checkbox" class="custom-control-input" 
                                                        id="service_<?php echo $service_type['id']; ?>" 
                                                        name="additional_services[]" 
                                                        value="<?php echo $service_type['name']; ?>">
                                                 <label class="custom-control-label font-weight-bold" for="service_<?php echo $service_type['id']; ?>">
                                                     <?php echo $service_type['name']; ?>
                                                 </label>
                                             </div>
                                             <small class="text-muted d-block mt-1"><?php echo $service_type['desc']; ?></small>
                                         </div>
                                     </div>
                                 </div>
                                 <?php } ?>
                             </div>
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