<?php
/**
 * Improved Technician Matcher with Detailed Service Skills
 * Matches technicians based on:
 * 1. Exact detailed service skill match
 * 2. Booking time slot availability
 * 3. Current booking capacity
 */

/**
 * Get available technicians for a booking with detailed skill matching
 * 
 * @param mysqli $mysqli Database connection
 * @param int $service_id Service ID
 * @param string $booking_date Booking date (Y-m-d format)
 * @param string $booking_time Booking time (H:i:s format)
 * @param int $exclude_booking_id Optional: Exclude current booking for reassignment
 * @return array List of available technicians with skill match details
 */
function getAvailableTechniciansWithSkillAndSlot($mysqli, $service_id, $booking_date, $booking_time, $exclude_booking_id = null) {
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
    
    // Step 2: Find technicians with matching detailed service skills
    // Using FIND_IN_SET to search in comma-separated t_skills column
    $skill_match_query = "SELECT 
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
                            (t.t_booking_limit - t.t_current_bookings) as available_slots
                         FROM tms_technician t
                         WHERE FIND_IN_SET(?, t.t_skills) > 0
                         AND t.t_current_bookings < t.t_booking_limit
                         ORDER BY available_slots DESC, t.t_experience DESC, t.t_name ASC";
    
    $stmt = $mysqli->prepare($skill_match_query);
    $stmt->bind_param('s', $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $available_technicians = [];
    
    while ($tech = $result->fetch_assoc()) {
        // Step 3: Check if technician is available for the specific time slot
        $is_slot_available = checkTechnicianTimeSlotAvailability(
            $mysqli, 
            $tech['t_id'], 
            $booking_date, 
            $booking_time,
            $exclude_booking_id
        );
        
        if ($is_slot_available['available']) {
            $tech['match_type'] = 'exact_skill';
            $tech['slot_available'] = true;
            $tech['slot_message'] = $is_slot_available['message'];
            $tech['conflicting_bookings'] = $is_slot_available['conflicting_count'];
            $available_technicians[] = $tech;
        } else {
            // Include but mark as unavailable for this slot
            $tech['match_type'] = 'exact_skill';
            $tech['slot_available'] = false;
            $tech['slot_message'] = $is_slot_available['message'];
            $tech['conflicting_bookings'] = $is_slot_available['conflicting_count'];
            // Still add to list but with warning
            $available_technicians[] = $tech;
        }
    }
    
    // If no exact skill matches found, try category match
    if (empty($available_technicians)) {
        $category_query = "SELECT 
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
                             (t.t_booking_limit - t.t_current_bookings) as available_slots
                          FROM tms_technician t
                          WHERE t.t_category = ?
                          AND t.t_current_bookings < t.t_booking_limit
                          ORDER BY available_slots DESC, t.t_experience DESC";
        
        $stmt = $mysqli->prepare($category_query);
        $stmt->bind_param('s', $service_category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($tech = $result->fetch_assoc()) {
            $is_slot_available = checkTechnicianTimeSlotAvailability(
                $mysqli, 
                $tech['t_id'], 
                $booking_date, 
                $booking_time,
                $exclude_booking_id
            );
            
            $tech['match_type'] = 'category_only';
            $tech['slot_available'] = $is_slot_available['available'];
            $tech['slot_message'] = $is_slot_available['message'];
            $tech['conflicting_bookings'] = $is_slot_available['conflicting_count'];
            $available_technicians[] = $tech;
        }
    }
    
    return $available_technicians;
}

/**
 * Check if technician is available for a specific time slot
 * Checks for overlapping bookings within ±2 hours window
 * 
 * @param mysqli $mysqli Database connection
 * @param int $technician_id Technician ID
 * @param string $booking_date Booking date (Y-m-d)
 * @param string $booking_time Booking time (H:i:s)
 * @param int $exclude_booking_id Optional: Exclude this booking ID
 * @return array ['available' => bool, 'message' => string, 'conflicting_count' => int]
 */
function checkTechnicianTimeSlotAvailability($mysqli, $technician_id, $booking_date, $booking_time, $exclude_booking_id = null) {
    // Check for bookings on the same date within ±2 hours window
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
            'message' => 'Busy at ' . implode(', ', $conflict_times) . ' on ' . date('M d', strtotime($booking_date)),
            'conflicting_count' => $conflict_count,
            'conflicting_bookings' => $conflicting_bookings
        ];
    }
    
    return [
        'available' => true,
        'message' => 'Available for this time slot',
        'conflicting_count' => 0,
        'conflicting_bookings' => []
    ];
}

