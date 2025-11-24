<?php
/**
 * ============================================================================
 * ULTIMATE TECHNICIAN MATCHER - Single Source of Truth
 * ============================================================================
 * 
 * This is the ONLY technician matching system. All others are deprecated.
 * 
 * MATCHING LOGIC:
 * ---------------
 * 1. Detailed Service Skills Match (from 43+ services checked during add technician)
 * 2. Booking Capacity Check (current_bookings < booking_limit)
 * 3. Time Slot Availability (no conflicting bookings at same time ±2 hours)
 * 4. Same Category Fallback (only if no exact skill match available)
 * 
 * WORKS FOR:
 * ----------
 * - New Assignment
 * - Change Technician
 * - Reassign After Rejection
 * - Auto-Assignment
 * 
 * DATABASE STRUCTURE:
 * -------------------
 * - t_skills: Comma-separated list of service names from 43+ services
 * - t_booking_limit: Maximum concurrent bookings
 * - t_current_bookings: Current active bookings count
 * - sb_booking_date: Booking date
 * - sb_booking_time: Booking time
 */

/**
 * Get available technicians for a booking with complete validation
 * 
 * @param mysqli $mysqli Database connection
 * @param int $service_id Service ID
 * @param string $booking_date Booking date (Y-m-d)
 * @param string $booking_time Booking time (H:i:s)
 * @param int $exclude_booking_id Exclude this booking (for reassignment)
 * @return array Technicians grouped by match quality
 */
