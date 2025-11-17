<?php
/**
 * Get Booking Status API
 * Returns current booking status for auto-refresh functionality
 */
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['u_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

include('vendor/inc/config.php');

$user_id = $_SESSION['u_id'];
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if($booking_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid booking ID']);
    exit();
}

// Get booking status
$query = "SELECT sb.sb_id, sb.sb_status, sb.sb_booking_date, sb.sb_booking_time, 
                 s.s_name, s.s_category
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_user_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'booking' => [
            'id' => $booking['sb_id'],
            'status' => $booking['sb_status'],
            'service_name' => $booking['s_name'],
            'booking_date' => $booking['sb_booking_date'],
            'booking_time' => $booking['sb_booking_time']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Booking not found']);
}

$stmt->close();
$mysqli->close();
?>
