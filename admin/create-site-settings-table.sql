-- SQL to create tms_site_settings table
-- Run this in phpMyAdmin or MySQL command line

USE `electrozot_db`;

-- Create the site settings table
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

-- Insert default contact settings
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

-- Verify the table was created
SELECT * FROM `tms_site_settings`;
