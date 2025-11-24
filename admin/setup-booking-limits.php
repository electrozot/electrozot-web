<?php
/**
 * Setup Script: Add Booking Limit Columns
 * Run this once to add the booking limit feature to your database
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$success = [];
$errors = [];

// Add t_booking_limit column
try {
    $result = $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT NOT NULL DEFAULT 1");
    if ($result) {
        $success[] = "Added t_booking_limit column";
    }
} catch (Exception $e) {
    $errors[] = "Error adding t_booking_limit: " . $e->getMessage();
}

// Add t_current_bookings column
try {
    $result = $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT NOT NULL DEFAULT 0");
    if ($result) {
        $success[] = "Added t_current_bookings column";
    }
} catch (Exception $e) {
    $errors[] = "Error adding t_current_bookings: " . $e->getMessage();
}

// Update existing technicians to have default values
try {
    $result = $mysqli->query("UPDATE tms_technician SET t_booking_limit = 1, t_current_bookings = 0 WHERE t_booking_limit IS NULL OR t_booking_limit = 0");
    if ($result) {
        $success[] = "Updated existing technicians with default values";
    }
} catch (Exception $e) {
    $errors[] = "Error updating defaults: " . $e->getMessage();
}

// Calculate current bookings for each technician
try {
    $result = $mysqli->query("
        UPDATE tms_technician t
        SET t_current_bookings = (
            SELECT COUNT(*) 
            FROM tms_service_booking sb 
            WHERE sb.sb_technician_id = t.t_id 
            AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
        )
    ");
    if ($result) {
        $success[] = "Calculated current bookings for all technicians";
    }
} catch (Exception $e) {
    $errors[] = "Error calculating current bookings: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Limit Setup</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body { padding: 50px; background: #f8f9fa; }
        .container { max-width: 800px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4"><i class="fas fa-cog"></i> Booking Limit Feature Setup</h2>
        
        <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle"></i> Success!</h5>
            <ul>
                <?php foreach ($success as $msg): ?>
                <li><?php echo $msg; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Errors</h5>
            <ul>
                <?php foreach ($errors as $msg): ?>
                <li><?php echo $msg; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> What was done:</h5>
            <ol>
                <li>Added <code>t_booking_limit</code> column (default: 1)</li>
                <li>Added <code>t_current_bookings</code> column (default: 0)</li>
                <li>Set default values for existing technicians</li>
                <li>Calculated current active bookings for each technician</li>
            </ol>
        </div>
        
        <div class="mt-4">
            <a href="admin-manage-technician.php" class="btn btn-primary">
                <i class="fas fa-users"></i> Go to Manage Technicians
            </a>
            <a href="admin-add-technician.php" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add New Technician
            </a>
        </div>
    </div>
    
    <script src="vendor/fontawesome-free/js/all.min.js"></script>
</body>
</html>
