<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  //Delete Service Booking
  if(isset($_GET['sb_id'])) {
    $sb_id = $_GET['sb_id'];
    
    // Snapshot booking before deletion into recycle bin
    $snap_sql = "SELECT * FROM tms_service_booking WHERE sb_id = ?";
    $snap_stmt = $mysqli->prepare($snap_sql);
    if ($snap_stmt) {
      $snap_stmt->bind_param('i', $sb_id);
      $snap_stmt->execute();
      $snap_res = $snap_stmt->get_result();
      $snap = $snap_res->fetch_assoc();
      if ($snap) {
        $payload = json_encode($snap);
        $rb_ins = $mysqli->prepare("INSERT INTO tms_recycle_bin (rb_type, rb_table, rb_object_id, rb_payload, rb_deleted_by) VALUES ('service_booking','tms_service_booking', ?, ?, ?)");
        if ($rb_ins) {
          $rb_ins->bind_param('isi', $sb_id, $payload, $aid);
          $rb_ins->execute();
        }
      }
    }
    
    // Get the booking details to free up technician if assigned
    $get_booking = "SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?";
    $get_stmt = $mysqli->prepare($get_booking);
    $get_stmt->bind_param('i', $sb_id);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    $booking = $get_result->fetch_object();
    
    // Free up the technician if one was assigned
    if($booking && $booking->sb_technician_id) {
      $free_tech = "UPDATE tms_technician SET t_status='Available' WHERE t_id=?";
      $free_stmt = $mysqli->prepare($free_tech);
      $free_stmt->bind_param('i', $booking->sb_technician_id);
      $free_stmt->execute();
    }
    
    // Delete the booking
    $query = "DELETE FROM tms_service_booking WHERE sb_id=?";
    $stmt = $mysqli->prepare($query);
    
    if(!$stmt) {
      $_SESSION['delete_error'] = "Database error: " . $mysqli->error;
    } else {
      $stmt->bind_param('i', $sb_id);
      $result = $stmt->execute();
      
      if($result && $stmt->affected_rows > 0) {
        $_SESSION['delete_success'] = "Service booking deleted and sent to Recycle Bin!";
      } else {
        $_SESSION['delete_error'] = "Failed to delete booking. " . ($stmt->error ? $stmt->error : "Booking not found.");
      }
    }
    
    // Redirect back to manage bookings page
    header("Location: admin-manage-service-booking.php");
    exit();
  } else {
    $_SESSION['delete_error'] = "Booking ID is missing.";
    header("Location: admin-manage-service-booking.php");
    exit();
  }
?>




