<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];

echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
.section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
h2 { color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
h3 { color: #333; margin-top: 20px; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #667eea; color: white; }
.success { color: #10b981; font-weight: bold; }
.error { color: #ef4444; font-weight: bold; }
.warning { color: #f59e0b; font-weight: bold; }
.info { background: #e0f2fe; padding: 10px; border-left: 4px solid #0284c7; margin: 10px 0; }
.test-btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
</style>";

echo "<h1>🔍 Complete System Check</h1>";

// 1. Check Database Columns
echo "<div class='section'>";
echo "<h2>1. Database Structure Check</h2>";

echo "<h3>Service Booking Table Columns:</h3>";
$booking_columns = [
    'sb_id' => 'Primary Key',
    'sb_status' => 'Booking Status',
    'sb_service_image' => 'Service Image Path',
    'sb_bill_image' => 'Bill Image Path',
    'sb_amount_charged' => 'Amount Charged',
    'sb_completed_at' => 'Completion Timestamp',
    'sb_not_done_reason' => 'Not Done Reason',
    'sb_not_done_at' => 'Not Done Timestamp',
    'sb_technician_id' => 'Assigned Technician'
];

echo "<table>";
echo "<tr><th>Column</th><th>Purpose</th><th>Status</th></tr>";
foreach($booking_columns as $col => $purpose) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$col'");
    $exists = $check->num_rows > 0;
    $status = $exists ? "<span class='success'>✓ EXISTS</span>" : "<span class='error'>✗ MISSING</span>";
    echo "<tr><td><code>$col</code></td><td>$purpose</td><td>$status</td></tr>";
}
echo "</table>";

echo "<h3>Technician Table Columns:</h3>";
$tech_columns = [
    't_id' => 'Primary Key',
    't_name' => 'Technician Name',
    't_is_available' => 'Availability Status',
    't_current_booking_id' => 'Current Booking ID'
];

echo "<table>";
echo "<tr><th>Column</th><th>Purpose</th><th>Status</th></tr>";
foreach($tech_columns as $col => $purpose) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$col'");
    $exists = $check->num_rows > 0;
    $status = $exists ? "<span class='success'>✓ EXISTS</span>" : "<span class='error'>✗ MISSING</span>";
    echo "<tr><td><code>$col</code></td><td>$purpose</td><td>$status</td></tr>";
}
echo "</table>";
echo "</div>";

// 2. Check Current Technician Status
echo "<div class='section'>";
echo "<h2>2. Your Technician Status</h2>";
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt = $mysqli->prepare($tech_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$tech = $stmt->get_result()->fetch_assoc();

if($tech) {
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>ID</td><td>" . $tech['t_id'] . "</td></tr>";
    echo "<tr><td>Name</td><td>" . $tech['t_name'] . "</td></tr>";
    echo "<tr><td>Available</td><td>" . (isset($tech['t_is_available']) && $tech['t_is_available'] ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "</td></tr>";
    echo "<tr><td>Current Booking</td><td>" . (isset($tech['t_current_booking_id']) && $tech['t_current_booking_id'] ? $tech['t_current_booking_id'] : "None") . "</td></tr>";
    echo "</table>";
}
echo "</div>";

// 3. Check Bookings
echo "<div class='section'>";
echo "<h2>3. Your Bookings Status</h2>";

$status_query = "SELECT 
    sb_status,
    COUNT(*) as count
FROM tms_service_booking 
WHERE sb_technician_id = ?
GROUP BY sb_status";
$stmt = $mysqli->prepare($status_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>Status</th><th>Count</th></tr>";
$total = 0;
while($row = $result->fetch_assoc()) {
    echo "<tr><td><strong>" . $row['sb_status'] . "</strong></td><td>" . $row['count'] . "</td></tr>";
    $total += $row['count'];
}
echo "<tr><td><strong>TOTAL</strong></td><td><strong>$total</strong></td></tr>";
echo "</table>";
echo "</div>";

// 4. Recent Bookings Detail
echo "<div class='section'>";
echo "<h2>4. Recent Bookings (Last 5)</h2>";

$recent_query = "SELECT 
    sb_id, 
    sb_status, 
    sb_service_image, 
    sb_bill_image, 
    sb_amount_charged,
    sb_completed_at,
    sb_not_done_reason,
    sb_not_done_at,
    sb_created_at
FROM tms_service_booking 
WHERE sb_technician_id = ?
ORDER BY sb_id DESC 
LIMIT 5";
$stmt = $mysqli->prepare($recent_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>ID</th><th>Status</th><th>Service Img</th><th>Bill Img</th><th>Amount</th><th>Completed</th><th>Not Done Reason</th><th>Actions</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>#" . $row['sb_id'] . "</td>";
    echo "<td><strong>" . $row['sb_status'] . "</strong></td>";
    echo "<td>" . ($row['sb_service_image'] ? "✓" : "✗") . "</td>";
    echo "<td>" . ($row['sb_bill_image'] ? "✓" : "✗") . "</td>";
    echo "<td>" . ($row['sb_amount_charged'] ? "₹" . $row['sb_amount_charged'] : "-") . "</td>";
    echo "<td>" . ($row['sb_completed_at'] ? date('M d, H:i', strtotime($row['sb_completed_at'])) : "-") . "</td>";
    echo "<td>" . ($row['sb_not_done_reason'] ? substr($row['sb_not_done_reason'], 0, 30) . "..." : "-") . "</td>";
    echo "<td>";
    if($row['sb_status'] != 'Completed' && $row['sb_status'] != 'Not Done') {
        echo "<a href='complete-booking.php?id=" . $row['sb_id'] . "&action=done' class='test-btn' style='padding:5px 10px;font-size:12px;'>Complete</a>";
    } else {
        echo "<span style='color:#999;'>Locked</span>";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// 5. Check Upload Folders
echo "<div class='section'>";
echo "<h2>5. Upload Folders Check</h2>";

$folders = [
    '../uploads/' => 'Main uploads folder',
    '../uploads/service_images/' => 'Service images folder',
    '../uploads/bill_images/' => 'Bill images folder'
];

echo "<table>";
echo "<tr><th>Folder</th><th>Purpose</th><th>Exists</th><th>Writable</th></tr>";
foreach($folders as $folder => $purpose) {
    $exists = file_exists($folder);
    $writable = is_writable($folder);
    
    echo "<tr>";
    echo "<td><code>$folder</code></td>";
    echo "<td>$purpose</td>";
    echo "<td>" . ($exists ? "<span class='success'>✓ YES</span>" : "<span class='error'>✗ NO</span>") . "</td>";
    echo "<td>" . ($writable ? "<span class='success'>✓ YES</span>" : "<span class='error'>✗ NO</span>") . "</td>";
    echo "</tr>";
}
echo "</table>";

// Try to create folders if they don't exist
echo "<div class='info'>";
echo "<strong>Auto-Fix:</strong> Attempting to create missing folders...<br>";
foreach($folders as $folder => $purpose) {
    if(!file_exists($folder)) {
        if(mkdir($folder, 0777, true)) {
            echo "✓ Created: $folder<br>";
        } else {
            echo "✗ Failed to create: $folder<br>";
        }
    }
}
echo "</div>";
echo "</div>";

// 6. Test Database Connection
echo "<div class='section'>";
echo "<h2>6. Database Connection Test</h2>";

$test_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ?";
$stmt = $mysqli->prepare($test_query);
if($stmt) {
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    echo "<p class='success'>✓ Database connection working! You have $count total bookings.</p>";
} else {
    echo "<p class='error'>✗ Database connection failed: " . $mysqli->error . "</p>";
}
echo "</div>";

// 7. Quick Actions
echo "<div class='section'>";
echo "<h2>7. Quick Actions</h2>";
echo "<a href='dashboard.php' class='test-btn'>Go to Dashboard</a>";
echo "<a href='debug-complete.php' class='test-btn'>Debug Complete System</a>";
echo "<a href='complete-booking.php?id=1&action=done' class='test-btn'>Test Complete Booking</a>";
echo "</div>";

// 8. System Recommendations
echo "<div class='section'>";
echo "<h2>8. System Status & Recommendations</h2>";

$issues = [];
$warnings = [];

// Check for missing columns
foreach($booking_columns as $col => $purpose) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$col'");
    if($check->num_rows == 0) {
        $issues[] = "Missing column: tms_service_booking.$col";
    }
}

foreach($tech_columns as $col => $purpose) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$col'");
    if($check->num_rows == 0) {
        $issues[] = "Missing column: tms_technician.$col";
    }
}

// Check folders
foreach($folders as $folder => $purpose) {
    if(!file_exists($folder)) {
        $warnings[] = "Missing folder: $folder";
    } elseif(!is_writable($folder)) {
        $warnings[] = "Folder not writable: $folder";
    }
}

if(count($issues) == 0 && count($warnings) == 0) {
    echo "<p class='success' style='font-size:18px;'>✓ All systems operational! Everything is working correctly.</p>";
} else {
    if(count($issues) > 0) {
        echo "<h3 class='error'>Critical Issues:</h3><ul>";
        foreach($issues as $issue) {
            echo "<li class='error'>$issue</li>";
        }
        echo "</ul>";
        echo "<div class='info'><strong>Fix:</strong> Visit complete-booking.php - it will automatically create missing columns.</div>";
    }
    
    if(count($warnings) > 0) {
        echo "<h3 class='warning'>Warnings:</h3><ul>";
        foreach($warnings as $warning) {
            echo "<li class='warning'>$warning</li>";
        }
        echo "</ul>";
        echo "<div class='info'><strong>Fix:</strong> Set folder permissions to 777 or create folders manually.</div>";
    }
}
echo "</div>";
?>
