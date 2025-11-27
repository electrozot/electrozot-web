<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Redirect if no booking ID provided
if($sb_id == 0){
    $_SESSION['error'] = "No booking ID provided.";
    header('Location: dashboard.php');
    exit();
}

// Create payment collection table if not exists
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

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, s.s_name, s.s_category, s.s_price
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $sb_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    $_SESSION['error'] = "Booking not found or not assigned to you.";
    header('Location: dashboard.php');
    exit();
}

$booking = $result->fetch_object();

// Check if admin has set a fixed price
$admin_price_set = ($booking->s_price !== null && $booking->s_price > 0);
$display_price = $admin_price_set ? $booking->s_price : ($booking->sb_total_price > 0 ? $booking->sb_total_price : 0);
$price_is_flexible = !$admin_price_set; // Technician can set price if admin hasn't

// Check if payment already collected
$payment_check = $mysqli->prepare("SELECT * FROM tms_payment_collection WHERE pc_booking_id = ?");
$payment_check->bind_param('i', $sb_id);
$payment_check->execute();
$payment_result = $payment_check->get_result();
$payment_collected = $payment_result->num_rows > 0;
$payment_data = $payment_collected ? $payment_result->fetch_object() : null;

// Add payment QR column if not exists
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_payment_qr VARCHAR(255) DEFAULT NULL");

// Get payment QR code (company)
$qr_query = "SELECT * FROM tms_payment_settings WHERE ps_id=1";
$qr_result = $mysqli->query($qr_query);
$qr_settings = $qr_result->fetch_object();

// Get technician's personal QR code
$tech_qr_query = "SELECT t_name, t_payment_qr FROM tms_technician WHERE t_id = ?";
$tech_qr_stmt = $mysqli->prepare($tech_qr_query);
$tech_qr_stmt->bind_param('i', $t_id);
$tech_qr_stmt->execute();
$tech_qr_result = $tech_qr_stmt->get_result();
$tech_data = $tech_qr_result->fetch_object();

// Check if technician has QR code
$tech_has_qr = false;
if($tech_data && !empty($tech_data->t_payment_qr)) {
    $qr_path = "../" . $tech_data->t_payment_qr;
    if(file_exists($qr_path)) {
        $tech_has_qr = true;
    }
}

$success = '';
$error = '';

