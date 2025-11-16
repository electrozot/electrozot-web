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

// Get booking stats
$booking_query = "SELECT COUNT(*) as total FROM tms_service_booking WHERE sb_user_id = ?";
$booking_stmt = $mysqli->prepare($booking_query);
$booking_stmt->bind_param('i', $aid);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();
$booking_stats = $booking_result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            padding-bottom: 70px;
        }
        
        /* Top Header - Android Style */
        .top-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .user-info p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .header-icon {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        /* Stats Cards */
        .stats-container {
            padding: 15px;
            margin-top: -30px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 15px;
        }
        
        .stats-row {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .stat-divider {
            width: 1px;
            background: #e0e0e0;
        }
        
        /* Quick Actions Grid */
        .quick-actions {
            padding: 0 15px 15px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 8px;
            color: #667eea;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        
        .action-item {
            background: white;
            border-radius: 12px;
            padding: 20px 10px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s;
        }
        
        .action-item:active {
            transform: scale(0.95);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .action-label {
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }
        
        /* Recent Activity */
        .recent-activity {
            padding: 0 15px 15px;
        }
        
        .activity-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .activity-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
            color: white;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        
        .activity-subtitle {
            font-size: 12px;
            color: #999;
        }
        
        .activity-arrow {
            color: #ccc;
            font-size: 18px;
        }
        
        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0 8px;
            z-index: 1000;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: #999;
            transition: all 0.3s;
        }
        
        .nav-item.active {
            color: #667eea;
        }
        
        .nav-item i {
            font-size: 22px;
            display: block;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 500;
        }
        
        /* Desktop View */
        @media (min-width: 768px) {
            body {
                max-width: 480px;
                margin: 0 auto;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
        }
        
        /* Color Schemes */
        .bg-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .bg-orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .bg-teal { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="header-content">
            <div class="user-info">
                <h3>Hello, <?php echo htmlspecialchars($user->u_fname); ?>! 👋</h3>
                <p>Welcome back to Electrozot</p>
            </div>
            <a href="user-view-profile.php" class="header-icon">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="stats-container">
        <div class="stats-card">
            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $booking_stats->total; ?></span>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">4.8</span>
                    <div class="stat-label">Rating</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <div class="stat-label">Support</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="section-title">
            <i class="fas fa-bolt"></i> Quick Actions
        </div>
        <div class="action-grid">
            <a href="book-service.php" class="action-item">
                <div class="action-icon bg-blue">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="action-label">Book Service</div>
            </a>
            
            <a href="user-view-booking.php" class="action-item">
                <div class="action-icon bg-pink">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="action-label">My Orders</div>
            </a>
            
            <a href="user-track-booking.php" class="action-item">
                <div class="action-icon bg-green">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="action-label">Track</div>
            </a>
            
            <a href="user-give-feedback.php" class="action-item">
                <div class="action-icon bg-orange">
                    <i class="fas fa-star"></i>
                </div>
                <div class="action-label">Feedback</div>
            </a>
            
            <a href="user-view-profile.php" class="action-item">
                <div class="action-icon bg-purple">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="action-label">Profile</div>
            </a>
            
            <a href="user-change-pwd.php" class="action-item">
                <div class="action-icon bg-teal">
                    <i class="fas fa-key"></i>
                </div>
                <div class="action-label">Security</div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <div class="section-title">
            <i class="fas fa-clock"></i> Recent Activity
        </div>
        
        <a href="user-view-booking.php" class="activity-card">
            <div class="activity-icon bg-blue">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">View All Bookings</div>
                <div class="activity-subtitle">Check your service history</div>
            </div>
            <i class="fas fa-chevron-right activity-arrow"></i>
        </a>
        
        <a href="book-service.php" class="activity-card">
            <div class="activity-icon bg-green">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">Book New Service</div>
                <div class="activity-subtitle">Browse 75+ services</div>
            </div>
            <i class="fas fa-chevron-right activity-arrow"></i>
        </a>
        
        <a href="user-track-booking.php" class="activity-card">
            <div class="activity-icon bg-orange">
                <i class="fas fa-route"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">Track Service</div>
                <div class="activity-subtitle">Real-time tracking</div>
            </div>
            <i class="fas fa-chevron-right activity-arrow"></i>
        </a>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="user-dashboard-android.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service.php" class="nav-item">
            <i class="fas fa-calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="user-view-booking.php" class="nav-item">
            <i class="fas fa-list-alt"></i>
            <span>Orders</span>
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
