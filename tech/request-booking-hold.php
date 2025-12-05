<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, u.u_id, s.s_name 
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header("Location: dashboard.php?error=invalid_booking");
    exit;
}

$booking = $result->fetch_object();

// Check if already on hold
if($booking->sb_is_on_hold == 1) {
    header("Location: dashboard.php?error=already_on_hold");
    exit;
}

// Check if there's a pending hold request
$check_pending = "SELECT * FROM tms_booking_hold_requests 
                  WHERE bhr_booking_id = ? AND bhr_status = 'Pending'";
$stmt_check = $mysqli->prepare($check_pending);
$stmt_check->bind_param('i', $booking_id);
$stmt_check->execute();
$pending_result = $stmt_check->get_result();

if($pending_result->num_rows > 0) {
    header("Location: dashboard.php?error=hold_request_pending");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = trim($_POST['reason']);
    
    if(empty($reason)) {
        $error = "Please provide a reason for holding the booking";
    } else {
        // Insert hold request
        $insert_query = "INSERT INTO tms_booking_hold_requests 
                        (bhr_booking_id, bhr_technician_id, bhr_reason, bhr_status) 
                        VALUES (?, ?, ?, 'Pending')";
        $stmt_insert = $mysqli->prepare($insert_query);
        $stmt_insert->bind_param('iis', $booking_id, $t_id, $reason);
        
        if($stmt_insert->execute()) {
            // Send notification to customer
            $notif_title = "Hold Request for Booking #" . $booking_id;
            $notif_message = "Your technician has requested to hold your booking temporarily. Reason: " . $reason . ". Please approve or reject this request.";
            
            $insert_notif = "INSERT INTO tms_customer_notifications 
                            (cn_user_id, cn_booking_id, cn_type, cn_title, cn_message, cn_action_required, cn_action_url) 
                            VALUES (?, ?, 'hold_request', ?, ?, 1, 'track-booking.php?id=')";
            $stmt_notif = $mysqli->prepare($insert_notif);
            $stmt_notif->bind_param('iiss', $booking->u_id, $booking_id, $notif_title, $notif_message);
            $stmt_notif->execute();
            
            // Log system activity (optional - skip if table structure doesn't match)
            try {
                $log_query = "INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                             VALUES (?, ?, ?, ?, 'technician')";
                $log_email = "tech_" . $t_id;
                $log_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $log_details = "Hold Request - Booking #$booking_id";
                $stmt_log = $mysqli->prepare($log_query);
                $stmt_log->bind_param('ssss', $log_email, $log_ip, $log_details, $log_details);
                $stmt_log->execute();
            } catch(Exception $e) {
                // Logging failed, but continue anyway
            }
            
            header("Location: dashboard.php?success=hold_requested");
            exit;
        } else {
            $error = "Failed to submit hold request. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Booking Hold - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .hold-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .hold-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .hold-header i {
            font-size: 3rem;
            color: #ffa502;
            margin-bottom: 15px;
        }
        
        .hold-header h2 {
            color: #333;
            font-weight: 700;
        }
        
        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .booking-info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .booking-info-item:last-child {
            border-bottom: none;
        }
        
        .booking-info-label {
            font-weight: 600;
            color: #666;
        }
        
        .booking-info-value {
            color: #333;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            resize: vertical;
            min-height: 120px;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .reason-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .reason-chip {
            background: #e8f4f8;
            color: #0575E6;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        
        .reason-chip:hover {
            background: #0575E6;
            color: white;
            border-color: #0575E6;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 165, 2, 0.4);
        }
        
        .btn-cancel {
            flex: 1;
            background: #6c757d;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
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
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        .info-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .info-box i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="hold-container">
        <div class="hold-header">
            <i class="fas fa-pause-circle"></i>
            <h2>Request Booking Hold</h2>
            <p style="color: #666;">Temporarily pause this booking</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> Customer must approve this hold request. Maximum hold period is 4 days.
        </div>
        
        <div class="booking-info">
            <div class="booking-info-item">
                <span class="booking-info-label">Booking ID:</span>
                <span class="booking-info-value">#<?php echo $booking->sb_id; ?></span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Customer:</span>
                <span class="booking-info-value"><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Service:</span>
                <span class="booking-info-value"><?php echo htmlspecialchars($booking->s_name); ?></span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Phone:</span>
                <span class="booking-info-value"><?php echo htmlspecialchars($booking->u_phone); ?></span>
            </div>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label>Reason for Hold Request <span style="color: red;">*</span></label>
                <textarea name="reason" id="reasonText" placeholder="Please explain why you need to hold this booking..." required></textarea>
                
                <div class="reason-suggestions">
                    <span class="reason-chip" onclick="setReason('Part shortage - waiting for spare parts')">🔧 Part Shortage</span>
                    <span class="reason-chip" onclick="setReason('Customer not available at address')">🏠 Customer Unavailable</span>
                    <span class="reason-chip" onclick="setReason('Need additional tools/equipment')">🛠️ Need Equipment</span>
                    <span class="reason-chip" onclick="setReason('Waiting for customer confirmation on repair cost')">💰 Cost Approval</span>
                    <span class="reason-chip" onclick="setReason('Technical issue requires specialist consultation')">👨‍🔧 Need Specialist</span>
                    <span class="reason-chip" onclick="setReason('Weather conditions not suitable for work')">🌧️ Weather Issue</span>
                </div>
            </div>
            
            <div class="btn-group">
                <a href="dashboard.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Send Request
                </button>
            </div>
        </form>
    </div>
    
    <script>
        function setReason(text) {
            document.getElementById('reasonText').value = text;
        }
    </script>
</body>
</html>
