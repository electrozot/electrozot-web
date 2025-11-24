<?php
/**
 * API: Check Skill Match
 * Returns technicians who have the required skill
 */
session_start();
include('vendor/inc/config.php');
header('Content-Type: application/json');

$service_name = isset($_GET['service']) ? $_GET['service'] : (isset($_POST['service']) ? $_POST['service'] : '');

if (empty($service_name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Service name is required'
    ]);
    exit;
}

try {
    // Find technicians with this skill
    $query = "SELECT t_id, t_name, t_phone, t_email, t_status, t_category,
                     t_current_bookings, t_booking_limit, t_skills,
                     (t_booking_limit - t_current_bookings) as available_slots
              FROM tms_technician 
              WHERE t_status = 'Available' 
                AND t_current_bookings < t_booking_limit
                AND FIND_IN_SET(?, t_skills) > 0
              ORDER BY available_slots DESC, t_current_bookings ASC";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $technicians = [];
    while ($row = $result->fetch_assoc()) {
        $technicians[] = [
            't_id' => $row['t_id'],
            't_name' => $row['t_name'],
            't_phone' => $row['t_phone'],
            't_email' => $row['t_email'],
            't_status' => $row['t_status'],
            't_category' => $row['t_category'],
            't_current_bookings' => $row['t_current_bookings'],
            't_booking_limit' => $row['t_booking_limit'],
            'available_slots' => $row['available_slots'],
            'skills' => explode(',', $row['t_skills'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'service_name' => $service_name,
        'count' => count($technicians),
        'technicians' => $technicians,
        'message' => count($technicians) > 0 
            ? count($technicians) . ' technician(s) available with this skill' 
            : 'No technicians available with this skill'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
