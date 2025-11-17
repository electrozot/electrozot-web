<?php
// AJAX endpoint to get subcategories based on selected category
session_start();
include('vendor/inc/config.php');

header('Content-Type: application/json');

if(isset($_POST['category'])) {
    $category = $_POST['category'];
    
    // Get subcategories for the selected category
    $query = "SELECT DISTINCT sc_subcategory FROM tms_service_categories 
              WHERE sc_category = ? AND sc_status = 'Active' 
              ORDER BY sc_subcategory";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subcategories = [];
    while($row = $result->fetch_assoc()) {
        $subcategories[] = $row['sc_subcategory'];
    }
    
    echo json_encode(['success' => true, 'subcategories' => $subcategories]);
} else {
    echo json_encode(['success' => false, 'message' => 'Category not provided']);
}
?>
