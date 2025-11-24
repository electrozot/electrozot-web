<?php
/**
 * AUTO-UPDATE: Technician Status
 * Automatically updates technician availability status based on active bookings
 * 
 * Logic:
 * - Available: Has capacity for more bookings (current_bookings < booking_limit)
 * - Booked/Busy: At full capacity (current_bookings >= booking_limit)
 * 
 * This script can be included anywhere to sync technician statuses
 */

if (!isset($mysqli)) {
    die('Database connection required');
}

// Step 1: Update current booking counts for all technicians
$update_counts = "UPDATE tms_technician t
                 SET t_current_bookings = (
                     SELECT COUNT(*)
                     FROM tms_service_booking sb
                     WHERE sb.sb_technician_id = t.t_id
                     AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                 )";

$mysqli->query($update_counts);

// Step 2: Update status based on booking capacity
$update_status = "UPDATE tms_technician
                 SET t_status = CASE
                     WHEN t_current_bookings >= t_booking_limit THEN 'Booked'
                     WHEN t_current_bookings > 0 AND t_current_bookings < t_booking_limit THEN 'Available'
                     ELSE 'Available'
                 END
                 WHERE t_status NOT IN ('Pending', 'Rejected')"; // Don't update guest technicians

$mysqli->query($update_status);

// Step 3: Update is_available flag (for backward compatibility)
$update_flag = "UPDATE tms_technician
               SET t_is_available = CASE
                   WHEN t_current_bookings < t_booking_limit THEN 1
                   ELSE 0
               END";

$mysqli->query($update_flag);

// Optional: Log the update (uncomment for debugging)
// error_log("Technician status auto-updated at " . date('Y-m-d H:i:s'));

?>
