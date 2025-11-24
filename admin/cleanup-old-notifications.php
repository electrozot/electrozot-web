<?php
/**
 * Cleanup Old Notification Systems
 * Removes duplicate/old notification implementations
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cleanup Old Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #667eea; }
        .section { background: #f9fafb; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea; border-radius: 5px; }
        .success { border-left-color: #10b981; background: #ecfdf5; }
        .warning { border-left-color: #f59e0b; background: #fffbeb; }
        .btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
        .btn-danger { background: #ef4444; }
        .btn-success { background: #10b981; }
        .file-list { background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧹 Cleanup Old Notification Systems</h2>
        
        <div class="section warning">
            <h3>⚠️ Old Notification Files Found</h3>
            <p>The following old notification implementations should be removed or replaced:</p>
            <div class="file-list">
admin/vendor/inc/notification-system.php (OLD - uses Web Audio API)<br>
admin/vendor/inc/booking-notification-system.php (OLD)<br>
admin/vendor/inc/admin-notification-widget.php (OLD)<br>
admin/js/notification-system.js (OLD)<br>
admin/api-realtime-notifications.php (OLD)<br>
            </div>
        </div>
        
        <div class="section success">
            <h3>✅ New Unified System</h3>
            <p><strong>Single notification system for all admin pages:</strong></p>
            <div class="file-list">
admin/vendor/inc/unified-notification-system.php (NEW)<br>
admin/api-unified-notifications.php (NEW)<br>
            </div>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✓ Works on ALL admin pages</li>
                <li>✓ Uses correct sound file: vendor/sounds/arived.mp3</li>
                <li>✓ Shows popup + browser notification</li>
                <li>✓ Triggers for: New, Rejected, Completed, Cancelled bookings</li>
                <li>✓ Real-time updates every 3 seconds</li>
            </ul>
        </div>
        
        <div class="section">
            <h3>📝 How to Apply</h3>
            <p><strong>Step 1:</strong> Add to navigation file</p>
            <p>Edit <code>admin/vendor/inc/nav.php</code> and add this line before the closing <code>&lt;/nav&gt;</code> tag:</p>
            <div class="file-list">
&lt;?php include('vendor/inc/unified-notification-system.php'); ?&gt;
            </div>
            
            <p><strong>Step 2:</strong> Remove old includes</p>
            <p>Search for and remove these lines from nav.php or any other files:</p>
            <div class="file-list">
&lt;?php include('vendor/inc/notification-system.php'); ?&gt;<br>
&lt;?php include('vendor/inc/booking-notification-system.php'); ?&gt;<br>
&lt;?php include('vendor/inc/admin-notification-widget.php'); ?&gt;<br>
&lt;script src="js/notification-system.js"&gt;&lt;/script&gt;
            </div>
            
            <p><strong>Step 3:</strong> Test</p>
            <ul>
                <li>Create a new booking (from user dashboard or quick booking)</li>
                <li>Should hear sound and see popup notification</li>
                <li>Notification bell should show count</li>
            </ul>
        </div>
        
        <div class="section">
            <h3>🔊 Sound File Location</h3>
            <p>The system uses: <code>admin/vendor/sounds/arived.mp3</code></p>
            <p>Current path: <code>C:\Users\91821\Desktop\elecrozot backend server\htdocs\electrozot\admin\vendor\sounds\arived.mp3</code></p>
            <?php
            $sound_file = 'vendor/sounds/arived.mp3';
            if (file_exists($sound_file)) {
                echo '<p style="color: green;">✓ Sound file exists</p>';
                echo '<p>File size: ' . round(filesize($sound_file) / 1024, 2) . ' KB</p>';
            } else {
                echo '<p style="color: red;">✗ Sound file not found!</p>';
            }
            ?>
        </div>
        
        <div class="section">
            <h3>🎯 Notification Triggers</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f0f0f0;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Event</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Trigger</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Icon</th>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">New Booking</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">User dashboard / Admin quick booking / Guest booking</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">🆕 Green bell</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">Rejected</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Technician rejects booking</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">❌ Red X</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">Completed</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Technician completes booking</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">✅ Blue check</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">Cancelled</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">User or Admin cancels booking</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">⚠️ Orange ban</td>
                </tr>
            </table>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <h3>🔧 Quick Actions</h3>
        <a href="vendor/inc/nav.php" class="btn">Edit Navigation File</a>
        <a href="admin-dashboard.php" class="btn btn-success">Go to Dashboard</a>
        <a href="test-unified-notifications.php" class="btn">Test Notifications</a>
        
        <hr style="margin: 30px 0;">
        
        <h3>📖 Manual Steps</h3>
        <ol>
            <li>Open <code>admin/vendor/inc/nav.php</code></li>
            <li>Find any old notification includes and remove them</li>
            <li>Add this line before <code>&lt;/nav&gt;</code>:
                <div class="file-list" style="margin: 10px 0;">
&lt;?php include('vendor/inc/unified-notification-system.php'); ?&gt;
                </div>
            </li>
            <li>Save the file</li>
            <li>Refresh any admin page</li>
            <li>You should see the notification bell icon in the top navigation</li>
        </ol>
    </div>
</body>
</html>
