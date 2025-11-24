<?php
/**
 * API: Assign booking to technician
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');
require_once('vendor/inc/booking-limit-helper.php');

$booking_id = $_POST['booking_id'] ?? null;
$technician_id = $_POST['technician_id'] ?? null;
$admin_id = $_SESSION['a_id'];

if (!$booking_id || !$technician_id) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$result = assignBookingToTechnician($mysqli, $booking_id, $technician_id, $admin_id);
echo json_encode($result);
?>
