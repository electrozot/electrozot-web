-- ============================================
-- ADD SUBCATEGORY COLUMN TO SERVICE TABLE
-- ============================================

USE `electrozot_db`;

-- Add subcategory column to tms_service table
ALTER TABLE `tms_service` 
ADD COLUMN `s_subcategory` VARCHAR(200) NULL AFTER `s_category`;

-- Update existing services with subcategories
UPDATE `tms_service` SET `s_subcategory` = 'Wiring & Fixtures' 
WHERE `s_category` = 'Basic Electrical Work' 
AND `s_name` IN ('Home Wiring - New Installation', 'Home Wiring - Repair', 'Switch/Socket Installation', 
                 'Switch/Socket Replacement', 'Tube Light Installation', 'LED Panel Installation', 
                 'Chandelier Installation', 'Festive Lighting Setup');

UPDATE `tms_service` SET `s_subcategory` = 'Safety & Power' 
WHERE `s_category` = 'Basic Electrical Work' 
AND `s_name` IN ('Circuit Breaker Troubleshooting', 'Fuse Box Repair', 'Inverter Installation', 
                 'UPS Installation', 'Voltage Stabilizer Installation', 'Grounding System Installation', 
                 'New Electrical Outlet Installation', 'Ceiling Fan Regulator Repair', 
                 'Electrical Fault Finding', 'Short Circuit Repair');

UPDATE `tms_service` SET `s_subcategory` = 'Major Appliances' 
WHERE `s_category` = 'Electronic Repair' 
AND `s_name` IN ('Split AC Repair', 'Window AC Repair', 'Central AC Repair', 'Refrigerator Repair', 
                 'Refrigerator Gas Charging', 'Semi-Automatic Washing Machine Repair', 
                 'Fully Automatic Washing Machine Repair', 'Front Load Washing Machine Repair', 
                 'Top Load Washing Machine Repair', 'Microwave Oven Repair', 'Geyser Repair');

UPDATE `tms_service` SET `s_subcategory` = 'Other Gadgets' 
WHERE `s_category` = 'Electronic Repair' 
AND `s_subcategory` IS NULL;

UPDATE `tms_service` SET `s_subcategory` = 'Appliance Setup' 
WHERE `s_category` = 'Installation & Setup' 
AND `s_name` IN ('LED TV Installation', 'DTH Dish Installation', 'Electric Chimney Installation', 
                 'Ceiling Fan Installation', 'Wall Fan Installation', 'Washing Machine Installation', 
                 'Washing Machine Uninstallation', 'Air Cooler Installation', 'Water Filter Installation', 
                 'RO Purifier Installation', 'Geyser Installation', 'Light Fixture Installation');

UPDATE `tms_service` SET `s_subcategory` = 'Tech & Security' 
WHERE `s_category` = 'Installation & Setup' 
AND `s_subcategory` IS NULL;

UPDATE `tms_service` SET `s_subcategory` = 'Routine Care' 
WHERE `s_category` = 'Servicing & Maintenance';

UPDATE `tms_service` SET `s_subcategory` = 'Fixtures & Taps' 
WHERE `s_category` = 'Plumbing Work';

SELECT 'Subcategory column added successfully!' AS Status;
