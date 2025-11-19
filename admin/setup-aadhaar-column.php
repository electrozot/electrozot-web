<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Add Aadhaar column to tms_technician table
echo "<h2>Setting up Aadhaar Column...</h2>";

// Check if column exists
$check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_aadhar'");
if($check->num_rows == 0) {
    echo "<p>Adding t_aadhar column...</p>";
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_aadhar VARCHAR(12) DEFAULT NULL AFTER t_phone");
    echo "<p style='color: green;'>✓ t_aadhar column added successfully</p>";
} else {
    echo "<p style='color: blue;'>✓ t_aadhar column already exists</p>";
}

// Check if address column exists
$check_addr = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_addr'");
if($check_addr->num_rows == 0) {
    echo "<p>Adding t_addr column...</p>";
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_addr VARCHAR(500) DEFAULT NULL");
    echo "<p style='color: green;'>✓ t_addr column added successfully</p>";
} else {
    echo "<p style='color: blue;'>✓ t_addr column already exists</p>";
}

// Check if email column exists
$check_email = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_email'");
if($check_email->num_rows == 0) {
    echo "<p>Adding t_email column...</p>";
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_email VARCHAR(200) DEFAULT NULL");
    echo "<p style='color: green;'>✓ t_email column added successfully</p>";
} else {
    echo "<p style='color: blue;'>✓ t_email column already exists</p>";
}

echo "<hr>";
echo "<h3 style='color: green;'>✓ Setup Complete!</h3>";
echo "<p>The Aadhaar number field is now available and will be displayed on ID cards.</p>";
echo "<p><a href='admin-generate-id-card.php'>Go to Generate ID Card</a></p>";
echo "<p><a href='admin-dashboard.php'>Back to Dashboard</a></p>";
?>
