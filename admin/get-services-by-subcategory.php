<?php
// AJAX endpoint to get services based on selected subcategory
session_start();
include('vendor/inc/config.php');

header('Content-Type: application/json');

if(isset($_POST['subcategory'])) {
    $subcategory = $_POST['subcategory'];
    
    // Get active services for the selected subcategory
    $query = "SELECT s_id, s_name, s_price, s_gadget_name 
              FROM tms_service 
              WHERE s_subcategory = ? AND s_status = 'Active' 
              ORDER BY s_name";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $subcategory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while($row = $result->fetch_assoc()) {
        $services[] = [
            'id' => $row['s_id'],
            'name' => $row['s_name'],
            'price' => $row['s_price'],
            'gadget_name' => $row['s_gadget_name']
        ];
    }
    
    echo json_encode(['success' => true, 'services' => $services]);
} else {
    echo json_encode(['success' => false, 'message' => 'Subcategory not provided']);
}
?>
