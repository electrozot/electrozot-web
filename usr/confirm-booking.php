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
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 20px 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }
        .logo {
            height: 35px;
            width: auto;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .back-btn {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        
        .brand-text h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 10px;
            opacity: 0.85;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        
        .page-title {
            font-size: 16px;
            font-weight: 600;
            text-align: right;
        }
        .content { padding: 15px; max-width: 600px; margin: 0 auto; }
        .service-info {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .service-name {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .service-detail {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
                <a href="javascript:history.back()" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </div>
            <div class="page-title">Confirm</div>
        </div>
    </div>

    <div class="content">
        <?php if (isset($error_msg)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
        </div>
        <?php endif; ?>

        <div class="service-info">
            <div class="service-name"><?php echo htmlspecialchars($service_name); ?></div>
            <div class="service-detail"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($category); ?> > <?php echo htmlspecialchars($subcategory); ?></div>
            <div class="service-detail"><i class="far fa-clock"></i> Duration: <?php echo htmlspecialchars($duration); ?></div>
        </div>

        <div class="form-card">
            <div class="form-title">Enter Booking Details</div>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user->u_phone); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Pincode *</label>
                    <input type="text" name="pincode" class="form-control" placeholder="Enter 6-digit pincode" pattern="[0-9]{6}" maxlength="6" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Complete Address *</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="House/Flat No., Street, Area, Landmark" required><?php echo htmlspecialchars($user->u_addr); ?></textarea>
                </div>

                <button type="submit" name="confirm_booking" class="btn-submit">
                    <i class="fas fa-check-circle"></i> Confirm Booking
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
