<?php
session_start();
include('config.php');

header('Content-Type: application/json');

if(isset($_POST['phone'])) {
    $phone = $_POST['phone'];
    
    // Check if customer exists with this phone number
    $query = "SELECT u_id, u_fname, u_lname, u_email, u_phone, u_addr, u_area, u_pincode FROM tms_user WHERE u_phone = ? LIMIT 1";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            'exists' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'exists' => false
        ]);
    }
} else {
    echo json_encode([
        'exists' => false,
        'error' => 'Phone number not provided'
    ]);
}
?>
