<?php
/**
 * AUTO-UPDATE: Booking Status
 * Automatically updates booking status based on technician assignment and completion
 * 
 * Status Flow:
 * 1. New booking (no technician) → Pending
 * 2. Technician assigned → Approved
 * 3. Technician rejects → Not Completed (or Rejected)
 * 4. Technician reassigned → Approved (again)
 * 5. Work completed → Completed
 * 
 * This ensures consistent status across the system
 */

if (!isset($mysqli)) {
    die('Database connection required');
}

// Get the booking ID if provided (for single booking update)
$booking_id = isset($booking_id) ? intval($booking_id) : null;

if ($booking_id) {
    // Update single booking
    $query = "UPDATE tms_service_booking 
              SET sb_status = CASE
                  -- If technician assigned and not rejected/completed
                  WHEN sb_technician_id IS NOT NULL 
                       AND sb_status NOT IN ('Completed', 'Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done')
                  THEN 'Approved'
                  
                  -- If no technician assigned and not rejected/completed
                  WHEN sb_technician_id IS NULL 
                       AND sb_status NOT IN ('Completed', 'Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done')
                  THEN 'Pending'
                  
                  -- Keep existing status for completed/rejected/cancelled
                  ELSE sb_status
              END
              WHERE sb_id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $stmt->close();
    
} else {
    // Update all bookings
    $query = "UPDATE tms_service_booking 
              SET sb_status = CASE
                  -- If technician assigned and not rejected/completed
                  WHEN sb_technician_id IS NOT NULL 
                       AND sb_status NOT IN ('Completed', 'Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done')
                  THEN 'Approved'
                  
                  -- If no technician assigned and not rejected/completed
                  WHEN sb_technician_id IS NULL 
                       AND sb_status NOT IN ('Completed', 'Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done')
                  THEN 'Pending'
                  
                  -- Keep existing status for completed/rejected/cancelled
                  ELSE sb_status
              END";
    
    $mysqli->query($query);
}

// Optional: Log the update (uncomment for debugging)
// error_log("Booking status auto-updated at " . date('Y-m-d H:i:s') . ($booking_id ? " for booking #$booking_id" : " for all bookings"));

?>
