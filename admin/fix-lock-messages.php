<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Update all existing lock reasons from "commission" to "charges"
$update_query = "UPDATE tms_technician 
                 SET lock_reason = REPLACE(lock_reason, 'Unpaid commission', 'Unpaid charges')
                 WHERE account_locked = 1 
                 AND lock_reason LIKE '%Unpaid commission%'";

if($mysqli->query($update_query)) {
    $affected = $mysqli->affected_rows;
    echo "✓ Updated {$affected} locked accounts<br>";
    echo "✓ Changed 'Unpaid commission' to 'Unpaid charges'<br>";
    echo "<br><a href='admin-unlock-technician.php'>View Locked Accounts</a>";
} else {
    echo "❌ Error: " . $mysqli->error;
}
?>
