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

// Handle form submission
if (isset($_POST['add_tech'])) {
    $t_name = $_POST['t_name'];
    $t_phone = isset($_POST['t_phone']) ? $_POST['t_phone'] : '';
    $t_aadhar = isset($_POST['t_aadhar']) ? $_POST['t_aadhar'] : '';
    $t_ez_id = isset($_POST['t_ez_id']) ? $_POST['t_ez_id'] : '';
    $t_id_no = $t_ez_id;
    $t_pwd = isset($_POST['t_pwd']) ? $_POST['t_pwd'] : '';
    $t_category = $_POST['t_category'];
    $t_specialization = isset($_POST['t_specialization']) ? $_POST['t_specialization'] : '';
    $t_experience = $_POST['t_experience'];
    $t_service_pincode = isset($_POST['t_service_pincode']) ? $_POST['t_service_pincode'] : '';
    $t_booking_limit = isset($_POST['t_booking_limit']) ? intval($_POST['t_booking_limit']) : 1;
    $t_status = 'Available';
    
    // Validate inputs
    if(!empty($t_phone) && !preg_match('/^[0-9]{10}$/', $t_phone)) {
        $_SESSION['error'] = "Phone number must be exactly 10 digits";
        header("Location: admin-add-technician.php");
        exit();
    }
    
    // Get selected skill IDs
    $selected_skill_ids = isset($_POST['skills']) ? $_POST['skills'] : [];
    
    if (empty($selected_skill_ids)) {
        $_SESSION['error'] = "Please select at least one skill!";
        header("Location: admin-add-technician.php");
        exit();
    }
    
    // Add QR code column if not exists
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_payment_qr VARCHAR(255) DEFAULT NULL");
    
    // Insert technician
    $t_pic = isset($_FILES["t_pic"]["name"]) ? $_FILES["t_pic"]["name"] : '';
    if(!empty($t_pic)) {
        move_uploaded_file($_FILES["t_pic"]["tmp_name"],"../vendor/img/".$_FILES["t_pic"]["name"]);
    }
    
    // Handle payment QR upload
    $t_payment_qr = '';
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
    
    $query = "INSERT INTO tms_technician (t_name, t_phone, t_aadhar, t_ez_id, t_id_no, t_pwd, t_category, t_specialization, t_experience, t_service_pincode, t_pic, t_payment_qr, t_status, t_booking_limit, t_current_bookings) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $mysqli->prepare($query);
    
    if(!$stmt) {
        $_SESSION['error'] = "Database prepare error: " . $mysqli->error;
        header("Location: admin-add-technician.php");
        exit();
    }
    
    $stmt->bind_param("sssssssssssssi", $t_name, $t_phone, $t_aadhar, $t_ez_id, $t_id_no, $t_pwd, $t_category, $t_specialization, $t_experience, $t_service_pincode, $t_pic, $t_payment_qr, $t_status, $t_booking_limit);
    
    if ($stmt->execute()) {
        $technician_id = $mysqli->insert_id;
        
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
        
        // Insert skills with detected column names
        $skill_insert = "INSERT INTO tms_technician_skills ($tech_col, $service_col) VALUES (?, ?)";
        $skill_stmt = $mysqli->prepare($skill_insert);
        
        if(!$skill_stmt) {
            $_SESSION['error'] = "Skills table error. Columns: $tech_col, $service_col. Error: " . $mysqli->error;
            header("Location: admin-add-technician.php");
            exit();
        }
        
        $success_count = 0;
        $skill_errors = [];
        foreach ($selected_skill_ids as $service_id) {
            $service_id = intval($service_id);
            $skill_stmt->bind_param("ii", $technician_id, $service_id);
            if ($skill_stmt->execute()) {
                $success_count++;
            } else {
                $skill_errors[] = "Service ID $service_id: " . $skill_stmt->error;
            }
        }
        $skill_stmt->close();
        
        if(!empty($skill_errors)) {
            $_SESSION['error'] = "Technician added but some skills failed: " . implode(", ", $skill_errors);
            header("Location: admin-manage-technician.php");
            exit;
        } else {
            // Redirect with success modal
            $success_message = "Technician added successfully with " . $success_count . " skills!";
            $redirect_url = "admin-manage-technician.php";
            header("Location: admin-add-technician.php?success=1&message=" . urlencode($success_message) . "&redirect=" . urlencode($redirect_url));
            exit;
        }
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: admin-add-technician.php");
        exit();
    }
}

