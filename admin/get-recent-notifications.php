<?php
ob_start();
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Get recent bookings (last 10)
$query = "SELECT sb.sb_id, sb.sb_status, sb.sb_created_at, sb.sb_updated_at,
                 u.u_fname, u.u_lname, u.u_phone,
                 s.s_name,
                 t.t_name
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          ORDER BY sb.sb_created_at DESC
          LIMIT 10";

$result = $mysqli->query($query);

$notifications = [];
if($result) {
    while($row = $result->fetch_assoc()) {
        $customer = ($row['u_fname'] ?? 'Guest') . ' ' . ($row['u_lname'] ?? '');
        $service = $row['s_name'] ?? 'Unknown Service';
        $status = $row['sb_status'];
        $technician = $row['t_name'] ?? 'Not Assigned';
        
        // Determine icon and message
        $icon = '📋';
        $message = '';
        
        if($status == 'Pending') {
            $icon = '🆕';
            $message = "New Booking #" . $row['sb_id'] . " from " . $customer . " - " . $service;
        } elseif($status == 'Approved' || $status == 'Assigned') {
            $icon = '✅';
            $message = "Booking #" . $row['sb_id'] . " assigned to " . $technician . " - " . $service;
        } elseif($status == 'In Progress') {
            $icon = '🔧';
            $message = "Booking #" . $row['sb_id'] . " in progress by " . $technician;
        } elseif($status == 'Completed') {
            $icon = '✔️';
            $message = "Booking #" . $row['sb_id'] . " completed by " . $technician;
        } elseif($status == 'Rejected') {
            $icon = '❌';
            $message = "Booking #" . $row['sb_id'] . " rejected";
        } else {
            $message = "Booking #" . $row['sb_id'] . " - " . $status;
        }
        
        $notifications[] = [
            'id' => $row['sb_id'],
            'icon' => $icon,
            'message' => $message,
            'customer' => $customer,
            'service' => $service,
            'status' => $status,
            'technician' => $technician,
            'created_at' => $row['sb_created_at']
        ];
    }
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'count' => count($notifications)
]);

exit;
?>
