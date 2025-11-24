<?php
/**
 * API: Technician completes booking
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['t_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');
require_once('../admin/vendor/inc/booking-limit-helper.php');

$booking_id = $_POST['booking_id'] ?? null;
$notes = $_POST['notes'] ?? '';
$technician_id = $_SESSION['t_id'];

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

// Verify booking belongs to this technician
$stmt = $mysqli->prepare("SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?");
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if (!$booking || $booking->sb_technician_id != $technician_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Update booking status to completed
$stmt = $mysqli->prepare("UPDATE tms_service_booking SET sb_status = 'Completed', sb_completed_at = NOW(), sb_updated_at = NOW() WHERE sb_id = ?");
$stmt->bind_param('i', $booking_id);

if ($stmt->execute()) {
    // Decrement technician booking count
    decrementTechnicianBookings($mysqli, $technician_id);
    
    // DIRECT UPDATE: Update technician availability status
    $update_status_sql = "UPDATE tms_technician 
                         SET t_status = CASE 
                             WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
                             ELSE 'Available'
                         END
                         WHERE t_id = ?";
    $status_stmt = $mysqli->prepare($update_status_sql);
    $status_stmt->bind_param('i', $technician_id);
    $status_stmt->execute();
    
    // Create admin notification
    $get_tech_name = $mysqli->prepare("SELECT t_name FROM tms_technician WHERE t_id = ?");
    $get_tech_name->bind_param('i', $technician_id);
    $get_tech_name->execute();
    $tech_result = $get_tech_name->get_result();
    $tech_data = $tech_result->fetch_object();
    $tech_name = $tech_data ? $tech_data->t_name : 'Technician';
    
    $notif_title = "Booking Completed";
    $notif_message = "$tech_name completed Booking #$booking_id successfully!";
    $notif_type = "BOOKING_COMPLETED";
    
    $notif_stmt = $mysqli->prepare("INSERT INTO tms_admin_notifications (an_type, an_title, an_message, an_booking_id, an_technician_id) VALUES (?, ?, ?, ?, ?)");
    $notif_stmt->bind_param('sssii', $notif_type, $notif_title, $notif_message, $booking_id, $technician_id);
    $notif_stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Booking completed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to complete booking']);
}
?>
