<?php
/**
 * API: Technician rejects booking
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
$reason = $_POST['reason'] ?? 'Not specified';
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

// Update booking status to rejected
$stmt = $mysqli->prepare("UPDATE tms_service_booking SET sb_status = 'Rejected by Technician', sb_rejected_at = NOW(), sb_rejection_reason = ?, sb_technician_id = NULL WHERE sb_id = ?");
$stmt->bind_param('si', $reason, $booking_id);

if ($stmt->execute()) {
    // Decrement technician booking count
    decrementTechnicianBookings($mysqli, $technician_id);
    
    echo json_encode(['success' => true, 'message' => 'Booking rejected. Admin will reassign.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reject booking']);
}
?>
