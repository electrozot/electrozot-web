<?php
/**
 * Technician Availability Checker
 * 
 * This file ensures that a technician can only handle ONE booking at a time.
 * A technician is considered "engaged" if they have ANY active booking, regardless of:
 * - Fresh assignment
 * - Reassignment
 * - Changed technician
 * 
 * A technician becomes available ONLY when they:
 * - Complete the booking
 * - Reject the booking
 * - Admin cancels/removes their assignment
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('vendor/inc/config.php');

/**
 * Check if a technician has reached their booking limit
 * Uses t_booking_limit and t_current_bookings fields
 * 
 * @param int $technician_id The technician ID to check
 * @param mysqli $mysqli Database connection
 * @return array ['is_at_limit' => bool, 'current_bookings' => int, 'booking_limit' => int, 'available_slots' => int]
 */
function checkTechnicianEngagement($technician_id, $mysqli) {
    // Get technician's booking limit and current bookings
    $query = "SELECT t_booking_limit, t_current_bookings, 
                     (t_booking_limit - t_current_bookings) as available_slots
              FROM tms_technician 
              WHERE t_id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $tech = $result->fetch_assoc();
        $is_at_limit = ($tech['t_current_bookings'] >= $tech['t_booking_limit']);
        
        return [
            'is_engaged' => $is_at_limit, // For backward compatibility
            'is_at_limit' => $is_at_limit,
            'current_bookings' => (int)$tech['t_current_bookings'],
            'booking_limit' => (int)$tech['t_booking_limit'],
            'available_slots' => (int)$tech['available_slots'],
            'booking_id' => null, // For backward compatibility
            'booking_status' => $is_at_limit ? 'At Capacity' : 'Available',
            'booking_date' => null,
            'booking_time' => null
        ];
    }
    
    // Default return if technician not found
    return [
        'is_engaged' => true,
        'is_at_limit' => true,
        'current_bookings' => 0,
        'booking_limit' => 0,
        'available_slots' => 0,
        'booking_id' => null,
        'booking_status' => 'Unknown',
        'booking_date' => null,
        'booking_time' => null
    ];
}

/**
 * Get all available technicians for a specific service category
 * Only returns technicians who are NOT currently engaged with any booking
 * Matches by: 1) Detailed Skills, 2) Category, 3) Specialization
 * 
 * @param string $service_category The service category or service name to match
 * @param mysqli $mysqli Database connection
 * @param int $exclude_booking_id Optional: Exclude technician currently assigned to this booking (for reassignment)
 * @return array List of available technicians
 */
function getAvailableTechnicians($service_category, $mysqli, $exclude_booking_id = null) {
    // PRIORITY 1: Match by detailed service skills
    // Now includes booking limit check
    $skill_query = "SELECT DISTINCT t.t_id, t.t_name, t.t_phone, t.t_email, t.t_specialization, t.t_category, t.t_status,
                    t.t_booking_limit, t.t_current_bookings,
                    (t.t_booking_limit - t.t_current_bookings) as available_slots,
                    GROUP_CONCAT(ts.skill_name SEPARATOR ', ') as skills
                    FROM tms_technician t
                    INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
                    WHERE (ts.skill_name LIKE ? OR ts.skill_name LIKE ?)
                      AND t.t_current_bookings < t.t_booking_limit
                    GROUP BY t.t_id
                    ORDER BY available_slots DESC, t.t_current_bookings ASC";
    
    $stmt = $mysqli->prepare($skill_query);
    $like_pattern = '%' . $service_category . '%';
    $stmt->bind_param('ss', $like_pattern, $service_category);
    $stmt->execute();
    $skill_result = $stmt->get_result();
    
    $available_technicians = [];
    $matched_tech_ids = [];
    
    // Process technicians matched by skills (HIGHEST PRIORITY)
    while($tech = $skill_result->fetch_assoc()) {
        $matched_tech_ids[] = $tech['t_id'];
        
        // Technician has available slots
        $tech['is_available'] = true;
        $tech['current_booking'] = null;
        $tech['availability_note'] = '✓ Skill Match - ' . $tech['available_slots'] . ' slot(s) available';
        $tech['match_type'] = 'skill';
        
        $available_technicians[] = $tech;
    }
    
    // PRIORITY 2: Match by category or specialization (if no skill matches found)
    if(empty($available_technicians)) {
        $category_query = "SELECT t_id, t_name, t_phone, t_email, t_specialization, t_category, t_status,
                                  t_booking_limit, t_current_bookings,
                                  (t_booking_limit - t_current_bookings) as available_slots
                          FROM tms_technician 
                          WHERE (t_category = ? OR t_category LIKE ? OR t_specialization LIKE ?)
                            AND t_current_bookings < t_booking_limit
                          ORDER BY available_slots DESC, t_current_bookings ASC";
        
        $stmt2 = $mysqli->prepare($category_query);
        $like_pattern = '%' . $service_category . '%';
        $stmt2->bind_param('sss', $service_category, $like_pattern, $like_pattern);
        $stmt2->execute();
        $category_result = $stmt2->get_result();
        
        while($tech = $category_result->fetch_assoc()) {
            // Skip if already matched by skills
            if(in_array($tech['t_id'], $matched_tech_ids)) {
                continue;
            }
            
            // Technician has available slots
            $tech['is_available'] = true;
            $tech['current_booking'] = null;
            $tech['availability_note'] = 'Category Match - ' . $tech['available_slots'] . ' slot(s) available';
            $tech['match_type'] = 'category';
            $tech['skills'] = null;
            
            $available_technicians[] = $tech;
        }
    }
    
    // If no technicians found with category match, get ALL available technicians as fallback
    if(empty($available_technicians)) {
        $fallback_query = "SELECT t_id, t_name, t_phone, t_email, t_specialization, t_category, t_status,
                                  t_booking_limit, t_current_bookings,
                                  (t_booking_limit - t_current_bookings) as available_slots
                          FROM tms_technician 
                          WHERE t_current_bookings < t_booking_limit
                          ORDER BY available_slots DESC, t_current_bookings ASC";
        $fallback_result = $mysqli->query($fallback_query);
        
        while($tech = $fallback_result->fetch_assoc()) {
            $tech['is_available'] = true;
            $tech['current_booking'] = null;
            $tech['availability_note'] = 'Available - ' . $tech['available_slots'] . ' slot(s) free';
            $tech['match_type'] = 'general';
            $tech['skills'] = null;
            
            $available_technicians[] = $tech;
        }
    }
    
    return $available_technicians;
}

