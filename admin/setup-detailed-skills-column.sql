-- ============================================================================
-- Setup Detailed Skills Column for Technician Skill Matching
-- ============================================================================
-- This script adds the t_skills column to store detailed service skills
-- Run this ONCE to enable the improved skill matching system
-- ============================================================================

-- Add t_skills column if it doesn't exist
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_skills TEXT DEFAULT NULL
COMMENT 'Comma-separated list of detailed service skills';

-- Add index for faster skill searching
ALTER TABLE tms_technician 
ADD FULLTEXT INDEX IF NOT EXISTS idx_skills (t_skills);

-- Verify the column was added
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'tms_technician' 
AND COLUMN_NAME = 't_skills';

-- Show sample of technicians with their skills
SELECT 
    t_id,
    t_name,
    t_category,
    t_skills,
    t_booking_limit,
    t_current_bookings
FROM tms_technician
LIMIT 5;

-- ============================================================================
-- NOTES:
-- ============================================================================
-- 1. The t_skills column stores skills as comma-separated values
--    Example: "AC (Split) - Repair,Refrigerator - Repair,Washing Machine - Repair"
--
-- 2. Skills should match EXACTLY with service names from tms_service table
--
-- 3. To add skills to a technician:
--    - Go to Admin → Manage Technicians
--    - Click Edit on technician
--    - Check the skills in "Detailed Service Skills" section
--    - Save
--
-- 4. The matching system uses FIND_IN_SET() to search skills efficiently
--
-- 5. If you need to manually add skills via SQL:
--    UPDATE tms_technician 
--    SET t_skills = 'AC (Split) - Repair,Refrigerator - Repair'
--    WHERE t_id = 1;
-- ============================================================================
