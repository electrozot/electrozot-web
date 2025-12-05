<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

$user_id = $_SESSION['u_id'];

// Get booking counts
$pending_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_user_id = ? AND sb_status = 'Pending'";
$stmt = $mysqli->prepare($pending_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$pending_count = $stmt->get_result()->fetch_assoc()['count'];

$approved_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_user_id = ? AND sb_status IN ('Approved', 'Confirmed')";
$stmt = $mysqli->prepare($approved_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$approved_count = $stmt->get_result()->fetch_assoc()['count'];

$completed_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_user_id = ? AND sb_status = 'Completed'";
$stmt = $mysqli->prepare($completed_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$completed_count = $stmt->get_result()->fetch_assoc()['count'];

$total_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_user_id = ?";
$stmt = $mysqli->prepare($total_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$total_count = $stmt->get_result()->fetch_assoc()['count'];

// Get recent bookings
$recent_query = "SELECT sb.sb_id, sb.sb_status, sb.sb_booking_date, s.s_name 
                 FROM tms_service_booking sb 
                 LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                 WHERE sb.sb_user_id = ? 
                 ORDER BY sb.sb_created_at DESC 
                 LIMIT 5";
$stmt = $mysqli->prepare($recent_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$recent_result = $stmt->get_result();
$recent_bookings = [];
while ($row = $recent_result->fetch_assoc()) {
    $recent_bookings[] = $row;
}

echo json_encode([
    'success' => true,
    'stats' => [
        'pending' => $pending_count,
        'approved' => $approved_count,
        'completed' => $completed_count,
        'total' => $total_count
    ],
    'recent_bookings' => $recent_bookings
]);
?>
