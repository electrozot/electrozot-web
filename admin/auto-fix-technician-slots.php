<?php
/**
 * AUTO-FIX: Technician Slots
 * This file automatically syncs technician data when included
 * Include this at the top of admin-dashboard.php or admin-manage-technicians.php
 */

// Only run if not already run in this session
if (!isset($_SESSION['technician_slots_synced'])) {
    
    // Sync all technician booking counts
    $sync_sql = "UPDATE tms_technician t
                SET t_current_bookings = (
                    SELECT COUNT(*)
                    FROM tms_service_booking sb
                    WHERE sb.sb_technician_id = t.t_id
                    AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                )";
    
    $mysqli->query($sync_sql);
    
    // Update all technician statuses
    $status_sql = "UPDATE tms_technician
                  SET t_status = CASE
                      WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
                      ELSE 'Available'
                  END";
    
    $mysqli->query($status_sql);
    
    // Mark as synced for this session
    $_SESSION['technician_slots_synced'] = true;
    $_SESSION['technician_slots_synced_time'] = time();
}
?>
