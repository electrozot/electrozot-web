-- ============================================================================
-- Technician Engagement System - Database Updates
-- ============================================================================
-- This script adds necessary columns to support the one-booking-per-technician rule
-- Run this script to ensure your database has all required fields
-- ============================================================================

-- Add availability tracking columns to technician table
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1 
COMMENT 'Tracks if technician is available for new bookings (1=available, 0=engaged)';

ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_current_booking_id INT DEFAULT NULL 
COMMENT 'Current booking ID if technician is engaged';

-- Add index for faster availability queries
CREATE INDEX IF NOT EXISTS idx_technician_availability 
ON tms_technician(t_is_available, t_status, t_category);

-- Add index for current booking lookup
CREATE INDEX IF NOT EXISTS idx_technician_current_booking 
ON tms_technician(t_current_booking_id);

-- ============================================================================
-- Initialize existing technicians
-- ============================================================================
-- Set all technicians to available if they don't have active bookings
UPDATE tms_technician t
SET t.t_is_available = 1,
    t.t_current_booking_id = NULL,
    t.t_status = 'Available'
WHERE t.t_id NOT IN (
    SELECT DISTINCT sb.sb_technician_id
    FROM tms_service_booking sb
    WHERE sb.sb_technician_id IS NOT NULL
    AND sb.sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
);

-- Set technicians to engaged if they have active bookings
UPDATE tms_technician t
INNER JOIN (
    SELECT sb_technician_id, sb_id, sb_status
    FROM tms_service_booking
    WHERE sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
    AND sb_technician_id IS NOT NULL
) sb ON t.t_id = sb.sb_technician_id
SET t.t_is_available = 0,
    t.t_current_booking_id = sb.sb_id,
    t.t_status = 'Booked';

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Check technician engagement status
SELECT 
    t.t_id,
    t.t_name,
    t.t_category,
    t.t_status,
    t.t_is_available,
    t.t_current_booking_id,
    sb.sb_status as current_booking_status
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_current_booking_id = sb.sb_id
ORDER BY t.t_name;

-- Count available vs engaged technicians
SELECT 
    COUNT(*) as total_technicians,
    SUM(CASE WHEN t_is_available = 1 THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN t_is_available = 0 THEN 1 ELSE 0 END) as engaged
FROM tms_technician;

-- Find technicians with inconsistent status
SELECT 
    t.t_id,
    t.t_name,
    t.t_status,
    t.t_is_available,
    t.t_current_booking_id,
    COUNT(sb.sb_id) as active_bookings
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
    AND sb.sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
GROUP BY t.t_id, t.t_name, t.t_status, t.t_is_available, t.t_current_booking_id
HAVING (t.t_is_available = 0 AND active_bookings = 0) 
    OR (t.t_is_available = 1 AND active_bookings > 0);

-- ============================================================================
-- Maintenance Queries (Run periodically to fix inconsistencies)
-- ============================================================================

-- Fix technicians marked as engaged but have no active bookings
UPDATE tms_technician t
SET t.t_is_available = 1,
    t.t_current_booking_id = NULL,
    t.t_status = 'Available'
WHERE t.t_is_available = 0
AND t.t_id NOT IN (
    SELECT DISTINCT sb_technician_id
    FROM tms_service_booking
    WHERE sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
    AND sb_technician_id IS NOT NULL
);

-- Fix technicians marked as available but have active bookings
UPDATE tms_technician t
INNER JOIN (
    SELECT sb_technician_id, MIN(sb_id) as booking_id
    FROM tms_service_booking
    WHERE sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
    AND sb_technician_id IS NOT NULL
    GROUP BY sb_technician_id
) sb ON t.t_id = sb.sb_technician_id
SET t.t_is_available = 0,
    t.t_current_booking_id = sb.booking_id,
    t.t_status = 'Booked'
WHERE t.t_is_available = 1;

-- ============================================================================
-- Success Message
-- ============================================================================
SELECT 'Technician engagement system columns added successfully!' as status;
SELECT 'Run the verification queries above to check the current state.' as next_step;
