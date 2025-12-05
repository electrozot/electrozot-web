<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Ensure bills table exists
$mysqli->query("CREATE TABLE IF NOT EXISTS `tms_bills` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_booking_id` int(11) NOT NULL,
  `bill_number` varchar(50) NOT NULL,
  `bill_generated_at` datetime NOT NULL,
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `bill_booking_id` (`bill_booking_id`),
  KEY `bill_number` (`bill_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if($booking_id == 0) {
    header('Location: admin-dashboard.php');
    exit();
}

// Check if bill is older than 30 days
$check_query = "SELECT bill_generated_at FROM tms_bills WHERE bill_booking_id = ?";
$check_stmt = $mysqli->prepare($check_query);
$check_stmt->bind_param('i', $booking_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if($check_result->num_rows > 0) {
    $bill_data = $check_result->fetch_object();
    $bill_date = strtotime($bill_data->bill_generated_at);
    $days_old = (time() - $bill_date) / (60 * 60 * 24);
    
    // If bill is older than 30 days, redirect silently
    if($days_old > 30) {
        header('Location: admin-view-service-booking.php?sb_id=' . $booking_id);
        exit();
    }
}

// Include bill generator
require_once('vendor/inc/bill-generator.php');

$billGen = new BillGenerator($mysqli, $booking_id);
$billHTML = $billGen->generateBillHTML();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bill - Electrozot Admin</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        
        .btn-action {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .btn-back:hover {
            background: #5a6268;
        }
        
        @media print {
            .action-buttons { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="action-buttons">
        <a href="admin-view-service-booking.php?sb_id=<?php echo $booking_id; ?>" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <?php echo $billHTML; ?>
</body>
</html>
