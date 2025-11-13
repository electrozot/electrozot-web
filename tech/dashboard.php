<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "Technician Dashboard";

// Ensure columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(20) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_email VARCHAR(100) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_addr TEXT DEFAULT ''");
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

$bookings_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name 
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   {$where_clause}
                   ORDER BY sb.sb_booking_date DESC, sb.sb_booking_time DESC";

$stmt_bookings = $mysqli->prepare($bookings_query);
if(count($params) == 1) {
    $stmt_bookings->bind_param($types, $params[0]);
} else {
    $stmt_bookings->bind_param($types, $params[0], $params[1]);
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
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
            overflow-y: auto;
            min-height: 100vh;
        }
        
        /* Header */
        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-image {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 10px;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .dashboard-title {
            background: #ff4757;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
        }
        
        /* Tech Info Horizontal */
        .tech-info-horizontal {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #f8f9fa, #fff);
            padding: 10px 20px;
            border-radius: 50px;
            border: 2px solid #e0e0e0;
        }
        
        .tech-avatar-small {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #ff4757, #ffa502);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .tech-details-horizontal {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .tech-name-small {
            font-size: 0.95rem;
            font-weight: 800;
            color: #333;
            line-height: 1;
        }
        
        .tech-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .tech-id-badge {
            background: #ffd700;
            color: #ff4757;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 0.5px;
        }
        
        .tech-phone-small {
            font-size: 0.75rem;
            color: #666;
            font-weight: 600;
        }
        
        .tech-phone-small i {
            color: #28a745;
            margin-right: 3px;
        }
        
        .tech-pin-badge {
            background: #007bff;
            color: white;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 900;
        }
        
        .tech-actions-horizontal {
            display: flex;
            gap: 8px;
            margin-left: 10px;
            padding-left: 10px;
            border-left: 2px solid #e0e0e0;
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
            font-size: 0.9rem;
        }
        
        .btn-icon:hover {
            transform: scale(1.1);
            text-decoration: none;
            color: white;
        }
        
        .btn-logout {
            background: #dc3545;
        }
        
        .btn-logout:hover {
            background: #c82333;
        }
        
        /* Control Bar */
        .control-bar {
            background: white;
            padding: 20px 30px;
            margin: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-box {
            flex: 0 0 auto;
            width: 280px;
            max-width: 280px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #ff4757;
        }
        
        .filter-btn {
            padding: 12px 30px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            display: inline-block;
        }
        
        .filter-btn:hover {
            text-decoration: none;
            color: #333;
        }
        
        .filter-btn.active {
            background: #ff4757;
            color: white;
            border-color: #ff4757;
        }
        
        .filter-btn .badge {
            background: #ffd700;
            color: #ff4757;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.85rem;
            margin-left: 8px;
            font-weight: 900;
        }
        
        .filter-btn.active .badge {
            background: white;
            color: #ff4757;
        }
        
        /* Main Content */
        .main-container-full {
            padding: 0 30px 30px 30px;
        }
        
        .bookings-section-full {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 300px);
            -webkit-overflow-scrolling: touch;
            position: relative;
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
        }
        
        .bookings-table thead {
            background: #f8f9fa;
        }
        
        .bookings-table th {
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .bookings-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .bookings-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .status-new {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-pending {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .call-btn {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
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
            padding: 8px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
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
            padding: 8px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
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
                font-size: 0.9rem;
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
            display: none;
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #ff4757;
        }
        
        .booking-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .booking-id-mobile {
            font-size: 1.1rem;
            font-weight: 900;
            color: #ff4757;
        }
        
        .booking-card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .booking-info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .booking-info-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #ff4757, #ffa502);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .booking-info-content {
            flex: 1;
        }
        
        .booking-info-label {
            font-size: 0.7rem;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .booking-info-value {
            font-size: 0.9rem;
            color: #333;
            font-weight: 600;
        }
        
        .booking-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }
        
        .mobile-call-btn {
            flex: 1;
            background: #28a745;
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        
        .mobile-call-btn:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
        
        .mobile-action-btn {
            flex: 1;
            background: #ff4757;
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        
        .mobile-action-btn:hover {
            background: #ff6b9d;
            color: white;
            text-decoration: none;
        }
        
        .mobile-view-btn {
            flex: 1;
            background: #007bff;
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        
        .mobile-view-btn:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        
        /* Mobile Landscape & Small Tablets (768px and below) */
        @media (max-width: 768px) {
            body {
                background: #f5f5f5;
                overflow-y: scroll;
                -webkit-overflow-scrolling: touch;
            }
            
            .header {
                flex-direction: column;
                gap: 12px;
                padding: 12px 15px;
                position: sticky;
                top: 0;
            }
            
            .logo-section {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .logo-image {
                width: 45px;
                height: 45px;
            }
            
            .dashboard-title {
                font-size: 0.85rem;
                padding: 6px 15px;
            }
            
            .tech-info-horizontal {
                width: 100%;
                padding: 10px 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .tech-avatar-small {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .tech-details-horizontal {
                text-align: center;
                flex: 1;
            }
            
            .tech-name-small {
                font-size: 0.85rem;
            }
            
            .tech-meta {
                flex-direction: row;
                justify-content: center;
                flex-wrap: wrap;
                gap: 6px;
            }
            
            .tech-actions-horizontal {
                border-left: none;
                border-top: 2px solid #e0e0e0;
                padding-top: 10px;
                padding-left: 0;
                margin-left: 0;
                width: 100%;
                justify-content: center;
            }
            
            .control-bar {
                flex-direction: column;
                padding: 15px;
                margin: 15px;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
                min-width: auto;
            }
            
            .search-box input {
                padding: 12px 15px;
                font-size: 1rem;
            }
            
            .filter-btn {
                width: 100%;
                padding: 12px 20px;
                text-align: center;
                font-size: 0.95rem;
            }
            
            .main-container-full {
                padding: 0 15px 15px 15px;
            }
            
            /* Hide table, show cards on mobile */
            .bookings-section-full {
                background: transparent;
                box-shadow: none;
                overflow: visible;
                max-height: none;
            }
            
            .bookings-table {
                display: none;
            }
            
            .booking-card {
                display: block;
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
                padding: 10px;
            }
            
            .logo-image {
                width: 40px;
                height: 40px;
            }
            
            .dashboard-title {
                font-size: 0.75rem;
                padding: 5px 12px;
            }
            
            .tech-info-horizontal {
                padding: 8px 10px;
                gap: 8px;
            }
            
            .tech-avatar-small {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            
            .tech-name-small {
                font-size: 0.75rem;
            }
            
            .tech-id-badge,
            .tech-phone-small,
            .tech-pin-badge {
                font-size: 0.65rem;
                padding: 2px 8px;
            }
            
            .btn-icon {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
            
            .control-bar {
                padding: 10px;
                margin: 10px;
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
                padding: 0 10px 10px 10px;
            }
            
            /* Make table scrollable both directions on very small screens */
            .bookings-section-full {
                overflow: auto;
                max-height: calc(100vh - 450px);
                min-height: 250px;
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
            .logo-section {
                flex-direction: column;
                align-items: center;
            }
            
            .logo-image {
                width: 38px;
                height: 38px;
            }
            
            .dashboard-title {
                font-size: 0.7rem;
                padding: 4px 10px;
            }
            
            .tech-info-horizontal {
                flex-direction: column;
                text-align: center;
            }
            
            .tech-details-horizontal {
                width: 100%;
            }
            
            .tech-meta {
                flex-direction: column;
                align-items: center;
            }
            
            .tech-actions-horizontal {
                width: 100%;
                padding-top: 8px;
                margin-top: 8px;
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
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <div class="logo-image">
                <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
            </div>
            <div class="dashboard-title">
                Technician Dashboard
            </div>
        </div>
        
        <!-- Tech Info Card - Horizontal -->
        <div class="tech-info-horizontal">
            <div class="tech-avatar-small">
                <i class="fas fa-user"></i>
            </div>
            <div class="tech-details-horizontal">
                <div class="tech-name-small"><?php echo htmlspecialchars($t_name); ?></div>
                <div class="tech-meta">
                    <a href="my-profile.php" class="tech-id-badge" style="text-decoration: none; cursor: pointer;" title="View Profile">
                        ID: <?php echo htmlspecialchars($t_id_no); ?>
                    </a>
                    <?php if(!empty($t_phone)): ?>
                        <span class="tech-phone-small"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($t_phone); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($t_pincode)): ?>
                        <span class="tech-pin-badge">PIN: <?php echo htmlspecialchars($t_pincode); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tech-actions-horizontal">
                <a href="my-profile.php" class="btn-icon" title="Profile">
                    <i class="fas fa-user-edit"></i>
                </a>
                <a href="logout.php" class="btn-icon btn-logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="control-bar">
        <div class="search-box">
            <form action="" method="GET">
                <input type="search" name="search" placeholder="Search by mobile number..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if($filter != 'all'): ?>
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                <?php endif; ?>
            </form>
        </div>
        
        <a href="?filter=new" class="filter-btn <?php echo $filter == 'new' ? 'active' : ''; ?>">
            New
            <?php if($new_count > 0): ?>
                <span class="badge"><?php echo $new_count; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?filter=pending" class="filter-btn <?php echo $filter == 'pending' ? 'active' : ''; ?>">
            Pending
            <?php if($pending_count > 0): ?>
                <span class="badge"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?filter=completed" class="filter-btn <?php echo $filter == 'completed' ? 'active' : ''; ?>">
            Completed
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

    <!-- Main Container -->
    <div class="main-container-full">
        <!-- Bookings Table -->
        <div class="bookings-section-full">
            <div class="scroll-hint">
                <i class="fas fa-arrows-alt"></i> Scroll to view all data
            </div>
            <?php if($bookings_result->num_rows > 0): ?>
                <!-- Desktop Table View -->
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
                        // Reset result pointer for table
                        $bookings_result->data_seek(0);
                        while($booking = $bookings_result->fetch_object()): 
                            // Extract pincode from customer address
                            $customer_pincode = '';
                            if(!empty($booking->u_addr)) {
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
                            <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                            <td><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></td>
                            <td><?php echo $customer_pincode ? $customer_pincode : '-'; ?></td>
                            <td><?php echo htmlspecialchars(substr($booking->u_addr, 0, 50)) . (strlen($booking->u_addr) > 50 ? '...' : ''); ?></td>
                            <td>
                                <?php if(!empty($booking->u_phone)): ?>
                                    <a href="tel:<?php echo $booking->u_phone; ?>" class="call-btn">
                                        <i class="fas fa-phone"></i> <?php echo $booking->u_phone; ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $booking->sb_status; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($booking->sb_status != 'Completed' && $booking->sb_status != 'Rejected'): ?>
                                    <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>" class="action-btn">
                                        <i class="fas fa-tasks"></i> Action
                                    </a>
                                <?php elseif($booking->sb_status == 'Completed'): ?>
                                    <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>" class="view-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Mobile Card View -->
                <?php 
                // Reset result pointer for cards
                $bookings_result->data_seek(0);
                while($booking = $bookings_result->fetch_object()): 
                    // Extract pincode from customer address
                    $customer_pincode = '';
                    if(!empty($booking->u_addr)) {
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
                    <div class="booking-card-header">
                        <div class="booking-id-mobile">#<?php echo $booking->sb_id; ?></div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $booking->sb_status; ?>
                        </span>
                    </div>
                    
                    <div class="booking-card-body">
                        <div class="booking-info-row">
                            <div class="booking-info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="booking-info-content">
                                <div class="booking-info-label">Customer Name</div>
                                <div class="booking-info-value"><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></div>
                            </div>
                        </div>
                        
                        <div class="booking-info-row">
                            <div class="booking-info-icon">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="booking-info-content">
                                <div class="booking-info-label">Pincode</div>
                                <div class="booking-info-value"><?php echo $customer_pincode ? $customer_pincode : 'Not Available'; ?></div>
                            </div>
                        </div>
                        
                        <div class="booking-info-row">
                            <div class="booking-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="booking-info-content">
                                <div class="booking-info-label">Address</div>
                                <div class="booking-info-value"><?php echo htmlspecialchars($booking->u_addr); ?></div>
                            </div>
                        </div>
                        
                        <?php if(!empty($booking->s_name)): ?>
                        <div class="booking-info-row">
                            <div class="booking-info-icon">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <div class="booking-info-content">
                                <div class="booking-info-label">Service</div>
                                <div class="booking-info-value"><?php echo htmlspecialchars($booking->s_name); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-card-actions">
                        <?php if(!empty($booking->u_phone)): ?>
                            <a href="tel:<?php echo $booking->u_phone; ?>" class="mobile-call-btn">
                                <i class="fas fa-phone"></i> Call
                            </a>
                        <?php endif; ?>
                        
                        <?php if($booking->sb_status != 'Completed' && $booking->sb_status != 'Rejected'): ?>
                            <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>" class="mobile-action-btn">
                                <i class="fas fa-tasks"></i> Take Action
                            </a>
                        <?php elseif($booking->sb_status == 'Completed'): ?>
                            <a href="complete-booking.php?id=<?php echo $booking->sb_id; ?>" class="mobile-view-btn">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        <?php endif; ?>
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
</body>
</html>
