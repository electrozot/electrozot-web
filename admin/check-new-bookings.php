<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

// Get the last check timestamp from session
$last_check = isset($_SESSION['last_booking_check']) ? $_SESSION['last_booking_check'] : date('Y-m-d H:i:s', strtotime('-1 hour'));

// Query for new bookings since last check
$query = "SELECT sb.sb_id, sb.sb_created_at, u.u_fname, u.u_lname, s.s_name, sb.sb_custom_service
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_created_at > ? 
          AND sb.sb_status = 'Pending'
          ORDER BY sb.sb_created_at DESC
          LIMIT 10";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $last_check);
$stmt->execute();
$result = $stmt->get_result();

$new_bookings = [];
while($row = $result->fetch_assoc()) {
    $service_name = !empty($row['sb_custom_service']) ? $row['sb_custom_service'] : $row['s_name'];
    $customer_name = $row['u_fname'] . ' ' . $row['u_lname'];
    
    $new_bookings[] = [
        'id' => $row['sb_id'],
        'customer' => $customer_name,
        'service' => $service_name,
        'time' => date('h:i A', strtotime($row['sb_created_at']))
    ];
}

// Update last check timestamp
$_SESSION['last_booking_check'] = date('Y-m-d H:i:s');

echo json_encode([
    'success' => true,
    'count' => count($new_bookings),
    'bookings' => $new_bookings
]);
?>
