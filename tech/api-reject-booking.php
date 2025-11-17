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
require_once('../admin/BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

$booking_id = $_POST['booking_id'] ?? null;
$reason = $_POST['reason'] ?? 'Not specified';
$technician_id = $_SESSION['t_id'];

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

$result = $bookingSystem->rejectBooking($booking_id, $technician_id, $reason);
echo json_encode($result);
?>
