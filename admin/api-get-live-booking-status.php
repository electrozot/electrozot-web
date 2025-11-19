<?php
/**
 * API: Get Live Booking Status (Admin Version)
 * Returns real-time booking status with full details for admin
 */

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check admin authentication
if (!isset($_SESSION['a_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

require_once('vendor/inc/config.php');

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if (!$booking_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing booking ID'
    ]);
    exit;
}

// Get comprehensive booking details
$query = "SELECT 
            sb.*,
            s.s_name as service_name,
            s.s_price as service_price,
            t.t_name as technician_name,
            t.t_phone as technician_phone,
            t.t_ez_id as technician_ez_id,
            t.t_status as technician_status,
            t.t_current_bookings,
            t.t_booking_limit,
            u.u_fname, u.u_lname, u.u_email, u.u_phone as user_phone
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          WHERE sb.sb_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking not found'
    ]);
    exit;
}

$booking = $result->fetch_assoc();

// Get status workflow info
$workflow = getStatusWorkflow($booking['sb_status'], $booking['sb_technician_id']);

// Format response
$response = [
    'success' => true,
    'booking' => [
        'id' => $booking['sb_id'],
        'status' => $booking['sb_status'],
        'status_badge' => $workflow['badge'],
        'status_icon' => $workflow['icon'],
        'status_message' => $workflow['message'],
        'next_action' => $workflow['next_action'],
        'service' => [
            'name' => $booking['service_name'] ?: $booking['sb_custom_service'],
            'price' => $booking['sb_total_price'],
            'is_custom' => !empty($booking['sb_custom_service'])
        ],
        'schedule' => [
            'date' => date('M d, Y', strtotime($booking['sb_booking_date'])),
            'time' => date('h:i A', strtotime($booking['sb_booking_time'])),
            'deadline_date' => $booking['sb_service_deadline_date'] ? date('M d, Y', strtotime($booking['sb_service_deadline_date'])) : null,
            'deadline_time' => $booking['sb_service_deadline_time'] ? date('h:i A', strtotime($booking['sb_service_deadline_time'])) : null
        ],
        'customer' => [
            'name' => $booking['u_fname'] ? ($booking['u_fname'] . ' ' . $booking['u_lname']) : 'Guest Customer',
            'phone' => $booking['sb_phone'],
            'email' => $booking['u_email'],
            'address' => $booking['sb_address'],
            'pincode' => $booking['sb_pincode']
        ],
        'technician' => null,
        'timestamps' => [
            'created' => $booking['sb_created_at'] ? date('M d, Y h:i A', strtotime($booking['sb_created_at'])) : null,
            'assigned' => $booking['sb_assigned_at'] ? date('M d, Y h:i A', strtotime($booking['sb_assigned_at'])) : null,
            'rejected' => $booking['sb_rejected_at'] ? date('M d, Y h:i A', strtotime($booking['sb_rejected_at'])) : null,
            'completed' => $booking['sb_completed_at'] ? date('M d, Y h:i A', strtotime($booking['sb_completed_at'])) : null,
            'cancelled' => $booking['sb_cancelled_at'] ? date('M d, Y h:i A', strtotime($booking['sb_cancelled_at'])) : null,
            'updated' => $booking['sb_updated_at'] ? date('M d, Y h:i A', strtotime($booking['sb_updated_at'])) : null
        ],
        'notes' => [
            'description' => $booking['sb_description'],
            'rejection_reason' => $booking['sb_rejection_reason'],
            'completion_notes' => $booking['sb_completion_notes']
        ]
    ]
];

// Add technician info if assigned
if ($booking['sb_technician_id']) {
    $response['booking']['technician'] = [
        'id' => $booking['sb_technician_id'],
        'name' => $booking['technician_name'],
        'phone' => $booking['technician_phone'],
        'ez_id' => $booking['technician_ez_id'],
        'status' => $booking['technician_status'],
        'current_bookings' => $booking['t_current_bookings'],
        'booking_limit' => $booking['t_booking_limit'],
        'availability' => $booking['t_current_bookings'] < $booking['t_booking_limit'] ? 'Available' : 'Busy'
    ];
}

echo json_encode($response);

/**
 * Get status workflow information
 */
function getStatusWorkflow($status, $technician_id) {
    $workflows = [
        'Pending' => [
            'badge' => 'warning',
            'icon' => 'fa-clock',
            'message' => 'Awaiting Technician Assignment',
            'next_action' => 'Assign a technician to approve this booking'
        ],
        'Approved' => [
            'badge' => 'info',
            'icon' => 'fa-check-circle',
            'message' => 'Technician Assigned - Awaiting Service',
            'next_action' => 'Technician will complete the service'
        ],
        'In Progress' => [
            'badge' => 'primary',
            'icon' => 'fa-tools',
            'message' => 'Service in Progress',
            'next_action' => 'Technician is working on the service'
        ],
        'Completed' => [
            'badge' => 'success',
            'icon' => 'fa-check-double',
            'message' => 'Service Completed Successfully',
            'next_action' => 'No action needed - booking is complete'
        ],
        'Not Completed' => [
            'badge' => 'danger',
            'icon' => 'fa-exclamation-triangle',
            'message' => 'Service Not Completed by Technician',
            'next_action' => 'Reassign to another technician'
        ],
        'Rejected' => [
            'badge' => 'danger',
            'icon' => 'fa-times-circle',
            'message' => 'Booking Rejected',
            'next_action' => 'Reassign or cancel booking'
        ],
        'Cancelled' => [
            'badge' => 'secondary',
            'icon' => 'fa-ban',
            'message' => 'Booking Cancelled',
            'next_action' => 'No action needed - booking is cancelled'
        ]
    ];
    
    return $workflows[$status] ?? [
        'badge' => 'secondary',
        'icon' => 'fa-question-circle',
        'message' => $status,
        'next_action' => 'Review booking status'
    ];
}
?>
