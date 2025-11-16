-- ============================================
-- ELECTROZOT - COMPLETE SERVICE SETUP
-- Run this file in phpMyAdmin to setup all services
-- ============================================

USE `electrozot_db`;

-- Step 1: Add subcategory column
ALTER TABLE `tms_service` 
ADD COLUMN `s_subcategory` VARCHAR(200) NULL AFTER `s_category`;

-- Step 2: Clear existing services (OPTIONAL - Remove comment to clear old data)
-- DELETE FROM `tms_service`;

-- ============================================
-- 1. BASIC ELECTRICAL WORK
-- ============================================

-- Wiring & Fixtures
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('Home Wiring - New Installation', 'Complete new home wiring installation with quality materials and safety standards', 'Basic Electrical Work', 'Wiring & Fixtures', 2500.00, '1-2 days', 'Active'),
('Home Wiring - Repair', 'Professional repair of existing home wiring systems', 'Basic Electrical Work', 'Wiring & Fixtures', 800.00, '2-4 hours', 'Active'),
('Switch/Socket Installation', 'Installation of new electrical switches and sockets', 'Basic Electrical Work', 'Wiring & Fixtures', 150.00, '30 mins', 'Active'),
('Switch/Socket Replacement', 'Replacement of faulty or old switches and sockets', 'Basic Electrical Work', 'Wiring & Fixtures', 120.00, '30 mins', 'Active'),
('Tube Light Installation', 'Installation of tube lights with proper wiring', 'Basic Electrical Work', 'Wiring & Fixtures', 200.00, '1 hour', 'Active'),
('LED Panel Installation', 'Modern LED panel light installation', 'Basic Electrical Work', 'Wiring & Fixtures', 300.00, '1 hour', 'Active'),
('Chandelier Installation', 'Professional chandelier installation and setup', 'Basic Electrical Work', 'Wiring & Fixtures', 500.00, '2-3 hours', 'Active'),
('Festive Lighting Setup', 'Decorative and festive lighting installation', 'Basic Electrical Work', 'Wiring & Fixtures', 600.00, '2-4 hours', 'Active');

-- Safety & Power
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('Circuit Breaker Troubleshooting', 'Diagnose and fix circuit breaker issues', 'Basic Electrical Work', 'Safety & Power', 400.00, '1-2 hours', 'Active'),
('Fuse Box Repair', 'Main panel fuse box repair and maintenance', 'Basic Electrical Work', 'Safety & Power', 500.00, '2-3 hours', 'Active'),
('Inverter Installation', 'Complete inverter installation with wiring', 'Basic Electrical Work', 'Safety & Power', 800.00, '3-4 hours', 'Active'),
('UPS Installation', 'UPS system installation and configuration', 'Basic Electrical Work', 'Safety & Power', 600.00, '2-3 hours', 'Active'),
('Voltage Stabilizer Installation', 'Voltage stabilizer installation for appliances', 'Basic Electrical Work', 'Safety & Power', 400.00, '1-2 hours', 'Active'),
('Grounding System Installation', 'Proper earthing and grounding system setup', 'Basic Electrical Work', 'Safety & Power', 1000.00, '4-6 hours', 'Active'),
('New Electrical Outlet Installation', 'Add new electrical points in your home', 'Basic Electrical Work', 'Safety & Power', 300.00, '1-2 hours', 'Active'),
('Ceiling Fan Regulator Repair', 'Repair or replace ceiling fan regulators', 'Basic Electrical Work', 'Safety & Power', 150.00, '30 mins', 'Active'),
('Electrical Fault Finding', 'Professional electrical fault diagnosis', 'Basic Electrical Work', 'Safety & Power', 500.00, '1-2 hours', 'Active'),
('Short Circuit Repair', 'Emergency short circuit repair service', 'Basic Electrical Work', 'Safety & Power', 700.00, '2-3 hours', 'Active');

-- ============================================
-- 2. ELECTRONIC REPAIR (GADGET/APPLIANCE)
-- ============================================

