<?php
// COMPREHENSIVE SYSTEM FIX - Run this file once to fix all issues
session_start();
include('admin/vendor/inc/config.php');

echo "<h1>🔧 Fixing All System Logics</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#10b981;font-weight:bold;} .error{color:#ef4444;font-weight:bold;} .section{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}</style>";

$fixes_applied = [];
$errors = [];

// 1. Fix Service Booking Table
echo "<div class='section'><h2>1. Fixing Service Booking Table</h2>";
try {
    // Add all required columns
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_image VARCHAR(255) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_image VARCHAR(255) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_amount_charged DECIMAL(10,2) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completed_at TIMESTAMP NULL DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_not_done_reason TEXT DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_not_done_at TIMESTAMP NULL DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_pincode VARCHAR(10) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejection_reason TEXT DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejected_at TIMESTAMP NULL DEFAULT NULL");
    
    $fixes_applied[] = "✓ Service booking table columns added/verified";
    echo "<p class='success'>✓ Service booking table fixed</p>";
} catch(Exception $e) {
    $errors[] = "✗ Service booking table: " . $e->getMessage();
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 2. Fix Technician Table
echo "<div class='section'><h2>2. Fixing Technician Table</h2>";
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(20) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_email VARCHAR(100) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_addr TEXT DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_booking_id INT DEFAULT NULL");
    
    $fixes_applied[] = "✓ Technician table columns added/verified";
    echo "<p class='success'>✓ Technician table fixed</p>";
} catch(Exception $e) {
    $errors[] = "✗ Technician table: " . $e->getMessage();
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 3. Create Cancelled Bookings Table
echo "<div class='section'><h2>3. Creating Cancelled Bookings Table</h2>";
try {
    $create_cancelled = "CREATE TABLE IF NOT EXISTS tms_cancelled_bookings (
        cb_id INT AUTO_INCREMENT PRIMARY KEY,
        cb_booking_id INT NOT NULL,
        cb_technician_id INT NOT NULL,
        cb_cancelled_by VARCHAR(50) DEFAULT 'Admin',
        cb_reason VARCHAR(255) DEFAULT 'Technician reassigned by admin',
        cb_cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(cb_booking_id),
        INDEX(cb_technician_id)
    )";
    $mysqli->query($create_cancelled);
    
    $fixes_applied[] = "✓ Cancelled bookings table created/verified";
    echo "<p class='success'>✓ Cancelled bookings table ready</p>";
} catch(Exception $e) {
    $errors[] = "✗ Cancelled bookings table: " . $e->getMessage();
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Create Upload Folders
echo "<div class='section'><h2>4. Creating Upload Folders</h2>";
$folders = [
    'uploads/',
    'uploads/service_images/',
    'uploads/bill_images/'
];

foreach($folders as $folder) {
    if(!file_exists($folder)) {
        if(mkdir($folder, 0777, true)) {
            $fixes_applied[] = "✓ Created folder: $folder";
            echo "<p class='success'>✓ Created: $folder</p>";
        } else {
            $errors[] = "✗ Failed to create: $folder";
            echo "<p class='error'>✗ Failed to create: $folder</p>";
        }
    } else {
        echo "<p class='success'>✓ Exists: $folder</p>";
    }
    
    // Set permissions
    if(file_exists($folder)) {
        chmod($folder, 0777);
        echo "<p class='success'>✓ Permissions set: $folder</p>";
    }
}
echo "</div>";

// 5. Fix Booking Statuses
echo "<div class='section'><h2>5. Standardizing Booking Statuses</h2>";
try {
    // Update any old status names to new standard
    $mysqli->query("UPDATE tms_service_booking SET sb_status = 'Pending' WHERE sb_status IN ('New', 'Assigned')");
    $mysqli->query("UPDATE tms_service_booking SET sb_status = 'In Progress' WHERE sb_status = 'Processing'");
    
    // Get status counts
    $result = $mysqli->query("SELECT sb_status, COUNT(*) as count FROM tms_service_booking GROUP BY sb_status");
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Status</th><th>Count</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['sb_status'] . "</td><td>" . $row['count'] . "</td></tr>";
    }
    echo "</table>";
    
    $fixes_applied[] = "✓ Booking statuses standardized";
    echo "<p class='success'>✓ Statuses standardized</p>";
} catch(Exception $e) {
    $errors[] = "✗ Status fix: " . $e->getMessage();
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Set All Technicians as Available
echo "<div class='section'><h2>6. Resetting Technician Availability</h2>";
try {
    $mysqli->query("UPDATE tms_technician SET t_is_available = 1, t_current_booking_id = NULL");
    $count = $mysqli->affected_rows;
    
    $fixes_applied[] = "✓ Reset $count technicians to available";
    echo "<p class='success'>✓ All $count technicians set to available</p>";
} catch(Exception $e) {
    $errors[] = "✗ Technician availability: " . $e->getMessage();
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 7. Verify Database Integrity
echo "<div class='section'><h2>7. Database Integrity Check</h2>";
try {
    // Check for orphaned bookings
    $orphaned = $mysqli->query("SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id NOT IN (SELECT t_id FROM tms_technician)");
    $orphan_count = $orphaned->fetch_assoc()['count'];
    
    if($orphan_count > 0) {
        echo "<p class='error'>⚠ Found $orphan_count bookings with invalid technician IDs</p>";
        $errors[] = "⚠ $orphan_count orphaned bookings found";
    } else {
        echo "<p class='success'>✓ No orphaned bookings</p>";
    }
    
    // Check for orphaned users
    $orphaned_users = $mysqli->query("SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_user_id NOT IN (SELECT u_id FROM tms_user)");
    $orphan_user_count = $orphaned_users->fetch_assoc()['count'];
    
    if($orphan_user_count > 0) {
        echo "<p class='error'>⚠ Found $orphan_user_count bookings with invalid user IDs</p>";
        $errors[] = "⚠ $orphan_user_count orphaned user bookings found";
    } else {
        echo "<p class='success'>✓ No orphaned user bookings</p>";
    }
    
} catch(Exception $e) {
    $errors[] = "✗ Integrity check: " . $e->getMessage();
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 8. Test Queries
echo "<div class='section'><h2>8. Testing Critical Queries</h2>";

// Test technician dashboard query
try {
    $test_query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name 
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LIMIT 1";
    $result = $mysqli->query($test_query);
    if($result) {
        echo "<p class='success'>✓ Technician dashboard query works</p>";
    }
} catch(Exception $e) {
    $errors[] = "✗ Dashboard query: " . $e->getMessage();
    echo "<p class='error'>✗ Dashboard query failed: " . $e->getMessage() . "</p>";
}

// Test admin rejected bookings query
try {
    $test_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_status IN ('Rejected', 'Not Done')";
    $result = $mysqli->query($test_query);
    $count = $result->fetch_assoc()['count'];
    echo "<p class='success'>✓ Admin rejected bookings query works ($count found)</p>";
} catch(Exception $e) {
    $errors[] = "✗ Rejected bookings query: " . $e->getMessage();
    echo "<p class='error'>✗ Rejected bookings query failed: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Summary
echo "<div class='section'><h2>📊 Summary</h2>";
echo "<h3>Fixes Applied (" . count($fixes_applied) . "):</h3>";
echo "<ul>";
foreach($fixes_applied as $fix) {
    echo "<li class='success'>$fix</li>";
}
echo "</ul>";

if(count($errors) > 0) {
    echo "<h3>Errors/Warnings (" . count($errors) . "):</h3>";
    echo "<ul>";
    foreach($errors as $error) {
        echo "<li class='error'>$error</li>";
    }
    echo "</ul>";
} else {
    echo "<h2 class='success'>✅ ALL SYSTEMS FIXED AND OPERATIONAL!</h2>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Visit <a href='tech/dashboard.php'>Technician Dashboard</a> - Test booking display</li>";
echo "<li>Visit <a href='tech/check-system.php'>System Check</a> - Verify all systems</li>";
echo "<li>Visit <a href='admin/admin-dashboard.php'>Admin Dashboard</a> - Check admin panel</li>";
echo "<li>Visit <a href='admin/admin-rejected-bookings.php'>Rejected Bookings</a> - Check rejected bookings</li>";
echo "<li>Test completing a booking - Upload images and mark as done</li>";
echo "</ol>";

echo "</div>";

echo "<div class='section'>";
echo "<h2>🔄 Refresh Pages</h2>";
echo "<p>After running this fix, please:</p>";
echo "<ol>";
echo "<li>Clear your browser cache (Ctrl+Shift+Delete)</li>";
echo "<li>Logout and login again</li>";
echo "<li>Test all functionalities</li>";
echo "</ol>";
echo "</div>";
?>
