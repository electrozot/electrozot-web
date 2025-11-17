<?php
/**
 * API: Cancel booking (Admin)
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
$admin_id = $_SESSION['a_id'];

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

$result = $bookingSystem->cancelBooking($booking_id, 'admin', $admin_id);
echo json_encode($result);
?>
