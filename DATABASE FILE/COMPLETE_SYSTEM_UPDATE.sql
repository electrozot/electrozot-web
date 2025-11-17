-- ============================================================================
-- COMPLETE BOOKING SYSTEM UPDATE
-- Implements all required logic for booking management
-- ============================================================================

USE `electrozot_db`;

-- ============================================================================
-- 1. UPDATE TECHNICIAN TABLE - Add booking limits and tracking
-- ============================================================================

ALTER TABLE `tms_technician` 
ADD COLUMN IF NOT EXISTS `t_booking_limit` INT DEFAULT 5 COMMENT 'Max bookings (1-5)',
ADD COLUMN IF NOT EXISTS `t_current_bookings` INT DEFAULT 0 COMMENT 'Current active bookings',
ADD COLUMN IF NOT EXISTS `t_email` VARCHAR(200) DEFAULT NULL COMMENT 'Technician email',
ADD COLUMN IF NOT EXISTS `t_phone` VARCHAR(200) DEFAULT NULL COMMENT 'Technician phone',
ADD COLUMN IF NOT EXISTS `t_password` VARCHAR(200) DEFAULT NULL COMMENT 'Technician login password',
ADD COLUMN IF NOT EXISTS `t_daily_assigned` INT DEFAULT 0 COMMENT 'Daily assigned count',
ADD COLUMN IF NOT EXISTS `t_daily_completed` INT DEFAULT 0 COMMENT 'Daily completed count',
ADD COLUMN IF NOT EXISTS `t_daily_rejected` INT DEFAULT 0 COMMENT 'Daily rejected count',
ADD COLUMN IF NOT EXISTS `t_last_reset_date` DATE DEFAULT NULL COMMENT 'Last daily reset date';

-- ============================================================================
-- 2. UPDATE SERVICE BOOKING TABLE - Add all required fields
-- ============================================================================

