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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 75px;
            padding-bottom: 70px;
            min-height: 100vh;
        }
        
        .top-header {
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
            padding: 15px 15px 12px;
        }
        
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 6px;
            color: #d13abd;
            font-size: 16px;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        
        .action-item {
            background: white;
            border-radius: 14px;
            padding: 15px 8px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(209, 58, 189, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(236, 110, 173, 0.08);
        }
        
        .action-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.2);
            border-color: rgba(236, 110, 173, 0.3);
        }
        
        .action-item:active {
            transform: translateY(0) scale(0.98);
        }
        
        .action-icon {
            width: 45px;
            height: 45px;
            margin: 0 auto 8px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }
        
        .action-label {
            font-size: 11px;
            color: #333;
            font-weight: 600;
            line-height: 1.2;
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
        
        /* Tablet & Desktop Responsive */
        @media (min-width: 768px) {
            body {
                background: #f0f2f5;
            }
            
            .quick-actions {
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .quick-actions {
                padding: 0 20px 15px;
            }
            
            .section-title {
                font-size: 17px;
                margin-bottom: 12px;
            }
            
            .action-grid {
                grid-template-columns: repeat(6, 1fr);
                gap: 12px;
            }
            
            .action-item {
                padding: 18px 10px;
            }
            
            .action-icon {
                width: 50px;
                height: 50px;
                font-size: 22px;
                margin-bottom: 10px;
            }
            
            .action-label {
                font-size: 12px;
            }
            
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
        
        /* Large Desktop */
        @media (min-width: 1200px) {
            .action-grid {
                gap: 15px;
            }
        }
        
        .bg-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .bg-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .bg-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .bg-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
        .bg-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .bg-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .action-item {
            animation: fadeInUp 0.3s ease-out;
            animation-fill-mode: both;
        }
        
        .action-item:nth-child(1) { animation-delay: 0.02s; }
        .action-item:nth-child(2) { animation-delay: 0.04s; }
        .action-item:nth-child(3) { animation-delay: 0.06s; }
        .action-item:nth-child(4) { animation-delay: 0.08s; }
        .action-item:nth-child(5) { animation-delay: 0.10s; }
        .action-item:nth-child(6) { animation-delay: 0.12s; }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <div class="top-header">
        <div class="header-content">
            <a href="../index.php" class="brand-section" style="text-decoration: none; color: white;">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </a>
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

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="section-title">
            <i class="fas fa-bolt"></i> Quick Actions
        </div>
        <div class="action-grid" style="grid-template-columns: repeat(4, 1fr);">
            <a href="book-service-step1.php" class="action-item">
                <div class="action-icon bg-indigo">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="action-label">Book</div>
            </a>
            
            <a href="user-manage-booking.php" class="action-item">
                <div class="action-icon bg-pink">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="action-label">Orders</div>
            </a>
            
            <a href="user-track-booking.php" class="action-item">
                <div class="action-icon bg-green">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="action-label">Track</div>
            </a>
            
            <a href="user-view-profile.php" class="action-item">
                <div class="action-icon bg-purple">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="action-label">Profile</div>
            </a>
        </div>
    </div>

    <!-- Services Grid - Browse by Category -->
    <div class="quick-actions">
        <div class="section-title">
            <i class="fas fa-th-large"></i> Browse Services
        </div>
        <div class="action-grid">
            <a href="book-service-step2.php?category=<?php echo urlencode('Basic Electrical Work'); ?>" class="action-item">
                <div class="action-icon bg-blue">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="action-label">Electrical</div>
            </a>
            
            <a href="book-service-step2.php?category=<?php echo urlencode('Electronic Repair'); ?>" class="action-item">
                <div class="action-icon bg-purple">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="action-label">Repair</div>
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
                <div class="action-label">Plumbing</div>
            </a>
            
            <a href="book-custom-service.php" class="action-item">
                <div class="action-icon bg-teal">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-label">Custom</div>
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
        <a href="../index.php" class="nav-item">
            <i class="fas fa-store"></i>
            <span>Main</span>
        </a>
    </div>

    <script>
    // Live dashboard updates
    function updateDashboardStats() {
        fetch('api-dashboard-stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stats if elements exist
                    const pendingEl = document.querySelector('[data-stat="pending"]');
                    const approvedEl = document.querySelector('[data-stat="approved"]');
                    const completedEl = document.querySelector('[data-stat="completed"]');
                    const totalEl = document.querySelector('[data-stat="total"]');
                    
                    if (pendingEl) pendingEl.textContent = data.stats.pending;
                    if (approvedEl) approvedEl.textContent = data.stats.approved;
                    if (completedEl) completedEl.textContent = data.stats.completed;
                    if (totalEl) totalEl.textContent = data.stats.total;
                }
            })
            .catch(error => console.error('Error updating dashboard:', error));
    }
    
    // Update every 10 seconds
    setInterval(updateDashboardStats, 10000);
    </script>

</body>
</html>
