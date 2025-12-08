<?php
session_start();
include('../admin/vendor/inc/config.php');

if (!isset($_SESSION['t_id'])) {
    die('Please log in as technician first');
}

$t_id = $_SESSION['t_id'];

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Bookings</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #10b981; color: white; }
        .new { background: #d1fae5; font-weight: bold; }
        .old { opacity: 0.6; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Your Bookings (Last 24 Hours)</h1>
        
        <div class="info">
            <strong>Your Technician ID:</strong> <?php echo $t_id; ?><br>
            <strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>Detection Window:</strong> Last 10 minutes
        </div>
        
        <h2>Recent Bookings</h2>
        <?php
        // Get all bookings for this technician in last 24 hours
        $query = "SELECT 
                    sb.sb_id,
                    sb.sb_status,
                    sb.sb_created_at,
                    sb.sb_updated_at,
                    sb.sb_assigned_at,
                    sb.sb_is_on_hold,
                    sb.sb_is_high_priority,
                    COALESCE(u.u_fname, 'Guest') as u_fname,
                    COALESCE(u.u_lname, '') as u_lname,
                    COALESCE(u.u_phone, 'N/A') as u_phone,
                    COALESCE(s.s_name, 'Unknown Service') as s_name,
                    TIMESTAMPDIFF(MINUTE, sb.sb_updated_at, NOW()) as minutes_ago
                  FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                  WHERE sb.sb_technician_id = ?
                  AND sb.sb_updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                  ORDER BY sb.sb_updated_at DESC";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $t_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            echo '<div class="info">❌ No bookings found in last 24 hours for technician ID: ' . $t_id . '</div>';
            echo '<div class="info">
                <strong>To test:</strong><br>
                1. Go to admin panel<br>
                2. Assign a NEW booking to technician ID: <strong>' . $t_id . '</strong><br>
                3. Refresh this page<br>
                4. You should see the booking here
            </div>';
        } else {
            echo '<table>';
            echo '<tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Assigned</th>
                    <th>Minutes Ago</th>
                    <th>Will Notify?</th>
                  </tr>';
            
            while ($booking = $result->fetch_assoc()) {
                $minutes_ago = $booking['minutes_ago'];
                $will_notify = ($minutes_ago <= 10 && $booking['sb_status'] != 'Cancelled' && $booking['sb_status'] != 'Completed');
                $row_class = $will_notify ? 'new' : 'old';
                
                echo '<tr class="' . $row_class . '">';
                echo '<td>#' . $booking['sb_id'] . '</td>';
                echo '<td>' . htmlspecialchars($booking['u_fname'] . ' ' . $booking['u_lname']) . '</td>';
                echo '<td>' . htmlspecialchars($booking['s_name']) . '</td>';
                echo '<td>' . $booking['sb_status'] . '</td>';
                echo '<td>' . $booking['sb_created_at'] . '</td>';
                echo '<td>' . $booking['sb_updated_at'] . '</td>';
                echo '<td>' . ($booking['sb_assigned_at'] ?: 'Not set') . '</td>';
                echo '<td>' . $minutes_ago . ' min</td>';
                echo '<td>' . ($will_notify ? '✅ YES' : '❌ NO (too old)') . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            
            echo '<div class="info">
                <strong>Legend:</strong><br>
                🟢 <strong>Green rows</strong> = Updated in last 10 minutes (WILL trigger notification)<br>
                ⚪ <strong>Gray rows</strong> = Older than 10 minutes (will NOT trigger notification)
            </div>';
        }
        ?>
        
        <h2>Notification Tracking</h2>
        <?php
        // Check what notifications have been shown
        $track_query = "SELECT 
                          tnt_booking_id,
                          tnt_action_type,
                          tnt_booking_status,
                          tnt_shown_at,
                          TIMESTAMPDIFF(MINUTE, tnt_shown_at, NOW()) as shown_minutes_ago
                        FROM tms_technician_notification_tracking
                        WHERE tnt_technician_id = ?
                        ORDER BY tnt_shown_at DESC
                        LIMIT 20";
        
        $track_stmt = $mysqli->prepare($track_query);
        $track_stmt->bind_param('i', $t_id);
        $track_stmt->execute();
        $track_result = $track_stmt->get_result();
        
        if ($track_result->num_rows == 0) {
            echo '<div class="info">No notifications have been shown yet.</div>';
        } else {
            echo '<table>';
            echo '<tr>
                    <th>Booking ID</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Shown At</th>
                    <th>Minutes Ago</th>
                  </tr>';
            
            while ($track = $track_result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>#' . $track['tnt_booking_id'] . '</td>';
                echo '<td>' . $track['tnt_action_type'] . '</td>';
                echo '<td>' . $track['tnt_booking_status'] . '</td>';
                echo '<td>' . $track['tnt_shown_at'] . '</td>';
                echo '<td>' . $track['shown_minutes_ago'] . ' min ago</td>';
                echo '</tr>';
            }
            
            echo '</table>';
        }
        ?>
        
        <div class="info" style="margin-top: 30px;">
            <h3>🔍 How to Test:</h3>
            <ol>
                <li><strong>Assign a NEW booking</strong> from admin to technician ID: <strong><?php echo $t_id; ?></strong></li>
                <li><strong>Refresh this page</strong> - you should see it in green</li>
                <li><strong>Go to dashboard</strong> - notification should appear</li>
                <li><strong>Come back here</strong> - it should appear in "Notification Tracking" table</li>
            </ol>
            
            <p><strong>Important:</strong> Notifications only trigger for bookings updated in the <strong>last 10 minutes</strong>!</p>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="dashboard.php" style="background: #10b981; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; display: inline-block;">Go to Dashboard</a>
            <button onclick="location.reload()" style="background: #3b82f6; color: white; padding: 12px 30px; border-radius: 5px; border: none; cursor: pointer; margin-left: 10px;">Refresh</button>
        </div>
    </div>
</body>
</html>
