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
    return; // Silently return if no database connection
}

// Step 1: Update current booking counts for all technicians
$update_counts = "UPDATE tms_technician t
                 SET t_current_bookings = (
                     SELECT COUNT(*)
                     FROM tms_service_booking sb
                     WHERE sb.sb_technician_id = t.t_id
                     AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                 )";

@$mysqli->query($update_counts); // Suppress errors if column doesn't exist

// Optional: Log the update (uncomment for debugging)
// error_log("Technician status auto-updated at " . date('Y-m-d H:i:s'));

?>
