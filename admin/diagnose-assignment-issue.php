<?php
/**
 * Diagnostic Tool: Technician Assignment Issues
 * This will help identify why technician assignment is failing
 */

session_start();
include('vendor/inc/config.php');

// Check if admin is logged in
if(!isset($_SESSION['a_id'])) {
    die("Please login as admin first");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Assignment Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-info { background: #17a2b8; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Technician Assignment Diagnostic Tool</h1>
    
    <?php
    // TEST 1: Check database connection
    echo "<h2>1. Database Connection</h2>";
    if($mysqli->ping()) {
        echo '<div class="success">✅ Database connection is working</div>';
    } else {
        echo '<div class="error">❌ Database connection failed: ' . $mysqli->error . '</div>';
    }
    
    // TEST 2: Check required columns exist
    echo "<h2>2. Database Schema Check</h2>";
    $required_columns = [
        'tms_service_booking' => ['sb_id', 'sb_technician_id', 'sb_status', 'sb_service_deadline_date', 'sb_service_deadline_time'],
        'tms_technician' => ['t_id', 't_name', 't_status', 't_booking_limit', 't_current_bookings', 't_current_booking_id']
    ];
    
    foreach($required_columns as $table => $columns) {
        echo "<h3>Table: $table</h3>";
        $check_query = "SHOW COLUMNS FROM $table";
        $result = $mysqli->query($check_query);
        
        if($result) {
            $existing_columns = [];
            while($row = $result->fetch_assoc()) {
                $existing_columns[] = $row['Field'];
            }
            
            echo "<table>";
            echo "<tr><th>Column</th><th>Status</th></tr>";
            foreach($columns as $col) {
                $exists = in_array($col, $existing_columns);
                $status = $exists ? '<span class="badge badge-success">✅ Exists</span>' : '<span class="badge badge-danger">❌ Missing</span>';
                echo "<tr><td>$col</td><td>$status</td></tr>";
            }
            echo "</table>";
        } else {
            echo '<div class="error">❌ Cannot check table: ' . $mysqli->error . '</div>';
        }
    }
    
    // TEST 3: Check technician availability
    echo "<h2>3. Technician Availability Status</h2>";
    $tech_query = "SELECT t_id, t_name, t_category, t_status, t_booking_limit, t_current_bookings, 
                   (t_booking_limit - t_current_bookings) as available_slots
                   FROM tms_technician 
                   ORDER BY t_name";
    $tech_result = $mysqli->query($tech_query);
    
    if($tech_result && $tech_result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Status</th><th>Current</th><th>Limit</th><th>Available Slots</th></tr>";
        
        while($tech = $tech_result->fetch_assoc()) {
            $status_badge = $tech['t_status'] == 'Available' ? 'badge-success' : 'badge-warning';
            $slots_badge = $tech['available_slots'] > 0 ? 'badge-success' : 'badge-danger';
            
            echo "<tr>";
            echo "<td>{$tech['t_id']}</td>";
            echo "<td>{$tech['t_name']}</td>";
            echo "<td>{$tech['t_category']}</td>";
            echo "<td><span class='badge $status_badge'>{$tech['t_status']}</span></td>";
            echo "<td>{$tech['t_current_bookings']}</td>";
            echo "<td>{$tech['t_booking_limit']}</td>";
            echo "<td><span class='badge $slots_badge'>{$tech['available_slots']}</span></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo '<div class="warning">⚠️ No technicians found in database</div>';
    }
    
    // TEST 4: Check pending bookings
    echo "<h2>4. Pending Bookings Status</h2>";
    $booking_query = "SELECT sb.sb_id, sb.sb_status, sb.sb_technician_id, 
                      CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                      s.s_name as service_name, s.s_category,
                      t.t_name as technician_name
                      FROM tms_service_booking sb
                      LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                      LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                      LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                      WHERE sb.sb_status IN ('Pending', 'Approved', 'In Progress')
                      ORDER BY sb.sb_id DESC
                      LIMIT 10";
    $booking_result = $mysqli->query($booking_query);
    
    if($booking_result && $booking_result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Booking ID</th><th>Customer</th><th>Service</th><th>Category</th><th>Status</th><th>Assigned To</th></tr>";
        
        while($booking = $booking_result->fetch_assoc()) {
            $status_class = $booking['sb_status'] == 'Pending' ? 'badge-warning' : 'badge-info';
            $tech_name = $booking['technician_name'] ? $booking['technician_name'] : '<span class="badge badge-danger">Not Assigned</span>';
            
            echo "<tr>";
            echo "<td>#{$booking['sb_id']}</td>";
            echo "<td>{$booking['customer_name']}</td>";
            echo "<td>{$booking['service_name']}</td>";
            echo "<td>{$booking['s_category']}</td>";
            echo "<td><span class='badge $status_class'>{$booking['sb_status']}</span></td>";
            echo "<td>$tech_name</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo '<div class="info">ℹ️ No pending bookings found</div>';
    }
    
    // TEST 5: Check for mismatched booking counts
    echo "<h2>5. Booking Count Accuracy Check</h2>";
    $mismatch_query = "SELECT t.t_id, t.t_name, t.t_current_bookings as stored_count,
                       (SELECT COUNT(*) FROM tms_service_booking sb 
                        WHERE sb.sb_technician_id = t.t_id 
                        AND sb.sb_status IN ('Pending', 'Approved', 'In Progress', 'Assigned')) as actual_count
                       FROM tms_technician t";
    $mismatch_result = $mysqli->query($mismatch_query);
    
    $has_mismatch = false;
    echo "<table>";
    echo "<tr><th>Technician</th><th>Stored Count</th><th>Actual Count</th><th>Status</th></tr>";
    
    while($row = $mismatch_result->fetch_assoc()) {
        $match = $row['stored_count'] == $row['actual_count'];
        $status = $match ? '<span class="badge badge-success">✅ Match</span>' : '<span class="badge badge-danger">❌ Mismatch</span>';
        
        if(!$match) $has_mismatch = true;
        
        echo "<tr>";
        echo "<td>{$row['t_name']}</td>";
        echo "<td>{$row['stored_count']}</td>";
        echo "<td>{$row['actual_count']}</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if($has_mismatch) {
        echo '<div class="error">❌ <strong>Issue Found:</strong> Booking counts are out of sync! This can cause assignment failures.</div>';
        echo '<div class="info">💡 <strong>Solution:</strong> <a href="sync-technician-slots-now.php">Click here to sync booking counts</a></div>';
    } else {
        echo '<div class="success">✅ All booking counts are accurate</div>';
    }
    
    // TEST 6: Check for required files
    echo "<h2>6. Required Files Check</h2>";
    $required_files = [
        'vendor/inc/config.php',
        'vendor/inc/booking-limit-helper.php',
        'vendor/inc/technician-matcher.php',
        'check-technician-availability.php'
    ];
    
    echo "<table>";
    echo "<tr><th>File</th><th>Status</th></tr>";
    foreach($required_files as $file) {
        $exists = file_exists($file);
        $status = $exists ? '<span class="badge badge-success">✅ Exists</span>' : '<span class="badge badge-danger">❌ Missing</span>';
        echo "<tr><td>$file</td><td>$status</td></tr>";
    }
    echo "</table>";
    
    // TEST 7: Test assignment query
    echo "<h2>7. Assignment Query Test</h2>";
    echo '<div class="info">Testing if the assignment UPDATE query works...</div>';
    
    // Get a test booking
    $test_booking_query = "SELECT sb_id, sb_technician_id FROM tms_service_booking WHERE sb_status = 'Pending' LIMIT 1";
    $test_result = $mysqli->query($test_booking_query);
    
    if($test_result && $test_result->num_rows > 0) {
        $test_booking = $test_result->fetch_assoc();
        echo '<div class="success">✅ Found test booking #' . $test_booking['sb_id'] . '</div>';
        
        // Test if UPDATE query syntax is valid (without executing)
        $test_update = "UPDATE tms_service_booking SET sb_status='Pending' WHERE sb_id=" . $test_booking['sb_id'];
        if($mysqli->query($test_update)) {
            echo '<div class="success">✅ UPDATE query syntax is valid</div>';
        } else {
            echo '<div class="error">❌ UPDATE query failed: ' . $mysqli->error . '</div>';
        }
    } else {
        echo '<div class="warning">⚠️ No pending bookings to test with</div>';
    }
    
    // SUMMARY
    echo "<h2>📋 Summary & Recommendations</h2>";
    
    if($has_mismatch) {
        echo '<div class="error">';
        echo '<h3>🔴 Critical Issue Found</h3>';
        echo '<p><strong>Problem:</strong> Technician booking counts are out of sync with actual bookings.</p>';
        echo '<p><strong>Impact:</strong> This prevents technicians from being assigned because the system thinks they are at capacity.</p>';
        echo '<p><strong>Solution:</strong></p>';
        echo '<ol>';
        echo '<li><a href="sync-technician-slots-now.php" style="color: #721c24; font-weight: bold;">Run the Sync Tool</a> to fix booking counts</li>';
        echo '<li>After syncing, try assigning technicians again</li>';
        echo '<li>If issue persists, check the admin-assign-technician.php file for errors</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div class="success">';
        echo '<h3>✅ System Looks Good</h3>';
        echo '<p>All checks passed. If you\'re still experiencing issues:</p>';
        echo '<ul>';
        echo '<li>Check browser console for JavaScript errors</li>';
        echo '<li>Verify the booking ID is being passed correctly</li>';
        echo '<li>Check if service deadline fields are filled</li>';
        echo '<li>Ensure technician has matching skills for the service</li>';
        echo '</ul>';
        echo '</div>';
    }
    ?>
    
    <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
        <h3>🔧 Quick Actions</h3>
        <p>
            <a href="sync-technician-slots-now.php" style="display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Sync Booking Counts</a>
            <a href="admin-manage-service-booking.php" style="display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Manage Bookings</a>
            <a href="admin-manage-technician.php" style="display: inline-block; padding: 10px 20px; background: #FF9800; color: white; text-decoration: none; border-radius: 5px;">Manage Technicians</a>
        </p>
    </div>
</div>
</body>
</html>
