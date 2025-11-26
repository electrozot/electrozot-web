<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "My Profile";

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
$t_pic = isset($tech_data->t_pic) ? $tech_data->t_pic : '';
$t_category = isset($tech_data->t_category) ? $tech_data->t_category : '';
$t_experience = isset($tech_data->t_experience) ? $tech_data->t_experience : '';
$t_specialization = isset($tech_data->t_specialization) ? $tech_data->t_specialization : '';
$t_service_pincode = isset($tech_data->t_service_pincode) ? $tech_data->t_service_pincode : '';

// Extract pincode from address (for display)
$t_pincode = '';
if(!empty($t_addr)) {
    preg_match('/\b\d{6}\b/', $t_addr, $matches);
    if(!empty($matches)) {
        $t_pincode = $matches[0];
    }
}

// Get current month statistics
$current_month = date('Y-m');
$month_start = $current_month . '-01';
$month_end = date('Y-m-t');

// Total orders this month
$orders_query = "SELECT COUNT(*) as total_orders FROM tms_service_booking 
                 WHERE sb_technician_id = ? 
                 AND DATE(sb_booking_date) BETWEEN ? AND ?";
$stmt_orders = $mysqli->prepare($orders_query);
$stmt_orders->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
$orders_data = $orders_result->fetch_object();
$total_orders = $orders_data->total_orders;

// Completed orders this month
$completed_query = "SELECT COUNT(*) as completed_orders FROM tms_service_booking 
                    WHERE sb_technician_id = ? 
                    AND sb_status = 'Completed'
                    AND DATE(sb_booking_date) BETWEEN ? AND ?";
$stmt_completed = $mysqli->prepare($completed_query);
$stmt_completed->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_completed->execute();
$completed_result = $stmt_completed->get_result();
$completed_data = $completed_result->fetch_object();
$completed_orders = $completed_data->completed_orders;

// Calculate earnings (completed orders * service price)
$earnings_query = "SELECT SUM(s.s_price) as total_earnings 
                   FROM tms_service_booking sb
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_technician_id = ? 
                   AND sb.sb_status = 'Completed'
                   AND DATE(sb_booking_date) BETWEEN ? AND ?";
$stmt_earnings = $mysqli->prepare($earnings_query);
$stmt_earnings->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_earnings->execute();
$earnings_result = $stmt_earnings->get_result();
$earnings_data = $earnings_result->fetch_object();
$total_earnings = $earnings_data->total_earnings ? $earnings_data->total_earnings : 0;

// Count services in service pincode area
$services_in_pincode = 0;
$display_pincode = !empty($t_service_pincode) ? $t_service_pincode : $t_pincode;

