<?php
/**
 * API: Check if any technician has exceeded rejection threshold
 * Returns technicians who need admin attention
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

$threshold = 3; // Rejection threshold

// Find technicians with 3+ rejections in last 7 days who haven't been notified
$query = "SELECT 
    t.t_id,
    t.t_name,
    t.t_phone,
    t.t_email,
    COUNT(tr.tr_id) as rejection_count,
    GROUP_CONCAT(
        CONCAT(
            'Booking #', tr.tr_booking_id, 
            ' - ', tr.tr_reason,
            ' (', DATE_FORMAT(tr.tr_rejected_at, '%d/%m/%Y %H:%i'), ')'
        ) 
        ORDER BY tr.tr_rejected_at DESC 
        SEPARATOR '|||'
    ) as rejection_details
FROM tms_technician t
INNER JOIN tms_technician_rejections tr ON t.t_id = tr.tr_technician_id
WHERE tr.tr_rejected_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    AND tr.tr_admin_notified = 0
GROUP BY t.t_id, t.t_name, t.t_phone, t.t_email
HAVING rejection_count >= ?
ORDER BY rejection_count DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $threshold);
$stmt->execute();
$result = $stmt->get_result();

$flagged_technicians = [];

while ($row = $result->fetch_assoc()) {
    // Get customer details for each rejected booking
    $booking_ids = [];
    $rejection_list = explode('|||', $row['rejection_details']);
    
    foreach ($rejection_list as $rejection) {
        if (preg_match('/Booking #(\d+)/', $rejection, $matches)) {
            $booking_ids[] = $matches[1];
        }
    }
    
    // Get customer info for these bookings
    $customers = [];
    if (!empty($booking_ids)) {
        $placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
        $customer_query = "SELECT sb_id, sb_phone, 
                          COALESCE(u.u_fname, 'Guest Customer') as customer_name
                          FROM tms_service_booking sb
                          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                          WHERE sb.sb_id IN ($placeholders)";
        $customer_stmt = $mysqli->prepare($customer_query);
        $customer_stmt->bind_param(str_repeat('i', count($booking_ids)), ...$booking_ids);
        $customer_stmt->execute();
        $customer_result = $customer_stmt->get_result();
        
        while ($customer = $customer_result->fetch_assoc()) {
            $customers[$customer['sb_id']] = [
                'name' => $customer['customer_name'],
                'phone' => $customer['sb_phone']
            ];
        }
    }
    
    $row['customers'] = $customers;
    $row['rejection_list'] = $rejection_list;
    $flagged_technicians[] = $row;
}

if (!empty($flagged_technicians)) {
    echo json_encode([
        'success' => true,
        'has_alerts' => true,
        'technicians' => $flagged_technicians,
        'threshold' => $threshold
    ]);
} else {
    echo json_encode([
        'success' => true,
        'has_alerts' => false
    ]);
}
?>
