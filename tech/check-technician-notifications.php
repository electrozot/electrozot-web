<?php
// Start output buffering to prevent any unwanted output
ob_start();

session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

// Clear any output that might have been generated
ob_end_clean();

// Now set the header
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$t_id = $_SESSION['t_id'];

// Ensure timestamp columns exist
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

// Get the last check timestamp from session (default to 30 seconds ago)
if(!isset($_SESSION['tech_last_check'])) {
    $_SESSION['tech_last_check'] = date('Y-m-d H:i:s', strtotime('-30 seconds'));
}
$last_check = $_SESSION['tech_last_check'];

// Check for new assignments (bookings assigned to this technician)
$new_assignments_query = "SELECT COUNT(*) as new_count 
                          FROM tms_service_booking 
                          WHERE sb_technician_id = ? 
                          AND sb_updated_at > ?
                          AND sb_status != 'Cancelled'";
$stmt = $mysqli->prepare($new_assignments_query);
if(!$stmt) {
    echo json_encode(['error' => 'Query preparation failed: ' . $mysqli->error]);
    exit;
}
$stmt->bind_param('is', $t_id, $last_check);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$new_count = $data['new_count'];

// Get details of new/updated bookings
$details_query = "SELECT sb.sb_id, sb.sb_status, sb.sb_updated_at, sb.sb_created_at,
                         sb.sb_service_deadline_date, sb.sb_service_deadline_time,
                         u.u_fname, u.u_lname, u.u_phone, u.u_addr,
                         s.s_name,
                         CASE 
                            WHEN sb.sb_created_at > ? THEN 'new_assignment'
                            WHEN sb.sb_updated_at > ? THEN 'status_update'
                            ELSE 'update'
                         END as notification_type
                  FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                  WHERE sb.sb_technician_id = ?
                  AND sb.sb_updated_at > ?
                  AND sb.sb_status != 'Cancelled'
                  ORDER BY sb.sb_updated_at DESC
                  LIMIT 5";
$details_stmt = $mysqli->prepare($details_query);
if(!$details_stmt) {
    echo json_encode(['error' => 'Details query preparation failed: ' . $mysqli->error]);
    exit;
}
$details_stmt->bind_param('ssis', $last_check, $last_check, $t_id, $last_check);
$details_stmt->execute();
$details_result = $details_stmt->get_result();

$notifications = [];
while($booking = $details_result->fetch_assoc()) {
    // Determine notification message
    $message = '';
    $action = '';
    
    if($booking['notification_type'] == 'new_assignment') {
        $message = 'New booking assigned to you';
        $action = 'assigned';
    } else {
        switch($booking['sb_status']) {
            case 'Pending':
                $message = 'Booking awaiting your action';
                $action = 'pending';
                break;
            case 'Approved':
                $message = 'Booking approved by admin';
                $action = 'approved';
                break;
            case 'In Progress':
                $message = 'Booking marked as in progress';
                $action = 'in_progress';
                break;
            case 'Completed':
                $message = 'Booking marked as completed';
                $action = 'completed';
                break;
            case 'Rejected':
                $message = 'Booking was rejected';
                $action = 'rejected';
                break;
            default:
                $message = 'Booking updated by admin';
                $action = 'updated';
        }
    }
    
    $notifications[] = [
        'id' => $booking['sb_id'],
        'customer' => ($booking['u_fname'] ?? 'Guest') . ' ' . ($booking['u_lname'] ?? ''),
        'phone' => $booking['u_phone'] ?? 'N/A',
        'address' => $booking['u_addr'] ?? 'N/A',
        'service' => $booking['s_name'] ?? 'Unknown Service',
        'status' => $booking['sb_status'],
        'deadline_date' => $booking['sb_service_deadline_date'],
        'deadline_time' => $booking['sb_service_deadline_time'],
        'message' => $message,
        'action' => $action,
        'updated_at' => $booking['sb_updated_at']
    ];
}

// Update last check timestamp to current time
$current_time = date('Y-m-d H:i:s');
$_SESSION['tech_last_check'] = $current_time;

// Return response
echo json_encode([
    'success' => true,
    'notification_count' => (int)$new_count,
    'has_notifications' => $new_count > 0,
    'notifications' => $notifications,
    'last_check' => $last_check,
    'current_time' => $current_time,
    'technician_id' => $t_id
]);

// Exit to prevent any trailing output
exit;
?>
