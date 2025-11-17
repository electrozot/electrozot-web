<?php
/**
 * API: User modifies booking (before technician assignment)
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['u_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');
require_once('../admin/BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

$booking_id = $_POST['booking_id'] ?? null;
$field_name = $_POST['field_name'] ?? null;
$new_value = $_POST['new_value'] ?? null;
$reason = $_POST['reason'] ?? 'User modification';
$user_id = $_SESSION['u_id'];

if (!$booking_id || !$field_name || !$new_value) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Allowed fields for user modification
$allowed_fields = ['sb_booking_date', 'sb_booking_time', 'sb_address', 'sb_phone', 'sb_description'];

if (!in_array($field_name, $allowed_fields)) {
    echo json_encode(['success' => false, 'message' => 'Cannot modify this field']);
    exit;
}

$result = $bookingSystem->modifyBooking($booking_id, $field_name, $new_value, 'user', $user_id, $reason);
echo json_encode($result);
?>
