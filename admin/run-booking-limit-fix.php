<?php
/**
 * Run Booking Limit Counter Fix
 * This script fixes the booking limit counter system
 */

session_start();
include('vendor/inc/config.php');

// Check if admin is logged in
if (!isset($_SESSION['a_id'])) {
    die("Error: You must be logged in as admin to run this script.");
}

echo "<h2>Booking Limit Counter Fix</h2>";
echo "<p>Starting fix process...</p>";

// Step 1: Add columns if they don't exist
echo "<h3>Step 1: Ensuring columns exist...</h3>";
$sql1 = "ALTER TABLE tms_technician 
         ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 1 COMMENT 'Maximum concurrent bookings allowed',
         ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0 COMMENT 'Current active bookings count'";

if ($mysqli->query($sql1)) {
    echo "<p style='color: green;'>✓ Columns verified/created</p>";
} else {
    echo "<p style='color: orange;'>⚠ Columns may already exist: " . $mysqli->error . "</p>";
}

// Step 2: Set default booking limit
echo "<h3>Step 2: Setting default booking limits...</h3>";
$sql2 = "UPDATE tms_technician 
         SET t_booking_limit = 1 
         WHERE t_booking_limit IS NULL OR t_booking_limit = 0";

if ($mysqli->query($sql2)) {
    $affected = $mysqli->affected_rows;
    echo "<p style='color: green;'>✓ Updated $affected technician(s) with default limit of 1</p>";
} else {
    echo "<p style='color: red;'>✗ Error: " . $mysqli->error . "</p>";
}

// Step 3: Recalculate current bookings
echo "<h3>Step 3: Recalculating current bookings...</h3>";
$sql3 = "UPDATE tms_technician t
         SET t_current_bookings = (
             SELECT COUNT(*) 
             FROM tms_service_booking sb 
             WHERE sb.sb_technician_id = t.t_id 
             AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
         )";

if ($mysqli->query($sql3)) {
    $affected = $mysqli->affected_rows;
    echo "<p style='color: green;'>✓ Recalculated bookings for $affected technician(s)</p>";
} else {
    echo "<p style='color: red;'>✗ Error: " . $mysqli->error . "</p>";
}

// Step 4: Create index
echo "<h3>Step 4: Creating performance index...</h3>";
$sql4 = "CREATE INDEX IF NOT EXISTS idx_booking_technician_status 
         ON tms_service_booking(sb_technician_id, sb_status)";

if ($mysqli->query($sql4)) {
    echo "<p style='color: green;'>✓ Index created/verified</p>";
} else {
    echo "<p style='color: orange;'>⚠ Index may already exist: " . $mysqli->error . "</p>";
}

// Step 5: Verify the results
echo "<h3>Step 5: Verification Report</h3>";
$sql5 = "SELECT 
            t.t_id,
            t.t_name,
            t.t_booking_limit,
            t.t_current_bookings,
            (t.t_booking_limit - t.t_current_bookings) as available_slots,
            (SELECT COUNT(*) 
             FROM tms_service_booking sb 
             WHERE sb.sb_technician_id = t.t_id 
             AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
            ) as actual_active_bookings
         FROM tms_technician t
         ORDER BY t.t_name";

$result = $mysqli->query($sql5);

if ($result) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #667eea; color: white;'>
            <th>ID</th>
            <th>Name</th>
            <th>Booking Limit</th>
            <th>Current Bookings (Counter)</th>
            <th>Actual Active Bookings</th>
            <th>Available Slots</th>
            <th>Status</th>
          </tr>";
    
    $total_techs = 0;
    $correct_count = 0;
    $incorrect_count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $total_techs++;
        $is_correct = ($row['t_current_bookings'] == $row['actual_active_bookings']);
        
        if ($is_correct) {
            $correct_count++;
            $status = "<span style='color: green;'>✓ Correct</span>";
            $row_color = "#e8f5e9";
        } else {
            $incorrect_count++;
            $status = "<span style='color: red;'>✗ Mismatch</span>";
            $row_color = "#ffebee";
        }
        
        echo "<tr style='background: $row_color;'>
                <td>{$row['t_id']}</td>
                <td>{$row['t_name']}</td>
                <td>{$row['t_booking_limit']}</td>
                <td>{$row['t_current_bookings']}</td>
                <td>{$row['actual_active_bookings']}</td>
                <td>{$row['available_slots']}</td>
                <td>$status</td>
              </tr>";
    }
    
    echo "</table>";
    
    echo "<div style='margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px;'>";
    echo "<h4>Summary:</h4>";
    echo "<p><strong>Total Technicians:</strong> $total_techs</p>";
    echo "<p style='color: green;'><strong>Correct Counters:</strong> $correct_count</p>";
    
    if ($incorrect_count > 0) {
        echo "<p style='color: red;'><strong>Incorrect Counters:</strong> $incorrect_count</p>";
        echo "<p style='color: orange;'>⚠ Some counters are still incorrect. This may be due to ongoing bookings. Run this script again or check manually.</p>";
    } else {
        echo "<p style='color: green; font-size: 18px;'><strong>✓ All counters are correct!</strong></p>";
    }
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>✗ Error fetching verification data: " . $mysqli->error . "</p>";
}

echo "<hr>";
echo "<h3>Fix Complete!</h3>";
echo "<p><a href='admin-manage-technician.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Go to Manage Technicians</a></p>";
echo "<p><a href='admin-dashboard.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a></p>";

?>
