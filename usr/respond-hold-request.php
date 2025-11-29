<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$u_id = $_SESSION['u_id'];
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if(!in_array($action, ['approve', 'reject'])) {
    header("Location: user-dashboard.php?error=invalid_action");
    exit;
}

// Get hold request details
$query = "SELECT bhr.*, sb.*, t.t_name, t.t_phone, u.u_id
          FROM tms_booking_hold_requests bhr
          LEFT JOIN tms_service_booking sb ON bhr.bhr_booking_id = sb.sb_id
          LEFT JOIN tms_technician t ON bhr.bhr_technician_id = t.t_id
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          WHERE bhr.bhr_id = ? AND u.u_id = ? AND bhr.bhr_status = 'Pending'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $request_id, $u_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header("Location: user-dashboard.php?error=invalid_request");
    exit;
}

$request = $result->fetch_object();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_response = isset($_POST['response']) ? trim($_POST['response']) : '';
    
    if($action == 'approve') {
        // Calculate hold end date (4 days from now)
        $hold_start = date('Y-m-d H:i:s');
        $hold_end = date('Y-m-d H:i:s', strtotime('+4 days'));
        
        // Update hold request status
        $update_request = "UPDATE tms_booking_hold_requests 
                          SET bhr_status = 'Approved', 
                              bhr_responded_at = NOW(),
                              bhr_customer_response = ?
                          WHERE bhr_id = ?";
        $stmt_update = $mysqli->prepare($update_request);
        $stmt_update->bind_param('si', $customer_response, $request_id);
        $stmt_update->execute();
        
        // Update booking to on-hold status
        $update_booking = "UPDATE tms_service_booking 
                          SET sb_is_on_hold = 1,
                              sb_hold_reason = ?,
                              sb_hold_start_date = ?,
                              sb_hold_end_date = ?,
                              sb_status = 'On Hold'
                          WHERE sb_id = ?";
        $stmt_booking = $mysqli->prepare($update_booking);
        $stmt_booking->bind_param('sssi', $request->bhr_reason, $hold_start, $hold_end, $request->bhr_booking_id);
        $stmt_booking->execute();
        
        // Notify technician
        $notif_title = "Hold Request Approved - Booking #" . $request->bhr_booking_id;
        $notif_message = "Customer has approved your hold request. Booking is now on hold until " . date('M d, Y', strtotime($hold_end)) . ".";
        if(!empty($customer_response)) {
            $notif_message .= " Customer note: " . $customer_response;
        }
        
        $insert_notif = "INSERT INTO tms_technician_notifications 
                        (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                        VALUES (?, ?, 'hold_approved', ?, ?)";
        $stmt_notif = $mysqli->prepare($insert_notif);
        $stmt_notif->bind_param('iiss', $request->bhr_technician_id, $request->bhr_booking_id, $notif_title, $notif_message);
        $stmt_notif->execute();
        
        // Notify admin about hold approval
        try {
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
            
            $admin_notif_title = "Hold Request Approved - Booking #" . $request->bhr_booking_id;
            $admin_notif_message = "Customer approved hold request for Booking #" . $request->bhr_booking_id . ". Technician: " . $request->t_name . ". Reason: " . $request->bhr_reason . ". Hold period: 4 days.";
            
            $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                                   (an_booking_id, an_type, an_title, an_message) 
                                   VALUES (?, 'hold_approved', ?, ?)";
            $stmt_admin = $mysqli->prepare($insert_admin_notif);
            $stmt_admin->bind_param('iss', $request->bhr_booking_id, $admin_notif_title, $admin_notif_message);
            $stmt_admin->execute();
        } catch(Exception $e) {}
        
        // Log activity (optional)
        try {
            $log_query = "INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                         VALUES (?, ?, ?, ?, 'customer')";
            $log_email = "customer_" . $u_id;
            $log_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $log_details = "Hold Approved - Booking #" . $request->bhr_booking_id;
            $stmt_log = $mysqli->prepare($log_query);
            $stmt_log->bind_param('ssss', $log_email, $log_ip, $log_details, $log_details);
            $stmt_log->execute();
        } catch(Exception $e) {
            // Logging failed, continue anyway
        }
        
        header("Location: user-track-booking.php?id=" . $request->bhr_booking_id . "&success=hold_approved");
        exit;
        
    } else if($action == 'reject') {
        // Update hold request status
        $update_request = "UPDATE tms_booking_hold_requests 
                          SET bhr_status = 'Rejected', 
                              bhr_responded_at = NOW(),
                              bhr_customer_response = ?
                          WHERE bhr_id = ?";
        $stmt_update = $mysqli->prepare($update_request);
        $stmt_update->bind_param('si', $customer_response, $request_id);
        $stmt_update->execute();
        
        // Notify technician
        $notif_title = "Hold Request Rejected - Booking #" . $request->bhr_booking_id;
        $notif_message = "Customer has rejected your hold request. Please proceed with the booking or contact customer.";
        if(!empty($customer_response)) {
            $notif_message .= " Customer note: " . $customer_response;
        }
        
        $insert_notif = "INSERT INTO tms_technician_notifications 
                        (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                        VALUES (?, ?, 'hold_rejected', ?, ?)";
        $stmt_notif = $mysqli->prepare($insert_notif);
        $stmt_notif->bind_param('iiss', $request->bhr_technician_id, $request->bhr_booking_id, $notif_title, $notif_message);
        $stmt_notif->execute();
        
        // Notify admin about hold rejection
        try {
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
            
            $admin_notif_title = "Hold Request Rejected - Booking #" . $request->bhr_booking_id;
            $admin_notif_message = "Customer rejected hold request for Booking #" . $request->bhr_booking_id . ". Technician: " . $request->t_name . ". Reason: " . $request->bhr_reason . ".";
            if(!empty($customer_response)) {
                $admin_notif_message .= " Customer note: " . $customer_response;
            }
            
            $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                                   (an_booking_id, an_type, an_title, an_message) 
                                   VALUES (?, 'hold_rejected', ?, ?)";
            $stmt_admin = $mysqli->prepare($insert_admin_notif);
            $stmt_admin->bind_param('iss', $request->bhr_booking_id, $admin_notif_title, $admin_notif_message);
            $stmt_admin->execute();
        } catch(Exception $e) {}
        
        // Log activity (optional)
        try {
            $log_query = "INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                         VALUES (?, ?, ?, ?, 'customer')";
            $log_email = "customer_" . $u_id;
            $log_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $log_details = "Hold Rejected - Booking #" . $request->bhr_booking_id;
            $stmt_log = $mysqli->prepare($log_query);
            $stmt_log->bind_param('ssss', $log_email, $log_ip, $log_details, $log_details);
            $stmt_log->execute();
        } catch(Exception $e) {
            // Logging failed, continue anyway
        }
        
        header("Location: user-track-booking.php?id=" . $request->bhr_booking_id . "&success=hold_rejected");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Hold Request - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .response-container {
            max-width: 700px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .response-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .response-header i {
            font-size: 3.5rem;
            color: #ffa502;
            margin-bottom: 15px;
        }
        
        .response-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .request-details {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #ffa502;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        
        .detail-value {
            color: #333;
            font-weight: 700;
            text-align: right;
        }
        
        .reason-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .reason-box h4 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .reason-box p {
            color: #856404;
            margin: 0;
            line-height: 1.6;
        }
        
        .form-group {
            margin-bottom: 25px;
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
            min-height: 100px;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-approve {
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
        
        .btn-approve:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 200, 83, 0.4);
        }
        
        .btn-reject {
            flex: 1;
            background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%);
            color: white;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-reject:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4);
        }
        
        .info-box {
            background: #e8f4f8;
            border: 2px solid #0575E6;
            color: #0575E6;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .info-box i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="response-container">
        <div class="response-header">
            <i class="fas fa-hand-paper"></i>
            <h2>Hold Request from Technician</h2>
            <p style="color: #666;">Please review and respond to this request</p>
        </div>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            If approved, your booking will be on hold for up to 4 days. You can unhold it anytime from your dashboard.
        </div>
        
        <div class="request-details">
            <div class="detail-row">
                <span class="detail-label">Booking ID:</span>
                <span class="detail-value">#<?php echo $request->bhr_booking_id; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Technician:</span>
                <span class="detail-value"><?php echo htmlspecialchars($request->t_name); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Technician Phone:</span>
                <span class="detail-value"><?php echo htmlspecialchars($request->t_phone); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Requested On:</span>
                <span class="detail-value"><?php echo date('M d, Y h:i A', strtotime($request->bhr_requested_at)); ?></span>
            </div>
        </div>
        
        <div class="reason-box">
            <h4><i class="fas fa-comment-dots"></i> Technician's Reason:</h4>
            <p><?php echo htmlspecialchars($request->bhr_reason); ?></p>
        </div>
        
        <form method="POST" id="responseForm">
            <div class="form-group">
                <label>Your Response (Optional)</label>
                <textarea name="response" placeholder="Add any notes or instructions for the technician..."></textarea>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn-reject" onclick="submitResponse('reject')">
                    <i class="fas fa-times-circle"></i> Reject Request
                </button>
                <button type="button" class="btn-approve" onclick="submitResponse('approve')">
                    <i class="fas fa-check-circle"></i> Approve Hold
                </button>
            </div>
        </form>
    </div>
    
    <script>
        function submitResponse(action) {
            if(confirm('Are you sure you want to ' + action + ' this hold request?')) {
                const form = document.getElementById('responseForm');
                form.action = '?id=<?php echo $request_id; ?>&action=' + action;
                form.submit();
            }
        }
    </script>
</body>
</html>
