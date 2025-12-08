<?php
/**
 * API: Admin takes action on technician rejections
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

$technician_id = $_POST['technician_id'] ?? null;
$action = $_POST['action'] ?? null; // 'lock_account', 'block_bookings', 'no_action'
$admin_notes = $_POST['admin_notes'] ?? '';

if (!$technician_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$mysqli->begin_transaction();

try {
    $block_until = date('Y-m-d H:i:s', strtotime('+2 days'));
    
    if ($action === 'lock_account') {
        // Lock account for 2 days
        $stmt = $mysqli->prepare("UPDATE tms_technician 
                                 SET t_status = 'Locked', 
                                     t_blocked_until = ?,
                                     t_block_reason = 'Excessive booking rejections - Account locked by admin'
                                 WHERE t_id = ?");
        $stmt->bind_param('si', $block_until, $technician_id);
        $stmt->execute();
        
        $action_message = "Account locked for 2 days due to excessive rejections";
        
    } elseif ($action === 'block_bookings') {
        // Block from receiving new bookings for 2 days but keep account active
        $stmt = $mysqli->prepare("UPDATE tms_technician 
                                 SET t_blocked_until = ?,
                                     t_block_reason = 'Blocked from new bookings - Excessive rejections'
                                 WHERE t_id = ?");
        $stmt->bind_param('si', $block_until, $technician_id);
        $stmt->execute();
        
        $action_message = "Blocked from new bookings for 2 days";
        
    } else {
        // No action - just mark as reviewed
        $action_message = "No action taken - Reviewed by admin";
    }
    
    // Mark all rejections as admin_notified = 1 (so they won't trigger alerts again)
    $stmt = $mysqli->prepare("UPDATE tms_technician_rejections 
                             SET tr_admin_notified = 1,
                                 tr_admin_action = ?,
                                 tr_admin_action_at = NOW(),
                                 tr_admin_notes = ?
                             WHERE tr_technician_id = ?");
    $stmt->bind_param('ssi', $action, $admin_notes, $technician_id);
    $stmt->execute();
    
    // Reset rejection counter in technician table
    $mysqli->query("UPDATE tms_technician SET t_rejection_count = 0 WHERE t_id = $technician_id");
    
    // Log admin action (using existing syslogs structure)
    $admin_email = $_SESSION['a_email'] ?? 'admin';
    $log_message = "Admin took action on technician #$technician_id rejections: $action_message";
    $log_stmt = $mysqli->prepare("INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                                  VALUES (?, ?, ?, ?, 'admin')");
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $log_stmt->bind_param('ssss', $log_message, $user_ip, $admin_notes, $action);
    $log_stmt->execute();
    
    $mysqli->commit();
    
    echo json_encode([
        'success' => true,
        'message' => $action_message
    ]);
    
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process action: ' . $e->getMessage()
    ]);
}
?>
