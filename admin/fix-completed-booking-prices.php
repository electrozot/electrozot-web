<?php
/**
 * Fix Completed Booking Prices
 * This script migrates price data for completed bookings that show ₹0
 * It checks for prices in payment records and updates the booking accordingly
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$aid = $_SESSION['a_id'];

// Initialize counters
$fixed_count = 0;
$skipped_count = 0;
$errors = [];

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Completed Booking Prices</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .success { color: #10b981; padding: 10px; background: #d1fae5; border-radius: 5px; margin: 10px 0; }
        .error { color: #ef4444; padding: 10px; background: #fee2e2; border-radius: 5px; margin: 10px 0; }
        .info { color: #3b82f6; padding: 10px; background: #dbeafe; border-radius: 5px; margin: 10px 0; }
        .warning { color: #f59e0b; padding: 10px; background: #fef3c7; border-radius: 5px; margin: 10px 0; }
        .booking-item { padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; margin: 10px 0; background: #f9fafb; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
<div class='container'>
<h1>🔧 Fix Completed Booking Prices</h1>
<p>This tool will fix completed bookings that show ₹0 by checking payment records and service prices.</p>
<hr>";

// Step 1: Find all completed bookings with zero or null final price
$query = "SELECT 
            sb.sb_id,
            sb.sb_total_price,
            sb.sb_final_price,
            sb.sb_tech_decided_price,
            sb.sb_price_set_by_tech,
            sb.sb_status,
            s.s_price as service_price,
            s.s_name as service_name,
            u.u_fname,
            u.u_lname
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          WHERE sb.sb_status = 'Completed'
          AND (sb.sb_final_price IS NULL OR sb.sb_final_price = 0)
          ORDER BY sb.sb_id DESC";

$result = $mysqli->query($query);

if(!$result) {
    echo "<div class='error'>❌ Query Error: " . $mysqli->error . "</div>";
    echo "</div></body></html>";
    exit;
}

echo "<div class='info'>📊 Found " . $result->num_rows . " completed bookings with zero price</div>";

if($result->num_rows == 0) {
    echo "<div class='success'>✅ All completed bookings have prices set correctly!</div>";
    echo "<a href='admin-dashboard.php' class='btn'>← Back to Dashboard</a>";
    echo "</div></body></html>";
    exit;
}

// Step 2: Check if payment table exists
$payment_table_exists = false;
$check_table = $mysqli->query("SHOW TABLES LIKE 'tms_payment_collection'");
if($check_table && $check_table->num_rows > 0) {
    $payment_table_exists = true;
    echo "<div class='info'>✓ Payment collection table found</div>";
} else {
    echo "<div class='warning'>⚠️ Payment collection table not found - will use service/booking prices</div>";
}

// Step 3: Process each booking
while($booking = $result->fetch_object()) {
    echo "<div class='booking-item'>";
    echo "<strong>Booking #{$booking->sb_id}</strong> - {$booking->service_name}<br>";
    echo "Customer: {$booking->u_fname} {$booking->u_lname}<br>";
    echo "Current Price: ₹" . number_format($booking->sb_total_price, 2) . "<br>";
    
    $price_to_set = null;
    $price_source = '';
    
    // Try to find price from payment records
    if($payment_table_exists) {
        $payment_query = "SELECT pc_amount FROM tms_payment_collection 
                         WHERE pc_booking_id = ? 
                         ORDER BY pc_collected_at DESC LIMIT 1";
        $stmt = $mysqli->prepare($payment_query);
        $stmt->bind_param('i', $booking->sb_id);
        $stmt->execute();
        $payment_result = $stmt->get_result();
        
        if($payment_result && $payment_result->num_rows > 0) {
            $payment = $payment_result->fetch_object();
            $price_to_set = $payment->pc_amount;
            $price_source = 'payment collection record';
        }
    }
    
    // If no payment found, try service price
    if($price_to_set === null && !empty($booking->service_price) && $booking->service_price > 0) {
        $price_to_set = $booking->service_price;
        $price_source = 'service price';
    }
    
    // If still no price, try sb_total_price
    if($price_to_set === null && !empty($booking->sb_total_price) && $booking->sb_total_price > 0) {
        $price_to_set = $booking->sb_total_price;
        $price_source = 'booking total price';
    }
    
    // Update the booking if we found a price
    if($price_to_set !== null && $price_to_set > 0) {
        $update_query = "UPDATE tms_service_booking 
                        SET sb_final_price = ?,
                            sb_tech_decided_price = ?,
                            sb_price_set_by_tech = 1
                        WHERE sb_id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('ddi', $price_to_set, $price_to_set, $booking->sb_id);
        
        if($update_stmt->execute()) {
            echo "<span style='color: #10b981;'>✅ Fixed! Set price to ₹" . number_format($price_to_set, 2) . " (from {$price_source})</span>";
            $fixed_count++;
        } else {
            echo "<span style='color: #ef4444;'>❌ Error updating: " . $update_stmt->error . "</span>";
            $errors[] = "Booking #{$booking->sb_id}: " . $update_stmt->error;
        }
    } else {
        echo "<span style='color: #f59e0b;'>⚠️ Skipped - No price found in any source</span>";
        $skipped_count++;
    }
    
    echo "</div>";
}

// Summary
echo "<hr>";
echo "<h2>📋 Summary</h2>";
echo "<div class='success'>✅ Fixed: {$fixed_count} bookings</div>";
if($skipped_count > 0) {
    echo "<div class='warning'>⚠️ Skipped: {$skipped_count} bookings (no price source found)</div>";
}
if(count($errors) > 0) {
    echo "<div class='error'>❌ Errors: " . count($errors) . " bookings<br>";
    foreach($errors as $error) {
        echo "- {$error}<br>";
    }
    echo "</div>";
}

echo "<a href='admin-dashboard.php' class='btn'>← Back to Dashboard</a>";
echo "<a href='admin-all-bookings.php' class='btn' style='background: #10b981; margin-left: 10px;'>View All Bookings</a>";
echo "</div></body></html>";
?>
