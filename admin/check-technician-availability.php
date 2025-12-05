<?php
/**
 * Technician Availability Checker
 * This file provides functions to check technician availability and capacity
 */

if (!function_exists('checkTechnicianAvailability')) {
    /**
     * Check if a technician is available for new bookings
     * @param mysqli $mysqli Database connection
     * @param int $technician_id Technician ID to check
     * @return bool True if available, false otherwise
     */
    function checkTechnicianAvailability($mysqli, $technician_id) {
        $query = "SELECT t_booking_limit, t_current_bookings, t_status, t_is_available 
                  FROM tms_technician 
                  WHERE t_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $technician_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($tech = $result->fetch_object()) {
            // Check if technician is available and has capacity
            if ($tech->t_status == 'Available' && 
                $tech->t_is_available == 1 && 
                $tech->t_current_bookings < $tech->t_booking_limit) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('getAvailableTechnicians')) {
    /**
     * Get list of available technicians for a specific category
     * @param mysqli $mysqli Database connection
     * @param string $category Service category
     * @return array Array of available technicians
     */
    function getAvailableTechnicians($mysqli, $category = null) {
        $query = "SELECT t_id, t_name, t_category, t_booking_limit, t_current_bookings, 
                         (t_booking_limit - t_current_bookings) as available_slots
                  FROM tms_technician 
                  WHERE t_status = 'Available' 
                  AND t_is_available = 1 
                  AND t_current_bookings < t_booking_limit";
        
        if ($category) {
            $query .= " AND t_category = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('s', $category);
        } else {
            $stmt = $mysqli->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $technicians = [];
        while ($tech = $result->fetch_object()) {
            $technicians[] = $tech;
        }
        
        return $technicians;
    }
}

if (!function_exists('getTechnicianCurrentLoad')) {
    /**
     * Get current booking load for a technician
     * @param mysqli $mysqli Database connection
     * @param int $technician_id Technician ID
     * @return array Array with booking statistics
     */
    function getTechnicianCurrentLoad($mysqli, $technician_id) {
        $query = "SELECT 
                    t.t_booking_limit,
                    t.t_current_bookings,
                    (t.t_booking_limit - t.t_current_bookings) as available_slots,
                    ROUND((t.t_current_bookings / t.t_booking_limit) * 100, 2) as load_percentage
                  FROM tms_technician t
                  WHERE t.t_id = ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $technician_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($load = $result->fetch_assoc()) {
            return $load;
        }
        
        return [
            't_booking_limit' => 0,
            't_current_bookings' => 0,
            'available_slots' => 0,
            'load_percentage' => 0
        ];
    }
}

if (!function_exists('updateTechnicianBookingCount')) {
    /**
     * Manually update technician booking count (use with caution)
     * @param mysqli $mysqli Database connection
     * @param int $technician_id Technician ID
     * @return bool Success status
     */
    function updateTechnicianBookingCount($mysqli, $technician_id) {
        // Count actual active bookings
        $count_query = "SELECT COUNT(*) as active_count 
                       FROM tms_service_booking 
                       WHERE sb_technician_id = ? 
                       AND sb_status IN ('Pending', 'Approved', 'In Progress')";
        
        $stmt = $mysqli->prepare($count_query);
        $stmt->bind_param('i', $technician_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $active_count = $row['active_count'];
        
        // Update technician record
        $update_query = "UPDATE tms_technician 
                        SET t_current_bookings = ? 
                        WHERE t_id = ?";
        
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param('ii', $active_count, $technician_id);
        
        return $stmt->execute();
    }
}
?>
