-- ============================================
-- DATABASE LOGIC TESTING SCRIPT
-- ElectroZot - Comprehensive Testing
-- ============================================
-- This script tests all critical database operations
-- ============================================

USE `electrozot_db`;

-- ============================================
-- TEST 1: USER MANAGEMENT
-- ============================================
SELECT '=== TEST 1: USER MANAGEMENT ===' as test_section;

-- Test 1.1: Count all users
SELECT 'Test 1.1: Total Users' as test_name, COUNT(*) as result FROM tms_user;

-- Test 1.2: Verify user categories
SELECT 'Test 1.2: Users by Category' as test_name, u_category, COUNT(*) as count 
FROM tms_user 
GROUP BY u_category;

-- Test 1.3: Check for duplicate emails
SELECT 'Test 1.3: Duplicate Emails Check' as test_name, u_email, COUNT(*) as count 
FROM tms_user 
GROUP BY u_email 
HAVING COUNT(*) > 1;

-- Test 1.4: Verify phone number format
SELECT 'Test 1.4: Invalid Phone Numbers' as test_name, u_fname, u_lname, u_phone 
FROM tms_user 
WHERE LENGTH(u_phone) != 11 OR u_phone NOT LIKE '017%';

-- ============================================
-- TEST 2: TECHNICIAN MANAGEMENT
-- ============================================
SELECT '=== TEST 2: TECHNICIAN MANAGEMENT ===' as test_section;

-- Test 2.1: Count all technicians
SELECT 'Test 2.1: Total Technicians' as test_name, COUNT(*) as result FROM tms_technician;

-- Test 2.2: Technicians by category
SELECT 'Test 2.2: Technicians by Category' as test_name, t_category, COUNT(*) as count 
FROM tms_technician 
GROUP BY t_category 
ORDER BY count DESC;

-- Test 2.3: Technicians by status
SELECT 'Test 2.3: Technicians by Status' as test_name, t_status, COUNT(*) as count 
FROM tms_technician 
GROUP BY t_status;

-- Test 2.4: Check for duplicate technician IDs
SELECT 'Test 2.4: Duplicate Tech IDs' as test_name, t_id_no, COUNT(*) as count 
FROM tms_technician 
GROUP BY t_id_no 
HAVING COUNT(*) > 1;

-- Test 2.5: Average experience by category
SELECT 'Test 2.5: Avg Experience by Category' as test_name, 
       t_category, 
       ROUND(AVG(CAST(t_experience AS DECIMAL(10,2))), 2) as avg_experience 
FROM tms_technician 
GROUP BY t_category 
ORDER BY avg_experience DESC;

-- ============================================
-- TEST 3: SERVICE MANAGEMENT
-- ============================================
SELECT '=== TEST 3: SERVICE MANAGEMENT ===' as test_section;

-- Test 3.1: All active services
SELECT 'Test 3.1: Active Services' as test_name, s_name, s_category, s_price, s_status 
FROM tms_service 
WHERE s_status = 'Active';

-- Test 3.2: Services by category
SELECT 'Test 3.2: Services by Category' as test_name, s_category, COUNT(*) as count 
FROM tms_service 
GROUP BY s_category;

-- Test 3.3: Price range analysis
SELECT 'Test 3.3: Service Price Range' as test_name, 
       MIN(s_price) as min_price, 
       MAX(s_price) as max_price, 
       AVG(s_price) as avg_price 
FROM tms_service;

-- ============================================
-- TEST 4: BOOKING MANAGEMENT
-- ============================================
SELECT '=== TEST 4: BOOKING MANAGEMENT ===' as test_section;

-- Test 4.1: Total bookings
SELECT 'Test 4.1: Total Bookings' as test_name, COUNT(*) as result FROM tms_service_booking;

-- Test 4.2: Bookings by status
SELECT 'Test 4.2: Bookings by Status' as test_name, sb_status, COUNT(*) as count 
FROM tms_service_booking 
GROUP BY sb_status 
ORDER BY count DESC;

