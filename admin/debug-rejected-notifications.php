<?php
/**
 * Debug script for rejected booking notifications
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Rejected Notifications</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .section { background: #252526; padding: 15px; margin: 10px 0; border-left: 3px solid #007acc; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        h2 { color: #4ec9b0; border-bottom: 1px solid #007acc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #3e3e42; }
        th { background: #2d2d30; color: #4ec9b0; }
        .query { background: #1e1e1e; padding: 10px; border: 1px solid #3e3e42; margin: 10px 0; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #007acc; color: white; text-decoration: none; margin: 5px; border-radius: 3px; }
        .btn:hover { background: #005a9e; }
    </style>
</head>
<body>
    <h1>🔍 Debug Rejected Booking Notifications</h1>
    
    <div class="section">
        <h2>Step 1: Check for "Not Completed" Bookings</h2>
        <?php
        $query1 = "SELECT 
                    sb.sb_id,
                    sb.sb_status,
                    sb.sb_updated_at,
                    sb.sb_technician_id,
                    sb.sb_rejection_reason,
                    s.s_name as service_name,
                    t.t_name as tech_name,
                    TIMESTAMPDIFF(SECOND, sb.sb_updated_at, NOW()) as seconds_ago
                   FROM tms_service_booking sb
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                   WHERE sb.sb_status = 'Not Completed'
                   ORDER BY sb.sb_updated_at DESC
                   LIMIT 10";
        
        echo '<div class="query">' . htmlspecialchars($query1) . '</div>';
        
        $result1 = $mysqli->query($query1);
        if ($result1 && $result1->num_rows > 0) {
            echo '<p class="success">✅ Found ' . $result1->num_rows . ' "Not Completed" booking(s)</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Status</th><th>Service</th><th>Technician</th><th>Reason</th><th>Updated</th><th>Seconds Ago</th></tr>';
            while ($row = $result1->fetch_assoc()) {
                $highlight = $row['seconds_ago'] <= 30 ? 'style="background: #1a3a1a;"' : '';
                echo '<tr ' . $highlight . '>';
                echo '<td>' . $row['sb_id'] . '</td>';
                echo '<td>' . $row['sb_status'] . '</td>';
                echo '<td>' . $row['service_name'] . '</td>';
                echo '<td>' . ($row['tech_name'] ?? 'NULL') . ' (ID: ' . ($row['sb_technician_id'] ?? 'NULL') . ')</td>';
                echo '<td>' . ($row['sb_rejection_reason'] ?? 'N/A') . '</td>';
                echo '<td>' . $row['sb_updated_at'] . '</td>';
                echo '<td>' . $row['seconds_ago'] . 's</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<p class="warning">⚠️ Rows highlighted in green are within 30 seconds (should trigger notification)</p>';
        } else {
            echo '<p class="error">❌ No "Not Completed" bookings found</p>';
            echo '<p>To test: Assign a booking to a technician, then have them reject it.</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Step 2: Test Notification Query (Last 30 Seconds)</h2>
        <?php
        $query2 = "SELECT 
                    sb.sb_id,
                    sb.sb_status,
                    COALESCE(sb.sb_updated_at, NOW()) as sb_updated_at,
                    COALESCE(s.s_name, 'Service') as service_name,
                    COALESCE(t.t_name, 'Technician') as tech_name,
                    TIMESTAMPDIFF(SECOND, sb.sb_updated_at, NOW()) as seconds_ago
                   FROM tms_service_booking sb
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                   WHERE sb.sb_status IN ('Rejected', 'Rejected by Technician', 'Not Completed')
                   AND sb.sb_updated_at IS NOT NULL
                   AND sb.sb_updated_at >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
                   ORDER BY sb.sb_id DESC
                   LIMIT 5";
        
        echo '<div class="query">' . htmlspecialchars($query2) . '</div>';
        
        $result2 = $mysqli->query($query2);
        if ($result2 && $result2->num_rows > 0) {
            echo '<p class="success">✅ Found ' . $result2->num_rows . ' booking(s) that SHOULD trigger notification</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Status</th><th>Service</th><th>Technician</th><th>Updated</th><th>Seconds Ago</th></tr>';
            while ($row = $result2->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['sb_id'] . '</td>';
                echo '<td>' . $row['sb_status'] . '</td>';
                echo '<td>' . $row['service_name'] . '</td>';
                echo '<td>' . $row['tech_name'] . '</td>';
                echo '<td>' . $row['sb_updated_at'] . '</td>';
                echo '<td>' . $row['seconds_ago'] . 's</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">⚠️ No bookings found in last 30 seconds</p>';
            echo '<p>This is normal if no bookings were rejected recently.</p>';
            echo '<p><strong>To test:</strong> Reject a booking as technician, then refresh this page within 30 seconds.</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Step 3: Check Session Tracking</h2>
        <?php
        if (isset($_SESSION['shown_notifications'])) {
            echo '<p class="success">✅ Session tracking is active</p>';
            echo '<p>Tracked notifications: ' . count($_SESSION['shown_notifications']) . '</p>';
            if (count($_SESSION['shown_notifications']) > 0) {
                echo '<table>';
                echo '<tr><th>Notification ID</th><th>Timestamp</th><th>Age (seconds)</th></tr>';
                foreach ($_SESSION['shown_notifications'] as $id => $timestamp) {
                    $age = time() - $timestamp;
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($id) . '</td>';
                    echo '<td>' . date('Y-m-d H:i:s', $timestamp) . '</td>';
                    echo '<td>' . $age . 's</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '<p><a href="?clear_session=1" class="btn">Clear Session Tracking</a></p>';
            }
        } else {
            echo '<p class="warning">⚠️ Session tracking not initialized yet</p>';
        }
        
        if (isset($_GET['clear_session'])) {
            unset($_SESSION['shown_notifications']);
            echo '<p class="success">✅ Session cleared! <a href="debug-rejected-notifications.php">Refresh</a></p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Step 4: Test API Call</h2>
        <p>Click button to call the notification API and see the response:</p>
        <button onclick="testAPI()" class="btn">Test API Call</button>
        <div id="apiResponse" style="margin-top: 10px;"></div>
        
        <script>
        function testAPI() {
            const responseDiv = document.getElementById('apiResponse');
            responseDiv.innerHTML = '<p style="color: #dcdcaa;">⏳ Calling API...</p>';
            
            fetch('api-unified-notifications.php?last_check=' + Math.floor(Date.now() / 1000 - 60))
                .then(response => response.json())
                .then(data => {
                    responseDiv.innerHTML = '<div class="query"><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                    
                    if (data.success) {
                        if (data.new_count > 0) {
                            responseDiv.innerHTML += '<p class="success">✅ API returned ' + data.new_count + ' notification(s)</p>';
                        } else {
                            responseDiv.innerHTML += '<p class="warning">⚠️ API returned 0 notifications (this is normal if no recent rejections)</p>';
                        }
                    } else {
                        responseDiv.innerHTML += '<p class="error">❌ API call failed</p>';
                    }
                })
                .catch(error => {
                    responseDiv.innerHTML = '<p class="error">❌ Error: ' + error + '</p>';
                });
        }
        </script>
    </div>
    
    <div class="section">
        <h2>Step 5: Check Unread Count</h2>
        <?php
        $count_query = "SELECT COUNT(*) as count 
                        FROM tms_service_booking 
                        WHERE sb_status IN ('Pending', 'Rejected', 'Rejected by Technician', 'Not Completed')";
        $count_result = $mysqli->query($count_query);
        $count_row = $count_result->fetch_object();
        $unread_count = $count_row->count;
        
        echo '<p>Total unread bookings: <strong class="success">' . $unread_count . '</strong></p>';
        
        $breakdown_query = "SELECT sb_status, COUNT(*) as count 
                           FROM tms_service_booking 
                           WHERE sb_status IN ('Pending', 'Rejected', 'Rejected by Technician', 'Not Completed')
                           GROUP BY sb_status";
        $breakdown_result = $mysqli->query($breakdown_query);
        
        if ($breakdown_result && $breakdown_result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Status</th><th>Count</th></tr>';
            while ($row = $breakdown_result->fetch_assoc()) {
                echo '<tr><td>' . $row['sb_status'] . '</td><td>' . $row['count'] . '</td></tr>';
            }
            echo '</table>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Testing Instructions</h2>
        <ol>
            <li><strong>Create a test booking</strong> and assign it to a technician</li>
            <li><strong>Login as that technician</strong> and reject the booking</li>
            <li><strong>Within 30 seconds</strong>, come back here and click "Test API Call"</li>
            <li>Check if the API returns the rejected booking</li>
            <li>Go to admin dashboard and check if notification appears</li>
        </ol>
        
        <h3>Expected Results:</h3>
        <ul>
            <li>✅ Step 1 shows the rejected booking</li>
            <li>✅ Step 2 shows the booking (if within 30 seconds)</li>
            <li>✅ Step 4 API returns notification with booking details</li>
            <li>✅ Admin dashboard shows popup + sound alert</li>
        </ul>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="admin-dashboard.php" class="btn">Go to Dashboard</a>
        <a href="admin-manage-service-booking.php" class="btn">View Bookings</a>
        <a href="debug-rejected-notifications.php" class="btn">Refresh Debug</a>
    </div>
</body>
</html>
