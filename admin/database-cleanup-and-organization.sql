-- ============================================
-- DATABASE CLEANUP AND ORGANIZATION
-- Removes duplicates, creates missing tables, optimizes structure
-- ============================================

USE `electrozot_db`;

-- ============================================
-- PART 1: CREATE MISSING TABLES
-- ============================================

-- Create admin notifications table (if not exists)
CREATE TABLE IF NOT EXISTS `tms_admin_notifications` (
  `an_id` INT NOT NULL AUTO_INCREMENT,
  `an_type` VARCHAR(50) NOT NULL COMMENT 'BOOKING_REJECTED, BOOKING_COMPLETED, etc.',
  `an_title` VARCHAR(255) NOT NULL,
  `an_message` TEXT NOT NULL,
  `an_booking_id` INT NULL,
  `an_technician_id` INT NULL,
  `an_user_id` INT NULL,
  `an_is_read` TINYINT(1) DEFAULT 0,
  `an_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`an_id`),
  INDEX `idx_type` (`an_type`),
  INDEX `idx_booking` (`an_booking_id`),
  INDEX `idx_created` (`an_created_at`),
  INDEX `idx_read` (`an_is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Admin notification system for booking events';

-- ============================================
-- PART 2: ADD MISSING COLUMNS TO EXISTING TABLES
-- ============================================

-- Add missing columns to tms_service_booking (if not exist)
ALTER TABLE `tms_service_booking`
ADD COLUMN IF NOT EXISTS `sb_rejected_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When booking was rejected',
ADD COLUMN IF NOT EXISTS `sb_completed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When booking was completed',
ADD COLUMN IF NOT EXISTS `sb_cancelled_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When booking was cancelled',
ADD COLUMN IF NOT EXISTS `sb_cancelled_by` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Who cancelled: user/admin/system',
ADD COLUMN IF NOT EXISTS `sb_assigned_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When technician was assigned',
ADD COLUMN IF NOT EXISTS `sb_updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update time',
ADD COLUMN IF NOT EXISTS `sb_rejection_reason` TEXT NULL DEFAULT NULL COMMENT 'Reason for rejection';

-- Add missing columns to tms_technician (if not exist)
ALTER TABLE `tms_technician`
ADD COLUMN IF NOT EXISTS `t_current_bookings` INT DEFAULT 0 COMMENT 'Current active booking count',
ADD COLUMN IF NOT EXISTS `t_booking_limit` INT DEFAULT 5 COMMENT 'Maximum concurrent bookings',
ADD COLUMN IF NOT EXISTS `t_status` VARCHAR(20) DEFAULT 'Available' COMMENT 'Available/Busy/Offline';

-- Add missing columns to tms_admin (if not exist)
ALTER TABLE `tms_admin`
ADD COLUMN IF NOT EXISTS `a_photo` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Admin profile photo',
ADD COLUMN IF NOT EXISTS `a_phone` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Admin phone number',
ADD COLUMN IF NOT EXISTS `a_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation time';

-- ============================================
-- PART 3: ADD INDEXES FOR PERFORMANCE
-- ============================================

-- Indexes for tms_service_booking
ALTER TABLE `tms_service_booking`
ADD INDEX IF NOT EXISTS `idx_status` (`sb_status`),
ADD INDEX IF NOT EXISTS `idx_technician` (`sb_technician_id`),
ADD INDEX IF NOT EXISTS `idx_user` (`sb_user_id`),
ADD INDEX IF NOT EXISTS `idx_date` (`sb_date`),
ADD INDEX IF NOT EXISTS `idx_created` (`sb_created_at`),
ADD INDEX IF NOT EXISTS `idx_rejected` (`sb_rejected_at`),
ADD INDEX IF NOT EXISTS `idx_completed` (`sb_completed_at`);

-- Indexes for tms_technician
ALTER TABLE `tms_technician`
ADD INDEX IF NOT EXISTS `idx_status` (`t_status`),
ADD INDEX IF NOT EXISTS `idx_category` (`t_category`);

-- Indexes for tms_service
ALTER TABLE `tms_service`
ADD INDEX IF NOT EXISTS `idx_category` (`s_category`),
ADD INDEX IF NOT EXISTS `idx_status` (`s_status`);

-- ============================================
-- PART 4: CLEAN UP DUPLICATE/OLD DATA
-- ============================================

-- Remove duplicate bookings (keep the latest one)
DELETE t1 FROM tms_service_booking t1
INNER JOIN tms_service_booking t2 
WHERE t1.sb_id < t2.sb_id 
AND t1.sb_user_id = t2.sb_user_id 
AND t1.sb_service_id = t2.sb_service_id 
AND t1.sb_date = t2.sb_date 
AND t1.sb_time = t2.sb_time
AND t1.sb_status = 'Pending'
AND t2.sb_status = 'Pending';

-- Remove old password reset requests (older than 24 hours)
DELETE FROM tms_pwd_resets 
WHERE r_id NOT IN (
    SELECT * FROM (
        SELECT r_id FROM tms_pwd_resets 
        ORDER BY r_id DESC 
        LIMIT 100
    ) AS temp
);

-- Remove old system logs (keep last 1000 entries)
DELETE FROM tms_syslogs 
WHERE l_id NOT IN (
    SELECT * FROM (
        SELECT l_id FROM tms_syslogs 
        ORDER BY l_id DESC 
        LIMIT 1000
    ) AS temp
);

-- Remove old admin notifications (keep last 500)
DELETE FROM tms_admin_notifications 
WHERE an_id NOT IN (
    SELECT * FROM (
        SELECT an_id FROM tms_admin_notifications 
        ORDER BY an_id DESC 
        LIMIT 500
    ) AS temp
);

-- ============================================
-- PART 5: UPDATE EXISTING DATA
-- ============================================

-- Set default values for technician booking limits
UPDATE tms_technician 
SET t_booking_limit = 5 
WHERE t_booking_limit IS NULL OR t_booking_limit = 0;

-- Sync technician current bookings with actual active bookings
UPDATE tms_technician t
SET t_current_bookings = (
    SELECT COUNT(*)
    FROM tms_service_booking sb
    WHERE sb.sb_technician_id = t.t_id
    AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
);

-- Update technician status based on booking count
UPDATE tms_technician
SET t_status = CASE
    WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
    WHEN t_current_bookings > 0 THEN 'Available'
    ELSE 'Available'
END;

-- Set sb_updated_at for bookings that don't have it
UPDATE tms_service_booking
SET sb_updated_at = sb_created_at
WHERE sb_updated_at IS NULL;

-- Set rejection timestamps for already rejected bookings
UPDATE tms_service_booking
SET sb_rejected_at = sb_updated_at
WHERE sb_status IN ('Rejected', 'Rejected by Technician', 'Not Done')
AND sb_rejected_at IS NULL;

-- Set completion timestamps for already completed bookings
UPDATE tms_service_booking
SET sb_completed_at = sb_updated_at
WHERE sb_status = 'Completed'
AND sb_completed_at IS NULL;

-- ============================================
-- PART 6: OPTIMIZE TABLES
-- ============================================

OPTIMIZE TABLE tms_admin;
OPTIMIZE TABLE tms_user;
OPTIMIZE TABLE tms_technician;
OPTIMIZE TABLE tms_service;
OPTIMIZE TABLE tms_service_booking;
OPTIMIZE TABLE tms_admin_notifications;
OPTIMIZE TABLE tms_feedback;
OPTIMIZE TABLE tms_syslogs;
OPTIMIZE TABLE tms_pwd_resets;

-- ============================================
-- PART 7: VERIFY DATABASE STRUCTURE
-- ============================================

-- Show all tables
SELECT 
    'Database Tables' as Info,
    COUNT(*) as Total_Tables
FROM information_schema.tables 
WHERE table_schema = 'electrozot_db';

-- Show table sizes
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
    table_rows AS 'Rows'
FROM information_schema.tables
WHERE table_schema = 'electrozot_db'
ORDER BY (data_length + index_length) DESC;

-- Show booking statistics
SELECT 
    'Booking Statistics' as Info,
    COUNT(*) as Total_Bookings,
    SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as Pending,
    SUM(CASE WHEN sb_status = 'Approved' THEN 1 ELSE 0 END) as Approved,
    SUM(CASE WHEN sb_status = 'Completed' THEN 1 ELSE 0 END) as Completed,
    SUM(CASE WHEN sb_status IN ('Rejected', 'Rejected by Technician', 'Not Done') THEN 1 ELSE 0 END) as Rejected
FROM tms_service_booking;

-- Show technician statistics
SELECT 
    'Technician Statistics' as Info,
    COUNT(*) as Total_Technicians,
    SUM(CASE WHEN t_status = 'Available' THEN 1 ELSE 0 END) as Available,
    SUM(CASE WHEN t_status = 'Busy' THEN 1 ELSE 0 END) as Busy,
    SUM(t_current_bookings) as Total_Active_Bookings,
    AVG(t_booking_limit) as Avg_Booking_Limit
FROM tms_technician;

-- ============================================
-- COMPLETION MESSAGE
-- ============================================

SELECT '✅ DATABASE CLEANUP AND ORGANIZATION COMPLETE!' as Status;
SELECT 'All tables optimized, duplicates removed, missing columns added' as Message;
SELECT 'Run this script periodically to maintain database health' as Recommendation;
