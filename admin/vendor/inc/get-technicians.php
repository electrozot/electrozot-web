<?php
session_start();
include('config.php');

if(isset($_POST['category']) && isset($_POST['service_name'])) {
    $category = $_POST['category'];
    $service_name = $_POST['service_name'];
    
    // Get available technicians matching service name OR category (same logic as assignment)
    $query = "SELECT t_id, t_name, t_id_no, t_category FROM tms_technician 
              WHERE (t_category = ? OR t_category = ?) AND t_status = 'Available' 
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $service_name, $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '<option value="">-- Select Technician --</option>';
    $matching_count = 0;
    
    while($tech = $result->fetch_object()) {
        $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ' - ID: ' . $tech->t_id_no . ')</option>';
        $matching_count++;
    }
    
    // If no matching technicians found, show message
    if($matching_count == 0) {
        $options .= '<option disabled>--- No available technicians for this service/category ---</option>';
    }
    
    echo $options;
} else if(isset($_POST['category'])) {
    // Fallback for old calls (backward compatibility)
    $category = $_POST['category'];
    
    $query = "SELECT t_id, t_name, t_id_no, t_category FROM tms_technician 
              WHERE t_category = ? AND t_status = 'Available' 
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '<option value="">-- Select Technician --</option>';
    $matching_count = 0;
    
    while($tech = $result->fetch_object()) {
        $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ' - ID: ' . $tech->t_id_no . ')</option>';
        $matching_count++;
    }
    
    if($matching_count == 0) {
        $options .= '<option disabled>--- No available technicians for this category ---</option>';
    }
    
    echo $options;
}
?>
