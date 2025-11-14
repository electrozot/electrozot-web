-- ============================================
-- EXECUTE ALL - Quick Testing Script
-- ElectroZot Database
-- ============================================
-- This script runs both sample data insertion and testing
-- ============================================

USE `electrozot_db`;

-- Show current state before insertion
SELECT '=== BEFORE INSERTION ===' as status;
SELECT 'Current Users' as info, COUNT(*) as count FROM tms_user;
SELECT 'Current Technicians' as info, COUNT(*) as count FROM tms_technician;
SELECT 'Current Bookings' as info, COUNT(*) as count FROM tms_service_booking;

-- ============================================
-- PART 1: INSERT SAMPLE DATA
-- ============================================

-- Insert Users
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

SELECT 'Users Inserted' as status, ROW_COUNT() as rows_affected;

-- Insert Technicians
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

SELECT 'Technicians Inserted' as status, ROW_COUNT() as rows_affected;

-- Show state after insertion
SELECT '=== AFTER INSERTION ===' as status;
SELECT 'Total Users' as info, COUNT(*) as count FROM tms_user;
SELECT 'Total Technicians' as info, COUNT(*) as count FROM tms_technician;

-- Note: Bookings will be inserted in next section with proper user IDs

SELECT '=== SAMPLE DATA INSERTION COMPLETED ===' as status;
SELECT 'Ready to insert bookings' as next_step;