-- Major Appliances
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('Split AC Repair', 'Complete split air conditioner repair service', 'Electronic Repair', 'Major Appliances', 800.00, '2-3 hours', 'Active'),
('Window AC Repair', 'Window air conditioner repair and maintenance', 'Electronic Repair', 'Major Appliances', 600.00, '2-3 hours', 'Active'),
('Central AC Repair', 'Central air conditioning system repair', 'Electronic Repair', 'Major Appliances', 1500.00, '4-6 hours', 'Active'),
('Refrigerator Repair', 'Refrigerator repair and troubleshooting', 'Electronic Repair', 'Major Appliances', 700.00, '2-3 hours', 'Active'),
('Refrigerator Gas Charging', 'Refrigerator gas refill and charging service', 'Electronic Repair', 'Major Appliances', 1200.00, '2-3 hours', 'Active'),
('Semi-Automatic Washing Machine Repair', 'Semi-automatic washing machine repair', 'Electronic Repair', 'Major Appliances', 500.00, '1-2 hours', 'Active'),
('Fully Automatic Washing Machine Repair', 'Fully automatic washing machine repair', 'Electronic Repair', 'Major Appliances', 700.00, '2-3 hours', 'Active'),
('Front Load Washing Machine Repair', 'Front load washing machine repair service', 'Electronic Repair', 'Major Appliances', 800.00, '2-3 hours', 'Active'),
('Top Load Washing Machine Repair', 'Top load washing machine repair service', 'Electronic Repair', 'Major Appliances', 600.00, '2-3 hours', 'Active'),
('Microwave Oven Repair', 'Microwave oven repair and troubleshooting', 'Electronic Repair', 'Major Appliances', 600.00, '1-2 hours', 'Active'),
('Geyser Repair', 'Water heater/geyser repair service', 'Electronic Repair', 'Major Appliances', 500.00, '1-2 hours', 'Active');

-- Other Gadgets
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('Ceiling Fan Repair', 'Ceiling fan repair and maintenance', 'Electronic Repair', 'Other Gadgets', 300.00, '1 hour', 'Active'),
('Table Fan Repair', 'Table fan repair service', 'Electronic Repair', 'Other Gadgets', 200.00, '1 hour', 'Active'),
('Exhaust Fan Repair', 'Exhaust fan repair and cleaning', 'Electronic Repair', 'Other Gadgets', 250.00, '1 hour', 'Active'),
('LED TV Repair', 'LED television repair and troubleshooting', 'Electronic Repair', 'Other Gadgets', 800.00, '2-3 hours', 'Active'),
('LCD TV Repair', 'LCD television repair service', 'Electronic Repair', 'Other Gadgets', 700.00, '2-3 hours', 'Active'),
('Smart TV Repair', 'Smart TV repair and software issues', 'Electronic Repair', 'Other Gadgets', 1000.00, '2-4 hours', 'Active'),
('Electric Iron Repair', 'Electric iron/press repair service', 'Electronic Repair', 'Other Gadgets', 200.00, '1 hour', 'Active'),
('Music System Repair', 'Music system and audio equipment repair', 'Electronic Repair', 'Other Gadgets', 600.00, '2-3 hours', 'Active'),
('Home Theatre Repair', 'Home theatre system repair service', 'Electronic Repair', 'Other Gadgets', 800.00, '2-3 hours', 'Active'),
('Room Heater Repair', 'Electric room heater repair', 'Electronic Repair', 'Other Gadgets', 400.00, '1-2 hours', 'Active'),
('Rod Heater Repair', 'Electric rod heater repair service', 'Electronic Repair', 'Other Gadgets', 300.00, '1 hour', 'Active'),
('Induction Cooktop Repair', 'Induction cooktop repair and troubleshooting', 'Electronic Repair', 'Other Gadgets', 500.00, '1-2 hours', 'Active'),
('Electric Stove Repair', 'Electric stove repair service', 'Electronic Repair', 'Other Gadgets', 400.00, '1-2 hours', 'Active'),
('Air Cooler Repair', 'Air cooler repair and maintenance', 'Electronic Repair', 'Other Gadgets', 400.00, '1-2 hours', 'Active'),
('Power Drill Repair', 'Power drill and tool repair', 'Electronic Repair', 'Other Gadgets', 500.00, '1-2 hours', 'Active'),
('Electric Cutter Repair', 'Electric cutter repair service', 'Electronic Repair', 'Other Gadgets', 450.00, '1-2 hours', 'Active'),
('Grinder Repair', 'Electric grinder repair service', 'Electronic Repair', 'Other Gadgets', 400.00, '1-2 hours', 'Active'),
('Water Filter Repair', 'Water filter/purifier repair service', 'Electronic Repair', 'Other Gadgets', 500.00, '1-2 hours', 'Active'),
('RO Purifier Repair', 'RO water purifier repair and maintenance', 'Electronic Repair', 'Other Gadgets', 700.00, '2-3 hours', 'Active');

