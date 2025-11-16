-- Add all services based on the detailed service list
-- Clear existing services first (optional - comment out if you want to keep existing)
-- TRUNCATE TABLE tms_service;

-- Reset auto increment
ALTER TABLE tms_service AUTO_INCREMENT = 1;

-- 1. BASIC ELECTRICAL WORK - Wiring & Fixtures
INSERT INTO tms_service (s_name, s_description, s_category, s_price, s_duration, s_status) VALUES
('Home Wiring (New installation and repair)', 'Complete home wiring services including new installation and repair of existing wiring systems', 'Electrical', 800.00, '3-4 hours', 'Active'),
('Switch/Socket Installation and Replacement', 'Installation and replacement of electrical switches and sockets', 'Electrical', 150.00, '30 mins', 'Active'),
('Light Fixture Installation', 'Installation of tube lights, LED panels, chandeliers and other light fixtures', 'Electrical', 200.00, '1 hour', 'Active'),
('Light Decoration/Festive Lighting Setup', 'Professional festive and decorative lighting installation and setup', 'Electrical', 500.00, '2-3 hours', 'Active'),

-- 1. BASIC ELECTRICAL WORK - Safety & Power
('Circuit Breaker and Fuse Box Repair', 'Main panel troubleshooting and repair of circuit breakers and fuse boxes', 'Electrical', 400.00, '1-2 hours', 'Active'),
('Inverter, UPS, and Voltage Stabilizer Installation', 'Professional installation and wiring of inverters, UPS systems and voltage stabilizers', 'Electrical', 600.00, '2-3 hours', 'Active'),
('Grounding and Earthing System Installation', 'Complete grounding and earthing system installation for electrical safety', 'Electrical', 700.00, '2-3 hours', 'Active'),
('New Electrical Outlet/Point Installation', 'Installation of new electrical outlets and power points', 'Electrical', 250.00, '1 hour', 'Active'),
('Ceiling Fan Regulator Repair/Replacement', 'Repair and replacement of ceiling fan regulators', 'Electrical', 150.00, '30 mins', 'Active'),
('Electrical Fault Finding and Short-Circuit Repair', 'Professional electrical fault diagnosis and short-circuit repair', 'Electrical', 350.00, '1-2 hours', 'Active'),

-- 2. ELECTRONIC REPAIR - Major Appliances
('Air Conditioner (AC) Repair', 'Repair of all types of AC - Split, Window, Central AC systems', 'Appliance', 500.00, '1-2 hours', 'Active'),
('Refrigerator Repair and Gas Charging', 'Complete refrigerator repair including gas charging and cooling issues', 'Appliance', 600.00, '1-2 hours', 'Active'),
('Washing Machine Repair', 'Repair of Semi/Fully automatic, Front/Top Load washing machines', 'Appliance', 400.00, '1-2 hours', 'Active'),
('Microwave Oven Repair', 'Professional microwave oven repair and troubleshooting', 'Appliance', 350.00, '1 hour', 'Active'),
('Geyser (Water Heater) Repair', 'Repair and maintenance of water heaters and geysers', 'Appliance', 400.00, '1-2 hours', 'Active'),

-- 2. ELECTRONIC REPAIR - Other Gadgets
('Fan Repair (Ceiling, Table, Exhaust)', 'Repair of all types of fans - ceiling, table, and exhaust fans', 'Appliance', 200.00, '30-45 mins', 'Active'),
('Television (TV) Repair and Troubleshooting', 'Professional TV repair and troubleshooting services', 'Appliance', 500.00, '1-2 hours', 'Active'),
('Electric Iron/Press Repair', 'Repair of electric irons and pressing machines', 'Appliance', 150.00, '30 mins', 'Active'),
('Music System/Home Theatre Repair', 'Repair and troubleshooting of music systems and home theatre', 'Appliance', 400.00, '1-2 hours', 'Active'),
('Electric Heater Repair', 'Repair of room heaters, rod heaters and other electric heating devices', 'Appliance', 300.00, '1 hour', 'Active'),
('Induction Cooktop and Electric Stove Repair', 'Repair of induction cooktops and electric stoves', 'Appliance', 350.00, '1 hour', 'Active'),
('Air Cooler Repair', 'Repair and maintenance of air coolers', 'Appliance', 300.00, '1 hour', 'Active'),
('Power Tools Repair', 'Repair of power tools - drills, cutters, grinders, etc.', 'Appliance', 400.00, '1-2 hours', 'Active'),
('Water Filter/Purifier Repair', 'Repair and maintenance of water filters and purifiers', 'Appliance', 350.00, '1 hour', 'Active'),

