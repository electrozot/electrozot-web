-- ============================================
-- ADD DETAILED SERVICES WITH CATEGORIES
-- ElectroZot - Comprehensive Service List
-- ============================================

USE `electrozot_db`;

-- First, add parent_category column if it doesn't exist
ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS parent_category VARCHAR(200) DEFAULT NULL;
ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS subcategory VARCHAR(200) DEFAULT NULL;

-- Clear existing services (optional - comment out if you want to keep existing)
-- DELETE FROM tms_service WHERE s_id > 0;
-- ALTER TABLE tms_service AUTO_INCREMENT = 1;

-- ============================================
-- 1. BASIC ELECTRICAL WORK
-- ============================================

-- Wiring & Fixtures
INSERT INTO tms_service (s_name, s_description, s_category, parent_category, subcategory, s_price, s_duration, s_status) VALUES
('Home Wiring Installation', 'New home wiring installation and repair services', 'Basic Electrical Work', 'Basic Electrical Work', 'Wiring & Fixtures', 2500.00, '4-6 hours', 'Active'),
('Switch/Socket Installation', 'Installation and replacement of switches and sockets', 'Basic Electrical Work', 'Basic Electrical Work', 'Wiring & Fixtures', 300.00, '1-2 hours', 'Active'),
('Light Fixture Installation', 'Tube lights, LED panels, chandeliers installation', 'Basic Electrical Work', 'Basic Electrical Work', 'Wiring & Fixtures', 500.00, '1-2 hours', 'Active'),
('Festive Lighting Setup', 'Light decoration and festive lighting installation', 'Basic Electrical Work', 'Basic Electrical Work', 'Wiring & Fixtures', 1500.00, '3-4 hours', 'Active'),

-- Safety & Power
('Circuit Breaker Repair', 'Main panel troubleshooting and circuit breaker repair', 'Basic Electrical Work', 'Basic Electrical Work', 'Safety & Power', 1200.00, '2-3 hours', 'Active'),
('Inverter/UPS Installation', 'Inverter, UPS, and voltage stabilizer installation', 'Basic Electrical Work', 'Basic Electrical Work', 'Safety & Power', 3000.00, '3-4 hours', 'Active'),
('Grounding System Installation', 'Grounding and earthing system installation', 'Basic Electrical Work', 'Basic Electrical Work', 'Safety & Power', 2000.00, '4-5 hours', 'Active'),
('Electrical Outlet Installation', 'New electrical outlet/point installation', 'Basic Electrical Work', 'Basic Electrical Work', 'Safety & Power', 400.00, '1-2 hours', 'Active'),
('Ceiling Fan Regulator Repair', 'Ceiling fan regulator repair and replacement', 'Basic Electrical Work', 'Basic Electrical Work', 'Safety & Power', 250.00, '1 hour', 'Active'),
('Electrical Fault Diagnosis', 'Electrical fault finding and short-circuit repair', 'Basic Electrical Work', 'Basic Electrical Work', 'Safety & Power', 800.00, '2-3 hours', 'Active'),

-- ============================================
-- 2. ELECTRONIC REPAIR
-- ============================================

-- Major Appliances
('AC Repair - Split/Window', 'Air conditioner repair for all types (Split, Window, Central)', 'Electronic Repair', 'Electronic Repair', 'Major Appliances', 1500.00, '2-3 hours', 'Active'),
('Refrigerator Repair', 'Refrigerator repair and gas charging', 'Electronic Repair', 'Electronic Repair', 'Major Appliances', 1200.00, '2-3 hours', 'Active'),
('Washing Machine Repair', 'Semi-automatic and fully automatic washing machine repair', 'Electronic Repair', 'Electronic Repair', 'Major Appliances', 1000.00, '2-3 hours', 'Active'),
('Microwave Oven Repair', 'Microwave oven repair and troubleshooting', 'Electronic Repair', 'Electronic Repair', 'Major Appliances', 800.00, '1-2 hours', 'Active'),
('Geyser/Water Heater Repair', 'Water heater repair and maintenance', 'Electronic Repair', 'Electronic Repair', 'Major Appliances', 700.00, '1-2 hours', 'Active'),

-- Other Gadgets
('Ceiling Fan Repair', 'Ceiling, table, and exhaust fan repair', 'Electronic Repair', 'Electronic Repair', 'Other Gadgets', 400.00, '1-2 hours', 'Active'),
('Television Repair', 'TV repair and troubleshooting', 'Electronic Repair', 'Electronic Repair', 'Other Gadgets', 1000.00, '2-3 hours', 'Active'),
('Electric Iron Repair', 'Electric iron and press repair', 'Electronic Repair', 'Electronic Repair', 'Other Gadgets', 300.00, '1 hour', 'Active'),
('Home Theatre Repair', 'Music system and home theatre repair', 'Electronic Repair', 'Electronic Repair', 'Other Gadgets', 800.00, '1-2 hours', 'Active'),
('Room Heater Repair', 'Electric heater repair (room heaters, rods)', 'Appliance', 'Electronic Repair', 'Other Gadgets', 500.00, '1-2 hours', 'Active'),
('Induction Cooktop Repair', 'Induction cooktop and electric stove repair', 'Appliance', 'Electronic Repair', 'Other Gadgets', 600.00, '1-2 hours', 'Active'),
('Air Cooler Repair', 'Air cooler repair and maintenance', 'Appliance', 'Electronic Repair', 'Other Gadgets', 500.00, '1-2 hours', 'Active'),
('Power Tools Repair', 'Drills, cutters, grinders repair', 'Appliance', 'Electronic Repair', 'Other Gadgets', 700.00, '1-2 hours', 'Active'),
('Water Filter/Purifier Repair', 'Water filter and purifier repair', 'Appliance', 'Electronic Repair', 'Other Gadgets', 600.00, '1-2 hours', 'Active'),

