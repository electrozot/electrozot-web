<?php
header('Content-Type: application/json');
include('vendor/inc/config.php');

if(isset($_POST['subcategory'])) {
    $subcategory = $_POST['subcategory'];
    
    // Get services by subcategory
    $query = "SELECT s_id as id, s_name as name, s_gadget_name as gadget_name, s_price as price 
              FROM tms_service 
              WHERE s_subcategory = ? AND s_status = 'Active' 
              ORDER BY s_name ASC";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $subcategory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    if(count($services) > 0) {
        echo json_encode([
            'success' => true,
            'services' => $services
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No services found for this subcategory'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Subcategory not provided'
    ]);
}
?>
