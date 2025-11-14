-- ============================================
-- UPDATE CATEGORIES - COMPLETE
-- ElectroZot - New 8 Categories
-- ============================================
-- This script:
-- 1. Deletes old services
-- 2. Adds 8 new services with new categories
-- 3. Updates existing technicians to new categories
-- ============================================

USE `electrozot_db`;

-- ============================================
-- STEP 1: DELETE OLD SERVICES
-- ============================================
DELETE FROM tms_service WHERE s_id > 0;
ALTER TABLE tms_service AUTO_INCREMENT = 1;

-- ============================================
-- STEP 2: ADD 8 NEW SERVICES
-- ============================================

INSERT INTO tms_service (s_name, s_description, s_category, s_price, s_duration, s_status) VALUES

('Wiring & Fixtures', 'Home Wiring (New installation and repair), Switch/Socket Installation and Replacement, Light Fixture Installation (Tube lights, LED panels, chandeliers), Light Decoration/Festive Lighting Setup', 'Wiring & Fixtures', 1500.00, '2-4 hours', 'Active'),

('Safety & Power', 'Circuit Breaker and Fuse Box (Main Panel) troubleshooting and repair, Inverter, UPS, and Voltage Stabilizer installation/wiring, Grounding and Earthing system installation, New Electrical Outlet/Point installation, Ceiling Fan Regulator repair/replacement, Electrical fault finding and short-circuit repair', 'Safety & Power', 1200.00, '2-3 hours', 'Active'),

('Major Appliances', 'Air Conditioner (AC) Repair (All types: Split, Window, Central), Refrigerator Repair and Gas Charging, Washing Machine Repair (Semi-automatic, Fully automatic, Front/Top Load), Microwave Oven Repair, Geyser (Water Heater) Repair', 'Major Appliances', 1200.00, '2-3 hours', 'Active'),

('Small Gadgets', 'Fan Repair (Ceiling, Table, Exhaust), Television (TV) Repair and Troubleshooting, Electric Iron/Press Repair, Music System/Home Theatre Repair, Electric Heater Repair (Room Heaters, Rods), Induction Cooktop and Electric Stove Repair, Air Cooler Repair, Power Tools Repair (Drills, Cutters, Grinders, etc.), Water Filter/Purifier Repair', 'Small Gadgets', 800.00, '1-2 hours', 'Active'),

('Appliance Setup', 'TV/DTH Dish Installation and Tuning, Electric Chimney Installation, Ceiling and Wall Fan Installation, Washing Machine Installation and Uninstallation, Air Cooler Installation, Water Filter/Purifier Installation, Geyser/Water Heater Installation, Light Fixture Installation', 'Appliance Setup', 800.00, '2-3 hours', 'Active'),

('Tech & Security', 'CCTV and Security Camera Installation, Wi-Fi Router and Modem Setup/Troubleshooting, Smart Home Device Installation (Smart switches, smart lights)', 'Tech & Security', 2000.00, '3-5 hours', 'Active'),

('Routine Care', 'AC Wet and Dry Servicing, Washing Machine General Maintenance and Cleaning, Geyser Descaling and Service, Water Filter Cartridge Replacement and General Service, Water Tank Cleaning (Manual and Motorized)', 'Routine Care', 600.00, '1-2 hours', 'Active'),

('Fixtures & Taps', 'Tap, Faucet, and Shower Installation/Repair, Washbasin and Sink Installation/Repair, Toilet, Commode, and Flush Tank Installation/Repair, Bathroom Fitting Installation', 'Fixtures & Taps', 800.00, '2-3 hours', 'Active');

-- ============================================
-- STEP 3: UPDATE EXISTING TECHNICIANS
-- ============================================
-- Map old categories to new categories

-- Electrical → Wiring & Fixtures and Safety & Power
UPDATE tms_technician SET t_category = 'Wiring & Fixtures' WHERE t_category = 'Electrical' AND t_id % 2 = 1;
UPDATE tms_technician SET t_category = 'Safety & Power' WHERE t_category = 'Electrical' AND t_id % 2 = 0;

-- Appliance → Major Appliances and Small Gadgets
UPDATE tms_technician SET t_category = 'Major Appliances' WHERE t_category = 'Appliance' AND t_id % 2 = 0;
UPDATE tms_technician SET t_category = 'Small Gadgets' WHERE t_category = 'Appliance' AND t_id % 2 = 1;

-- General → Appliance Setup, Tech & Security, Routine Care
UPDATE tms_technician SET t_category = 'Appliance Setup' WHERE t_category = 'General' AND t_id % 3 = 0;
UPDATE tms_technician SET t_category = 'Tech & Security' WHERE t_category = 'General' AND t_id % 3 = 1;
UPDATE tms_technician SET t_category = 'Routine Care' WHERE t_category = 'General' AND t_id % 3 = 2;

-- Plumbing → Fixtures & Taps
UPDATE tms_technician SET t_category = 'Fixtures & Taps' WHERE t_category = 'Plumbing';

-- HVAC → Routine Care
UPDATE tms_technician SET t_category = 'Routine Care' WHERE t_category = 'HVAC';

-- ============================================
-- VERIFICATION
-- ============================================

SELECT '=== SERVICES ADDED ===' as status;
SELECT s_id, s_name, s_category, s_price FROM tms_service ORDER BY s_id;

SELECT '=== TECHNICIANS UPDATED ===' as status;
SELECT t_id, t_name, t_category, t_status FROM tms_technician ORDER BY t_category, t_name;

SELECT '=== CATEGORY SUMMARY ===' as status;
SELECT 
    s.s_category,
    COUNT(DISTINCT s.s_id) as services,
    COUNT(DISTINCT t.t_id) as technicians
FROM tms_service s
LEFT JOIN tms_technician t ON s.s_category = t.t_category
GROUP BY s.s_category
ORDER BY s.s_category;

-- ============================================
-- SUMMARY
-- ============================================
-- 8 New Service Categories:
-- 1. Wiring & Fixtures
-- 2. Safety & Power
-- 3. Major Appliances
-- 4. Small Gadgets
-- 5. Appliance Setup
-- 6. Tech & Security
-- 7. Routine Care
-- 8. Fixtures & Taps
--
-- Old categories (Electrical, Plumbing, HVAC, Appliance, General)
-- have been mapped to new categories
-- ============================================
