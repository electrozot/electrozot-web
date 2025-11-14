-- ============================================
-- SAMPLE DATA INSERT SCRIPT
-- ElectroZot Database - Testing Data
-- ============================================
-- This script adds 20+ sample records for:
-- 1. Users/Clients (tms_user)
-- 2. Technicians (tms_technician)
-- 3. Bookings (tms_service_booking)
-- ============================================

USE `electrozot_db`;

-- ============================================
-- 1. INSERT SAMPLE USERS/CLIENTS (25 records)
-- ============================================

INSERT INTO `tms_user` (`u_fname`, `u_lname`, `u_phone`, `u_addr`, `u_category`, `u_email`, `u_pwd`, `t_tech_category`, `t_tech_id`, `t_booking_date`, `t_booking_status`) VALUES
('John', 'Anderson', '01712345001', '123 Main St, Dhaka', 'User', 'john.anderson@email.com', '123456', '', '', '', ''),
('Sarah', 'Williams', '01712345002', '456 Park Ave, Chittagong', 'User', 'sarah.williams@email.com', '123456', '', '', '', ''),
('Michael', 'Brown', '01712345003', '789 Lake Rd, Sylhet', 'User', 'michael.brown@email.com', '123456', '', '', '', ''),
('Emily', 'Davis', '01712345004', '321 Hill St, Rajshahi', 'User', 'emily.davis@email.com', '123456', '', '', '', ''),
('David', 'Miller', '01712345005', '654 River Rd, Khulna', 'User', 'david.miller@email.com', '123456', '', '', '', ''),
('Jessica', 'Wilson', '01712345006', '987 Ocean Dr, Barisal', 'User', 'jessica.wilson@email.com', '123456', '', '', '', ''),
('James', 'Moore', '01712345007', '147 Forest Ave, Rangpur', 'User', 'james.moore@email.com', '123456', '', '', '', ''),
('Jennifer', 'Taylor', '01712345008', '258 Garden St, Mymensingh', 'User', 'jennifer.taylor@email.com', '123456', '', '', '', ''),
('Robert', 'Thomas', '01712345009', '369 Valley Rd, Comilla', 'User', 'robert.thomas@email.com', '123456', '', '', '', ''),
('Linda', 'Jackson', '01712345010', '741 Mountain View, Gazipur', 'User', 'linda.jackson@email.com', '123456', '', '', '', ''),
('William', 'White', '01712345011', '852 Sunset Blvd, Narayanganj', 'User', 'william.white@email.com', '123456', '', '', '', ''),
('Mary', 'Harris', '01712345012', '963 Sunrise Ave, Jessore', 'User', 'mary.harris@email.com', '123456', '', '', '', ''),
('Richard', 'Martin', '01712345013', '159 Maple St, Bogura', 'User', 'richard.martin@email.com', '123456', '', '', '', ''),
('Patricia', 'Thompson', '01712345014', '357 Oak Dr, Dinajpur', 'User', 'patricia.thompson@email.com', '123456', '', '', '', ''),
('Charles', 'Garcia', '01712345015', '486 Pine Rd, Pabna', 'User', 'charles.garcia@email.com', '123456', '', '', '', ''),
('Barbara', 'Martinez', '01712345016', '753 Cedar Ave, Sirajganj', 'User', 'barbara.martinez@email.com', '123456', '', '', '', ''),
('Joseph', 'Robinson', '01712345017', '951 Birch St, Tangail', 'User', 'joseph.robinson@email.com', '123456', '', '', '', ''),
('Susan', 'Clark', '01712345018', '246 Elm Dr, Jamalpur', 'User', 'susan.clark@email.com', '123456', '', '', '', ''),
('Thomas', 'Rodriguez', '01712345019', '135 Willow Rd, Netrokona', 'User', 'thomas.rodriguez@email.com', '123456', '', '', '', ''),
('Karen', 'Lewis', '01712345020', '864 Spruce Ave, Sherpur', 'User', 'karen.lewis@email.com', '123456', '', '', '', ''),
('Daniel', 'Lee', '01712345021', '579 Ash St, Kushtia', 'User', 'daniel.lee@email.com', '123456', '', '', '', ''),
('Nancy', 'Walker', '01712345022', '792 Poplar Dr, Chuadanga', 'User', 'nancy.walker@email.com', '123456', '', '', '', ''),
('Matthew', 'Hall', '01712345023', '318 Cypress Rd, Meherpur', 'User', 'matthew.hall@email.com', '123456', '', '', '', ''),
('Betty', 'Allen', '01712345024', '426 Redwood Ave, Jhenaidah', 'User', 'betty.allen@email.com', '123456', '', '', '', ''),
('Anthony', 'Young', '01712345025', '537 Sequoia St, Magura', 'User', 'anthony.young@email.com', '123456', '', '', '', '');

