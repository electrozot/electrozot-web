<?php
// Include PWA session fix for production compatibility
include('pwa-session-fix.php');
configure_pwa_session();
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');
include('check-account-status.php'); // Check if account is locked

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "Technician Dashboard";

// Ensure columns and tables exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(20) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_email VARCHAR(100) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_addr TEXT DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_pincode VARCHAR(10) DEFAULT NULL");
    
    // Add booking hold system columns
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_on_hold TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_reason TEXT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_start_date TIMESTAMP NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_end_date TIMESTAMP NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_high_priority TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_priority_reason VARCHAR(255) NULL");
    
    // Create cancelled bookings table if not exists
    $create_cancelled_table = "CREATE TABLE IF NOT EXISTS tms_cancelled_bookings (
        cb_id INT AUTO_INCREMENT PRIMARY KEY,
        cb_booking_id INT NOT NULL,
        cb_technician_id INT NOT NULL,
        cb_cancelled_by VARCHAR(50) DEFAULT 'Admin',
        cb_reason VARCHAR(255) DEFAULT 'Technician reassigned by admin',
        cb_cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(cb_booking_id),
        INDEX(cb_technician_id)
    )";
    $mysqli->query($create_cancelled_table);
    
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

// Get technician details
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt_tech = $mysqli->prepare($tech_query);
$stmt_tech->bind_param('i', $t_id);
$stmt_tech->execute();
$tech_result = $stmt_tech->get_result();
$tech_data = $tech_result->fetch_object();

$t_phone = isset($tech_data->t_phone) ? $tech_data->t_phone : '';
$t_email = isset($tech_data->t_email) ? $tech_data->t_email : '';
$t_addr = isset($tech_data->t_addr) ? $tech_data->t_addr : '';
$t_service_pincode = isset($tech_data->t_service_pincode) ? $tech_data->t_service_pincode : '';

