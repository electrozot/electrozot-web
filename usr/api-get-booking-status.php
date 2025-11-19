<?php
/**
 * API: Get Real-Time Booking Status
 * Returns live booking status for customers
 */

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once('../admin/vendor/inc/config.php');

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$phone = isset($_GET['phone']) ? $_GET['phone'] : (isset($_SESSION['u_phone']) ? $_SESSION['u_phone'] : '');

if (!$booking_id || !$phone) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing booking ID or phone number'
    ]);
    exit;
}

// Get booking details with technician info
$query = "SELECT 
            sb.*,
            s.s_name as service_name,
            t.t_name as technician_name,
            t.t_phone as technician_phone,
            t.t_ez_id as technician_ez_id
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          WHERE sb.sb_id = ? AND sb.sb_phone = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('is', $booking_id, $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking not found or unauthorized'
    ]);
    exit;
}

$booking = $result->fetch_assoc();

// Determine status details
$status_info = getStatusInfo($booking['sb_status'], $booking['sb_technician_id']);

// Format response
$response = [
    'success' => true,
    'booking' => [
        'id' => $booking['sb_id'],
        'status' => $booking['sb_status'],
        'status_color' => $status_info['color'],
        'status_icon' => $status_info['icon'],
        'status_message' => $status_info['message'],
        'status_description' => $status_info['description'],
        'service_name' => $booking['service_name'] ?: $booking['sb_custom_service'],
        'booking_date' => date('M d, Y', strtotime($booking['sb_booking_date'])),
        'booking_time' => date('h:i A', strtotime($booking['sb_booking_time'])),
        'total_price' => $booking['sb_total_price'],
        'address' => $booking['sb_address'],
        'technician' => null,
        'timeline' => getBookingTimeline($booking),
        'can_cancel' => in_array($booking['sb_status'], ['Pending', 'Approved']),
        'updated_at' => $booking['sb_updated_at'] ? date('M d, Y h:i A', strtotime($booking['sb_updated_at'])) : null
    ]
];

// Add technician info if assigned
if ($booking['sb_technician_id']) {
    $response['booking']['technician'] = [
        'name' => $booking['technician_name'],
        'phone' => $booking['technician_phone'],
        'ez_id' => $booking['technician_ez_id']
    ];
}

echo json_encode($response);

/**
 * Get status information with color, icon, and messages
 */
function getStatusInfo($status, $technician_id) {
    $info = [
        'Pending' => [
            'color' => 'warning',
            'icon' => 'fa-clock',
            'message' => 'Awaiting Technician Assignment',
            'description' => 'Your booking is confirmed. We are finding the best technician for you.'
        ],
        'Approved' => [
            'color' => 'info',
            'icon' => 'fa-check-circle',
            'message' => 'Technician Assigned',
            'description' => 'A technician has been assigned to your booking. They will contact you soon.'
        ],
        'In Progress' => [
            'color' => 'primary',
            'icon' => 'fa-tools',
            'message' => 'Work in Progress',
            'description' => 'The technician is currently working on your service.'
        ],
        'Completed' => [
            'color' => 'success',
            'icon' => 'fa-check-double',
            'message' => 'Service Completed',
            'description' => 'Your service has been completed successfully. Thank you for choosing us!'
        ],
        'Not Completed' => [
            'color' => 'danger',
            'icon' => 'fa-exclamation-triangle',
            'message' => 'Service Not Completed',
            'description' => 'The technician was unable to complete the service. We will assign another technician shortly.'
        ],
        'Rejected' => [
            'color' => 'danger',
            'icon' => 'fa-times-circle',
            'message' => 'Booking Rejected',
            'description' => 'This booking was rejected. Please contact support for assistance.'
        ],
        'Cancelled' => [
            'color' => 'secondary',
            'icon' => 'fa-ban',
            'message' => 'Booking Cancelled',
            'description' => 'This booking has been cancelled.'
        ]
    ];
    
    return $info[$status] ?? [
        'color' => 'secondary',
        'icon' => 'fa-question-circle',
        'message' => $status,
        'description' => 'Status information not available.'
    ];
}

/**
 * Get booking timeline/history
 */
function getBookingTimeline($booking) {
    $timeline = [];
    
    // Booking created
    if ($booking['sb_created_at']) {
        $timeline[] = [
            'event' => 'Booking Created',
            'timestamp' => date('M d, Y h:i A', strtotime($booking['sb_created_at'])),
            'icon' => 'fa-plus-circle',
            'color' => 'success'
        ];
    }
    
    // Technician assigned
    if ($booking['sb_technician_id'] && $booking['sb_assigned_at']) {
        $timeline[] = [
            'event' => 'Technician Assigned',
            'timestamp' => date('M d, Y h:i A', strtotime($booking['sb_assigned_at'])),
            'icon' => 'fa-user-check',
            'color' => 'info'
        ];
    }
    
    // Rejected
    if ($booking['sb_rejected_at']) {
        $timeline[] = [
            'event' => 'Service Not Completed',
            'timestamp' => date('M d, Y h:i A', strtotime($booking['sb_rejected_at'])),
            'icon' => 'fa-exclamation-triangle',
            'color' => 'warning',
            'note' => $booking['sb_rejection_reason']
        ];
    }
    
    // Completed
    if ($booking['sb_completed_at']) {
        $timeline[] = [
            'event' => 'Service Completed',
            'timestamp' => date('M d, Y h:i A', strtotime($booking['sb_completed_at'])),
            'icon' => 'fa-check-circle',
            'color' => 'success'
        ];
    }
    
    // Cancelled
    if ($booking['sb_cancelled_at']) {
        $timeline[] = [
            'event' => 'Booking Cancelled',
            'timestamp' => date('M d, Y h:i A', strtotime($booking['sb_cancelled_at'])),
            'icon' => 'fa-ban',
            'color' => 'secondary'
        ];
    }
    
    return $timeline;
}
?>