-- 3. INSTALLATION & SETUP - Appliance Setup
('TV/DTH Dish Installation and Tuning', 'Professional TV and DTH dish installation with channel tuning', 'Installation', 400.00, '1-2 hours', 'Active'),
('Electric Chimney Installation', 'Installation of electric kitchen chimneys', 'Installation', 500.00, '1-2 hours', 'Active'),
('Ceiling and Wall Fan Installation', 'Installation of ceiling fans and wall-mounted fans', 'Installation', 300.00, '1 hour', 'Active'),
('Washing Machine Installation and Uninstallation', 'Professional washing machine installation and uninstallation services', 'Installation', 300.00, '1 hour', 'Active'),
('Air Cooler Installation', 'Installation and setup of air coolers', 'Installation', 250.00, '45 mins', 'Active'),
('Water Filter/Purifier Installation', 'Installation of water filters and purifiers', 'Installation', 400.00, '1-2 hours', 'Active'),
('Geyser/Water Heater Installation', 'Professional geyser and water heater installation', 'Installation', 500.00, '1-2 hours', 'Active'),
('Light Fixture Installation', 'Installation of various light fixtures and fittings', 'Installation', 200.00, '1 hour', 'Active'),

-- 3. INSTALLATION & SETUP - Tech & Security
('CCTV and Security Camera Installation', 'Professional CCTV and security camera installation and setup', 'Installation', 1000.00, '2-3 hours', 'Active'),
('Wi-Fi Router and Modem Setup/Troubleshooting', 'Wi-Fi router and modem installation, setup and troubleshooting', 'Installation', 300.00, '1 hour', 'Active'),
('Smart Home Device Installation', 'Installation of smart switches, smart lights and other smart home devices', 'Installation', 500.00, '1-2 hours', 'Active'),

-- 4. SERVICING & MAINTENANCE - Routine Care
('AC Wet and Dry Servicing', 'Complete AC wet and dry servicing for optimal performance', 'Maintenance', 600.00, '1-2 hours', 'Active'),
('Washing Machine General Maintenance and Cleaning', 'General maintenance and deep cleaning of washing machines', 'Maintenance', 400.00, '1 hour', 'Active'),
('Geyser Descaling and Service', 'Descaling and complete servicing of geysers', 'Maintenance', 450.00, '1-2 hours', 'Active'),
('Water Filter Cartridge Replacement and Service', 'Water filter cartridge replacement and general service', 'Maintenance', 350.00, '45 mins', 'Active'),
('Water Tank Cleaning (Manual and Motorized)', 'Professional water tank cleaning - manual and motorized', 'Maintenance', 800.00, '2-3 hours', 'Active'),

-- 5. PLUMBING WORK - Fixtures & Taps
('Tap, Faucet, and Shower Installation/Repair', 'Installation and repair of taps, faucets and showers', 'Plumbing', 250.00, '1 hour', 'Active'),
('Washbasin and Sink Installation/Repair', 'Installation and repair of washbasins and sinks', 'Plumbing', 400.00, '1-2 hours', 'Active'),
('Toilet, Commode, and Flush Tank Installation', 'Installation of toilets, commodes and flush tanks', 'Plumbing', 600.00, '2-3 hours', 'Active');

-- Verify the data
SELECT COUNT(*) as total_services FROM tms_service WHERE s_status = 'Active';
SELECT s_category, COUNT(*) as count FROM tms_service WHERE s_status = 'Active' GROUP BY s_category;
