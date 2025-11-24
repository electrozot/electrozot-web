<?php
/**
 * Technician Skill-Based Matcher
 * Matches technicians based on:
 * 1. Service skills (from 43+ services)
 * 2. Concurrent booking limit
 * 3. Current availability
 */

/**
 * Get available technicians for a specific service
 * 
 * @param mysqli $mysqli Database connection
 * @param int $service_id Service ID from tms_service table
 * @param int $exclude_booking_id Optional: Exclude current booking (for reassignment)
 * @return array List of available technicians with skill match
 */
function getAvailableTechniciansForService($mysqli, $service_id, $exclude_booking_id = null) {
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
    
    // Step 2: Find technicians with matching skills
    // Priority 1: Exact service name match
    $exact_match_query = "SELECT DISTINCT 
                            t.t_id,
                            t.t_name,
                            t.t_phone,
                            t.t_email,
                            t.t_category,
                            t.t_specialization,
                            t.t_experience,
                            t.t_booking_limit,
                            t.t_current_bookings,
                            (t.t_booking_limit - t.t_current_bookings) as available_slots,
                            ts.skill_name,
                            'exact' as match_type
                         FROM tms_technician t
                         INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
                         WHERE ts.skill_name = ?
                         AND t.t_current_bookings < t.t_booking_limit
                         ORDER BY available_slots DESC, t.t_experience DESC, t.t_name ASC";
    
    $stmt = $mysqli->prepare($exact_match_query);
    $stmt->bind_param('s', $service_name);
    $stmt->execute();
    $exact_matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // If exact matches found, return them
    if (count($exact_matches) > 0) {
        return $exact_matches;
    }
    
    // Priority 2: Partial service name match
    $partial_match_query = "SELECT DISTINCT 
                              t.t_id,
                              t.t_name,
                              t.t_phone,
                              t.t_email,
                              t.t_category,
                              t.t_specialization,
                              t.t_experience,
                              t.t_booking_limit,
                              t.t_current_bookings,
                              (t.t_booking_limit - t.t_current_bookings) as available_slots,
                              ts.skill_name,
                              'partial' as match_type
                           FROM tms_technician t
                           INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
                           WHERE ts.skill_name LIKE ?
                           AND t.t_current_bookings < t.t_booking_limit
                           ORDER BY available_slots DESC, t.t_experience DESC, t.t_name ASC";
    
    $stmt = $mysqli->prepare($partial_match_query);
    $search_term = '%' . $service_name . '%';
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $partial_matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // If partial matches found, return them
    if (count($partial_matches) > 0) {
        return $partial_matches;
    }
    
    // Priority 3: Category match (fallback)
    $category_match_query = "SELECT DISTINCT 
                               t.t_id,
                               t.t_name,
                               t.t_phone,
                               t.t_email,
                               t.t_category,
                               t.t_specialization,
                               t.t_experience,
                               t.t_booking_limit,
                               t.t_current_bookings,
                               (t.t_booking_limit - t.t_current_bookings) as available_slots,
                               NULL as skill_name,
                               'category' as match_type
                            FROM tms_technician t
                            WHERE t.t_category = ?
                            AND t.t_current_bookings < t.t_booking_limit
                            ORDER BY available_slots DESC, t.t_experience DESC, t.t_name ASC";
    
    $stmt = $mysqli->prepare($category_match_query);
    $stmt->bind_param('s', $service_category);
    $stmt->execute();
    $category_matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return $category_matches;
}

/**
 * Get available technicians by service name (for reassignment/change)
 * 
 * @param mysqli $mysqli Database connection
 * @param string $service_name Service name
 * @param string $service_category Service category
 * @return array List of available technicians
 */
