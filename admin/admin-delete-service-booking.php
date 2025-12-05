<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  include('vendor/inc/soft-delete.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  //Delete Service Booking
  if(isset($_GET['sb_id'])) {
    $sb_id = $_GET['sb_id'];
    
    // Get the booking details to free up technician if assigned
    $get_booking = "SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?";
    $get_stmt = $mysqli->prepare($get_booking);
    $get_stmt->bind_param('i', $sb_id);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    $booking = $get_result->fetch_object();
    
    // Free up the technician if one was assigned
    if($booking && $booking->sb_technician_id) {
      // Decrement technician's current bookings count
      require_once('vendor/inc/booking-limit-helper.php');
      decrementTechnicianBookings($mysqli, $booking->sb_technician_id);
      
      // Update technician status to Available if they have capacity
      $mysqli->query("UPDATE tms_technician 
                     SET t_status = CASE 
                         WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
                         ELSE 'Available'
                     END
                     WHERE t_id = {$booking->sb_technician_id}");
    }
    
    // Use soft delete function
    if(softDeleteBooking($mysqli, $sb_id, $aid, 'Deleted by admin')) {
      $_SESSION['delete_success'] = "Service booking deleted and sent to Recycle Bin!";
    } else {
      $_SESSION['delete_error'] = "Failed to delete booking.";
    }
    
    // Redirect back to all bookings page
    header("Location: admin-all-bookings.php");
    exit();
  } else {
    $_SESSION['delete_error'] = "Booking ID is missing.";
    header("Location: admin-all-bookings.php");
    exit();
  }
?>




