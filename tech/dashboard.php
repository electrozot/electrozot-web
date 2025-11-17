<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

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

if($filter == 'new') {
    $where_clause .= " AND sb.sb_status = 'Pending'";
} elseif($filter == 'pending') {
    $where_clause .= " AND sb.sb_status = 'In Progress'";
} elseif($filter == 'completed') {
    $where_clause .= " AND sb.sb_status = 'Completed'";
}

if(!empty($search)) {
    $where_clause .= " AND u.u_phone LIKE ?";
    $params[] = "%{$search}%";
    $types .= 's';
}

// Query to get only active bookings (exclude cancelled ones) - Sort by status priority then date
$bookings_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name,
                   CASE 
                       WHEN sb.sb_status = 'Pending' THEN 1
                       WHEN sb.sb_status = 'In Progress' THEN 2
                       WHEN sb.sb_status = 'Completed' THEN 3
                       WHEN sb.sb_status = 'Not Done' THEN 4
                       ELSE 5
                   END as status_priority
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_cancelled_bookings cb ON sb.sb_id = cb.cb_booking_id AND cb.cb_technician_id = ?
                   {$where_clause}
                   AND cb.cb_id IS NULL
                   ORDER BY status_priority ASC, sb.sb_created_at DESC, sb.sb_service_deadline_date ASC";

$stmt_bookings = $mysqli->prepare($bookings_query);
if(count($params) == 1) {
    $stmt_bookings->bind_param('ii', $t_id, $params[0]);
} else {
    $stmt_bookings->bind_param('iii', $t_id, $params[0], $params[1]);
}
$stmt_bookings->execute();
$bookings_result = $stmt_bookings->get_result();

// Get counts
$new_count = 0;
$pending_count = 0;
$completed_count = 0;

$count_query = "SELECT 
                COUNT(CASE WHEN sb_status = 'Pending' THEN 1 END) as new_count,
                COUNT(CASE WHEN sb_status = 'In Progress' THEN 1 END) as pending_count,
                COUNT(CASE WHEN sb_status = 'Completed' THEN 1 END) as completed_count
                FROM tms_service_booking WHERE sb_technician_id = ?";
