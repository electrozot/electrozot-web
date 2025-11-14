-- ============================================
-- ADD SERVICES - FINAL VERSION
-- ElectroZot - 8 Service Categories
-- ============================================

USE `electrozot_db`;

-- DELETE ALL OLD SERVICES
DELETE FROM tms_service WHERE s_id > 0;
ALTER TABLE tms_service AUTO_INCREMENT = 1;

-- ============================================
-- INSERT 8 SERVICES
-- ============================================

INSERT INTO tms_service (s_name, s_description, s_category, s_price, s_duration, s_status) VALUES

-- 1. Wiring & Fixtures
('Wiring & Fixtures', 'Home Wiring (New installation and repair), Switch/Socket Installation and Replacement, Light Fixture Installation (Tube lights, LED panels, chandeliers), Light Decoration/Festive Lighting Setup', 'Wiring & Fixtures', 1500.00, '2-4 hours', 'Active'),

-- 2. Safety & Power
('Safety & Power', 'Circuit Breaker and Fuse Box (Main Panel) troubleshooting and repair, Inverter, UPS, and Voltage Stabilizer installation/wiring, Grounding and Earthing system installation, New Electrical Outlet/Point installation, Ceiling Fan Regulator repair/replacement, Electrical fault finding and short-circuit repair', 'Safety & Power', 1200.00, '2-3 hours', 'Active'),

-- 3. Major Appliances
('Major Appliances', 'Air Conditioner (AC) Repair (All types: Split, Window, Central), Refrigerator Repair and Gas Charging, Washing Machine Repair (Semi-automatic, Fully automatic, Front/Top Load), Microwave Oven Repair, Geyser (Water Heater) Repair', 'Major Appliances', 1200.00, '2-3 hours', 'Active'),

-- 4. Small Gadgets
('Small Gadgets', 'Fan Repair (Ceiling, Table, Exhaust), Television (TV) Repair and Troubleshooting, Electric Iron/Press Repair, Music System/Home Theatre Repair, Electric Heater Repair (Room Heaters, Rods), Induction Cooktop and Electric Stove Repair, Air Cooler Repair, Power Tools Repair (Drills, Cutters, Grinders, etc.), Water Filter/Purifier Repair', 'Small Gadgets', 800.00, '1-2 hours', 'Active'),

-- 5. Appliance Setup
('Appliance Setup', 'TV/DTH Dish Installation and Tuning, Electric Chimney Installation, Ceiling and Wall Fan Installation, Washing Machine Installation and Uninstallation, Air Cooler Installation, Water Filter/Purifier Installation, Geyser/Water Heater Installation, Light Fixture Installation', 'Appliance Setup', 800.00, '2-3 hours', 'Active'),

-- 6. Tech & Security
('Tech & Security', 'CCTV and Security Camera Installation, Wi-Fi Router and Modem Setup/Troubleshooting, Smart Home Device Installation (Smart switches, smart lights)', 'Tech & Security', 2000.00, '3-5 hours', 'Active'),

-- 7. Routine Care
('Routine Care', 'AC Wet and Dry Servicing, Washing Machine General Maintenance and Cleaning, Geyser Descaling and Service, Water Filter Cartridge Replacement and General Service, Water Tank Cleaning (Manual and Motorized)', 'Routine Care', 600.00, '1-2 hours', 'Active'),

-- 8. Fixtures & Taps
('Fixtures & Taps', 'Tap, Faucet, and Shower Installation/Repair, Washbasin and Sink Installation/Repair, Toilet, Commode, and Flush Tank Installation/Repair, Bathroom Fitting Installation', 'Fixtures & Taps', 800.00, '2-3 hours', 'Active');

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Services Added Successfully!' as status;

SELECT s_id, s_name, s_category, s_price, s_duration 
FROM tms_service 
ORDER BY s_id;

SELECT s_category, COUNT(*) as service_count 
FROM tms_service 
GROUP BY s_category;

-- ============================================
-- SUMMARY
-- ============================================
-- Total Services: 8
-- 
-- 1. Wiring & Fixtures - ৳1,500
-- 2. Safety & Power - ৳1,200
-- 3. Major Appliances - ৳1,200
-- 4. Small Gadgets - ৳800
-- 5. Appliance Setup - ৳800
-- 6. Tech & Security - ৳2,000
-- 7. Routine Care - ৳600
-- 8. Fixtures & Taps - ৳800
-- ============================================