-- Test 4.3: Bookings by service
SELECT 'Test 4.3: Bookings by Service' as test_name, 
       s.s_name, 
       COUNT(sb.sb_id) as booking_count 
FROM tms_service s
LEFT JOIN tms_service_booking sb ON s.s_id = sb.sb_service_id
GROUP BY s.s_id
ORDER BY booking_count DESC;

-- Test 4.4: Revenue by service
SELECT 'Test 4.4: Revenue by Service' as test_name, 
       s.s_name, 
       SUM(sb.sb_total_price) as total_revenue,
       COUNT(sb.sb_id) as booking_count
FROM tms_service s
LEFT JOIN tms_service_booking sb ON s.s_id = sb.sb_service_id
GROUP BY s.s_id
ORDER BY total_revenue DESC;

-- Test 4.5: Total revenue
SELECT 'Test 4.5: Total Revenue' as test_name, 
       SUM(sb_total_price) as total_revenue 
FROM tms_service_booking;

-- Test 4.6: Revenue by status
SELECT 'Test 4.6: Revenue by Status' as test_name, 
       sb_status, 
       SUM(sb_total_price) as revenue,
       COUNT(*) as booking_count
FROM tms_service_booking 
GROUP BY sb_status 
ORDER BY revenue DESC;

-- ============================================
-- TEST 5: BOOKING-USER RELATIONSHIPS
-- ============================================
SELECT '=== TEST 5: BOOKING-USER RELATIONSHIPS ===' as test_section;

-- Test 5.1: Users with most bookings
SELECT 'Test 5.1: Top 10 Users by Bookings' as test_name,
       u.u_fname, 
       u.u_lname, 
       u.u_email,
       COUNT(sb.sb_id) as booking_count
FROM tms_user u
LEFT JOIN tms_service_booking sb ON u.u_id = sb.sb_user_id
GROUP BY u.u_id
ORDER BY booking_count DESC
LIMIT 10;

-- Test 5.2: Users with no bookings
SELECT 'Test 5.2: Users with No Bookings' as test_name,
       u.u_fname, 
       u.u_lname, 
       u.u_email
FROM tms_user u
LEFT JOIN tms_service_booking sb ON u.u_id = sb.sb_user_id
WHERE sb.sb_id IS NULL;

-- Test 5.3: Customer spending analysis
SELECT 'Test 5.3: Top 10 Customers by Spending' as test_name,
       u.u_fname, 
       u.u_lname,
       SUM(sb.sb_total_price) as total_spent,
       COUNT(sb.sb_id) as booking_count
FROM tms_user u
INNER JOIN tms_service_booking sb ON u.u_id = sb.sb_user_id
GROUP BY u.u_id
ORDER BY total_spent DESC
LIMIT 10;

-- ============================================
-- TEST 6: BOOKING-TECHNICIAN RELATIONSHIPS
-- ============================================
SELECT '=== TEST 6: BOOKING-TECHNICIAN RELATIONSHIPS ===' as test_section;

-- Test 6.1: Technicians with most bookings
SELECT 'Test 6.1: Top 10 Technicians by Bookings' as test_name,
       t.t_name, 
       t.t_category,
       COUNT(sb.sb_id) as booking_count
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
GROUP BY t.t_id
ORDER BY booking_count DESC
LIMIT 10;

-- Test 6.2: Technicians with no bookings
SELECT 'Test 6.2: Technicians with No Bookings' as test_name,
       t.t_name, 
       t.t_category,
       t.t_status
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
WHERE sb.sb_id IS NULL;

-- Test 6.3: Technician workload by status
SELECT 'Test 6.3: Technician Workload by Status' as test_name,
       t.t_name,
       t.t_category,
       sb.sb_status,
       COUNT(sb.sb_id) as count
