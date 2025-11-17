-- ============================================
-- POPULATE SERVICES WITH SUBCATEGORIES AND GADGET NAMES
-- This matches the user dashboard structure
-- ============================================

USE `electrozot_db`;

-- Ensure columns exist
ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_subcategory VARCHAR(200) NULL;
ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_gadget_name VARCHAR(200) NULL;

-- Clear existing services (optional - uncomment if you want fresh start)
-- DELETE FROM tms_service WHERE s_id > 0;
-- ALTER TABLE tms_service AUTO_INCREMENT = 1;

-- ============================================
-- 1. WIRING & FIXTURES
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('Home Wiring Installation', 'Complete home wiring services including new installation and repair', 'Basic Electrical Work', 'Wiring & Fixtures', NULL, 2500.00, '4-6 hours', 'Active'),
('Switch/Socket Installation', 'Installation and replacement of electrical switches and sockets', 'Basic Electrical Work', 'Wiring & Fixtures', NULL, 300.00, '1-2 hours', 'Active'),
('Tube Light Installation', 'Installation of tube lights and LED panels', 'Basic Electrical Work', 'Wiring & Fixtures', 'Tube Light', 200.00, '1 hour', 'Active'),
('LED Panel Installation', 'Installation of LED panel lights', 'Basic Electrical Work', 'Wiring & Fixtures', 'LED Panel', 250.00, '1 hour', 'Active'),
('Chandelier Installation', 'Professional chandelier installation', 'Basic Electrical Work', 'Wiring & Fixtures', 'Chandelier', 500.00, '2 hours', 'Active'),
('Festive Lighting Setup', 'Decorative and festive lighting installation', 'Basic Electrical Work', 'Wiring & Fixtures', NULL, 1500.00, '3-4 hours', 'Active');

-- ============================================
-- 2. SAFETY & POWER
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('Circuit Breaker Repair', 'Main panel troubleshooting and circuit breaker repair', 'Basic Electrical Work', 'Safety & Power', NULL, 1200.00, '2-3 hours', 'Active'),
('Fuse Box Repair', 'Fuse box repair and replacement', 'Basic Electrical Work', 'Safety & Power', NULL, 800.00, '1-2 hours', 'Active'),
('Inverter Installation', 'Inverter installation and wiring', 'Basic Electrical Work', 'Safety & Power', 'Inverter', 3000.00, '3-4 hours', 'Active'),
('UPS Installation', 'UPS system installation', 'Basic Electrical Work', 'Safety & Power', 'UPS', 2500.00, '2-3 hours', 'Active'),
('Voltage Stabilizer Installation', 'Voltage stabilizer installation', 'Basic Electrical Work', 'Safety & Power', 'Stabilizer', 1500.00, '1-2 hours', 'Active'),
('Grounding System Installation', 'Complete grounding and earthing system', 'Basic Electrical Work', 'Safety & Power', NULL, 2000.00, '4-5 hours', 'Active'),
('Electrical Outlet Installation', 'New electrical outlet/point installation', 'Basic Electrical Work', 'Safety & Power', NULL, 400.00, '1-2 hours', 'Active'),
('Fan Regulator Repair', 'Ceiling fan regulator repair', 'Basic Electrical Work', 'Safety & Power', 'Fan Regulator', 250.00, '1 hour', 'Active'),
('Electrical Fault Diagnosis', 'Fault finding and short-circuit repair', 'Basic Electrical Work', 'Safety & Power', NULL, 800.00, '2-3 hours', 'Active');

