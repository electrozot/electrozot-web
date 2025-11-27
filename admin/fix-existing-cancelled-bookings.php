<?php
/**
 * Fix Existing Cancelled Bookings
 * Updates old cancelled bookings to set sb_cancelled_by = 'admin'
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Update all cancelled bookings that don't have sb_cancelled_by set
$update_query = "UPDATE tms_service_booking 
                 SET sb_cancelled_by = 'admin',
                     sb_cancelled_at = COALESCE(sb_cancelled_at, sb_updated_at)
                 WHERE sb_status = 'Cancelled' 
                 AND sb_cancelled_by IS NULL";

$result = $mysqli->query($update_query);
$affected = $mysqli->affected_rows;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Cancelled Bookings</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body { padding: 40px; background: #f5f7fa; font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .success { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-left: 5px solid #10b981; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .btn-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 25px; font-weight: 700; text-decoration: none; display: inline-block; margin-top: 20px; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); color: white; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h1 style="color: #2d3748; border-bottom: 4px solid #10b981; padding-bottom: 15px; margin-bottom: 30px;">
        <i class="fas fa-check-circle" style="color: #10b981;"></i> Fix Cancelled Bookings
    </h1>
    
    <div class="success">
        <h4 style="color: #065f46; margin-bottom: 15px;">
            <i class="fas fa-check-circle"></i> Update Complete!
        </h4>
        <p style="color: #047857; font-size: 1.1rem; margin: 0;">
            <strong><?php echo $affected; ?> booking(s)</strong> have been updated with <code>sb_cancelled_by = 'admin'</code>
        </p>
    </div>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
        <h5 style="color: #4a5568; margin-bottom: 15px;">What was fixed:</h5>
        <ul style="color: #6b7280;">
            <li>All cancelled bookings now have <code>sb_cancelled_by</code> set to 'admin'</li>
            <li>These bookings will no longer appear in the notification center</li>
            <li>Only technician rejections will show in notifications</li>
        </ul>
    </div>
    
    <div style="text-align: center;">
        <a href="admin-notifications.php" class="btn-custom">
            <i class="fas fa-bell"></i> View Notifications
        </a>
        <a href="admin-dashboard.php" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); margin-left: 10px;">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