FROM tms_technician t
INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
GROUP BY t.t_id, sb.sb_status
ORDER BY t.t_name, sb.sb_status;

-- Test 6.4: Technician revenue generation
SELECT 'Test 6.4: Top 10 Technicians by Revenue' as test_name,
       t.t_name,
       t.t_category,
       SUM(sb.sb_total_price) as total_revenue,
       COUNT(sb.sb_id) as booking_count
FROM tms_technician t
INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
GROUP BY t.t_id
ORDER BY total_revenue DESC
LIMIT 10;

-- ============================================
-- TEST 7: PENDING & REJECTED BOOKINGS
-- ============================================
SELECT '=== TEST 7: PENDING & REJECTED BOOKINGS ===' as test_section;

-- Test 7.1: All pending bookings (need assignment)
SELECT 'Test 7.1: Pending Bookings' as test_name,
       sb.sb_id,
       u.u_fname,
       u.u_lname,
       s.s_name,
       sb.sb_booking_date,
       sb.sb_status
FROM tms_service_booking sb
INNER JOIN tms_user u ON sb.sb_user_id = u.u_id
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
WHERE sb.sb_status = 'Pending'
ORDER BY sb.sb_booking_date;

-- Test 7.2: All rejected bookings (need reassignment)
SELECT 'Test 7.2: Rejected Bookings' as test_name,
       sb.sb_id,
       u.u_fname,
       u.u_lname,
       s.s_name,
       s.s_category,
       t.t_name as rejected_by,
       sb.sb_booking_date
FROM tms_service_booking sb
INNER JOIN tms_user u ON sb.sb_user_id = u.u_id
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
WHERE sb.sb_status = 'Rejected'
ORDER BY sb.sb_booking_date;

-- Test 7.3: Available technicians for rejected bookings
SELECT 'Test 7.3: Available Techs for Rejected Bookings' as test_name,
       s.s_category,
       COUNT(DISTINCT t.t_id) as available_technicians
FROM tms_service_booking sb
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
CROSS JOIN tms_technician t
WHERE sb.sb_status = 'Rejected'
  AND t.t_category = s.s_category
  AND t.t_status = 'Available'
GROUP BY s.s_category;

-- ============================================
-- TEST 8: DATE & TIME ANALYSIS
-- ============================================
SELECT '=== TEST 8: DATE & TIME ANALYSIS ===' as test_section;

-- Test 8.1: Bookings by date
SELECT 'Test 8.1: Bookings by Date' as test_name,
       sb_booking_date,
       COUNT(*) as booking_count
FROM tms_service_booking
GROUP BY sb_booking_date
ORDER BY sb_booking_date DESC
LIMIT 10;

-- Test 8.2: Bookings by time slot
SELECT 'Test 8.2: Bookings by Time Slot' as test_name,
       HOUR(sb_booking_time) as hour,
       COUNT(*) as booking_count
FROM tms_service_booking
GROUP BY HOUR(sb_booking_time)
ORDER BY hour;

-- Test 8.3: Future bookings
SELECT 'Test 8.3: Future Bookings' as test_name,
       COUNT(*) as count
FROM tms_service_booking
WHERE sb_booking_date > CURDATE();

-- Test 8.4: Past bookings
SELECT 'Test 8.4: Past Bookings' as test_name,
       COUNT(*) as count
FROM tms_service_booking
WHERE sb_booking_date < CURDATE();

-- ============================================
-- TEST 9: DATA INTEGRITY CHECKS
-- ============================================
SELECT '=== TEST 9: DATA INTEGRITY CHECKS ===' as test_section;

-- Test 9.1: Orphaned bookings (invalid user_id)
SELECT 'Test 9.1: Orphaned Bookings (Invalid User)' as test_name,
       sb.sb_id,
       sb.sb_user_id
FROM tms_service_booking sb
LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
WHERE u.u_id IS NULL;

