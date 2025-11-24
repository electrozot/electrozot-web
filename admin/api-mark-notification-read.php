<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

$notification_id = $_POST['notification_id'] ?? null;

if($notification_id) {
    // Mark single notification as read
    $stmt = $mysqli->prepare("UPDATE tms_admin_notifications SET an_is_read = 1 WHERE an_id = ?");
    $stmt->bind_param('i', $notification_id);
    $stmt->execute();
} else {
    // Mark all as read
    $mysqli->query("UPDATE tms_admin_notifications SET an_is_read = 1 WHERE an_is_read = 0");
}

echo json_encode(['success' => true]);
?>
