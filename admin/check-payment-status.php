<?php
/**
 * AJAX Endpoint: Check Payment Status
 * Returns whether payment is already made for a technician on a specific date
 */

session_start();
include('vendor/inc/config.php');

// Check if admin is logged in
if(!isset($_SESSION['a_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$tech_id = isset($_POST['tech_id']) ? intval($_POST['tech_id']) : 0;
$lock_date = isset($_POST['lock_date']) ? $_POST['lock_date'] : date('Y-m-d');
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

if($tech_id <= 0) {
    echo json_encode(['error' => 'Invalid technician ID']);
    exit();
}

// Check if technician is already locked
$lock_check = "SELECT account_locked FROM tms_technician WHERE t_id = ?";
$stmt_lock = $mysqli->prepare($lock_check);
$stmt_lock->bind_param('i', $tech_id);
$stmt_lock->execute();
$lock_result = $stmt_lock->get_result();
$lock_data = $lock_result->fetch_object();
$stmt_lock->close();

if($lock_data && $lock_data->account_locked == 1) {
    echo json_encode([
        'already_locked' => true,
        'already_paid' => false,
        'paid_amount' => 0,
        'date' => date('d M Y', strtotime($lock_date))
    ]);
    exit();
}

// Check if payment already made for this date
$payment_check = "SELECT COALESCE(SUM(cp_amount), 0) as paid_amount
                  FROM tms_commission_payments 
                  WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
$stmt = $mysqli->prepare($payment_check);
$stmt->bind_param('is', $tech_id, $lock_date);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_object();
$stmt->close();

$paid_amount = $data->paid_amount;

// If payment already made for this amount or more
if($paid_amount >= $amount && $amount > 0) {
    echo json_encode([
        'already_paid' => true,
        'already_locked' => false,
        'paid_amount' => number_format($paid_amount, 0),
        'date' => date('d M Y', strtotime($lock_date))
    ]);
} else {
    echo json_encode([
        'already_paid' => false,
        'already_locked' => false,
        'paid_amount' => number_format($paid_amount, 0),
        'pending_amount' => number_format($amount - $paid_amount, 0),
        'date' => date('d M Y', strtotime($lock_date))
    ]);
}
?>
