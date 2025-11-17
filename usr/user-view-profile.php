<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>My Profile - Electrozot</title>
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
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
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
            margin-right: 15px;
        }
        
        .brand-section {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .brand-logo {
            height: 40px;
            width: auto;
            margin-right: 12px;
        }
        
        .brand-text {
            display: flex;
            flex-direction: column;
        }
        
        .brand-name {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .brand-tagline {
            font-size: 11px;
            font-weight: 400;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }
        
        .content {
            padding: 0 15px 15px;
        }
        
        .profile-header {
            background: white;
            border-radius: 0 0 30px 30px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            margin: -15px 0 20px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            color: white;
            font-weight: 700;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .profile-email {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #6366f1;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 25px 0 12px;
        }
        
        .info-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            overflow: hidden;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 12px;
            color: #999;
            margin-bottom: 3px;
        }
        
        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }
        
        .action-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            overflow: hidden;
        }
        
        .action-item {
            display: flex;
            align-items: center;
            padding: 15px;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }
        
        .action-item:last-child {
            border-bottom: none;
        }
        
        .action-item:active {
            background: #f9fafb;
        }
        
        .action-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .action-icon.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .action-icon.purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        
        .action-icon.red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .action-content {
            flex: 1;
        }
        
        .action-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }
        
        .action-desc {
            font-size: 12px;
            color: #999;
        }
        
        .action-arrow {
            color: #d1d5db;
            font-size: 18px;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 15px;
            border-radius: 15px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            margin-top: 20px;
        }
        
        .logout-btn i {
            margin-right: 8px;
        }
        
        .logout-btn:active {
            transform: scale(0.98);
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
        <a href="user-dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="brand-section">
            <img src="../vendor/EZlogonew.png" alt="Electrozot" class="brand-logo">
            <div class="brand-text">
                <div class="brand-name">Electrozot</div>
                <div class="brand-tagline">We Make Perfect</div>
            </div>
        </div>
        <div style="width: 40px;"></div>
    </div>

    <div class="profile-header">
        <div class="profile-avatar">
            <?php 
            $initials = strtoupper(substr($user->u_fname, 0, 1) . substr($user->u_lname, 0, 1));
            echo $initials;
            ?>
        </div>
        <div class="profile-name"><?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?></div>
        <div class="profile-email">
            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user->u_email); ?>
        </div>
        
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-value">
                    <?php 
                    // Count bookings
                    $booking_count = (!empty($user->t_tech_category) && !empty($user->t_booking_date)) ? 1 : 0;
                    echo $booking_count;
                    ?>
                </div>
                <div class="stat-label">Bookings</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">
                    <?php 
                    $status = $user->t_booking_status ?? 'None';
                    if ($status == 'Completed') {
                        echo '1';
                    } else {
                        echo '0';
                    }
                    ?>
                </div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">
                    <?php 
                    if ($status == 'Pending' || $status == 'Confirmed' || $status == 'In Progress') {
                        echo '1';
                    } else {
                        echo '0';
                    }
                    ?>
                </div>
                <div class="stat-label">Active</div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="section-title">Personal Information</div>
        
        <div class="info-card">
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($user->u_email); ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($user->u_phone); ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($user->u_addr ?: 'Not provided'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="section-title">Account Settings</div>
        
        <div class="action-card">
            <a href="user-update-profile.php" class="action-item">
                <div class="action-icon blue">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Edit Profile</div>
                    <div class="action-desc">Update your personal information</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
            
            <a href="user-change-pwd.php" class="action-item">
                <div class="action-icon purple">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="action-content">
                    <div class="action-title">Change Password</div>
                    <div class="action-desc">Update your account password</div>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
        
        <a href="user-logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?');">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item">
            <i class="fas fa-clipboard-list"></i>
            <span>Bookings</span>
        </a>
        <a href="user-track-booking.php" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Track</span>
        </a>
        <a href="user-view-profile.php" class="nav-item active">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
</body>
</html>
