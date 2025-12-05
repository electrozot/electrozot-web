<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get service details from URL
$service_name = isset($_GET['service_name']) ? $_GET['service_name'] : '';
$duration = isset($_GET['duration']) ? $_GET['duration'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';

if (empty($service_name)) {
    header("Location: book-service-step1.php");
    exit();
}

// Get user details
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();

// Handle form submission
if (isset($_POST['confirm_booking'])) {
    // Auto-detect current date and time
    date_default_timezone_set("Asia/Kolkata");
    $booking_date = date('Y-m-d');
    $booking_time = date('H:i:s');
    
    $pincode = $_POST['pincode'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
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
        $error_msg = "You have reached the maximum limit of 3 active bookings. Please wait for one of your bookings to be completed.";
    } else {
    
    // Get service ID from service name, or create service if not exists
    $service_query = "SELECT s_id FROM tms_service WHERE s_name = ? LIMIT 1";
    $service_stmt = $mysqli->prepare($service_query);
    $service_stmt->bind_param('s', $service_name);
    $service_stmt->execute();
    $service_result = $service_stmt->get_result();
    $service_data = $service_result->fetch_object();
    
    if ($service_data) {
        $service_id = $service_data->s_id;
    } else {
        // Create service if it doesn't exist (with default price 0)
        $default_price = 0;
        $create_service = "INSERT INTO tms_service (s_name, s_category, s_price, s_duration, s_description) VALUES (?, ?, ?, ?, ?)";
        $create_stmt = $mysqli->prepare($create_service);
        $service_desc = "Professional " . $service_name . " service";
        $create_stmt->bind_param('ssdss', $service_name, $category, $default_price, $duration, $service_desc);
        $create_stmt->execute();
        $service_id = $mysqli->insert_id;
    }
    
    // Also update tms_user table for backward compatibility
    $booking_info = $category . " > " . $subcategory . " > " . $service_name . " | Pincode: " . $pincode . " | Address: " . $address . " | Phone: " . $phone;
    $update_user = "UPDATE tms_user SET t_tech_category = ?, t_booking_date = ?, t_booking_status = 'Pending' WHERE u_id = ?";
    $update_user_stmt = $mysqli->prepare($update_user);
    if (!$update_user_stmt) {
        die("Error preparing user update: " . $mysqli->error);
    }
    $update_user_stmt->bind_param('ssi', $booking_info, $booking_date, $aid);
    if (!$update_user_stmt->execute()) {
        die("Error updating user: " . $update_user_stmt->error);
    }
    
    // Insert into tms_service_booking table (with default price 0)
    $default_price = 0;
    $insert_query = "INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_pincode, sb_phone, sb_status, sb_total_price) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param('iisssssd', $aid, $service_id, $booking_date, $booking_time, $address, $pincode, $phone, $default_price);
    
    if ($insert_stmt->execute()) {
        $_SESSION['booking_success'] = true;
        $redirect_url = "confirm-booking.php?success=1&service_name=" . urlencode($service_name) . 
                       "&duration=" . urlencode($duration) . 
                       "&category=" . urlencode($category) . 
                       "&subcategory=" . urlencode($subcategory);
        header("Location: " . $redirect_url);
        exit();
    } else {
        $error_msg = "Booking failed. Please try again. Error: " . $mysqli->error;
    }
    } // Close booking limit check
}

// Check if booking was successful
$show_success = isset($_GET['success']) && $_GET['success'] == 1 && isset($_SESSION['booking_success']);
if ($show_success) {
    unset($_SESSION['booking_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-top: 75px;
            padding-bottom: 80px;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 10px 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.3);
            z-index: 1000;
        }
        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-left: 0;
            margin-left: -5px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .logo {
            height: 55px;
            width: auto;
        }
        
        .brand-text h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 13px;
            opacity: 0.85;
            margin: 3px 0 0 0;
            font-style: italic;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }
        
        .header-icons {
            display: flex;
            gap: 6px;
        }
        
        .header-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        .content { 
            padding: 15px; 
            padding-bottom: 25px; 
            max-width: 600px; 
            margin: 0 auto; 
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-title i {
            color: #d13abd;
        }
        
        .service-info {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
            border: 1px solid rgba(236, 110, 173, 0.1);
        }
        
        .service-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffe8f0;
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3);
        }
        
        .service-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .service-detail {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .service-detail i {
            color: #d13abd;
            width: 20px;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
            border: 1px solid rgba(236, 110, 173, 0.1);
        }
        
        .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-title i {
            color: #d13abd;
        }
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label i {
            color: #d13abd;
            font-size: 16px;
        }
        
        .form-label .required {
            color: #ef4444;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #d13abd;
            background: #fff5f7;
            box-shadow: 0 0 0 3px rgba(209, 58, 189, 0.1);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(209, 58, 189, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert i {
            font-size: 20px;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        
        .info-box {
            background: linear-gradient(135deg, #ffe8f0 0%, #ffd6e8 100%);
            border-left: 4px solid #d13abd;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
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
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .success-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .success-content {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .success-message {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 16px);
            max-width: 450px;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            box-shadow: 0 3px 20px rgba(209, 58, 189, 0.35), 0 1px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 4px 6px;
            z-index: 1000;
            border-radius: 20px;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 4px 2px;
            position: relative;
            border-radius: 12px;
        }
        
        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }
        
        .nav-item.active { 
            color: white;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
        }
        
        .nav-item i {
            font-size: 16px;
            display: block;
            margin-bottom: 1px;
        }
        
        .nav-item.active i {
            animation: bounce 0.4s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        
        .nav-item span {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }
        
        @media (min-width: 768px) {
            .bottom-nav {
                max-width: 400px;
                bottom: 10px;
                padding: 5px 8px;
            }
            
            .nav-item {
                padding: 5px 4px;
            }
            
            .nav-item i {
                font-size: 18px;
                margin-bottom: 2px;
            }
            
            .nav-item span {
                font-size: 9px;
            }
        }
        
        @media (min-width: 768px) {
            body {
                max-width: 1200px;
                margin: 0 auto;
                box-shadow: 0 0 40px rgba(0,0,0,0.15);
            }
            
            .header {
                border-radius: 0;
            }
            
            .content {
                padding: 30px 50px;
                padding-bottom: 100px;
                max-width: 100%;
            }
            
            .service-info {
                padding: 30px;
            }
            
            .form-card {
                padding: 30px;
            }
            
            .service-name {
                font-size: 22px;
            }
            
            .service-detail {
                font-size: 15px;
            }
            
            .form-control {
                padding: 15px;
                font-size: 15px;
            }
            
            .btn-submit {
                padding: 18px;
                font-size: 18px;
            }
        }
        
        @media (min-width: 1024px) {
            body {
                max-width: 1400px;
            }
            
            .content {
                padding: 40px 80px;
                padding-bottom: 100px;
            }
            
            .service-info {
                padding: 35px;
            }
            
            .form-card {
                padding: 35px;
            }
        }
        
        @media (min-width: 1440px) {
            body {
                max-width: 1600px;
            }
            
            .content {
                padding: 50px 100px;
                padding-bottom: 100px;
            }
        }
    </style>
</head>
<body>
    <?php if ($show_success): ?>
    <div class="success-modal">
        <div class="success-content">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="success-title">Booking Confirmed!</div>
            <div class="success-message">Your service booking has been confirmed. We'll contact you soon.</div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'user-dashboard.php';
        }, 3000);
    </script>
    <?php else: ?>
    
    <div class="header">
        <div class="header-content">
            <div class="brand-section">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </div>
            <div class="user-section">
                <div class="header-icons">
                    <a href="user-view-profile.php" class="header-icon">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <?php if (isset($error_msg)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $error_msg; ?></span>
        </div>
        <?php endif; ?>

        <div class="page-title">
            <i class="fas fa-check-circle"></i>
            Confirm Your Booking
        </div>
        
        <div class="info-box">
            <p><i class="fas fa-info-circle"></i> Please review your service details and provide your contact information to complete the booking.</p>
        </div>
        
        <div class="service-info">
            <div class="service-header">
                <div class="service-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <div class="service-name"><?php echo htmlspecialchars($service_name); ?></div>
                    <div class="service-detail">
                        <i class="fas fa-tag"></i> 
                        <?php echo htmlspecialchars($category); ?> › <?php echo htmlspecialchars($subcategory); ?>
                    </div>
                </div>
            </div>
            <div class="service-detail"><i class="far fa-clock"></i> Duration: <?php echo htmlspecialchars($duration); ?></div>
        </div>

        <div class="form-card">
            <div class="form-title">
                <i class="fas fa-edit"></i>
                Enter Booking Details
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?>" readonly style="background: #f9fafb; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i>
                        Phone Number <span class="required">*</span>
                    </label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user->u_phone); ?>" placeholder="Enter your phone number" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-pin"></i>
                        Pincode <span class="required">*</span>
                    </label>
                    <input type="text" name="pincode" class="form-control" placeholder="Enter 6-digit pincode" pattern="[0-9]{6}" maxlength="6" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Complete Address <span class="required">*</span>
                    </label>
                    <textarea name="address" class="form-control" placeholder="House/Flat No., Street, Area, Landmark" required><?php echo htmlspecialchars($user->u_addr); ?></textarea>
                </div>

                <button type="submit" name="confirm_booking" class="btn-submit">
                    <i class="fas fa-check-circle"></i> 
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service-step1.php" class="nav-item active">
            <i class="fas fa-calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item">
            <i class="fas fa-list-alt"></i>
            <span>Orders</span>
        </a>
        <a href="user-view-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <a href="../index.php" class="nav-item">
            <i class="fas fa-store"></i>
            <span>Main</span>
        </a>
    </div>
</body>
</html>
