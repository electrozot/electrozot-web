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

// Check if technician is locked before assignment
$check_lock = $mysqli->prepare("SELECT t_name, account_locked, lock_reason FROM tms_technician WHERE t_id = ?");
$check_lock->bind_param('i', $technician_id);
$check_lock->execute();
$tech_result = $check_lock->get_result();
$tech = $tech_result->fetch_object();

if ($tech && isset($tech->account_locked) && $tech->account_locked == 1) {
    echo json_encode([
        'success' => false,
        'is_locked' => true,
        'technician_name' => $tech->t_name,
        'lock_reason' => $tech->lock_reason ?? 'Account locked by system',
        'message' => "Cannot assign booking to {$tech->t_name}. This technician's account is locked."
    ]);
    exit;
}

$result = assignBookingToTechnician($mysqli, $booking_id, $technician_id, $admin_id);
echo json_encode($result);
?>
