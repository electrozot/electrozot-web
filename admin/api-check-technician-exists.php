<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

$phone = isset($_GET['phone']) ? $_GET['phone'] : '';
$aadhar = isset($_GET['aadhar']) ? $_GET['aadhar'] : '';

try {
    $exists = false;
    $message = '';
    $technician = null;
    
    if (!empty($phone)) {
        // Check if phone exists
        $query = "SELECT t_id, t_name, t_ez_id, t_phone FROM tms_technician WHERE t_phone = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $exists = true;
            $technician = $result->fetch_assoc();
            $message = "Mobile number already registered to: " . $technician['t_name'] . " (EZ ID: " . $technician['t_ez_id'] . ")";
        }
    }
    
    if (!$exists && !empty($aadhar)) {
        // Check if Aadhaar exists
        $query = "SELECT t_id, t_name, t_ez_id, t_aadhar FROM tms_technician WHERE t_aadhar = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $aadhar);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $exists = true;
            $technician = $result->fetch_assoc();
            $message = "Aadhaar number already registered to: " . $technician['t_name'] . " (EZ ID: " . $technician['t_ez_id'] . ")";
        }
    }
    
    echo json_encode([
        'exists' => $exists,
        'message' => $message,
        'technician' => $technician
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'exists' => false,
        'message' => 'Error checking: ' . $e->getMessage()
    ]);
}
?>
