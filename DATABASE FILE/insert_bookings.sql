-- ============================================
-- INSERT BOOKINGS WITH DYNAMIC USER IDs
-- This script should be run AFTER users and technicians are inserted
-- ============================================

USE `electrozot_db`;

-- Get the starting user ID (should be 14 or higher after sample data)
SET @start_user_id = (SELECT MIN(u_id) FROM tms_user WHERE u_email LIKE '%@email.com');

-- Insert 30 sample bookings with various statuses
INSERT INTO `tms_service_booking` (`sb_user_id`, `sb_service_id`, `sb_technician_id`, `sb_booking_date`, `sb_booking_time`, `sb_address`, `sb_phone`, `sb_description`, `sb_status`, `sb_total_price`, `sb_created_at`) VALUES
-- Pending Bookings (5 records - awaiting assignment)
(@start_user_id + 0, 1, NULL, '2024-12-01', '10:00:00', '123 Main St, Dhaka', '01712345001', 'Need electrical outlet installation in living room', 'Pending', 150.00, '2024-11-10 08:30:00'),
(@start_user_id + 1, 2, NULL, '2024-12-02', '14:00:00', '456 Park Ave, Chittagong', '01712345002', 'Kitchen sink is leaking badly', 'Pending', 120.00, '2024-11-11 09:15:00'),
(@start_user_id + 2, 3, NULL, '2024-12-03', '09:00:00', '789 Lake Rd, Sylhet', '01712345003', 'AC not cooling properly', 'Pending', 200.00, '2024-11-12 10:45:00'),
(@start_user_id + 3, 4, NULL, '2024-12-04', '15:00:00', '321 Hill St, Rajshahi', '01712345004', 'Refrigerator making strange noise', 'Pending', 100.00, '2024-11-13 11:20:00'),
(@start_user_id + 4, 5, NULL, '2024-12-05', '11:00:00', '654 River Rd, Khulna', '01712345005', 'Need door lock repair', 'Pending', 80.00, '2024-11-14 12:00:00'),

-- Assigned Bookings (5 records - technician assigned, not started)
(@start_user_id + 5, 1, 3, '2024-11-20', '10:00:00', '987 Ocean Dr, Barisal', '01712345006', 'Replace circuit breaker', 'Assigned', 150.00, '2024-11-08 13:30:00'),
(@start_user_id + 6, 2, 4, '2024-11-21', '13:00:00', '147 Forest Ave, Rangpur', '01712345007', 'Bathroom pipe replacement', 'Assigned', 120.00, '2024-11-09 14:15:00'),
(@start_user_id + 7, 3, 5, '2024-11-22', '09:30:00', '258 Garden St, Mymensingh', '01712345008', 'HVAC system maintenance', 'Assigned', 200.00, '2024-11-10 15:00:00'),
(@start_user_id + 8, 4, 6, '2024-11-23', '14:30:00', '369 Valley Rd, Comilla', '01712345009', 'Washing machine not spinning', 'Assigned', 100.00, '2024-11-11 16:45:00'),
(@start_user_id + 9, 5, 7, '2024-11-24', '10:30:00', '741 Mountain View, Gazipur', '01712345010', 'Paint bedroom walls', 'Assigned', 80.00, '2024-11-12 17:20:00'),

-- In Progress Bookings (5 records - technician working on it)
(@start_user_id + 10, 1, 8, '2024-11-15', '11:00:00', '852 Sunset Blvd, Narayanganj', '01712345011', 'Install ceiling fan', 'In Progress', 150.00, '2024-11-05 08:00:00'),
(@start_user_id + 11, 2, 9, '2024-11-16', '15:00:00', '963 Sunrise Ave, Jessore', '01712345012', 'Fix water pressure issue', 'In Progress', 120.00, '2024-11-06 09:30:00'),
(@start_user_id + 12, 3, 10, '2024-11-17', '10:00:00', '159 Maple St, Bogura', '01712345013', 'AC filter replacement', 'In Progress', 200.00, '2024-11-07 10:15:00'),
(@start_user_id + 13, 4, 11, '2024-11-18', '13:30:00', '357 Oak Dr, Dinajpur', '01712345014', 'Microwave not heating', 'In Progress', 100.00, '2024-11-08 11:00:00'),
(@start_user_id + 14, 5, 12, '2024-11-19', '09:00:00', '486 Pine Rd, Pabna', '01712345015', 'Fix broken window', 'In Progress', 80.00, '2024-11-09 12:30:00'),

