<?php
/**
 * API: Set technician on leave
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

$technician_id = $_POST['technician_id'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$reason = $_POST['reason'] ?? '';

if (!$technician_id || !$start_date || !$end_date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$result = $bookingSystem->setTechnicianLeave($technician_id, $start_date, $end_date, $reason);
echo json_encode($result);
?>