-- ============================================
-- 3. MAJOR APPLIANCES
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('AC Repair', 'Air conditioner repair service', 'Electronic Repair', 'Major Appliances', 'Split AC', 1500.00, '2-3 hours', 'Active'),
('AC Repair', 'Air conditioner repair service', 'Electronic Repair', 'Major Appliances', 'Window AC', 1200.00, '2-3 hours', 'Active'),
('AC Repair', 'Air conditioner repair service', 'Electronic Repair', 'Major Appliances', 'Central AC', 2000.00, '3-4 hours', 'Active'),
('Refrigerator Repair', 'Refrigerator repair and gas charging', 'Electronic Repair', 'Major Appliances', 'Refrigerator', 1200.00, '2-3 hours', 'Active'),
('Washing Machine Repair', 'Washing machine repair service', 'Electronic Repair', 'Major Appliances', 'Semi-Automatic', 800.00, '2-3 hours', 'Active'),
('Washing Machine Repair', 'Washing machine repair service', 'Electronic Repair', 'Major Appliances', 'Fully Automatic', 1000.00, '2-3 hours', 'Active'),
('Washing Machine Repair', 'Washing machine repair service', 'Electronic Repair', 'Major Appliances', 'Front Load', 1200.00, '2-3 hours', 'Active'),
('Microwave Oven Repair', 'Microwave oven repair and troubleshooting', 'Electronic Repair', 'Major Appliances', 'Microwave', 800.00, '1-2 hours', 'Active'),
('Geyser Repair', 'Water heater repair and maintenance', 'Electronic Repair', 'Major Appliances', 'Geyser', 700.00, '1-2 hours', 'Active');

-- ============================================
-- 4. SMALL GADGETS
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('TV Repair', 'Television repair and troubleshooting', 'Electronic Repair', 'Small Gadgets', 'LED TV', 1000.00, '2-3 hours', 'Active'),
('TV Repair', 'Television repair and troubleshooting', 'Electronic Repair', 'Small Gadgets', 'LCD TV', 900.00, '2-3 hours', 'Active'),
('Fan Repair', 'Fan repair service', 'Electronic Repair', 'Small Gadgets', 'Ceiling Fan', 400.00, '1-2 hours', 'Active'),
('Fan Repair', 'Fan repair service', 'Electronic Repair', 'Small Gadgets', 'Table Fan', 300.00, '1 hour', 'Active'),
('Fan Repair', 'Fan repair service', 'Electronic Repair', 'Small Gadgets', 'Exhaust Fan', 350.00, '1 hour', 'Active'),
('Heater Repair', 'Electric heater repair', 'Electronic Repair', 'Small Gadgets', 'Room Heater', 500.00, '1-2 hours', 'Active'),
('Cooler Repair', 'Air cooler repair and maintenance', 'Electronic Repair', 'Small Gadgets', 'Air Cooler', 500.00, '1-2 hours', 'Active'),
('Music System Repair', 'Music system and home theatre repair', 'Electronic Repair', 'Small Gadgets', 'Music System', 800.00, '1-2 hours', 'Active'),
('Induction Cooktop Repair', 'Induction cooktop repair', 'Electronic Repair', 'Small Gadgets', 'Induction', 600.00, '1-2 hours', 'Active'),
('Iron Repair', 'Electric iron repair', 'Electronic Repair', 'Small Gadgets', 'Electric Iron', 300.00, '1 hour', 'Active'),
('Power Tools Repair', 'Power tools repair service', 'Electronic Repair', 'Small Gadgets', 'Drill/Cutter', 700.00, '1-2 hours', 'Active');

-- ============================================
-- 5. APPLIANCE SETUP
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('TV Installation', 'TV installation and setup', 'Installation & Setup', 'Appliance Setup', 'LED TV', 500.00, '1-2 hours', 'Active'),
('DTH Installation', 'DTH dish installation and tuning', 'Installation & Setup', 'Appliance Setup', 'DTH Dish', 800.00, '2-3 hours', 'Active'),
('Chimney Installation', 'Electric chimney installation', 'Installation & Setup', 'Appliance Setup', 'Electric Chimney', 1200.00, '2-3 hours', 'Active'),
('Fan Installation', 'Fan installation service', 'Installation & Setup', 'Appliance Setup', 'Ceiling Fan', 500.00, '1-2 hours', 'Active'),
('Fan Installation', 'Fan installation service', 'Installation & Setup', 'Appliance Setup', 'Wall Fan', 400.00, '1 hour', 'Active'),
('Washing Machine Installation', 'Washing machine installation and setup', 'Installation & Setup', 'Appliance Setup', 'Washing Machine', 600.00, '1-2 hours', 'Active'),
('Cooler Installation', 'Air cooler installation', 'Installation & Setup', 'Appliance Setup', 'Air Cooler', 400.00, '1 hour', 'Active'),
('Water Filter Installation', 'Water filter installation', 'Installation & Setup', 'Appliance Setup', 'Water Filter', 600.00, '1-2 hours', 'Active'),
('RO Purifier Installation', 'RO purifier installation', 'Installation & Setup', 'Appliance Setup', 'RO Purifier', 1000.00, '2-3 hours', 'Active'),
('Geyser Installation', 'Water heater installation', 'Installation & Setup', 'Appliance Setup', 'Geyser', 1000.00, '2-3 hours', 'Active'),
('Light Fixture Installation', 'Light fixture installation', 'Installation & Setup', 'Appliance Setup', 'Light Fixture', 300.00, '1 hour', 'Active');

