<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<h2>Database Health Check</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// Check tms_user table
echo "<h3>tms_user Table:</h3>";
$columns = ['u_id', 'u_fname', 'u_lname', 'u_phone', 'u_email', 'u_addr', 'u_area', 'u_pincode', 'u_pwd', 'u_category', 'registration_type', 'created_at', 'is_deleted', 'deleted_at'];
foreach($columns as $col) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_user LIKE '$col'");
    if($check && $check->num_rows > 0) {
        echo "<span class='success'>✓ $col exists</span><br>";
    } else {
        echo "<span class='error'>✗ $col missing</span><br>";
    }
}

// Check tms_technician table
echo "<h3>tms_technician Table:</h3>";
$columns = ['t_id', 't_name', 't_phone', 't_email', 't_id_no', 't_ez_id', 't_category', 't_pwd', 't_status', 't_pincode', 't_service_pincode', 'is_deleted', 'deleted_at'];
foreach($columns as $col) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$col'");
    if($check && $check->num_rows > 0) {
        echo "<span class='success'>✓ $col exists</span><br>";
    } else {
        echo "<span class='error'>✗ $col missing</span><br>";
    }
}

// Check tms_service_booking table
echo "<h3>tms_service_booking Table:</h3>";
$columns = ['sb_id', 'sb_user_id', 'sb_service_id', 'sb_technician_id', 'sb_booking_date', 'sb_status', 'sb_pincode', 'sb_rejection_reason', 'sb_bill_image', 'sb_service_image', 'sb_charged_price'];
foreach($columns as $col) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$col'");
    if($check && $check->num_rows > 0) {
        echo "<span class='success'>✓ $col exists</span><br>";
    } else {
        echo "<span class='error'>✗ $col missing</span><br>";
    }
}

// Check tms_deleted_items table
echo "<h3>tms_deleted_items Table:</h3>";
$check_table = $mysqli->query("SHOW TABLES LIKE 'tms_deleted_items'");
if($check_table && $check_table->num_rows > 0) {
    echo "<span class='success'>✓ Table exists</span><br>";
    $columns = ['di_id', 'di_item_type', 'di_item_id', 'di_item_data', 'di_deleted_by', 'di_deleted_date'];
    foreach($columns as $col) {
        $check = $mysqli->query("SHOW COLUMNS FROM tms_deleted_items LIKE '$col'");
        if($check && $check->num_rows > 0) {
            echo "<span class='success'>✓ $col exists</span><br>";
        } else {
            echo "<span class='error'>✗ $col missing</span><br>";
        }
    }
} else {
    echo "<span class='error'>✗ Table missing</span><br>";
}

echo "<h3>Summary:</h3>";
echo "<p>If you see any red ✗ marks, those columns/tables need to be created.</p>";
echo "<p><a href='admin-dashboard.php'>Back to Dashboard</a></p>";
?>
