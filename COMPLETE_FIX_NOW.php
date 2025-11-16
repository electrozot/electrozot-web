<?php
// COMPLETE SYSTEM FIX - This will fix EVERYTHING
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('admin/vendor/inc/config.php');

echo "<!DOCTYPE html><html><head><title>Complete System Fix</title>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #dc3545; }
.warning { background: #fff3cd; color: #856404; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #ffc107; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #17a2b8; }
h1 { color: #667eea; }
h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
.section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #667eea; color: white; }
.btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
</style></head><body>";

echo "<h1>🔧 COMPLETE SYSTEM FIX</h1>";
echo "<p>This will fix ALL issues with your booking system...</p>";

$fixes = [];
$errors = [];

// ============================================
// STEP 1: FIX DATABASE STRUCTURE
// ============================================
echo "<div class='section'>";
echo "<h2>Step 1: Fixing Database Structure</h2>";

// Service Booking Table
$booking_columns = [
    'sb_service_image' => 'VARCHAR(255) DEFAULT NULL',
    'sb_bill_image' => 'VARCHAR(255) DEFAULT NULL',
    'sb_amount_charged' => 'DECIMAL(10,2) DEFAULT NULL',
    'sb_completed_at' => 'TIMESTAMP NULL DEFAULT NULL',
    'sb_not_done_reason' => 'TEXT DEFAULT NULL',
    'sb_not_done_at' => 'TIMESTAMP NULL DEFAULT NULL',
    'sb_rejection_reason' => 'TEXT DEFAULT NULL',
    'sb_rejected_at' => 'TIMESTAMP NULL DEFAULT NULL',
    'sb_pincode' => 'VARCHAR(10) DEFAULT NULL'
];

echo "<h3>Service Booking Table:</h3>";
foreach($booking_columns as $col => $def) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$col'");
    if($check->num_rows == 0) {
        try {
            $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN $col $def");
            echo "<div class='success'>✓ Added: $col</div>";
            $fixes[] = "Added column: tms_service_booking.$col";
        } catch(Exception $e) {
            echo "<div class='error'>✗ Failed: $col - " . $e->getMessage() . "</div>";
            $errors[] = "Failed to add: tms_service_booking.$col";
        }
    } else {
        echo "<div class='info'>✓ Exists: $col</div>";
    }
}

// Technician Table
$tech_columns = [
    't_phone' => 'VARCHAR(20) DEFAULT ""',
    't_email' => 'VARCHAR(100) DEFAULT ""',
    't_addr' => 'TEXT DEFAULT ""',
    't_is_available' => 'TINYINT(1) DEFAULT 1',
    't_current_booking_id' => 'INT DEFAULT NULL'
];

echo "<h3>Technician Table:</h3>";
foreach($tech_columns as $col => $def) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$col'");
    if($check->num_rows == 0) {
        try {
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN $col $def");
            echo "<div class='success'>✓ Added: $col</div>";
            $fixes[] = "Added column: tms_technician.$col";
        } catch(Exception $e) {
            echo "<div class='error'>✗ Failed: $col - " . $e->getMessage() . "</div>";
            $errors[] = "Failed to add: tms_technician.$col";
        }
    } else {
        echo "<div class='info'>✓ Exists: $col</div>";
    }
}

echo "</div>";

// ============================================
// STEP 2: RESET TECHNICIAN AVAILABILITY
// ============================================
echo "<div class='section'>";
echo "<h2>Step 2: Resetting Technician Availability</h2>";

try {
    $mysqli->query("UPDATE tms_technician SET t_is_available = 1, t_current_booking_id = NULL");
    $count = $mysqli->affected_rows;
    echo "<div class='success'>✓ Reset $count technicians to AVAILABLE</div>";
    $fixes[] = "Reset $count technicians to available";
} catch(Exception $e) {
    echo "<div class='error'>✗ Failed to reset technicians: " . $e->getMessage() . "</div>";
    $errors[] = "Failed to reset technicians";
}

echo "</div>";

// ============================================
// STEP 3: CREATE UPLOAD FOLDERS
// ============================================
echo "<div class='section'>";
echo "<h2>Step 3: Creating Upload Folders</h2>";

$folders = [
    'uploads/',
    'uploads/service_images/',
    'uploads/bill_images/'
];

foreach($folders as $folder) {
    if(!file_exists($folder)) {
        if(mkdir($folder, 0777, true)) {
            echo "<div class='success'>✓ Created: $folder</div>";
            $fixes[] = "Created folder: $folder";
        } else {
            echo "<div class='error'>✗ Failed to create: $folder</div>";
            $errors[] = "Failed to create: $folder";
        }
    } else {
        echo "<div class='info'>✓ Exists: $folder</div>";
    }
    
    if(file_exists($folder)) {
        chmod($folder, 0777);
    }
}

echo "</div>";

// ============================================
// STEP 4: TEST QUERIES
// ============================================
echo "<div class='section'>";
echo "<h2>Step 4: Testing Critical Queries</h2>";

