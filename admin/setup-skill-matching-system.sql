-- ============================================================================
-- SETUP SKILL-BASED TECHNICIAN MATCHING SYSTEM
-- ============================================================================
-- This script adds the t_skills column to enable skill-based matching
-- Run this ONCE to enable the system
-- ============================================================================

-- Step 1: Add t_skills column to store technician skills
ALTER TABLE `tms_technician` 
ADD COLUMN IF NOT EXISTS `t_skills` TEXT DEFAULT NULL
COMMENT 'Comma-separated list of services technician can perform';

-- Step 2: Add index for faster searching
ALTER TABLE `tms_technician` 
ADD FULLTEXT INDEX IF NOT EXISTS `idx_t_skills` (`t_skills`);

-- Step 3: Verify the column was added
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'tms_technician' 
AND COLUMN_NAME = 't_skills';

-- Step 4: Show current technicians (skills will be NULL initially)
SELECT 
    t_id,
    t_name,
    t_category,
    t_specialization,
    t_skills,
    t_booking_limit,
    t_current_bookings
FROM tms_technician
ORDER BY t_id;

-- ============================================================================
-- NOTES:
-- ============================================================================
-- 1. After running this script, you need to edit each technician to add their skills
-- 2. Go to Admin Panel → Manage Technicians → Edit Technician
-- 3. Check the services each technician can perform
-- 4. Skills will be saved as comma-separated values
--    Example: "Wash Basin Installation,Tap Repair,AC Repair"
-- 5. The system will then match technicians based on these skills
-- ============================================================================
