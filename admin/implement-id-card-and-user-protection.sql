-- SQL Implementation for ID Card Auto-Save and User Data Protection
-- Run this in phpMyAdmin

USE `electrozot_db`;

-- 1. Create table to store generated ID cards
CREATE TABLE IF NOT EXISTS `tms_generated_id_cards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `technician_id` int NOT NULL,
  `technician_name` varchar(200) NOT NULL,
  `technician_phone` varchar(20) NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `pdf_path` varchar(500) DEFAULT NULL,
  `generated_by_admin_id` int NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_to_whatsapp` tinyint(1) DEFAULT 0,
  `whatsapp_sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technician_id` (`technician_id`),
  KEY `generated_by_admin_id` (`generated_by_admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Stores all generated ID cards for technicians';

-- 2. Add column to technician table to track if ID card was generated
ALTER TABLE `tms_technician` 
ADD COLUMN IF NOT EXISTS `t_id_card_generated` tinyint(1) DEFAULT 0 COMMENT 'Whether ID card has been generated',
ADD COLUMN IF NOT EXISTS `t_id_card_path` varchar(500) DEFAULT NULL COMMENT 'Path to latest ID card',
ADD COLUMN IF NOT EXISTS `t_id_card_generated_at` timestamp NULL DEFAULT NULL COMMENT 'When ID card was last generated';

-- 3. Ensure user data protection - Add is_deleted flag if not exists
ALTER TABLE `tms_user` 
ADD COLUMN IF NOT EXISTS `u_is_deleted` tinyint(1) DEFAULT 0 COMMENT 'Soft delete flag - 1 means deleted',
ADD COLUMN IF NOT EXISTS `u_deleted_at` timestamp NULL DEFAULT NULL COMMENT 'When user was soft deleted',
ADD COLUMN IF NOT EXISTS `u_deleted_by` int DEFAULT NULL COMMENT 'Admin ID who deleted the user';

-- 4. Create index for better performance on deleted users
CREATE INDEX IF NOT EXISTS `idx_user_deleted` ON `tms_user` (`u_is_deleted`);

-- 5. Create trigger to prevent hard delete of users (optional safety measure)
DELIMITER $$

DROP TRIGGER IF EXISTS `prevent_user_hard_delete`$$

CREATE TRIGGER `prevent_user_hard_delete`
BEFORE DELETE ON `tms_user`
FOR EACH ROW
BEGIN
    -- Log the attempt
    INSERT INTO tms_system_logs (log_type, log_message, log_data, created_at)
    VALUES ('USER_DELETE_ATTEMPT', 
            CONCAT('Attempted to hard delete user: ', OLD.u_fname, ' ', OLD.u_lname),
            CONCAT('User ID: ', OLD.u_id, ', Email: ', OLD.u_email),
            NOW());
    
    -- Prevent the delete by signaling an error
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Hard delete not allowed. Use soft delete instead.';
END$$

DELIMITER ;

-- 6. Create system logs table if it doesn't exist (for the trigger)
CREATE TABLE IF NOT EXISTS `tms_system_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `log_type` varchar(100) NOT NULL,
  `log_message` text,
  `log_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `log_type` (`log_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='System activity logs';

-- Verify tables were created
SELECT 'ID Cards Table' as 'Table', COUNT(*) as 'Records' FROM tms_generated_id_cards
UNION ALL
SELECT 'System Logs Table', COUNT(*) FROM tms_system_logs;
