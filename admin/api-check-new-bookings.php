<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

// Get the last check timestamp from session
$last_check = isset($_SESSION['last_booking_check']) ? $_SESSION['last_booking_check'] : date('Y-m-d H:i:s', strtotime('-1 hour'));

// Get new bookings since last check
$query = "SELECT sb.sb_id, sb.sb_booking_date, sb.sb_booking_time, sb.sb_status, sb.sb_created_at,
          u.u_fname, u.u_lname, u.u_phone, s.s_name,
          CASE 
            WHEN u.registration_type = 'guest' THEN 'Guest Booking'
            WHEN u.registration_type = 'admin' THEN 'Quick Booking'
            WHEN u.registration_type = 'self' THEN 'User Booking'
            ELSE 'New Booking'
          END as booking_type
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_created_at > ?
          AND sb.sb_status = 'Pending'
          ORDER BY sb.sb_created_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $last_check);
$stmt->execute();
$result = $stmt->get_result();

$new_bookings = [];
while($row = $result->fetch_assoc()) {
    $new_bookings[] = $row;
}

// Update last check timestamp
$_SESSION['last_booking_check'] = date('Y-m-d H:i:s');

// Get total pending bookings count
$count_query = "SELECT COUNT(*) as total FROM tms_service_booking WHERE sb_status = 'Pending'";
$count_result = $mysqli->query($count_query);
$count_data = $count_result->fetch_assoc();

echo json_encode([
    'success' => true,
    'new_bookings' => $new_bookings,
    'new_count' => count($new_bookings),
    'total_pending' => $count_data['total']
]);
?>
