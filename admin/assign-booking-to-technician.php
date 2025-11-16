<?php
// This file handles booking assignment and technician availability
session_start();
include('vendor/inc/config.php');

if(isset($_POST['assign_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $technician_id = intval($_POST['technician_id']);
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // 1. Check if technician is available
        $check_query = "SELECT t_is_available, t_current_booking_id FROM tms_technician WHERE t_id = ?";
        $stmt = $mysqli->prepare($check_query);
        $stmt->bind_param('i', $technician_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tech = $result->fetch_assoc();
        
        if(!$tech['t_is_available'] && $tech['t_current_booking_id']) {
            throw new Exception("Technician is already assigned to booking #" . $tech['t_current_booking_id']);
        }
        
        // 2. Assign booking to technician
        $assign_query = "UPDATE tms_service_booking 
                        SET sb_technician_id = ?, 
                            sb_status = 'Pending' 
                        WHERE sb_id = ?";
        $stmt = $mysqli->prepare($assign_query);
        $stmt->bind_param('ii', $technician_id, $booking_id);
        $stmt->execute();
        
        // 3. Mark technician as unavailable and set current booking
        $update_tech = "UPDATE tms_technician 
                       SET t_is_available = 0, 
                           t_current_booking_id = ? 
                       WHERE t_id = ?";
        $stmt = $mysqli->prepare($update_tech);
        $stmt->bind_param('ii', $booking_id, $technician_id);
        $stmt->execute();
        
        // Commit transaction
        $mysqli->commit();
        
        $_SESSION['success'] = "Booking #$booking_id assigned to technician successfully!";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
        
    } catch(Exception $e) {
        // Rollback on error
        $mysqli->rollback();
        $_SESSION['error'] = "Assignment failed: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>
