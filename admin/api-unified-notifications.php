<?php
/**
 * Unified Notifications API
 * Returns all types of booking notifications for admin
 * Triggers: New booking, Rejected, Cancelled, Completed
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
$lastCheckTimestamp = isset($_GET['last_check']) ? intval($_GET['last_check']) : (time() - 60);
$currentTimestamp = time();

$notifications = [];

// 1. NEW BOOKINGS (from user dashboard, admin quick booking, guest booking)
$new_bookings_query = "SELECT 
                        sb.sb_id,
                        sb.sb_status,
                        sb.sb_created_at,
                        CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                        u.u_phone,
                        s.s_name as service_name,
                        s.s_category,
                        'NEW_BOOKING' as notification_type
                       FROM tms_service_booking sb
                       LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                       LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                       WHERE UNIX_TIMESTAMP(sb.sb_created_at) > ?
                       AND sb.sb_status = 'Pending'
                       ORDER BY sb.sb_created_at DESC";

$stmt = $mysqli->prepare($new_bookings_query);
$stmt->bind_param('i', $lastCheckTimestamp);
$stmt->execute();
$new_bookings = $stmt->get_result();

while ($booking = $new_bookings->fetch_assoc()) {
    $notifications[] = [
        'id' => 'new_' . $booking['sb_id'],
        'type' => 'NEW_BOOKING',
        'booking_id' => $booking['sb_id'],
        'message' => 'Booking #' . $booking['sb_id'] . ' - ' . $booking['service_name'],
        'details' => 'Customer: ' . $booking['customer_name'] . ' | Phone: ' . $booking['u_phone'],
        'timestamp' => strtotime($booking['sb_created_at'])
    ];
}
$stmt->close();

// 2. REJECTED BOOKINGS (by technician)
$rejected_query = "SELECT 
                    sb.sb_id,
                    sb.sb_status,
                    sb.sb_rejected_at,
                    sb.sb_rejection_reason,
                    CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                    s.s_name as service_name,
                    t.t_name as technician_name,
                    'BOOKING_REJECTED' as notification_type
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                   WHERE UNIX_TIMESTAMP(sb.sb_rejected_at) > ?
                   AND sb.sb_status IN ('Rejected', 'Rejected by Technician', 'Not Done')
                   ORDER BY sb.sb_rejected_at DESC";

$stmt = $mysqli->prepare($rejected_query);
$stmt->bind_param('i', $lastCheckTimestamp);
$stmt->execute();
$rejected_bookings = $stmt->get_result();

while ($booking = $rejected_bookings->fetch_assoc()) {
    $reason = $booking['sb_rejection_reason'] ? substr($booking['sb_rejection_reason'], 0, 50) : 'No reason provided';
    $notifications[] = [
        'id' => 'rejected_' . $booking['sb_id'],
        'type' => 'BOOKING_REJECTED',
        'booking_id' => $booking['sb_id'],
        'message' => 'Booking #' . $booking['sb_id'] . ' rejected by ' . ($booking['technician_name'] ?? 'technician'),
        'details' => 'Reason: ' . $reason . ' | Service: ' . $booking['service_name'],
        'timestamp' => strtotime($booking['sb_rejected_at'])
    ];
}
$stmt->close();

// 3. COMPLETED BOOKINGS
$completed_query = "SELECT 
                     sb.sb_id,
                     sb.sb_status,
                     sb.sb_completed_at,
                     CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                     s.s_name as service_name,
                     t.t_name as technician_name,
                     'BOOKING_COMPLETED' as notification_type
                    FROM tms_service_booking sb
                    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                    LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                    WHERE UNIX_TIMESTAMP(sb.sb_completed_at) > ?
                    AND sb.sb_status = 'Completed'
                    ORDER BY sb.sb_completed_at DESC";

$stmt = $mysqli->prepare($completed_query);
$stmt->bind_param('i', $lastCheckTimestamp);
$stmt->execute();
$completed_bookings = $stmt->get_result();

while ($booking = $completed_bookings->fetch_assoc()) {
    $notifications[] = [
        'id' => 'completed_' . $booking['sb_id'],
        'type' => 'BOOKING_COMPLETED',
        'booking_id' => $booking['sb_id'],
        'message' => 'Booking #' . $booking['sb_id'] . ' completed successfully',
        'details' => 'Technician: ' . ($booking['technician_name'] ?? 'N/A') . ' | Service: ' . $booking['service_name'],
        'timestamp' => strtotime($booking['sb_completed_at'])
    ];
}
$stmt->close();

// 4. CANCELLED BOOKINGS (by user or admin)
$cancelled_query = "SELECT 
                     sb.sb_id,
                     sb.sb_status,
                     sb.sb_cancelled_at,
                     sb.sb_cancelled_by,
                     CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                     s.s_name as service_name,
                     'BOOKING_CANCELLED' as notification_type
                    FROM tms_service_booking sb
                    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                    WHERE UNIX_TIMESTAMP(sb.sb_cancelled_at) > ?
                    AND sb.sb_status = 'Cancelled'
                    ORDER BY sb.sb_cancelled_at DESC";

$stmt = $mysqli->prepare($cancelled_query);
$stmt->bind_param('i', $lastCheckTimestamp);
$stmt->execute();
$cancelled_bookings = $stmt->get_result();

while ($booking = $cancelled_bookings->fetch_assoc()) {
    $cancelled_by = $booking['sb_cancelled_by'] ?? 'user';
    $notifications[] = [
        'id' => 'cancelled_' . $booking['sb_id'],
        'type' => 'BOOKING_CANCELLED',
        'booking_id' => $booking['sb_id'],
        'message' => 'Booking #' . $booking['sb_id'] . ' cancelled by ' . $cancelled_by,
        'details' => 'Customer: ' . $booking['customer_name'] . ' | Service: ' . $booking['service_name'],
        'timestamp' => strtotime($booking['sb_cancelled_at'])
    ];
}
$stmt->close();

// Sort notifications by timestamp (newest first)
usort($notifications, function($a, $b) {
    return $b['timestamp'] - $a['timestamp'];
});

// Get total unread count (pending + rejected bookings)
$unread_query = "SELECT COUNT(*) as count 
                 FROM tms_service_booking 
                 WHERE sb_status IN ('Pending', 'Rejected', 'Rejected by Technician', 'Not Done')";
$unread_result = $mysqli->query($unread_query);
$unread_count = $unread_result->fetch_object()->count;

// Return response
echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'unread_count' => $unread_count,
    'current_timestamp' => $currentTimestamp,
    'last_check' => $lastCheckTimestamp,
    'new_count' => count($notifications)
]);
?>