/**
 * Get all detailed service skills for a service
 * 
 * @param mysqli $mysqli Database connection
 * @param int $service_id Service ID
 * @return array Service details with skills
 */
function getServiceWithSkills($mysqli, $service_id) {
    $query = "SELECT s_id, s_name, s_category, s_subcategory, s_description 
              FROM tms_service 
              WHERE s_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $service_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Format technicians as HTML options for dropdown with skill and slot info
 * 
 * @param array $technicians List of technicians
 * @param int $selected_id Currently selected technician ID
 * @return string HTML options
 */
function formatTechniciansWithSkillAndSlot($technicians, $selected_id = 0) {
    if (empty($technicians)) {
        return '<option value="">❌ No technicians available with required skills</option>';
    }
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Group by availability and match type
    $available_exact = array_filter($technicians, function($t) { 
        return $t['slot_available'] && $t['match_type'] === 'exact_skill'; 
    });
    $busy_exact = array_filter($technicians, function($t) { 
        return !$t['slot_available'] && $t['match_type'] === 'exact_skill'; 
    });
    $available_category = array_filter($technicians, function($t) { 
        return $t['slot_available'] && $t['match_type'] === 'category_only'; 
    });
    $busy_category = array_filter($technicians, function($t) { 
        return !$t['slot_available'] && $t['match_type'] === 'category_only'; 
    });
    
    // Available with exact skill match (BEST)
    if (!empty($available_exact)) {
        $options .= '<optgroup label="✅ Available - Has Required Skill (' . count($available_exact) . ')">';
        foreach ($available_exact as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s, %d slot%s free) - %s</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                $exp,
                $slots,
                $slots != 1 ? 's' : '',
                $tech['slot_message']
            );
        }
        $options .= '</optgroup>';
    }
    
    // Available with category match only
    if (!empty($available_category)) {
        $options .= '<optgroup label="⚠️ Available - Category Match Only (' . count($available_category) . ')">';
        foreach ($available_category as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s, %d slot%s free) - %s</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                $exp,
                $slots,
                $slots != 1 ? 's' : '',
                $tech['slot_message']
            );
        }
        $options .= '</optgroup>';
    }
    
    // Busy but has skill (show as disabled)
    if (!empty($busy_exact)) {
        $options .= '<optgroup label="🔴 Busy at This Time - Has Skill (' . count($busy_exact) . ')">';
        foreach ($busy_exact as $tech) {
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            
            $options .= sprintf(
                '<option value="%d" disabled>%s (%s) - %s</option>',
                $tech['t_id'],
                htmlspecialchars($tech['t_name']),
                $exp,
                $tech['slot_message']
            );
        }
        $options .= '</optgroup>';
    }
    
    // Busy category match
    if (!empty($busy_category)) {
        $options .= '<optgroup label="🔴 Busy at This Time - Category Match (' . count($busy_category) . ')">';
        foreach ($busy_category as $tech) {
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            
            $options .= sprintf(
                '<option value="%d" disabled>%s (%s) - %s</option>',
                $tech['t_id'],
                htmlspecialchars($tech['t_name']),
                $exp,
                $tech['slot_message']
            );
        }
        $options .= '</optgroup>';
    }
    
    return $options;
}
?>
