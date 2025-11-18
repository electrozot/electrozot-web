-- Simple Skill-Based Matching System
-- Works with existing service names from booking flow

-- Step 1: Add skills column to technician table (stores comma-separated service names)
ALTER TABLE `tms_technician` 
ADD COLUMN IF NOT EXISTS `t_skills` TEXT COMMENT 'Comma-separated list of services technician can handle';

-- Step 2: Add helper columns for better matching
ALTER TABLE `tms_service_booking`
ADD COLUMN IF NOT EXISTS `sb_service_name` VARCHAR(255) AFTER `sb_service_id`,
ADD COLUMN IF NOT EXISTS `sb_category` VARCHAR(100) AFTER `sb_service_name`,
ADD COLUMN IF NOT EXISTS `sb_subcategory` VARCHAR(100) AFTER `sb_category`;

-- Done! Now admin can:
-- 1. Select skills when adding/editing technician
-- 2. System will match booking service name with technician skills
-- 3. Only show technicians who have that skill

SELECT 'Simple skill matching setup completed!' AS Status;