-- ============================================
-- 6. TECH & SECURITY
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('Camera Installation', 'CCTV camera installation', 'Installation & Setup', 'Tech & Security', 'CCTV Camera', 3000.00, '4-6 hours', 'Active'),
('WiFi Installation', 'WiFi router setup and installation', 'Installation & Setup', 'Tech & Security', 'WiFi Router', 500.00, '1-2 hours', 'Active'),
('Smart Device Setup', 'Smart home device installation', 'Installation & Setup', 'Tech & Security', 'Smart Switch', 800.00, '1-2 hours', 'Active'),
('Smart Device Setup', 'Smart home device installation', 'Installation & Setup', 'Tech & Security', 'Smart Light', 600.00, '1-2 hours', 'Active');

-- ============================================
-- 7. ROUTINE CARE
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('AC Servicing', 'AC wet and dry servicing', 'Servicing & Maintenance', 'Routine Care', 'AC', 800.00, '1-2 hours', 'Active'),
('Washing Machine Cleaning', 'Washing machine maintenance and cleaning', 'Servicing & Maintenance', 'Routine Care', 'Washing Machine', 500.00, '1-2 hours', 'Active'),
('Geyser Descaling', 'Geyser descaling and service', 'Servicing & Maintenance', 'Routine Care', 'Geyser', 600.00, '1-2 hours', 'Active'),
('Water Filter Servicing', 'Water filter cartridge replacement', 'Servicing & Maintenance', 'Routine Care', 'Water Filter', 400.00, '1 hour', 'Active'),
('Water Tank Cleaning', 'Water tank cleaning service', 'Servicing & Maintenance', 'Routine Care', NULL, 1500.00, '3-4 hours', 'Active'),
('Chimney Cleaning', 'Electric chimney cleaning', 'Servicing & Maintenance', 'Routine Care', 'Electric Chimney', 700.00, '1-2 hours', 'Active');

-- ============================================
-- 8. FIXTURES & TAPS
-- ============================================
INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) VALUES
('Tap Repair', 'Tap and faucet repair', 'Plumbing Work', 'Fixtures & Taps', 'Tap/Faucet', 400.00, '1-2 hours', 'Active'),
('Shower Installation', 'Shower installation and repair', 'Plumbing Work', 'Fixtures & Taps', 'Shower', 600.00, '1-2 hours', 'Active'),
('Washbasin Installation', 'Washbasin and sink installation', 'Plumbing Work', 'Fixtures & Taps', 'Washbasin', 800.00, '2-3 hours', 'Active'),
('Toilet Installation', 'Toilet and commode installation', 'Plumbing Work', 'Fixtures & Taps', 'Toilet/Commode', 1200.00, '2-3 hours', 'Active'),
('Flush Tank Repair', 'Flush tank repair and installation', 'Plumbing Work', 'Fixtures & Taps', 'Flush Tank', 500.00, '1-2 hours', 'Active'),
('Pipe Leak Fix', 'Pipe leak repair service', 'Plumbing Work', 'Fixtures & Taps', NULL, 600.00, '1-2 hours', 'Active');

-- ============================================
-- VERIFICATION
-- ============================================
SELECT '✅ Services populated successfully!' as Status;

-- Show summary by subcategory
SELECT 
    s_subcategory as 'Service Type',
    COUNT(*) as 'Total Services',
    COUNT(DISTINCT s_gadget_name) as 'Unique Gadgets'
FROM tms_service 
WHERE s_status = 'Active'
GROUP BY s_subcategory
ORDER BY s_subcategory;

-- Show all services with gadget names
SELECT 
    s_subcategory as 'Type',
    s_name as 'Service',
    s_gadget_name as 'Gadget',
    CONCAT('₹', s_price) as 'Price',
    s_duration as 'Duration'
FROM tms_service 
WHERE s_status = 'Active'
ORDER BY s_subcategory, s_name, s_gadget_name;
