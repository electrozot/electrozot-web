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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            padding-bottom: 70px;
        }
        
        .top-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 10px 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }
        
        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 15px;
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
        
        .user-name {
            font-size: 16px;
            font-weight: 600;
            white-space: nowrap;
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
            font-size: 14px;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
        }
        
        .header-icon:hover {
            background: rgba(255,255,255,0.35);
            transform: scale(1.05);
        }
        

        .quick-actions {
            padding: 0 15px 15px;
        }
        
        .section-title {
            font-size: 17px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 8px;
            color: #6366f1;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        
        .action-item {
            background: white;
            border-radius: 15px;
            padding: 20px 10px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
            transition: all 0.3s;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }
        
        .action-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.2);
        }
        
        .action-item:active {
            transform: scale(0.95);
        }
        
        .action-icon {
            width: 55px;
            height: 55px;
            margin: 0 auto 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
        }
        
        .action-label {
            font-size: 13px;
            color: #333;
            font-weight: 600;
        }
        
        .recent-activity {
            padding: 0 15px 15px;
        }
        
        .activity-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .activity-card:active {
            transform: scale(0.98);
        }
        
        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 15px;
            color: white;
        }
        
        .activity-content { flex: 1; }
        
        .activity-title {
            font-size: 15px;
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
        
        .bottom-nav {
            position: fixed;
            bottom: 8px;
            left: 8px;
            right: 8px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-around;
            padding: 6px 0;
            z-index: 1000;
            border-radius: 20px;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: #999;
            transition: all 0.3s;
            padding: 4px;
        }
        
        .nav-item.active { color: #667eea; }
        
        .nav-item i {
            font-size: 20px;
            display: block;
            margin-bottom: 3px;
        }
        
        .nav-item span {
            font-size: 10px;
            font-weight: 600;
        }
        
        /* Tablet & Desktop Responsive */
        @media (min-width: 768px) {
            body {
                background: #e9ecef;
            }
            
            .top-header {
                padding: 30px 20px 35px;
            }
            
            .logo {
                height: 55px;
            }
            
            .brand-text h2 {
                font-size: 24px;
            }
            
            .brand-text p {
                font-size: 13px;
            }
            
            .user-name {
                font-size: 18px;
            }
            
            .header-icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
            }
            

            .quick-actions,
            .recent-activity {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px 20px;
            }
            
            .section-title {
                font-size: 20px;
                margin-bottom: 20px;
            }
            
            .action-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
            
            .action-item {
                padding: 25px 15px;
            }
            
            .action-icon {
                width: 65px;
                height: 65px;
                font-size: 30px;
            }
            
            .action-label {
                font-size: 14px;
            }
            
            .activity-card {
                padding: 20px;
                margin-bottom: 15px;
            }
            
            .activity-icon {
                width: 60px;
                height: 60px;
                font-size: 26px;
            }
            
            .activity-title {
                font-size: 17px;
            }
            
            .activity-subtitle {
                font-size: 14px;
            }
            
            .bottom-nav {
                max-width: 1200px;
                left: 50%;
                transform: translateX(-50%);
                bottom: 10px;
                margin: 0 10px;
                border-radius: 20px;
            }
        }
        
        /* Large Desktop */
        @media (min-width: 1200px) {
            .action-grid {
                gap: 25px;
            }
            
            .action-item {
                padding: 30px 20px;
            }
            
            .action-icon {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }
        }
        
        .bg-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .bg-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .bg-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .bg-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
    </style>
</head>
<body>
    <div class="top-header">
        <div class="header-content">
            <div class="brand-section">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </div>
            <div class="user-section">
                <div class="user-name"><?php echo htmlspecialchars($user->u_fname); ?></div>
                <div class="header-icons">
                    <a href="user-view-profile.php" class="header-icon">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>



    <?php if(isset($_SESSION['linked_bookings']) && $_SESSION['linked_bookings'] > 0): ?>
    <div style="margin: 15px; padding: 15px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="font-size: 24px;"></i>
            <div>
                <div style="font-weight: 700; font-size: 16px; margin-bottom: 4px;">Welcome Back!</div>
                <div style="font-size: 13px; opacity: 0.95;">
                    We found <?php echo $_SESSION['linked_bookings']; ?> previous booking(s) and linked them to your account. 
                    <a href="user-manage-booking.php" style="color: white; text-decoration: underline; font-weight: 600;">View Now</a>
                </div>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['linked_bookings']); endif; ?>

    <div class="quick-actions">
        <div class="section-title">
            <i class="fas fa-bolt"></i> Quick Actions
        </div>
        <div class="action-grid">
            <a href="book-service-step1.php" class="action-item">
                <div class="action-icon bg-blue">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="action-label">Book Service</div>
            </a>
            
            <a href="user-manage-booking.php" class="action-item">
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
        </div>
    </div>

    <div class="quick-actions">
        <div class="section-title">
            <i class="fas fa-th-large"></i> Our Services
        </div>
        <div class="action-grid">
            <a href="book-service-step2.php?category=<?php echo urlencode('Basic Electrical Work'); ?>" class="action-item">
                <div class="action-icon bg-blue">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="action-label">Electrical Work</div>
            </a>
            
            <a href="book-service-step2.php?category=<?php echo urlencode('Electronic Repair'); ?>" class="action-item">
                <div class="action-icon bg-purple">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="action-label">Electronic Repair</div>
            </a>
            
            <a href="book-service-step2.php?category=<?php echo urlencode('Installation & Setup'); ?>" class="action-item">
                <div class="action-icon bg-pink">
                    <i class="fas fa-wrench"></i>
                </div>
                <div class="action-label">Installation</div>
            </a>
            
            <a href="book-service-step2.php?category=<?php echo urlencode('Servicing & Maintenance'); ?>" class="action-item">
                <div class="action-icon bg-green">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="action-label">Maintenance</div>
            </a>
            
            <a href="book-service-step2.php?category=<?php echo urlencode('Plumbing Work'); ?>" class="action-item">
                <div class="action-icon bg-orange">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="action-label">Plumbing Work</div>
            </a>
            
            <a href="book-custom-service.php" class="action-item">
                <div class="action-icon bg-teal">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-label">Other Service</div>
            </a>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service-step1.php" class="nav-item">
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
    </div>


</body>
</html>
