<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Load all services from database
$services_query = "SELECT s_id, s_name, s_category, s_subcategory FROM tms_service ORDER BY s_name";
$services_result = $mysqli->query($services_query);

// Define EXACT order matching booking form and add service form
$category_order = [
    'BASIC ELECTRICAL WORK',
    'ELECTRONIC REPAIR',
    'INSTALLATION & SETUP',
    'SERVICING & MAINTENANCE',
    'PLUMBING WORK'
];

$subcategory_order = [
    'Wiring & Fixtures',
    'Safety & Power',
    'Major Appliances',
    'Other Gadgets',
    'Appliance Setup',
    'Tech & Security',
    'Routine Care',
    'Fixtures & Taps'
];

$all_services_temp = [];
$categories = [];
$subcategories = [];

while ($row = $services_result->fetch_assoc()) {
    $subcategory = $row['s_subcategory'];
    $category = $row['s_category'];
    
    if (!in_array($category, $categories)) {
        $categories[] = $category;
    }
    if (!in_array($subcategory, $subcategories)) {
        $subcategories[] = $subcategory;
    }
    
    if (!isset($all_services_temp[$subcategory])) {
        $all_services_temp[$subcategory] = [];
    }
    $all_services_temp[$subcategory][] = [
        'id' => $row['s_id'],
        'name' => $row['s_name'],
        'category' => $category
    ];
}

// Reorder services to match exact order
$all_services = [];
foreach ($subcategory_order as $subcat) {
    if (isset($all_services_temp[$subcat])) {
        $all_services[$subcat] = $all_services_temp[$subcat];
    }
}