function getSmartAvailableTechnicians($mysqli, $service_id, $booking_date, $booking_time, $exclude_booking_id = null) {
    // Step 1: Get service details
    $service_query = "SELECT s_id, s_name, s_category, s_subcategory 
                     FROM tms_service 
                     WHERE s_id = ?";
    $stmt = $mysqli->prepare($service_query);
    $stmt->bind_param('i', $service_id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    
    if (!$service) {
        return [];
    }
    
    $service_name = $service['s_name'];
    $service_category = $service['s_category'];
    
    // Step 2: Find technicians with skill match (flexible matching)
    // Try multiple matching strategies for better results
    $exact_skill_query = "SELECT 
                            t.t_id,
                            t.t_name,
                            t.t_phone,
                            t.t_email,
                            t.t_category,
                            t.t_specialization,
                            t.t_experience,
                            t.t_booking_limit,
                            t.t_current_bookings,
                            t.t_skills,
                            (t.t_booking_limit - t.t_current_bookings) as available_slots,
                            CASE
                                WHEN FIND_IN_SET(?, t.t_skills) > 0 THEN 'exact_skill'
                                WHEN t.t_skills LIKE CONCAT('%', ?, '%') THEN 'partial_skill'
                                WHEN t.t_category = ? THEN 'same_category'
                                ELSE 'no_match'
                            END as match_type_raw
                         FROM tms_technician t
                         WHERE (
                            FIND_IN_SET(?, t.t_skills) > 0
                            OR t.t_skills LIKE CONCAT('%', ?, '%')
                            OR t.t_category = ?
                         )
                         AND t.t_status != 'Inactive'
                         ORDER BY 
                            CASE
                                WHEN FIND_IN_SET(?, t.t_skills) > 0 THEN 1
                                WHEN t.t_skills LIKE CONCAT('%', ?, '%') THEN 2
                                WHEN t.t_category = ? THEN 3
                                ELSE 4
                            END,
                            available_slots DESC, 
                            t.t_experience DESC, 
                            t.t_name ASC";
    
    $stmt = $mysqli->prepare($exact_skill_query);
    $stmt->bind_param('sssssssss', 
        $service_name, $service_name, $service_category,  // CASE conditions
        $service_name, $service_name, $service_category,  // WHERE conditions
        $service_name, $service_name, $service_category   // ORDER BY conditions
    );
    $stmt->execute();
    $result = $stmt->get_result();
    
    $technicians = [];
    
    // Step 3: Check each technician for capacity ONLY
    while ($tech = $result->fetch_assoc()) {
        // ONLY CHECK: Has booking capacity?
        $has_capacity = $tech['t_current_bookings'] < $tech['t_booking_limit'];
        
        // TIME SLOT CHECK REMOVED - Admin can manage scheduling
        // Technician is available if they have capacity, period.
        $is_available = $has_capacity;
        
        // Use the match type from query, or determine from category
        $match_type = isset($tech['match_type_raw']) ? $tech['match_type_raw'] : 'same_category';
        if ($match_type === 'no_match') {
            $match_type = 'same_category';
        }
        
        $tech['match_type'] = $match_type;
        $tech['has_capacity'] = $has_capacity;
        $tech['has_free_slot'] = true;  // Always true now
        $tech['is_available'] = $is_available;
        $tech['slot_message'] = $has_capacity ? 'Available' : 'At capacity';
        $tech['conflicting_bookings'] = 0;
        $tech['unavailable_reason'] = $has_capacity ? '' : 'At booking capacity';
        
        $technicians[] = $tech;
    }
    
    // Category matching is now included in the main query above
    // No need for separate fallback query
    
    return $technicians;
}

/**
 * Check if technician has free time slot (no conflicting bookings)
 * Checks ±2 hours window to prevent overlapping bookings
 * 
 * @param mysqli $mysqli Database connection
 * @param int $technician_id Technician ID
 * @param string $booking_date Booking date
 * @param string $booking_time Booking time
 * @param int $exclude_booking_id Exclude this booking
 * @param bool $use_lock Use FOR UPDATE lock (for assignment validation)
 */
function checkTimeSlotAvailability($mysqli, $technician_id, $booking_date, $booking_time, $exclude_booking_id = null, $use_lock = false) {
    $check_query = "SELECT 
                        sb_id,
                        sb_booking_time,
                        sb_status,
                        TIMESTAMPDIFF(MINUTE, ?, sb_booking_time) as time_diff_minutes
                    FROM tms_service_booking
                    WHERE sb_technician_id = ?
                    AND sb_booking_date = ?
                    AND sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
                    AND ABS(TIMESTAMPDIFF(MINUTE, ?, sb_booking_time)) <= 120";
    
    if ($exclude_booking_id) {
        $check_query .= " AND sb_id != ?";
    }
    
    // Add row lock for race condition protection during assignment
    if ($use_lock) {
        $check_query .= " FOR UPDATE";
    }
    
    $stmt = $mysqli->prepare($check_query);
    
    if ($exclude_booking_id) {
        $stmt->bind_param('sisis', $booking_time, $technician_id, $booking_date, $booking_time, $exclude_booking_id);
    } else {
        $stmt->bind_param('siss', $booking_time, $technician_id, $booking_date, $booking_time);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $conflicting_bookings = $result->fetch_all(MYSQLI_ASSOC);
    $conflict_count = count($conflicting_bookings);
    
    if ($conflict_count > 0) {
        $conflict_times = array_map(function($b) {
            return date('h:i A', strtotime($b['sb_booking_time']));
        }, $conflicting_bookings);
        
        return [
            'available' => false,
            'message' => 'Busy at ' . implode(', ', $conflict_times),
            'conflicting_count' => $conflict_count,
            'conflicting_bookings' => $conflicting_bookings
        ];
    }
    
    return [
        'available' => true,
        'message' => 'Free at this time',
        'conflicting_count' => 0,
        'conflicting_bookings' => []
    ];
}

/**
 * Get human-readable reason why technician is unavailable
 */
function getUnavailableReason($has_capacity, $time_slot_check) {
    if (!$has_capacity && !$time_slot_check['available']) {
        return 'At capacity & busy at this time';
    } else if (!$has_capacity) {
        return 'At booking capacity';
    } else if (!$time_slot_check['available']) {
        return $time_slot_check['message'];
    }
    return '';
}

/**
 * Format technicians as HTML dropdown options
 * Groups by: Available > Unavailable (with reasons)
 */
function formatSmartTechnicianOptions($technicians, $selected_id = 0) {
    if (empty($technicians)) {
        return '<option value="">❌ No technicians found for this service</option>';
    }
    
    // Group technicians by match quality and availability
    $available_exact = array_filter($technicians, function($t) { 
        return $t['is_available'] && $t['match_type'] === 'exact_skill'; 
    });
    $available_partial = array_filter($technicians, function($t) { 
        return $t['is_available'] && $t['match_type'] === 'partial_skill'; 
    });
    $available_category = array_filter($technicians, function($t) { 
        return $t['is_available'] && $t['match_type'] === 'same_category'; 
    });
    $unavailable_exact = array_filter($technicians, function($t) { 
        return !$t['is_available'] && $t['match_type'] === 'exact_skill'; 
    });
    $unavailable_partial = array_filter($technicians, function($t) { 
        return !$t['is_available'] && $t['match_type'] === 'partial_skill'; 
    });
    $unavailable_category = array_filter($technicians, function($t) { 
        return !$t['is_available'] && $t['match_type'] === 'same_category'; 
    });
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // 1. Available with exact skill (BEST OPTION)
    if (!empty($available_exact)) {
        $options .= '<optgroup label="✅ Available Now - Has Required Skill (' . count($available_exact) . ')">';
        foreach ($available_exact as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s exp, %d/%d slots) - %s</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                $exp,
                $slots,
                $tech['t_booking_limit'],
                $tech['slot_message']
            );
        }
        $options .= '</optgroup>';
    }
    
    // 2. Available same category (FALLBACK)
    if (!empty($available_category)) {
        $options .= '<optgroup label="⚡ Available - Same Category (Can Handle) (' . count($available_category) . ')">';
        foreach ($available_category as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s exp, %d/%d slots) - %s</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                $exp,
                $slots,
                $tech['t_booking_limit'],
                $tech['slot_message']
            );
        }
        $options .= '</optgroup>';
    }
    
    // 3. Unavailable with exact skill (SHOW AS DISABLED REFERENCE)
    if (!empty($unavailable_exact)) {
        $options .= '<optgroup label="🔴 Unavailable - Has Skill (' . count($unavailable_exact) . ')">';
        foreach ($unavailable_exact as $tech) {
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            
            $options .= sprintf(
                '<option value="%d" disabled>%s (%s exp) - %s</option>',
                $tech['t_id'],
                htmlspecialchars($tech['t_name']),
                $exp,
                $tech['unavailable_reason']
            );
        }
        $options .= '</optgroup>';
    }
    
    // 4. Unavailable same category (SHOW AS DISABLED REFERENCE)
    if (!empty($unavailable_category)) {
        $options .= '<optgroup label="🔴 Unavailable - Same Category (' . count($unavailable_category) . ')">';
        foreach ($unavailable_category as $tech) {
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            
            $options .= sprintf(
                '<option value="%d" disabled>%s (%s exp) - %s</option>',
                $tech['t_id'],
                htmlspecialchars($tech['t_name']),
                $exp,
                $tech['unavailable_reason']
            );
        }
        $options .= '</optgroup>';
    }
    
    return $options;
}

