<?php
/**
 * AJAX endpoint to check if phone number is already registered
 * Returns JSON response
 */

header('Content-Type: application/json');

include('vendor/inc/config.php');

if(isset($_POST['phone'])) {
    $phone = trim($_POST['phone']);
    
    // Validate phone number format (10 digits)
    if(!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode([
            'available' => false,
            'message' => 'Invalid phone number format',
            'valid' => false
        ]);
        exit;
    }
    
    // Check if phone exists in database
    $stmt = $mysqli->prepare("SELECT u_id FROM tms_user WHERE u_phone = ?");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows > 0) {
        echo json_encode([
            'available' => false,
            'message' => 'This mobile number is already registered',
            'valid' => true
        ]);
    } else {
        echo json_encode([
            'available' => true,
            'message' => 'Mobile number is available',
            'valid' => true
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'available' => false,
        'message' => 'No phone number provided',
        'valid' => false
    ]);
}

$mysqli->close();
?>
