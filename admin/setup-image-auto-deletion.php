<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<h2>Setting up Automatic Image Deletion System...</h2>";

// Ensure sb_completed_date column exists
$check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE 'sb_completed_date'");
if($check->num_rows == 0) {
    echo "<p>Adding sb_completed_date column...</p>";
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN sb_completed_date DATETIME DEFAULT NULL AFTER sb_status");
    echo "<p style='color: green;'>✓ sb_completed_date column added successfully</p>";
    
    // Update existing completed bookings with current timestamp
    $mysqli->query("UPDATE tms_service_booking SET sb_completed_date = NOW() WHERE sb_status = 'Completed' AND sb_completed_date IS NULL");
    echo "<p style='color: green;'>✓ Updated existing completed bookings with current date</p>";
} else {
    echo "<p style='color: blue;'>✓ sb_completed_date column already exists</p>";
}

// Create completions directory if it doesn't exist
$completions_dir = __DIR__ . '/../vendor/img/completions';
if (!is_dir($completions_dir)) {
    mkdir($completions_dir, 0755, true);
    echo "<p style='color: green;'>✓ Created completions directory</p>";
} else {
    echo "<p style='color: blue;'>✓ Completions directory exists</p>";
}

// Create bills directory if it doesn't exist
$bills_dir = __DIR__ . '/../vendor/img/bills';
if (!is_dir($bills_dir)) {
    mkdir($bills_dir, 0755, true);
    echo "<p style='color: green;'>✓ Created bills directory</p>";
} else {
    echo "<p style='color: blue;'>✓ Bills directory exists</p>";
}

// Create logs directory for cron job
$logs_dir = __DIR__ . '/logs';
if (!is_dir($logs_dir)) {
    mkdir($logs_dir, 0755, true);
    echo "<p style='color: green;'>✓ Created logs directory</p>";
} else {
    echo "<p style='color: blue;'>✓ Logs directory exists</p>";
}

echo "<hr>";
echo "<h3 style='color: green;'>✓ Setup Complete!</h3>";
echo "<div style='background: #f0f8ff; padding: 20px; border-left: 4px solid #007bff; margin: 20px 0;'>";
echo "<h4>Automatic Image Deletion System Configured</h4>";
echo "<p><strong>How it works:</strong></p>";
echo "<ul>";
echo "<li><strong>Customer View:</strong> Images are hidden after 31 days from service completion</li>";
echo "<li><strong>Admin View:</strong> Images are hidden after 40 days from service completion</li>";
echo "<li><strong>Physical Deletion:</strong> Images are permanently deleted after 40 days by cron job</li>";
echo "</ul>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Set up a daily cron job to run: <code>php " . __DIR__ . "/cron-delete-old-images.php</code></li>";
echo "<li>Or access via URL (secure): <code>https://yourdomain.com/admin/cron-delete-old-images.php?token=electrozot_secure_cron_2024</code></li>";
echo "<li>Recommended cron schedule: Daily at 2 AM - <code>0 2 * * * /usr/bin/php " . __DIR__ . "/cron-delete-old-images.php</code></li>";
echo "</ol>";
echo "<p><strong>Note:</strong> No messages are shown to users about image deletion. Images simply disappear after the specified period.</p>";
echo "</div>";
echo "<p><a href='admin-dashboard.php' class='btn btn-primary'>Back to Dashboard</a></p>";
?>