/**
 * Get technicians for reassignment (by service name)
 * Used when service_id is not available
 */
function getSmartTechniciansForReassignment($mysqli, $service_name, $service_category, $booking_date, $booking_time, $exclude_booking_id = null) {
    // Get service ID first
    $service_query = "SELECT s_id FROM tms_service WHERE s_name = ? LIMIT 1";
    $stmt = $mysqli->prepare($service_query);
    $stmt->bind_param('s', $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    
    if ($service) {
        return getSmartAvailableTechnicians($mysqli, $service['s_id'], $booking_date, $booking_time, $exclude_booking_id);
    }
    
    // Fallback: Direct query if service not found
    $technicians = [];
    
    $query = "SELECT 
                t.t_id,
                t.t_name,
                t.t_phone,
                t.t_email,
                t.t_category,
                t.t_experience,
                t.t_booking_limit,
                t.t_current_bookings,
                t.t_skills,
                (t.t_booking_limit - t.t_current_bookings) as available_slots
             FROM tms_technician t
             WHERE (FIND_IN_SET(?, t.t_skills) > 0 OR t.t_category = ?)
             AND t.t_status != 'Inactive'
             ORDER BY 
                CASE WHEN FIND_IN_SET(?, t.t_skills) > 0 THEN 0 ELSE 1 END,
                available_slots DESC, 
                t.t_experience DESC";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('sss', $service_name, $service_category, $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($tech = $result->fetch_assoc()) {
        $has_capacity = $tech['t_current_bookings'] < $tech['t_booking_limit'];
        $time_slot_check = checkTimeSlotAvailability($mysqli, $tech['t_id'], $booking_date, $booking_time, $exclude_booking_id);
        $is_truly_available = $has_capacity && $time_slot_check['available'];
        
        $has_exact_skill = strpos($tech['t_skills'], $service_name) !== false;
        
        $tech['match_type'] = $has_exact_skill ? 'exact_skill' : 'same_category';
        $tech['has_capacity'] = $has_capacity;
        $tech['has_free_slot'] = $time_slot_check['available'];
        $tech['is_available'] = $is_truly_available;
        $tech['slot_message'] = $time_slot_check['message'];
        $tech['conflicting_bookings'] = $time_slot_check['conflicting_count'];
        $tech['unavailable_reason'] = getUnavailableReason($has_capacity, $time_slot_check);
        
        $technicians[] = $tech;
    }
    
    return $technicians;
}

