<?php
/**
 * Cleanup Old Service Structure Files
 * This script moves unused files to a backup folder
 * Run once to clean up old files that are no longer needed
 */

// Files that are no longer needed (replaced by new simplified structure)
$files_to_backup = [
    // Old "View" pages (merged into "Manage" pages)
    'admin/admin-view-service.php',
    'admin/admin-view-technician.php',
    'admin/admin-view-user.php',
    'admin/admin-view-feedback.php',
    
    // Old booking management pages (merged into admin-all-bookings.php)
    'admin/admin-manage-booking.php',
    'admin/admin-manage-service-booking.php',
    'admin/admin-view-booking.php',
    'admin/admin-view-service-booking.php',
    
    // Old feedback pages (merged into admin-manage-feedback.php)
    'admin/admin-add-feedback.php',
    'admin/admin-edit-feedback.php',
    
    // Old slider management pages (merged into admin-home-slider.php)
    'admin/admin-manage-slider.php',
    'admin/admin-edit-slider.php',
    
    // Old booking add pages (replaced by admin-quick-booking.php)
    'admin/admin-add-booking.php',
    'admin/admin-add-booking-usr.php',
];

// Create backup directory
$backup_dir = 'admin/OLD_FILES_BACKUP_' . date('Y-m-d');
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Cleanup Old Files</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; margin: 10px 0; border-radius: 5px; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; margin: 10px 0; border-radius: 5px; }
        .warning { color: orange; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; margin: 10px 0; border-radius: 5px; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; margin: 10px 0; border-radius: 5px; }
        h1 { color: #333; }
        .file-list { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🧹 Cleanup Old Service Structure Files</h1>
    <p>This script will move old, unused files to a backup folder.</p>
    <hr>";

$moved_count = 0;
$not_found_count = 0;
$error_count = 0;

foreach ($files_to_backup as $file) {
    if (file_exists($file)) {
        $filename = basename($file);
        $destination = $backup_dir . '/' . $filename;
        
        if (rename($file, $destination)) {
            echo "<div class='success'>✓ Moved: $file → $destination</div>";
            $moved_count++;
        } else {
            echo "<div class='error'>✗ Failed to move: $file</div>";
            $error_count++;
        }
    } else {
        echo "<div class='info'>ℹ Already removed or doesn't exist: $file</div>";
        $not_found_count++;
    }
}

echo "<hr>
    <h2>📊 Summary</h2>
    <div class='file-list'>
        <p><strong>Files moved to backup:</strong> $moved_count</p>
        <p><strong>Files not found (already removed):</strong> $not_found_count</p>
        <p><strong>Errors:</strong> $error_count</p>
        <p><strong>Backup location:</strong> $backup_dir</p>
    </div>";

if ($moved_count > 0) {
    echo "<div class='success'>
            <h3>✅ Cleanup Complete!</h3>
            <p>Old files have been moved to: <strong>$backup_dir</strong></p>
            <p>You can delete this backup folder later if everything works fine.</p>
          </div>";
}

echo "<div class='info'>
        <h3>📝 What Was Cleaned Up?</h3>
        <ul>
            <li><strong>View Pages:</strong> Merged into Manage pages</li>
            <li><strong>Old Booking Pages:</strong> Replaced by All Bookings</li>
            <li><strong>Old Feedback Pages:</strong> Merged into Manage Feedbacks</li>
            <li><strong>Old Slider Pages:</strong> Merged into Home Slider</li>
            <li><strong>Old Add Booking:</strong> Replaced by Quick Booking</li>
        </ul>
      </div>
      
      <div class='warning'>
        <h3>⚠️ Important Notes:</h3>
        <ul>
            <li>Files are moved to backup, not deleted</li>
            <li>Test your admin panel to ensure everything works</li>
            <li>If something breaks, restore files from backup folder</li>
            <li>After confirming everything works, you can delete the backup folder</li>
        </ul>
      </div>
      
      <p><a href='admin/admin-dashboard.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a></p>
</body>
</html>";
?>
