<?php
/**
 * API: Real-time Lock Status Checker
 * Returns current lock status for technician
 * Called every 3 seconds by JavaScript
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['t_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');

$tech_id = $_SESSION['t_id'];

// Check if account is locked (both commission lock and rejection lock)
$check_query = "SELECT t_status, t_blocked_until, t_block_reason, account_locked, lock_reason, locked_at 
                FROM tms_technician 
                WHERE t_id = ?";
$stmt_check = $mysqli->prepare($check_query);
$stmt_check->bind_param('i', $tech_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$tech_status = $result_check->fetch_object();
$stmt_check->close();

$is_locked = false;
$lock_type = '';
$lock_reason = '';
$locked_until = '';

if ($tech_status) {
    // Check for COMMISSION LOCK (unpaid charges)
    if (isset($tech_status->account_locked) && $tech_status->account_locked == 1) {
        $is_locked = true;
        $lock_type = 'commission';
        $lock_reason = $tech_status->lock_reason ?? 'Your account has been locked due to unpaid commission charges';
    }
    // Check for REJECTION LOCK (excessive rejections)
    elseif ($tech_status->t_status === 'Locked') {
        // Check if block period expired
        if ($tech_status->t_blocked_until && strtotime($tech_status->t_blocked_until) <= time()) {
            // Auto-unlock
            $mysqli->query("UPDATE tms_technician SET t_status = 'Available', t_blocked_until = NULL, t_block_reason = NULL WHERE t_id = $tech_id");
            $is_locked = false;
        } else {
            $is_locked = true;
            $lock_type = 'rejection';
            $lock_reason = $tech_status->t_block_reason ?? 'Your account has been locked by admin';
            $locked_until = $tech_status->t_blocked_until ? date('M d, Y h:i A', strtotime($tech_status->t_blocked_until)) : '';
        }
    }
}

echo json_encode([
    'success' => true,
    'is_locked' => $is_locked,
    'lock_type' => $lock_type,
    'lock_reason' => $lock_reason,
    'locked_until' => $locked_until
]);
?>
