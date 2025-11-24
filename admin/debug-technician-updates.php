<?php
/**
 * Debug Technician Updates
 * Shows real-time what happens when bookings are rejected/completed
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Technician Updates</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #667eea; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9fafb; }
        .error { background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #ecfdf5; color: #059669; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fffbeb; color: #d97706; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .badge-available { background: #10b981; color: white; }
        .badge-busy { background: #ef4444; color: white; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Debug Technician Updates</h2>
        
        <?php
        // Check if helper functions exist
        echo "<div class='section'>";
        echo "<h3>1️⃣ Function Check</h3>";
        
        $functions_to_check = [
            'updateTechnicianAvailabilityStatus',
            'incrementTechnicianBookings',
            'decrementTechnicianBookings',
            'syncTechnicianBookingCounts'
        ];
        
        echo "<table>";
        echo "<tr><th>Function</th><th>Status</th></tr>";
        foreach ($functions_to_check as $func) {
            $exists = function_exists($func);
            echo "<tr>";
            echo "<td><code>$func()</code></td>";
            echo "<td>" . ($exists ? "✅ Exists" : "❌ Missing") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        
        // Show current technician data
        echo "<div class='section'>";
        echo "<h3>2️⃣ Current Technician Data</h3>";
        
        $query = "SELECT 
                    t.t_id,
                    t.t_name,
                    t.t_status,
                    t.t_current_bookings,
                    t.t_booking_limit,
                    (SELECT COUNT(*) FROM tms_service_booking 
                     WHERE sb_technician_id = t.t_id 
                     AND sb_status IN ('Pending', 'Approved', 'In Progress')) as actual_active_bookings,
                    (SELECT GROUP_CONCAT(CONCAT('#', sb_id, ' (', sb_status, ')') SEPARATOR ', ')
                     FROM tms_service_booking 
                     WHERE sb_technician_id = t.t_id 
                     AND sb_status IN ('Pending', 'Approved', 'In Progress')) as active_booking_ids
                  FROM tms_technician t
                  ORDER BY t.t_id";
        
        $result = $mysqli->query($query);
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Name</th>";
            echo "<th>Status</th>";
            echo "<th>Recorded Count</th>";
            echo "<th>Actual Count</th>";
            echo "<th>Limit</th>";
            echo "<th>Should Be</th>";
            echo "<th>Active Bookings</th>";
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                $should_be_status = ($row['actual_active_bookings'] >= $row['t_booking_limit']) ? 'Busy' : 'Available';
                $is_correct = ($row['t_status'] == $should_be_status) && ($row['t_current_bookings'] == $row['actual_active_bookings']);
                
                $status_class = 'badge-' . strtolower($row['t_status']);
                
                echo "<tr" . (!$is_correct ? " style='background: #fef2f2;'" : "") . ">";
                echo "<td>#{$row['t_id']}</td>";
                echo "<td>" . htmlspecialchars($row['t_name']) . "</td>";
                echo "<td><span class='badge $status_class'>" . htmlspecialchars($row['t_status']) . "</span></td>";
                echo "<td>{$row['t_current_bookings']}</td>";
                echo "<td>{$row['actual_active_bookings']}</td>";
                echo "<td>{$row['t_booking_limit']}</td>";
                echo "<td><strong>$should_be_status</strong></td>";
                echo "<td style='font-size: 11px;'>" . ($row['active_booking_ids'] ?: 'None') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "</div>";
        
        // Test the update function manually
        echo "<div class='section'>";
        echo "<h3>3️⃣ Manual Function Test</h3>";
        
        if (function_exists('updateTechnicianAvailabilityStatus')) {
            // Get first technician
            $test_query = "SELECT t_id, t_name, t_status, t_current_bookings, t_booking_limit FROM tms_technician LIMIT 1";
            $test_result = $mysqli->query($test_query);
            
            if ($test_result && $test_row = $test_result->fetch_assoc()) {
                $test_tech_id = $test_row['t_id'];
                
                echo "<p><strong>Testing with Technician #{$test_tech_id} ({$test_row['t_name']})</strong></p>";
                echo "<p>Before: Status = {$test_row['t_status']}, Bookings = {$test_row['t_current_bookings']}/{$test_row['t_booking_limit']}</p>";
                
                // Call the function
                include('vendor/inc/booking-limit-helper.php');
                $update_result = updateTechnicianAvailabilityStatus($mysqli, $test_tech_id);
                
                // Check after
                $after_query = "SELECT t_status, t_current_bookings, t_booking_limit FROM tms_technician WHERE t_id = ?";
                $stmt = $mysqli->prepare($after_query);
                $stmt->bind_param('i', $test_tech_id);
                $stmt->execute();
                $after_result = $stmt->get_result();
                $after_row = $after_result->fetch_assoc();
                
                echo "<p>After: Status = {$after_row['t_status']}, Bookings = {$after_row['t_current_bookings']}/{$after_row['t_booking_limit']}</p>";
                
                if ($update_result) {
                    echo "<div class='success'>✅ Function executed successfully!</div>";
                } else {
                    echo "<div class='error'>❌ Function returned false</div>";
                }
            }
        } else {
            echo "<div class='error'>❌ updateTechnicianAvailabilityStatus() function not found!</div>";
        }
        echo "</div>";
        
        // Show recent booking status changes
        echo "<div class='section'>";
        echo "<h3>4️⃣ Recent Booking Status Changes</h3>";
        
        $recent_query = "SELECT 
                          sb.sb_id,
                          sb.sb_status,
                          sb.sb_technician_id,
                          t.t_name as tech_name,
                          sb.sb_rejected_at,
                          sb.sb_completed_at,
                          sb.sb_updated_at,
                          s.s_name as service_name
                        FROM tms_service_booking sb
                        LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                        WHERE sb.sb_status IN ('Rejected by Technician', 'Completed', 'Cancelled')
                        ORDER BY COALESCE(sb.sb_rejected_at, sb.sb_completed_at, sb.sb_updated_at) DESC
                        LIMIT 10";
        
        $result = $mysqli->query($recent_query);
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>Booking ID</th>";
            echo "<th>Service</th>";
            echo "<th>Status</th>";
            echo "<th>Technician</th>";
            echo "<th>Changed At</th>";
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                $changed_at = $row['sb_rejected_at'] ?: $row['sb_completed_at'] ?: $row['sb_updated_at'];
                echo "<tr>";
                echo "<td>#{$row['sb_id']}</td>";
                echo "<td>" . htmlspecialchars($row['service_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sb_status']) . "</td>";
                echo "<td>" . ($row['tech_name'] ?: 'Unassigned') . "</td>";
                echo "<td>" . ($changed_at ? date('Y-m-d H:i:s', strtotime($changed_at)) : 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No recent status changes found.</p>";
        }
        echo "</div>";
        
        // Show SQL to manually fix
        echo "<div class='section'>";
        echo "<h3>5️⃣ Manual Fix SQL</h3>";
        echo "<p>If automatic updates aren't working, run these SQL commands:</p>";
        
        echo "<div class='code'>";
        echo "-- Sync all technician booking counts\n";
        echo "UPDATE tms_technician t\n";
        echo "SET t_current_bookings = (\n";
        echo "    SELECT COUNT(*)\n";
        echo "    FROM tms_service_booking sb\n";
        echo "    WHERE sb.sb_technician_id = t.t_id\n";
        echo "    AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')\n";
        echo ");\n\n";
        
        echo "-- Update all technician statuses based on booking count\n";
        echo "UPDATE tms_technician\n";
        echo "SET t_status = CASE\n";
        echo "    WHEN t_current_bookings >= t_booking_limit THEN 'Busy'\n";
        echo "    ELSE 'Available'\n";
        echo "END;";
        echo "</div>";
        
        echo "<form method='post' style='margin-top: 20px;'>";
        echo "<button type='submit' name='run_manual_fix' class='btn'>🔧 Run Manual Fix Now</button>";
        echo "</form>";
        
        if (isset($_POST['run_manual_fix'])) {
            echo "<div style='margin-top: 20px;'>";
            
            // Sync counts
            $sync_sql = "UPDATE tms_technician t
                        SET t_current_bookings = (
                            SELECT COUNT(*)
                            FROM tms_service_booking sb
                            WHERE sb.sb_technician_id = t.t_id
                            AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                        )";
            
            if ($mysqli->query($sync_sql)) {
                echo "<div class='success'>✅ Step 1: Synced booking counts (affected {$mysqli->affected_rows} rows)</div>";
            } else {
                echo "<div class='error'>❌ Step 1 failed: " . $mysqli->error . "</div>";
            }
            
            // Update statuses
            $status_sql = "UPDATE tms_technician
                          SET t_status = CASE
                              WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
                              ELSE 'Available'
                          END";
            
            if ($mysqli->query($status_sql)) {
                echo "<div class='success'>✅ Step 2: Updated statuses (affected {$mysqli->affected_rows} rows)</div>";
                echo "<p><strong>Refresh the page to see updated data!</strong></p>";
            } else {
                echo "<div class='error'>❌ Step 2 failed: " . $mysqli->error . "</div>";
            }
            
            echo "</div>";
        }
        
        echo "</div>";
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>📋 Instructions</h3>
        <ol>
            <li><strong>Check Function Status:</strong> All functions should show "✅ Exists"</li>
            <li><strong>Check Technician Data:</strong> Look for red rows (mismatches)</li>
            <li><strong>Test Function:</strong> See if manual function call works</li>
            <li><strong>Run Manual Fix:</strong> Click button above to sync all data</li>
            <li><strong>Test Rejection:</strong> Have a technician reject a booking and refresh this page</li>
        </ol>
        
        <h3>🔧 Quick Actions</h3>
        <a href="?" class="btn">🔄 Refresh</a>
        <a href="admin-manage-technicians.php" class="btn">👥 Manage Technicians</a>
        <a href="admin-bookings.php" class="btn">📋 View Bookings</a>
        <a href="admin-dashboard.php" class="btn">🏠 Dashboard</a>
    </div>
</body>
</html>