-- Test 9.2: Orphaned bookings (invalid service_id)
SELECT 'Test 9.2: Orphaned Bookings (Invalid Service)' as test_name,
       sb.sb_id,
       sb.sb_service_id
FROM tms_service_booking sb
LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
WHERE s.s_id IS NULL;

-- Test 9.3: Bookings with invalid technician assignment
SELECT 'Test 9.3: Invalid Technician Assignments' as test_name,
       sb.sb_id,
       sb.sb_technician_id
FROM tms_service_booking sb
LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
WHERE sb.sb_technician_id IS NOT NULL 
  AND t.t_id IS NULL;

-- Test 9.4: Assigned bookings without technician
SELECT 'Test 9.4: Assigned Bookings Without Technician' as test_name,
       sb.sb_id,
       sb.sb_status
FROM tms_service_booking sb
WHERE sb.sb_status IN ('Assigned', 'In Progress', 'Completed')
  AND sb.sb_technician_id IS NULL;

-- ============================================
-- TEST 10: BUSINESS LOGIC VALIDATION
-- ============================================
SELECT '=== TEST 10: BUSINESS LOGIC VALIDATION ===' as test_section;

-- Test 10.1: Category mismatch (technician category vs service category)
SELECT 'Test 10.1: Category Mismatch' as test_name,
       sb.sb_id,
       s.s_category as service_category,
       t.t_category as technician_category
FROM tms_service_booking sb
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
INNER JOIN tms_technician t ON sb.sb_technician_id = t.t_id
WHERE s.s_category != t.t_category;

-- Test 10.2: Price consistency check
SELECT 'Test 10.2: Price Mismatch' as test_name,
       sb.sb_id,
       s.s_price as service_price,
       sb.sb_total_price as booking_price
FROM tms_service_booking sb
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
WHERE s.s_price != sb.sb_total_price;

-- Test 10.3: Booking status flow validation
SELECT 'Test 10.3: Valid Status Values' as test_name,
       sb_status,
       COUNT(*) as count
FROM tms_service_booking
WHERE sb_status NOT IN ('Pending', 'Assigned', 'In Progress', 'Completed', 'Rejected', 'Cancelled')
GROUP BY sb_status;

-- ============================================
-- TEST 11: SUMMARY STATISTICS
-- ============================================
SELECT '=== TEST 11: SUMMARY STATISTICS ===' as test_section;

-- Test 11.1: Overall system summary
SELECT 'Test 11.1: System Summary' as test_name,
       (SELECT COUNT(*) FROM tms_user) as total_users,
       (SELECT COUNT(*) FROM tms_technician) as total_technicians,
       (SELECT COUNT(*) FROM tms_service) as total_services,
       (SELECT COUNT(*) FROM tms_service_booking) as total_bookings,
       (SELECT SUM(sb_total_price) FROM tms_service_booking) as total_revenue;

-- Test 11.2: Completion rate
SELECT 'Test 11.2: Booking Completion Rate' as test_name,
       ROUND((SELECT COUNT(*) FROM tms_service_booking WHERE sb_status = 'Completed') * 100.0 / 
             (SELECT COUNT(*) FROM tms_service_booking), 2) as completion_rate_percent;

-- Test 11.3: Rejection rate
SELECT 'Test 11.3: Booking Rejection Rate' as test_name,
       ROUND((SELECT COUNT(*) FROM tms_service_booking WHERE sb_status = 'Rejected') * 100.0 / 
             (SELECT COUNT(*) FROM tms_service_booking), 2) as rejection_rate_percent;

-- Test 11.4: Average booking value
SELECT 'Test 11.4: Average Booking Value' as test_name,
       ROUND(AVG(sb_total_price), 2) as avg_booking_value
FROM tms_service_booking;

-- ============================================
-- END OF DATABASE LOGIC TESTING
-- ============================================
SELECT '=== ALL TESTS COMPLETED ===' as test_section;
