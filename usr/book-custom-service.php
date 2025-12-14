<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info
$query = "SELECT * FROM tms_user WHERE u_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $aid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_object();

// Handle form submission
if(isset($_POST['submit_custom_booking'])) {
    $service_name = trim($_POST['service_name']);
    $service_description = trim($_POST['service_description']);
    $subcategory = isset($_POST['subcategory']) ? trim($_POST['subcategory']) : '';
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['preferred_time'];
    $address = trim($_POST['address']);
    $pincode = trim($_POST['pincode']);
    $phone = $user->u_phone;
    
    // Check active bookings limit (3 bookings per user)
    $check_active_bookings = "SELECT COUNT(*) as active_count FROM tms_service_booking 
                               WHERE sb_user_id = ? 
                               AND sb_status NOT IN ('Rejected', 'Cancelled', 'Completed')";
    $stmt_check_limit = $mysqli->prepare($check_active_bookings);
    $stmt_check_limit->bind_param('i', $aid);
    $stmt_check_limit->execute();
    $result_limit = $stmt_check_limit->get_result();
    $limit_data = $result_limit->fetch_object();
    $active_bookings_count = $limit_data->active_count;
    $stmt_check_limit->close();
    
    // If user already has 3 or more active bookings, reject the new booking
    if($active_bookings_count >= 3) {
        $error = "You have reached the maximum limit of 3 active bookings. Please wait for one of your bookings to be completed.";
    }
    // Validation
    elseif(empty($service_name) || empty($service_description) || empty($subcategory) || empty($preferred_date) || empty($address) || empty($pincode)) {
        $error = "Please fill all required fields including service category";
    } else {
        // Check if "Custom Service" exists in tms_service table, if not create it
        $check_service = "SELECT s_id FROM tms_service WHERE s_name = 'Custom Service Request' LIMIT 1";
        $check_result = $mysqli->query($check_service);
        
        if($check_result->num_rows > 0) {
            $service_row = $check_result->fetch_object();
            $custom_service_id = $service_row->s_id;
        } else {
            // Create the custom service entry
            $create_service = "INSERT INTO tms_service (s_name, s_category, s_price, s_duration, s_description, s_status) 
                              VALUES ('Custom Service Request', 'Custom Service', 0, 'To be determined', 'Customer requested custom service - price and duration to be quoted', 'Active')";
            if($mysqli->query($create_service)) {
                $custom_service_id = $mysqli->insert_id;
            } else {
                $error = "System error. Please try again.";
                $custom_service_id = null;
            }
        }
        
        if($custom_service_id) {
            // Create booking with custom service
            $status = "Pending";
            $total_price = 0; // Will be quoted by admin
            
            $insert_query = "INSERT INTO tms_service_booking 
                            (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_phone, sb_address, sb_pincode, sb_description, sb_status, sb_total_price) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $mysqli->prepare($insert_query);
            $description = "Custom Service: " . $service_name . " - " . $service_description;
            $stmt->bind_param('iisssssssd', $aid, $custom_service_id, $preferred_date, $preferred_time, $phone, $address, $pincode, $description, $status, $total_price);
            
            if($stmt->execute()) {
                $booking_id = $mysqli->insert_id;
                $_SESSION['booking_success'] = "Custom service request submitted! Booking ID: #" . str_pad($booking_id, 5, '0', STR_PAD_LEFT);
                header("location: user-manage-booking.php");
                exit();
            } else {
                $error = "Failed to submit request. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">
    <title>Book Custom Service - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="vendor/inc/navbar-styles.css?v=<?php echo time(); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            min-height: 100vh;
            padding-top: 75px;
            padding-bottom: 70px;
        }
        

        

        
        .content {
            padding: 20px 15px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .info-box {
            background: linear-gradient(135deg, #ffe8f0 0%, #ffd6e8 100%);
            border-left: 4px solid #d13abd;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .info-box i {
            color: #d13abd;
            margin-right: 8px;
        }
        
        .info-box p {
            font-size: 13px;
            color: #b91c9e;
            margin: 0;
            line-height: 1.6;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-label .required {
            color: #ef4444;
            margin-left: 3px;
        }
        
        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: #d13abd;
            background: #fff5f7;
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        
        .btn-submit:active {
            transform: scale(0.98);
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        .examples-section {
            background: #f9fafb;
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
        }
        
        .examples-title {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
        }
        
        .example-item {
            font-size: 12px;
            color: #666;
            padding: 5px 0;
            padding-left: 15px;
            position: relative;
        }
        
        .example-item:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #6366f1;
        }
        
        .subcategory-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .subcategory-btn {
            padding: 15px 10px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
        }
        
        .subcategory-btn i {
            font-size: 24px;
            color: #6366f1;
        }
        
        .subcategory-btn:hover {
            border-color: #d13abd;
            background: linear-gradient(135deg, rgba(209, 58, 189, 0.05) 0%, rgba(236, 110, 173, 0.05) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(209, 58, 189, 0.15);
        }
        
        .subcategory-btn.active {
            border-color: #d13abd;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3);
        }
        
        .subcategory-btn.active i {
            color: white;
        }
        

    </style>
</head>
<body>
    <?php include('vendor/inc/navbar.php'); ?>

    <div class="content">
        <div class="page-title">
            <i class="fas fa-plus-circle" style="color: #6366f1;"></i>
            Book Custom Service
        </div>
        <div class="page-subtitle">
            Can't find the service you need? Tell us what you're looking for and we'll help you!
        </div>

        <div class="info-box">
            <p>
                <i class="fas fa-info-circle"></i>
                <strong>How it works:</strong> Describe the service you need, and our team will review your request. We'll contact you with a quote and schedule the service.
            </p>
        </div>

        <?php if(isset($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">
                        Service Name <span class="required">*</span>
                    </label>
                    <input type="text" name="service_name" class="form-input" required 
                           placeholder="e.g., Solar Panel Installation" maxlength="100">
                    <div class="form-hint">Brief name of the service you need</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Service Description <span class="required">*</span>
                    </label>
                    <textarea name="service_description" class="form-textarea" required 
                              placeholder="Please describe what you need in detail..."></textarea>
                    <div class="form-hint">Provide as much detail as possible about your requirements</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Service Category <span class="required">*</span>
                    </label>
                    <div class="subcategory-grid">
                        <button type="button" class="subcategory-btn" data-subcategory="Wiring & Fixtures">
                            <i class="fas fa-plug"></i>
                            <span>Wiring & Fixtures</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Safety & Power">
                            <i class="fas fa-shield-alt"></i>
                            <span>Safety & Power</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Major Appliances">
                            <i class="fas fa-tv"></i>
                            <span>Major Appliances</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Other Gadgets">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Other Gadgets</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Appliance Setup">
                            <i class="fas fa-tools"></i>
                            <span>Appliance Setup</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Tech & Security">
                            <i class="fas fa-video"></i>
                            <span>Tech & Security</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Routine Care">
                            <i class="fas fa-wrench"></i>
                            <span>Routine Care</span>
                        </button>
                        <button type="button" class="subcategory-btn" data-subcategory="Fixtures & Taps">
                            <i class="fas fa-faucet"></i>
                            <span>Fixtures & Taps</span>
                        </button>
                    </div>
                    <input type="hidden" name="subcategory" id="subcategoryInput" required>
                    <div class="form-hint" id="subcategoryHint">Select the category that best matches your service</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Preferred Date <span class="required">*</span>
                    </label>
                    <input type="date" name="preferred_date" class="form-input" required 
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Preferred Time
                    </label>
                    <select name="preferred_time" class="form-select">
                        <option value="09:00:00">Morning (9 AM - 12 PM)</option>
                        <option value="14:00:00">Afternoon (2 PM - 5 PM)</option>
                        <option value="17:00:00">Evening (5 PM - 8 PM)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Service Address <span class="required">*</span>
                    </label>
                    <textarea name="address" class="form-textarea" required 
                              placeholder="Enter complete address where service is needed"><?php echo htmlspecialchars($user->u_addr ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Pincode <span class="required">*</span>
                    </label>
                    <input type="text" name="pincode" class="form-input" required 
                           pattern="[0-9]{6}" maxlength="6" 
                           placeholder="6-digit pincode"
                           value="<?php echo htmlspecialchars($user->u_pincode ?? ''); ?>">
                </div>

                <button type="submit" name="submit_custom_booking" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Submit Request
                </button>
            </form>

            <div class="examples-section">
                <div class="examples-title">Examples of custom services:</div>
                <div class="example-item">Solar panel installation and setup</div>
                <div class="example-item">Home automation system installation</div>
                <div class="example-item">Water purifier installation and service</div>
                <div class="example-item">Generator repair and maintenance</div>
                <div class="example-item">Electrical wiring for new construction</div>
                <div class="example-item">Any other electrical/plumbing service</div>
            </div>
        </div>
    </div>



    <script>
        // Handle subcategory button clicks
        document.querySelectorAll('.subcategory-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.subcategory-btn').forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Set hidden input value
                const subcategory = this.getAttribute('data-subcategory');
                document.getElementById('subcategoryInput').value = subcategory;
                
                // Update hint
                document.getElementById('subcategoryHint').innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> Selected: <strong>' + subcategory + '</strong>';
            });
        });
        
        // Form validation - ensure subcategory is selected
        document.querySelector('form').addEventListener('submit', function(e) {
            const subcategoryInput = document.getElementById('subcategoryInput');
            if(!subcategoryInput.value) {
                e.preventDefault();
                alert('Please select a service category');
                document.querySelector('.subcategory-grid').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>

    <?php include('vendor/inc/bottom-nav.php'); ?>
</body>
</html>
