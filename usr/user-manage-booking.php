<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
if (!$user_stmt) {
    die("Database error: " . $mysqli->error);
}
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();

if (!$user) {
    die("User not found. Please login again.");
}

// Check for cancel success/error
$cancel_success = isset($_GET['cancelled']) && $_GET['cancelled'] == 1;
$cancel_error = isset($_GET['error']) && $_GET['error'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>My Bookings - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .top-bar {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 15px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .logo {
            height: 45px;
            width: auto;
        }
        
        .brand-text h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 11px;
            opacity: 0.85;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        
        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .content {
            padding: 15px;
        }
        
        .booking-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-id {
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge {
            background: rgba(255,255,255,0.25);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .service-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .service-title i {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .info-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            margin-right: 12px;
            font-size: 14px;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-top: 2px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 15px;
            background: #f9fafb;
        }
        
        .btn {
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .btn-track {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }
        
        .btn-cancel {
            background: white;
            color: #ef4444;
            border: 2px solid #ef4444;
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        .empty-state {
            background: white;
            border-radius: 20px;
            padding: 50px 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            margin-top: 50px;
        }
        
        .empty-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #6366f1;
        }
        
        .empty-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .empty-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .btn-book {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 15px 35px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .btn-book i {
            margin-right: 8px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            z-index: 100;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: #999;
            padding: 8px;
            transition: all 0.3s;
        }
        
        .nav-item.active {
            color: #6366f1;
        }
        
        .nav-item i {
            font-size: 20px;
            display: block;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="brand-section">
            <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
            <div class="brand-text">
                <h2>Electrozot</h2>
                <p>We make perfect</p>
            </div>
        </div>
        <a href="user-dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <div class="content">
        <?php if ($cancel_success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Booking cancelled successfully!
        </div>
        <?php endif; ?>
        
        <?php if ($cancel_error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            Failed to cancel booking. Please try again.
        </div>
        <?php endif; ?>
        
        <?php
        if (!empty($user->t_tech_category) && !empty($user->t_booking_date)) {
            // Parse booking info
            $booking_parts = explode('|', $user->t_tech_category);
            $service_info = isset($booking_parts[0]) ? trim($booking_parts[0]) : 'Service Booking';
            
            // Extract service details
            $service_parts = explode('>', $service_info);
            $category = isset($service_parts[0]) ? trim($service_parts[0]) : '';
            $subcategory = isset($service_parts[1]) ? trim($service_parts[1]) : '';
            $service_name = isset($service_parts[2]) ? trim($service_parts[2]) : 'Service';
            
            $pincode = '';
            $address = '';
            $phone = '';
            
            for ($i = 1; $i < count($booking_parts); $i++) {
                if (strpos($booking_parts[$i], 'Pincode:') !== false) {
                    $pincode = trim(str_replace('Pincode:', '', $booking_parts[$i]));
                } elseif (strpos($booking_parts[$i], 'Address:') !== false) {
                    $address = trim(str_replace('Address:', '', $booking_parts[$i]));
                } elseif (strpos($booking_parts[$i], 'Phone:') !== false) {
                    $phone = trim(str_replace('Phone:', '', $booking_parts[$i]));
                }
            }
            
            $status = $user->t_booking_status ?? 'Pending';
        ?>
        
        <div class="booking-card">
            <div class="card-header">
                <div class="booking-id">
                    <i class="fas fa-receipt"></i> Booking #<?php echo str_pad($user->u_id, 5, '0', STR_PAD_LEFT); ?>
                </div>
                <div class="status-badge">
                    <?php echo $status; ?>
                </div>
            </div>
            
            <div class="card-body">
                <div class="service-title">
                    <i class="fas fa-tools"></i>
                    <span><?php echo htmlspecialchars($service_name); ?></span>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Customer</div>
                        <div class="info-value"><?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Contact</div>
                        <div class="info-value"><?php echo htmlspecialchars($phone ?: $user->u_phone); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Booking Date</div>
                        <div class="info-value"><?php echo date('d M Y', strtotime($user->t_booking_date)); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Pincode</div>
                        <div class="info-value"><?php echo htmlspecialchars($pincode ?: 'N/A'); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($address)): ?>
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($address); ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <a href="user-track-booking.php" class="btn btn-track">
                    <i class="fas fa-map-marker-alt"></i> Track
                </a>
                <?php if ($status != 'Cancelled' && $status != 'Completed'): ?>
                <a href="user-delete-booking.php?u_id=<?php echo $user->u_id; ?>" class="btn btn-cancel" onclick="return confirm('Cancel this booking?');">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php } else { ?>
        
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="empty-title">No Bookings Yet</div>
            <div class="empty-text">You haven't made any bookings yet.<br>Book your first service now!</div>
            <a href="book-service-step1.php" class="btn-book">
                <i class="fas fa-plus-circle"></i> Book Service
            </a>
        </div>
        
        <?php } ?>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item active">
            <i class="fas fa-clipboard-list"></i>
            <span>Bookings</span>
        </a>
        <a href="user-track-booking.php" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Track</span>
        </a>
        <a href="user-view-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
</body>
</html>