// Handle Payment Collection
if(isset($_POST['collect_payment'])){
    // If admin set fixed price, use that. Otherwise, use technician's entered amount
    if($admin_price_set) {
        $pc_amount = $display_price;
    } else {
        $pc_amount = floatval($_POST['pc_amount']);
    }
    
    $pc_method = $_POST['pc_method'];
    
    if($pc_amount <= 0) {
        $error = 'Please enter a valid amount greater than 0';
    } else {
        // Insert payment record
        $insert_query = "INSERT INTO tms_payment_collection (pc_booking_id, pc_amount, pc_method, pc_collected_by) VALUES (?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $insert_stmt->bind_param('idsi', $sb_id, $pc_amount, $pc_method, $t_id);
        
        if($insert_stmt->execute()) {
            // Send notification to customer
            $mysqli->query("CREATE TABLE IF NOT EXISTS tms_user_notifications (
                un_id INT AUTO_INCREMENT PRIMARY KEY,
                un_user_id INT NOT NULL,
                un_booking_id INT,
                un_type VARCHAR(50) NOT NULL,
                un_title VARCHAR(255) NOT NULL,
                un_message TEXT NOT NULL,
                un_is_read TINYINT(1) DEFAULT 0,
                un_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (un_user_id),
                INDEX idx_read (un_is_read)
            )");
            
            $user_notif_title = "Payment Received";
            $user_notif_message = "Payment of ₹" . number_format($pc_amount, 2) . " received via " . $pc_method . " for Booking #" . $sb_id;
            $user_notif_type = "PAYMENT_RECEIVED";
            
            $user_notif_stmt = $mysqli->prepare("INSERT INTO tms_user_notifications (un_user_id, un_booking_id, un_type, un_title, un_message) VALUES (?, ?, ?, ?, ?)");
            $user_notif_stmt->bind_param('iisss', $booking->sb_user_id, $sb_id, $user_notif_type, $user_notif_title, $user_notif_message);
            $user_notif_stmt->execute();
            
            // Send notification to admin
            $mysqli->query("CREATE TABLE IF NOT EXISTS tms_admin_notifications (
                an_id INT AUTO_INCREMENT PRIMARY KEY,
                an_type VARCHAR(50) NOT NULL,
                an_title VARCHAR(255) NOT NULL,
                an_message TEXT NOT NULL,
                an_booking_id INT,
                an_technician_id INT,
                an_is_read TINYINT(1) DEFAULT 0,
                an_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_read (an_is_read),
                INDEX idx_booking (an_booking_id)
            )");
            
            $admin_notif_title = "Payment Collected";
            $admin_notif_message = "Technician collected ₹" . number_format($pc_amount, 2) . " via " . $pc_method . " for Booking #" . $sb_id;
            $admin_notif_type = "PAYMENT_COLLECTED";
            
            $admin_notif_stmt = $mysqli->prepare("INSERT INTO tms_admin_notifications (an_type, an_title, an_message, an_booking_id, an_technician_id) VALUES (?, ?, ?, ?, ?)");
            $admin_notif_stmt->bind_param('sssii', $admin_notif_type, $admin_notif_title, $admin_notif_message, $sb_id, $t_id);
            $admin_notif_stmt->execute();
            
            // Store success data in session for modal
            $_SESSION['payment_success'] = true;
            $_SESSION['payment_booking_id'] = $sb_id;
            $_SESSION['payment_amount'] = $pc_amount;
            $_SESSION['payment_method'] = $pc_method;
            
            // Redirect to show success modal
            header('Location: collect-payment.php?id=' . $sb_id . '&success=1');
            exit();
        } else {
            $error = 'Failed to record payment. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collect Payment - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(180deg, #e0f2fe 0%, #f0f9ff 50%, #ffffff 100%);
            min-height: 100vh;
            padding-top: 70px;
            padding-bottom: 100px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            text-decoration: none;
        }
        
        .notif-icon-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.4);
            color: white;
        }
        
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 0 15px 15px 15px;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 15px;
            animation: slideUp 0.4s ease;
            border: 1px solid #e2e8f0;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-header {
            text-align: left;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .card-header h3 {
            color: #1e293b;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .card-header p {
            color: #64748b;
            font-size: 0.85rem;
            margin: 0;
        }
        
        .amount-display {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        .amount-display .label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .amount-display .amount {
            font-size: 2.5rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .booking-details {
            background: #f8fafc;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-row .label {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .detail-row .value {
            color: #1e293b;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .payment-methods-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .payment-method {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
            background: #f8fafc;
        }
        
        .payment-method::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .payment-method:hover::before {
            opacity: 1;
        }
        
        .payment-method:hover {
            border-color: #10b981;
            background: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }
        
        .payment-method.selected {
            border-color: #10b981;
            background: #ecfdf5;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }
        
        .payment-method-disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
            pointer-events: none;
        }
        
        .payment-method-disabled input {
            cursor: not-allowed;
        }
        
        .payment-method input[type="radio"] {
            width: 22px;
            height: 22px;
            accent-color: #667eea;
            cursor: pointer;
        }
        
        .payment-method-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        .payment-method-icon.qr {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        .payment-method-icon.cash {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .payment-method-content {
            flex: 1;
        }
        
        .payment-method-content strong {
            font-size: 1rem;
            color: #1e293b;
            display: block;
            margin-bottom: 3px;
        }
        
        .payment-method-content p {
            margin: 0;
            color: #64748b;
            font-size: 0.8rem;
        }
        
        .qr-code-section {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            margin: 20px 0;
            border: 2px dashed #667eea;
            animation: fadeIn 0.4s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .qr-code-section h5 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .qr-code-section img {
            max-width: 250px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            background: white;
            padding: 10px;
        }
        
        .qr-code-section .business-name {
            margin-top: 15px;
            font-weight: 700;
            color: #1e293b;
            font-size: 1rem;
        }
        
        .qr-code-section .upi-id {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-submit:active:not(:disabled) {
            transform: translateY(-1px);
        }
        
        .btn-submit:disabled {
            background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
            cursor: not-allowed;
            box-shadow: none;
            opacity: 0.6;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
        }
        
        .payment-collected-card {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            padding: 30px 25px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.2);
            border: 2px solid #6ee7b7;
        }
        
        .payment-collected-card .icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            color: #10b981;
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .payment-collected-card h4 {
            font-weight: 700;
            margin-bottom: 10px;
            color: #065f46;
            font-size: 1.3rem;
        }
        
        .payment-collected-card p {
            color: #047857;
            font-size: 0.9rem;
        }
        
        .proceed-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            border-radius: 50px;
            font-weight: 900;
            font-size: 1.05rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            transition: all 0.3s;
        }
        
        .proceed-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 576px) {
            .header {
                padding: 8px 15px;
            }
            
            .logo-image {
                width: 50px;
                height: 50px;
            }
            
            .brand-title {
                font-size: 1.2rem;
            }
            
            .brand-subtitle {
                font-size: 0.65rem;
            }
            
            .container {
                padding: 0 10px 10px 10px;
            }
            
            .card {
                padding: 20px;
                border-radius: 20px;
            }
            
            .amount-display .amount {
                font-size: 2rem;
            }
            
            .payment-method {
                padding: 15px;
            }
            
            .payment-method-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
        }
        
        /* Success Modal Styles */
        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }
        
        .success-modal {
            background: white;
            border-radius: 30px;
            padding: 50px 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,0.6);
            animation: slideUpBounce 0.6s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUpBounce {
            0% {
                opacity: 0;
                transform: translateY(100px) scale(0.8);
            }
            60% {
                opacity: 1;
                transform: translateY(-10px) scale(1.05);
            }
            100% {
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes checkPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.4);
            animation: checkPulse 1s ease infinite;
        }
        
        .success-icon i {
            font-size: 4rem;
            color: white;
        }
        
        .success-modal h2 {
            color: #10b981;
            font-weight: 900;
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .success-modal .amount {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1e293b;
            margin: 20px 0;
        }
        
        .success-modal .method-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            margin: 10px 0 20px;
        }
        
        .success-modal .redirect-text {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 20px;
        }
        
        .success-modal .redirect-text i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Payment Success Modal -->
    <?php if(isset($_GET['success']) && isset($_SESSION['payment_success'])): ?>
    <div class="success-modal-overlay">
        <div class="success-modal">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h2>Payment Collected!</h2>
            
            <div class="amount">
                <i class="fas fa-rupee-sign"></i><?php echo number_format($_SESSION['payment_amount'], 2); ?>
            </div>
            
            <div class="method-badge" style="
                <?php 
                $method = $_SESSION['payment_method'];
                if($method == 'QR') {
                    echo 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;';
                } elseif($method == 'TechQR') {
                    echo 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;';
                } else {
                    echo 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;';
                }
                ?>
            ">
                <?php 
                if($method == 'QR') {
                    echo '<i class="fas fa-qrcode"></i> Company QR';
                } elseif($method == 'TechQR') {
                    echo '<i class="fas fa-user-circle"></i> Technician QR';
                } else {
                    echo '<i class="fas fa-money-bill-wave"></i> Cash Payment';
                }
                ?>
            </div>
            
            <p style="color: #10b981; font-weight: 700; font-size: 1.1rem; margin: 15px 0;">
                <i class="fas fa-check-circle"></i> Payment Successfully Recorded
            </p>
            
            <div class="redirect-text">
                <i class="fas fa-spinner"></i> Redirecting to complete service...
            </div>
        </div>
    </div>
    
    <script>
        // Auto redirect after 2 seconds
        setTimeout(function() {
            window.location.href = 'complete-booking.php?id=<?php echo $_SESSION['payment_booking_id']; ?>&action=done';
        }, 2000);
    </script>
    
    <?php 
    // Clear session variables
    unset($_SESSION['payment_success']);
    unset($_SESSION['payment_booking_id']);
    unset($_SESSION['payment_amount']);
    unset($_SESSION['payment_method']);
    ?>
    <?php endif; ?>
    
    <!-- Header -->
    <div class="header">
        <a href="dashboard.php" class="logo-section">
            <div class="logo-image">
                <img src="../vendor/EZlogonew.png" alt="EZ">
            </div>
            <div class="brand-info">
                <div class="brand-title">ELECTROZOT</div>
                <div class="brand-subtitle">We Make Perfect</div>
            </div>
        </a>
        <div class="header-actions">
            <a href="dashboard.php" class="notif-icon-btn" title="Dashboard">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </div>
    
    <div class="container">
        <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3>💰 Collect Payment</h3>
                <p>Secure payment collection from customer</p>
            </div>
            
            <?php if($admin_price_set): ?>
            <div class="amount-display">
                <div class="label">Fixed Amount (Set by Admin)</div>
                <div class="amount">
                    <i class="fas fa-rupee-sign"></i><?php echo number_format($display_price, 2); ?>
                </div>
                <div style="font-size: 0.75rem; opacity: 0.9; margin-top: 5px;">
                    <i class="fas fa-lock"></i> This price cannot be changed
                </div>
            </div>
            <?php else: ?>
            <div class="amount-display" id="flexiblePriceDisplay" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: #0c4a6e; border: 2px solid #7dd3fc;">
                <div class="label" style="color: #0c4a6e;">Enter Service Amount</div>
                <div class="amount" id="enteredAmount" style="display: none; color: #0c4a6e;">
                    <i class="fas fa-rupee-sign"></i><span id="amountValue">0.00</span>
                </div>
                <div id="amountPlaceholder" style="font-size: 0.85rem; margin-top: 5px; color: #0369a1;">
                    <i class="fas fa-info-circle"></i> Price not fixed - Enter amount below
                </div>
            </div>
            <?php endif; ?>
            
            <div class="booking-details">
                <div class="detail-row">
                    <span class="label">Booking ID</span>
                    <span class="value">#<?php echo $sb_id; ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Customer</span>
                    <span class="value"><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Service</span>
                    <span class="value"><?php echo htmlspecialchars($booking->s_name); ?></span>
                </div>
            </div>
            
            <?php if($payment_collected): ?>
                <div class="payment-collected-card" style="animation: slideUp 0.5s ease;">
                    <div class="icon" style="animation: scaleIn 0.6s ease;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4 style="font-size: 1.8rem; margin-bottom: 15px;">Payment Collected Successfully!</h4>
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 15px; margin: 20px 0;">
                        <p style="font-size: 1.3rem; font-weight: 900; margin: 0;">
                            ₹<?php echo number_format($payment_data->pc_amount, 2); ?>
                        </p>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">
                            via 
                            <?php 
                            if($payment_data->pc_method == 'QR') {
                                echo '<span style="background: white; color: #667eea; padding: 4px 12px; border-radius: 20px; font-weight: 700;">Company QR</span>';
                            } elseif($payment_data->pc_method == 'TechQR') {
                                echo '<span style="background: white; color: #f59e0b; padding: 4px 12px; border-radius: 20px; font-weight: 700;">Tech QR</span>';
                            } else {
                                echo '<span style="background: white; color: #10b981; padding: 4px 12px; border-radius: 20px; font-weight: 700;">Cash</span>';
                            }
                            ?>
                        </p>
                    </div>
                    <p style="font-size: 0.95rem; opacity: 0.9; margin-top: 15px;">
                        <i class="fas fa-spinner fa-spin"></i> Redirecting to complete service...
                    </p>
                </div>
                
                <script>
                    // Auto-redirect after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'complete-booking.php?id=<?php echo $sb_id; ?>&action=done';
                    }, 2000);
                </script>
            <?php else: ?>
                <form method="POST" id="paymentForm">
                    <?php if($admin_price_set): ?>
                        <input type="hidden" name="pc_amount" value="<?php echo $display_price; ?>">
                    <?php else: ?>
                        <!-- Technician enters amount -->
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="font-weight: 700; color: #1e293b; margin-bottom: 10px; display: block;">
                                <i class="fas fa-rupee-sign"></i> Enter Service Amount (₹)
                            </label>
                            <input type="number" name="pc_amount" id="amountInput" class="form-control" 
                                   placeholder="Enter total amount (parts + labor)" 
                                   step="0.01" min="1" required
                                   style="border: 3px solid #7dd3fc; border-radius: 15px; padding: 15px; font-size: 1.2rem; font-weight: 700; text-align: center; background: #f0f9ff;"
                                   oninput="updateAmountDisplay(this.value)">
                            <small style="color: #64748b; display: block; margin-top: 8px;">
                                <i class="fas fa-info-circle"></i> Calculate based on parts cost + labor charges
                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <div class="payment-methods-title">
                        <i class="fas fa-hand-holding-usd"></i> Choose Payment Method
                        <?php if(!$admin_price_set): ?>
                        <span id="methodLockMessage" style="color: #dc3545; font-size: 0.8rem; font-weight: 600;">
                            (Enter amount first)
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="payment-method <?php if(!$admin_price_set): ?>payment-method-disabled<?php endif; ?>" id="method_qr_container" onclick="selectMethod('QR')">
                        <input type="radio" name="pc_method" value="QR" id="method_qr" required <?php if(!$admin_price_set): ?>disabled<?php endif; ?>>
                        <div class="payment-method-icon qr">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div class="payment-method-content">
                            <strong>Company QR Code</strong>
                            <p>Electrozot official payment QR</p>
                        </div>
                    </div>
                    
                    <?php if($tech_has_qr): ?>
                    <div class="payment-method <?php if(!$admin_price_set): ?>payment-method-disabled<?php endif; ?>" id="method_techqr_container" onclick="selectMethod('TechQR')">
                        <input type="radio" name="pc_method" value="TechQR" id="method_techqr" required <?php if(!$admin_price_set): ?>disabled<?php endif; ?>>
                        <div class="payment-method-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="payment-method-content">
                            <strong><?php echo htmlspecialchars($tech_data->t_name); ?> QR</strong>
                            <p>Technician's personal payment QR</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="payment-method <?php if(!$admin_price_set): ?>payment-method-disabled<?php endif; ?>" id="method_cash_container" onclick="selectMethod('Cash')">
                        <input type="radio" name="pc_method" value="Cash" id="method_cash" required <?php if(!$admin_price_set): ?>disabled<?php endif; ?>>
                        <div class="payment-method-icon cash">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="payment-method-content">
                            <strong>Cash Payment</strong>
                            <p>Collect cash directly from customer</p>
                        </div>
                    </div>
                    
                    <div id="qrCodeSection" style="display: none;">
                        <?php if(!empty($qr_settings->ps_qr_code) && file_exists("../" . $qr_settings->ps_qr_code)): ?>
                            <div class="qr-code-section">
                                <h5>
                                    <i class="fas fa-building"></i> Company QR Code
                                </h5>
                                <img src="../<?php echo $qr_settings->ps_qr_code; ?>" alt="Payment QR">
                                <p class="business-name"><?php echo htmlspecialchars($qr_settings->ps_business_name); ?></p>
                                <?php if(!empty($qr_settings->ps_upi_id)): ?>
                                <p class="upi-id"><?php echo htmlspecialchars($qr_settings->ps_upi_id); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> Company QR Code not configured. Please contact admin.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($tech_has_qr): ?>
                    <div id="techQrCodeSection" style="display: none;">
                        <div class="qr-code-section" style="border-color: #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
                            <h5 style="color: #d97706;">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($tech_data->t_name); ?>'s QR
                            </h5>
                            <img src="../<?php echo $tech_data->t_payment_qr; ?>" alt="Technician Payment QR">
                            <p class="business-name" style="color: #92400e;">Personal Payment QR</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" name="collect_payment" class="btn-submit" id="submitBtn" disabled>
                        <i class="fas fa-check-circle"></i> Confirm Payment Received
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Update amount display when technician enters price
        function updateAmountDisplay(value) {
            const amount = parseFloat(value);
            const amountDisplay = document.getElementById('enteredAmount');
            const amountValue = document.getElementById('amountValue');
            const placeholder = document.getElementById('amountPlaceholder');
            const methodLockMessage = document.getElementById('methodLockMessage');
            
            if(amount > 0) {
                // Show entered amount in pink box
                amountValue.textContent = amount.toFixed(2);
                amountDisplay.style.display = 'flex';
                placeholder.style.display = 'none';
                
                // Enable payment methods
                document.querySelectorAll('.payment-method-disabled').forEach(el => {
                    el.classList.remove('payment-method-disabled');
                    el.style.pointerEvents = 'auto';
                    el.style.opacity = '1';
                    el.style.cursor = 'pointer';
                });
                
                // Enable radio buttons
                document.getElementById('method_qr').disabled = false;
                <?php if($tech_has_qr): ?>
                document.getElementById('method_techqr').disabled = false;
                <?php endif; ?>
                document.getElementById('method_cash').disabled = false;
                
                // Hide lock message
                if(methodLockMessage) {
                    methodLockMessage.style.display = 'none';
                }
            } else {
                // Hide amount, show placeholder
                amountDisplay.style.display = 'none';
                placeholder.style.display = 'block';
                
                // Disable payment methods
                document.querySelectorAll('.payment-method').forEach(el => {
                    if(!el.classList.contains('payment-method-disabled')) {
                        el.classList.add('payment-method-disabled');
                        el.style.pointerEvents = 'none';
                        el.style.opacity = '0.5';
                        el.style.cursor = 'not-allowed';
                    }
                });
                
                // Disable radio buttons
                document.getElementById('method_qr').disabled = true;
                <?php if($tech_has_qr): ?>
                document.getElementById('method_techqr').disabled = true;
                <?php endif; ?>
                document.getElementById('method_cash').disabled = true;
                
                // Show lock message
                if(methodLockMessage) {
                    methodLockMessage.style.display = 'inline';
                }
                
                // Disable submit button
                document.getElementById('submitBtn').disabled = true;
            }
        }
        
        function selectMethod(method) {
            // Check if payment methods are enabled
            const methodElement = document.getElementById('method_' + method.toLowerCase());
            if(methodElement.disabled) {
                return; // Don't allow selection if disabled
            }
            
            methodElement.checked = true;
            document.getElementById('submitBtn').disabled = false;
            
            // Update visual selection
            document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
            
            // Hide all QR sections first
            document.getElementById('qrCodeSection').style.display = 'none';
            <?php if($tech_has_qr): ?>
            document.getElementById('techQrCodeSection').style.display = 'none';
            <?php endif; ?>
            
            // Show appropriate QR code
            if(method === 'QR') {
                document.getElementById('qrCodeSection').style.display = 'block';
            } else if(method === 'TechQR') {
                <?php if($tech_has_qr): ?>
                document.getElementById('techQrCodeSection').style.display = 'block';
                <?php endif; ?>
            }
        }
        
        // Enable button when method selected
        document.querySelectorAll('input[name="pc_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if(!this.disabled) {
                    document.getElementById('submitBtn').disabled = false;
                }
            });
        });
    </script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