// Use service pincode if available, otherwise extract from address
$t_pincode = '';
if(!empty($t_service_pincode)) {
    $t_pincode = $t_service_pincode;
} elseif(!empty($t_addr)) {
    preg_match('/\b\d{6}\b/', $t_addr, $matches);
    if(!empty($matches)) {
        $t_pincode = $matches[0];
    }
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filter
$where_clause = "WHERE sb.sb_technician_id = ?";
$params = [$t_id];
$types = 'i';

if($filter == 'pending') {
    $where_clause .= " AND sb.sb_status = 'In Progress'";
} elseif($filter == 'completed') {
    // Completed filter shows only bookings completed today
    $where_clause .= " AND sb.sb_status = 'Completed' AND DATE(sb.sb_completed_at) = CURDATE()";
} elseif($filter == 'all') {
    // All filter shows only active bookings (exclude completed)
    $where_clause .= " AND sb.sb_status != 'Completed'";
}

if(!empty($search)) {
    $where_clause .= " AND (u.u_phone LIKE ? OR u.u_fname LIKE ? OR u.u_lname LIKE ? OR CONCAT(u.u_fname, ' ', u.u_lname) LIKE ? OR sb.sb_id LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sssss';
}

// Query to get only active bookings (exclude cancelled ones)
// Sort: High Priority first, then New bookings, then others, Completed at bottom
$bookings_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name,
                   CASE 
                       WHEN sb.sb_status = 'Pending' THEN 1
                       WHEN sb.sb_status = 'Approved' THEN 2
                       WHEN sb.sb_status = 'In Progress' THEN 3
                       WHEN sb.sb_status = 'On Hold' THEN 4
                       WHEN sb.sb_status = 'Not Done' THEN 5
                       WHEN sb.sb_status = 'Not Completed' THEN 6
                       WHEN sb.sb_status = 'Completed' THEN 7
                       ELSE 8
                   END as status_priority,
                   CASE 
                       WHEN sb.sb_is_high_priority = 1 AND sb.sb_status != 'Completed' THEN 0
                       ELSE 1
                   END as priority_order
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_cancelled_bookings cb ON sb.sb_id = cb.cb_booking_id AND cb.cb_technician_id = ?
                   {$where_clause}
                   AND cb.cb_id IS NULL
                   ORDER BY priority_order ASC, status_priority ASC, sb.sb_created_at DESC";

$stmt_bookings = $mysqli->prepare($bookings_query);

// Bind parameters: first for cancelled bookings join, then the WHERE clause params
$bind_params = array_merge([$t_id], $params);
$bind_types = 'i' . $types;

// Create references for bind_param
$bind_refs = [];
$bind_refs[] = &$bind_types;
foreach($bind_params as $key => $value) {
    $bind_refs[] = &$bind_params[$key];
}
call_user_func_array(array($stmt_bookings, 'bind_param'), $bind_refs);

$stmt_bookings->execute();
$bookings_result = $stmt_bookings->get_result();

// Get counts (excluding cancelled bookings)
$new_count = 0;
$pending_count = 0;
$completed_count = 0;
$all_active_count = 0;

$count_query = "SELECT 
                COUNT(CASE WHEN sb.sb_status = 'Pending' THEN 1 END) as new_count,
                COUNT(CASE WHEN sb.sb_status = 'In Progress' THEN 1 END) as pending_count,
                COUNT(CASE WHEN sb.sb_status = 'Completed' AND DATE(sb.sb_completed_at) = CURDATE() THEN 1 END) as completed_count,
                COUNT(CASE WHEN sb.sb_status != 'Completed' THEN 1 END) as all_active_count
                FROM tms_service_booking sb
                LEFT JOIN tms_cancelled_bookings cb ON sb.sb_id = cb.cb_booking_id AND cb.cb_technician_id = ?
                WHERE sb.sb_technician_id = ? AND cb.cb_id IS NULL";
$stmt_count = $mysqli->prepare($count_query);
$stmt_count->bind_param('ii', $t_id, $t_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$counts = $count_result->fetch_object();
$new_count = $counts->new_count;
$pending_count = $counts->pending_count;
$completed_count = $counts->completed_count;
$all_active_count = $counts->all_active_count;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Technician Dashboard - Electrozot</title>
    
    <!-- PWA Meta Tags for Fullscreen -->
    <meta name="application-name" content="Electrozot Technician">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EZ Tech">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="msapplication-tap-highlight" content="no">
    
    <!-- Favicon with Cache Busting -->
    <link rel="shortcut icon" href="../vendor/img/icons/favicon.ico?v=<?php echo time(); ?>" type="image/x-icon">
    <link rel="icon" href="../vendor/img/icons/favicon.ico?v=<?php echo time(); ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="../vendor/img/icons/favicon-16x16.png?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="../vendor/img/icons/favicon-32x32.png?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="../vendor/img/icons/favicon-96x96.png?v=<?php echo time(); ?>">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="57x57" href="../vendor/img/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../vendor/img/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../vendor/img/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../vendor/img/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../vendor/img/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../vendor/img/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../vendor/img/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../vendor/img/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../vendor/img/icons/apple-icon-180x180.png">
    
    <!-- Android Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="../vendor/img/icons/android-icon-192x192.png">
    
    <!-- MS Tile Icons -->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../vendor/img/icons/ms-icon-144x144.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            overflow-y: auto;
            overflow-x: hidden;
            height: 100%;
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg, #e8f4f8 0%, #f8f9fa 50%, #ffffff 100%);
            overflow-x: hidden;
            overflow-y: auto;
            min-height: 100vh;
            height: 100%;
            position: relative;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(5, 117, 230, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 242, 96, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Header - Fullscreen PWA Mode */
        .header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 10px 20px;
            padding-top: calc(10px + env(safe-area-inset-top));
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 2px solid rgba(6, 182, 212, 0.3);
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            min-height: 70px;
            height: auto;
        }
        

        

        /* Header Logo and Search Section */
        .header-left-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .header-logo {
            width: 55px;
            height: 55px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 0;
            margin: 0;
            margin-left: -10px;
        }
        
        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.3));
            padding: 0;
            margin: 0;
        }
        
        .header-search-bar {
            flex: 1;
            max-width: 500px;
        }
        
        .header-search-bar form {
            width: 100%;
        }
        
        .header-search-bar input {
            width: 100%;
            height: 40px;
            padding: 8px 16px;
            border: 2px solid transparent;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background-image: linear-gradient(rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.95)), linear-gradient(135deg, #ec4899 0%, #ef4444 100%);
            background-origin: border-box;
            background-clip: padding-box, border-box;
        }
        
        .header-search-bar input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.2), 0 4px 15px rgba(0,0,0,0.15);
            background-image: linear-gradient(rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.98)), linear-gradient(135deg, #ec4899 0%, #dc2626 100%);
            transform: translateY(-1px);
        }
        
        .header-search-bar input::placeholder {
            color: #6c757d;
            font-weight: 400;
        }

        
        .header-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        /* Header Menu Button - Slim */
        .header-menu-btn {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            padding: 6px;
            gap: 2px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .header-menu-btn span {
            width: 100%;
            height: 1.5px;
            background: white;
            border-radius: 1px;
            transition: all 0.2s;
        }
        
        .header-menu-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
            transform: scale(1.05);
        }
        
        .header-tier-badge {
            animation: tierBadgeGlow 3s ease-in-out infinite;
        }
        
        @keyframes tierBadgeGlow {
            0%, 100% {
                box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            }
            50% {
                box-shadow: 0 5px 20px rgba(0,0,0,0.4), 0 0 30px rgba(255,255,255,0.3);
            }
        }


        
        .header-btn {
            padding: 6px 12px;
            border-radius: 20px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        
        .header-btn.active {
            background: #667eea;
            color: white;
        }
        
        .header-btn:hover {
            text-decoration: none;
        }
        

        
        .notif-icon-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            position: relative;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .notif-icon-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.4);
        }
        
        .notif-icon-btn .notif-dot {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 12px;
            height: 12px;
            background: #00F260;
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse 2s infinite;
            box-shadow: 0 2px 8px rgba(0, 242, 96, 0.6);
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        

        
        .tech-info-horizontal {
            display: none;
        }
        
        .tech-actions-horizontal {
            display: none;
        }
        
        .btn-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        
        .btn-icon:hover {
            transform: scale(1.05);
            text-decoration: none;
            color: white;
        }
        
        .btn-profile {
            background: #667eea;
        }
        
        .btn-logout {
            background: #dc3545;
        }
        
        .btn-logout:hover {
            background: #c82333;
        }
        
        /* Menu Toggle Button */
        .btn-menu {
            background: #28a745;
        }
        
        /* Mobile Notification Alert */
        .mobile-notification-alert {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            color: white;
            padding: 12px 15px;
            margin: 75px 15px 10px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            animation: slideDown 0.5s ease-out;
            position: relative;
            z-index: 1;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .mobile-alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .mobile-alert-content i {
            font-size: 1.5rem;
            animation: bellShake 1s ease-in-out infinite;
        }
        
        @keyframes bellShake {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-15deg); }
            20%, 40% { transform: rotate(15deg); }
            50% { transform: rotate(0deg); }
        }
        
        .mobile-alert-content span {
            font-size: 1rem;
            font-weight: 700;
        }
        
        .mobile-alert-btn {
            background: white;
            color: #10b981;
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            white-space: nowrap;
        }
        
        .mobile-alert-btn:hover {
            background: #ffd700;
            color: #10b981;
            text-decoration: none;
        }
        

        
        .btn-notifications {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-notifications:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: scale(1.15);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .notification-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #ff4757;
            border-radius: 50%;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            font-size: 0.7rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(255, 215, 0, 0.6);
            animation: notificationPulse 2s infinite, notificationGlow 2s infinite;
        }
        
        @keyframes notificationPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        
        @keyframes notificationGlow {
            0%, 100% { 
                box-shadow: 0 3px 10px rgba(255, 215, 0, 0.6);
            }
            50% { 
                box-shadow: 0 5px 20px rgba(255, 215, 0, 0.9), 0 0 30px rgba(255, 215, 0, 0.4);
            }
        }
        
        /* Control Bar - Touching Bottom of Nav */
        .control-bar {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 6px 12px;
            margin: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-bottom: 2px solid transparent;
            border-image: linear-gradient(90deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%) 1;
            position: fixed;
            top: calc(70px + env(safe-area-inset-top));
            left: 0;
            right: 0;
            width: 100%;
            z-index: 998;
            border-top: 1px solid rgba(16, 185, 129, 0.1);
        }
        
        .control-main-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
        }
        
        .control-left-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        

        

        
        /* Sidebar Menu - Slides from Left */
        .sidebar-menu {
            position: fixed;
            top: 0;
            left: -300px;
            width: 280px;
            height: 100vh;
            background: white;
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            transition: left 0.3s ease;
            z-index: 1001;
            overflow-y: auto;
        }
        
        .sidebar-menu.active {
            left: 0;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        .sidebar-header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 20px;
            color: white;
        }
        
        .sidebar-close {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            color: white;
            font-size: 1.2rem;
        }
        
        .sidebar-user-info {
            text-align: center;
            margin-top: 10px;
        }
        
        .sidebar-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2rem;
            color: white;
        }
        
        .sidebar-name {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .sidebar-id {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .sidebar-phone {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .sidebar-pin {
            background: white;
            color: #10b981;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            margin-top: 5px;
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
        }
        
        .sidebar-menu-items {
            padding: 20px 0;
        }
        
        .sidebar-menu-item {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu-item:hover {
            background: #f8f9fa;
            border-left-color: #10b981;
            text-decoration: none;
            color: #10b981;
        }
        
        .sidebar-menu-item i {
            width: 25px;
            font-size: 1.2rem;
        }
        
        .sidebar-menu-item.logout {
            color: #dc3545;
        }
        
        .sidebar-menu-item.logout:hover {
            background: #ffebee;
            border-left-color: #dc3545;
            color: #dc3545;
        }
        
        .filter-buttons-row {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-shrink: 0;
        }
        
        .filter-buttons-row::-webkit-scrollbar {
            display: none;
        }
        
        .filter-btn {
            padding: 4px 8px;
            border: 1px solid transparent;
            background: white;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.7rem;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            height: 28px;
        }
        
        .filter-btn:hover {
            text-decoration: none;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #ec4899 0%, #ef4444 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(236, 72, 153, 0.4);
        }
        
        .filter-btn .badge {
            background: linear-gradient(135deg, #ec4899 0%, #ef4444 100%);
            color: white;
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 0.6rem;
            font-weight: 900;
            min-width: 14px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(236, 72, 153, 0.3);
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .filter-btn.active .badge {
            background: white;
            color: #ec4899;
        }
        
        .filter-btn i {
            font-size: 0.65rem;
        }
        
        /* Main Content */
        .main-container-full {
            padding: 0 20px 20px 20px;
            padding-top: 115px;
            max-width: 100%;
            width: 100%;
            margin: 0 auto;
            overflow-x: hidden;
            overflow-y: auto;
            height: 100vh;
            position: relative;
        }
        
        /* Alert Messages */
        .alert-message {
            position: relative;
            z-index: 997;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            animation: slideInDown 0.5s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .alert-success {
            background: #d4edda !important;
            border: 2px solid #28a745 !important;
            color: #155724 !important;
        }
        
        .alert-warning {
            background: #fff3cd !important;
            border: 2px solid #ffc107 !important;
            color: #856404 !important;
        }
        
        .alert-error {
            background: #f8d7da !important;
            border: 2px solid #dc3545 !important;
            color: #721c24 !important;
        }
        
        .bookings-section-full {
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
            overflow-x: visible;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            position: relative;
            padding: 20px;
            padding-bottom: 40px;
            border-radius: 20px;
        }
        
        /* Custom Scrollbar - Horizontal */
        .bookings-section-full::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }
        
        .bookings-section-full::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .bookings-section-full::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ff4757, #ffa502);
            border-radius: 10px;
        }
        
        .bookings-section-full::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #ff6b9d, #ffa502);
        }
        
        /* Scrollbar corner */
        .bookings-section-full::-webkit-scrollbar-corner {
            background: #f1f1f1;
        }
        
        /* Table */
        .bookings-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        
        .bookings-table thead {
            background: #f8f9fa;
        }
        
        .bookings-table th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
            font-size: 0.85rem;
        }
        
        .bookings-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        
        .bookings-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.75rem;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .status-new {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #856404;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #0575E6 0%, #03a9f4 100%);
            color: white;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #00c853 0%, #00F260 100%);
            color: white;
        }
        
        .call-btn {
            background: linear-gradient(135deg, #00c853 0%, #00F260 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            box-shadow: 0 3px 10px rgba(0, 200, 83, 0.3);
            border: none;
        }
        
        .call-btn:hover {
            background: linear-gradient(135deg, #00F260 0%, #00c853 100%);
            text-decoration: none;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 200, 83, 0.4);
        }
        
        .action-btn {
            background: linear-gradient(135deg, #0575E6 0%, #00F260 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            box-shadow: 0 3px 10px rgba(5, 117, 230, 0.3);
            border: none;
        }
        
        .action-btn:hover {
            background: linear-gradient(135deg, #00F260 0%, #0575E6 100%);
            text-decoration: none;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 117, 230, 0.4);
        }
        
        .view-btn {
            background: linear-gradient(135deg, #03a9f4 0%, #0575E6 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            box-shadow: 0 3px 10px rgba(3, 169, 244, 0.3);
            border: none;
        }
        
        .view-btn:hover {
            background: linear-gradient(135deg, #0575E6 0%, #03a9f4 100%);
            text-decoration: none;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(3, 169, 244, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        /* Scroll Hint */
        .scroll-hint {
            display: none;
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(90deg, rgba(255,71,87,0.9), rgba(255,165,2,0.9));
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 700;
            z-index: 10;
            animation: pulse-hint 2s ease-in-out infinite;
        }
        
        @keyframes pulse-hint {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }
        
        @media (max-width: 768px) {
            .scroll-hint {
                display: block;
            }
        }
        
        /* Large Tablets & Small Desktops (1200px and below) */
        @media (max-width: 1200px) {
            html, body {
                overflow-x: hidden;
                overflow-y: auto;
            }

            .header {
                padding: 15px 20px;
            }
            
            .tech-info-horizontal {
                padding: 8px 15px;
            }
            
            .tech-meta {
                font-size: 0.85rem;
            }
            
            .control-bar {
                padding: 8px 12px;
                top: calc(70px + env(safe-area-inset-top));
            }
            
            .control-main-row {
                gap: 10px;
            }
            
            .control-left-section {
                gap: 8px;
            }
            
            .header-left-section {
                gap: 10px;
            }
            
            .header-logo {
                width: 50px;
                height: 50px;
            }
            
            .header-search-bar {
                max-width: 380px;
            }
            
            .header-search-bar input {
                height: 36px;
                font-size: 0.85rem;
                padding: 6px 14px;
            }
            

            
            .main-container-full {
                padding: 0 20px 20px 20px;
                padding-top: 125px;
                overflow-x: hidden;
            }

            .bookings-section-full {
                overflow-x: auto;
            }
            
            .bookings-table {
                font-size: 0.9rem;
            }
        }
        
        /* Tablets (992px and below) */
        @media (max-width: 992px) {
            html, body {
                overflow-x: hidden;
                overflow-y: auto;
                height: 100%;
            }

            .main-container-full {
                overflow-x: hidden;
            }

            .bookings-section-full {
                overflow-x: auto;
            }




            
            .dashboard-title {
                font-size: 0.95rem;
                padding: 7px 18px;
            }
            
            .tech-avatar-small {
                width: 42px;
                height: 42px;
                font-size: 1.1rem;
            }
            
            .tech-name-small {
                font-size: 0.9rem;
            }
            
            .search-box {
                width: 240px;
                max-width: 240px;
            }
            
            .filter-btn {
                padding: 10px 20px;
                font-size: 0.95rem;
                font-weight: 900;
            }
            
            .bookings-table th,
            .bookings-table td {
                padding: 12px 8px;
                font-size: 0.85rem;
            }
            
            .call-btn,
            .action-btn,
            .view-btn {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
        
        /* Mobile Card Layout */
        .booking-card {
            display: block;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
        }

        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.15);
            border-color: rgba(6, 182, 212, 0.3);
        }
        
        .booking-card-body {
            padding: 0;
        }
        
        .order-field-mobile {
            margin-bottom: 8px;
            display: block !important;
            visibility: visible !important;
        }
        
        .order-field-mobile label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: block !important;
            visibility: visible !important;
        }
        
        .order-field-mobile p {
            font-size: 1.1rem;
            color: #1f2937;
            margin: 0;
            font-weight: 600;
            line-height: 1.3;
            display: block !important;
            visibility: visible !important;
        }
        
        .order-id-mobile {
            font-size: 1.4rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .action-buttons-side {
            display: flex !important;
            flex-direction: column;
            gap: 8px;
            margin-left: 12px;
            justify-content: flex-start;
            align-items: stretch;
            padding-top: 0;
            visibility: visible !important;
        }
        
        .action-btn-mobile {
            padding: 10px 16px;
            border: none;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
            min-width: 80px;
            color: white;
        }
        
        .call-btn-mobile {
            background: #10b981;
        }
        
        .call-btn-mobile:hover {
            background: #059669;
            color: white;
            text-decoration: none;
            transform: scale(1.05);
        }
        
        .view-btn-mobile {
            background: #0ea5e9;
        }
        
        .view-btn-mobile:hover {
            background: #0284c7;
            color: white;
            text-decoration: none;
            transform: scale(1.05);
        }
        
        .action-btn-mobile i {
            font-size: 0.7rem;
        }
        
        .action-btn-mobile span {
            font-size: 0.75rem;
        }
        
        .booking-card-actions {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #f3f4f6;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .mobile-done-btn {
            flex: 1;
            min-width: 0;
            background: #10b981;
            color: white;
            padding: 12px 8px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
        }
        
        .mobile-done-btn:hover {
            background: #059669;
            color: white;
            text-decoration: none;
        }
        
        .mobile-done-btn.done {
            background: #10b981;
            cursor: not-allowed;
            opacity: 0.7;
            flex: 1;
        }
        
        .mobile-notdone-btn {
            flex: 1;
            min-width: 0;
            background: #ef4444;
            color: white;
            padding: 12px 8px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
        }
        
        .mobile-notdone-btn:hover {
            background: #dc2626;
            color: white;
            text-decoration: none;
        }
        
        .mobile-done-btn i,
        .mobile-notdone-btn i,
        .mobile-hold-btn i {
            font-size: 0.9rem;
        }
        
        .mobile-hold-btn {
            flex: 1;
            min-width: 0;
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
            color: white;
            padding: 12px 8px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
        }
        
        .mobile-hold-btn:hover {
            background: linear-gradient(135deg, #ff6348 0%, #ffa502 100%);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .priority-badge {
            background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            box-shadow: 0 3px 10px rgba(255, 71, 87, 0.4);
            animation: priorityPulse 2s infinite;
        }
        
        @keyframes priorityPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 3px 10px rgba(255, 71, 87, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 5px 20px rgba(255, 71, 87, 0.6);
            }
        }
        
        .priority-badge i {
            animation: fire 1s infinite;
        }
        
        @keyframes fire {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        .on-hold-badge {
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            box-shadow: 0 3px 10px rgba(255, 165, 2, 0.4);
        }
        
        .on-hold-info {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-top: 8px;
            font-weight: 600;
        }
        
        .hold-pending-info {
            background: #e8f4f8;
            border: 2px solid #0575E6;
            color: #0575E6;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-top: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .hold-pending-info i {
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        

        
        /* Mobile Landscape & Small Tablets (768px and below) */
        @media (max-width: 768px) {
            html, body {
                overflow-x: hidden;
                overflow-y: auto;
                height: 100%;
                -webkit-overflow-scrolling: touch;
            }

            body {
                background: #f5f5f5;
            }

            .main-container-full {
                overflow-x: hidden;
            }

            .bookings-section-full {
                overflow-x: auto;
            }
            
            .header {
                padding: 12px 15px;
            }
            

            

            

            
            .tech-info-horizontal {
                width: 100%;
                padding: 15px;
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
            }
            
            .tech-avatar-small {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
            
            .tech-details-horizontal {
                text-align: center;
                flex: 1;
            }
            
            .tech-name-small {
                font-size: 1rem;
            }
            
            .tech-meta {
                flex-direction: row;
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .tech-id-badge,
            .tech-phone-small,
            .tech-pin-badge {
                font-size: 0.8rem;
                padding: 4px 12px;
            }
            
            .tech-actions-horizontal {
                border-left: none;
                border-top: 2px solid #e0e0e0;
                padding-top: 12px;
                padding-left: 0;
                margin-left: 0;
                width: 100%;
                justify-content: center;
                gap: 12px;
            }
            
            .btn-icon {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
            }
            
            .control-bar {
                flex-direction: column;
                padding: 6px 10px;
                top: calc(70px + env(safe-area-inset-top));
                gap: 6px;
            }
            
            .control-main-row {
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }
            
            .control-left-section {
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }
            
            .header-left-section {
                gap: 8px;
            }
            
            .header-logo {
                width: 45px;
                height: 45px;
            }
            
            .header-search-bar {
                max-width: 300px;
            }
            
            .header-search-bar input {
                height: 32px;
                font-size: 0.8rem;
                padding: 5px 12px;
            }
            
            .filter-buttons-row {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 2px;
            }
            


            .main-container-full {
                padding-top: 165px;
            }
            
            .search-box {
                width: 100%;
                min-width: auto;
            }
            

            
            .header-actions {
                flex-shrink: 0;
            }
            
            .notif-icon-btn {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
            }
            
            .filter-buttons-row {
                gap: 6px;
                padding-bottom: 3px;
            }
            
            .filter-btn {
                padding: 3px 6px;
                font-size: 0.65rem;
                font-weight: 700;
                height: 26px;
            }
            
            .filter-btn .badge {
                font-size: 0.55rem;
                padding: 1px 3px;
                height: 14px;
                min-width: 12px;
            }
            
            .filter-btn i {
                font-size: 0.85rem;
            }
            
            .main-container-full {
                padding: 0 10px 60px 10px;
                padding-top: 140px;
                width: 100%;
                max-width: 100vw;
                overflow-x: hidden;
            }
            
            .alert-message {
                padding: 12px !important;
                font-size: 0.9rem;
                margin-bottom: 15px !important;
            }

            .bookings-section-full {
                padding: 10px;
                width: 100%;
                max-width: 100%;
            }
            
            /* Hide table, show cards on mobile */
            .bookings-section-full {
                background: transparent;
                box-shadow: none;
                overflow: visible;
                max-height: none;
                padding-bottom: 50px;
            }
            
            .bookings-table {
                display: none;
            }
            
            .booking-card {
                display: block;
                padding: 10px;
                margin-bottom: 8px;
            }
            
            .booking-info-icon {
                width: 32px;
                height: 32px;
                font-size: 0.85rem;
            }
            
            .booking-info-label {
                font-size: 0.65rem;
            }
            
            .booking-info-value {
                font-size: 0.85rem;
            }
            
            .booking-id-mobile {
                font-size: 1rem;
            }
            
            .status-badge {
                font-size: 0.7rem;
                padding: 4px 10px;
            }
            
            .mobile-call-btn,
            .mobile-action-btn,
            .mobile-view-btn {
                padding: 8px;
                font-size: 0.75rem;
            }
            
            .scroll-hint {
                display: none;
            }
            
            .empty-state {
                background: white;
                border-radius: 12px;
                padding: 40px 20px;
            }
        }
        
        /* Mobile Portrait (576px and below) */
        @media (max-width: 576px) {
            html, body {
                overflow-x: hidden;
                overflow-y: auto;
                height: 100%;
                -webkit-overflow-scrolling: touch;
            }

            .main-container-full {
                overflow-x: hidden;
            }

            .bookings-section-full {
                overflow-x: auto;
            }

            .header {
                padding: 10px 12px;
                gap: 10px;
            }
            

            

            
            .notif-icon-btn {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
            }
            
            .btn-icon {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
            
            .control-bar {
                padding: 8px 10px;
                top: calc(70px + env(safe-area-inset-top));
            }
            
            .control-main-row {
                gap: 8px;
            }
            
            .control-left-section {
                gap: 6px;
            }
            


            .main-container-full {
                padding-top: 120px;
            }
            
            .mobile-notification-alert {
                margin: 115px 10px 15px 10px;
                z-index: 1;
            }
            
            .search-box input {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            .filter-btn {
                padding: 3px 8px;
                font-size: 0.7rem;
                height: 28px;
            }
            
            .filter-btn .badge {
                font-size: 0.6rem;
                padding: 1px 4px;
                height: 15px;
                min-width: 13px;
            }
            
            .main-container-full {
                padding: 0 8px 60px 8px;
                padding-top: 120px;
                width: 100%;
                max-width: 100vw;
            }
            
            .alert-message {
                padding: 10px !important;
                font-size: 0.85rem;
                margin-bottom: 12px !important;
            }
            
            /* Make table scrollable both directions on very small screens */
            .bookings-section-full {
                overflow: visible;
                max-height: none;
                min-height: auto;
                padding: 8px;
                padding-bottom: 50px;
                width: 100%;
            }

            .booking-card {
                padding: 10px;
                padding-left: 16px;
                margin-bottom: 10px;
            }

            .order-field-mobile {
                margin-bottom: 6px;
            }

            .order-field-mobile p {
                font-size: 0.9rem;
            }

            .order-id-mobile {
                font-size: 1.1rem;
            }

            .mobile-done-btn,
            .mobile-notdone-btn,
            .mobile-hold-btn {
                padding: 10px 6px;
                font-size: 0.8rem;
                gap: 4px;
            }
            
            .bookings-table {
                min-width: 700px;
                font-size: 0.7rem;
            }
            
            .bookings-table th,
            .bookings-table td {
                padding: 6px 3px;
            }
            
            .empty-state {
                padding: 40px 15px;
            }
            
            .empty-state i {
                font-size: 3rem;
            }
            
            .empty-state h3 {
                font-size: 1.2rem;
            }
        }
        
        /* Extra Small Devices (480px and below) */
        @media (max-width: 480px) {
            .header {
                padding: 8px 10px;
                gap: 8px;
            }
            

            

            
            .notif-icon-btn {
                width: 42px;
                height: 42px;
                font-size: 1.05rem;
            }
            
            .notif-icon-btn .notif-dot {
                width: 10px;
                height: 10px;
            }
            
            .filter-btn {
                font-size: 0.65rem;
                padding: 3px 6px;
                height: 26px;
            }
            
            .bookings-table {
                font-size: 0.65rem;
            }
            
            .call-btn,
            .action-btn,
            .view-btn {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
            
            .status-badge {
                padding: 3px 8px;
                font-size: 0.65rem;
            }
        }
        
        /* Landscape Orientation Adjustments */
        @media (max-height: 500px) and (orientation: landscape) {
            .header {
                padding: 8px 15px;
            }
            
            .tech-info-horizontal {
                padding: 6px 12px;
            }
            
            .control-bar {
                padding: 8px 12px;
                top: calc(70px + env(safe-area-inset-top));
            }
            
            .control-main-row {
                gap: 8px;
            }
            
            .control-left-section {
                gap: 6px;
            }
            


            .header-left-section {
                gap: 6px;
            }
            
            .header-logo {
                width: 40px;
                height: 40px;
            }
            
            .header-search-bar {
                max-width: 220px;
            }
            
            .header-search-bar input {
                height: 28px;
                font-size: 0.7rem;
                padding: 4px 8px;
            }

            .main-container-full {
                padding-top: 110px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar Menu -->
    <div class="sidebar-menu" id="sidebarMenu">
        <div class="sidebar-header">
            <button class="sidebar-close" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
            <div class="sidebar-user-info">
                <div class="sidebar-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="sidebar-name"><?php echo htmlspecialchars($t_name); ?></div>
                
                <?php 
                // Display technician tier badge
                $tier_badge = '';
                $tier_name = '';
                $tier_color = '';
                
                if(isset($tech_data->t_booking_limit)) {
                    if($tech_data->t_booking_limit >= 5) {
                        $tier_badge = '⭐';
                        $tier_name = 'Star Technician';
                        $tier_color = '#00c853';
                    } elseif($tech_data->t_booking_limit >= 3) {
                        $tier_badge = '💎';
                        $tier_name = 'Premium Technician';
                        $tier_color = '#667eea';
                    } else {
                        $tier_name = 'Regular Technician';
                        $tier_color = '#6c757d';
                    }
                }
                
                if(!empty($tier_badge)): ?>
                    <div class="sidebar-tier-badge" style="background: <?php echo $tier_color; ?>; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.9rem; font-weight: 700; display: inline-block; margin: 10px 0; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                        <span style="font-size: 1.2rem;"><?php echo $tier_badge; ?></span> <?php echo $tier_name; ?>
                    </div>
                <?php elseif(!empty($tier_name)): ?>
                    <div class="sidebar-tier-badge" style="background: <?php echo $tier_color; ?>; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.9rem; font-weight: 700; display: inline-block; margin: 10px 0; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                        <?php echo $tier_name; ?>
                    </div>
                <?php endif; ?>
                
                <div class="sidebar-id">ID: <?php echo htmlspecialchars($t_id_no); ?></div>
                <?php if(!empty($t_phone)): ?>
                    <div class="sidebar-phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($t_phone); ?></div>
                <?php endif; ?>
                <?php if(!empty($t_pincode)): ?>
                    <div class="sidebar-pin">PIN: <?php echo htmlspecialchars($t_pincode); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="sidebar-menu-items">
            <a href="notifications.php" class="sidebar-menu-item">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="notification-settings.php" class="sidebar-menu-item">
                <i class="fas fa-cog"></i>
                <span>Sound Settings</span>
            </a>
            <a href="my-profile.php" class="sidebar-menu-item">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>
            <a href="change-password.php" class="sidebar-menu-item">
                <i class="fas fa-key"></i>
                <span>Change Password</span>
            </a>
            <a href="logout.php" class="sidebar-menu-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>


    <!-- Header -->
    <div class="header">
        <div class="header-left-section">
            <div class="header-logo">
                <img src="../vendor/EZlogonew.png" alt="EZ">
            </div>
            <div class="header-search-bar">
                <form action="" method="GET" id="headerSearchForm">
                    <input type="search" name="search" id="headerSearchInput" placeholder="Search by phone, name, or ID..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
                    <?php if($filter != 'all'): ?>
                        <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>

        
        <?php 
        // Display technician tier badge in header
        $tier_badge_header = '';
        $tier_name_header = '';
        $tier_color_header = '';
        
        if(isset($tech_data->t_booking_limit)) {
            if($tech_data->t_booking_limit >= 5) {
                $tier_badge_header = '⭐';
                $tier_name_header = 'Star';
                $tier_color_header = 'linear-gradient(135deg, #00c853 0%, #00F260 100%)';
            } elseif($tech_data->t_booking_limit >= 3) {
                $tier_badge_header = '💎';
                $tier_name_header = 'Premium';
                $tier_color_header = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
        }
        
        if(!empty($tier_badge_header)): ?>
            <div class="header-tier-badge" style="background: <?php echo $tier_color_header; ?>; color: white; padding: 6px 15px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 5px; box-shadow: 0 3px 10px rgba(0,0,0,0.2); margin-left: auto; margin-right: 10px;">
                <span style="font-size: 1.1rem;"><?php echo $tier_badge_header; ?></span>
                <span><?php echo $tier_name_header; ?></span>
            </div>
        <?php endif; ?>
        
        <div class="header-actions">
            <button class="notif-icon-btn" onclick="window.location.href='notifications.php'">
                <i class="fas fa-bell"></i>
                <?php if($new_count > 0): ?>
                    <span class="notification-count" id="notificationCount"><?php echo $new_count; ?></span>
                <?php else: ?>
                    <span class="notification-count" id="notificationCount" style="display: none;">0</span>
                <?php endif; ?>
                <span class="notif-dot" id="headerNotifDot" style="display: none;"></span>
            </button>
            
            <button class="header-menu-btn" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
    


    <!-- Mobile Notification Alert -->
    <div class="mobile-notification-alert" id="mobileNotificationAlert" style="display: none;">
        <div class="mobile-alert-content">
            <i class="fas fa-bell"></i>
            <span id="mobileAlertText">You have new notifications!</span>
        </div>
        <a href="notifications.php" class="mobile-alert-btn">View</a>
    </div>

    <!-- Control Bar -->
    <div class="control-bar">
        <!-- Single Row: Menu, Search, and Filter Buttons -->
        <div class="control-main-row">
            <div class="control-left-section">
                <div class="filter-buttons-row">
                    <a href="?filter=pending<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter == 'pending' ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> Pending
                        <?php if($pending_count > 0): ?>
                            <span class="badge"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="?<?php echo !empty($search) ? 'search=' . urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">
                        <i class="fas fa-list"></i> All Active
                        <?php if($all_active_count > 0): ?>
                            <span class="badge"><?php echo $all_active_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="?filter=completed<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter == 'completed' ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Today's Completed
                        <?php if($completed_count > 0): ?>
                            <span class="badge"><?php echo $completed_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container-full">
        <?php if(isset($_GET['success'])): ?>
            <?php if($_GET['success'] == 'completed'): ?>
                <div class="alert-message alert-success" style="background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 15px; font-weight: 600; text-align: center;">
                    <i class="fas fa-check-circle"></i> Booking marked as Done successfully! Status is now permanent.
                </div>
            <?php elseif($_GET['success'] == 'not_done'): ?>
                <div class="alert-message alert-warning" style="background: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px; border-radius: 10px; margin-bottom: 15px; font-weight: 600; text-align: center;">
                    <i class="fas fa-info-circle"></i> Booking marked as Not Done. Admin has been notified. Status is now permanent.
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <?php if($_GET['error'] == 'status_locked'): ?>
                <div class="alert-message alert-error" style="background: #f8d7da; border: 2px solid #dc3545; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 15px; font-weight: 600; text-align: center;">
                    <i class="fas fa-lock"></i> This booking status is already set and cannot be changed.
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Bookings Table -->
        <div class="bookings-section-full">
            <div class="scroll-hint">
                <i class="fas fa-arrows-alt"></i> Scroll to view all data
            </div>
            <?php if($bookings_result->num_rows > 0): ?>
                <!-- Cards Only - No Table -->
                <div style="display: none;">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Name</th>
                            <th>Pincode</th>
                            <th>Address</th>
                            <th>Call</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Show only active bookings (cancelled bookings are already filtered out in query)
                        $bookings_result->data_seek(0);
                        while($booking = $bookings_result->fetch_object()):
                            
                            // Get pincode from booking or extract from address
                            $customer_pincode = '';
                            
                            // First check if sb_pincode property exists and is not empty
                            if(isset($booking->sb_pincode) && !empty($booking->sb_pincode)) {
                                $customer_pincode = $booking->sb_pincode;
                            }
                            // If not, try to extract from address
                            elseif(!empty($booking->sb_address)) {
                                // Try multiple patterns to extract 6-digit pincode from booking address
                                if(preg_match('/\b(\d{6})\b/', $booking->sb_address, $pin_matches)) {
                                    $customer_pincode = $pin_matches[1];
                                } elseif(preg_match('/(\d{6})/', $booking->sb_address, $pin_matches)) {
                                    $customer_pincode = $pin_matches[1];
                                }
                            }
                            // Last resort: try user address
                            elseif(!empty($booking->u_addr)) {
                                // Try multiple patterns to extract 6-digit pincode
                                if(preg_match('/\b(\d{6})\b/', $booking->u_addr, $pin_matches)) {
                                    $customer_pincode = $pin_matches[1];
                                } elseif(preg_match('/(\d{6})/', $booking->u_addr, $pin_matches)) {
                                    $customer_pincode = $pin_matches[1];
                                } elseif(preg_match('/pin[:\s-]*(\d{6})/i', $booking->u_addr, $pin_matches)) {
                                    $customer_pincode = $pin_matches[1];
                                } elseif(preg_match('/pincode[:\s-]*(\d{6})/i', $booking->u_addr, $pin_matches)) {
                                    $customer_pincode = $pin_matches[1];
                                }
                            }
                            
                            $status_class = '';
                            if($booking->sb_status == 'Pending') {
                                $status_class = 'status-new';
                            } elseif($booking->sb_status == 'In Progress') {
                                $status_class = 'status-pending';
                            } elseif($booking->sb_status == 'Completed') {
                                $status_class = 'status-completed';
                            }
                        ?>
                        <tr>
                            <td><strong style="color: #ff4757;">#<?php echo $booking->sb_id; ?></strong></td>
                            <td><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></td>
                            <td><?php echo $customer_pincode ? $customer_pincode : '-'; ?></td>
                            <td><?php echo htmlspecialchars(substr($booking->u_addr, 0, 50)) . (strlen($booking->u_addr) > 50 ? '...' : ''); ?></td>
                            <td>
                                <?php if(!empty($booking->u_phone)): ?>
                                    <a href="tel:<?php echo $booking->u_phone; ?>" class="call-btn">
                                        <i class="fas fa-phone"></i> <?php echo $booking->u_phone; ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $booking->sb_status; ?>
                                </span>
                            </td>
                            <td>
                                <a href="booking-details.php?id=<?php echo $booking->sb_id; ?>" class="view-btn">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
                
                <!-- Mobile Card View -->
                <?php 
                // Show only active bookings (cancelled bookings are already filtered out)
                $bookings_result->data_seek(0);
                while($booking = $bookings_result->fetch_object()):
                    
                    // Get pincode from booking or extract from address
                    $customer_pincode = '';
                    
                    // First check if sb_pincode property exists and is not empty
                    if(isset($booking->sb_pincode) && !empty($booking->sb_pincode)) {
                        $customer_pincode = $booking->sb_pincode;
                    }
                    // If not, try to extract from booking address
                    elseif(!empty($booking->sb_address)) {
                        if(preg_match('/\b(\d{6})\b/', $booking->sb_address, $pin_matches)) {
                            $customer_pincode = $pin_matches[1];
                        } elseif(preg_match('/(\d{6})/', $booking->sb_address, $pin_matches)) {
                            $customer_pincode = $pin_matches[1];
                        }
                    }
                    // Last resort: try user address
                    elseif(!empty($booking->u_addr)) {
                        if(preg_match('/\b(\d{6})\b/', $booking->u_addr, $pin_matches)) {
                            $customer_pincode = $pin_matches[1];
                        } elseif(preg_match('/(\d{6})/', $booking->u_addr, $pin_matches)) {
                            $customer_pincode = $pin_matches[1];
                        }
                    }
                    
                    $status_class = '';
                    if($booking->sb_status == 'Pending') {
                        $status_class = 'status-new';
                    } elseif($booking->sb_status == 'In Progress') {
                        $status_class = 'status-pending';
                    } elseif($booking->sb_status == 'Completed') {
                        $status_class = 'status-completed';
                    }
                ?>
                <div class="booking-card">
                    <div class="booking-card-body">
                        <?php if(isset($booking->sb_is_high_priority) && $booking->sb_is_high_priority == 1 && $booking->sb_status != 'Completed'): ?>
                            <div class="priority-badge">
                                <i class="fas fa-fire"></i> HIGH PRIORITY
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($booking->sb_is_on_hold) && $booking->sb_is_on_hold == 1): ?>
                            <div class="on-hold-badge">
                                <i class="fas fa-pause-circle"></i> ON HOLD
                            </div>
                        <?php endif; ?>
                        
                        <div style="display: flex;">
                            <!-- Left Side - Vertical List of Order Details -->
                            <div style="flex: 1;">
                                <div class="order-field-mobile">
                                    <label>Order ID</label>
                                    <p class="order-id-mobile">#<?php echo $booking->sb_id; ?></p>
                                </div>
                                <div class="order-field-mobile">
                                    <label>Customer Name</label>
                                    <p><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></p>
                                </div>
                                <div class="order-field-mobile">
                                    <label>Pincode</label>
                                    <p><?php echo $customer_pincode ? $customer_pincode : 'N/A'; ?></p>
                                </div>
                                <div class="order-field-mobile">
                                    <label>Address</label>
                                    <p><?php echo htmlspecialchars($booking->u_addr); ?></p>
                                </div>
                                <div class="order-field-mobile">
                                    <label>Service</label>
                                    <p><?php echo !empty($booking->s_name) ? htmlspecialchars($booking->s_name) : 'N/A'; ?></p>
                                </div>
                            </div>
                            
                            <!-- Right Side - Call & View Buttons -->
                            <div class="action-buttons-side">
                                <?php if(!empty($booking->u_phone)): ?>
                                    <a href="tel:<?php echo $booking->u_phone; ?>" class="action-btn-mobile call-btn-mobile">
                                        <i class="fas fa-phone"></i>
                                        <span>Call</span>
                                    </a>
                                <?php endif; ?>
                                <a href="booking-details.php?id=<?php echo $booking->sb_id; ?>" class="action-btn-mobile view-btn-mobile">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Done / Not Done Buttons -->
                        <div class="booking-card-actions">
                            <?php if(isset($booking->sb_is_on_hold) && $booking->sb_is_on_hold == 1): ?>
                                <!-- Booking is on hold -->
                                <div class="on-hold-info">
                                    <i class="fas fa-info-circle"></i> This booking is on hold until <?php echo date('M d, Y', strtotime($booking->sb_hold_end_date)); ?>. Waiting for customer to resume.
                                </div>
                            <?php elseif($booking->sb_status == 'Completed'): ?>
                                <button class="mobile-done-btn done" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-check-circle"></i> Completed
                                </button>
                            <?php elseif($booking->sb_status == 'Not Done'): ?>
                                <button class="mobile-notdone-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-times-circle"></i> Not Done
                                </button>
                            <?php else: ?>
                                <?php
                                // Ensure payment collection table exists
                                $mysqli->query("CREATE TABLE IF NOT EXISTS tms_payment_collection (
                                    pc_id INT AUTO_INCREMENT PRIMARY KEY,
                                    pc_booking_id INT NOT NULL,
                                    pc_amount DECIMAL(10,2) NOT NULL,
                                    pc_method ENUM('QR','TechQR','Cash') NOT NULL,
                                    pc_collected_by INT NOT NULL,
                                    pc_collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    pc_status ENUM('Collected','Verified') DEFAULT 'Collected',
                                    INDEX(pc_booking_id),
                                    INDEX(pc_collected_by)
                                )");
                                
                                // Check if payment has been collected for this booking
                                $payment_check_query = "SELECT COUNT(*) as count FROM tms_payment_collection WHERE pc_booking_id = ?";
                                $payment_check_stmt = $mysqli->prepare($payment_check_query);
                                $payment_check_stmt->bind_param('i', $booking->sb_id);
                                $payment_check_stmt->execute();
                                $payment_check_result = $payment_check_stmt->get_result();
                                $payment_check_data = $payment_check_result->fetch_object();
                                $payment_already_collected = $payment_check_data->count > 0;
                                
                                // Check if there's a pending hold request for this booking
                                $check_pending_hold_req = "SELECT COUNT(*) as count FROM tms_booking_hold_requests 
                                                          WHERE bhr_booking_id = ? AND bhr_status = 'Pending'";
                                $stmt_pending_req = $mysqli->prepare($check_pending_hold_req);
                                $stmt_pending_req->bind_param('i', $booking->sb_id);
                                $stmt_pending_req->execute();
                                $pending_hold_req_result = $stmt_pending_req->get_result();
                                $pending_hold_req_data = $pending_hold_req_result->fetch_object();
                                $has_pending_hold_request = $pending_hold_req_data->count > 0;
                                ?>
                                
                                <?php if($has_pending_hold_request): ?>
                                    <!-- Hold request pending - disable all action buttons -->
                                    <div class="hold-pending-info">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span>Hold request sent. Waiting for customer approval. All actions are disabled until customer responds.</span>
                                    </div>
                                    <button class="mobile-done-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Hold request pending - waiting for customer response">
                                        <i class="fas fa-lock"></i> Locked - Hold Pending
                                    </button>
                                    <button class="mobile-notdone-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Hold request pending - waiting for customer response">
                                        <i class="fas fa-lock"></i> Locked - Hold Pending
                                    </button>
                                <?php elseif($payment_already_collected): ?>
                                    <!-- Payment already collected, show Complete Service button -->
                                    <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>&action=done" class="mobile-done-btn">
                                        <i class="fas fa-check-circle"></i> Complete Service
                                    </a>
                                    <button class="mobile-notdone-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot mark as Not Done after payment collected">
                                        <i class="fas fa-lock"></i> Payment Collected
                                    </button>
                                <?php else: ?>
                                    <!-- Payment not collected yet, show Collect Payment button -->
                                    <a href="collect-payment.php?id=<?php echo $booking->sb_id; ?>" class="mobile-done-btn">
                                        <i class="fas fa-rupee-sign"></i> Collect Payment
                                    </a>
                                    <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>&action=not-done" class="mobile-notdone-btn">
                                        <i class="fas fa-times"></i> Not Done
                                    </a>
                                <?php endif; ?>
                                
                                <!-- Hold Button - Only show if not completed, not on hold, and no pending request -->
                                <?php if($booking->sb_status != 'Completed' && $booking->sb_status != 'Not Done' && (!isset($booking->sb_is_on_hold) || $booking->sb_is_on_hold != 1)): ?>
                                    <?php if(!$has_pending_hold_request): ?>
                                        <a href="request-booking-hold.php?id=<?php echo $booking->sb_id; ?>" class="mobile-hold-btn">
                                            <i class="fas fa-pause-circle"></i> Request Hold
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Bookings Found</h3>
                    <p>No bookings match your current filter.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
        
        // Auto-search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            let searchTimeout;
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    
                    // Auto-submit after 500ms of no typing
                    searchTimeout = setTimeout(function() {
                        searchForm.submit();
                    }, 500);
                });
            }
            
            // Auto-hide alert messages after 5 seconds
            const alertMessages = document.querySelectorAll('.alert-message');
            
            alertMessages.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s, transform 0.5s';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    
                    setTimeout(function() {
                        alert.remove();
                        // Remove the query parameter from URL
                        if (window.history.replaceState) {
                            const url = new URL(window.location);
                            url.searchParams.delete('success');
                            url.searchParams.delete('error');
                            window.history.replaceState({}, '', url);
                        }
                    }, 500);
                }, 5000);
            });
        });
    </script>
    
    <!-- Ultimate Notification System - Works Everywhere, Never Fails -->
    <?php include('includes/ultimate-notification-system.php'); ?>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
    
    <script>
    // Live dashboard updates for technician
    function updateTechDashboardStats() {
        fetch('api-tech-dashboard-stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stats if elements exist
                    const pendingEl = document.querySelector('[data-stat="pending"]');
                    const progressEl = document.querySelector('[data-stat="progress"]');
                    const completedEl = document.querySelector('[data-stat="completed"]');
                    const totalEl = document.querySelector('[data-stat="total"]');
                    
                    if (pendingEl && pendingEl.textContent !== data.stats.pending.toString()) {
                        pendingEl.textContent = data.stats.pending;
                        pendingEl.style.animation = 'pulse 0.5s';
                    }
                    if (progressEl && progressEl.textContent !== data.stats.progress.toString()) {
                        progressEl.textContent = data.stats.progress;
                        progressEl.style.animation = 'pulse 0.5s';
                    }
                    if (completedEl && completedEl.textContent !== data.stats.completed.toString()) {
                        completedEl.textContent = data.stats.completed;
                        completedEl.style.animation = 'pulse 0.5s';
                    }
                    if (totalEl && totalEl.textContent !== data.stats.total.toString()) {
                        totalEl.textContent = data.stats.total;
                        totalEl.style.animation = 'pulse 0.5s';
                    }
                }
            })
            .catch(error => console.error('Error updating tech dashboard:', error));
    }
    
    // Update every 30 seconds (reduced frequency to avoid conflicts with notification system)
    setInterval(updateTechDashboardStats, 30000);
    </script>
</body>
</html>
