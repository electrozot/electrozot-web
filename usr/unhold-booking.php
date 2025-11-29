<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$u_id = $_SESSION['u_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get booking details
$query = "SELECT sb.*, t.t_id, t.t_name 
          FROM tms_service_booking sb
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          WHERE sb.sb_id = ? AND sb.sb_user_id = ? AND sb.sb_is_on_hold = 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $u_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header("Location: user-dashboard.php?error=invalid_booking");
    exit;
}

$booking = $result->fetch_object();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update booking - remove hold and mark as high priority
    $update_booking = "UPDATE tms_service_booking 
                      SET sb_is_on_hold = 0,
                          sb_hold_reason = NULL,
                          sb_hold_start_date = NULL,
                          sb_hold_end_date = NULL,
                          sb_is_high_priority = 1,
                          sb_priority_reason = 'Customer unholded booking - requires immediate attention',
                          sb_status = 'In Progress'
                      WHERE sb_id = ?";
    $stmt_update = $mysqli->prepare($update_booking);
    $stmt_update->bind_param('i', $booking_id);
    
    if($stmt_update->execute()) {
        // Notify technician
        $notif_title = "🔥 HIGH PRIORITY - Booking #" . $booking_id . " Unholded";
        $notif_message = "Customer has unholded Booking #" . $booking_id . ". This booking is now marked as HIGH PRIORITY and requires immediate attention. Please contact the customer and complete the service as soon as possible.";
        
        $insert_notif = "INSERT INTO tms_technician_notifications 
                        (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                        VALUES (?, ?, 'booking_unholded', ?, ?)";
        $stmt_notif = $mysqli->prepare($insert_notif);
        $stmt_notif->bind_param('iiss', $booking->t_id, $booking_id, $notif_title, $notif_message);
        $stmt_notif->execute();
        
        // Notify admin about unhold action
        try {
            // Create admin notifications table if not exists
            $create_admin_notif = "CREATE TABLE IF NOT EXISTS tms_admin_notifications (
                an_id INT AUTO_INCREMENT PRIMARY KEY,
                an_booking_id INT NOT NULL,
                an_type VARCHAR(50) NOT NULL,
                an_title VARCHAR(255) NOT NULL,
                an_message TEXT NOT NULL,
                an_is_read TINYINT(1) DEFAULT 0,
                an_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX(an_booking_id),
                INDEX(an_is_read)
            )";
            $mysqli->query($create_admin_notif);
            
            // Insert admin notification
            $admin_notif_title = "Booking #" . $booking_id . " Unholded by Customer";
            $admin_notif_message = "Customer has resumed Booking #" . $booking_id . " which was on hold. Booking is now marked as HIGH PRIORITY for technician " . $booking->t_name . ".";
            
            $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                                   (an_booking_id, an_type, an_title, an_message) 
                                   VALUES (?, 'booking_unholded', ?, ?)";
            $stmt_admin = $mysqli->prepare($insert_admin_notif);
            $stmt_admin->bind_param('iss', $booking_id, $admin_notif_title, $admin_notif_message);
            $stmt_admin->execute();
        } catch(Exception $e) {
            // Admin notification failed, but continue anyway
        }
        
        // Log activity (optional)
        try {
            $log_query = "INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                         VALUES (?, ?, ?, ?, 'customer')";
            $log_email = "customer_" . $u_id;
            $log_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $log_details = "Booking Unholded - #$booking_id (High Priority)";
            $stmt_log = $mysqli->prepare($log_query);
            $stmt_log->bind_param('ssss', $log_email, $log_ip, $log_details, $log_details);
            $stmt_log->execute();
        } catch(Exception $e) {
            // Logging failed, continue anyway
        }
        
        header("Location: user-track-booking.php?id=" . $booking_id . "&success=booking_unholded");
        exit;
    } else {
        $error = "Failed to unhold booking. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unhold Booking - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00c853 0%, #00F260 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .unhold-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .unhold-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .unhold-header i {
            font-size: 4rem;
            color: #00c853;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .unhold-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .booking-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #00c853;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
            font-weight: 700;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .warning-box h4 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .warning-box ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }
        
        .warning-box li {
            margin-bottom: 8px;
        }
        
        .priority-badge {
            background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        .priority-badge i {
            margin-right: 8px;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-unhold {
            flex: 1;
            background: linear-gradient(135deg, #00c853 0%, #00F260 100%);
            color: white;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-unhold:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 200, 83, 0.4);
        }
        
        .btn-cancel {
            flex: 1;
            background: #6c757d;
            color: white;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: block;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .hold-info {
            background: #e8f4f8;
            border: 2px solid #0575E6;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .hold-info p {
            margin: 0;
            color: #0575E6;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="unhold-container">
        <div class="unhold-header">
            <i class="fas fa-play-circle"></i>
            <h2>Resume Booking</h2>
            <p style="color: #666;">Unhold and mark as high priority</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="hold-info">
            <p><i class="fas fa-pause-circle"></i> This booking is currently on hold since <?php echo date('M d, Y', strtotime($booking->sb_hold_start_date)); ?></p>
        </div>
        
        <div class="booking-info">
            <div class="info-item">
                <span class="info-label">Booking ID:</span>
                <span class="info-value">#<?php echo $booking->sb_id; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Technician:</span>
                <span class="info-value"><?php echo htmlspecialchars($booking->t_name); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Hold Reason:</span>
                <span class="info-value"><?php echo htmlspecialchars($booking->sb_hold_reason); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Hold Until:</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($booking->sb_hold_end_date)); ?></span>
            </div>
        </div>
        
        <div class="priority-badge">
            <i class="fas fa-fire"></i> This booking will be marked as HIGH PRIORITY
        </div>
        
        <div class="warning-box">
            <h4><i class="fas fa-info-circle"></i> What happens when you unhold?</h4>
            <ul>
                <li>✅ Booking will be immediately resumed</li>
                <li>🔥 Marked as HIGH PRIORITY for technician</li>
                <li>📢 Technician will receive instant notification</li>
                <li>⚡ Booking will appear at top of technician's dashboard</li>
                <li>📞 Technician will contact you to schedule completion</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="btn-group">
                <a href="track-booking.php?id=<?php echo $booking_id; ?>" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
                <button type="submit" class="btn-unhold" onclick="return confirm('Are you sure you want to unhold this booking? The technician will be notified immediately.')">
                    <i class="fas fa-play-circle"></i> Unhold Now
                </button>
            </div>
        </form>
    </div>
</body>
</html>