-- ============================================
-- 3. INSTALLATION & SETUP
-- ============================================

-- Appliance Setup
('TV/DTH Installation', 'TV and DTH dish installation and tuning', 'General', 'Installation & Setup', 'Appliance Setup', 800.00, '2-3 hours', 'Active'),
('Electric Chimney Installation', 'Kitchen chimney installation', 'General', 'Installation & Setup', 'Appliance Setup', 1200.00, '2-3 hours', 'Active'),
('Ceiling/Wall Fan Installation', 'Ceiling and wall fan installation', 'General', 'Installation & Setup', 'Appliance Setup', 500.00, '1-2 hours', 'Active'),
('Washing Machine Installation', 'Washing machine installation and setup', 'General', 'Installation & Setup', 'Appliance Setup', 600.00, '1-2 hours', 'Active'),
('Air Cooler Installation', 'Air cooler installation', 'General', 'Installation & Setup', 'Appliance Setup', 400.00, '1 hour', 'Active'),
('Water Filter Installation', 'Water filter and purifier installation', 'General', 'Installation & Setup', 'Appliance Setup', 800.00, '1-2 hours', 'Active'),
('Geyser Installation', 'Water heater and geyser installation', 'General', 'Installation & Setup', 'Appliance Setup', 1000.00, '2-3 hours', 'Active'),

-- Tech & Security
('CCTV Camera Installation', 'CCTV and security camera installation', 'General', 'Installation & Setup', 'Tech & Security', 3000.00, '4-6 hours', 'Active'),
('Wi-Fi Router Setup', 'Wi-Fi router and modem setup/troubleshooting', 'General', 'Installation & Setup', 'Tech & Security', 500.00, '1-2 hours', 'Active'),
('Smart Home Device Setup', 'Smart switches and smart lights installation', 'General', 'Installation & Setup', 'Tech & Security', 1500.00, '2-3 hours', 'Active'),

-- ============================================
-- 4. SERVICING & MAINTENANCE
-- ============================================

-- Routine Care
('AC Servicing', 'AC wet and dry servicing', 'HVAC', 'Servicing & Maintenance', 'Routine Care', 800.00, '1-2 hours', 'Active'),
('Washing Machine Cleaning', 'Washing machine general maintenance and cleaning', 'Appliance', 'Servicing & Maintenance', 'Routine Care', 500.00, '1-2 hours', 'Active'),
('Geyser Descaling', 'Geyser descaling and service', 'Appliance', 'Servicing & Maintenance', 'Routine Care', 600.00, '1-2 hours', 'Active'),
('Water Filter Maintenance', 'Water filter cartridge replacement and service', 'General', 'Servicing & Maintenance', 'Routine Care', 400.00, '1 hour', 'Active'),
('Water Tank Cleaning', 'Water tank cleaning (manual and motorized)', 'General', 'Servicing & Maintenance', 'Routine Care', 1500.00, '3-4 hours', 'Active'),

-- ============================================
-- 5. PLUMBING WORK
-- ============================================

-- Fixtures & Taps
('Tap Installation/Repair', 'Tap, faucet, and shower installation/repair', 'Plumbing', 'Plumbing Work', 'Fixtures & Taps', 400.00, '1-2 hours', 'Active'),
('Washbasin Installation', 'Washbasin and sink installation/repair', 'Plumbing', 'Plumbing Work', 'Fixtures & Taps', 800.00, '2-3 hours', 'Active'),
('Toilet Installation', 'Toilet, commode, and flush tank installation/repair', 'Plumbing', 'Plumbing Work', 'Fixtures & Taps', 1200.00, '2-3 hours', 'Active'),
('Bathroom Fitting Installation', 'Complete bathroom fitting installation', 'Plumbing', 'Plumbing Work', 'Fixtures & Taps', 2000.00, '4-5 hours', 'Active');

-- Verify insertion
SELECT 'Services Added Successfully!' as status;
SELECT s_category, parent_category, COUNT(*) as count 
FROM tms_service 
GROUP BY s_category, parent_category 
ORDER BY parent_category, s_category;

-- Show summary
SELECT 
    parent_category,
    COUNT(*) as total_services,
    COUNT(DISTINCT subcategory) as subcategories
FROM tms_service 
WHERE parent_category IS NOT NULL
GROUP BY parent_category;