-- ============================================
-- 3. INSTALLATION & SETUP
-- ============================================

-- Appliance Setup
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('TV/DTH Dish Installation', 'TV and DTH dish installation with tuning', 'Installation & Setup', 'Appliance Setup', 600.00, '2-3 hours', 'Active'),
('Electric Chimney Installation', 'Kitchen chimney installation service', 'Installation & Setup', 'Appliance Setup', 800.00, '2-3 hours', 'Active'),
('Ceiling Fan Installation', 'New ceiling fan installation', 'Installation & Setup', 'Appliance Setup', 400.00, '1-2 hours', 'Active'),
('Wall Fan Installation', 'Wall mounted fan installation', 'Installation & Setup', 'Appliance Setup', 350.00, '1 hour', 'Active'),
('Washing Machine Installation', 'Washing machine installation and setup', 'Installation & Setup', 'Appliance Setup', 500.00, '1-2 hours', 'Active'),
('Washing Machine Uninstallation', 'Washing machine removal and uninstallation', 'Installation & Setup', 'Appliance Setup', 300.00, '1 hour', 'Active'),
('Air Cooler Installation', 'Air cooler installation and setup', 'Installation & Setup', 'Appliance Setup', 400.00, '1-2 hours', 'Active'),
('Water Filter/Purifier Installation', 'Water filter and purifier installation', 'Installation & Setup', 'Appliance Setup', 700.00, '2-3 hours', 'Active'),
('Geyser/Water Heater Installation', 'Water heater/geyser installation', 'Installation & Setup', 'Appliance Setup', 700.00, '2-3 hours', 'Active'),
('Light Fixture Installation', 'General light fixture installation', 'Installation & Setup', 'Appliance Setup', 300.00, '1 hour', 'Active');

-- Tech & Security
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('CCTV Camera Installation - Single', 'Single CCTV camera installation', 'Installation & Setup', 'Tech & Security', 1200.00, '2-3 hours', 'Active'),
('CCTV Camera Installation - 4 Cameras', 'Complete 4 camera CCTV system installation', 'Installation & Setup', 'Tech & Security', 4000.00, '1 day', 'Active'),
('Security Camera Installation', 'Security camera installation and setup', 'Installation & Setup', 'Tech & Security', 1500.00, '2-4 hours', 'Active'),
('Wi-Fi Router Setup', 'Wi-Fi router installation and configuration', 'Installation & Setup', 'Tech & Security', 300.00, '1 hour', 'Active'),
('Modem Setup/Troubleshooting', 'Internet modem setup and troubleshooting', 'Installation & Setup', 'Tech & Security', 250.00, '1 hour', 'Active'),
('Smart Switch Installation', 'Smart home switch installation', 'Installation & Setup', 'Tech & Security', 400.00, '1-2 hours', 'Active'),
('Smart Light Installation', 'Smart lighting system installation', 'Installation & Setup', 'Tech & Security', 500.00, '1-2 hours', 'Active'),
('Smart Home Device Installation', 'General smart home device installation (smart switches, smart lights)', 'Installation & Setup', 'Tech & Security', 600.00, '2-3 hours', 'Active');

-- ============================================
-- 4. SERVICING & MAINTENANCE
-- ============================================