if(!empty($display_pincode)) {
    $pincode_query = "SELECT COUNT(*) as pincode_services 
                      FROM tms_service_booking sb
                      LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                      WHERE sb.sb_technician_id = ? 
                      AND u.u_addr LIKE ?";
    $stmt_pincode = $mysqli->prepare($pincode_query);
    $pincode_param = "%{$display_pincode}%";
    $stmt_pincode->bind_param('is', $t_id, $pincode_param);
    $stmt_pincode->execute();
    $pincode_result = $stmt_pincode->get_result();
    $pincode_data = $pincode_result->fetch_object();
    $services_in_pincode = $pincode_data->pincode_services;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 80px;
            padding-bottom: 20px;
            position: relative;
            overflow-x: hidden;
        }
        

        
        /* Header */
        .header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 8px 20px;
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
            height: 70px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        
        .logo-image {
            width: 55px;
            height: 55px;
            background: transparent;
            border-radius: 8px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }
        
        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            justify-content: center;
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 900;
            color: white;
            margin: 0;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
            margin: 0;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
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
        
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 15px;
            position: relative;
            z-index: 1;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        

        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .profile-header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 30px 25px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }
        
        .service-pincode-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            display: block;
            margin-bottom: 10px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .service-pincode-badge:hover {
            background: rgba(255,255,255,0.25);
        }
        
        .service-pincode-badge i {
            margin-right: 5px;
        }
        
        .profile-main {
            display: flex;
            align-items: flex-start;
            gap: 25px;
            position: relative;
            z-index: 2;
        }
        
        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 15px;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            object-fit: cover;
            background: white;
            transition: all 0.3s;
        }
        
        @keyframes photoZoom {
            from {
                opacity: 0;
                transform: scale(0.8) rotate(-5deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }
        
        .profile-photo:hover {
            transform: scale(1.08) rotate(2deg);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }
        
        .profile-photo-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 15px;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            transition: all 0.3s;
        }
        
        .profile-photo-placeholder:hover {
            transform: scale(1.08) rotate(-2deg);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }
        
        .profile-photo-placeholder i {
            animation: iconFloat 3s ease-in-out infinite;
        }
        
        @keyframes iconFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-info h2 {
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 10px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .tech-id-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            display: block;
            margin-bottom: 10px;
            font-size: 0.85rem;
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        
        .tech-id-badge:hover {
            background: rgba(255,255,255,0.25);
        }
        
        .tech-id-badge i {
            margin-right: 5px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .contact-item:hover {
            background: rgba(255,255,255,0.25);
        }
        
        .contact-item i {
            font-size: 1rem;
        }
        
        .profile-actions {
            padding: 20px 25px;
            background: #f8f9fa;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            border-top: 2px solid #e2e8f0;
        }
        
        .action-btn {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .action-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .action-btn i {
            margin-right: 8px;
            transition: transform 0.3s;
        }
        
        .action-btn:hover i {
            transform: scale(1.2) rotate(5deg);
        }
        
        .btn-change-password {
            background: linear-gradient(135deg, #4299e1 0%, #667eea 100%);
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .btn-change-password:hover {
            background: linear-gradient(135deg, #3182ce 0%, #5a67d8 100%);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 30px rgba(66, 153, 225, 0.5);
            text-decoration: none;
            color: white;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 30px rgba(245, 101, 101, 0.5);
            text-decoration: none;
            color: white;
        }
        
        .btn-call-admin {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .btn-call-admin:hover {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 30px rgba(72, 187, 120, 0.5);
            text-decoration: none;
            color: white;
        }
        
        .btn-whatsapp-admin {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .btn-whatsapp-admin:hover {
            background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 30px rgba(37, 211, 102, 0.5);
            text-decoration: none;
            color: white;
        }
        
        .profile-details {
            padding: 25px;
            background: white;
        }
        
        .detail-row {
            display: flex;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .detail-row:nth-child(1) { animation-delay: 0.1s; }
        .detail-row:nth-child(2) { animation-delay: 0.2s; }
        .detail-row:nth-child(3) { animation-delay: 0.3s; }
        .detail-row:nth-child(4) { animation-delay: 0.4s; }
        .detail-row:nth-child(5) { animation-delay: 0.5s; }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .detail-row:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .detail-label {
            font-weight: 700;
            color: #555;
            width: 180px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        
        .detail-label i {
            color: #667eea;
            width: 25px;
            font-size: 1.1rem;
            transition: transform 0.3s;
        }
        
        .detail-row:hover .detail-label i {
            transform: scale(1.2) rotate(5deg);
        }
        
        .detail-value {
            color: #2d3748;
            flex: 1;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .stats-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stats-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stats-header h3 {
            font-size: 1.5rem;
            font-weight: 900;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .stats-header p {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f85959ff 0%, #fc9484ff 100%);
            padding: 20px 15px;
            border-radius: 15px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.8s; }
        .stat-card:nth-child(2) { animation-delay: 0.9s; }
        .stat-card:nth-child(3) { animation-delay: 1s; }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transition: all 0.6s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .stat-card.stat-orders {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.stat-earnings {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            opacity: 0.95;
        }
        
        @keyframes iconBounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-label {
            font-size: 0.85rem;
            font-weight: 700;
            opacity: 0.95;
            position: relative;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .profile-main {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-info h2 {
                font-size: 1.8rem;
            }
            
            .contact-info {
                justify-content: center;
            }
            
            .profile-actions {
                flex-direction: column;
            }
            
            .action-btn {
                width: 100%;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .detail-label {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <a href="dashboard.php" class="logo-section">
            <div class="logo-image">
                <img src="../vendor/EZlogonew.png" alt="EZ">
            </div>
            <div class="brand-info">
                <h1 class="brand-title">Electrozot</h1>
                <p class="brand-subtitle">We Make Perfect</p>
            </div>
        </a>
        
        <div class="header-actions">
            <button class="notif-icon-btn" onclick="window.location.href='notifications.php'">
                <i class="fas fa-bell"></i>
            </button>
        </div>
    </div>

    <div class="profile-container">
        <!-- Profile Card -->
        <div class="profile-card">
            <!-- Header Section -->
            <div class="profile-header">
                <div class="profile-main">
                    <div>
                        <?php if(!empty($t_pic)): ?>
                            <img src="../vendor/img/<?php echo htmlspecialchars($t_pic); ?>" class="profile-photo" alt="Profile Photo">
                        <?php else: ?>
                            <div class="profile-photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($t_name); ?></h2>
                        
                        <div class="service-pincode-badge">
                            <i class="fas fa-map-marker-alt"></i> 
                            Service Area: <?php echo $display_pincode ? $display_pincode : 'Not Set'; ?> 
                            <span class="badge badge-light ml-1"><?php echo $services_in_pincode; ?> Services</span>
                        </div>
                        
                        <div class="tech-id-badge">
                            <i class="fas fa-id-card"></i> Technician ID: <?php echo htmlspecialchars($t_id_no); ?>
                        </div>
                        
                        <?php if(!empty($t_phone)): ?>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($t_phone); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($t_email)): ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($t_email); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="profile-actions">
                <a href="tel:7559606925" class="action-btn btn-call-admin">
                    <i class="fas fa-phone-alt"></i> Call Admin
                </a>
                <a href="https://wa.me/917559606925?text=Hi%20Admin,%20I%20am%20<?php echo urlencode($t_name); ?>%20(ID:%20<?php echo urlencode($t_id_no); ?>).%20I%20need%20assistance." target="_blank" class="action-btn btn-whatsapp-admin">
                    <i class="fab fa-whatsapp"></i> WhatsApp Admin
                </a>
                <a href="change-password.php" class="action-btn btn-change-password">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="logout.php" class="action-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            <!-- Profile Details -->
            <div class="profile-details">
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-briefcase"></i>
                        Category
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_category); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-star"></i>
                        Specialization
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_specialization); ?></div>
                </div>
                
                <?php if(!empty($t_service_pincode)): ?>
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-map-pin"></i>
                        Service Pincode
                    </div>
                    <div class="detail-value">
                        <span style="background: linear-gradient(135deg, #4299e1, #667eea); color: white; padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 1.1rem;">
                            <?php echo htmlspecialchars($t_service_pincode); ?>
                        </span>
                        <small style="display: block; margin-top: 8px; color: #666;">
                            <i class="fas fa-info-circle"></i> This is your designated service area
                        </small>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-clock"></i>
                        Experience
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_experience); ?> years</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Address
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_addr); ?></div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="stats-section">
            <div class="stats-header">
                <h3>
                    <i class="fas fa-chart-line"></i> Data of This Month
                </h3>
                <p><?php echo date('F Y'); ?></p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card stat-orders">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_orders; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                
                <div class="stat-card stat-earnings">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-value">₹<?php echo number_format($total_earnings, 0); ?></div>
                    <div class="stat-label">Earnings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $completed_orders; ?></div>
                    <div class="stat-label">Completed Orders</div>
                </div>
            </div>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
