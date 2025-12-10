<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Ensure booking hold system tables and columns exist
try {
    // Add booking hold system columns
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_on_hold TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_reason TEXT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_start_date TIMESTAMP NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_end_date TIMESTAMP NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_high_priority TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_priority_reason VARCHAR(255) NULL");
    
    // Create booking hold requests table
    $create_hold_table = "CREATE TABLE IF NOT EXISTS tms_booking_hold_requests (
        bhr_id INT AUTO_INCREMENT PRIMARY KEY,
        bhr_booking_id INT NOT NULL,
        bhr_technician_id INT NOT NULL,
        bhr_reason TEXT NOT NULL,
        bhr_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        bhr_requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        bhr_responded_at TIMESTAMP NULL,
        bhr_customer_response TEXT NULL,
        INDEX(bhr_booking_id),
        INDEX(bhr_technician_id),
        INDEX(bhr_status)
    )";
    $mysqli->query($create_hold_table);
    
    // Create customer notifications table
    $create_customer_notif = "CREATE TABLE IF NOT EXISTS tms_customer_notifications (
        cn_id INT AUTO_INCREMENT PRIMARY KEY,
        cn_user_id INT NOT NULL,
        cn_booking_id INT NOT NULL,
        cn_type VARCHAR(50) NOT NULL,
        cn_title VARCHAR(255) NOT NULL,
        cn_message TEXT NOT NULL,
        cn_is_read TINYINT(1) DEFAULT 0,
        cn_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        cn_action_required TINYINT(1) DEFAULT 0,
        cn_action_url VARCHAR(255) NULL,
        INDEX(cn_user_id),
        INDEX(cn_booking_id),
        INDEX(cn_is_read)
    )";
    $mysqli->query($create_customer_notif);
    
    // Create technician notifications table
    $create_tech_notif = "CREATE TABLE IF NOT EXISTS tms_technician_notifications (
        tn_id INT AUTO_INCREMENT PRIMARY KEY,
        tn_technician_id INT NOT NULL,
        tn_booking_id INT NOT NULL,
        tn_type VARCHAR(50) NOT NULL,
        tn_title VARCHAR(255) NOT NULL,
        tn_message TEXT NOT NULL,
        tn_is_read TINYINT(1) DEFAULT 0,
        tn_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(tn_technician_id),
        INDEX(tn_booking_id),
        INDEX(tn_is_read)
    )";
    $mysqli->query($create_tech_notif);
} catch(Exception $e) {}

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

// Get pending hold requests for this customer
$hold_requests_query = "SELECT bhr.*, sb.sb_id, t.t_name, t.t_phone, s.s_name
                        FROM tms_booking_hold_requests bhr
                        LEFT JOIN tms_service_booking sb ON bhr.bhr_booking_id = sb.sb_id
                        LEFT JOIN tms_technician t ON bhr.bhr_technician_id = t.t_id
                        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                        WHERE sb.sb_user_id = ? AND bhr.bhr_status = 'Pending'
                        ORDER BY bhr.bhr_requested_at DESC";
$hold_stmt = $mysqli->prepare($hold_requests_query);
$hold_stmt->bind_param('i', $aid);
$hold_stmt->execute();
$hold_requests_result = $hold_stmt->get_result();
$pending_hold_count = $hold_requests_result->num_rows;

// Get active bookings only (exclude completed): On Hold → New/In Progress → Rejected
$all_bookings_query = "SELECT sb.*, s.s_name, t.t_name, sb.sb_hold_reason, sb.sb_hold_end_date,
                       CASE 
                           WHEN sb.sb_is_on_hold = 1 THEN 1
                           WHEN sb.sb_status IN ('Pending', 'Approved', 'In Progress', 'On Hold') THEN 2
                           WHEN sb.sb_status IN ('Rejected', 'Not Done', 'Cancelled') THEN 3
                           ELSE 4
                       END as sort_order
                       FROM tms_service_booking sb
                       LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                       LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                       WHERE sb.sb_user_id = ? 
                       AND sb.sb_status NOT IN ('Completed')
                       ORDER BY sort_order ASC, sb.sb_created_at DESC
                       LIMIT 20";