-- Completed Bookings (5 records - successfully finished)
(@start_user_id + 15, 1, 13, '2024-11-01', '10:00:00', '753 Cedar Ave, Sirajganj', '01712345016', 'Electrical wiring for new room', 'Completed', 150.00, '2024-10-25 08:00:00'),
(@start_user_id + 16, 2, 14, '2024-11-02', '14:00:00', '951 Birch St, Tangail', '01712345017', 'Install new water heater', 'Completed', 120.00, '2024-10-26 09:00:00'),
(@start_user_id + 17, 3, 15, '2024-11-03', '11:00:00', '246 Elm Dr, Jamalpur', '01712345018', 'Complete HVAC inspection', 'Completed', 200.00, '2024-10-27 10:00:00'),
(@start_user_id + 18, 4, 16, '2024-11-04', '15:30:00', '135 Willow Rd, Netrokona', '01712345019', 'Repair dishwasher', 'Completed', 100.00, '2024-10-28 11:00:00'),
(@start_user_id + 19, 5, 17, '2024-11-05', '09:30:00', '864 Spruce Ave, Sherpur', '01712345020', 'General home maintenance', 'Completed', 80.00, '2024-10-29 12:00:00'),

-- Rejected Bookings (5 records - need reassignment)
(@start_user_id + 20, 1, 18, '2024-11-25', '10:00:00', '579 Ash St, Kushtia', '01712345021', 'Emergency electrical repair', 'Rejected', 150.00, '2024-11-13 08:00:00'),
(@start_user_id + 21, 2, 19, '2024-11-26', '13:00:00', '792 Poplar Dr, Chuadanga', '01712345022', 'Burst pipe emergency', 'Rejected', 120.00, '2024-11-13 09:00:00'),
(@start_user_id + 22, 3, 20, '2024-11-27', '15:00:00', '318 Cypress Rd, Meherpur', '01712345023', 'AC completely stopped', 'Rejected', 200.00, '2024-11-13 10:00:00'),
(@start_user_id + 23, 4, 21, '2024-11-28', '11:00:00', '426 Redwood Ave, Jhenaidah', '01712345024', 'Oven not working', 'Rejected', 100.00, '2024-11-13 11:00:00'),
(@start_user_id + 24, 5, 22, '2024-11-29', '14:00:00', '537 Sequoia St, Magura', '01712345025', 'Door hinge broken', 'Rejected', 80.00, '2024-11-13 12:00:00'),

-- Cancelled Bookings (5 records - customer cancelled)
(@start_user_id + 0, 1, NULL, '2024-11-30', '10:00:00', '123 Main St, Dhaka', '01712345001', 'Changed mind about installation', 'Cancelled', 150.00, '2024-11-14 08:00:00'),
(@start_user_id + 1, 2, NULL, '2024-12-01', '14:00:00', '456 Park Ave, Chittagong', '01712345002', 'Found another service', 'Cancelled', 120.00, '2024-11-14 09:00:00'),
(@start_user_id + 2, 3, 23, '2024-12-02', '09:00:00', '789 Lake Rd, Sylhet', '01712345003', 'Schedule conflict', 'Cancelled', 200.00, '2024-11-14 10:00:00'),
(@start_user_id + 3, 4, 24, '2024-12-03', '15:00:00', '321 Hill St, Rajshahi', '01712345004', 'Budget constraints', 'Cancelled', 100.00, '2024-11-14 11:00:00'),
(@start_user_id + 4, 5, 25, '2024-12-04', '11:00:00', '654 River Rd, Khulna', '01712345005', 'Postponed indefinitely', 'Cancelled', 80.00, '2024-11-14 12:00:00');

-- Verify insertion
SELECT 'Bookings Inserted' as status, ROW_COUNT() as rows_affected;

-- Show booking status distribution
SELECT sb_status, COUNT(*) as count 
FROM tms_service_booking 
GROUP BY sb_status 
ORDER BY count DESC;

SELECT '=== BOOKING INSERTION COMPLETED ===' as status;
