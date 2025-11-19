<?php
/**
 * Fix Technician Availability & Slots
 * Syncs technician booking counts and availability status
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
include('vendor/inc/booking-limit-helper.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Technician Availability</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #667eea; }
        .section { background: #f9fafb; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea; border-radius: 5px; }
        .success { border-left-color: #10b981; background: #ecfdf5; }
        .error { border-left-color: #ef4444; background: #fef2f2; }
        .warning { border-left-color: #f59e0b; background: #fffbeb; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .badge-available { background: #10b981; color: white; }
        .badge-busy { background: #ef4444; color: white; }
        .badge-offline { background: #6b7280; color: white; }
        .btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔧 Fix Technician Availability & Slots</h2>
        
        <?php
        $fixes_applied = [];
        $errors = [];
        
        // Step 1: Check current technician status
        echo "<div class='section'>";
        echo "<h3>1️⃣ Current Technician Status (Before Fix)</h3>";
        
        $before_query = "SELECT 
                          t_id,
                          t_name,
                          t_status,
                          t_current_bookings,
                          t_booking_limit,
                          (SELECT COUNT(*) FROM tms_service_booking 
                           WHERE sb_technician_id = t.t_id 
                           AND sb_status IN ('Pending', 'Approved', 'In Progress')) as actual_bookings
                        FROM tms_technician t
                        ORDER BY t_id";
        
        $result = $mysqli->query($before_query);
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Name</th>";
            echo "<th>Status</th>";
            echo "<th>Recorded Bookings</th>";
            echo "<th>Actual Bookings</th>";
            echo "<th>Limit</th>";
            echo "<th>Issue?</th>";
            echo "</tr>";
            
            $issues_found = 0;
            while ($row = $result->fetch_assoc()) {
                $has_issue = ($row['t_current_bookings'] != $row['actual_bookings']) || 
                            (($row['t_current_bookings'] >= $row['t_booking_limit']) && $row['t_status'] == 'Available') ||
                            (($row['t_current_bookings'] < $row['t_booking_limit']) && $row['t_status'] == 'Busy');
                
                if ($has_issue) $issues_found++;
                
                $status_class = 'badge-' . strtolower($row['t_status']);
                
                echo "<tr" . ($has_issue ? " style='background: #fef2f2;'" : "") . ">";
                echo "<td>#{$row['t_id']}</td>";
                echo "<td>" . htmlspecialchars($row['t_name']) . "</td>";
                echo "<td><span class='badge $status_class'>" . htmlspecialchars($row['t_status']) . "</span></td>";
                echo "<td>" . $row['t_current_bookings'] . "</td>";
                echo "<td>" . $row['actual_bookings'] . "</td>";
                echo "<td>" . $row['t_booking_limit'] . "</td>";
                echo "<td>" . ($has_issue ? "⚠️ Mismatch" : "✓ OK") . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            if ($issues_found > 0) {
                echo "<p class='warning' style='padding: 10px; margin-top: 10px;'><strong>⚠️ Found $issues_found technician(s) with incorrect data</strong></p>";
            } else {
                echo "<p class='success' style='padding: 10px; margin-top: 10px;'><strong>✓ All technicians have correct data</strong></p>";
            }
        } else {
            echo "<p>No technicians found.</p>";
        }
        echo "</div>";
        
        // Step 2: Sync booking counts
        echo "<div class='section'>";
        echo "<h3>2️⃣ Sync Booking Counts</h3>";
        
        $sync_result = syncTechnicianBookingCounts($mysqli);
        
        if ($sync_result['success']) {
            echo "<p style='color: green;'>✓ Synced {$sync_result['synced_count']} technician(s)</p>";
            $fixes_applied[] = "Synced booking counts for {$sync_result['synced_count']} technicians";
        } else {
            echo "<p style='color: red;'>✗ Failed to sync booking counts</p>";
            $errors[] = "Sync failed";
        }
        echo "</div>";
        
        // Step 3: Check after fix
        echo "<div class='section success'>";
        echo "<h3>3️⃣ Technician Status (After Fix)</h3>";
        
        $after_query = "SELECT 
                         t_id,
                         t_name,
                         t_status,
                         t_current_bookings,
                         t_booking_limit,
                         (t_booking_limit - t_current_bookings) as available_slots,
                         (SELECT COUNT(*) FROM tms_service_booking 
                          WHERE sb_technician_id = t.t_id 
                          AND sb_status IN ('Pending', 'Approved', 'In Progress')) as actual_bookings
                        FROM tms_technician t
                        ORDER BY t_id";
        
        $result = $mysqli->query($after_query);
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Name</th>";
            echo "<th>Status</th>";
            echo "<th>Current Bookings</th>";
            echo "<th>Booking Limit</th>";
            echo "<th>Available Slots</th>";
            echo "<th>Verified</th>";
            echo "</tr>";
            
            $all_correct = true;
            while ($row = $result->fetch_assoc()) {
                $is_correct = ($row['t_current_bookings'] == $row['actual_bookings']) &&
                             (($row['t_current_bookings'] >= $row['t_booking_limit'] && $row['t_status'] == 'Busy') ||
                              ($row['t_current_bookings'] < $row['t_booking_limit'] && $row['t_status'] == 'Available'));
                
                if (!$is_correct) $all_correct = false;
                
                $status_class = 'badge-' . strtolower($row['t_status']);
                
                echo "<tr>";
                echo "<td>#{$row['t_id']}</td>";
                echo "<td>" . htmlspecialchars($row['t_name']) . "</td>";
                echo "<td><span class='badge $status_class'>" . htmlspecialchars($row['t_status']) . "</span></td>";
                echo "<td>{$row['t_current_bookings']}</td>";
                echo "<td>{$row['t_booking_limit']}</td>";
                echo "<td><strong>{$row['available_slots']}</strong></td>";
                echo "<td>" . ($is_correct ? "✓" : "⚠️") . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            if ($all_correct) {
                echo "<p style='color: green; font-weight: bold; margin-top: 15px;'>✅ All technicians now have correct availability status!</p>";
            }
        }
        echo "</div>";
        
        // Step 4: Test the automatic update
        echo "<div class='section'>";
        echo "<h3>4️⃣ Test Automatic Updates</h3>";
        echo "<p>The system will now automatically update technician availability when:</p>";
        echo "<ul>";
        echo "<li>✅ A booking is assigned to a technician</li>";
        echo "<li>✅ A technician rejects a booking</li>";
        echo "<li>✅ A technician completes a booking</li>";
        echo "<li>✅ An admin reassigns a booking</li>";
        echo "</ul>";
        
        echo "<h4>How it works:</h4>";
        echo "<ol>";
        echo "<li><strong>Increment:</strong> When booking assigned → t_current_bookings++</li>";
        echo "<li><strong>Check:</strong> If t_current_bookings >= t_booking_limit → Set status to 'Busy'</li>";
        echo "<li><strong>Decrement:</strong> When booking rejected/completed → t_current_bookings--</li>";
        echo "<li><strong>Check:</strong> If t_current_bookings < t_booking_limit → Set status to 'Available'</li>";
        echo "</ol>";
        echo "</div>";
        
        // Step 5: Show active bookings per technician
        echo "<div class='section'>";
        echo "<h3>5️⃣ Active Bookings by Technician</h3>";
        
        $bookings_query = "SELECT 
                            t.t_id,
                            t.t_name,
                            t.t_status,
                            sb.sb_id,
                            sb.sb_status,
                            s.s_name as service_name,
                            sb.sb_date,
                            sb.sb_time
                          FROM tms_technician t
                          LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
                            AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                          ORDER BY t.t_id, sb.sb_date, sb.sb_time";
        
        $result = $mysqli->query($bookings_query);
        
        if ($result && $result->num_rows > 0) {
            $current_tech = null;
            $booking_count = 0;
            
            while ($row = $result->fetch_assoc()) {
                if ($current_tech != $row['t_id']) {
                    if ($current_tech !== null) {
                        echo "</ul></div>";
                    }
                    $current_tech = $row['t_id'];
                    $booking_count = 0;
                    
                    $status_class = 'badge-' . strtolower($row['t_status']);
                    echo "<div style='margin: 15px 0; padding: 15px; background: #f9fafb; border-radius: 5px;'>";
                    echo "<h4>" . htmlspecialchars($row['t_name']) . " <span class='badge $status_class'>{$row['t_status']}</span></h4>";
                    echo "<ul style='margin: 10px 0;'>";
                }
                
                if ($row['sb_id']) {
                    $booking_count++;
                    echo "<li>";
                    echo "<strong>Booking #{$row['sb_id']}</strong> - ";
                    echo htmlspecialchars($row['service_name']) . " | ";
                    echo "<span style='color: #667eea;'>{$row['sb_status']}</span> | ";
                    echo date('M d, Y', strtotime($row['sb_date'])) . " at {$row['sb_time']}";
                    echo "</li>";
                }
            }
            
            if ($current_tech !== null) {
                if ($booking_count == 0) {
                    echo "<li style='color: #6b7280;'>No active bookings</li>";
                }
                echo "</ul></div>";
            }
        } else {
            echo "<p>No active bookings found.</p>";
        }
        echo "</div>";
        
        // Summary
        echo "<div class='section success'>";
        echo "<h3>📊 Summary</h3>";
        
        if (count($fixes_applied) > 0) {
            echo "<p><strong>✅ Fixes Applied:</strong></p>";
            echo "<ul>";
            foreach ($fixes_applied as $fix) {
                echo "<li style='color: green;'>$fix</li>";
            }
            echo "</ul>";
        }
        
        if (count($errors) > 0) {
            echo "<p><strong>❌ Errors:</strong></p>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li style='color: red;'>$error</li>";
            }
            echo "</ul>";
        }
        
        echo "<h4>✅ Technician availability now updates automatically!</h4>";
        echo "<p><strong>What happens now:</strong></p>";
        echo "<ol>";
        echo "<li>When technician <strong>accepts/gets assigned</strong> a booking → Slots decrease, status updates to 'Busy' if full</li>";
        echo "<li>When technician <strong>rejects</strong> a booking → Slots increase, status updates to 'Available'</li>";
        echo "<li>When technician <strong>completes</strong> a booking → Slots increase, status updates to 'Available'</li>";
        echo "<li>System keeps <strong>t_current_bookings</strong> and <strong>t_status</strong> in sync automatically</li>";
        echo "</ol>";
        echo "</div>";
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>🧪 Test the Fix</h3>
        <p>To verify automatic updates work:</p>
        <ol>
            <li>Check a technician's current status and booking count above</li>
            <li>Assign them a new booking (or have them reject/complete one)</li>
            <li>Refresh this page</li>
            <li>Verify their booking count and status updated correctly</li>
        </ol>
        
        <h3>🔧 Quick Actions</h3>
        <a href="admin-manage-technicians.php" class="btn">Manage Technicians</a>
        <a href="admin-bookings.php" class="btn">View Bookings</a>
        <a href="admin-dashboard.php" class="btn">Dashboard</a>
        <a href="?" class="btn">Refresh</a>
    </div>
</body>
</html>
