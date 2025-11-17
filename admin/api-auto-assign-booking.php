<?php
/**
 * API: Auto-assign booking to best available technician
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');
require_once('BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

$booking_id = $_POST['booking_id'] ?? null;

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

$result = $bookingSystem->autoAssignBooking($booking_id);
echo json_encode($result);
?>