-- ============================================
-- 2. INSERT SAMPLE TECHNICIANS (25 records)
-- ============================================

INSERT INTO `tms_technician` (`t_name`, `t_id_no`, `t_experience`, `t_specialization`, `t_category`, `t_pic`, `t_status`) VALUES
('Ahmed Hassan', 'TECH006', '8', 'Electrical Wiring & Circuits', 'Electrical', 'tech6.jpg', 'Available'),
('Karim Rahman', 'TECH007', '12', 'Pipe Installation & Repair', 'Plumbing', 'tech7.jpg', 'Available'),
('Rahim Ali', 'TECH008', '6', 'AC Installation & Service', 'HVAC', 'tech8.jpg', 'Available'),
('Jamal Uddin', 'TECH009', '10', 'Refrigerator Repair', 'Appliance', 'tech9.jpg', 'Available'),
('Faruk Islam', 'TECH010', '5', 'Home Maintenance', 'General', 'tech10.jpg', 'Available'),
('Shakib Khan', 'TECH011', '15', 'Industrial Electrical', 'Electrical', 'tech11.jpg', 'Available'),
('Masud Rana', 'TECH012', '9', 'Water Heater Specialist', 'Plumbing', 'tech12.jpg', 'Available'),
('Rafiq Ahmed', 'TECH013', '7', 'Central Heating Systems', 'HVAC', 'tech13.jpg', 'Available'),
('Habib Mia', 'TECH014', '11', 'Washing Machine Expert', 'Appliance', 'tech14.jpg', 'Available'),
('Salam Sheikh', 'TECH015', '4', 'Carpentry & Painting', 'General', 'tech15.jpg', 'Available'),
('Nasir Hossain', 'TECH016', '13', 'Smart Home Electrical', 'Electrical', 'tech16.jpg', 'Available'),
('Babul Miah', 'TECH017', '8', 'Drainage Systems', 'Plumbing', 'tech17.jpg', 'Available'),
('Kamrul Islam', 'TECH018', '10', 'Ventilation Expert', 'HVAC', 'tech18.jpg', 'Available'),
('Shafiq Ullah', 'TECH019', '6', 'Microwave & Oven Repair', 'Appliance', 'tech19.jpg', 'Available'),
('Monir Hossain', 'TECH020', '9', 'Flooring & Tiling', 'General', 'tech20.jpg', 'Available'),
('Aziz Rahman', 'TECH021', '14', 'Solar Panel Installation', 'Electrical', 'tech21.jpg', 'Available'),
('Belal Ahmed', 'TECH022', '7', 'Bathroom Plumbing', 'Plumbing', 'tech22.jpg', 'Available'),
('Delwar Hossain', 'TECH023', '12', 'Commercial HVAC', 'HVAC', 'tech23.jpg', 'Available'),
('Enamul Haque', 'TECH024', '5', 'Dishwasher Specialist', 'Appliance', 'tech24.jpg', 'Available'),
('Fazlul Karim', 'TECH025', '8', 'Door & Window Repair', 'General', 'tech25.jpg', 'Available'),
('Golam Mostafa', 'TECH026', '11', 'Generator Installation', 'Electrical', 'tech26.jpg', 'Available'),
('Hanif Molla', 'TECH027', '6', 'Kitchen Plumbing', 'Plumbing', 'tech27.jpg', 'Available'),
('Iqbal Hossain', 'TECH028', '9', 'Duct Cleaning', 'HVAC', 'tech28.jpg', 'Available'),
('Jalal Uddin', 'TECH029', '13', 'TV & Electronics Repair', 'Appliance', 'tech29.jpg', 'Available'),
('Khalid Mahmud', 'TECH030', '7', 'Roofing & Gutter', 'General', 'tech30.jpg', 'Available');


