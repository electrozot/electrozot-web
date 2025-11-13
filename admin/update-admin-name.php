<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Update admin name to MOH
$update_query = "UPDATE tms_admin SET a_name = 'MOH' WHERE a_id = 3";
if($mysqli->query($update_query)) {
    $_SESSION['a_name'] = 'MOH';
    echo "✅ Admin name updated successfully to 'MOH'!<br><br>";
    echo "<a href='admin-profile.php'>Go to Profile</a> | <a href='admin-dashboard.php'>Go to Dashboard</a>";
} else {
    echo "❌ Failed to update: " . $mysqli->error;
}
?>
