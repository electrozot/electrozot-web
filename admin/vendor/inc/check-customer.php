<?php
// Check if customer exists by phone number
session_start();
include('config.php');

header('Content-Type: application/json');

if(isset($_POST['phone'])) {
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    
    if(strlen($phone) === 10) {
        $query = "SELECT u_id, u_fname, u_lname, u_email, u_addr, u_area, u_pincode 
                  FROM tms_user 
                  WHERE u_phone = ?";
        
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
            echo json_encode(['exists' => false]);
        }
    } else {
        echo json_encode(['exists' => false, 'error' => 'Invalid phone number']);
    }
} else {
    echo json_encode(['exists' => false, 'error' => 'Phone number not provided']);
}
?>