ALTER TABLE `tms_service_booking`
ADD COLUMN IF NOT EXISTS `sb_gadget_type` VARCHAR(200) DEFAULT NULL COMMENT 'Type of gadget',
ADD COLUMN IF NOT EXISTS `sb_work_type` VARCHAR(200) DEFAULT NULL COMMENT 'Type of work required',
ADD COLUMN IF NOT EXISTS `sb_is_guest` TINYINT(1) DEFAULT 0 COMMENT '1=Guest, 0=Registered',
ADD COLUMN IF NOT EXISTS `sb_guest_name` VARCHAR(200) DEFAULT NULL COMMENT 'Guest user name',
ADD COLUMN IF NOT EXISTS `sb_guest_email` VARCHAR(200) DEFAULT NULL COMMENT 'Guest user email',
ADD COLUMN IF NOT EXISTS `sb_assigned_by` INT DEFAULT NULL COMMENT 'Admin ID who assigned',
ADD COLUMN IF NOT EXISTS `sb_assigned_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Assignment timestamp',
ADD COLUMN IF NOT EXISTS `sb_accepted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Acceptance timestamp',
ADD COLUMN IF NOT EXISTS `sb_rejected_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Rejection timestamp',
ADD COLUMN IF NOT EXISTS `sb_completed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Completion timestamp',
ADD COLUMN IF NOT EXISTS `sb_cancelled_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Cancellation timestamp',
ADD COLUMN IF NOT EXISTS `sb_cancelled_by` VARCHAR(50) DEFAULT NULL COMMENT 'Who cancelled (admin/user/technician)',
ADD COLUMN IF NOT EXISTS `sb_rejection_reason` TEXT DEFAULT NULL COMMENT 'Reason for rejection',
ADD COLUMN IF NOT EXISTS `sb_completion_notes` TEXT DEFAULT NULL COMMENT 'Completion notes',
ADD COLUMN IF NOT EXISTS `sb_completion_image` VARCHAR(500) DEFAULT NULL COMMENT 'Completion proof image',
ADD COLUMN IF NOT EXISTS `sb_can_user_cancel` TINYINT(1) DEFAULT 1 COMMENT '1=Can cancel, 0=Cannot cancel',
ADD COLUMN IF NOT EXISTS `sb_previous_technician_id` INT DEFAULT NULL COMMENT 'Previous technician if reassigned',
ADD COLUMN IF NOT EXISTS `sb_reassignment_count` INT DEFAULT 0 COMMENT 'Number of times reassigned',
ADD COLUMN IF NOT EXISTS `sb_admin_notes` TEXT DEFAULT NULL COMMENT 'Admin notes';

-- Update status column to handle all states
ALTER TABLE `tms_service_booking` 
MODIFY COLUMN `sb_status` VARCHAR(200) NOT NULL DEFAULT 'Pending' 
COMMENT 'Pending/Approved/Rejected by Technician/Completed/Cancelled';

-- ============================================================================
-- 3. CREATE BOOKING HISTORY TABLE - Track all status changes
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_booking_history` (
  `bh_id` INT NOT NULL AUTO_INCREMENT,
  `bh_booking_id` INT NOT NULL,
  `bh_old_status` VARCHAR(200) DEFAULT NULL,
  `bh_new_status` VARCHAR(200) NOT NULL,
  `bh_old_technician_id` INT DEFAULT NULL,
  `bh_new_technician_id` INT DEFAULT NULL,
  `bh_changed_by` VARCHAR(100) NOT NULL COMMENT 'admin/user/technician',
  `bh_changed_by_id` INT DEFAULT NULL,
  `bh_notes` TEXT DEFAULT NULL,
  `bh_created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bh_id`),
  KEY `bh_booking_id` (`bh_booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Booking status change history';

-- ============================================================================
-- 4. CREATE ADMIN NOTIFICATIONS TABLE - Real-time notifications
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_admin_notifications` (
  `an_id` INT NOT NULL AUTO_INCREMENT,
  `an_type` VARCHAR(100) NOT NULL COMMENT 'new_booking/booking_accepted/booking_rejected/booking_completed',
  `an_booking_id` INT NOT NULL,
  `an_title` VARCHAR(500) NOT NULL,
  `an_message` TEXT NOT NULL,
  `an_is_read` TINYINT(1) DEFAULT 0,
  `an_is_sound_played` TINYINT(1) DEFAULT 0,
  `an_created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`an_id`),
  KEY `an_booking_id` (`an_booking_id`),
  KEY `an_is_read` (`an_is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Admin notifications';

-- ============================================================================
-- 5. CREATE TECHNICIAN NOTIFICATIONS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_technician_notifications` (
  `tn_id` INT NOT NULL AUTO_INCREMENT,
  `tn_technician_id` INT NOT NULL,
  `tn_type` VARCHAR(100) NOT NULL COMMENT 'new_assignment/booking_cancelled/booking_reassigned',
  `tn_booking_id` INT NOT NULL,
  `tn_title` VARCHAR(500) NOT NULL,
  `tn_message` TEXT NOT NULL,
  `tn_is_read` TINYINT(1) DEFAULT 0,
  `tn_created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tn_id`),
  KEY `tn_technician_id` (`tn_technician_id`),
  KEY `tn_booking_id` (`tn_booking_id`),
  KEY `tn_is_read` (`tn_is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Technician notifications';

-- ============================================================================
-- 6. CREATE USER NOTIFICATIONS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_user_notifications` (
  `un_id` INT NOT NULL AUTO_INCREMENT,
  `un_user_id` INT DEFAULT NULL COMMENT 'NULL for guest users',
  `un_booking_id` INT NOT NULL,
  `un_type` VARCHAR(100) NOT NULL COMMENT 'booking_confirmed/technician_assigned/booking_completed/booking_rejected',
  `un_title` VARCHAR(500) NOT NULL,
  `un_message` TEXT NOT NULL,
  `un_is_read` TINYINT(1) DEFAULT 0,
  `un_created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`un_id`),
  KEY `un_user_id` (`un_user_id`),
  KEY `un_booking_id` (`un_booking_id`),
  KEY `un_is_read` (`un_is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User notifications';

-- ============================================================================
-- 7. CREATE DAILY TECHNICIAN STATS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_technician_daily_stats` (
  `tds_id` INT NOT NULL AUTO_INCREMENT,
  `tds_technician_id` INT NOT NULL,
  `tds_date` DATE NOT NULL,
  `tds_assigned_count` INT DEFAULT 0,
  `tds_completed_count` INT DEFAULT 0,
  `tds_rejected_count` INT DEFAULT 0,
  `tds_active_count` INT DEFAULT 0,
  `tds_created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tds_updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tds_id`),
  UNIQUE KEY `unique_tech_date` (`tds_technician_id`, `tds_date`),
  KEY `tds_date` (`tds_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Daily technician statistics';

-- ============================================================================
-- 8. CREATE GUEST USERS TABLE - Permanent storage
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_guest_users` (
  `gu_id` INT NOT NULL AUTO_INCREMENT,
  `gu_name` VARCHAR(200) NOT NULL,
  `gu_email` VARCHAR(200) DEFAULT NULL,
  `gu_phone` VARCHAR(200) NOT NULL,
  `gu_address` VARCHAR(500) NOT NULL,
  `gu_first_booking_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gu_last_booking_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gu_total_bookings` INT DEFAULT 1,
  PRIMARY KEY (`gu_id`),
  KEY `gu_phone` (`gu_phone`),
  KEY `gu_email` (`gu_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Permanent guest user records';

-- ============================================================================
-- 9. UPDATE EXISTING TECHNICIANS - Set default values
-- ============================================================================

UPDATE `tms_technician` 
SET 
  `t_booking_limit` = 5,
  `t_current_bookings` = 0,
  `t_daily_assigned` = 0,
  `t_daily_completed` = 0,
  `t_daily_rejected` = 0,
  `t_last_reset_date` = CURDATE()
WHERE `t_booking_limit` IS NULL OR `t_booking_limit` = 0;

-- ============================================================================
-- 10. CREATE STORED PROCEDURES
-- ============================================================================

-- Procedure to check if technician can accept new booking
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_can_assign_to_technician`$$
CREATE PROCEDURE `sp_can_assign_to_technician`(
  IN p_technician_id INT,
  OUT p_can_assign BOOLEAN,
  OUT p_message VARCHAR(500)
)
BEGIN
  DECLARE v_current_bookings INT;
  DECLARE v_booking_limit INT;
  
  -- Get technician's current bookings and limit
  SELECT t_current_bookings, t_booking_limit
  INTO v_current_bookings, v_booking_limit
  FROM tms_technician
  WHERE t_id = p_technician_id;
  
  -- Check if can assign
  IF v_current_bookings < v_booking_limit THEN
    SET p_can_assign = TRUE;
    SET p_message = CONCAT('Can assign. Current: ', v_current_bookings, '/', v_booking_limit);
  ELSE
    SET p_can_assign = FALSE;
    SET p_message = CONCAT('Cannot assign. Limit reached: ', v_current_bookings, '/', v_booking_limit);
  END IF;
END$$

-- Procedure to update technician booking count
DROP PROCEDURE IF EXISTS `sp_update_technician_count`$$
CREATE PROCEDURE `sp_update_technician_count`(
  IN p_technician_id INT,
  IN p_action VARCHAR(50)
)
BEGIN
  IF p_action = 'increment' THEN
    UPDATE tms_technician 
    SET t_current_bookings = t_current_bookings + 1,
        t_daily_assigned = t_daily_assigned + 1
    WHERE t_id = p_technician_id;
  ELSEIF p_action = 'decrement' THEN
    UPDATE tms_technician 
    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
    WHERE t_id = p_technician_id;
  END IF;
END$$

-- Procedure to reset daily stats
DROP PROCEDURE IF EXISTS `sp_reset_daily_stats`$$
CREATE PROCEDURE `sp_reset_daily_stats`()
BEGIN
  UPDATE tms_technician
  SET t_daily_assigned = 0,
      t_daily_completed = 0,
      t_daily_rejected = 0,
      t_last_reset_date = CURDATE()
  WHERE t_last_reset_date < CURDATE() OR t_last_reset_date IS NULL;
END$$

DELIMITER ;

-- ============================================================================
-- 11. CREATE TRIGGERS
-- ============================================================================

-- Trigger: After booking assignment
DELIMITER $$

DROP TRIGGER IF EXISTS `trg_after_booking_assign`$$
CREATE TRIGGER `trg_after_booking_assign`
AFTER UPDATE ON `tms_service_booking`
FOR EACH ROW
BEGIN
  -- If technician assigned and status changed to Approved
  IF NEW.sb_technician_id IS NOT NULL 
     AND OLD.sb_technician_id IS NULL 
     AND NEW.sb_status = 'Approved' THEN
    
    -- Increment technician count
    UPDATE tms_technician 
    SET t_current_bookings = t_current_bookings + 1,
        t_daily_assigned = t_daily_assigned + 1
    WHERE t_id = NEW.sb_technician_id;
    
    -- Create admin notification
    INSERT INTO tms_admin_notifications (an_type, an_booking_id, an_title, an_message)
    VALUES ('booking_assigned', NEW.sb_id, 'Booking Assigned', 
            CONCAT('Booking #', NEW.sb_id, ' assigned to technician'));
    
    -- Create technician notification
    INSERT INTO tms_technician_notifications (tn_technician_id, tn_type, tn_booking_id, tn_title, tn_message)
    VALUES (NEW.sb_technician_id, 'new_assignment', NEW.sb_id, 'New Booking Assigned',
            CONCAT('You have been assigned booking #', NEW.sb_id));
  END IF;
  
  -- If technician changed (reassignment)
  IF NEW.sb_technician_id != OLD.sb_technician_id 
     AND OLD.sb_technician_id IS NOT NULL 
     AND NEW.sb_technician_id IS NOT NULL THEN
    
    -- Decrement old technician count
    UPDATE tms_technician 
    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
    WHERE t_id = OLD.sb_technician_id;
    
    -- Increment new technician count
    UPDATE tms_technician 
    SET t_current_bookings = t_current_bookings + 1,
        t_daily_assigned = t_daily_assigned + 1
    WHERE t_id = NEW.sb_technician_id;
  END IF;
  
  -- If booking completed
  IF NEW.sb_status = 'Completed' AND OLD.sb_status != 'Completed' THEN
    -- Decrement technician count and increment completed
    UPDATE tms_technician 
    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0),
        t_daily_completed = t_daily_completed + 1
    WHERE t_id = NEW.sb_technician_id;
  END IF;
  
  -- If booking rejected
  IF NEW.sb_status = 'Rejected by Technician' AND OLD.sb_status != 'Rejected by Technician' THEN
    -- Decrement technician count and increment rejected
    UPDATE tms_technician 
    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0),
        t_daily_rejected = t_daily_rejected + 1
    WHERE t_id = NEW.sb_technician_id;
  END IF;
END$$

DELIMITER ;

-- ============================================================================
-- 12. CREATE INDEXES FOR PERFORMANCE
-- ============================================================================

ALTER TABLE `tms_service_booking` 
ADD INDEX IF NOT EXISTS `idx_status` (`sb_status`),
ADD INDEX IF NOT EXISTS `idx_technician_status` (`sb_technician_id`, `sb_status`),
ADD INDEX IF NOT EXISTS `idx_user_status` (`sb_user_id`, `sb_status`),
ADD INDEX IF NOT EXISTS `idx_created_at` (`sb_created_at`),
ADD INDEX IF NOT EXISTS `idx_booking_date` (`sb_booking_date`);

-- ============================================================================
-- 13. INSERT DEFAULT ADMIN SETTINGS
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tms_settings` (
  `setting_id` INT NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `setting_description` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tms_settings` (`setting_key`, `setting_value`, `setting_description`) VALUES
('default_booking_limit', '5', 'Default booking limit for new technicians'),
('enable_sound_alerts', '1', 'Enable sound alerts for admin (1=Yes, 0=No)'),
('enable_guest_booking', '1', 'Allow guest users to book (1=Yes, 0=No)'),
('auto_approve_bookings', '0', 'Auto approve bookings without admin review (1=Yes, 0=No)'),
('notification_retention_days', '30', 'Days to keep old notifications')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================================================
-- COMPLETE! Database structure updated successfully
-- ============================================================================

SELECT 'Database update completed successfully!' AS Status;
