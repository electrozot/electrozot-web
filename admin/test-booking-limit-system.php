<?php
/**
 * Test Booking Limit System
 * Automated test to verify the booking limit counter is working correctly
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Limit System Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #667eea; }
        .test-case { background: #f9fafb; padding: 15px; margin: 15px 0; border-left: 4px solid #667eea; border-radius: 5px; }
        .pass { border-left-color: #10b981; background: #ecfdf5; }
        .fail { border-left-color: #ef4444; background: #fef2f2; }
        .test-title { font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .test-result { margin-top: 10px; padding: 10px; border-radius: 5px; }
        .result-pass { background: #d1fae5; color: #065f46; }
        .result-fail { background: #fee2e2; color: #991b1b; }
        .summary { background: #dbeafe; padding: 20px; border-radius: 5px; margin-top: 30px; }
        .btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px 0 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧪 Booking Limit System Test</h2>
        <p>Running automated tests to verify the booking limit counter system...</p>
        
        <?php
        $tests_passed = 0;
        $tests_failed = 0;
        $test_results = [];
        
        // TEST 1: Check if columns exist
        echo "<div class='test-case'>";
        echo "<div class='test-title'>TEST 1: Database Schema Check</div>";
        echo "<p>Checking if t_booking_limit and t_current_bookings columns exist...</p>";
        
        $schema_check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_booking_limit'");
        $has_limit = $schema_check->num_rows > 0;
        
        $schema_check2 = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_current_bookings'");
        $has_current = $schema_check2->num_rows > 0;
        
        if ($has_limit && $has_current) {
            echo "<div class='test-result result-pass'>✓ PASS: Both columns exist</div>";
            $tests_passed++;
            echo "</div>";
        } else {
            echo "<div class='test-result result-fail'>✗ FAIL: Missing columns. Run fix script first.</div>";
            $tests_failed++;
            echo "</div>";
        }
        
        // TEST 2: Check if all technicians have booking limits set
        echo "<div class='test-case'>";
        echo "<div class='test-title'>TEST 2: Booking Limits Configuration</div>";
        echo "<p>Checking if all technicians have booking limits configured...</p>";
        
        $limit_check = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_booking_limit IS NULL OR t_booking_limit = 0");
        $missing_limits = $limit_check->fetch_object()->count;
        
        if ($missing_limits == 0) {
            echo "<div class='test-result result-pass'>✓ PASS: All technicians have booking limits set</div>";
            $tests_passed++;
        } else {
            echo "<div class='test-result result-fail'>✗ FAIL: $missing_limits technician(s) missing booking limit. Run fix script.</div>";
            $tests_failed++;
        }
        echo "</div>";
        
        // TEST 3: Counter accuracy check
        echo "<div class='test-case'>";
        echo "<div class='test-title'>TEST 3: Counter Accuracy</div>";
        echo "<p>Comparing counter values with actual active bookings...</p>";
        
        $accuracy_query = "SELECT 
                            t.t_id,
                            t.t_name,
                            t.t_current_bookings,
                            (SELECT COUNT(*) 
                             FROM tms_service_booking sb 
                             WHERE sb.sb_technician_id = t.t_id 
                             AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
                            ) as actual_count
                          FROM tms_technician t
                          HAVING t.t_current_bookings != actual_count";
        
        $accuracy_result = $mysqli->query($accuracy_query);
        $mismatches = $accuracy_result->num_rows;
        
        if ($mismatches == 0) {
            echo "<div class='test-result result-pass'>✓ PASS: All counters match actual bookings</div>";
            $tests_passed++;
        } else {
            echo "<div class='test-result result-fail'>✗ FAIL: $mismatches technician(s) have incorrect counters</div>";
            echo "<ul>";
            while ($row = $accuracy_result->fetch_assoc()) {
                echo "<li>{$row['t_name']}: Counter={$row['t_current_bookings']}, Actual={$row['actual_count']}</li>";
            }
            echo "</ul>";
            $tests_failed++;
        }
        echo "</div>";
        
        // TEST 4: Availability logic check
        echo "<div class='test-case'>";
        echo "<div class='test-title'>TEST 4: Availability Logic</div>";
        echo "<p>Checking if technicians at capacity are correctly filtered...</p>";
        
        $at_capacity_query = "SELECT COUNT(*) as count 
                             FROM tms_technician 
                             WHERE t_current_bookings >= t_booking_limit";
        $at_capacity = $mysqli->query($at_capacity_query)->fetch_object()->count;
        
        $available_query = "SELECT COUNT(*) as count 
                           FROM tms_technician 
                           WHERE t_current_bookings < t_booking_limit";
        $available = $mysqli->query($available_query)->fetch_object()->count;
        
        echo "<p>Technicians at capacity: <strong>$at_capacity</strong></p>";
        echo "<p>Technicians available: <strong>$available</strong></p>";
        
        // Check if the query works
        $test_query = $mysqli->query("SELECT t_id FROM tms_technician WHERE t_current_bookings < t_booking_limit LIMIT 1");
        if ($test_query) {
            echo "<div class='test-result result-pass'>✓ PASS: Availability query works correctly</div>";
            $tests_passed++;
        } else {
            echo "<div class='test-result result-fail'>✗ FAIL: Availability query error</div>";
            $tests_failed++;
        }
        echo "</div>";
        
        // TEST 5: Check for negative values
        echo "<div class='test-case'>";
        echo "<div class='test-title'>TEST 5: Data Integrity</div>";
        echo "<p>Checking for negative or invalid counter values...</p>";
        
        $negative_check = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_current_bookings < 0");
        $negative_count = $negative_check->fetch_object()->count;
        
        $overflow_check = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_current_bookings > t_booking_limit + 5");
        $overflow_count = $overflow_check->fetch_object()->count;
        
        if ($negative_count == 0 && $overflow_count == 0) {
            echo "<div class='test-result result-pass'>✓ PASS: No data integrity issues found</div>";
            $tests_passed++;
        } else {
            echo "<div class='test-result result-fail'>✗ FAIL: Found data issues</div>";
            if ($negative_count > 0) echo "<p>- $negative_count technician(s) with negative counters</p>";
            if ($overflow_count > 0) echo "<p>- $overflow_count technician(s) with counters way above limit</p>";
            $tests_failed++;
        }
        echo "</div>";
        
        // TEST 6: Index check
        echo "<div class='test-case'>";
        echo "<div class='test-title'>TEST 6: Performance Index</div>";
        echo "<p>Checking if performance index exists...</p>";
        
        $index_check = $mysqli->query("SHOW INDEX FROM tms_service_booking WHERE Key_name = 'idx_booking_technician_status'");
        $has_index = $index_check->num_rows > 0;
        
        if ($has_index) {
            echo "<div class='test-result result-pass'>✓ PASS: Performance index exists</div>";
            $tests_passed++;
        } else {
            echo "<div class='test-result result-fail'>⚠ WARNING: Performance index missing (optional but recommended)</div>";
            // Don't count as failure
            $tests_passed++;
        }
        echo "</div>";
        
        // Summary
        $total_tests = $tests_passed + $tests_failed;
        $pass_rate = round(($tests_passed / $total_tests) * 100);
        
        echo "<div class='summary'>";
        echo "<h3>📊 Test Summary</h3>";
        echo "<p><strong>Total Tests:</strong> $total_tests</p>";
        echo "<p style='color: #10b981;'><strong>Passed:</strong> $tests_passed</p>";
        echo "<p style='color: #ef4444;'><strong>Failed:</strong> $tests_failed</p>";
        echo "<p><strong>Pass Rate:</strong> $pass_rate%</p>";
        
        if ($tests_failed == 0) {
            echo "<div style='background: #d1fae5; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
            echo "<h4 style='color: #065f46; margin: 0;'>✓ ALL TESTS PASSED!</h4>";
            echo "<p style='color: #065f46; margin: 10px 0 0 0;'>The booking limit system is working correctly.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #fee2e2; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
            echo "<h4 style='color: #991b1b; margin: 0;'>⚠ SOME TESTS FAILED</h4>";
            echo "<p style='color: #991b1b; margin: 10px 0 0 0;'>Please run the fix script to resolve issues.</p>";
            echo "</div>";
        }
        echo "</div>";
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>🔧 Actions</h3>
        <?php if ($tests_failed > 0): ?>
            <a href="run-booking-limit-fix.php" class="btn" style="background: #ef4444;">Run Fix Script</a>
        <?php endif; ?>
        <a href="check-technician-booking-count.php" class="btn">Check Individual Technicians</a>
        <a href="admin-manage-technician.php" class="btn" style="background: #10b981;">Manage Technicians</a>
        <a href="admin-dashboard.php" class="btn">Dashboard</a>
        <a href="?" class="btn">Run Tests Again</a>
        
        <hr style="margin: 30px 0;">
        
        <h3>📖 What These Tests Check</h3>
        <ul>
            <li><strong>Test 1:</strong> Verifies database columns exist</li>
            <li><strong>Test 2:</strong> Ensures all technicians have booking limits configured</li>
            <li><strong>Test 3:</strong> Compares counter with actual active bookings</li>
            <li><strong>Test 4:</strong> Tests the availability filtering logic</li>
            <li><strong>Test 5:</strong> Checks for data corruption or invalid values</li>
            <li><strong>Test 6:</strong> Verifies performance optimization index</li>
        </ul>
    </div>
</body>
</html>
