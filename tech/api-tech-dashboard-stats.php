<?php
// Suppress any PHP errors/warnings that would break JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any stray output
ob_start();

session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

// Ensure hold system columns exist before querying
try {
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_on_hold TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_high_priority TINYINT(1) DEFAULT 0");
} catch(Exception $e) {}

header('Content-Type: application/json');

$tech_id = $_SESSION['t_id'];

// Get booking counts
$pending_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Approved'";
$stmt = $mysqli->prepare($pending_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$pending_count = $stmt->get_result()->fetch_assoc()['count'];

$progress_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Confirmed'";
$stmt = $mysqli->prepare($progress_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$progress_count = $stmt->get_result()->fetch_assoc()['count'];

$completed_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Completed'";
$stmt = $mysqli->prepare($completed_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$completed_count = $stmt->get_result()->fetch_assoc()['count'];

$total_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ?";
$stmt = $mysqli->prepare($total_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$total_count = $stmt->get_result()->fetch_assoc()['count'];

// Get recent bookings
$recent_query = "SELECT sb.sb_id, sb.sb_status, sb.sb_booking_date, sb.sb_booking_time, 
                 s.s_name, u.u_fname, u.u_lname 
                 FROM tms_service_booking sb 
                 LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                 LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id 
                 WHERE sb.sb_technician_id = ? 
                 ORDER BY sb.sb_created_at DESC 
                 LIMIT 5";
$stmt = $mysqli->prepare($recent_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$recent_result = $stmt->get_result();
$recent_bookings = [];
while ($row = $recent_result->fetch_assoc()) {
    $recent_bookings[] = $row;
}

// Clear any buffered output
ob_clean();

echo json_encode([
    'success' => true,
    'stats' => [
        'pending' => $pending_count,
        'progress' => $progress_count,
        'completed' => $completed_count,
        'total' => $total_count
    ],
    'recent_bookings' => $recent_bookings
]);

// End output buffering
ob_end_flush();
?>