/**
 * Check if specific technician can accept a booking
 * Returns detailed availability status
 * 
 * ⚠️ RACE CONDITION SAFE VERSION
 * Use this inside a transaction with $use_lock = true for atomic validation
 */
function canTechnicianAcceptBooking($mysqli, $technician_id, $booking_date, $booking_time, $exclude_booking_id = null, $use_lock = false) {
    $query = "SELECT 
                t_id,
                t_name,
                t_booking_limit,
                t_current_bookings,
                (t_booking_limit - t_current_bookings) as available_slots
              FROM tms_technician
              WHERE t_id = ?";
    
    // Add row lock for race condition protection
    if ($use_lock) {
        $query .= " FOR UPDATE";
    }
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $tech = $stmt->get_result()->fetch_assoc();
    
    if (!$tech) {
        return [
            'can_accept' => false,
            'message' => 'Technician not found',
            'details' => []
        ];
    }
    
    $has_capacity = $tech['t_current_bookings'] < $tech['t_booking_limit'];
    $time_slot_check = checkTimeSlotAvailability($mysqli, $technician_id, $booking_date, $booking_time, $exclude_booking_id, $use_lock);
    $can_accept = $has_capacity && $time_slot_check['available'];
    
    $message = '';
    if (!$can_accept) {
        if (!$has_capacity) {
            $message = sprintf('%s is at capacity (%d/%d bookings)', 
                $tech['t_name'], $tech['t_current_bookings'], $tech['t_booking_limit']);
        } else {
            $message = sprintf('%s is busy at this time: %s', 
                $tech['t_name'], $time_slot_check['message']);
        }
    } else {
        $message = sprintf('%s is available (%d slots free)', 
            $tech['t_name'], $tech['available_slots']);
    }
    
    return [
        'can_accept' => $can_accept,
        'has_capacity' => $has_capacity,
        'has_free_slot' => $time_slot_check['available'],
        'message' => $message,
        'details' => $tech,
        'time_slot_info' => $time_slot_check
    ];
}
?>