$stmt_count = $mysqli->prepare($count_query);
$stmt_count->bind_param('i', $t_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$counts = $count_result->fetch_object();
$new_count = $counts->new_count;
$pending_count = $counts->pending_count;
$completed_count = $counts->completed_count;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            overflow-y: scroll;
            height: 100%;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
            overflow-y: scroll;
            min-height: 100vh;
            height: auto;
            position: relative;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
            border-bottom: 2px solid #e2e8f0;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        
        /* Search and Menu Bar */
        .search-menu-bar {
            background: white;
            padding: 10px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 15px;
            position: fixed;
            top: 75px;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 999;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .search-menu-bar .menu-toggle-btn {
            flex-shrink: 0;
        }
        
        .search-menu-bar .header-search {
            flex: 1;
            max-width: 600px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-image {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 8px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header-search {
            flex: 1;
            min-width: 0;
        }
        
        .header-search form {
            width: 100%;
        }
        
        .header-search input {
            width: 100%;
            padding: 12px 20px;
            border: 3px solid #1e293b;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 600;
            background: white;
            box-sizing: border-box;
        }
        
        .header-search input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .header-search input::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }
        
        .header-actions {
            display: flex;
            gap: 6px;
            align-items: center;
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
        
        /* Hamburger Menu Button */
        .menu-toggle-btn {
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #667eea;
            padding: 8px;
            gap: 4px;
        }
        
        .menu-toggle-btn span {
            width: 100%;
            height: 3px;
            background: #667eea;
            border-radius: 2px;
        }
        
        .menu-toggle-btn:hover {
            background: #f8f9fa;
            border-color: #764ba2;
        }
        
        .menu-toggle-btn:hover span {
            background: #764ba2;
        }
        
        .notif-icon-btn {
            width: 50px;
            height: 50px;
            background: #1e293b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            position: relative;
            border: 3px solid #1e293b;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(30, 41, 59, 0.3);
        }
        
        .notif-icon-btn:hover {
            background: #3b82f6;
            border-color: #3b82f6;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        
        .notif-icon-btn .notif-dot {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 12px;
            height: 12px;
            background: #ff4757;
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Sidebar Menu */
        .sidebar-menu {
            position: fixed;
            top: 0;
            right: -300px;
            width: 280px;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 15px rgba(0,0,0,0.2);
            transition: right 0.3s ease;
            z-index: 1001;
            overflow-y: auto;
        }
        
        .sidebar-menu.active {
            right: 0;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #ffd700;
            color: #667eea;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            margin-top: 5px;
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
            border-left-color: #667eea;
            text-decoration: none;
            color: #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            margin: 140px 15px 15px 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            animation: slideDown 0.5s ease-out;
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
            color: #667eea;
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            white-space: nowrap;
        }
        
        .mobile-alert-btn:hover {
            background: #ffd700;
            color: #667eea;
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
        
        /* Control Bar - Compact */
        .control-bar {
            background: white;
            padding: 6px 10px;
            margin: 0;
            margin-top: 140px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .filter-buttons-row {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .filter-buttons-row::-webkit-scrollbar {
            display: none;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border: 3px solid #1e293b;
            background: white;
            border-radius: 20px;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .filter-btn:hover {
            text-decoration: none;
            background: #f1f5f9;
        }
        
        .filter-btn.active {
            background: #1e293b;
            color: white;
            border-color: #1e293b;
        }
        
        .filter-btn .badge {
            background: #ffd700;
            color: #667eea;
            padding: 2px 6px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 900;
            min-width: 18px;
            text-align: center;
        }
        
        .filter-btn.active .badge {
            background: white;
            color: #667eea;
        }
        
        .filter-btn i {
            font-size: 0.8rem;
        }
        
        /* Main Content */
        .main-container-full {
            padding: 0 20px 50px 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .bookings-section-full {
            background: #f5f5f5;
            overflow: visible;
            -webkit-overflow-scrolling: touch;
            position: relative;
            padding: 8px;
            padding-bottom: 30px;
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
            padding: 3px 8px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 0.65rem;
            display: inline-block;
        }
        
        .status-new {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-pending {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-completed {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .call-btn {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
            font-size: 0.8rem;
        }
        
        .call-btn:hover {
            background: #218838;
            text-decoration: none;
            color: white;
            transform: scale(1.05);
        }
        
        .action-btn {
            background: #ff4757;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
            font-size: 0.8rem;
        }
        
        .action-btn:hover {
            background: #ff6b9d;
            text-decoration: none;
            color: white;
            transform: scale(1.05);
        }
        
        .view-btn {
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
            font-size: 0.8rem;
        }
        
        .view-btn:hover {
            background: #0056b3;
            text-decoration: none;
            color: white;
            transform: scale(1.05);
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
                padding: 15px 20px;
                margin: 15px 20px;
            }
            
            .main-container-full {
                padding: 0 20px 20px 20px;
            }
            
            .bookings-table {
                font-size: 0.9rem;
            }
        }
        
        /* Tablets (992px and below) */
        @media (max-width: 992px) {
            .logo-image {
                width: 48px;
                height: 48px;
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
            background: white;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        
        .booking-card-body {
            padding: 0;
        }
        
        .order-field-mobile {
            margin-bottom: 8px;
        }
        
        .order-field-mobile label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: block;
        }
        
        .order-field-mobile p {
            font-size: 1.1rem;
            color: #1f2937;
            margin: 0;
            font-weight: 600;
            line-height: 1.3;
        }
        
        .order-id-mobile {
            font-size: 1.4rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .action-buttons-side {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-left: 10px;
            justify-content: flex-start;
            padding-top: 0;
        }
        
        .action-btn-mobile {
            padding: 8px 14px;
            border: none;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            min-width: 75px;
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
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #f3f4f6;
        }
        
        .mobile-done-btn {
            flex: 1;
            background: #10b981;
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.9rem;
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
            background: #ef4444;
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }
        
        .mobile-notdone-btn:hover {
            background: #dc2626;
            color: white;
            text-decoration: none;
        }
        
        .mobile-done-btn i,
        .mobile-notdone-btn i {
            font-size: 0.95rem;
        }
        
        .booking-card-actions {
            display: flex;
            gap: 6px;
        }
        
        /* Mobile Landscape & Small Tablets (768px and below) */
        @media (max-width: 768px) {
            body {
                background: #f5f5f5;
                overflow-y: scroll;
                -webkit-overflow-scrolling: touch;
            }
            
            .header {
                padding: 12px 15px;
            }
            
            .search-menu-bar {
                top: 65px;
                padding: 8px 15px;
            }
            
            .logo-section {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-shrink: 0;
            }
            
            .logo-image {
                width: 55px;
                height: 55px;
            }
            
            .menu-toggle-btn {
                width: 42px;
                height: 42px;
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
                padding: 15px;
                margin: 15px;
                margin-top: 120px;
                gap: 12px;
            }
            
            .search-box {
                width: 100%;
                min-width: auto;
            }
            
            .header-search {
                flex: 1;
                min-width: 0;
            }
            
            .header-search input {
                padding: 10px 12px;
                font-size: 0.85rem;
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
                padding: 8px 14px;
                font-size: 0.85rem;
                font-weight: 900;
            }
            
            .filter-btn .badge {
                font-size: 0.7rem;
                padding: 2px 6px;
            }
            
            .filter-btn i {
                font-size: 0.85rem;
            }
            
            .main-container-full {
                padding: 0 15px 60px 15px;
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
            .header {
                padding: 10px 12px;
                gap: 10px;
            }
            
            .logo-image {
                width: 50px;
                height: 50px;
            }
            
            .menu-toggle-btn {
                width: 38px;
                height: 38px;
            }
            
            .header-search input {
                padding: 8px 10px;
                font-size: 0.8rem;
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
                padding: 10px;
                margin: 10px;
                margin-top: 110px;
            }
            
            .search-box input {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            .filter-btn {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            
            .filter-btn .badge {
                font-size: 0.7rem;
                padding: 2px 6px;
            }
            
            .main-container-full {
                padding: 0 10px 60px 10px;
            }
            
            /* Make table scrollable both directions on very small screens */
            .bookings-section-full {
                overflow: visible;
                max-height: none;
                min-height: auto;
                padding-bottom: 50px;
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
            
            .logo-section {
                gap: 8px;
            }
            
            .logo-image {
                width: 45px;
                height: 45px;
            }
            
            .menu-toggle-btn {
                width: 36px;
                height: 36px;
                padding: 6px;
                gap: 3px;
            }
            
            .menu-toggle-btn span {
                height: 2px;
            }
            
            .header-search input {
                padding: 6px 8px;
                font-size: 0.75rem;
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
                font-size: 0.8rem;
                padding: 7px 12px;
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
                padding: 10px 15px;
                margin: 10px 15px;
                margin-top: 100px;
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
        <div class="logo-section">
            <div class="logo-image">
                <img src="../vendor/EZlogonew.png" alt="EZ">
            </div>
        </div>
        
        <div class="header-actions">
            <button class="notif-icon-btn" onclick="window.location.href='notifications.php'">
                <i class="fas fa-bell"></i>
                <span class="notif-dot" id="headerNotifDot" style="display: none;"></span>
            </button>
        </div>
    </div>
    
    <!-- Search and Menu Bar -->
    <div class="search-menu-bar">
        <button class="menu-toggle-btn" onclick="toggleSidebar()">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <div class="header-search">
            <form action="" method="GET">
                <input type="search" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if($filter != 'all'): ?>
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                <?php endif; ?>
            </form>
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
        <div class="filter-buttons-row">
            <a href="?filter=new" class="filter-btn <?php echo $filter == 'new' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> New
                <?php if($new_count > 0): ?>
                    <span class="badge"><?php echo $new_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a href="?filter=pending" class="filter-btn <?php echo $filter == 'pending' ? 'active' : ''; ?>">
                <i class="fas fa-clock"></i> Pending
                <?php if($pending_count > 0): ?>
                    <span class="badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a href="?filter=completed" class="filter-btn <?php echo $filter == 'completed' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> Completed
                <?php if($completed_count > 0): ?>
                    <span class="badge"><?php echo $completed_count; ?></span>
                <?php endif; ?>
            </a>
            
            <?php if($filter != 'all' || !empty($search)): ?>
                <a href="?" class="filter-btn">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
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
                            <?php if($booking->sb_status == 'Completed'): ?>
                                <button class="mobile-done-btn done" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-check-circle"></i> Completed
                                </button>
                            <?php elseif($booking->sb_status == 'Not Done'): ?>
                                <button class="mobile-notdone-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-times-circle"></i> Not Done
                                </button>
                            <?php else: ?>
                                <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>&action=done" class="mobile-done-btn">
                                    <i class="fas fa-check"></i> Done
                                </a>
                                <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>&action=not-done" class="mobile-notdone-btn">
                                    <i class="fas fa-times"></i> Not Done
                                </a>
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
        
        // Auto-hide alert messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
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
    
    <!-- Technician Notification System -->
    <?php include('includes/notification-system.php'); ?>
</body>
</html>
