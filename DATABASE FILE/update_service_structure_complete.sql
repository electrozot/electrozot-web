-- ============================================
-- ELECTROZOT SERVICE STRUCTURE UPDATE
-- Complete 3-Level Hierarchy Implementation
-- ============================================
-- This script creates the exact service structure as specified
-- Level 1: Main Categories (5 categories)
-- Level 2: Subcategories (e.g., Wiring & Fixtures, Major Appliances)
-- Level 3: Individual Services
-- ============================================

USE `electrozot_db`;

-- ============================================
-- STEP 1: Update tms_service table structure
-- ============================================

-- Add new columns for the 3-level hierarchy
ALTER TABLE `tms_service` 
ADD COLUMN IF NOT EXISTS `s_main_category` VARCHAR(200) NOT NULL DEFAULT '' AFTER `s_category`,
ADD COLUMN IF NOT EXISTS `s_subcategory` VARCHAR(200) NOT NULL DEFAULT '' AFTER `s_main_category`,
ADD COLUMN IF NOT EXISTS `s_icon` VARCHAR(200) DEFAULT NULL AFTER `s_subcategory`,
ADD COLUMN IF NOT EXISTS `s_display_order` INT DEFAULT 0 AFTER `s_icon`;

-- Add indexes for better performance
ALTER TABLE `tms_service`
ADD INDEX IF NOT EXISTS `idx_main_category` (`s_main_category`),
ADD INDEX IF NOT EXISTS `idx_subcategory` (`s_subcategory`),
ADD INDEX IF NOT EXISTS `idx_status` (`s_status`);

-- ============================================
-- STEP 2: Clear existing services (optional)
-- ============================================
-- Uncomment the line below if you want to start fresh
-- TRUNCATE TABLE `tms_service`;

-- ============================================
-- STEP 3: Insert all services with hierarchy
-- ============================================

-- ============================================
-- 1. BASIC ELECTRICAL WORK
-- ============================================

-- Wiring & Fixtures
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('Home Wiring (New installation and repair)', 'Complete home wiring services including new installations and repairs', 'Electrical', 'BASIC ELECTRICAL WORK', 'Wiring & Fixtures', 500.00, '4-6 hours', 'Active', 1),
('Switch/Socket Installation and Replacement', 'Professional installation and replacement of switches and sockets', 'Electrical', 'BASIC ELECTRICAL WORK', 'Wiring & Fixtures', 150.00, '1-2 hours', 'Active', 2),
('Light Fixture Installation (Tube lights, LED panels, chandeliers)', 'Installation of all types of light fixtures including tube lights, LED panels, and chandeliers', 'Electrical', 'BASIC ELECTRICAL WORK', 'Wiring & Fixtures', 200.00, '1-2 hours', 'Active', 3),
('Light Decoration/Festive Lighting Setup', 'Professional festive and decorative lighting installation', 'Electrical', 'BASIC ELECTRICAL WORK', 'Wiring & Fixtures', 300.00, '2-3 hours', 'Active', 4);

-- Safety & Power
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('Circuit Breaker and Fuse Box (Main Panel) troubleshooting and repair', 'Expert troubleshooting and repair of circuit breakers and main panels', 'Electrical', 'BASIC ELECTRICAL WORK', 'Safety & Power', 400.00, '2-4 hours', 'Active', 5),
('Inverter, UPS, and Voltage Stabilizer installation/wiring', 'Professional installation and wiring of inverters, UPS, and voltage stabilizers', 'Electrical', 'BASIC ELECTRICAL WORK', 'Safety & Power', 350.00, '2-3 hours', 'Active', 6),
('Grounding and Earthing system installation', 'Complete grounding and earthing system installation for safety', 'Electrical', 'BASIC ELECTRICAL WORK', 'Safety & Power', 600.00, '4-6 hours', 'Active', 7),
('New Electrical Outlet/Point installation', 'Installation of new electrical outlets and power points', 'Electrical', 'BASIC ELECTRICAL WORK', 'Safety & Power', 200.00, '1-2 hours', 'Active', 8),
('Ceiling Fan Regulator repair/replacement', 'Repair and replacement of ceiling fan regulators', 'Electrical', 'BASIC ELECTRICAL WORK', 'Safety & Power', 100.00, '30 min - 1 hour', 'Active', 9),
('Electrical fault finding and short-circuit repair', 'Professional electrical fault diagnosis and short-circuit repairs', 'Electrical', 'BASIC ELECTRICAL WORK', 'Safety & Power', 300.00, '2-3 hours', 'Active', 10);

