<?php
/**
 * Get All Bookings Status API
 * Checks if any booking status has changed for auto-refresh
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

// Get current booking statuses
$query = "SELECT sb_id, sb_status, sb_booking_date 
          FROM tms_service_booking 
          WHERE sb_user_id = ? 
          ORDER BY sb_created_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while($row = $result->fetch_assoc()) {
    $bookings[] = [
        'id' => $row['sb_id'],
        'status' => $row['sb_status'],
        'date' => $row['sb_booking_date']
    ];
}

// Store current state in session to compare
$current_state = json_encode($bookings);
$has_changes = false;

if(isset($_SESSION['last_booking_state'])) {
    $has_changes = ($_SESSION['last_booking_state'] !== $current_state);
}

$_SESSION['last_booking_state'] = $current_state;

echo json_encode([
    'success' => true,
    'has_changes' => $has_changes,
    'bookings' => $bookings
]);

$stmt->close();
$mysqli->close();
?>
