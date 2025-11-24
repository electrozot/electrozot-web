<?php
session_start();
include('config.php');
include('technician-matcher.php');

// Ensure booking limit columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 1");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0");
} catch(Exception $e) {}

// Get parameters
$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$service_name = isset($_POST['service_name']) ? $_POST['service_name'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';

// Use new skill-based matcher
if ($service_id > 0) {
    // Get technicians by service ID (best method)
    $technicians = getAvailableTechniciansForService($mysqli, $service_id);
    echo formatTechniciansAsOptions($technicians);
} else if (!empty($service_name)) {
    // Get technicians by service name (for reassignment)
    $technicians = getAvailableTechniciansByServiceName($mysqli, $service_name, $category);
    echo formatTechniciansAsOptions($technicians);
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
                (t_booking_limit - t_current_bookings) as available_slots,
                NULL as skill_name,
                'general' as match_type
              FROM tms_technician 
              WHERE t_current_bookings < t_booking_limit
              ORDER BY available_slots DESC, t_category, t_name";
    
    $result = $mysqli->query($query);
    $technicians = [];
    
    while ($tech = $result->fetch_assoc()) {
        $technicians[] = $tech;
    }
    
    if (empty($technicians)) {
        echo '<option value="">❌ No available technicians</option>';
    } else {
        echo '<option value="">-- Select Technician --</option>';
        echo '<optgroup label="✅ All Available Technicians (' . count($technicians) . ')">';
        foreach ($technicians as $tech) {
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            echo sprintf(
                '<option value="%d">%s (%s, %s exp, %d slot%s free)</option>',
                $tech['t_id'],
                htmlspecialchars($tech['t_name']),
                htmlspecialchars($tech['t_category']),
                $exp,
                $slots,
                $slots != 1 ? 's' : ''
            );
        }
        echo '</optgroup>';
    }
}
?>
