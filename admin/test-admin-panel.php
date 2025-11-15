<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$test_results = [];
$errors = [];
$warnings = [];

// Test 1: Check all admin pages exist
$admin_pages = [
    'admin-dashboard.php' => 'Dashboard',
    'admin-quick-booking.php' => 'Quick Booking',
    'admin-all-bookings.php' => 'All Bookings',
    'admin-add-technician.php' => 'Add Technician',
    'admin-manage-technician.php' => 'Manage Technicians',
    'admin-manage-technician-passwords.php' => 'Manage Technician Passwords',
    'admin-add-service.php' => 'Add Service',
    'admin-manage-service.php' => 'Manage Services',
    'admin-add-user.php' => 'Add User',
    'admin-manage-user.php' => 'Manage Users',
    'admin-manage-user-passwords.php' => 'Manage User Passwords',
    'admin-add-feedback.php' => 'Add Feedback',
    'admin-manage-feedback.php' => 'Manage Feedbacks',
    'admin-view-feedback.php' => 'View Feedbacks',
    'admin-publish-feedback.php' => 'Publish Feedbacks',
    'admin-view-syslogs.php' => 'System Logs',
    'admin-recycle-bin.php' => 'Recycle Bin',
    'admin-manage-gallery.php' => 'Gallery Images',
    'admin-manage-slider.php' => 'Home Slider',
    'admin-profile.php' => 'Admin Profile',
    'admin-change-password.php' => 'Change Password'
];

foreach($admin_pages as $file => $name) {
    if(file_exists($file)) {
        $test_results[] = "✅ $name ($file) - EXISTS";
    } else {
        $errors[] = "❌ $name ($file) - MISSING";
    }
}

// Test 2: Check database tables
$required_tables = [
    'tms_admin' => 'Admin Users',
    'tms_user' => 'Users',
    'tms_technician' => 'Technicians',
    'tms_service' => 'Services',
    'tms_service_booking' => 'Service Bookings',
    'tms_feedback' => 'Feedbacks',
    'tms_syslogs' => 'System Logs',
    'tms_recycle_bin' => 'Recycle Bin'
];

foreach($required_tables as $table => $name) {
    $check = $mysqli->query("SHOW TABLES LIKE '$table'");
    if($check && $check->num_rows > 0) {
        $test_results[] = "✅ Table: $name ($table) - EXISTS";
    } else {
        $errors[] = "❌ Table: $name ($table) - MISSING";
    }
}

// Test 3: Check critical columns in tms_technician
$tech_columns = ['t_id', 't_name', 't_phone', 't_ez_id', 't_category', 't_status', 't_pwd'];
foreach($tech_columns as $col) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$col'");
    if($check && $check->num_rows > 0) {
        $test_results[] = "✅ Technician Column: $col - EXISTS";
    } else {
        $warnings[] = "⚠️ Technician Column: $col - MISSING (may need migration)";
    }
}

// Test 4: Check sidebar links
$sidebar_file = 'vendor/inc/sidebar.php';
if(file_exists($sidebar_file)) {
    $sidebar_content = file_get_contents($sidebar_file);
    
    // Check for dropdown styling
    if(strpos($sidebar_content, 'background-color: #4a5568') !== false) {
        $test_results[] = "✅ Sidebar dropdown styling - APPLIED";
    } else {
        $warnings[] = "⚠️ Sidebar dropdown styling - NOT FOUND";
    }
    
    // Check for merged menu items
    if(strpos($sidebar_content, 'admin-manage-technician.php') !== false) {
        $test_results[] = "✅ Manage Technicians link - EXISTS";
    } else {
        $errors[] = "❌ Manage Technicians link - MISSING";
    }
} else {
    $errors[] = "❌ Sidebar file - MISSING";
}

// Test 5: Check navigation bar
$nav_file = 'vendor/inc/nav.php';
if(file_exists($nav_file)) {
    $nav_content = file_get_contents($nav_file);
    
    // Check for Quick Booking button
    if(strpos($nav_content, 'admin-quick-booking.php') !== false) {
        $test_results[] = "✅ Quick Booking button in navbar - EXISTS";
    } else {
        $warnings[] = "⚠️ Quick Booking button in navbar - NOT FOUND";
    }
    
    // Check for centered button
    if(strpos($nav_content, 'mx-auto') !== false) {
        $test_results[] = "✅ Quick Booking button - CENTERED";
    } else {
        $warnings[] = "⚠️ Quick Booking button - NOT CENTERED";
    }
} else {
    $errors[] = "❌ Navigation file - MISSING";
}

// Test 6: Check data counts
$counts = [];
$counts['Technicians'] = $mysqli->query("SELECT COUNT(*) as c FROM tms_technician")->fetch_object()->c;
$counts['Users'] = $mysqli->query("SELECT COUNT(*) as c FROM tms_user")->fetch_object()->c;
$counts['Services'] = $mysqli->query("SELECT COUNT(*) as c FROM tms_service")->fetch_object()->c;
$counts['Bookings'] = $mysqli->query("SELECT COUNT(*) as c FROM tms_service_booking")->fetch_object()->c;

foreach($counts as $type => $count) {
    $test_results[] = "📊 $type: $count records";
}

// Test 7: Check system logs functionality
$log_check = $mysqli->query("SELECT COUNT(*) as c FROM tms_syslogs");
if($log_check) {
    $log_count = $log_check->fetch_object()->c;
    $test_results[] = "✅ System Logs: $log_count entries";
} else {
    $warnings[] = "⚠️ System Logs table may need setup";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel Testing</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .test-container { max-width: 1200px; margin: 0 auto; }
        .test-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .result-item { padding: 8px; border-bottom: 1px solid #eee; }
        .result-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-card">
            <h2><i class="fas fa-vial"></i> Admin Panel Unit Testing</h2>
            <p class="text-muted">Comprehensive testing of all admin functionality</p>
            <hr>
            
            <!-- Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h4><?php echo count($test_results); ?></h4>
                        <small>Tests Passed</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-danger">
                        <h4><?php echo count($errors); ?></h4>
                        <small>Errors Found</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <h4><?php echo count($warnings); ?></h4>
                        <small>Warnings</small>
                    </div>
                </div>
            </div>
            
            <!-- Errors -->
            <?php if(count($errors) > 0): ?>
            <div class="test-card" style="border-left: 4px solid #dc3545;">
                <h4 class="error"><i class="fas fa-times-circle"></i> Errors (Critical)</h4>
                <?php foreach($errors as $error): ?>
                <div class="result-item error"><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Warnings -->
            <?php if(count($warnings) > 0): ?>
            <div class="test-card" style="border-left: 4px solid #ffc107;">
                <h4 class="warning"><i class="fas fa-exclamation-triangle"></i> Warnings</h4>
                <?php foreach($warnings as $warning): ?>
                <div class="result-item warning"><?php echo $warning; ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Passed Tests -->
            <div class="test-card" style="border-left: 4px solid #28a745;">
                <h4 class="success"><i class="fas fa-check-circle"></i> Passed Tests</h4>
                <?php foreach($test_results as $result): ?>
                <div class="result-item success"><?php echo $result; ?></div>
                <?php endforeach; ?>
            </div>
            
            <!-- Actions -->
            <div class="mt-4">
                <a href="admin-dashboard.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <button onclick="location.reload()" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Re-run Tests
                </button>
            </div>
        </div>
    </div>
</body>
</html>
