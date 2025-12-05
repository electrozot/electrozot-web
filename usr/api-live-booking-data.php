<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

$user_id = $_SESSION['u_id'];
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id > 0) {
    // Get specific booking with technician info
    $query = "SELECT sb.sb_id, sb.sb_status, sb.sb_booking_date, sb.sb_booking_time, 
              s.s_name, s.s_category, t.t_name, t.t_pic 
              FROM tms_service_booking sb 
              LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
              LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id 
              WHERE sb.sb_id = ? AND sb.sb_user_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($booking = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'data' => $booking
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
}
?>
