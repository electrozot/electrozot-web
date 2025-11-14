<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Add f_photo column to tms_feedback table if it doesn't exist
$query = "ALTER TABLE tms_feedback ADD COLUMN IF NOT EXISTS f_photo VARCHAR(255) DEFAULT NULL";
if($mysqli->query($query)) {
    echo "Success: Database updated successfully. Photo column added to feedback table.";
} else {
    echo "Note: " . $mysqli->error;
}

// Create feedbacks directory if it doesn't exist
$dir = "../vendor/img/feedbacks/";
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
    echo "<br>Success: Feedbacks directory created.";
} else {
    echo "<br>Info: Feedbacks directory already exists.";
}

echo "<br><br><a href='admin-manage-feedback.php'>Go to Manage Feedbacks</a>";
?>
