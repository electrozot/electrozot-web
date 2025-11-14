<?php
session_start();
include('config.php');

if(isset($_POST['category'])) {
    $category = $_POST['category'];
    
    // Get available technicians matching the service category
    // First, try to get technicians with matching category
    $query = "SELECT t_id, t_name, t_id_no, t_category FROM tms_technician 
              WHERE t_category = ? AND t_status = 'Available' 
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '<option value="">-- Select Technician --</option>';
    $matching_count = 0;
    
    // Add matching technicians first
    while($tech = $result->fetch_object()) {
        $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ' - ID: ' . $tech->t_id_no . ')</option>';
        $matching_count++;
    }
    
    // If no matching technicians found, show all available technicians with a separator
    if($matching_count == 0) {
        $options .= '<option disabled>--- No ' . htmlspecialchars($category) . ' technicians available ---</option>';
        $options .= '<option disabled>--- Other Available Technicians ---</option>';
        
        $query_all = "SELECT t_id, t_name, t_id_no, t_category FROM tms_technician 
                      WHERE t_status = 'Available' 
                      ORDER BY t_category, t_name";
        $stmt_all = $mysqli->prepare($query_all);
        $stmt_all->execute();
        $result_all = $stmt_all->get_result();
        
        while($tech = $result_all->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ' - ID: ' . $tech->t_id_no . ')</option>';
        }
    }
    
    echo $options;
}
?>
