<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

// Ensure payment collection table exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_payment_collection (
    pc_id INT AUTO_INCREMENT PRIMARY KEY,
    pc_booking_id INT NOT NULL,
    pc_amount DECIMAL(10,2) NOT NULL,
    pc_method ENUM('QR','TechQR','Cash') NOT NULL,
    pc_collected_by INT NOT NULL,
    pc_collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pc_status ENUM('Collected','Verified','Pending') DEFAULT 'Collected',
    INDEX(pc_booking_id)
)");

// Get today's revenue from payment collection
$revenue_query = "SELECT SUM(IFNULL(pc.pc_amount, 0)) as revenue
                  FROM tms_payment_collection pc
                  INNER JOIN tms_service_booking sb ON pc.pc_booking_id = sb.sb_id
                  WHERE sb.sb_status = 'Completed'
                  AND DATE(pc.pc_collected_at) = CURDATE()";

$result = $mysqli->query($revenue_query);
$revenue = 0;

if($result) {
    $row = $result->fetch_object();
    $revenue = $row->revenue ? floatval($row->revenue) : 0;
}

echo json_encode([
    'revenue' => $revenue,
    'formatted' => number_format($revenue, 0),
    'timestamp' => time()
]);
?>
