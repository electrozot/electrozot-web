<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

$u_id = $_SESSION['u_id'];
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if(!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
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
    echo json_encode(['success' => false, 'message' => 'Invalid request or already responded']);
    exit;
}

$request = $result->fetch_object();

if($action == 'approve') {
    // Calculate hold end date (4 days from now)
    $hold_start = date('Y-m-d H:i:s');
    $hold_end = date('Y-m-d H:i:s', strtotime('+4 days'));
    
    // Update hold request status
    $update_request = "UPDATE tms_booking_hold_requests 
                      SET bhr_status = 'Approved', 
                          bhr_responded_at = NOW()
                      WHERE bhr_id = ?";
    $stmt_update = $mysqli->prepare($update_request);
    $stmt_update->bind_param('i', $request_id);
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
    
    $insert_notif = "INSERT INTO tms_technician_notifications 
                    (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                    VALUES (?, ?, 'hold_approved', ?, ?)";
    $stmt_notif = $mysqli->prepare($insert_notif);
    $stmt_notif->bind_param('iiss', $request->bhr_technician_id, $request->bhr_booking_id, $notif_title, $notif_message);
    $stmt_notif->execute();
    
    // Notify admin
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
    
    echo json_encode(['success' => true, 'message' => 'Hold request approved']);
    
} else if($action == 'reject') {
    // Update hold request status
    $update_request = "UPDATE tms_booking_hold_requests 
                      SET bhr_status = 'Rejected', 
                          bhr_responded_at = NOW()
                      WHERE bhr_id = ?";
    $stmt_update = $mysqli->prepare($update_request);
    $stmt_update->bind_param('i', $request_id);
    $stmt_update->execute();
    
    // Notify technician
    $notif_title = "Hold Request Rejected - Booking #" . $request->bhr_booking_id;
    $notif_message = "Customer has rejected your hold request. Please proceed with the booking or contact customer.";
    
    $insert_notif = "INSERT INTO tms_technician_notifications 
                    (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                    VALUES (?, ?, 'hold_rejected', ?, ?)";
    $stmt_notif = $mysqli->prepare($insert_notif);
    $stmt_notif->bind_param('iiss', $request->bhr_technician_id, $request->bhr_booking_id, $notif_title, $notif_message);
    $stmt_notif->execute();
    
    // Notify admin
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
        
        $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                               (an_booking_id, an_type, an_title, an_message) 
                               VALUES (?, 'hold_rejected', ?, ?)";
        $stmt_admin = $mysqli->prepare($insert_admin_notif);
        $stmt_admin->bind_param('iss', $request->bhr_booking_id, $admin_notif_title, $admin_notif_message);
        $stmt_admin->execute();
    } catch(Exception $e) {}
    
    echo json_encode(['success' => true, 'message' => 'Hold request rejected']);
}
?>