/**
 * Mark technician as engaged with a booking
 * 
 * @param int $technician_id The technician ID
 * @param int $booking_id The booking ID
 * @param mysqli $mysqli Database connection
 * @return bool Success status
 */
function engageTechnician($technician_id, $booking_id, $mysqli) {
    // First check if technician is already engaged
    $engagement = checkTechnicianEngagement($technician_id, $mysqli);
    
    if($engagement['is_engaged'] && $engagement['booking_id'] != $booking_id) {
        // Technician is already engaged with a different booking
        return false;
    }
    
    // Update technician status to Booked
    $query = "UPDATE tms_technician SET t_status = 'Booked' WHERE t_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $technician_id);
    return $stmt->execute();
}

/**
 * Free up technician (make them available for new bookings)
 * 
 * @param int $technician_id The technician ID
 * @param mysqli $mysqli Database connection
 * @return bool Success status
 */
function freeTechnician($technician_id, $mysqli) {
    // Update technician status to Available
    $query = "UPDATE tms_technician SET t_status = 'Available' WHERE t_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $technician_id);
    return $stmt->execute();
}

/**
 * Get technician engagement summary (for admin dashboard)
 * 
 * @param mysqli $mysqli Database connection
 * @return array Summary of all technicians and their engagement status
 */
function getTechnicianEngagementSummary($mysqli) {
    $query = "SELECT t_id, t_name, t_category, t_status FROM tms_technician ORDER BY t_name ASC";
    $result = $mysqli->query($query);
    
    $summary = [];
    
    while($tech = $result->fetch_assoc()) {
        $engagement = checkTechnicianEngagement($tech['t_id'], $mysqli);
        
        $summary[] = [
            'technician_id' => $tech['t_id'],
            'technician_name' => $tech['t_name'],
            'category' => $tech['t_category'],
            'status' => $tech['t_status'],
            'is_engaged' => $engagement['is_engaged'],
            'current_booking_id' => $engagement['booking_id'],
            'current_booking_status' => $engagement['booking_status']
        ];
    }
    
    return $summary;
}

// If called directly via AJAX, return JSON response
if(isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch($_GET['action']) {
        case 'check_engagement':
            if(isset($_GET['technician_id'])) {
                $tech_id = intval($_GET['technician_id']);
                $engagement = checkTechnicianEngagement($tech_id, $mysqli);
                echo json_encode($engagement);
            } else {
                echo json_encode(['error' => 'Technician ID required']);
            }
            break;
            
        case 'get_available':
            if(isset($_GET['category'])) {
                $category = $_GET['category'];
                $exclude_booking = isset($_GET['exclude_booking']) ? intval($_GET['exclude_booking']) : null;
                $available = getAvailableTechnicians($category, $mysqli, $exclude_booking);
                echo json_encode($available);
            } else {
                echo json_encode(['error' => 'Category required']);
            }
            break;
            
        case 'get_summary':
            $summary = getTechnicianEngagementSummary($mysqli);
            echo json_encode($summary);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit();
}
?>
