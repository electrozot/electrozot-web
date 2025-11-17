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
 * Check if a technician is currently engaged with any booking
 * 
 * @param int $technician_id The technician ID to check
 * @param mysqli $mysqli Database connection
 * @return array ['is_engaged' => bool, 'booking_id' => int|null, 'booking_status' => string|null]
 */
function checkTechnicianEngagement($technician_id, $mysqli) {
    // Check if technician has any active booking
    // Active means: Pending, Approved, Assigned, In Progress
    // NOT active means: Completed, Rejected, Cancelled, Not Done
    
    $query = "SELECT sb_id, sb_status, sb_booking_date, sb_booking_time 
              FROM tms_service_booking 
              WHERE sb_technician_id = ? 
              AND sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
              ORDER BY sb_created_at DESC
              LIMIT 1";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        return [
            'is_engaged' => true,
            'booking_id' => $booking['sb_id'],
            'booking_status' => $booking['sb_status'],
            'booking_date' => $booking['sb_booking_date'],
            'booking_time' => $booking['sb_booking_time']
        ];
    }
    
    return [
        'is_engaged' => false,
        'booking_id' => null,
        'booking_status' => null,
        'booking_date' => null,
        'booking_time' => null
    ];
}

/**
 * Get all available technicians for a specific service category
 * Only returns technicians who are NOT currently engaged with any booking
 * 
 * @param string $service_category The service category to match
 * @param mysqli $mysqli Database connection
 * @param int $exclude_booking_id Optional: Exclude technician currently assigned to this booking (for reassignment)
 * @return array List of available technicians
 */
function getAvailableTechnicians($service_category, $mysqli, $exclude_booking_id = null) {
    // Get all technicians matching the service category
    // Try exact match first, then partial match (LIKE)
    $query = "SELECT t_id, t_name, t_phone, t_email, t_specialization, t_category, t_status 
              FROM tms_technician 
              WHERE t_category = ? OR t_category LIKE ? OR t_specialization LIKE ?
              ORDER BY t_name ASC";
    
    $stmt = $mysqli->prepare($query);
    $like_pattern = '%' . $service_category . '%';
    $stmt->bind_param('sss', $service_category, $like_pattern, $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $available_technicians = [];
    
    while($tech = $result->fetch_assoc()) {
        // Check if this technician is engaged
        $engagement = checkTechnicianEngagement($tech['t_id'], $mysqli);
        
        // If technician is engaged, check if it's with the excluded booking (for reassignment)
        if($engagement['is_engaged']) {
            if($exclude_booking_id && $engagement['booking_id'] == $exclude_booking_id) {
                // This technician is assigned to the booking we're reassigning, so include them
                $tech['is_available'] = true;
                $tech['current_booking'] = $engagement['booking_id'];
                $tech['availability_note'] = 'Currently assigned to this booking';
            } else {
                // Technician is engaged with another booking, skip them
                continue;
            }
        } else {
            // Technician is free
            $tech['is_available'] = true;
            $tech['current_booking'] = null;
            $tech['availability_note'] = 'Available';
        }
        
        $available_technicians[] = $tech;
    }
    
    // If no technicians found with category match, get ALL available technicians as fallback
    if(empty($available_technicians)) {
        $fallback_query = "SELECT t_id, t_name, t_phone, t_email, t_specialization, t_category, t_status 
                          FROM tms_technician 
                          ORDER BY t_name ASC";
        $fallback_result = $mysqli->query($fallback_query);
        
        while($tech = $fallback_result->fetch_assoc()) {
            $engagement = checkTechnicianEngagement($tech['t_id'], $mysqli);
            
            if($engagement['is_engaged']) {
                if($exclude_booking_id && $engagement['booking_id'] == $exclude_booking_id) {
                    $tech['is_available'] = true;
                    $tech['current_booking'] = $engagement['booking_id'];
                    $tech['availability_note'] = 'Currently assigned to this booking';
                } else {
                    continue;
                }
            } else {
                $tech['is_available'] = true;
                $tech['current_booking'] = null;
                $tech['availability_note'] = 'Available (No category match)';
            }
            
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
