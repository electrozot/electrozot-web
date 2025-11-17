<?php
// Start output buffering to prevent any unwanted output
ob_start();

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Clear any output that might have been generated
ob_end_clean();

// Now set the header
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Ensure timestamp columns exist
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

// Get the last check timestamp from session (default to 30 seconds ago for testing)
if(!isset($_SESSION['last_booking_check'])) {
    $_SESSION['last_booking_check'] = date('Y-m-d H:i:s', strtotime('-30 seconds'));
}
$last_check = $_SESSION['last_booking_check'];

// Check for new bookings since last check
$query = "SELECT COUNT(*) as new_count FROM tms_service_booking WHERE sb_created_at > ?";
$stmt = $mysqli->prepare($query);
if(!$stmt) {
    echo json_encode(['error' => 'Query preparation failed: ' . $mysqli->error]);
    exit;
}
$stmt->bind_param('s', $last_check);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Get details of new bookings
$details_query = "SELECT sb.sb_id, sb.sb_booking_date, sb.sb_booking_time, sb.sb_status, sb.sb_created_at,
                         u.u_fname, u.u_lname, u.u_phone,
                         s.s_name
                  FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                  WHERE sb.sb_created_at > ?
                  ORDER BY sb.sb_created_at DESC
                  LIMIT 5";
$details_stmt = $mysqli->prepare($details_query);
if(!$details_stmt) {
    echo json_encode(['error' => 'Details query preparation failed: ' . $mysqli->error]);
    exit;
}
$details_stmt->bind_param('s', $last_check);
$details_stmt->execute();
$details_result = $details_stmt->get_result();

$new_bookings = [];
while($booking = $details_result->fetch_assoc()) {
    $new_bookings[] = [
        'id' => $booking['sb_id'],
        'customer' => ($booking['u_fname'] ?? 'Guest') . ' ' . ($booking['u_lname'] ?? ''),
        'phone' => $booking['u_phone'] ?? 'N/A',
        'service' => $booking['s_name'] ?? 'Unknown Service',
        'status' => $booking['sb_status'],
        'created_at' => $booking['sb_created_at']
    ];
}

// Check for status updates (bookings updated by technicians)
$update_query = "SELECT COUNT(*) as update_count FROM tms_service_booking 
                 WHERE sb_updated_at > ? AND sb_updated_at != sb_created_at";
$update_stmt = $mysqli->prepare($update_query);
if($update_stmt) {
    $update_stmt->bind_param('s', $last_check);
    $update_stmt->execute();
    $update_result = $update_stmt->get_result();
    $update_data = $update_result->fetch_assoc();
    $update_count = $update_data['update_count'];
} else {
    $update_count = 0;
}

// Get details of updated bookings
$updated_bookings = [];
if($update_count > 0) {
    $update_details_query = "SELECT sb.sb_id, sb.sb_status, sb.sb_updated_at,
                                    u.u_fname, u.u_lname, u.u_phone,
                                    s.s_name
                             FROM tms_service_booking sb
                             LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                             LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                             WHERE sb.sb_updated_at > ? AND sb.sb_updated_at != sb.sb_created_at
                             ORDER BY sb.sb_updated_at DESC
                             LIMIT 5";
    $update_details_stmt = $mysqli->prepare($update_details_query);
    if($update_details_stmt) {
        $update_details_stmt->bind_param('s', $last_check);
        $update_details_stmt->execute();
        $update_details_result = $update_details_stmt->get_result();
        
        while($booking = $update_details_result->fetch_assoc()) {
            $updated_bookings[] = [
                'id' => $booking['sb_id'],
                'customer' => ($booking['u_fname'] ?? 'Guest') . ' ' . ($booking['u_lname'] ?? ''),
                'phone' => $booking['u_phone'] ?? 'N/A',
                'service' => $booking['s_name'] ?? 'Unknown Service',
                'status' => $booking['sb_status'],
                'updated_at' => $booking['sb_updated_at']
            ];
        }
    }
}

// Update last check timestamp to current time
$current_time = date('Y-m-d H:i:s');
$_SESSION['last_booking_check'] = $current_time;

// Return response with debug info
echo json_encode([
    'success' => true,
    'new_count' => (int)$data['new_count'],
    'has_new' => $data['new_count'] > 0,
    'bookings' => $new_bookings,
    'update_count' => (int)$update_count,
    'has_updates' => $update_count > 0,
    'updates' => $updated_bookings,
    'last_check' => $last_check,
    'current_time' => $current_time,
    'debug' => [
        'session_id' => session_id(),
        'query_executed' => true
    ]
]);

// Exit to prevent any trailing output
exit;