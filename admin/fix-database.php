<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<h2>Database Fix Script</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;}</style>";

// Fix tms_user table
echo "<h3>Fixing tms_user table...</h3>";
$queries = [
    "ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS u_area VARCHAR(100)",
    "ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS u_pincode VARCHAR(10)",
    "ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS registration_type ENUM('admin', 'self', 'guest') DEFAULT 'admin'",
    "ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS is_deleted TINYINT(1) DEFAULT 0",
    "ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL"
];
foreach($queries as $query) {
    if($mysqli->query($query)) {
        echo "<span class='success'>✓ Query executed</span><br>";
    } else {
        echo "<span class='error'>✗ Error: " . $mysqli->error . "</span><br>";
    }
}

// Fix tms_technician table
echo "<h3>Fixing tms_technician table...</h3>";
$queries = [
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(15)",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_ez_id VARCHAR(20)",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_pwd VARCHAR(200) DEFAULT ''",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_pincode VARCHAR(255)",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_service_pincode VARCHAR(20)",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS is_deleted TINYINT(1) DEFAULT 0",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL"
];
foreach($queries as $query) {
    if($mysqli->query($query)) {
        echo "<span class='success'>✓ Query executed</span><br>";
    } else {
        echo "<span class='error'>✗ Error: " . $mysqli->error . "</span><br>";
    }
}

// Fix tms_service_booking table
echo "<h3>Fixing tms_service_booking table...</h3>";
$queries = [
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_pincode VARCHAR(10)",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejection_reason TEXT",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejected_at TIMESTAMP NULL",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_image VARCHAR(200)",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_image VARCHAR(200)",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_charged_price DECIMAL(10,2)",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completion_date DATETIME"
];
foreach($queries as $query) {
    if($mysqli->query($query)) {
        echo "<span class='success'>✓ Query executed</span><br>";
    } else {
        echo "<span class='error'>✗ Error: " . $mysqli->error . "</span><br>";
    }
}

// Create tms_deleted_items table
echo "<h3>Creating tms_deleted_items table...</h3>";
$query = "CREATE TABLE IF NOT EXISTS tms_deleted_items (
    di_id INT AUTO_INCREMENT PRIMARY KEY,
    di_item_type VARCHAR(50) NOT NULL,
    di_item_id INT NOT NULL,
    di_item_data TEXT NOT NULL,
    di_deleted_by INT NOT NULL,
    di_deleted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    di_reason TEXT,
    INDEX(di_item_type),
    INDEX(di_deleted_date)
)";
if($mysqli->query($query)) {
    echo "<span class='success'>✓ Table created/verified</span><br>";
} else {
    echo "<span class='error'>✗ Error: " . $mysqli->error . "</span><br>";
}

echo "<h3>Done!</h3>";
echo "<p><a href='check-database-health.php'>Check Database Health</a> | <a href='admin-dashboard.php'>Back to Dashboard</a></p>";
?>
