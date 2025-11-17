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

// Get booking ID from URL
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Verify the booking belongs to the logged-in user
$verify_query = "SELECT sb_user_id FROM tms_service_booking WHERE sb_id = ?";
$verify_stmt = $mysqli->prepare($verify_query);
$verify_stmt->bind_param('i', $booking_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();
$booking = $verify_result->fetch_object();

if (!$booking || $booking->sb_user_id != $aid) {
    header("Location: user-manage-booking.php?error=1");
    exit();
}

// Cancel the booking by updating status
$update_query = "UPDATE tms_service_booking SET sb_status = 'Cancelled' WHERE sb_id = ?";
$update_stmt = $mysqli->prepare($update_query);
$update_stmt->bind_param('i', $booking_id);

if ($update_stmt->execute()) {
    $_SESSION['cancel_success'] = true;
    header("Location: user-manage-booking.php?cancelled=1");
} else {
    $_SESSION['cancel_error'] = true;
    header("Location: user-manage-booking.php?error=1");
}
exit();
?>
