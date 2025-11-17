<?php
/**
 * Booking Limit Helper Functions
 * Handles technician booking capacity management
 */

/**
 * Check if technician can accept new booking
 */
function canAssignToTechnician($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("SELECT t_name, t_current_bookings, t_booking_limit FROM tms_technician WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_object();
    
    if (!$tech) {
        return ['success' => false, 'message' => 'Technician not found'];
    }
    
    $current = isset($tech->t_current_bookings) ? $tech->t_current_bookings : 0;
    $limit = isset($tech->t_booking_limit) ? $tech->t_booking_limit : 1;
    
    if ($current >= $limit) {
        return [
            'success' => false, 
            'message' => "Cannot assign. {$tech->t_name} has reached booking limit ({$current}/{$limit})"
        ];
    }
    
    return [
        'success' => true, 
        'message' => "Can assign ({$current}/{$limit})",
        'current' => $current,
        'limit' => $limit
    ];
}

/**
 * Increment technician booking count
 */
function incrementTechnicianBookings($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = t_current_bookings + 1 WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    return $stmt->execute();
}

/**
 * Decrement technician booking count
 */
function decrementTechnicianBookings($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = GREATEST(t_current_bookings - 1, 0) WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    return $stmt->execute();
}

/**
 * Get available technicians with capacity
 */
function getAvailableTechniciansWithCapacity($mysqli, $service_category = null) {
    $sql = "SELECT t_id, t_name, t_category, t_specialization, t_current_bookings, t_booking_limit,
            (t_booking_limit - t_current_bookings) as available_slots
            FROM tms_technician 
            WHERE t_status = 'Available'
            AND t_current_bookings < t_booking_limit";
    
    if ($service_category) {
        $sql .= " AND t_category = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('s', $service_category);
    } else {
        $stmt = $mysqli->prepare($sql);
    }
    
    $sql .= " ORDER BY available_slots DESC, t_current_bookings ASC";
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $technicians = [];
    while ($row = $result->fetch_object()) {
        $technicians[] = $row;
    }
    
    return $technicians;
}

/**
 * Assign booking to technician with limit check
 */
function assignBookingToTechnician($mysqli, $booking_id, $technician_id, $admin_id) {
    // Check if can assign
    $canAssign = canAssignToTechnician($mysqli, $technician_id);
    if (!$canAssign['success']) {
        return $canAssign;
    }
    
    // Get current booking details
    $stmt = $mysqli->prepare("SELECT sb_technician_id, sb_user_id FROM tms_service_booking WHERE sb_id = ?");
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_object();
    
    if (!$booking) {
        return ['success' => false, 'message' => 'Booking not found'];
    }
    
    $old_technician_id = $booking->sb_technician_id;
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // Update booking
        $stmt = $mysqli->prepare("UPDATE tms_service_booking 
                                 SET sb_technician_id = ?, sb_status = 'Approved', sb_assigned_at = NOW()
                                 WHERE sb_id = ?");
        $stmt->bind_param('ii', $technician_id, $booking_id);
        $stmt->execute();
        
        // If reassigning, decrement old technician count
        if ($old_technician_id && $old_technician_id != $technician_id) {
            decrementTechnicianBookings($mysqli, $old_technician_id);
        }
        
        // Increment new technician count
        incrementTechnicianBookings($mysqli, $technician_id);
        
        $mysqli->commit();
        
        return [
            'success' => true,
            'message' => 'Booking assigned successfully',
            'booking_id' => $booking_id,
            'technician_id' => $technician_id
        ];
        
    } catch (Exception $e) {
        $mysqli->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>