// Reorder categories
$categories = $category_order;
$subcategories = $subcategory_order;

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
            // Add payment QR column if not exists
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_payment_qr VARCHAR(255) DEFAULT NULL");
            
            // Get existing QR code from guest technician
            $existing_qr_query = "SELECT t_payment_qr FROM tms_technician WHERE t_id = ?";
            $existing_qr_stmt = $mysqli->prepare($existing_qr_query);
            $existing_qr_stmt->bind_param('i', $guest_id);
            $existing_qr_stmt->execute();
            $existing_qr_result = $existing_qr_stmt->get_result();
            $existing_qr_data = $existing_qr_result->fetch_object();
            $t_payment_qr = $existing_qr_data->t_payment_qr ?? '';
            
            // Handle payment QR upload (only if admin uploads a new one)
            if(!empty($_FILES["t_payment_qr"]["name"])) {
                $qr_dir = "../uploads/technician_qr/";
                if(!file_exists($qr_dir)) {
                    mkdir($qr_dir, 0777, true);
                }
                $qr_extension = pathinfo($_FILES["t_payment_qr"]["name"], PATHINFO_EXTENSION);
                $qr_filename = "tech_qr_" . time() . "_" . rand(1000, 9999) . "." . $qr_extension;
                if(move_uploaded_file($_FILES["t_payment_qr"]["tmp_name"], $qr_dir . $qr_filename)) {
                    $t_payment_qr = "uploads/technician_qr/" . $qr_filename;
                }
            }
            
            // Approve guest technician
            if(!empty($t_payment_qr)) {
                $approve_query = "UPDATE tms_technician SET 
                                t_ez_id = ?,
                                t_id_no = ?,
                                t_category = ?,
                                t_specialization = ?,
                                t_booking_limit = ?,
                                t_payment_qr = ?,
                                t_status = 'Available',
                                t_is_guest = 0
                                WHERE t_id = ?";
                $approve_stmt = $mysqli->prepare($approve_query);
                $approve_stmt->bind_param('ssssssi', $t_ez_id, $t_ez_id, $t_category, $t_specialization, $t_booking_limit, $t_payment_qr, $guest_id);
            } else {
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
            }
            
            if($approve_stmt->execute()){
                // Get selected skills
                $selected_skill_ids = isset($_POST['skills']) ? $_POST['skills'] : [];
                
                // Check if skills are provided
                if(empty($selected_skill_ids)) {
                    // Rollback the approval - set back to guest status
                    $rollback_query = "UPDATE tms_technician SET t_status = 'Pending', t_is_guest = 1 WHERE t_id = ?";
                    $rollback_stmt = $mysqli->prepare($rollback_query);
                    $rollback_stmt->bind_param('i', $guest_id);
                    $rollback_stmt->execute();
                    
                    $_SESSION['error'] = "Please select at least one skill/service for the technician before approving!";
                    header("Location: admin-guest-technicians.php");
                    exit();
                }
                
                // Check which column names exist in the table
                $columns_check = $mysqli->query("SHOW COLUMNS FROM tms_technician_skills");
                $column_names = [];
                while($col = $columns_check->fetch_assoc()) {
                    $column_names[] = $col['Field'];
                }
                
                // Determine correct column names based on what exists
                $tech_col = 'ts_technician_id';
                $service_col = 'ts_service_id';
                
                if(in_array('technician_id', $column_names)) {
                    $tech_col = 'technician_id';
                } elseif(in_array('t_id', $column_names)) {
                    $tech_col = 't_id';
                }
                
                if(in_array('service_id', $column_names)) {
                    $service_col = 'service_id';
                } elseif(in_array('s_id', $column_names)) {
                    $service_col = 's_id';
                }
                
                // Delete any existing skills for this technician first (in case of re-approval)
                $delete_old_skills = "DELETE FROM tms_technician_skills WHERE $tech_col = ?";
                $delete_stmt = $mysqli->prepare($delete_old_skills);
                $delete_stmt->bind_param('i', $guest_id);
                $delete_stmt->execute();
                
                // Insert skills with detected column names
                $skill_insert = "INSERT INTO tms_technician_skills ($tech_col, $service_col) VALUES (?, ?)";
                $skill_stmt = $mysqli->prepare($skill_insert);
                
                $success_count = 0;
                $failed_skills = [];
                
                foreach ($selected_skill_ids as $service_id) {
                    $service_id = intval($service_id);
                    $skill_stmt->bind_param("ii", $guest_id, $service_id);
                    if ($skill_stmt->execute()) {
                        $success_count++;
                    } else {
                        $failed_skills[] = $service_id;
                    }
                }
                $skill_stmt->close();
                
                if($success_count > 0) {
                    $_SESSION['success'] = "Guest technician approved successfully with " . $success_count . " skills! Now a regular EZ Technician and ready for job assignments.";
                } else {
                    // Rollback if no skills were added
                    $rollback_query = "UPDATE tms_technician SET t_status = 'Pending', t_is_guest = 1 WHERE t_id = ?";
                    $rollback_stmt = $mysqli->prepare($rollback_query);
                    $rollback_stmt->bind_param('i', $guest_id);
                    $rollback_stmt->execute();
                    
                    $_SESSION['error'] = "Failed to add skills. Technician approval cancelled. Please try again.";
                }
                
                header("Location: admin-guest-technicians.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to approve technician: " . $mysqli->error;
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
                                                <form method="POST" enctype="multipart/form-data" class="border p-3 bg-light">
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
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i> <strong>IMPORTANT:</strong> You must select at least one service skill below before approving. Without skills, the technician won't appear in job assignment lists!
                                                            </div>
                                                            <button type="button" class="btn btn-danger btn-block btn-lg" data-toggle="collapse" data-target="#skills_<?php echo $guest->t_id; ?>">
                                                                <i class="fas fa-tools"></i> Select Service Skills (REQUIRED - Click to Expand)
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="collapse show mt-3" id="skills_<?php echo $guest->t_id; ?>">
                                                        <div class="card card-body bg-light border-danger">
                                                            <div class="alert alert-danger">
                                                                <i class="fas fa-exclamation-circle"></i> <strong>REQUIRED:</strong> Select all specific services this technician can perform. At least one skill must be selected!
                                                            </div>
                                                            
                                                            <?php 
                                                            $colors = ['primary', 'info', 'success', 'warning', 'secondary', 'dark', 'danger', 'primary'];
                                                            $counter = 1;
                                                            
                                                            foreach ($all_services as $subcategory => $services): 
                                                                $color = isset($colors[$counter-1]) ? $colors[$counter-1] : 'secondary';
                                                            ?>
                                                            <div class="card border-<?php echo $color; ?> mb-3">
                                                                <div class="card-header bg-<?php echo $color; ?> text-white py-2">
                                                                    <strong><i class="fas fa-wrench"></i> <?php echo $counter; ?>. <?php echo htmlspecialchars($subcategory); ?></strong>
                                                                    <span class="badge badge-light text-dark ml-2"><?php echo count($services); ?> services</span>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <?php foreach ($services as $service): ?>
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" 
                                                                                       id="skill_<?php echo $service['id']; ?>_<?php echo $guest->t_id; ?>" 
                                                                                       name="skills[]" 
                                                                                       value="<?php echo $service['id']; ?>">
                                                                                <label class="custom-control-label" for="skill_<?php echo $service['id']; ?>_<?php echo $guest->t_id; ?>">
                                                                                    <?php echo htmlspecialchars($service['name']); ?>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php 
                                                                $counter++;
                                                            endforeach; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-qrcode"></i> Payment QR Code</label>
                                                                <?php if(!empty($guest->t_payment_qr)): ?>
                                                                    <div class="alert alert-success">
                                                                        <i class="fas fa-check-circle"></i> Technician has already uploaded a payment QR code
                                                                        <div class="mt-2">
                                                                            <img src="../<?php echo htmlspecialchars($guest->t_payment_qr); ?>" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                                        </div>
                                                                        <small class="d-block mt-2 text-muted">You can replace it by uploading a new one below (optional)</small>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <input type="file" class="form-control-file" name="t_payment_qr" accept="image/*">
                                                                <small class="text-muted">
                                                                    <?php if(!empty($guest->t_payment_qr)): ?>
                                                                        Upload only if you want to replace the existing QR code
                                                                    <?php else: ?>
                                                                        Upload technician's personal payment QR for direct payments (Optional)
                                                                    <?php endif; ?>
                                                                </small>
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
