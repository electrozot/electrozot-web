<?php
session_start();
include('config.php');

// Ensure columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_booking_id INT DEFAULT NULL");
} catch(Exception $e) {}

// Common WHERE clause for availability - technician must be available AND not have current booking
$availability_check = "AND (t_is_available = 1 OR t_status = 'Available') AND (t_current_booking_id IS NULL OR t_current_booking_id = 0)";

if(isset($_POST['category']) && isset($_POST['service_name'])) {
    $category = $_POST['category'];
    $service_name = $_POST['service_name'];
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Get technicians who can do this EXACT service
    $query = "SELECT t_id, t_name, t_id_no, t_category 
              FROM tms_technician 
              WHERE t_category = ? $availability_check
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $options .= '<optgroup label="✅ Qualified for: ' . htmlspecialchars($service_name) . ' (' . $result->num_rows . ' available)">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (✓ Can do: ' . htmlspecialchars($service_name) . ')</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available technicians for ' . htmlspecialchars($service_name) . '</option>';
    }
    
    echo $options;
    
} else if(isset($_POST['category'])) {
    $category = $_POST['category'];
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Get available technicians for this category
    $query = "SELECT t_id, t_name, t_id_no, t_category 
              FROM tms_technician 
              WHERE t_category = ? $availability_check
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $options .= '<optgroup label="✅ Available ' . htmlspecialchars($category) . ' (' . $result->num_rows . ')">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' - ' . $tech->t_category . '</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available ' . htmlspecialchars($category) . ' technicians</option>';
    }
    
    echo $options;
    
} else {
    // Get all available technicians
    $query = "SELECT t_id, t_name, t_id_no, t_category 
              FROM tms_technician 
              WHERE 1=1 $availability_check
              ORDER BY t_category, t_name";
    $result = $mysqli->query($query);
    
    $options = '<option value="">-- Select Technician --</option>';
    
    if($result->num_rows > 0) {
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ')</option>';
        }
    } else {
        $options .= '<option disabled>❌ No available technicians</option>';
    }
    
    echo $options;
}
?>
