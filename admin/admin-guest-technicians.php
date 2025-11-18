<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle approval with password verification
if(isset($_POST['approve_guest'])){
    $guest_id = intval($_POST['guest_id']);
    $admin_password = $_POST['admin_password'];
    $t_ez_id = $_POST['t_ez_id'];
    $t_category = $_POST['t_category'];
    $t_specialization = $_POST['t_specialization'];
    $t_booking_limit = intval($_POST['t_booking_limit']);
    
    // Verify admin password
    $admin_check = "SELECT a_pwd FROM tms_admin WHERE a_id = ?";
    $admin_stmt = $mysqli->prepare($admin_check);
    $admin_stmt->bind_param('i', $aid);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();
    $admin_data = $admin_result->fetch_object();
    
    // Hash the entered password with MD5 to compare with stored hash
    $admin_password_hash = md5($admin_password);
    
    if($admin_data && $admin_password_hash === $admin_data->a_pwd){
        // Get guest technician details
        $guest_details_query = "SELECT t_phone, t_aadhar FROM tms_technician WHERE t_id = ?";
        $guest_details_stmt = $mysqli->prepare($guest_details_query);
        $guest_details_stmt->bind_param('i', $guest_id);
        $guest_details_stmt->execute();
        $guest_details = $guest_details_stmt->get_result()->fetch_object();
        
        // Check if mobile or Aadhaar already exists for another approved technician
        $check_duplicate = "SELECT t_id, t_name, t_ez_id FROM tms_technician 
                           WHERE (t_phone = ? OR t_aadhar = ?) 
                           AND t_id != ? 
                           AND (t_is_guest = 0 OR t_status IN ('Available', 'Booked'))";
        $dup_stmt = $mysqli->prepare($check_duplicate);
        $dup_stmt->bind_param('ssi', $guest_details->t_phone, $guest_details->t_aadhar, $guest_id);
        $dup_stmt->execute();
        $dup_result = $dup_stmt->get_result();
        
        if($dup_result->num_rows > 0){
            $existing = $dup_result->fetch_object();
            $_SESSION['error'] = "This technician is already registered as EZ Technician: " . htmlspecialchars($existing->t_name) . " (EZ ID: " . htmlspecialchars($existing->t_ez_id) . "). Cannot approve duplicate registration.";
            header("Location: admin-guest-technicians.php");
            exit();
        } else {
            // Check if EZ ID already exists
            $check_ez = "SELECT t_id FROM tms_technician WHERE t_ez_id = ? AND t_id != ?";
            $check_stmt = $mysqli->prepare($check_ez);
            $check_stmt->bind_param('si', $t_ez_id, $guest_id);
            $check_stmt->execute();
            
            if($check_stmt->get_result()->num_rows > 0){
                $_SESSION['error'] = "EZ ID already exists! Please use a unique EZ ID.";
                header("Location: admin-guest-technicians.php");
                exit();
            } else {
            // Approve guest technician
            $approve_query = "UPDATE tms_technician SET 
                            t_ez_id = ?,
                            t_id_no = ?,
                            t_category = ?,
                            t_specialization = ?,
                            t_booking_limit = ?,
                            t_status = 'Available',
                            t_is_guest = 0
                            WHERE t_id = ?";
            $approve_stmt = $mysqli->prepare($approve_query);
            $approve_stmt->bind_param('ssssii', $t_ez_id, $t_ez_id, $t_category, $t_specialization, $t_booking_limit, $guest_id);
            
            if($approve_stmt->execute()){
                // Insert technician skills if provided
                if(isset($_POST['tech_skills']) && is_array($_POST['tech_skills'])) {
                    foreach($_POST['tech_skills'] as $skill) {
                        $insert_skill = "INSERT INTO tms_technician_skills (t_id, skill_name) VALUES (?, ?)";
                        $stmt_skill = $mysqli->prepare($insert_skill);
                        $stmt_skill->bind_param('is', $guest_id, $skill);
                        $stmt_skill->execute();
                    }
                    $_SESSION['success'] = "Guest technician approved successfully with " . count($_POST['tech_skills']) . " skills! Now a regular EZ Technician.";
                } else {
                    $_SESSION['success'] = "Guest technician approved successfully! Now a regular EZ Technician.";
                }
                header("Location: admin-guest-technicians.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to approve technician.";
                header("Location: admin-guest-technicians.php");
                exit();
            }
            }
        }
    } else {
        $_SESSION['error'] = "Invalid admin password! Approval denied.";
        header("Location: admin-guest-technicians.php");
        exit();
    }
}

// Handle rejection
if(isset($_POST['reject_guest'])){
    $guest_id = intval($_POST['guest_id']);
    $reject_reason = $_POST['reject_reason'];
    
    $reject_query = "UPDATE tms_technician SET t_status = 'Rejected' WHERE t_id = ?";
    $reject_stmt = $mysqli->prepare($reject_query);
    $reject_stmt->bind_param('i', $guest_id);
    
    if($reject_stmt->execute()){
        $_SESSION['success'] = "Guest technician rejected.";
        header("Location: admin-guest-technicians.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to reject technician.";
        header("Location: admin-guest-technicians.php");
        exit();
    }
}

// Get messages from session
if(isset($_SESSION['success'])) {
    $succ = $_SESSION['success'];
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])) {
    $err = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Get guest technicians
$guest_query = "SELECT * FROM tms_technician WHERE t_is_guest = 1 AND t_status = 'Pending' ORDER BY t_registered_at DESC";
$guest_result = $mysqli->query($guest_query);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Technicians</a></li>
                    <li class="breadcrumb-item active">Guest Technicians</li>
                </ol>

                <?php if(isset($succ)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle"></i> <?php echo $succ; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($err)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #0575E6 0%, #00F260 100%);">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-user-clock"></i> Guest Technician Registrations
                            <span class="badge badge-light ml-2"><?php echo $guest_result->num_rows; ?> Pending</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if($guest_result->num_rows > 0): ?>
                            <?php while($guest = $guest_result->fetch_object()): ?>
                                <div class="card mb-4 border-primary">
                                    <div class="card-header bg-light">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($guest->t_name); ?>
                                                    <span class="badge badge-warning ml-2">PENDING APPROVAL</span>
                                                </h5>
                                                <small class="text-muted">
                                                    Registered: <?php echo date('d M Y, h:i A', strtotime($guest->t_registered_at)); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <span class="badge badge-info">ID: <?php echo $guest->t_id_no; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <?php if(!empty($guest->t_pic)): ?>
                                                    <img src="../vendor/img/<?php echo $guest->t_pic; ?>" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                <?php else: ?>
                                                    <div class="bg-secondary text-white p-5">
                                                        <i class="fas fa-user fa-5x"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong><i class="fas fa-phone"></i> Phone:</strong> <?php echo htmlspecialchars($guest->t_phone); ?></p>
                                                        <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($guest->t_email); ?></p>
                                                        <p><strong><i class="fas fa-id-card-alt"></i> Aadhaar:</strong> <?php echo htmlspecialchars($guest->t_aadhar ?? 'N/A'); ?></p>
                                                        <p><strong><i class="fas fa-map-pin"></i> Service Pincode:</strong> <?php echo htmlspecialchars($guest->t_service_pincode); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong><i class="fas fa-clock"></i> Experience:</strong> <?php echo htmlspecialchars($guest->t_experience ?? 'N/A'); ?></p>
                                                        <p><strong><i class="fas fa-tools"></i> Skills:</strong> <?php echo htmlspecialchars($guest->t_skills ?? 'N/A'); ?></p>
                                                        <p><strong><i class="fas fa-map-marker-alt"></i> Address:</strong> <?php echo htmlspecialchars($guest->t_addr); ?></p>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <!-- Approval Form -->
                                                <form method="POST" class="border p-3 bg-light">
                                                    <input type="hidden" name="guest_id" value="<?php echo $guest->t_id; ?>">
                                                    <h6 class="text-success"><i class="fas fa-check-circle"></i> Approve & Convert to EZ Technician</h6>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>EZ ID <span class="text-danger">*</span></label>
                                                                <input type="text" name="t_ez_id" class="form-control" id="ez_id_<?php echo $guest->t_id; ?>" required readonly>
                                                                <button type="button" class="btn btn-sm btn-info mt-1" onclick="generateEZID(<?php echo $guest->t_id; ?>)">
                                                                    <i class="fas fa-sync"></i> Generate
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Service Category <span class="text-danger">*</span></label>
                                                                <select name="t_category" class="form-control" required>
                                                                    <option value="">Select Category</option>
                                                                    <option value="BASIC ELECTRICAL WORK">BASIC ELECTRICAL WORK</option>
                                                                    <option value="ELECTRONIC REPAIR">ELECTRONIC REPAIR</option>
                                                                    <option value="INSTALLATION & SETUP">INSTALLATION & SETUP</option>
                                                                    <option value="SERVICING & MAINTENANCE">SERVICING & MAINTENANCE</option>
                                                                    <option value="PLUMBING WORK">PLUMBING WORK</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Booking Limit <span class="text-danger">*</span></label>
                                                                <select name="t_booking_limit" class="form-control" required>
                                                                    <option value="1">1 booking at a time</option>
                                                                    <option value="2">2 bookings at a time</option>
                                                                    <option value="3">3 bookings at a time</option>
                                                                    <option value="4">4 bookings at a time</option>
                                                                    <option value="5">5 bookings at a time</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Specialization</label>
                                                                <input type="text" name="t_specialization" class="form-control" value="<?php echo htmlspecialchars($guest->t_skills ?? ''); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Detailed Skills Section -->
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <button type="button" class="btn btn-info btn-block" data-toggle="collapse" data-target="#skills_<?php echo $guest->t_id; ?>">
                                                                <i class="fas fa-tools"></i> Select Detailed Service Skills (Optional - Click to Expand)
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="collapse mt-3" id="skills_<?php echo $guest->t_id; ?>">
                                                        <div class="card card-body bg-light">
                                                            <p class="text-muted mb-3">
                                                                <i class="fas fa-info-circle"></i> Select all specific services this technician can perform
                                                            </p>
                                                            
                                                            <!-- BASIC ELECTRICAL WORK -->
                                                            <div class="card border-primary mb-3">
                                                                <div class="card-header bg-primary text-white py-2">
                                                                    <strong><i class="fas fa-bolt"></i> BASIC ELECTRICAL WORK</strong>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_1_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Home Wiring (New installation and repair)">
                                                                                <label class="custom-control-label" for="skill_1_<?php echo $guest->t_id; ?>">Home Wiring</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_2_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Switch/Socket Installation and Replacement">
                                                                                <label class="custom-control-label" for="skill_2_<?php echo $guest->t_id; ?>">Switch/Socket Installation</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_3_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Light Fixture Installation">
                                                                                <label class="custom-control-label" for="skill_3_<?php echo $guest->t_id; ?>">Light Fixture Installation</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_4_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Circuit Breaker troubleshooting">
                                                                                <label class="custom-control-label" for="skill_4_<?php echo $guest->t_id; ?>">Circuit Breaker troubleshooting</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_5_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Inverter/UPS installation">
                                                                                <label class="custom-control-label" for="skill_5_<?php echo $guest->t_id; ?>">Inverter/UPS installation</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_6_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Electrical fault finding">
                                                                                <label class="custom-control-label" for="skill_6_<?php echo $guest->t_id; ?>">Electrical fault finding</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- ELECTRONIC REPAIR -->
                                                            <div class="card border-success mb-3">
                                                                <div class="card-header bg-success text-white py-2">
                                                                    <strong><i class="fas fa-wrench"></i> ELECTRONIC REPAIR</strong>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_7_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Air Conditioner (AC) Repair">
                                                                                <label class="custom-control-label" for="skill_7_<?php echo $guest->t_id; ?>">AC Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_8_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Refrigerator Repair">
                                                                                <label class="custom-control-label" for="skill_8_<?php echo $guest->t_id; ?>">Refrigerator Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_9_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Washing Machine Repair">
                                                                                <label class="custom-control-label" for="skill_9_<?php echo $guest->t_id; ?>">Washing Machine Repair</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_10_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Microwave Oven Repair">
                                                                                <label class="custom-control-label" for="skill_10_<?php echo $guest->t_id; ?>">Microwave Oven Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_11_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Geyser Repair">
                                                                                <label class="custom-control-label" for="skill_11_<?php echo $guest->t_id; ?>">Geyser Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_12_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Fan Repair">
                                                                                <label class="custom-control-label" for="skill_12_<?php echo $guest->t_id; ?>">Fan Repair</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- INSTALLATION & SETUP -->
                                                            <div class="card border-warning mb-3">
                                                                <div class="card-header bg-warning text-dark py-2">
                                                                    <strong><i class="fas fa-cogs"></i> INSTALLATION & SETUP</strong>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_13_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="CCTV Camera Installation">
                                                                                <label class="custom-control-label" for="skill_13_<?php echo $guest->t_id; ?>">CCTV Camera Installation</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_14_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="WiFi Router Setup">
                                                                                <label class="custom-control-label" for="skill_14_<?php echo $guest->t_id; ?>">WiFi Router Setup</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_15_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Smart Home Device Setup">
                                                                                <label class="custom-control-label" for="skill_15_<?php echo $guest->t_id; ?>">Smart Home Device Setup</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_16_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="TV Wall Mounting">
                                                                                <label class="custom-control-label" for="skill_16_<?php echo $guest->t_id; ?>">TV Wall Mounting</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_17_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Appliance Installation">
                                                                                <label class="custom-control-label" for="skill_17_<?php echo $guest->t_id; ?>">Appliance Installation</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- SERVICING & MAINTENANCE -->
                                                            <div class="card border-info mb-3">
                                                                <div class="card-header bg-info text-white py-2">
                                                                    <strong><i class="fas fa-tools"></i> SERVICING & MAINTENANCE</strong>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_18_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="AC Servicing & Cleaning">
                                                                                <label class="custom-control-label" for="skill_18_<?php echo $guest->t_id; ?>">AC Servicing & Cleaning</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_19_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Chimney Cleaning">
                                                                                <label class="custom-control-label" for="skill_19_<?php echo $guest->t_id; ?>">Chimney Cleaning</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_20_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Water Purifier Servicing">
                                                                                <label class="custom-control-label" for="skill_20_<?php echo $guest->t_id; ?>">Water Purifier Servicing</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_21_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Geyser Servicing">
                                                                                <label class="custom-control-label" for="skill_21_<?php echo $guest->t_id; ?>">Geyser Servicing</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- PLUMBING WORK -->
                                                            <div class="card border-danger mb-3">
                                                                <div class="card-header bg-danger text-white py-2">
                                                                    <strong><i class="fas fa-wrench"></i> PLUMBING WORK</strong>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_22_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Tap/Faucet Repair">
                                                                                <label class="custom-control-label" for="skill_22_<?php echo $guest->t_id; ?>">Tap/Faucet Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_23_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Toilet Repair">
                                                                                <label class="custom-control-label" for="skill_23_<?php echo $guest->t_id; ?>">Toilet Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_24_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Washbasin Installation">
                                                                                <label class="custom-control-label" for="skill_24_<?php echo $guest->t_id; ?>">Washbasin Installation</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_25_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Pipe Leakage Repair">
                                                                                <label class="custom-control-label" for="skill_25_<?php echo $guest->t_id; ?>">Pipe Leakage Repair</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="skill_26_<?php echo $guest->t_id; ?>" name="tech_skills[]" value="Drainage Cleaning">
                                                                                <label class="custom-control-label" for="skill_26_<?php echo $guest->t_id; ?>">Drainage Cleaning</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Admin Password <span class="text-danger">*</span></label>
                                                                <input type="password" name="admin_password" class="form-control" placeholder="Enter your password" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button type="submit" name="approve_guest" class="btn btn-success btn-block">
                                                                <i class="fas fa-check"></i> Approve & Make EZ Technician
                                                            </button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn btn-danger btn-block" onclick="rejectGuest(<?php echo $guest->t_id; ?>)">
                                                                <i class="fas fa-times"></i> Reject Application
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                
                                                <!-- Hidden Reject Form -->
                                                <form method="POST" id="reject_form_<?php echo $guest->t_id; ?>" style="display:none;">
                                                    <input type="hidden" name="guest_id" value="<?php echo $guest->t_id; ?>">
                                                    <input type="hidden" name="reject_reason" id="reject_reason_<?php echo $guest->t_id; ?>">
                                                    <input type="hidden" name="reject_guest" value="1">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <h5>No Pending Guest Registrations</h5>
                                <p>All guest technician applications have been processed.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function generateEZID(guestId) {
        $.get('api-generate-ez-id.php', function(data){
            if(data.success) {
                $('#ez_id_' + guestId).val(data.ez_id);
            } else {
                alert('Error generating EZ ID: ' + data.message);
            }
        });
    }
    
    function rejectGuest(guestId) {
        var reason = prompt("Enter rejection reason:");
        if(reason) {
            $('#reject_reason_' + guestId).val(reason);
            $('#reject_form_' + guestId).submit();
        }
    }
    
    // Auto-generate EZ ID for all pending guests when page loads
    $(document).ready(function() {
        <?php 
        $guest_result->data_seek(0); // Reset result pointer
        while($guest = $guest_result->fetch_object()): 
        ?>
        generateEZID(<?php echo $guest->t_id; ?>);
        <?php endwhile; ?>
    });
    </script>
</body>
</html>
