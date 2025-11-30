<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

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
    echo json_encode(['success' => false, 'message' => 'Invalid booking or not on hold']);
    exit;
}

$booking = $result->fetch_object();

// Update booking - remove hold and mark as high priority
// IMPORTANT: Set sb_was_on_hold = 1 to prevent technician rejection and customer cancellation
$update_booking = "UPDATE tms_service_booking 
                  SET sb_is_on_hold = 0,
                      sb_hold_reason = NULL,
                      sb_hold_start_date = NULL,
                      sb_hold_end_date = NULL,
                      sb_was_on_hold = 1,
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
        
        $admin_notif_title = "Booking #" . $booking_id . " Unholded by Customer";
        $admin_notif_message = "Customer has resumed Booking #" . $booking_id . " which was on hold. Booking is now marked as HIGH PRIORITY for technician " . $booking->t_name . ".";
        
        $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                               (an_booking_id, an_type, an_title, an_message) 
                               VALUES (?, 'booking_unholded', ?, ?)";
        $stmt_admin = $mysqli->prepare($insert_admin_notif);
        $stmt_admin->bind_param('iss', $booking_id, $admin_notif_title, $admin_notif_message);
        $stmt_admin->execute();
    } catch(Exception $e) {}
    
    echo json_encode(['success' => true, 'message' => 'Booking resumed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
}
?>
