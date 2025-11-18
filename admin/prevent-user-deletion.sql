-- SQL to PERMANENTLY PREVENT USER DELETION
-- This adds database-level protection so users cannot be deleted from anywhere
-- Run this in phpMyAdmin

USE `electrozot_db`;

-- 1. Create system logs table if not exists
CREATE TABLE IF NOT EXISTS `tms_system_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `log_type` varchar(100) NOT NULL,
  `log_message` text,
  `log_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `log_type` (`log_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='System activity and security logs';

-- 2. Add protection flag to user table
ALTER TABLE `tms_user` 
ADD COLUMN IF NOT EXISTS `u_deletion_protected` tinyint(1) DEFAULT 1 COMMENT 'Protection flag - 1 means cannot be deleted',
ADD COLUMN IF NOT EXISTS `u_registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When user was registered';

-- 3. Update all existing users to be protected
UPDATE `tms_user` SET `u_deletion_protected` = 1 WHERE `u_deletion_protected` IS NULL OR `u_deletion_protected` = 0;

-- 4. Create trigger to PREVENT any DELETE on tms_user table
DELIMITER $$

DROP TRIGGER IF EXISTS `block_user_deletion`$$

CREATE TRIGGER `block_user_deletion`
BEFORE DELETE ON `tms_user`
FOR EACH ROW
BEGIN
    -- Log the deletion attempt with full details
    INSERT INTO tms_system_logs (log_type, log_message, log_data, created_at)
    VALUES (
        'USER_DELETE_BLOCKED_BY_TRIGGER', 
        CONCAT('BLOCKED: Attempted to delete user - ', OLD.u_fname, ' ', OLD.u_lname),
        CONCAT('User ID: ', OLD.u_id, ', Email: ', OLD.u_email, ', Phone: ', OLD.u_phone, ', Registered: ', OLD.u_registered_at),
        NOW()
    );
    
    -- PREVENT the delete by signaling an error
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = '❌ USER DELETION BLOCKED: Users cannot be deleted once registered. This is for data integrity and compliance. User data is permanently protected.';
END$$

DELIMITER ;

-- 5. Create trigger to prevent UPDATE that tries to mark user as deleted
DELIMITER $$

DROP TRIGGER IF EXISTS `block_user_soft_delete`$$

CREATE TRIGGER `block_user_soft_delete`
BEFORE UPDATE ON `tms_user`
FOR EACH ROW
BEGIN
    -- If someone tries to set u_is_deleted = 1, block it
    IF NEW.u_is_deleted = 1 AND OLD.u_is_deleted = 0 THEN
        -- Log the attempt
        INSERT INTO tms_system_logs (log_type, log_message, log_data, created_at)
        VALUES (
            'USER_SOFT_DELETE_BLOCKED', 
            CONCAT('BLOCKED: Attempted to soft-delete user - ', OLD.u_fname, ' ', OLD.u_lname),
            CONCAT('User ID: ', OLD.u_id, ', Email: ', OLD.u_email),
            NOW()
        );
        
        -- Prevent the soft delete
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = '❌ USER SOFT DELETE BLOCKED: Users cannot be marked as deleted. User data is permanently protected.';
    END IF;
END$$

DELIMITER ;

-- 6. Create a view for active users (for queries that need to filter)
CREATE OR REPLACE VIEW `v_active_users` AS
SELECT * FROM `tms_user` WHERE `u_deletion_protected` = 1;

-- 7. Add index for better performance
CREATE INDEX IF NOT EXISTS `idx_user_protected` ON `tms_user` (`u_deletion_protected`);

-- 8. Insert initial log entry
INSERT INTO tms_system_logs (log_type, log_message, log_data)
VALUES (
    'USER_PROTECTION_ENABLED',
    'User deletion protection has been permanently enabled',
    CONCAT('Total protected users: ', (SELECT COUNT(*) FROM tms_user), ', Timestamp: ', NOW())
);

-- Verify the protection is active
SELECT 
    'User Protection Status' as 'Feature',
    'ENABLED' as 'Status',
    COUNT(*) as 'Protected Users',
    NOW() as 'Activated At'
FROM tms_user
WHERE u_deletion_protected = 1;

-- Show recent logs
SELECT * FROM tms_system_logs 
WHERE log_type LIKE '%USER%' 
ORDER BY created_at DESC 
LIMIT 5;
