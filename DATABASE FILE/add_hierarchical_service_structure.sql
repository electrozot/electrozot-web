-- ============================================
-- HIERARCHICAL SERVICE STRUCTURE
-- Category → Subcategory → Service
-- ============================================

USE `electrozot_db`;

-- Ensure subcategory column exists
ALTER TABLE `tms_service` 
ADD COLUMN IF NOT EXISTS `s_subcategory` VARCHAR(200) NULL AFTER `s_category`;

-- Add gadget/device name column for specific service items
ALTER TABLE `tms_service` 
ADD COLUMN IF NOT EXISTS `s_gadget_name` VARCHAR(200) NULL AFTER `s_subcategory`;

-- Create a reference table for categories and subcategories
CREATE TABLE IF NOT EXISTS `tms_service_categories` (
  `sc_id` INT AUTO_INCREMENT PRIMARY KEY,
  `sc_category` VARCHAR(200) NOT NULL,
  `sc_subcategory` VARCHAR(200) NOT NULL,
  `sc_status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  UNIQUE KEY `unique_category_subcategory` (`sc_category`, `sc_subcategory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert predefined categories and subcategories
INSERT IGNORE INTO `tms_service_categories` (`sc_category`, `sc_subcategory`) VALUES
-- Basic Electrical Work
('Basic Electrical Work', 'Wiring & Fixtures'),
('Basic Electrical Work', 'Safety & Power'),

-- Electronic Repair
('Electronic Repair', 'Major Appliances'),
('Electronic Repair', 'Small Gadgets'),

-- Installation & Setup
('Installation & Setup', 'Appliance Setup'),
('Installation & Setup', 'Tech & Security'),

-- Servicing & Maintenance
('Servicing & Maintenance', 'Routine Care'),

-- Plumbing Work
('Plumbing Work', 'Fixtures & Taps');

SELECT 'Hierarchical service structure created successfully!' AS Status;