$all_bookings_stmt = $mysqli->prepare($all_bookings_query);
$all_bookings_stmt->bind_param('i', $aid);
$all_bookings_stmt->execute();
$all_bookings_result = $all_bookings_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Dashboard - Electrozot</title>
    
    <!-- PWA Meta Tags for Fullscreen -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        /* Hide browser loading bars in PWA */
        ::-webkit-progress-bar,
        ::-webkit-progress-value {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
        
        /* Hide Android Chrome loading bar */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: transparent !important;
            z-index: 9999;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 75px;
            padding-bottom: 70px;
            min-height: 100vh;
            -webkit-tap-highlight-color: transparent;
        }
        
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 10px 15px;
            padding-top: calc(10px + env(safe-area-inset-top));
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
            height: 29px;
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

    <!-- Pending Hold Requests Alert -->
    <?php if($pending_hold_count > 0): ?>
    <div style="margin: 15px; padding: 0; background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(255, 165, 2, 0.3); border: 3px solid #ffa502; overflow: hidden; animation: pulse 2s infinite;">
        <div style="background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%); padding: 12px 15px; color: white;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 24px; animation: shake 1s infinite;"></i>
                <div>
                    <div style="font-weight: 700; font-size: 16px;">⚠️ Action Required!</div>
                    <div style="font-size: 13px; opacity: 0.95;">
                        You have <?php echo $pending_hold_count; ?> pending hold request<?php echo $pending_hold_count > 1 ? 's' : ''; ?> from technician
                    </div>
                </div>
            </div>
        </div>
        
        <div style="padding: 15px;">
            <?php 
            $hold_requests_result->data_seek(0);
            while($hold_req = $hold_requests_result->fetch_object()): 
            ?>
            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 12px; padding: 12px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; color: #856404; font-size: 14px; margin-bottom: 4px;">
                            <i class="fas fa-tools"></i> #<?php echo $hold_req->bhr_booking_id; ?> - <?php echo htmlspecialchars($hold_req->s_name); ?>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">
                            <?php echo htmlspecialchars($hold_req->bhr_reason); ?>
                        </div>
                        <div style="font-size: 11px; color: #999;">
                            <i class="fas fa-user-cog"></i> <?php echo htmlspecialchars($hold_req->t_name); ?>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <button onclick="respondHoldRequest(<?php echo $hold_req->bhr_id; ?>, 'approve')" 
                           style="background: linear-gradient(135deg, #00c853 0%, #00F260 100%); color: white; padding: 12px 20px; border-radius: 10px; border: none; font-weight: 700; font-size: 14px; cursor: pointer; white-space: nowrap; box-shadow: 0 3px 10px rgba(0, 200, 83, 0.4);">
                            <i class="fas fa-check-circle"></i> Approve Hold
                        </button>
                        <button onclick="respondHoldRequest(<?php echo $hold_req->bhr_id; ?>, 'reject')" 
                           style="background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%); color: white; padding: 12px 20px; border-radius: 10px; border: none; font-weight: 700; font-size: 14px; cursor: pointer; white-space: nowrap; box-shadow: 0 3px 10px rgba(255, 71, 87, 0.4);">
                            <i class="fas fa-times-circle"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <style>
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
    </style>
    <?php endif; ?>

    <!-- Active Bookings Button -->
    <?php if($all_bookings_result->num_rows > 0): ?>
    <div style="margin: 15px; padding: 0;">
        <button onclick="toggleBookings()" style="width: 100%; background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%); color: white; padding: 15px; border-radius: 15px; border: none; font-weight: 700; font-size: 16px; cursor: pointer; box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3); display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fas fa-list-alt"></i> Active Bookings (<?php echo $all_bookings_result->num_rows; ?>)</span>
            <i class="fas fa-chevron-down" id="bookingsChevron"></i>
        </button>
        
        <div id="bookingsContainer" style="display: none; margin-top: 10px;">
            <?php 
            $all_bookings_result->data_seek(0);
            while($booking = $all_bookings_result->fetch_object()): 
            ?>
            <div style="display: block; background: white; border-radius: 12px; padding: 12px; margin-bottom: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid <?php 
                if($booking->sb_is_on_hold == 1) echo '#ffa502';
                elseif($booking->sb_status == 'Completed') echo '#10b981';
                elseif($booking->sb_status == 'Pending') echo '#ffd700';
                else echo '#0ea5e9';
            ?>; <?php echo $booking->sb_is_on_hold == 1 ? 'background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);' : ''; ?>">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                    <a href="user-track-booking.php?id=<?php echo $booking->sb_id; ?>" style="flex: 1; text-decoration: none;">
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px; flex-wrap: wrap;">
                            <span style="font-weight: 700; color: #333; font-size: 14px;">
                                #<?php echo str_pad($booking->sb_id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                            
                            <?php if($booking->sb_is_on_hold == 1): ?>
                            <span style="background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%); color: white; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: 700;">
                                <i class="fas fa-pause-circle"></i> ON HOLD
                            </span>
                            <?php elseif($booking->sb_is_high_priority == 1): ?>
                            <span style="background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%); color: white; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: 700;">
                                <i class="fas fa-fire"></i> PRIORITY
                            </span>
                            <?php endif; ?>
                            
                            <span style="background: <?php 
                                if($booking->sb_status == 'Completed') echo '#d4edda';
                                elseif($booking->sb_status == 'Pending') echo '#fff3cd';
                                elseif($booking->sb_status == 'In Progress') echo '#cfe2ff';
                                else echo '#f8f9fa';
                            ?>; color: <?php 
                                if($booking->sb_status == 'Completed') echo '#155724';
                                elseif($booking->sb_status == 'Pending') echo '#856404';
                                elseif($booking->sb_status == 'In Progress') echo '#084298';
                                else echo '#666';
                            ?>; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: 700;">
                                <?php echo $booking->sb_status; ?>
                            </span>
                        </div>
                        
                        <div style="color: #333; font-weight: 600; font-size: 13px; margin-bottom: 3px;">
                            <i class="fas fa-wrench"></i> <?php echo htmlspecialchars($booking->s_name); ?>
                        </div>
                        
                        <div style="color: #666; font-size: 11px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?></span>
                            <?php if(!empty($booking->t_name)): ?>
                            <span><i class="fas fa-user-cog"></i> <?php echo htmlspecialchars($booking->t_name); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($booking->sb_is_on_hold == 1 && !empty($booking->sb_hold_reason)): ?>
                        <div style="color: #856404; font-size: 10px; margin-top: 3px; background: #fff3cd; padding: 3px 6px; border-radius: 4px; display: inline-block;">
                            <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars(substr($booking->sb_hold_reason, 0, 40)); ?><?php echo strlen($booking->sb_hold_reason) > 40 ? '...' : ''; ?>
                        </div>
                        <?php endif; ?>
                    </a>
                    
                    <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                        <?php if($booking->sb_is_on_hold == 1): ?>
                        <button onclick="event.stopPropagation(); unholdBookingDashboard(<?php echo $booking->sb_id; ?>)" 
                           style="background: linear-gradient(135deg, #00c853 0%, #00F260 100%); color: white; padding: 8px 14px; border-radius: 8px; border: none; font-weight: 700; font-size: 11px; cursor: pointer; box-shadow: 0 2px 8px rgba(0, 200, 83, 0.3); white-space: nowrap;">
                            <i class="fas fa-play-circle"></i> Resume
                        </button>
                        <?php else: ?>
                        <i class="fas fa-chevron-right" style="color: #d13abd; font-size: 14px;"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

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
    // Toggle bookings list
    function toggleBookings() {
        const container = document.getElementById('bookingsContainer');
        const chevron = document.getElementById('bookingsChevron');
        
        if(container.style.display === 'none') {
            container.style.display = 'block';
            chevron.classList.remove('fa-chevron-down');
            chevron.classList.add('fa-chevron-up');
        } else {
            container.style.display = 'none';
            chevron.classList.remove('fa-chevron-up');
            chevron.classList.add('fa-chevron-down');
        }
    }
    
    // Respond to hold request - Single click
    function respondHoldRequest(requestId, action) {
        const actionText = action === 'approve' ? 'approve' : 'reject';
        const message = action === 'approve' 
            ? 'Approve this hold request?\n\n✓ Booking will be on hold for up to 4 days\n✓ You can resume it anytime\n✓ Technician will be notified'
            : 'Reject this hold request?\n\n✓ Booking will continue normally\n✓ Technician will be notified';
        
        if(confirm(message)) {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('api-respond-hold.php?id=' + requestId + '&action=' + action, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('✅ Hold request ' + actionText + 'd successfully!');
                    location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to respond'));
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                alert('❌ Error: Could not respond. Please try again.');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        }
    }
    
    // Unhold/Resume booking from dashboard - Single click
    function unholdBookingDashboard(bookingId) {
        if(confirm('Resume this booking?\n\n✓ Booking will be marked as HIGH PRIORITY\n✓ Technician will be notified immediately\n✓ Service will continue\n\nClick OK to resume now.')) {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('api-unhold-booking.php?id=' + bookingId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    alert('✅ Booking resumed successfully!\n\nYour booking is now HIGH PRIORITY.\nTechnician has been notified.');
                    location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to resume booking'));
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Unhold error:', error);
                alert('❌ Error: Could not resume booking. Please try again.\n\nDetails: ' + error.message);
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        }
    }
    
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

    <!-- Customer Notification System - DISABLED -->
    <?php // include('vendor/inc/customer-notification-system.php'); ?>
    <?php // <script src="js/customer-notifications.js"></script> ?>

</body>
</html>