// Get messages from session
$succ = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$err = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <?php if(!empty($succ)) {?>
                <script>
                setTimeout(function() {
                    swal({
                        title: "Success!",
                        text: "<?php echo $succ;?>",
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                }, 100);
                </script>
                <?php } ?>
                <?php if(!empty($err)) {?>
                <script>
                setTimeout(function() {
                    swal("Failed!", "<?php echo $err;?>!", "error");
                }, 100);
                </script>
                <?php } ?>
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Technicians</a></li>
                    <li class="breadcrumb-item active">Add Technician</li>
                </ol>
                <hr>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-plus"></i> Add New Technician</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <!-- Basic Information -->
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
                                            <input type="tel" class="form-control" name="t_phone" placeholder="10-digit mobile" pattern="[0-9]{10}" maxlength="10" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-id-card-alt text-warning"></i> Aadhaar Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="t_aadhar" placeholder="12-digit Aadhaar" pattern="[0-9]{12}" maxlength="12" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-id-badge text-primary"></i> EZ ID <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="t_ez_id" name="t_ez_id" placeholder="Auto-generating..." required readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-info" type="button" onclick="generateNextEZID(event)" title="Regenerate EZ ID">
                                                    <i class="fas fa-sync-alt"></i> Regenerate
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-success"><i class="fas fa-check-circle"></i> EZ ID is auto-generated on page load</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="t_pwd" placeholder="Login password" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Professional Details -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-briefcase"></i> Professional Details</h6>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Primary Service Category <span class="text-danger">*</span></label>
                                        <select class="form-control" name="t_category" required>
                                            <option value="">Select Category...</option>
                                            <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Specialization</label>
                                        <input type="text" class="form-control" name="t_specialization" placeholder="e.g., AC Repair, Plumbing">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Experience (Years)</label>
                                        <input type="number" class="form-control" name="t_experience" placeholder="e.g., 5" min="0" value="0">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Service Pincode <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="t_service_pincode" placeholder="6-digit" pattern="[0-9]{6}" maxlength="6" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Booking Limit <span class="text-danger">*</span></label>
                                        <select class="form-control" name="t_booking_limit" required>
                                            <option value="1" selected>1 Booking</option>
                                            <option value="2">2 Bookings</option>
                                            <option value="3">3 Bookings</option>
                                            <option value="4">4 Bookings</option>
                                            <option value="5">5 Bookings</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-circle"></i> Profile Picture</label>
                                        <input type="file" class="form-control-file" name="t_pic" accept="image/*">
                                        <small class="text-muted">Upload technician photo</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-qrcode"></i> Payment QR Code (Optional)</label>
                                        <input type="file" class="form-control-file" name="t_payment_qr" accept="image/*">
                                        <small class="text-muted">Upload technician's personal payment QR</small>
                                    </div>
                                </div>
                            </div>

                            
                            <!-- Service Skills Section - Dynamic from Database -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-tools"></i> Service Skills 
                                        <small class="text-muted">(Select all services this technician can perform)</small>
                                    </h6>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Check all specific services this technician is skilled in. This ensures accurate job assignment.
                                    </div>
                                </div>
                            </div>
                            
                            <?php 
                            $colors = ['primary', 'info', 'success', 'warning', 'secondary', 'dark', 'danger', 'primary'];
                            $counter = 1;
                            
                            foreach ($all_services as $subcategory => $services): 
                                $color = isset($colors[$counter-1]) ? $colors[$counter-1] : 'secondary';
                            ?>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card border-<?php echo $color; ?>">
                                        <div class="card-header bg-<?php echo $color; ?> text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-wrench"></i> <?php echo $counter; ?>. <?php echo htmlspecialchars($subcategory); ?> 
                                                <span class="badge badge-light text-dark"><?php echo count($services); ?> services</span>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <?php foreach ($services as $service): ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input type="checkbox" class="custom-control-input" 
                                                               id="skill_<?php echo $service['id']; ?>" 
                                                               name="skills[]" 
                                                               value="<?php echo $service['id']; ?>">
                                                        <label class="custom-control-label" for="skill_<?php echo $service['id']; ?>">
                                                            <?php echo htmlspecialchars($service['name']); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                $counter++;
                            endforeach; ?>
                            
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
                    </div>
                </div>
            </div>
            <?php include('vendor/inc/footer.php');?>
        </div>
    </div>

    <script>
    function generateNextEZID(event) {
        if(event) event.preventDefault();
        fetch('api-generate-ez-id.php')
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('t_ez_id').value = data.ez_id;
                } else {
                    console.error('Error generating EZ ID:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Auto-generate EZ ID when page loads
    window.addEventListener('DOMContentLoaded', function() {
        generateNextEZID(null);
        setupValidation();
    });
    
    // Real-time validation for mobile and Aadhaar
    function setupValidation() {
        const phoneInput = document.querySelector('input[name="t_phone"]');
        const aadharInput = document.querySelector('input[name="t_aadhar"]');
        const submitBtn = document.querySelector('button[name="add_tech"]');
        
        let phoneValid = true;
        let aadharValid = true;
        
        // Create error message containers
        const phoneError = document.createElement('div');
        phoneError.className = 'text-danger mt-1';
        phoneError.style.fontSize = '0.875rem';
        phoneInput.parentElement.appendChild(phoneError);
        
        const aadharError = document.createElement('div');
        aadharError.className = 'text-danger mt-1';
        aadharError.style.fontSize = '0.875rem';
        aadharInput.parentElement.appendChild(aadharError);
        
        // Check phone number
        phoneInput.addEventListener('blur', function() {
            const phone = this.value.trim();
            if (phone.length === 10) {
                fetch('api-check-technician-exists.php?phone=' + phone)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            phoneError.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                            phoneInput.classList.add('is-invalid');
                            phoneValid = false;
                            submitBtn.disabled = true;
                        } else {
                            phoneError.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Mobile number available</span>';
                            phoneInput.classList.remove('is-invalid');
                            phoneInput.classList.add('is-valid');
                            phoneValid = true;
                            if (aadharValid) submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
        
        // Check Aadhaar number
        aadharInput.addEventListener('blur', function() {
            const aadhar = this.value.trim();
            if (aadhar.length === 12) {
                fetch('api-check-technician-exists.php?aadhar=' + aadhar)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            aadharError.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                            aadharInput.classList.add('is-invalid');
                            aadharValid = false;
                            submitBtn.disabled = true;
                        } else {
                            aadharError.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Aadhaar number available</span>';
                            aadharInput.classList.remove('is-invalid');
                            aadharInput.classList.add('is-valid');
                            aadharValid = true;
                            if (phoneValid) submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
        
        // Clear validation on input change
        phoneInput.addEventListener('input', function() {
            if (this.value.length < 10) {
                phoneError.innerHTML = '';
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
        
        aadharInput.addEventListener('input', function() {
            if (this.value.length < 12) {
                aadharError.innerHTML = '';
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }
    </script>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
    
    <!-- Success Modal -->
    <?php include("vendor/inc/success-modal.php");?>
</body>
</html>
