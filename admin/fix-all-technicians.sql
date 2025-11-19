-- ============================================
-- Fix All Technician Slots and Availability
-- Run this SQL to sync all technician data
-- ============================================

-- Step 1: Sync all technician booking counts with actual active bookings
UPDATE tms_technician t
SET t_current_bookings = (
    SELECT COUNT(*)
    FROM tms_service_booking sb
    WHERE sb.sb_technician_id = t.t_id
    AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
);

-- Step 2: Update all technician statuses based on their booking count
UPDATE tms_technician
SET t_status = CASE
    WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
    ELSE 'Available'
END;

-- Step 3: Verify the fix - Show all technicians with their updated data
SELECT 
    t_id,
    t_name,
    t_status,
    t_current_bookings,
    t_booking_limit,
    (t_booking_limit - t_current_bookings) as available_slots,
    (SELECT COUNT(*) 
     FROM tms_service_booking sb 
     WHERE sb.sb_technician_id = t.t_id 
     AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')) as verified_count
FROM tms_technician
ORDER BY t_name;

-- ============================================
-- Expected Results:
-- - Abhi should show 0/2 bookings, Available
-- - All technicians should have correct counts
-- - Status should match booking capacity
-- ============================================
