<?php
/**
 * API: Check for new bookings (Real-time polling)
 * Returns new bookings and notifications for admin
 */

session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');
require_once('BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

// Get new bookings count
$newBookingsCount = $bookingSystem->getNewBookingsCount();

// Get unread notifications
$notifications = $bookingSystem->getUnreadAdminNotifications();

// Get latest pending bookings
$stmt = $conn->query("
    SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name as service_name
    FROM tms_service_booking sb
    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
    WHERE sb.sb_status = 'Pending'
    ORDER BY sb.sb_created_at DESC
    LIMIT 5
");
$pendingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'new_bookings_count' => $newBookingsCount,
    'notifications' => $notifications,
    'pending_bookings' => $pendingBookings,
    'timestamp' => time()
]);
?>
