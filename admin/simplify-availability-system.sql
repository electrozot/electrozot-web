-- ============================================================
-- Simplify Technician Availability System
-- Removes redundant status fields, uses only booking capacity
-- ============================================================

-- Step 1: Create backup of current status values
-- ============================================================
CREATE TABLE IF NOT EXISTS tms_technician_status_backup AS 
SELECT t_id, t_status, t_is_available, NOW() as backup_date 
FROM tms_technician;

SELECT 'Backup created' as message;

-- Step 2: Remove redundant t_status column
-- ============================================================
ALTER TABLE tms_technician DROP COLUMN IF EXISTS t_status;

SELECT 't_status column removed' as message;

-- Step 3: Remove redundant t_is_available column
-- ============================================================
ALTER TABLE tms_technician DROP COLUMN IF EXISTS t_is_available;

SELECT 't_is_available column removed' as message;

-- Step 4: Create view for easy availability queries
-- ============================================================
CREATE OR REPLACE VIEW v_technician_availability AS
SELECT 
    t_id,
    t_name,
    t_phone,
    t_email,
    t_category,
    t_specialization,
    t_booking_limit,
    t_current_bookings,
    (t_booking_limit - t_current_bookings) as available_slots,
    CASE 
        WHEN t_current_bookings < t_booking_limit THEN 'Available'
        ELSE 'At Capacity'
    END as availability_status,
    CASE 
        WHEN t_current_bookings < t_booking_limit THEN 1
        ELSE 0
    END as is_available
FROM tms_technician;

SELECT 'Compatibility view created' as message;

-- ============================================================
-- VERIFICATION
-- ============================================================

-- Show all technicians with their availability
SELECT 
    t_name,
    t_category,
    t_booking_limit,
    t_current_bookings,
    (t_booking_limit - t_current_bookings) as available_slots,
    CASE 
        WHEN t_current_bookings < t_booking_limit THEN '✓ Available'
        ELSE '✗ At Capacity'
    END as status
FROM tms_technician
ORDER BY available_slots DESC, t_name ASC;

-- ============================================================
-- USAGE EXAMPLES
-- ============================================================

-- Example 1: Get all available technicians
-- SELECT * FROM tms_technician
-- WHERE t_current_bookings < t_booking_limit
-- ORDER BY (t_booking_limit - t_current_bookings) DESC;

-- Example 2: Use the view (easier)
-- SELECT * FROM v_technician_availability
-- WHERE is_available = 1;

-- Example 3: Get available technicians for specific category
-- SELECT * FROM v_technician_availability
-- WHERE is_available = 1
-- AND t_category = 'AC Repair';

-- Example 4: Check if specific technician is available
-- SELECT 
--   t_name,
--   availability_status,
--   available_slots
-- FROM v_technician_availability
-- WHERE t_id = 1;

-- ============================================================
-- SIMPLIFICATION COMPLETE
-- ============================================================
-- Benefits:
-- ✓ Single source of truth (booking capacity)
-- ✓ No manual status updates needed
-- ✓ Automatically accurate
-- ✓ No confusion between fields
-- ✓ Simpler code
-- ============================================================

SELECT 'Simplification complete! Use t_current_bookings < t_booking_limit for availability checks.' as message;
