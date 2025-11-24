<?php
/**
 * AJAX: Get available technicians for a specific service
 * Used when changing service during assignment/reassignment
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');
require_once('vendor/inc/ultimate-technician-matcher.php');

$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;

if (!$service_id) {
    echo json_encode(['success' => false, 'message' => 'Service ID required']);
    exit;
}

// Get booking details
$booking_query = "SELECT sb_booking_date, sb_booking_time FROM tms_service_booking WHERE sb_id = ?";
$stmt = $mysqli->prepare($booking_query);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$booking_result = $stmt->get_result();
$booking = $booking_result->fetch_assoc();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

// Get service details
$service_query = "SELECT s_name, s_category FROM tms_service WHERE s_id = ?";
$stmt = $mysqli->prepare($service_query);
$stmt->bind_param('i', $service_id);
$stmt->execute();
$service_result = $stmt->get_result();
$service = $service_result->fetch_assoc();

if (!$service) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit;
}

// Check if custom service
$is_custom_service = (stripos($service['s_name'], 'Custom Service') !== false || 
                      stripos($service['s_name'], 'Other') !== false);

$technicians_list = [];

if ($is_custom_service) {
    // For custom services, show ALL technicians with capacity
    $all_techs_query = "SELECT t.t_id, t.t_name, t.t_experience, t.t_current_bookings, t.t_booking_limit,
                               (t.t_booking_limit - t.t_current_bookings) as available_slots,
                               t.t_skills
                        FROM tms_technician t
                        WHERE t.t_status != 'Inactive'
                        ORDER BY 
                            CASE WHEN t.t_current_bookings < t.t_booking_limit THEN 0 ELSE 1 END,
                            t.t_experience DESC,
                            t.t_name ASC";
    $result = $mysqli->query($all_techs_query);
    
    while ($tech = $result->fetch_assoc()) {
        $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
        $slots = $tech['available_slots'];
        $skills = !empty($tech['t_skills']) ? ' | Skills: '.$tech['t_skills'] : '';
        $is_available = ($slots > 0);
        
        $display_name = $tech['t_name'] . ' ('.$exp.', '.$slots.' slot'.($slots!=1?'s':'').' free)'.$skills;
        
        $technicians_list[] = [
            't_id' => $tech['t_id'],
            'display_name' => $display_name,
            'disabled' => !$is_available,
            'available_slots' => $slots,
            'is_available' => $is_available
        ];
    }
} else {
    // Regular service - use smart matcher
    $available_techs = getSmartAvailableTechnicians(
        $mysqli, 
        $service_id, 
        $booking['sb_booking_date'],
        $booking['sb_booking_time'],
        $booking_id
    );
    
    if (!empty($available_techs)) {
        foreach ($available_techs as $tech) {
            $exp = $tech['t_experience'] ? $tech['t_experience'].' yrs' : 'New';
            $slots = $tech['available_slots'];
            $is_available = isset($tech['is_available']) ? $tech['is_available'] : true;
            
            $display_name = $tech['t_name'] . ' ('.$exp.', '.$slots.' slot'.($slots!=1?'s':'').' free) - '.$tech['slot_message'];
            
            $technicians_list[] = [
                't_id' => $tech['t_id'],
                'display_name' => $display_name,
                'disabled' => !$is_available,
                'available_slots' => $slots,
                'is_available' => $is_available,
                'match_type' => $tech['match_type']
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'technicians' => $technicians_list,
    'service_name' => $service['s_name'],
    'is_custom_service' => $is_custom_service
]);
?>
