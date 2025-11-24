<?php
/**
 * Real-time Notifications API
 * Returns new bookings and status changes for admin
 */

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if admin is logged in
if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

$aid = $_SESSION['a_id'];
$lastCheckTime = isset($_GET['last_check']) ? intval($_GET['last_check']) : (time() - 60);

// Get new bookings (created in last check interval)
$new_bookings_query = "SELECT sb.*, 
                              CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                              s.s_name as service_name
                       FROM tms_service_booking sb
                       LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                       LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                       WHERE sb.sb_created_at >= FROM_UNIXTIME(?)
                       AND sb.sb_status = 'Pending'
                       ORDER BY sb.sb_created_at DESC";

$stmt = $mysqli->prepare($new_bookings_query);
$stmt->bind_param('i', $lastCheckTime);
$stmt->execute();
$new_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get status changes (updated in last check interval)
$status_changes_query = "SELECT sb.*, 
                                CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                                s.s_name as service_name,
                                t.t_name as technician_name
                         FROM tms_service_booking sb
                         LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                         LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                         LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                         WHERE sb.sb_updated_at >= FROM_UNIXTIME(?)
                         AND sb.sb_status IN ('Rejected', 'Completed', 'Cancelled')
                         ORDER BY sb.sb_updated_at DESC";

$stmt = $mysqli->prepare($status_changes_query);
$stmt->bind_param('i', $lastCheckTime);
$stmt->execute();
$status_changes_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Format status changes
$status_changes = [];
foreach ($status_changes_result as $change) {
    $status_changes[] = [
        'type' => $change['sb_status'],
        'sb_id' => $change['sb_id'],
        'customer_name' => $change['customer_name'],
        'service_name' => $change['service_name'],
        'technician_name' => $change['technician_name'],
        'status' => $change['sb_status'],
        'reason' => $change['sb_rejection_reason'] ?? ''
    ];
}

// Get total unread count
$unread_query = "SELECT COUNT(*) as count FROM tms_service_booking 
                 WHERE sb_status IN ('Pending', 'Rejected') 
                 OR (sb_technician_id IS NULL AND sb_status NOT IN ('Cancelled', 'Completed'))";
$unread_result = $mysqli->query($unread_query);
$total_unread = $unread_result->fetch_object()->count;

echo json_encode([
    'success' => true,
    'new_bookings' => $new_bookings,
    'status_changes' => $status_changes,
    'total_unread' => $total_unread,
    'timestamp' => time()
]);
?>
