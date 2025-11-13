<?php
session_start();
include('config.php');

if(isset($_POST['category'])) {
    $category = $_POST['category'];
    
    $query = "SELECT t_id, t_name, t_id_no FROM tms_technician WHERE t_category = ? AND t_status = 'Available' ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '<option value="">-- Select Technician --</option>';
    
    while($tech = $result->fetch_object()) {
        $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (ID: ' . $tech->t_id_no . ')</option>';
    }
    
    echo $options;
}
?>
