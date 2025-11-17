<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

try {
    // Get the latest EZ ID from database
    $query = "SELECT t_ez_id FROM tms_technician WHERE t_ez_id LIKE 'EZ%' ORDER BY t_ez_id DESC LIMIT 1";
    $result = $mysqli->query($query);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastEZID = $row['t_ez_id'];
        
        // Extract the numeric part (e.g., "EZ0001" -> "0001")
        $numericPart = preg_replace('/[^0-9]/', '', $lastEZID);
        
        if($numericPart) {
            // Increment the number
            $nextNumber = intval($numericPart) + 1;
        } else {
            // If no valid number found, start from 1
            $nextNumber = 1;
        }
    } else {
        // No existing EZ IDs, start from 1
        $nextNumber = 1;
    }
    
    // Format as EZ0001, EZ0002, etc. (4 digits with leading zeros)
    $nextEZID = 'EZ' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    
    // Double-check this ID doesn't exist (in case of race condition)
    $checkQuery = "SELECT t_id FROM tms_technician WHERE t_ez_id = ?";
    $stmt = $mysqli->prepare($checkQuery);
    $stmt->bind_param('s', $nextEZID);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows > 0) {
        // ID already exists, try next one
        $nextNumber++;
        $nextEZID = 'EZ' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    echo json_encode([
        'success' => true,
        'ez_id' => $nextEZID,
        'message' => 'EZ ID generated successfully'
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error generating EZ ID: ' . $e->getMessage()
    ]);
}
?>
