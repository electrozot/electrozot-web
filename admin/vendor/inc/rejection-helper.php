<?php
/**
 * Helper functions for rejection tracking system
 */

/**
 * Check if technician is blocked from receiving bookings
 */
function isTechnicianBlocked($mysqli, $technician_id) {
    $stmt = $mysqli->prepare("SELECT t_blocked_until, t_block_reason FROM tms_technician WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_object();
    
    if ($tech && $tech->t_blocked_until) {
        $blocked_until = strtotime($tech->t_blocked_until);
        $now = time();
        
        if ($now < $blocked_until) {
            // Still blocked
            return [
                'blocked' => true,
                'until' => $tech->t_blocked_until,
                'reason' => $tech->t_block_reason
            ];
        } else {
            // Block expired, clear it
            $mysqli->query("UPDATE tms_technician SET t_blocked_until = NULL, t_block_reason = NULL WHERE t_id = $technician_id");
            return ['blocked' => false];
        }
    }
    
    return ['blocked' => false];
}

/**
 * Get available technicians (excluding blocked ones)
 */
function getAvailableTechniciansExcludingBlocked($mysqli, $service_id = null) {
    $query = "SELECT t.* FROM tms_technician t 
              WHERE t.t_status = 'Available' 
              AND (t.t_blocked_until IS NULL OR t.t_blocked_until < NOW())";
    
    if ($service_id) {
        $query .= " AND EXISTS (
            SELECT 1 FROM tms_technician_skills ts 
            WHERE ts.ts_technician_id = t.t_id 
            AND ts.ts_service_id = $service_id
        )";
    }
    
    $query .= " ORDER BY t.t_current_bookings ASC";
    
    return $mysqli->query($query);
}

/**
 * Auto-unblock technicians whose block period has expired
 */
function autoUnblockTechnicians($mysqli) {
    $mysqli->query("UPDATE tms_technician 
                   SET t_blocked_until = NULL, 
                       t_block_reason = NULL,
                       t_status = CASE 
                           WHEN t_status = 'Locked' THEN 'Available'
                           ELSE t_status
                       END
                   WHERE t_blocked_until IS NOT NULL 
                   AND t_blocked_until < NOW()");
    
    return $mysqli->affected_rows;
}
?>