function getAvailableTechniciansByServiceName($mysqli, $service_name, $service_category = '') {
    $technicians = [];
    
    // Priority 1: Exact skill match
    $exact_query = "SELECT DISTINCT 
                      t.t_id,
                      t.t_name,
                      t.t_phone,
                      t.t_category,
                      t.t_experience,
                      t.t_booking_limit,
                      t.t_current_bookings,
                      (t.t_booking_limit - t.t_current_bookings) as available_slots,
                      ts.skill_name,
                      'exact' as match_type
                   FROM tms_technician t
                   INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
                   WHERE ts.skill_name = ?
                   AND t.t_current_bookings < t.t_booking_limit
                   ORDER BY available_slots DESC, t.t_experience DESC";
    
    $stmt = $mysqli->prepare($exact_query);
    $stmt->bind_param('s', $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($tech = $result->fetch_assoc()) {
        $technicians[] = $tech;
    }
    
    // If no exact matches and category provided, try category match
    if (empty($technicians) && !empty($service_category)) {
        $category_query = "SELECT 
                             t.t_id,
                             t.t_name,
                             t.t_phone,
                             t.t_category,
                             t.t_experience,
                             t.t_booking_limit,
                             t.t_current_bookings,
                             (t.t_booking_limit - t.t_current_bookings) as available_slots,
                             NULL as skill_name,
                             'category' as match_type
                          FROM tms_technician t
                          WHERE t.t_category = ?
                          AND t.t_current_bookings < t.t_booking_limit
                          ORDER BY available_slots DESC, t.t_experience DESC";
        
        $stmt = $mysqli->prepare($category_query);
        $stmt->bind_param('s', $service_category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($tech = $result->fetch_assoc()) {
            $technicians[] = $tech;
        }
    }
    
    return $technicians;
}

/**
 * Format technicians as HTML options for dropdown
 * 
 * @param array $technicians List of technicians
 * @param int $selected_id Currently selected technician ID
 * @return string HTML options
 */
function formatTechniciansAsOptions($technicians, $selected_id = 0) {
    if (empty($technicians)) {
        return '<option value="">❌ No available technicians found</option>';
    }
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Group by match type
    $exact_matches = array_filter($technicians, function($t) { return $t['match_type'] === 'exact'; });
    $partial_matches = array_filter($technicians, function($t) { return $t['match_type'] === 'partial'; });
    $category_matches = array_filter($technicians, function($t) { return $t['match_type'] === 'category'; });
    
    // Exact matches
    if (!empty($exact_matches)) {
        $options .= '<optgroup label="✅ Perfect Match (' . count($exact_matches) . ' technicians)">';
        foreach ($exact_matches as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            $skill = $tech['skill_name'] ? ' - ' . $tech['skill_name'] : '';
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s exp, %d slot%s free%s)</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                $exp,
                $slots,
                $slots != 1 ? 's' : '',
                $skill
            );
        }
        $options .= '</optgroup>';
    }
    
    // Partial matches
    if (!empty($partial_matches)) {
        $options .= '<optgroup label="⚠️ Similar Skills (' . count($partial_matches) . ' technicians)">';
        foreach ($partial_matches as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            $skill = $tech['skill_name'] ? ' - ' . $tech['skill_name'] : '';
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s exp, %d slot%s free%s)</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                $exp,
                $slots,
                $slots != 1 ? 's' : '',
                $skill
            );
        }
        $options .= '</optgroup>';
    }
    
    // Category matches
    if (!empty($category_matches)) {
        $options .= '<optgroup label="📋 Category Match (' . count($category_matches) . ' technicians)">';
        foreach ($category_matches as $tech) {
            $selected = ($tech['t_id'] == $selected_id) ? 'selected' : '';
            $exp = $tech['t_experience'] ? $tech['t_experience'] . ' yrs' : 'New';
            $slots = $tech['available_slots'];
            
            $options .= sprintf(
                '<option value="%d" %s>%s (%s, %s exp, %d slot%s free)</option>',
                $tech['t_id'],
                $selected,
                htmlspecialchars($tech['t_name']),
                htmlspecialchars($tech['t_category']),
                $exp,
                $slots,
                $slots != 1 ? 's' : ''
            );
        }
        $options .= '</optgroup>';
    }
    
    return $options;
}

/**
 * Check if technician can accept a booking
 * 
 * @param mysqli $mysqli Database connection
 * @param int $technician_id Technician ID
 * @return array ['can_accept' => bool, 'message' => string, 'details' => array]
 */
function canTechnicianAcceptBooking($mysqli, $technician_id) {
    $query = "SELECT 
                t_id,
                t_name,
                t_booking_limit,
                t_current_bookings,
                (t_booking_limit - t_current_bookings) as available_slots
              FROM tms_technician
              WHERE t_id = ?";
    
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
    
    $can_accept = $tech['t_current_bookings'] < $tech['t_booking_limit'];
    
    return [
        'can_accept' => $can_accept,
        'message' => $can_accept 
            ? sprintf('%s has %d slot(s) available', $tech['t_name'], $tech['available_slots'])
            : sprintf('%s is at capacity (%d/%d bookings)', $tech['t_name'], $tech['t_current_bookings'], $tech['t_booking_limit']),
        'details' => $tech
    ];
}
?>
