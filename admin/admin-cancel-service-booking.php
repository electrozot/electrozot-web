<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Cancel Service Booking (Admin has power to cancel at any stage)
if(isset($_GET['sb_id'])) {
    $sb_id = intval($_GET['sb_id']);
    
    // Get the booking details
    $get_booking = "SELECT sb_technician_id, sb_status FROM tms_service_booking WHERE sb_id = ?";
    $get_stmt = $mysqli->prepare($get_booking);
    $get_stmt->bind_param('i', $sb_id);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    $booking = $get_result->fetch_object();
    
    if(!$booking) {
        $_SESSION['error'] = "Booking not found.";
        header("Location: admin-all-bookings.php");
        exit();
    }
    
    // Free up the technician if one was assigned
    if($booking->sb_technician_id) {
        $free_tech = "UPDATE tms_technician SET t_status='Available' WHERE t_id=?";
        $free_stmt = $mysqli->prepare($free_tech);
        $free_stmt->bind_param('i', $booking->sb_technician_id);
        $free_stmt->execute();
        
        // Add to cancelled bookings table for record keeping
        $create_table = "CREATE TABLE IF NOT EXISTS tms_cancelled_bookings (
            cb_id INT AUTO_INCREMENT PRIMARY KEY,
            cb_booking_id INT NOT NULL,
            cb_technician_id INT NOT NULL,
            cb_cancelled_by VARCHAR(50) DEFAULT 'Admin',
            cb_reason VARCHAR(255) DEFAULT 'Booking deleted by admin',
            cb_cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(cb_booking_id),
            INDEX(cb_technician_id)
        )";
        $mysqli->query($create_table);
        
        $cancel_reason = "Booking deleted by admin";
        $insert_cancel = "INSERT INTO tms_cancelled_bookings (cb_booking_id, cb_technician_id, cb_cancelled_by, cb_reason) 
                         VALUES (?, ?, 'Admin', ?)";
        $cancel_record_stmt = $mysqli->prepare($insert_cancel);
        $cancel_record_stmt->bind_param('iis', $sb_id, $booking->sb_technician_id, $cancel_reason);
        $cancel_record_stmt->execute();
    }
    
    // DELETE the booking permanently from database
    $delete_query = "DELETE FROM tms_service_booking WHERE sb_id = ?";
    $delete_stmt = $mysqli->prepare($delete_query);
    $delete_stmt->bind_param('i', $sb_id);
    
    if($delete_stmt->execute()) {
        $_SESSION['success'] = "Booking #$sb_id deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete booking. Please try again.";
    }
    
    // Redirect back
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-all-bookings.php';
    header("Location: $redirect");
    exit();
} else {
    $_SESSION['error'] = "Booking ID is missing.";
    header("Location: admin-all-bookings.php");
    exit();
}
?>