-- ============================================
-- 3. INSERT SAMPLE BOOKINGS (30 records)
-- ============================================
-- Mix of statuses: Pending, Assigned, In Progress, Completed, Rejected, Cancelled
-- Dates: Mix of past, current, and future dates

INSERT INTO `tms_service_booking` (`sb_user_id`, `sb_service_id`, `sb_technician_id`, `sb_booking_date`, `sb_booking_time`, `sb_address`, `sb_phone`, `sb_description`, `sb_status`, `sb_total_price`, `sb_created_at`) VALUES
-- Pending Bookings (awaiting assignment)
(14, 1, NULL, '2024-12-01', '10:00:00', '123 Main St, Dhaka', '01712345001', 'Need electrical outlet installation in living room', 'Pending', 150.00, '2024-11-10 08:30:00'),
(15, 2, NULL, '2024-12-02', '14:00:00', '456 Park Ave, Chittagong', '01712345002', 'Kitchen sink is leaking badly', 'Pending', 120.00, '2024-11-11 09:15:00'),
(16, 3, NULL, '2024-12-03', '09:00:00', '789 Lake Rd, Sylhet', '01712345003', 'AC not cooling properly', 'Pending', 200.00, '2024-11-12 10:45:00'),
(17, 4, NULL, '2024-12-04', '15:00:00', '321 Hill St, Rajshahi', '01712345004', 'Refrigerator making strange noise', 'Pending', 100.00, '2024-11-13 11:20:00'),
(18, 5, NULL, '2024-12-05', '11:00:00', '654 River Rd, Khulna', '01712345005', 'Need door lock repair', 'Pending', 80.00, '2024-11-14 12:00:00'),

-- Assigned Bookings (technician assigned, not started)
(19, 1, 3, '2024-11-20', '10:00:00', '987 Ocean Dr, Barisal', '01712345006', 'Replace circuit breaker', 'Assigned', 150.00, '2024-11-08 13:30:00'),
(20, 2, 4, '2024-11-21', '13:00:00', '147 Forest Ave, Rangpur', '01712345007', 'Bathroom pipe replacement', 'Assigned', 120.00, '2024-11-09 14:15:00'),
(21, 3, 5, '2024-11-22', '09:30:00', '258 Garden St, Mymensingh', '01712345008', 'HVAC system maintenance', 'Assigned', 200.00, '2024-11-10 15:00:00'),
(22, 4, 6, '2024-11-23', '14:30:00', '369 Valley Rd, Comilla', '01712345009', 'Washing machine not spinning', 'Assigned', 100.00, '2024-11-11 16:45:00'),
(23, 5, 7, '2024-11-24', '10:30:00', '741 Mountain View, Gazipur', '01712345010', 'Paint bedroom walls', 'Assigned', 80.00, '2024-11-12 17:20:00'),

-- In Progress Bookings (technician working on it)
(24, 1, 8, '2024-11-15', '11:00:00', '852 Sunset Blvd, Narayanganj', '01712345011', 'Install ceiling fan', 'In Progress', 150.00, '2024-11-05 08:00:00'),
(25, 2, 9, '2024-11-16', '15:00:00', '963 Sunrise Ave, Jessore', '01712345012', 'Fix water pressure issue', 'In Progress', 120.00, '2024-11-06 09:30:00'),
(26, 3, 10, '2024-11-17', '10:00:00', '159 Maple St, Bogura', '01712345013', 'AC filter replacement', 'In Progress', 200.00, '2024-11-07 10:15:00'),
(27, 4, 11, '2024-11-18', '13:30:00', '357 Oak Dr, Dinajpur', '01712345014', 'Microwave not heating', 'In Progress', 100.00, '2024-11-08 11:00:00'),
(28, 5, 12, '2024-11-19', '09:00:00', '486 Pine Rd, Pabna', '01712345015', 'Fix broken window', 'In Progress', 80.00, '2024-11-09 12:30:00'),

