<?php
/**
 * API: Check for booking updates for customer
 * Returns new notifications about booking status changes
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['u_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');

$user_id = $_SESSION['u_id'];
$last_check = isset($_POST['last_check']) ? $_POST['last_check'] : date('Y-m-d H:i:s', strtotime('-1 minute'));

// Get booking updates since last check
$query = "SELECT sb.sb_id, sb.sb_status, sb.sb_booking_date, sb.sb_updated_at,
                 s.s_name as service_name,
                 t.t_name as technician_name,
                 sb.sb_is_on_hold, sb.sb_hold_reason
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          WHERE sb.sb_user_id = ? 
          AND sb.sb_updated_at > ?
          AND sb.sb_status NOT IN ('Cancelled')
          ORDER BY sb.sb_updated_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('is', $user_id, $last_check);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($booking = $result->fetch_object()) {
    $notification = [
        'booking_id' => $booking->sb_id,
        'service_name' => $booking->service_name,
        'status' => $booking->sb_status,
        'technician' => $booking->technician_name,
        'updated_at' => $booking->sb_updated_at,
        'is_on_hold' => $booking->sb_is_on_hold,
        'hold_reason' => $booking->sb_hold_reason
    ];
    
    // Create notification message based on status
    switch ($booking->sb_status) {
        case 'Pending':
            $notification['title'] = 'Booking Received';
            $notification['message'] = 'Your booking for ' . $booking->service_name . ' has been received and is being processed.';
            $notification['type'] = 'info';
            break;
        case 'Approved':
            $notification['title'] = 'Booking Approved';
            $notification['message'] = 'Your booking has been approved! Technician will be assigned soon.';
            $notification['type'] = 'success';
            break;
        case 'Assigned':
            $notification['title'] = 'Technician Assigned';
            $notification['message'] = $booking->technician_name . ' has been assigned to your booking.';
            $notification['type'] = 'success';
            break;
        case 'In Progress':
            $notification['title'] = 'Service Started';
            $notification['message'] = 'Your service is now in progress!';
            $notification['type'] = 'info';
            break;
        case 'Completed':
            $notification['title'] = 'Service Completed';
            $notification['message'] = 'Your service has been completed successfully!';
            $notification['type'] = 'success';
            break;
        case 'On Hold':
            $notification['title'] = 'Booking On Hold';
            $notification['message'] = 'Your booking has been put on hold. Reason: ' . ($booking->hold_reason ?? 'Pending customer action');
            $notification['type'] = 'warning';
            break;
        case 'Rejected':
            $notification['title'] = 'Booking Rejected';
            $notification['message'] = 'Your booking has been rejected. Please contact support.';
            $notification['type'] = 'error';
            break;
        default:
            $notification['title'] = 'Booking Updated';
            $notification['message'] = 'Your booking status has been updated to: ' . $booking->sb_status;
            $notification['type'] = 'info';
    }
    
    $notifications[] = $notification;
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'count' => count($notifications),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
