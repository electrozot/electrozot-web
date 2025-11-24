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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 15px;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
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
        
        .back-btn {
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .back-btn:hover {
            background: white;
            text-decoration: none;
            color: #764ba2;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }
        
        .back-btn i {
            transition: transform 0.3s;
        }
        
        .back-btn:hover i {
            transform: translateX(-5px);
        }
        
        .profile-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            margin-bottom: 30px;
            animation: slideIn 0.8s ease-out 0.2s both;
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            padding: 50px 40px;
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
            padding: 15px 30px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 25px;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            }
            50% {
                box-shadow: 0 8px 35px rgba(0,0,0,0.15);
            }
        }
        
        .service-pincode-badge:hover {
            transform: scale(1.05);
            background: rgba(255,255,255,0.25);
        }
        
        .service-pincode-badge h5 {
            margin: 0;
            font-weight: 800;
            font-size: 1.2rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .service-pincode-badge .badge {
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-3px);
            }
        }
        
        .profile-main {
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
            z-index: 2;
        }
        
        .profile-photo {
            width: 160px;
            height: 160px;
            border-radius: 25px;
            border: 6px solid white;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            object-fit: cover;
            background: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: photoZoom 0.8s ease-out 0.4s both;
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
            width: 160px;
            height: 160px;
            border-radius: 25px;
            border: 6px solid white;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4.5rem;
            color: white;
            transition: all 0.4s;
            animation: photoZoom 0.8s ease-out 0.4s both;
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
        
        .profile-info h2 {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 15px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideInRight 0.8s ease-out 0.6s both;
            letter-spacing: -1px;
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
            background: rgba(255,255,255,0.3);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 900;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 1.15rem;
            border: 2px solid rgba(255,255,255,0.4);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            animation: slideInRight 0.8s ease-out 0.7s both;
            transition: all 0.3s;
        }
        
        .tech-id-badge:hover {
            transform: scale(1.05);
            background: rgba(255,255,255,0.4);
        }
        
        .contact-info {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.2);
            padding: 12px 25px;
            border-radius: 50px;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeIn 0.8s ease-out 0.8s both;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .contact-item:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.3);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .contact-item i {
            font-size: 1.3rem;
            animation: iconPulse 2s ease-in-out infinite;
        }
        
        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .profile-actions {
            padding: 35px 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            border-top: 3px solid #e2e8f0;
        }
        
        .action-btn {
            padding: 18px 35px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            font-size: 1.05rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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
            padding: 45px;
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
        }
        
        .detail-row {
            display: flex;
            padding: 25px;
            margin-bottom: 15px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideInLeft 0.6s ease-out both;
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
            width: 220px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.05rem;
        }
        
        .detail-label i {
            color: #667eea;
            width: 30px;
            font-size: 1.3rem;
            transition: transform 0.3s;
        }
        
        .detail-row:hover .detail-label i {
            transform: scale(1.2) rotate(5deg);
        }
        
        .detail-value {
            color: #2d3748;
            flex: 1;
            font-weight: 600;
            font-size: 1.05rem;
        }
        
        .stats-section {
            background: white;
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            animation: slideIn 0.8s ease-out 0.4s both;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stats-header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeInDown 0.8s ease-out 0.6s both;
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
            font-size: 2.3rem;
            font-weight: 900;
            color: #2d3748;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stats-header p {
            color: #718096;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f85959ff 0%, #fc9484ff 100%);
            padding: 40px 30px;
            border-radius: 25px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            animation: scaleIn 0.6s ease-out both;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        
        .stat-card:hover::before {
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
        }
        
        .stat-card.stat-orders {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.stat-earnings {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            opacity: 0.95;
            animation: iconBounce 2s ease-in-out infinite;
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
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
            text-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: countUp 1s ease-out;
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
            font-size: 1.2rem;
            font-weight: 700;
            opacity: 0.95;
            position: relative;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 1px;
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
    <div class="profile-container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <!-- Profile Card -->
        <div class="profile-card">
            <!-- Header Section -->
            <div class="profile-header">
                <div class="service-pincode-badge">
                    <h5>
                        <i class="fas fa-map-marker-alt"></i> 
                        Service Area Pincode: <?php echo $display_pincode ? $display_pincode : 'Not Set'; ?> 
                        <span class="badge badge-light ml-2"><?php echo $services_in_pincode; ?> Services</span>
                    </h5>
                </div>
                
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
                        <div class="tech-id-badge">
                            Technician ID: <?php echo htmlspecialchars($t_id_no); ?>
                        </div>
                        
                        <div class="contact-info">
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
