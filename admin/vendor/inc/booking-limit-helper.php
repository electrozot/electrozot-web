<?php
/**
 * Booking Limit Helper Functions
 * Handles technician booking capacity management
 */

/**
 * Update technician availability status based on current bookings
 */
function updateTechnicianAvailabilityStatus($mysqli, $technician_id) {
    // Get technician's current bookings and limit
    $stmt = $mysqli->prepare("SELECT t_current_bookings, t_booking_limit FROM tms_technician WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_object();
    
    if (!$tech) {
        return false;
    }
    
    $current = isset($tech->t_current_bookings) ? $tech->t_current_bookings : 0;
    $limit = isset($tech->t_booking_limit) ? $tech->t_booking_limit : 1;
    
    // Determine new status
    $new_status = ($current >= $limit) ? 'Busy' : 'Available';
    
    // Update status
    $update_stmt = $mysqli->prepare("UPDATE tms_technician SET t_status = ? WHERE t_id = ?");
    $update_stmt->bind_param('si', $new_status, $technician_id);
    return $update_stmt->execute();
}

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
 * Increment technician booking count and update availability
 */
function incrementTechnicianBookings($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = t_current_bookings + 1 WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $result = $stmt->execute();
    
    // Update technician availability status
    if ($result) {
        updateTechnicianAvailabilityStatus($mysqli, $technician_id);
    }
    
    return $result;
}

/**
 * Decrement technician booking count and update availability
 */
function decrementTechnicianBookings($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = GREATEST(t_current_bookings - 1, 0) WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $result = $stmt->execute();
    
    // Update technician availability status
    if ($result) {
        updateTechnicianAvailabilityStatus($mysqli, $technician_id);
    }
    
    return $result;
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

/**
 * Recalculate and sync technician booking counts from actual bookings
 */
function syncTechnicianBookingCounts($mysqli, $technician_id = null) {
    if ($technician_id) {
        // Sync specific technician
        $count_query = "SELECT COUNT(*) as active_count 
                       FROM tms_service_booking 
                       WHERE sb_technician_id = ? 
                       AND sb_status IN ('Pending', 'Approved', 'In Progress')";
        $stmt = $mysqli->prepare($count_query);
        $stmt->bind_param('i', $technician_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_object();
        $actual_count = $row->active_count;
        
        // Update technician's current bookings
        $update_stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = ? WHERE t_id = ?");
        $update_stmt->bind_param('ii', $actual_count, $technician_id);
        $update_stmt->execute();
        
        // Update availability status
        updateTechnicianAvailabilityStatus($mysqli, $technician_id);
        
        return ['success' => true, 'technician_id' => $technician_id, 'count' => $actual_count];
    } else {
        // Sync all technicians
        $techs_query = "SELECT t_id FROM tms_technician";
        $result = $mysqli->query($techs_query);
        
        $synced = 0;
        while ($tech = $result->fetch_object()) {
            syncTechnicianBookingCounts($mysqli, $tech->t_id);
            $synced++;
        }
        
        return ['success' => true, 'synced_count' => $synced];
    }
}

/**
 * Get technician's actual active booking count
 */
function getTechnicianActiveBookingCount($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) as active_count 
                             FROM tms_service_booking 
                             WHERE sb_technician_id = ? 
                             AND sb_status IN ('Pending', 'Approved', 'In Progress')");
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    return $row ? $row->active_count : 0;
}
