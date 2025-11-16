<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user ID from URL
$u_id = isset($_GET['u_id']) ? $_GET['u_id'] : 0;

// Verify it's the logged-in user
if ($u_id != $aid) {
    header("Location: user-manage-booking.php");
    exit();
}

// Cancel the booking by updating status
$update_query = "UPDATE tms_user SET t_booking_status = 'Cancelled' WHERE u_id = ?";
$update_stmt = $mysqli->prepare($update_query);
$update_stmt->bind_param('i', $aid);

if ($update_stmt->execute()) {
    $_SESSION['cancel_success'] = true;
    header("Location: user-manage-booking.php?cancelled=1");
} else {
    $_SESSION['cancel_error'] = true;
    header("Location: user-manage-booking.php?error=1");
}
exit();
?>
