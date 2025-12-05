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

// Verify the booking belongs to the logged-in user and check technician assignment
$verify_query = "SELECT sb_user_id, sb_technician_id, sb_status, sb_was_on_hold FROM tms_service_booking WHERE sb_id = ?";
$verify_stmt = $mysqli->prepare($verify_query);
$verify_stmt->bind_param('i', $booking_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();
$booking = $verify_result->fetch_object();

if (!$booking || $booking->sb_user_id != $aid) {
    header("Location: user-manage-booking.php?error=1");
    exit();
}

// Check if booking was on hold - if yes, customer cannot cancel it
if ($booking->sb_was_on_hold == 1) {
    $_SESSION['cancel_error'] = "This booking cannot be cancelled. It was previously on hold and only admin can cancel it. Please contact support.";
    header("Location: user-manage-booking.php?error=hold_protected");
    exit();
}

// Check if technician is assigned - if yes, user cannot cancel
if (!empty($booking->sb_technician_id)) {
    $_SESSION['cancel_error'] = "Cannot cancel booking after technician is assigned. Please contact admin for assistance.";
    header("Location: user-manage-booking.php?error=technician_assigned");
    exit();
}

// Check if booking is already completed or cancelled
if (in_array($booking->sb_status, ['Completed', 'Cancelled'])) {
    $_SESSION['cancel_error'] = "Cannot cancel a " . strtolower($booking->sb_status) . " booking.";
    header("Location: user-manage-booking.php?error=invalid_status");
    exit();
}

// Cancel the booking by updating status
$update_query = "UPDATE tms_service_booking SET sb_status = 'Cancelled', sb_cancelled_at = NOW(), sb_cancelled_by = ? WHERE sb_id = ?";
$update_stmt = $mysqli->prepare($update_query);
$update_stmt->bind_param('ii', $aid, $booking_id);

if ($update_stmt->execute()) {
    $_SESSION['cancel_success'] = true;
    header("Location: user-manage-booking.php?cancelled=1");
} else {
    $_SESSION['cancel_error'] = "Failed to cancel booking. Please try again.";
    header("Location: user-manage-booking.php?error=1");
}
exit();
?>
