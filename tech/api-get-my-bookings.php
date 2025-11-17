<?php
/**
 * API: Get technician's assigned bookings
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['t_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');

$technician_id = $_SESSION['t_id'];

// Get active bookings
$stmt = $conn->prepare("
    SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr,
           s.s_name as service_name, s.s_description, s.s_price
    FROM tms_service_booking sb
    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
    WHERE sb.sb_technician_id = ? 
    AND sb.sb_status IN ('Approved', 'Pending')
    ORDER BY sb.sb_booking_date ASC, sb.sb_booking_time ASC
");
$stmt->execute([$technician_id]);
$active_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get completed bookings
$stmt = $conn->prepare("
    SELECT sb.*, u.u_fname, u.u_lname, s.s_name as service_name
    FROM tms_service_booking sb
    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
    WHERE sb.sb_technician_id = ? 
    AND sb.sb_status = 'Completed'
    ORDER BY sb.sb_completed_at DESC
    LIMIT 10
");
$stmt->execute([$technician_id]);
$completed_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'active_bookings' => $active_bookings,
    'completed_bookings' => $completed_bookings
]);
?>
