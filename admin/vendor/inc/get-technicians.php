<?php
/**
 * Get Technicians for Assignment/Reassignment
 * Uses ULTIMATE TECHNICIAN MATCHER for all scenarios
 */
session_start();
include('config.php');
require_once('ultimate-technician-matcher.php');

// Ensure booking limit columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 3");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0");
} catch(Exception $e) {}

// Get parameters
$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$service_name = isset($_POST['service_name']) ? trim($_POST['service_name']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : date('Y-m-d');
$booking_time = isset($_POST['booking_time']) ? $_POST['booking_time'] : '10:00:00';
$exclude_booking_id = isset($_POST['exclude_booking_id']) ? intval($_POST['exclude_booking_id']) : null;

// Use ULTIMATE smart matcher
if ($service_id > 0) {
    // Best method: Get by service ID with full validation
    $technicians = getSmartAvailableTechnicians($mysqli, $service_id, $booking_date, $booking_time, $exclude_booking_id);
    echo formatSmartTechnicianOptions($technicians);
} else if (!empty($service_name)) {
    // For reassignment: Get by service name
    $technicians = getSmartTechniciansForReassignment($mysqli, $service_name, $category, $booking_date, $booking_time, $exclude_booking_id);
    echo formatSmartTechnicianOptions($technicians);
} else {
    // No service specified - show all available technicians
    $query = "SELECT 
                t_id,
                t_name,
                t_phone,
                t_category,
                t_experience,
                t_booking_limit,
                t_current_bookings,
                t_skills,
                (t_booking_limit - t_current_bookings) as available_slots
              FROM tms_technician 
              WHERE t_status != 'Inactive'
              AND t_current_bookings < t_booking_limit
              ORDER BY available_slots DESC, t_category, t_name";
    
    $result = $mysqli->query($query);
    $technicians = [];
    
    while ($tech = $result->fetch_assoc()) {
        $time_slot_check = checkTimeSlotAvailability($mysqli, $tech['t_id'], $booking_date, $booking_time, $exclude_booking_id);
        
        $tech['match_type'] = 'general';
        $tech['has_capacity'] = true;
        $tech['has_free_slot'] = $time_slot_check['available'];
        $tech['is_available'] = $time_slot_check['available'];
        $tech['slot_message'] = $time_slot_check['message'];
        $tech['conflicting_bookings'] = $time_slot_check['conflicting_count'];
        $tech['unavailable_reason'] = $time_slot_check['message'];
        
        $technicians[] = $tech;
    }
    
    echo formatSmartTechnicianOptions($technicians);
}
?>
