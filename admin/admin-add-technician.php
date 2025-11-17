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
  
  // Create technician_skills table if it doesn't exist
  $table_check = $mysqli->query("SHOW TABLES LIKE 'tms_technician_skills'");
  if($table_check->num_rows == 0) {
      $create_table = "CREATE TABLE IF NOT EXISTS tms_technician_skills (
          ts_id INT AUTO_INCREMENT PRIMARY KEY,
          t_id INT NOT NULL,
          skill_name VARCHAR(255) NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY unique_tech_skill (t_id, skill_name)
      )";
      $mysqli->query($create_table);
  }
  
  //Add Technician
  // Add phone column if it doesn't exist
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(15) DEFAULT NULL");
  
  // Add booking limit columns if they don't exist
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT NOT NULL DEFAULT 1");
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT NOT NULL DEFAULT 0");
  
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
            $t_booking_limit = isset($_POST['t_booking_limit']) ? intval($_POST['t_booking_limit']) : 1;
            
            // Validate booking limit (1-5)
            if($t_booking_limit < 1 || $t_booking_limit > 5) {
                $t_booking_limit = 1;
            }
            
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
                    $query="insert into tms_technician (t_name, t_phone, t_ez_id, t_experience, t_id_no, t_specialization, t_category, t_pic, t_status, t_pwd, t_service_pincode, t_booking_limit, t_current_bookings) values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $mysqli->prepare($query);
                    $rc=$stmt->bind_param('sssssssssssii', $t_name, $t_phone, $t_ez_id, $t_experience, $t_id_no, $t_specialization, $t_category, $t_pic, $t_status, $t_pwd, $t_service_pincode, $t_booking_limit, $t_current_bookings = 0);
                    $stmt->execute();
            
                    if($stmt)
                    {
                        $tech_id = $mysqli->insert_id;
                        
                        // Insert technician skills
                        if(isset($_POST['tech_skills']) && is_array($_POST['tech_skills'])) {
                            foreach($_POST['tech_skills'] as $skill) {
                                $insert_skill = "INSERT INTO tms_technician_skills (t_id, skill_name) VALUES (?, ?)";
                                $stmt_skill = $mysqli->prepare($insert_skill);
                                $stmt_skill->bind_param('is', $tech_id, $skill);
                                $stmt_skill->execute();
                            }
                        }
                        
                        $succ = "Technician Added Successfully with " . (isset($_POST['tech_skills']) ? count($_POST['tech_skills']) : 0) . " skills";
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
                                         <div class="input-group">
                                             <input type="text" class="form-control" id="t_ez_id" name="t_ez_id" placeholder="EZ0001" required style="text-transform: uppercase;" readonly>
                                             <div class="input-group-append">
                                                 <button class="btn btn-success" type="button" onclick="generateNextEZID()">
                                                     <i class="fas fa-sync-alt"></i> Auto Generate
                                                 </button>
                                             </div>
                                         </div>
                                         <small class="text-success"><i class="fas fa-info-circle"></i> Click "Auto Generate" to get the next available EZ ID</small>
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
                                         <label>Primary Service Category <span class="text-danger">*</span></label>
                                         <select class="form-control" name="t_category" required onchange="showCategoryDetails(this)">
                                             <option value="">Select Primary Category...</option>
                                             <option value="BASIC ELECTRICAL WORK" data-description="Wiring, fixtures, circuit breakers, inverters, electrical repairs">1. BASIC ELECTRICAL WORK</option>
                                             <option value="ELECTRONIC REPAIR" data-description="AC, refrigerator, washing machine, TV, fans, appliance repairs">2. ELECTRONIC REPAIR</option>
                                             <option value="INSTALLATION & SETUP" data-description="Appliance installation, CCTV, WiFi, smart home setup">3. INSTALLATION & SETUP</option>
                                             <option value="SERVICING & MAINTENANCE" data-description="AC servicing, filter cleaning, routine maintenance">4. SERVICING & MAINTENANCE</option>
                                             <option value="PLUMBING WORK" data-description="Taps, faucets, washbasin, toilet, plumbing fixtures">5. PLUMBING WORK</option>
                                         </select>
                                         <div id="categoryDetails" class="alert alert-info mt-2 py-2" style="display:none; font-size: 0.875rem;">
                                             <strong>Includes:</strong> <span id="categoryDescription"></span>
                                         </div>
                                         <small class="text-muted">Select the main service category this technician specializes in</small>
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
                             </div>
                             
                             <!-- Booking Capacity Section -->
                             <div class="row mt-3">
                                 <div class="col-12">
                                     <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-tasks"></i> Booking Capacity</h6>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label><i class="fas fa-layer-group text-warning"></i> Maximum Concurrent Bookings <span class="text-danger">*</span></label>
                                         <select class="form-control" name="t_booking_limit" id="t_booking_limit" required onchange="updateBookingLimitInfo()">
                                             <option value="1" selected>1 Booking at a time</option>
                                             <option value="2">2 Bookings at a time</option>
                                             <option value="3">3 Bookings at a time</option>
                                             <option value="4">4 Bookings at a time</option>
                                             <option value="5">5 Bookings at a time</option>
                                         </select>
                                         <small class="text-muted">
                                             <i class="fas fa-info-circle"></i> How many bookings can this technician handle simultaneously?
                                         </small>
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-6">
                                     <div class="alert alert-info mt-4" id="bookingLimitInfo">
                                         <strong><i class="fas fa-lightbulb"></i> Booking Limit:</strong>
                                         <p class="mb-0" id="limitDescription">
                                             Technician can take <strong>1 booking</strong> at a time. After completing or rejecting it, they become available for the next booking.
                                         </p>
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
                             function updateBookingLimitInfo() {
                                 const limit = document.getElementById('t_booking_limit').value;
                                 const description = document.getElementById('limitDescription');
                                 
                                 if(limit == 1) {
                                     description.innerHTML = 'Technician can take <strong>1 booking</strong> at a time. After completing or rejecting it, they become available for the next booking.';
                                 } else {
                                     description.innerHTML = `Technician can handle <strong>${limit} bookings</strong> simultaneously. They must complete or reject at least <strong>${limit - 1}</strong> booking(s) before accepting new ones when at capacity.`;
                                 }
                             }
                             </script>
                             
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
                             
                             <!-- Detailed Service Skills Section -->
                             <div class="row mt-4">
                                 <div class="col-12">
                                     <h6 class="border-bottom pb-2 mb-3">
                                         <i class="fas fa-tools"></i> Detailed Service Skills 
                                         <small class="text-muted">(Select all services this technician can perform)</small>
                                     </h6>
                                     <div class="alert alert-light border">
                                         <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                             <i class="fas fa-info-circle text-primary"></i> 
                                             Check all the specific services this technician is skilled in. This helps in accurate job assignment.
                                         </p>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- 1. BASIC ELECTRICAL WORK -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <div class="card border-primary">
                                         <div class="card-header bg-primary text-white">
                                             <h6 class="mb-0"><i class="fas fa-bolt"></i> 1. BASIC ELECTRICAL WORK</h6>
                                         </div>
                                         <div class="card-body">
                                             <div class="row">
                                                 <div class="col-md-6">
                                                     <h6 class="text-primary"><i class="fas fa-lightbulb"></i> Wiring & Fixtures</h6>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_1" name="tech_skills[]" value="Home Wiring (New installation and repair)">
                                                         <label class="custom-control-label" for="skill_1">Home Wiring (New installation and repair)</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_2" name="tech_skills[]" value="Switch/Socket Installation and Replacement">
                                                         <label class="custom-control-label" for="skill_2">Switch/Socket Installation and Replacement</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_3" name="tech_skills[]" value="Light Fixture Installation (Tube lights, LED panels, chandeliers)">
                                                         <label class="custom-control-label" for="skill_3">Light Fixture Installation (Tube lights, LED panels, chandeliers)</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_4" name="tech_skills[]" value="Light Decoration/Festive Lighting Setup">
                                                         <label class="custom-control-label" for="skill_4">Light Decoration/Festive Lighting Setup</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <h6 class="text-primary"><i class="fas fa-shield-alt"></i> Safety & Power</h6>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_5" name="tech_skills[]" value="Circuit Breaker and Fuse Box troubleshooting and repair">
                                                         <label class="custom-control-label" for="skill_5">Circuit Breaker and Fuse Box troubleshooting</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_6" name="tech_skills[]" value="Inverter, UPS, and Voltage Stabilizer installation/wiring">
                                                         <label class="custom-control-label" for="skill_6">Inverter, UPS, and Voltage Stabilizer installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_7" name="tech_skills[]" value="Grounding and Earthing system installation">
                                                         <label class="custom-control-label" for="skill_7">Grounding and Earthing system installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_8" name="tech_skills[]" value="New Electrical Outlet/Point installation">
                                                         <label class="custom-control-label" for="skill_8">New Electrical Outlet/Point installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_9" name="tech_skills[]" value="Ceiling Fan Regulator repair/replacement">
                                                         <label class="custom-control-label" for="skill_9">Ceiling Fan Regulator repair/replacement</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_10" name="tech_skills[]" value="Electrical fault finding and short-circuit repair">
                                                         <label class="custom-control-label" for="skill_10">Electrical fault finding and short-circuit repair</label>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- 2. ELECTRONIC REPAIR -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <div class="card border-success">
                                         <div class="card-header bg-success text-white">
                                             <h6 class="mb-0"><i class="fas fa-wrench"></i> 2. ELECTRONIC REPAIR (GADGET/APPLIANCE)</h6>
                                         </div>
                                         <div class="card-body">
                                             <div class="row">
                                                 <div class="col-md-6">
                                                     <h6 class="text-success"><i class="fas fa-snowflake"></i> Major Appliances</h6>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_11" name="tech_skills[]" value="Air Conditioner (AC) Repair (Split, Window, Central)">
                                                         <label class="custom-control-label" for="skill_11">Air Conditioner (AC) Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_12" name="tech_skills[]" value="Refrigerator Repair and Gas Charging">
                                                         <label class="custom-control-label" for="skill_12">Refrigerator Repair and Gas Charging</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_13" name="tech_skills[]" value="Washing Machine Repair (Semi/Fully automatic, Front/Top Load)">
                                                         <label class="custom-control-label" for="skill_13">Washing Machine Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_14" name="tech_skills[]" value="Microwave Oven Repair">
                                                         <label class="custom-control-label" for="skill_14">Microwave Oven Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_15" name="tech_skills[]" value="Geyser (Water Heater) Repair">
                                                         <label class="custom-control-label" for="skill_15">Geyser (Water Heater) Repair</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <h6 class="text-success"><i class="fas fa-tv"></i> Other Gadgets</h6>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_16" name="tech_skills[]" value="Fan Repair (Ceiling, Table, Exhaust)">
                                                         <label class="custom-control-label" for="skill_16">Fan Repair (Ceiling, Table, Exhaust)</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_17" name="tech_skills[]" value="Television (TV) Repair and Troubleshooting">
                                                         <label class="custom-control-label" for="skill_17">Television (TV) Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_18" name="tech_skills[]" value="Electric Iron/Press Repair">
                                                         <label class="custom-control-label" for="skill_18">Electric Iron/Press Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_19" name="tech_skills[]" value="Music System/Home Theatre Repair">
                                                         <label class="custom-control-label" for="skill_19">Music System/Home Theatre Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_20" name="tech_skills[]" value="Electric Heater Repair (Room Heaters, Rods)">
                                                         <label class="custom-control-label" for="skill_20">Electric Heater Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_21" name="tech_skills[]" value="Induction Cooktop and Electric Stove Repair">
                                                         <label class="custom-control-label" for="skill_21">Induction Cooktop and Electric Stove Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_22" name="tech_skills[]" value="Air Cooler Repair">
                                                         <label class="custom-control-label" for="skill_22">Air Cooler Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_23" name="tech_skills[]" value="Power Tools Repair (Drills, Cutters, Grinders, etc.)">
                                                         <label class="custom-control-label" for="skill_23">Power Tools Repair</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_24" name="tech_skills[]" value="Water Filter/Purifier Repair">
                                                         <label class="custom-control-label" for="skill_24">Water Filter/Purifier Repair</label>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- 3. INSTALLATION & SETUP -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <div class="card border-info">
                                         <div class="card-header bg-info text-white">
                                             <h6 class="mb-0"><i class="fas fa-cog"></i> 3. INSTALLATION & SETUP</h6>
                                         </div>
                                         <div class="card-body">
                                             <div class="row">
                                                 <div class="col-md-6">
                                                     <h6 class="text-info"><i class="fas fa-plug"></i> Appliance Setup</h6>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_25" name="tech_skills[]" value="TV/DTH Dish Installation and Tuning">
                                                         <label class="custom-control-label" for="skill_25">TV/DTH Dish Installation and Tuning</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_26" name="tech_skills[]" value="Electric Chimney Installation">
                                                         <label class="custom-control-label" for="skill_26">Electric Chimney Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_27" name="tech_skills[]" value="Ceiling and Wall Fan Installation">
                                                         <label class="custom-control-label" for="skill_27">Ceiling and Wall Fan Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_28" name="tech_skills[]" value="Washing Machine Installation and Uninstallation">
                                                         <label class="custom-control-label" for="skill_28">Washing Machine Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_29" name="tech_skills[]" value="Air Cooler Installation">
                                                         <label class="custom-control-label" for="skill_29">Air Cooler Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_30" name="tech_skills[]" value="Water Filter/Purifier Installation">
                                                         <label class="custom-control-label" for="skill_30">Water Filter/Purifier Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_31" name="tech_skills[]" value="Geyser/Water Heater Installation">
                                                         <label class="custom-control-label" for="skill_31">Geyser/Water Heater Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_32" name="tech_skills[]" value="Light Fixture Installation">
                                                         <label class="custom-control-label" for="skill_32">Light Fixture Installation</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <h6 class="text-info"><i class="fas fa-video"></i> Tech & Security</h6>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_33" name="tech_skills[]" value="CCTV and Security Camera Installation">
                                                         <label class="custom-control-label" for="skill_33">CCTV and Security Camera Installation</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_34" name="tech_skills[]" value="Wi-Fi Router and Modem Setup/Troubleshooting">
                                                         <label class="custom-control-label" for="skill_34">Wi-Fi Router and Modem Setup</label>
                                                     </div>
                                                     <div class="custom-control custom-checkbox mb-2">
                                                         <input type="checkbox" class="custom-control-input" id="skill_35" name="tech_skills[]" value="Smart Home Device Installation (Smart switches, smart lights)">
                                                         <label class="custom-control-label" for="skill_35">Smart Home Device Installation</label>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- 4. SERVICING & MAINTENANCE -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <div class="card border-warning">
                                         <div class="card-header bg-warning text-dark">
                                             <h6 class="mb-0"><i class="fas fa-tools"></i> 4. SERVICING & MAINTENANCE</h6>
                                         </div>
                                         <div class="card-body">
                                             <div class="row">
                                                 <div class="col-md-12">
                                                     <h6 class="text-warning"><i class="fas fa-broom"></i> Routine Care</h6>
                                                     <div class="row">
                                                         <div class="col-md-6">
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_36" name="tech_skills[]" value="AC Wet and Dry Servicing">
                                                                 <label class="custom-control-label" for="skill_36">AC Wet and Dry Servicing</label>
                                                             </div>
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_37" name="tech_skills[]" value="Washing Machine General Maintenance and Cleaning">
                                                                 <label class="custom-control-label" for="skill_37">Washing Machine Maintenance and Cleaning</label>
                                                             </div>
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_38" name="tech_skills[]" value="Geyser Descaling and Service">
                                                                 <label class="custom-control-label" for="skill_38">Geyser Descaling and Service</label>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_39" name="tech_skills[]" value="Water Filter Cartridge Replacement and General Service">
                                                                 <label class="custom-control-label" for="skill_39">Water Filter Cartridge Replacement</label>
                                                             </div>
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_40" name="tech_skills[]" value="Water Tank Cleaning (Manual and Motorized)">
                                                                 <label class="custom-control-label" for="skill_40">Water Tank Cleaning</label>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- 5. PLUMBING WORK -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <div class="card border-danger">
                                         <div class="card-header bg-danger text-white">
                                             <h6 class="mb-0"><i class="fas fa-faucet"></i> 5. PLUMBING WORK</h6>
                                         </div>
                                         <div class="card-body">
                                             <div class="row">
                                                 <div class="col-md-12">
                                                     <h6 class="text-danger"><i class="fas fa-wrench"></i> Fixtures & Taps</h6>
                                                     <div class="row">
                                                         <div class="col-md-6">
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_41" name="tech_skills[]" value="Tap, Faucet, and Shower Installation/Repair">
                                                                 <label class="custom-control-label" for="skill_41">Tap, Faucet, and Shower Installation/Repair</label>
                                                             </div>
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_42" name="tech_skills[]" value="Washbasin and Sink Installation/Repair">
                                                                 <label class="custom-control-label" for="skill_42">Washbasin and Sink Installation/Repair</label>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <div class="custom-control custom-checkbox mb-2">
                                                                 <input type="checkbox" class="custom-control-input" id="skill_43" name="tech_skills[]" value="Toilet, Commode, and Flush Tank Installation">
                                                                 <label class="custom-control-label" for="skill_43">Toilet, Commode, and Flush Tank Installation</label>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             
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
         
         <!-- EZ ID Auto Generation Script -->
         <script>
         function generateNextEZID() {
             const ezIdInput = document.getElementById('t_ez_id');
             const btn = event.target.closest('button');
             const originalHTML = btn.innerHTML;
             
             // Show loading state
             btn.disabled = true;
             btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
             
             // Fetch next EZ ID from server
             fetch('api-generate-ez-id.php')
                 .then(response => response.json())
                 .then(data => {
                     if(data.success) {
                         ezIdInput.value = data.ez_id;
                         ezIdInput.style.backgroundColor = '#d4edda';
                         ezIdInput.style.borderColor = '#28a745';
                         
                         // Show success message
                         swal("Success!", "Generated EZ ID: " + data.ez_id, "success");
                         
                         // Reset styling after 2 seconds
                         setTimeout(() => {
                             ezIdInput.style.backgroundColor = '';
                             ezIdInput.style.borderColor = '';
                         }, 2000);
                     } else {
                         swal("Error!", data.message || "Failed to generate EZ ID", "error");
                     }
                 })
                 .catch(error => {
                     console.error('Error:', error);
                     swal("Error!", "Failed to generate EZ ID. Please try again.", "error");
                 })
                 .finally(() => {
                     // Restore button state
                     btn.disabled = false;
                     btn.innerHTML = originalHTML;
                 });
         }
         
         // Auto-generate EZ ID on page load
         window.addEventListener('DOMContentLoaded', function() {
             generateNextEZID();
         });
         </script>

 </body>

 </html>