-- Completed Bookings (successfully finished)
(29, 1, 13, '2024-11-01', '10:00:00', '753 Cedar Ave, Sirajganj', '01712345016', 'Electrical wiring for new room', 'Completed', 150.00, '2024-10-25 08:00:00'),
(30, 2, 14, '2024-11-02', '14:00:00', '951 Birch St, Tangail', '01712345017', 'Install new water heater', 'Completed', 120.00, '2024-10-26 09:00:00'),
(31, 3, 15, '2024-11-03', '11:00:00', '246 Elm Dr, Jamalpur', '01712345018', 'Complete HVAC inspection', 'Completed', 200.00, '2024-10-27 10:00:00'),
(32, 4, 16, '2024-11-04', '15:30:00', '135 Willow Rd, Netrokona', '01712345019', 'Repair dishwasher', 'Completed', 100.00, '2024-10-28 11:00:00'),
(33, 5, 17, '2024-11-05', '09:30:00', '864 Spruce Ave, Sherpur', '01712345020', 'General home maintenance', 'Completed', 80.00, '2024-10-29 12:00:00'),

-- Rejected Bookings (need reassignment)
(34, 1, 18, '2024-11-25', '10:00:00', '579 Ash St, Kushtia', '01712345021', 'Emergency electrical repair', 'Rejected', 150.00, '2024-11-13 08:00:00'),
(35, 2, 19, '2024-11-26', '13:00:00', '792 Poplar Dr, Chuadanga', '01712345022', 'Burst pipe emergency', 'Rejected', 120.00, '2024-11-13 09:00:00'),
(36, 3, 20, '2024-11-27', '15:00:00', '318 Cypress Rd, Meherpur', '01712345023', 'AC completely stopped', 'Rejected', 200.00, '2024-11-13 10:00:00'),
(37, 4, 21, '2024-11-28', '11:00:00', '426 Redwood Ave, Jhenaidah', '01712345024', 'Oven not working', 'Rejected', 100.00, '2024-11-13 11:00:00'),
(38, 5, 22, '2024-11-29', '14:00:00', '537 Sequoia St, Magura', '01712345025', 'Door hinge broken', 'Rejected', 80.00, '2024-11-13 12:00:00'),

-- Cancelled Bookings (customer cancelled)
(14, 1, NULL, '2024-11-30', '10:00:00', '123 Main St, Dhaka', '01712345001', 'Changed mind about installation', 'Cancelled', 150.00, '2024-11-14 08:00:00'),
(15, 2, NULL, '2024-12-01', '14:00:00', '456 Park Ave, Chittagong', '01712345002', 'Found another service', 'Cancelled', 120.00, '2024-11-14 09:00:00'),
(16, 3, 23, '2024-12-02', '09:00:00', '789 Lake Rd, Sylhet', '01712345003', 'Schedule conflict', 'Cancelled', 200.00, '2024-11-14 10:00:00'),
(17, 4, 24, '2024-12-03', '15:00:00', '321 Hill St, Rajshahi', '01712345004', 'Budget constraints', 'Cancelled', 100.00, '2024-11-14 11:00:00'),
(18, 5, 25, '2024-12-04', '11:00:00', '654 River Rd, Khulna', '01712345005', 'Postponed indefinitely', 'Cancelled', 80.00, '2024-11-14 12:00:00');

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Count total users
SELECT COUNT(*) as total_users FROM tms_user;

-- Count total technicians
SELECT COUNT(*) as total_technicians FROM tms_technician;

-- Count total bookings
SELECT COUNT(*) as total_bookings FROM tms_service_booking;

-- Bookings by status
SELECT sb_status, COUNT(*) as count 
FROM tms_service_booking 
GROUP BY sb_status 
ORDER BY count DESC;

-- Technicians by category
SELECT t_category, COUNT(*) as count 
FROM tms_technician 
GROUP BY t_category 
ORDER BY count DESC;

-- Services with booking count
SELECT s.s_name, s.s_category, COUNT(sb.sb_id) as booking_count
FROM tms_service s
LEFT JOIN tms_service_booking sb ON s.s_id = sb.sb_service_id
GROUP BY s.s_id
ORDER BY booking_count DESC;

-- ============================================
-- END OF SAMPLE DATA INSERT SCRIPT
-- ============================================
