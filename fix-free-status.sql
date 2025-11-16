-- ============================================================================
-- FIX "FREE" STATUS TO "AVAILABLE"
-- ============================================================================
-- This script fixes any technicians with incorrect "Free" status
-- and changes them to the correct "Available" status
-- ============================================================================

-- Step 1: Check how many technicians have "Free" status
SELECT 
    COUNT(*) as total_free_status,
    'Technicians with "Free" status (should be "Available")' as description
FROM tms_technician 
WHERE t_status = 'Free';

-- Step 2: Show all technicians with "Free" status
SELECT 
    t_id,
    t_name,
    t_status,
    t_is_available,
    t_current_booking_id,
    t_category
FROM tms_technician 
WHERE t_status = 'Free';

-- Step 3: Fix "Free" status to "Available"
UPDATE tms_technician 
SET t_status = 'Available',
    t_is_available = 1,
    t_current_booking_id = NULL
WHERE t_status = 'Free';

-- Step 4: Verify the fix
SELECT 
    COUNT(*) as remaining_free_status,
    'Should be 0 if fix was successful' as description
FROM tms_technician 
WHERE t_status = 'Free';

-- Step 5: Show current status distribution
SELECT 
    t_status,
    COUNT(*) as count,
    GROUP_CONCAT(t_name SEPARATOR ', ') as technicians
FROM tms_technician 
GROUP BY t_status;

-- ============================================================================
-- ADDITIONAL CLEANUP
-- ============================================================================

-- Fix any other invalid status values (case-insensitive)
UPDATE tms_technician 
SET t_status = 'Available',
    t_is_available = 1,
    t_current_booking_id = NULL
WHERE LOWER(t_status) IN ('free', 'idle', 'ready', 'waiting');

-- Fix any technicians with "available" (lowercase)
UPDATE tms_technician 
SET t_status = 'Available'
WHERE t_status = 'available';

-- Fix any technicians with "booked" (lowercase)
UPDATE tms_technician 
SET t_status = 'Booked'
WHERE t_status = 'booked';

-- ============================================================================
-- FINAL VERIFICATION
-- ============================================================================

-- Show all technicians with their current status
SELECT 
    t_id,
    t_name,
    t_status,
    t_is_available,
    t_current_booking_id,
    t_category,
    CASE 
        WHEN t_status = 'Available' AND t_is_available = 1 AND t_current_booking_id IS NULL THEN '✓ Correct'
        WHEN t_status = 'Booked' AND t_is_available = 0 AND t_current_booking_id IS NOT NULL THEN '✓ Correct'
        ELSE '✗ Inconsistent'
    END as status_check
FROM tms_technician 
ORDER BY t_status, t_name;

-- ============================================================================
-- SUMMARY
-- ============================================================================

SELECT 
    'Total Technicians' as metric,
    COUNT(*) as count
FROM tms_technician
UNION ALL
SELECT 
    'Available (Free)' as metric,
    COUNT(*) as count
FROM tms_technician 
WHERE t_status = 'Available'
UNION ALL
SELECT 
    'Booked (Busy)' as metric,
    COUNT(*) as count
FROM tms_technician 
WHERE t_status = 'Booked'
UNION ALL
SELECT 
    'Invalid Status' as metric,
    COUNT(*) as count
FROM tms_technician 
WHERE t_status NOT IN ('Available', 'Booked');