-- ============================================
-- 2. ELECTRONIC REPAIR (GADGET/APPLIANCE)
-- ============================================

-- Major Appliances
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('Air Conditioner (AC) Repair (Split, Window, Central)', 'Complete AC repair services for all types - Split, Window, and Central AC', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Major Appliances', 500.00, '2-4 hours', 'Active', 11),
('Refrigerator Repair and Gas Charging', 'Refrigerator repair and gas charging services', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Major Appliances', 400.00, '2-3 hours', 'Active', 12),
('Washing Machine Repair (Semi/Fully automatic, Front/Top Load)', 'Repair services for all types of washing machines', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Major Appliances', 350.00, '2-3 hours', 'Active', 13),
('Microwave Oven Repair', 'Professional microwave oven repair and maintenance', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Major Appliances', 300.00, '1-2 hours', 'Active', 14),
('Geyser (Water Heater) Repair', 'Geyser and water heater repair services', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Major Appliances', 250.00, '1-2 hours', 'Active', 15);

-- Other Gadgets
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('Fan Repair (Ceiling, Table, Exhaust)', 'Repair services for all types of fans', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 150.00, '1-2 hours', 'Active', 16),
('Television (TV) Repair and Troubleshooting', 'TV repair and troubleshooting for all brands', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 400.00, '2-3 hours', 'Active', 17),
('Electric Iron/Press Repair', 'Electric iron and press repair services', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 100.00, '1 hour', 'Active', 18),
('Music System/Home Theatre Repair', 'Music system and home theatre repair services', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 350.00, '2-3 hours', 'Active', 19),
('Electric Heater Repair (Room Heaters, Rods)', 'Electric heater repair for room heaters and heating rods', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 200.00, '1-2 hours', 'Active', 20),
('Induction Cooktop and Electric Stove Repair', 'Induction cooktop and electric stove repair services', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 250.00, '1-2 hours', 'Active', 21),
('Air Cooler Repair', 'Air cooler repair and maintenance', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 200.00, '1-2 hours', 'Active', 22),
('Power Tools Repair (Drills, Cutters, Grinders, etc.)', 'Repair services for power tools including drills, cutters, and grinders', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 300.00, '2-3 hours', 'Active', 23),
('Water Filter/Purifier Repair', 'Water filter and purifier repair services', 'Appliance', 'ELECTRONIC REPAIR (GADGET/APPLIANCE)', 'Other Gadgets', 250.00, '1-2 hours', 'Active', 24);

-- ============================================
-- 3. INSTALLATION & SETUP
-- ============================================

-- Appliance Setup
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('TV/DTH Dish Installation and Tuning', 'Professional TV and DTH dish installation with tuning', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 300.00, '2-3 hours', 'Active', 25),
('Electric Chimney Installation', 'Kitchen chimney installation services', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 400.00, '2-3 hours', 'Active', 26),
('Ceiling and Wall Fan Installation', 'Professional ceiling and wall fan installation', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 200.00, '1-2 hours', 'Active', 27),
('Washing Machine Installation and Uninstallation', 'Washing machine installation and uninstallation services', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 250.00, '1-2 hours', 'Active', 28),
('Air Cooler Installation', 'Air cooler installation and setup', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 150.00, '1 hour', 'Active', 29),
('Water Filter/Purifier Installation', 'Water filter and purifier installation services', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 300.00, '1-2 hours', 'Active', 30),
('Geyser/Water Heater Installation', 'Geyser and water heater installation services', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 350.00, '2-3 hours', 'Active', 31),
('Light Fixture Installation', 'Installation of various light fixtures', 'Installation', 'INSTALLATION & SETUP', 'Appliance Setup', 200.00, '1-2 hours', 'Active', 32);

-- Tech & Security
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('CCTV and Security Camera Installation', 'Professional CCTV and security camera installation', 'Installation', 'INSTALLATION & SETUP', 'Tech & Security', 800.00, '4-6 hours', 'Active', 33),
('Wi-Fi Router and Modem Setup/Troubleshooting', 'Wi-Fi router and modem setup and troubleshooting', 'Installation', 'INSTALLATION & SETUP', 'Tech & Security', 200.00, '1-2 hours', 'Active', 34),
('Smart Home Device Installation (Smart switches, smart lights)', 'Smart home device installation including smart switches and lights', 'Installation', 'INSTALLATION & SETUP', 'Tech & Security', 400.00, '2-3 hours', 'Active', 35);

-- ============================================
-- 4. SERVICING & MAINTENANCE
-- ============================================

-- Routine Care
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('AC Wet and Dry Servicing', 'Complete AC wet and dry servicing', 'Maintenance', 'SERVICING & MAINTENANCE', 'Routine Care', 600.00, '2-3 hours', 'Active', 36),
('Washing Machine General Maintenance and Cleaning', 'Washing machine general maintenance and deep cleaning', 'Maintenance', 'SERVICING & MAINTENANCE', 'Routine Care', 300.00, '1-2 hours', 'Active', 37),
('Geyser Descaling and Service', 'Geyser descaling and complete servicing', 'Maintenance', 'SERVICING & MAINTENANCE', 'Routine Care', 350.00, '2-3 hours', 'Active', 38),
('Water Filter Cartridge Replacement and General Service', 'Water filter cartridge replacement and general servicing', 'Maintenance', 'SERVICING & MAINTENANCE', 'Routine Care', 250.00, '1 hour', 'Active', 39),
('Water Tank Cleaning (Manual and Motorized)', 'Professional water tank cleaning services', 'Maintenance', 'SERVICING & MAINTENANCE', 'Routine Care', 500.00, '3-4 hours', 'Active', 40);

-- ============================================
-- 5. PLUMBING WORK
-- ============================================

-- Fixtures & Taps
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_main_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`, `s_display_order`) VALUES
('Tap, Faucet, and Shower Installation/Repair', 'Installation and repair of taps, faucets, and showers', 'Plumbing', 'PLUMBING WORK', 'Fixtures & Taps', 200.00, '1-2 hours', 'Active', 41),
('Washbasin and Sink Installation/Repair', 'Washbasin and sink installation and repair services', 'Plumbing', 'PLUMBING WORK', 'Fixtures & Taps', 300.00, '2-3 hours', 'Active', 42),
('Toilet, Commode, and Flush Tank Installation', 'Complete toilet, commode, and flush tank installation', 'Plumbing', 'PLUMBING WORK', 'Fixtures & Taps', 500.00, '3-4 hours', 'Active', 43);

-- ============================================
-- STEP 4: Verification Query
-- ============================================

-- Run this to verify the structure
SELECT 
    s_main_category AS 'Main Category',
    s_subcategory AS 'Subcategory',
    COUNT(*) AS 'Service Count'
FROM tms_service
WHERE s_status = 'Active'
GROUP BY s_main_category, s_subcategory
ORDER BY s_display_order;

-- ============================================
-- COMPLETION MESSAGE
-- ============================================
SELECT 'Service structure updated successfully!' AS 'Status',
       COUNT(*) AS 'Total Services'
FROM tms_service
WHERE s_status = 'Active';
