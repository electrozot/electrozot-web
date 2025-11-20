<?php
/**
 * Test Technician Notification System
 * 
 * This script tests if notifications are properly sent to technicians
 * when they are assigned or reassigned to bookings
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<h2>Testing Technician Notification System</h2>";
echo "<hr>";

// Ensure timestamp columns exist
echo "<h3>1. Checking Database Columns</h3>";
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL DEFAULT NULL");

$check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE 'sb_updated_at'");
if($check->num_rows > 0) {
    echo "<p style='color: green;'>✓ sb_updated_at column exists</p>";
} else {
    echo "<p style='color: red;'>✗ sb_updated_at column missing</p>";
}

$check2 = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE 'sb_assigned_at'");
if($check2->num_rows > 0) {
    echo "<p style='color: green;'>✓ sb_assigned_at column exists</p>";
} else {
    echo "<p style='color: red;'>✗ sb_assigned_at column missing</p>";
}

// Check for recent assignments
echo "<hr>";
echo "<h3>2. Recent Technician Assignments (Last 24 hours)</h3>";

$recent_query = "SELECT sb.sb_id, sb.sb_technician_id, sb.sb_status, sb.sb_assigned_at, sb.sb_updated_at,
                 t.t_name, t.t_phone, u.u_fname, u.u_lname, s.s_name
                 FROM tms_service_booking sb
                 LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                 LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                 LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                 WHERE sb.sb_assigned_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 OR sb.sb_updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 ORDER BY sb.sb_updated_at DESC
                 LIMIT 10";

$result = $mysqli->query($recent_query);

if($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Booking ID</th>";
    echo "<th>Technician</th>";
    echo "<th>Customer</th>";
    echo "<th>Service</th>";
    echo "<th>Status</th>";
    echo "<th>Assigned At</th>";
    echo "<th>Updated At</th>";
    echo "<th>Notification Status</th>";
    echo "</tr>";
    
    while($row = $result->fetch_object()) {
        $assigned_time = $row->sb_assigned_at ? date('M d, Y h:i A', strtotime($row->sb_assigned_at)) : 'Not set';
        $updated_time = $row->sb_updated_at ? date('M d, Y h:i A', strtotime($row->sb_updated_at)) : 'Not set';
        
        // Check if this would trigger notification (updated in last 30 seconds)
        $is_recent = false;
        if($row->sb_updated_at) {
            $time_diff = time() - strtotime($row->sb_updated_at);
            $is_recent = $time_diff < 30;
        }
        
        $notif_status = $is_recent ? 
            "<span style='color: green; font-weight: bold;'>✓ Will trigger notification</span>" : 
            "<span style='color: orange;'>⏰ Too old (>30s)</span>";
        
        echo "<tr>";
        echo "<td>#{$row->sb_id}</td>";
        echo "<td>{$row->t_name}<br><small>{$row->t_phone}</small></td>";
        echo "<td>{$row->u_fname} {$row->u_lname}</td>";
        echo "<td>{$row->s_name}</td>";
        echo "<td><span style='background: #007bff; color: white; padding: 3px 8px; border-radius: 3px;'>{$row->sb_status}</span></td>";
        echo "<td>{$assigned_time}</td>";
        echo "<td>{$updated_time}</td>";
        echo "<td>{$notif_status}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color: orange;'>No recent assignments found in the last 24 hours.</p>";
}

// Test notification detection
echo "<hr>";
echo "<h3>3. Test Notification Detection</h3>";

// Get a technician with recent assignments
$tech_query = "SELECT DISTINCT t.t_id, t.t_name, t.t_phone
               FROM tms_technician t
               INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
               WHERE sb.sb_updated_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
               LIMIT 1";

$tech_result = $mysqli->query($tech_query);

if($tech_result->num_rows > 0) {
    $tech = $tech_result->fetch_object();
    
    echo "<p><strong>Testing with Technician:</strong> {$tech->t_name} (ID: {$tech->t_id})</p>";
    
    // Simulate what the notification check would find
    $last_check = date('Y-m-d H:i:s', strtotime('-30 seconds'));
    
    $notif_query = "SELECT COUNT(*) as new_count 
                    FROM tms_service_booking 
                    WHERE sb_technician_id = ? 
                    AND sb_updated_at > ?
                    AND sb_status != 'Cancelled'";
    
    $stmt = $mysqli->prepare($notif_query);
    $stmt->bind_param('is', $tech->t_id, $last_check);
    $stmt->execute();
    $notif_result = $stmt->get_result();
    $notif_data = $notif_result->fetch_object();
    
    if($notif_data->new_count > 0) {
        echo "<p style='color: green; font-weight: bold;'>✓ {$notif_data->new_count} notification(s) would be detected for this technician!</p>";
        echo "<p style='background: #d4edda; padding: 10px; border-left: 4px solid #28a745;'>";
        echo "<strong>Expected Behavior:</strong><br>";
        echo "1. Technician dashboard will detect this update<br>";
        echo "2. Sound alert will play (arived.mp3)<br>";
        echo "3. Visual notification toast will appear<br>";
        echo "4. Page will reload after 5 seconds to show new booking<br>";
        echo "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No recent notifications for this technician (last 30 seconds)</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No technicians with recent assignments found</p>";
}

// Check sound file
echo "<hr>";
echo "<h3>4. Sound File Check</h3>";

$sound_file = __DIR__ . '/vendor/sounds/arived.mp3';
if(file_exists($sound_file)) {
    $file_size = filesize($sound_file);
    echo "<p style='color: green;'>✓ Sound file exists: vendor/sounds/arived.mp3</p>";
    echo "<p>File size: " . number_format($file_size / 1024, 2) . " KB</p>";
    echo "<p><audio controls><source src='vendor/sounds/arived.mp3' type='audio/mpeg'>Your browser does not support audio.</audio></p>";
} else {
    echo "<p style='color: red;'>✗ Sound file NOT found at: {$sound_file}</p>";
    echo "<p style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'>";
    echo "<strong>Action Required:</strong> Upload arived.mp3 to admin/vendor/sounds/ directory";
    echo "</p>";
}

// Check notification system file
echo "<hr>";
echo "<h3>5. Notification System Files</h3>";

$notif_system_file = __DIR__ . '/../tech/includes/notification-system.php';
if(file_exists($notif_system_file)) {
    echo "<p style='color: green;'>✓ Notification system file exists</p>";
} else {
    echo "<p style='color: red;'>✗ Notification system file NOT found</p>";
}

$check_notif_file = __DIR__ . '/../tech/check-technician-notifications.php';
if(file_exists($check_notif_file)) {
    echo "<p style='color: green;'>✓ Notification check API exists</p>";
} else {
    echo "<p style='color: red;'>✗ Notification check API NOT found</p>";
}

// Summary
echo "<hr>";
echo "<h3>6. System Status Summary</h3>";

$all_good = true;
$issues = [];

// Check database columns
$check_cols = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE 'sb_updated_at'");
if($check_cols->num_rows == 0) {
    $all_good = false;
    $issues[] = "Missing sb_updated_at column";
}

// Check sound file
if(!file_exists($sound_file)) {
    $all_good = false;
    $issues[] = "Missing sound file (arived.mp3)";
}

// Check notification files
if(!file_exists($notif_system_file)) {
    $all_good = false;
    $issues[] = "Missing notification-system.php";
}

if(!file_exists($check_notif_file)) {
    $all_good = false;
    $issues[] = "Missing check-technician-notifications.php";
}

if($all_good) {
    echo "<div style='background: #d4edda; padding: 20px; border-left: 5px solid #28a745; border-radius: 5px;'>";
    echo "<h4 style='color: #155724; margin-top: 0;'>✓ All Systems Operational!</h4>";
    echo "<p style='color: #155724;'>The technician notification system is properly configured and ready to use.</p>";
    echo "<p style='color: #155724;'><strong>How to test:</strong></p>";
    echo "<ol style='color: #155724;'>";
    echo "<li>Open technician dashboard in one browser tab</li>";
    echo "<li>Assign or reassign a booking to that technician from admin panel</li>";
    echo "<li>Within 5 seconds, the technician should hear a sound and see a notification</li>";
    echo "<li>The page will automatically reload to show the new booking</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-left: 5px solid #dc3545; border-radius: 5px;'>";
    echo "<h4 style='color: #721c24; margin-top: 0;'>⚠️ Issues Found</h4>";
    echo "<ul style='color: #721c24;'>";
    foreach($issues as $issue) {
        echo "<li>{$issue}</li>";
    }
    echo "</ul>";
    echo "<p style='color: #721c24;'><strong>Action:</strong> Fix the issues above and run this test again.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='admin-dashboard.php' class='btn btn-primary' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Back to Dashboard</a></p>";
echo "<p><a href='admin-assign-technician.php' class='btn btn-success' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Test by Assigning Booking</a></p>";
?>
