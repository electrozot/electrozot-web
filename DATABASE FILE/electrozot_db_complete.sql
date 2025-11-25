-- ============================================================================
-- ELECTROZOT DATABASE - COMPLETE CONSOLIDATED VERSION
-- ============================================================================
-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 27, 2023 at 10:00 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10
--
-- This file contains:
-- 1. Core database structure and tables
-- 2. All feature enhancements and modifications
-- 3. Triggers and views for automation
-- 4. Sample data for testing
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- DATABASE CREATION
-- ============================================================================

-- CREATE DATABASE IF NOT EXISTS `electrozot_db`;
-- USE `electrozot_db`;

-- ============================================================================
-- CORE TABLES
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: tms_admin
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_admin` (
  `a_id` int NOT NULL AUTO_INCREMENT,
  `a_name` varchar(200) NOT NULL,
  `a_email` varchar(200) NOT NULL,
  `a_pwd` varchar(200) NOT NULL,
  `a_photo` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Admin profile photo',
  `a_phone` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Admin phone number',
  `a_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation time',
  PRIMARY KEY (`a_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample admin data
-- Default credentials: admin@electrozot.in / admin123
INSERT INTO `tms_admin` (`a_id`, `a_name`, `a_email`, `a_pwd`, `a_photo`, `a_phone`) VALUES
(4, 'Admin User', 'admin@electrozot.in', '0192023a7bbd73250516f069df18b500',"null" ,'9876543210');

-- ----------------------------------------------------------------------------
-- Table: tms_user
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_user` (
  `u_id` int NOT NULL AUTO_INCREMENT,
  `u_fname` varchar(200) NOT NULL,
  `u_lname` varchar(200) NOT NULL,
  `u_phone` varchar(200) NOT NULL,
  `u_addr` varchar(200) NOT NULL,
  `u_area` varchar(100) DEFAULT NULL COMMENT 'User area/locality',
  `u_pincode` varchar(10) DEFAULT NULL COMMENT 'User pincode',
  `u_category` varchar(200) NOT NULL,
  `u_email` varchar(200) NOT NULL,
  `u_pwd` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `t_tech_category` varchar(200) NOT NULL,
  `t_tech_id` varchar(200) NOT NULL,
  `t_booking_date` varchar(200) NOT NULL,
  `t_booking_status` varchar(200) NOT NULL,
  `u_is_deleted` tinyint(1) DEFAULT 0 COMMENT 'Soft delete flag - 1 means deleted',
  `u_deleted_at` timestamp NULL DEFAULT NULL COMMENT 'When user was soft deleted',
  `u_deleted_by` int DEFAULT NULL COMMENT 'Admin ID who deleted the user',
  `u_deletion_protected` tinyint(1) DEFAULT 1 COMMENT 'Protection flag - 1 means cannot be deleted',
  `u_registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When user was registered',
  `registration_type` enum('admin','self','guest') DEFAULT 'admin' COMMENT 'How user was registered',
  PRIMARY KEY (`u_id`),
  INDEX `idx_user_deleted` (`u_is_deleted`),
  INDEX `idx_user_protected` (`u_deletion_protected`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample user data
INSERT INTO `tms_user` (`u_id`, `u_fname`, `u_lname`, `u_phone`, `u_addr`, `u_category`, `u_email`, `u_pwd`, `t_tech_category`, `t_tech_id`, `t_booking_date`, `t_booking_status`) VALUES
(13, 'Clint', '01', '01600000000', 'Bogura,Bangladesh', 'User', 'clint@gmail.com', '123456', '', '', '', '');

-- ----------------------------------------------------------------------------
-- Table: tms_technician
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_technician` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `t_name` varchar(200) NOT NULL,
  `t_id_no` varchar(200) NOT NULL,
  `t_phone` varchar(20) DEFAULT NULL,
  `t_aadhar` varchar(12) DEFAULT NULL COMMENT 'Aadhaar number for verification',
  `t_ez_id` varchar(20) DEFAULT NULL COMMENT 'Electrozot unique ID',
  `t_email` varchar(200) DEFAULT NULL,
  `t_addr` varchar(500) DEFAULT NULL COMMENT 'Technician address',
  `t_pwd` varchar(255) DEFAULT NULL COMMENT 'Technician login password',
  `t_experience` varchar(200) NOT NULL,
  `t_specialization` varchar(200) NOT NULL,
  `t_category` varchar(200) NOT NULL,
  `t_pic` varchar(200) NOT NULL,
  `t_service_pincode` varchar(20) DEFAULT '' COMMENT 'Service area pincode',
  `t_status` VARCHAR(20) DEFAULT 'Available' COMMENT 'Technician status: Available, Busy, Offline',
  `t_is_available` TINYINT(1) DEFAULT 1 COMMENT 'Whether technician is available (1) or not (0)',
  `t_is_guest` TINYINT(1) DEFAULT 0 COMMENT 'Whether technician is a guest (1) or permanent (0)',
  `t_booking_limit` INT NOT NULL DEFAULT 5 COMMENT 'Maximum concurrent bookings (1-5)',
  `t_current_bookings` INT NOT NULL DEFAULT 0 COMMENT 'Current active bookings count',
  `t_skills` TEXT DEFAULT NULL COMMENT 'Comma-separated list of detailed service skills',
  `t_id_card_generated` tinyint(1) DEFAULT 0 COMMENT 'Whether ID card has been generated',
  `t_id_card_path` varchar(500) DEFAULT NULL COMMENT 'Path to latest ID card',
  `t_id_card_generated_at` timestamp NULL DEFAULT NULL COMMENT 'When ID card was last generated',
  `t_registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When technician was registered',
  PRIMARY KEY (`t_id`),
  UNIQUE KEY `idx_ez_id` (`t_ez_id`),
  UNIQUE KEY `idx_aadhar` (`t_aadhar`)now recheck the king of set a,
  INDEX `idx_category` (`t_category`),
  INDEX `idx_status` (`t_status`),
  INDEX `idx_phone` (`t_phone`),
  FULLTEXT INDEX `idx_skills` (`t_skills`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample technician data
INSERT INTO `tms_technician` (`t_id`, `t_name`, `t_id_no`, `t_experience`, `t_specialization`, `t_category`, `t_pic`, `t_booking_limit`, `t_current_bookings`) VALUES
(3, 'John Smith', 'TECH001', '10', 'Electrical Repairs', 'Electrical', 'tech1.jpg', 5, 0),
(4, 'Sarah Johnson', 'TECH002', '8', 'Plumbing Services', 'Plumbing', 'tech2.jpg', 5, 0),
(5, 'Mike Williams', 'TECH003', '12', 'HVAC Systems', 'HVAC', 'tech3.jpg', 5, 0),
(6, 'Emily Davis', 'TECH004', '5', 'Appliance Repair', 'Appliance', 'tech4.jpg', 5, 0),
(7, 'David Brown', 'TECH005', '15', 'General Maintenance', 'General', 'tech5.jpg', 5, 0);

-- ----------------------------------------------------------------------------
-- Table: tms_service
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_service` (
  `s_id` int NOT NULL AUTO_INCREMENT,
  `s_name` varchar(200) NOT NULL,
  `s_description` longtext NOT NULL,
  `s_category` varchar(200) NOT NULL,
  `s_price` decimal(10,2) NOT NULL,
  `s_duration` varchar(200) NOT NULL,
  `s_status` varchar(200) NOT NULL DEFAULT 'Active',
  `s_admin_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Admin-set fixed price. If NULL, technician sets price',
  `is_popular` tinyint(1) DEFAULT 0 COMMENT 'Whether service is marked as popular',
  `s_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`s_id`),
  INDEX `idx_category` (`s_category`),
  INDEX `idx_status` (`s_status`),
  INDEX `idx_popular` (`is_popular`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample service data
INSERT INTO `tms_service` (`s_id`, `s_name`, `s_description`, `s_category`, `s_price`, `s_duration`, `s_status`) VALUES
(1, 'Electrical Repair', 'Complete electrical system repair and maintenance', 'Electrical', 150.00, '2-3 hours', 'Active'),
(2, 'Plumbing Service', 'Professional plumbing installation and repair', 'Plumbing', 120.00, '1-2 hours', 'Active'),
(3, 'HVAC Maintenance', 'Heating, ventilation and air conditioning service', 'HVAC', 200.00, '3-4 hours', 'Active'),
(4, 'Appliance Repair', 'Home appliance repair and maintenance', 'Appliance', 100.00, '1-2 hours', 'Active'),
(5, 'General Maintenance', 'General home maintenance and repairs', 'General', 80.00, '1-2 hours', 'Active');

-- ----------------------------------------------------------------------------
-- Table: tms_service_booking
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_service_booking` (
  `sb_id` int NOT NULL AUTO_INCREMENT,
  `sb_user_id` int NOT NULL,
  `sb_service_id` int DEFAULT NULL COMMENT 'Service ID - NULL for custom services',
  `sb_technician_id` int DEFAULT NULL,
  `sb_service_name` VARCHAR(255) DEFAULT NULL,
  `sb_category` VARCHAR(100) DEFAULT NULL,
  `sb_subcategory` VARCHAR(100) DEFAULT NULL,
  `sb_booking_date` date NOT NULL,
  `sb_date` date DEFAULT NULL,
  `sb_booking_time` time NOT NULL,
  `sb_time` time DEFAULT NULL,
  `sb_address` varchar(500) NOT NULL,
  `sb_pincode` varchar(10) DEFAULT NULL COMMENT 'Booking location pincode',
  `sb_phone` varchar(200) NOT NULL,
  `sb_description` longtext,
  `sb_status` varchar(200) NOT NULL DEFAULT 'Pending',
  `sb_total_price` decimal(10,2) NOT NULL,
  `sb_final_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Final price charged for the service',
  `sb_tech_decided_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Price decided by technician (only for this booking)',
  `sb_price_set_by_tech` TINYINT(1) DEFAULT 0 COMMENT 'Whether final price was set by technician (1) or admin (0)',
  `sb_custom_service` varchar(255) DEFAULT NULL COMMENT 'Custom service name for other services',
  `sb_rejection_reason` TEXT NULL DEFAULT NULL COMMENT 'Reason for rejection',
  `sb_not_done_reason` TEXT NULL DEFAULT NULL COMMENT 'Reason why service was not completed',
  `sb_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sb_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sb_assigned_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When technician was assigned',
  `sb_rejected_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When booking was rejected',
  `sb_completed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When booking was completed',
  `sb_cancelled_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When booking was cancelled',
  `sb_cancelled_by` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Who cancelled: user/admin/system',
  `sb_not_done_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When service was marked as not done',
  PRIMARY KEY (`sb_id`),
  KEY `sb_user_id` (`sb_user_id`),
  KEY `sb_service_id` (`sb_service_id`),
  KEY `sb_technician_id` (`sb_technician_id`),
  INDEX `idx_status` (`sb_status`),
  INDEX `idx_technician` (`sb_technician_id`),
  INDEX `idx_user` (`sb_user_id`),
  INDEX `idx_date` (`sb_date`),
  INDEX `idx_created` (`sb_created_at`),
  INDEX `idx_rejected` (`sb_rejected_at`),
  INDEX `idx_completed` (`sb_completed_at`),
  INDEX `idx_booking_technician_status` (`sb_technician_id`, `sb_status`),
  CONSTRAINT `tms_service_booking_ibfk_1` FOREIGN KEY (`sb_user_id`) REFERENCES `tms_user` (`u_id`) ON DELETE CASCADE,
  CONSTRAINT `tms_service_booking_ibfk_2` FOREIGN KEY (`sb_service_id`) REFERENCES `tms_service` (`s_id`) ON DELETE CASCADE,
  CONSTRAINT `tms_service_booking_ibfk_3` FOREIGN KEY (`sb_technician_id`) REFERENCES `tms_technician` (`t_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- Table: tms_feedback
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_feedback` (
  `f_id` int NOT NULL AUTO_INCREMENT,
  `f_uname` varchar(200) NOT NULL,
  `f_content` longtext NOT NULL,
  `f_status` varchar(200) NOT NULL,
  PRIMARY KEY (`f_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample feedback data
INSERT INTO `tms_feedback` (`f_id`, `f_uname`, `f_content`, `f_status`) VALUES
(1, 'Elliot Gape', 'This is a demo feedback text. This is a demo feedback text. This is a demo feedback text.', 'Published'),
(2, 'Mark L. Anderson', 'Sample Feedback Text for testing! Sample Feedback Text for testing! Sample Feedback Text for testing!', 'Published'),
(3, 'Liam Moore ', 'test number 3', '');

-- ----------------------------------------------------------------------------
-- Table: tms_pwd_resets
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_pwd_resets` (
  `r_id` int NOT NULL AUTO_INCREMENT,
  `r_email` varchar(200) NOT NULL,
  PRIMARY KEY (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample password reset data
INSERT INTO `tms_pwd_resets` (`r_id`, `r_email`) VALUES
(2, 'admin@gmail.com');

-- ----------------------------------------------------------------------------
-- Table: tms_syslogs
-- ----------------------------------------------------------------------------
CREATE TABLE `tms_syslogs` (
  `l_id` int NOT NULL AUTO_INCREMENT,
  `u_id` varchar(200) NOT NULL,
  `u_email` varchar(200) NOT NULL,
  `u_ip` varbinary(200) NOT NULL,
  `u_city` varchar(200) NOT NULL,
  `u_country` varchar(200) NOT NULL,
  `u_logintime` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `log_type` VARCHAR(50) DEFAULT 'login' COMMENT 'Type of log entry',
  `user_type` VARCHAR(50) DEFAULT 'admin' COMMENT 'Type of user: admin, technician, user',
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- FEATURE TABLES
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: tms_site_settings
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tms_site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_label` varchar(200) NOT NULL,
  `setting_type` varchar(50) NOT NULL DEFAULT 'text',
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `display_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default site settings
INSERT INTO `tms_site_settings` (`setting_key`, `setting_value`, `setting_label`, `setting_type`, `setting_group`, `display_order`) VALUES
('site_name', 'Electrozot', 'Site Name', 'text', 'general', 1),
('site_tagline', 'We Make Perfect', 'Site Tagline', 'text', 'general', 2),
('business_address', 'Your Business Address Here', 'Business Address', 'textarea', 'contact', 3),
('primary_phone', '7559606925', 'Primary Phone', 'tel', 'contact', 4),
('secondary_phone', '', 'Secondary Phone', 'tel', 'contact', 5),
('whatsapp_number', '7559606925', 'WhatsApp Number', 'tel', 'contact', 6),
('primary_email', 'info@electrozot.com', 'Primary Email', 'email', 'contact', 7),
('support_email', 'support@electrozot.com', 'Support Email', 'email', 'contact', 8),
('facebook_url', '', 'Facebook URL', 'url', 'social', 9),
('instagram_url', '', 'Instagram URL', 'url', 'social', 10),
('twitter_url', '', 'Twitter URL', 'url', 'social', 11),
('linkedin_url', '', 'LinkedIn URL', 'url', 'social', 12);

-- ----------------------------------------------------------------------------
-- Table: tms_generated_id_cards
-- ----------------------------------------------------------------------------
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

-- ----------------------------------------------------------------------------
-- Table: tms_system_logs
-- ----------------------------------------------------------------------------
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

-- ----------------------------------------------------------------------------
-- Table: tms_admin_notifications
-- ----------------------------------------------------------------------------
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Admin notification system for booking events';

-- ----------------------------------------------------------------------------
-- Table: tms_home_slider
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tms_home_slider` (
  `slider_id` int NOT NULL AUTO_INCREMENT,
  `slider_image` varchar(255) NOT NULL,
  `slider_title` varchar(255) NOT NULL,
  `slider_description` text,
  `slider_order` int DEFAULT 0,
  `slider_status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`slider_id`),
  INDEX `idx_status` (`slider_status`),
  INDEX `idx_order` (`slider_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Home page slider/carousel images';

-- Sample slider data
INSERT INTO `tms_home_slider` (`slider_image`, `slider_title`, `slider_description`, `slider_order`, `slider_status`) VALUES
('default-slider-1.jpg', 'Welcome to Electrozot', 'Professional electrical and maintenance services at your doorstep', 1, 'Active'),
('default-slider-2.jpg', 'Expert Technicians', 'Certified and experienced professionals ready to serve you', 2, 'Active'),
('default-slider-3.jpg', '24/7 Support', 'Round the clock customer support for all your service needs', 3, 'Active');

-- ----------------------------------------------------------------------------
-- Table: tms_gallery
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tms_gallery` (
  `g_id` int NOT NULL AUTO_INCREMENT,
  `g_title` varchar(255) NOT NULL,
  `g_image` varchar(255) NOT NULL,
  `g_service_id` int DEFAULT NULL,
  `g_description` text,
  `g_status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`g_id`),
  KEY `g_service_id` (`g_service_id`),
  INDEX `idx_status` (`g_status`),
  CONSTRAINT `tms_gallery_ibfk_1` FOREIGN KEY (`g_service_id`) REFERENCES `tms_service` (`s_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Gallery images for showcasing work';

-- ============================================================================
-- VIEWS
-- ============================================================================

-- ----------------------------------------------------------------------------
-- View: v_active_users
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_active_users` AS
SELECT * FROM `tms_user` WHERE `u_deletion_protected` = 1;

-- ----------------------------------------------------------------------------
-- View: v_technician_availability
-- ----------------------------------------------------------------------------
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

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Trigger: Log User Deletion (Admin can delete with password)
-- ----------------------------------------------------------------------------
DELIMITER $

DROP TRIGGER IF EXISTS `block_user_deletion`$

CREATE TRIGGER `log_user_deletion`
BEFORE DELETE ON `tms_user`
FOR EACH ROW
BEGIN
    -- Log the deletion with full details
    INSERT INTO tms_system_logs (log_type, log_message, log_data, created_at)
    VALUES (
        'USER_DELETED', 
        CONCAT('User deleted by admin - ', OLD.u_fname, ' ', OLD.u_lname),
        CONCAT('User ID: ', OLD.u_id, ', Email: ', OLD.u_email, ', Phone: ', OLD.u_phone, ', Registered: ', OLD.u_registered_at),
        NOW()
    );
END$

DELIMITER ;

-- Note: Soft delete trigger removed - Admin can delete users with password verification

-- ----------------------------------------------------------------------------
-- Trigger: Auto-update Booking Counter
-- ----------------------------------------------------------------------------
DELIMITER $

DROP TRIGGER IF EXISTS trg_booking_status_update$

CREATE TRIGGER trg_booking_status_update
AFTER UPDATE ON tms_service_booking
FOR EACH ROW
BEGIN
    -- When booking is completed, cancelled, or rejected
    IF NEW.sb_status IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician') 
       AND OLD.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician') THEN
        
        -- Decrement old technician's count
        IF OLD.sb_technician_id IS NOT NULL THEN
            UPDATE tms_technician 
            SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
            WHERE t_id = OLD.sb_technician_id;
        END IF;
        
    -- When technician is assigned to an active booking
    ELSEIF NEW.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
           AND (OLD.sb_technician_id IS NULL OR OLD.sb_technician_id != NEW.sb_technician_id) THEN
        
        -- Decrement old technician's count (if reassigning)
        IF OLD.sb_technician_id IS NOT NULL AND OLD.sb_technician_id != NEW.sb_technician_id THEN
            UPDATE tms_technician 
            SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
            WHERE t_id = OLD.sb_technician_id;
        END IF;
        
        -- Increment new technician's count
        IF NEW.sb_technician_id IS NOT NULL THEN
            UPDATE tms_technician 
            SET t_current_bookings = t_current_bookings + 1
            WHERE t_id = NEW.sb_technician_id;
        END IF;
    END IF;
END$

DELIMITER ;

-- ============================================================================
-- AUTO INCREMENT SETTINGS
-- ============================================================================

ALTER TABLE `tms_admin` MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `tms_feedback` MODIFY `f_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `tms_pwd_resets` MODIFY `r_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `tms_syslogs` MODIFY `l_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `tms_user` MODIFY `u_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `tms_technician` MODIFY `t_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `tms_service` MODIFY `s_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `tms_service_booking` MODIFY `sb_id` int NOT NULL AUTO_INCREMENT;

-- ============================================================================
-- INITIAL DATA SYNC
-- ============================================================================

-- Update all existing users to be protected
UPDATE `tms_user` SET `u_deletion_protected` = 1 WHERE `u_deletion_protected` IS NULL OR `u_deletion_protected` = 0;

-- Sync technician current bookings with actual active bookings
UPDATE tms_technician t
SET t_current_bookings = (
    SELECT COUNT(*)
    FROM tms_service_booking sb
    WHERE sb.sb_technician_id = t.t_id
    AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
);

-- Set default booking limits for technicians
UPDATE tms_technician 
SET t_booking_limit = 5 
WHERE t_booking_limit IS NULL OR t_booking_limit = 0;

-- Insert initial log entry
INSERT INTO tms_system_logs (log_type, log_message, log_data)
VALUES (
    'DATABASE_INITIALIZED',
    'Electrozot database initialized with all features',
    CONCAT('Timestamp: ', NOW())
);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- END OF DATABASE SCRIPT
-- ============================================================================
