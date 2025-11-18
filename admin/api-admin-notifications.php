<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

// Create notifications table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS tms_admin_notifications (
    an_id INT AUTO_INCREMENT PRIMARY KEY,
    an_type VARCHAR(50) NOT NULL,
    an_title VARCHAR(255) NOT NULL,
    an_message TEXT NOT NULL,
    an_booking_id INT,
    an_technician_id INT,
    an_is_read TINYINT(1) DEFAULT 0,
    an_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(an_is_read),
    INDEX(an_created_at),
    INDEX(an_booking_id)
)";
$mysqli->query($create_table);

// Get unread notifications
$query = "SELECT an.*, t.t_name as technician_name, sb.sb_id as booking_number
          FROM tms_admin_notifications an
          LEFT JOIN tms_technician t ON an.an_technician_id = t.t_id
          LEFT JOIN tms_service_booking sb ON an.an_booking_id = sb.sb_id
          WHERE an.an_is_read = 0
          ORDER BY an.an_created_at DESC
          LIMIT 10";

$result = $mysqli->query($query);
$notifications = [];

while($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'count' => count($notifications)
]);
?>
