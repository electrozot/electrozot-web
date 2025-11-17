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
$stmt = $mysqli->prepare("UPDATE tms_service_booking SET sb_status = 'Completed', sb_completed_at = NOW() WHERE sb_id = ?");
$stmt->bind_param('i', $booking_id);

if ($stmt->execute()) {
    // Decrement technician booking count
    decrementTechnicianBookings($mysqli, $technician_id);
    
    echo json_encode(['success' => true, 'message' => 'Booking completed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to complete booking']);
}
?>