-- Routine Care
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('AC Wet Servicing', 'Complete AC wet servicing with deep cleaning', 'Servicing & Maintenance', 'Routine Care', 800.00, '2-3 hours', 'Active'),
('AC Dry Servicing', 'AC dry servicing and filter cleaning', 'Servicing & Maintenance', 'Routine Care', 500.00, '1-2 hours', 'Active'),
('Washing Machine Cleaning', 'Deep cleaning of washing machine', 'Servicing & Maintenance', 'Routine Care', 400.00, '1-2 hours', 'Active'),
('Washing Machine Maintenance', 'General washing machine maintenance', 'Servicing & Maintenance', 'Routine Care', 500.00, '1-2 hours', 'Active'),
('Geyser Descaling', 'Geyser descaling and cleaning service', 'Servicing & Maintenance', 'Routine Care', 600.00, '2-3 hours', 'Active'),
('Geyser Service', 'Complete geyser servicing', 'Servicing & Maintenance', 'Routine Care', 500.00, '1-2 hours', 'Active'),
('Water Filter Cartridge Replacement', 'Water filter cartridge replacement', 'Servicing & Maintenance', 'Routine Care', 400.00, '1 hour', 'Active'),
('Water Filter General Service', 'Complete water filter servicing', 'Servicing & Maintenance', 'Routine Care', 600.00, '1-2 hours', 'Active'),
('Water Tank Cleaning - Manual', 'Manual water tank cleaning service', 'Servicing & Maintenance', 'Routine Care', 800.00, '3-4 hours', 'Active'),
('Water Tank Cleaning - Motorized', 'Motorized water tank cleaning service', 'Servicing & Maintenance', 'Routine Care', 1200.00, '2-3 hours', 'Active');

-- ============================================
-- 5. PLUMBING WORK
-- ============================================

-- Fixtures & Taps
INSERT INTO `tms_service` (`s_name`, `s_description`, `s_category`, `s_subcategory`, `s_price`, `s_duration`, `s_status`) VALUES
('Tap Installation', 'New tap installation service', 'Plumbing Work', 'Fixtures & Taps', 300.00, '1 hour', 'Active'),
('Tap Repair', 'Tap and faucet repair service', 'Plumbing Work', 'Fixtures & Taps', 200.00, '1 hour', 'Active'),
('Faucet Installation', 'Faucet installation and replacement', 'Plumbing Work', 'Fixtures & Taps', 350.00, '1 hour', 'Active'),
('Shower Installation', 'Shower installation and setup', 'Plumbing Work', 'Fixtures & Taps', 500.00, '1-2 hours', 'Active'),
('Shower Repair', 'Shower repair and maintenance', 'Plumbing Work', 'Fixtures & Taps', 300.00, '1 hour', 'Active'),
('Washbasin Installation', 'Washbasin installation service', 'Plumbing Work', 'Fixtures & Taps', 800.00, '2-3 hours', 'Active'),
('Washbasin Repair', 'Washbasin repair and fixing', 'Plumbing Work', 'Fixtures & Taps', 400.00, '1-2 hours', 'Active'),
('Kitchen Sink Installation', 'Kitchen sink installation', 'Plumbing Work', 'Fixtures & Taps', 700.00, '2-3 hours', 'Active'),
('Kitchen Sink Repair', 'Kitchen sink repair service', 'Plumbing Work', 'Fixtures & Taps', 350.00, '1 hour', 'Active'),
('Toilet Installation', 'Complete toilet installation', 'Plumbing Work', 'Fixtures & Taps', 1200.00, '3-4 hours', 'Active'),
('Commode Installation', 'Commode installation service', 'Plumbing Work', 'Fixtures & Taps', 1000.00, '2-3 hours', 'Active'),
('Flush Tank Installation', 'Flush tank installation and setup', 'Plumbing Work', 'Fixtures & Taps', 600.00, '1-2 hours', 'Active');

-- ============================================
-- SUMMARY
-- ============================================
SELECT 'Setup completed successfully!' AS Status;
SELECT s_category AS Category, s_subcategory AS Subcategory, COUNT(*) AS Total 
FROM tms_service 
WHERE s_status = 'Active'
GROUP BY s_category, s_subcategory 
ORDER BY s_category, s_subcategory;