// Test 1: Technician Dashboard Query
echo "<h3>Test 1: Technician Dashboard Query</h3>";
try {
    $test = $mysqli->query("SELECT sb.*, u.u_fname, u.u_lname, s.s_name 
                           FROM tms_service_booking sb
                           LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                           LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                           LIMIT 1");
    if($test) {
        echo "<div class='success'>✓ Technician dashboard query works</div>";
    }
} catch(Exception $e) {
    echo "<div class='error'>✗ Dashboard query failed: " . $e->getMessage() . "</div>";
    $errors[] = "Dashboard query failed";
}

// Test 2: Available Technicians Query
echo "<h3>Test 2: Available Technicians Query</h3>";
try {
    $test = $mysqli->query("SELECT t_id, t_name, t_category, t_is_available, t_current_booking_id 
                           FROM tms_technician 
                           WHERE (t_is_available = 1 OR t_status = 'Available')
                           AND (t_current_booking_id IS NULL OR t_current_booking_id = 0)");
    $count = $test->num_rows;
    echo "<div class='success'>✓ Available technicians query works ($count available)</div>";
} catch(Exception $e) {
    echo "<div class='error'>✗ Available technicians query failed: " . $e->getMessage() . "</div>";
    $errors[] = "Available technicians query failed";
}

// Test 3: Rejected Bookings Query
echo "<h3>Test 3: Rejected Bookings Query</h3>";
try {
    $test = $mysqli->query("SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_status IN ('Rejected', 'Not Done')");
    $row = $test->fetch_assoc();
    echo "<div class='success'>✓ Rejected bookings query works (" . $row['count'] . " found)</div>";
} catch(Exception $e) {
    echo "<div class='error'>✗ Rejected bookings query failed: " . $e->getMessage() . "</div>";
    $errors[] = "Rejected bookings query failed";
}

echo "</div>";

// ============================================
// STEP 5: SHOW CURRENT STATUS
// ============================================
echo "<div class='section'>";
echo "<h2>Step 5: Current System Status</h2>";

// Booking counts
$status_query = "SELECT sb_status, COUNT(*) as count FROM tms_service_booking GROUP BY sb_status";
$status_result = $mysqli->query($status_query);

echo "<h3>Bookings by Status:</h3>";
echo "<table>";
echo "<tr><th>Status</th><th>Count</th></tr>";
while($row = $status_result->fetch_assoc()) {
    echo "<tr><td>" . $row['sb_status'] . "</td><td>" . $row['count'] . "</td></tr>";
}
echo "</table>";

// Technician availability
$tech_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN t_is_available = 1 AND (t_current_booking_id IS NULL OR t_current_booking_id = 0) THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN t_is_available = 0 OR (t_current_booking_id IS NOT NULL AND t_current_booking_id != 0) THEN 1 ELSE 0 END) as busy
FROM tms_technician";
$tech_result = $mysqli->query($tech_query);
$tech_stats = $tech_result->fetch_assoc();

echo "<h3>Technician Availability:</h3>";
echo "<table>";
echo "<tr><th>Status</th><th>Count</th></tr>";
echo "<tr><td>Total Technicians</td><td>" . $tech_stats['total'] . "</td></tr>";
echo "<tr><td>Available (Free)</td><td style='color:#10b981;font-weight:bold;'>" . $tech_stats['available'] . "</td></tr>";
echo "<tr><td>Busy (With Booking)</td><td style='color:#ef4444;font-weight:bold;'>" . $tech_stats['busy'] . "</td></tr>";
echo "</table>";

echo "</div>";

// ============================================
// SUMMARY
// ============================================
echo "<div class='section'>";
echo "<h2>📊 Summary</h2>";

echo "<h3>Fixes Applied (" . count($fixes) . "):</h3>";
if(count($fixes) > 0) {
    echo "<ul>";
    foreach($fixes as $fix) {
        echo "<li class='success'>✓ $fix</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No fixes needed - system was already configured correctly.</p>";
}

if(count($errors) > 0) {
    echo "<h3>Errors (" . count($errors) . "):</h3>";
    echo "<ul>";
    foreach($errors as $error) {
        echo "<li class='error'>✗ $error</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='success'><h2>✅ ALL SYSTEMS OPERATIONAL!</h2></div>";
}

echo "</div>";

// ============================================
// NEXT STEPS
// ============================================
echo "<div class='section'>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Clear browser cache</strong> (Ctrl+Shift+Delete)</li>";
echo "<li><strong>Logout and login again</strong></li>";
echo "<li><strong>Test technician dashboard:</strong> <a href='tech/dashboard.php' class='btn'>Technician Dashboard</a></li>";
echo "<li><strong>Test admin dashboard:</strong> <a href='admin/admin-dashboard.php' class='btn'>Admin Dashboard</a></li>";
echo "<li><strong>Test rejected bookings:</strong> <a href='admin/admin-rejected-bookings.php' class='btn'>Rejected Bookings</a></li>";
echo "<li><strong>Test completion:</strong> Try completing a booking with images</li>";
echo "<li><strong>Test reassignment:</strong> Try reassigning a rejected booking</li>";
echo "</ol>";

echo "<h3>Diagnostic Tools:</h3>";
echo "<a href='tech/check-system.php' class='btn'>System Check</a>";
echo "<a href='admin/test-technician-availability.php' class='btn'>Test Availability</a>";
echo "<a href='admin/test-available-technicians.php' class='btn'>Available Technicians</a>";

echo "</div>";

echo "</body></html>";
?>
