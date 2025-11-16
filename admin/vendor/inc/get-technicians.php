<?php
session_start();
include('config.php');

// Ensure columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_booking_id INT DEFAULT NULL");
} catch(Exception $e) {}

// CRITICAL: Availability check - technician must be available AND have NO current booking
$availability_check = "(t_is_available = 1 OR t_status = 'Available') AND (t_current_booking_id IS NULL OR t_current_booking_id = 0)";

if(isset($_POST['category']) && isset($_POST['service_name'])) {
    // Reassignment with service name and category
    $category = $_POST['category'];
    $service_name = $_POST['service_name'];
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Get ONLY available technicians who can do this EXACT service
    $query = "SELECT t_id, t_name, t_id_no, t_category, t_current_booking_id 
              FROM tms_technician 
              WHERE t_category = ? 
              AND $availability_check
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = $result->num_rows;
    
    if($count > 0) {
        $options .= '<optgroup label="✅ Available for: ' . htmlspecialchars($service_name) . ' (' . $count . ' free)">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (✓ Free & Qualified)</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available technicians for ' . htmlspecialchars($service_name) . '</option>';
    }
    
    echo $options;
    
} else if(isset($_POST['category'])) {
    // Reassignment with category only
    $category = $_POST['category'];
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Get ONLY available technicians for this category
    $query = "SELECT t_id, t_name, t_id_no, t_category, t_current_booking_id 
              FROM tms_technician 
              WHERE t_category = ? 
              AND $availability_check
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = $result->num_rows;
    
    if($count > 0) {
        $options .= '<optgroup label="✅ Available ' . htmlspecialchars($category) . ' (' . $count . ' free)">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (✓ Free)</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available ' . htmlspecialchars($category) . ' technicians (all busy)</option>';
    }
    
    echo $options;
    
} else {
    // Get all available technicians (no filters)
    $query = "SELECT t_id, t_name, t_id_no, t_category, t_current_booking_id 
              FROM tms_technician 
              WHERE $availability_check
              ORDER BY t_category, t_name";
    $result = $mysqli->query($query);
    
    $options = '<option value="">-- Select Technician --</option>';
    
    if($result->num_rows > 0) {
        $options .= '<optgroup label="✅ All Available Technicians (' . $result->num_rows . ' free)">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ' - ✓ Free)</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available technicians (all busy with bookings)</option>';
    }
    
    echo $options;
}
?>
