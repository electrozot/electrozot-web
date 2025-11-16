<?php
// Quick fix for missing columns
session_start();
include('vendor/inc/config.php');

echo "<h1>🔧 Fixing Missing Columns</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#10b981;font-weight:bold;} .error{color:#ef4444;font-weight:bold;} .section{background:white;padding:20px;margin:20px 0;border-radius:8px;}</style>";

echo "<div class='section'>";
echo "<h2>Adding Missing Columns to tms_service_booking</h2>";

$columns_to_add = [
    "sb_service_image VARCHAR(255) DEFAULT NULL",
    "sb_bill_image VARCHAR(255) DEFAULT NULL",
    "sb_amount_charged DECIMAL(10,2) DEFAULT NULL",
    "sb_completed_at TIMESTAMP NULL DEFAULT NULL",
    "sb_not_done_reason TEXT DEFAULT NULL",
    "sb_not_done_at TIMESTAMP NULL DEFAULT NULL",
    "sb_rejection_reason TEXT DEFAULT NULL",
    "sb_rejected_at TIMESTAMP NULL DEFAULT NULL",
    "sb_pincode VARCHAR(10) DEFAULT NULL"
];

foreach($columns_to_add as $column_def) {
    $column_name = explode(' ', $column_def)[0];
    
    // Check if column exists
    $check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$column_name'");
    
    if($check->num_rows == 0) {
        // Column doesn't exist, add it
        try {
            $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN $column_def");
            echo "<p class='success'>✓ Added column: $column_name</p>";
        } catch(Exception $e) {
            echo "<p class='error'>✗ Failed to add $column_name: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>✓ Column already exists: $column_name</p>";
    }
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>Adding Missing Columns to tms_technician</h2>";

$tech_columns = [
    "t_phone VARCHAR(20) DEFAULT ''",
    "t_email VARCHAR(100) DEFAULT ''",
    "t_addr TEXT DEFAULT ''",
    "t_is_available TINYINT(1) DEFAULT 1",
    "t_current_booking_id INT DEFAULT NULL"
];

foreach($tech_columns as $column_def) {
    $column_name = explode(' ', $column_def)[0];
    
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$column_name'");
    
    if($check->num_rows == 0) {
        try {
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN $column_def");
            echo "<p class='success'>✓ Added column: $column_name</p>";
        } catch(Exception $e) {
            echo "<p class='error'>✗ Failed to add $column_name: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>✓ Column already exists: $column_name</p>";
    }
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>✅ Done!</h2>";
echo "<p>All required columns have been added.</p>";
echo "<p><a href='admin-rejected-bookings.php' style='padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>Go to Rejected Bookings</a></p>";
echo "<p><a href='admin-dashboard.php' style='padding:10px 20px;background:#10b981;color:white;text-decoration:none;border-radius:5px;margin-left:10px;'>Go to Dashboard</a></p>";
echo "</div>";
?>
