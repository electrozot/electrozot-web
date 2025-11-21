<?php
/**
 * Notification System Test & Debug Page
 * Use this to test and troubleshoot the notification system
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Clear shown notifications if requested
if (isset($_GET['clear_session'])) {
    $_SESSION['shown_notifications'] = [];
    header('Location: test-notifications.php?cleared=1');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notification System Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 20px; }
        .test-card { margin-bottom: 20px; }
        .log-entry { 
            padding: 8px 12px; 
            margin: 5px 0; 
            border-left: 3px solid #667eea; 
            background: #f8f9fa;
            font-family: monospace;
            font-size: 13px;
        }
        .log-entry.success { border-left-color: #10b981; background: #f0fdf4; }
        .log-entry.error { border-left-color: #ef4444; background: #fef2f2; }
        .log-entry.warning { border-left-color: #f59e0b; background: #fffbeb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-vial"></i> Notification System Test & Debug</h4>
            </div>
            <div class="card-body">
                
                <?php if (isset($_GET['cleared'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Session cleared successfully!
                </div>
                <?php endif; ?>
                
                <!-- Session Info -->
                <div class="card test-card">
                    <div class="card-header"><strong>Session Information</strong></div>
                    <div class="card-body">
                        <p><strong>Admin ID:</strong> <?php echo $_SESSION['a_id']; ?></p>
                        <p><strong>Shown Notifications Count:</strong> 
                            <?php echo isset($_SESSION['shown_notifications']) ? count($_SESSION['shown_notifications']) : 0; ?>
                        </p>
                        <?php if (isset($_SESSION['shown_notifications']) && count($_SESSION['shown_notifications']) > 0): ?>
                        <details>
                            <summary>View Tracked Notifications</summary>
                            <pre><?php print_r($_SESSION['shown_notifications']); ?></pre>
                        </details>
                        <?php endif; ?>
                        <a href="?clear_session=1" class="btn btn-warning btn-sm mt-2">
                            <i class="fas fa-trash"></i> Clear Session
                        </a>
                    </div>
                </div>
                
                <!-- Recent Bookings -->
                <div class="card test-card">
                    <div class="card-header"><strong>Recent Bookings (Last 5 Minutes)</strong></div>
                    <div class="card-body">
                        <?php
                        $recent_query = "SELECT 
                                            sb_id, 
                                            sb_status, 
                                            sb_created_at,
                                            TIMESTAMPDIFF(SECOND, sb_created_at, NOW()) as seconds_ago
                                         FROM tms_service_booking 
                                         WHERE sb_created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                                         ORDER BY sb_created_at DESC";
                        $recent_result = $mysqli->query($recent_query);
                        
                        if ($recent_result && $recent_result->num_rows > 0):
                        ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Seconds Ago</th>
                                    <th>Should Notify?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $recent_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['sb_id']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['sb_status'] == 'Pending' ? 'warning' : 'secondary'; ?>">
                                            <?php echo $row['sb_status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $row['sb_created_at']; ?></td>
                                    <td><?php echo $row['seconds_ago']; ?>s</td>
                                    <td>
                                        <?php 
                                        $notif_id = 'booking_' . $row['sb_id'];
                                        $should_notify = ($row['seconds_ago'] <= 30 && 
                                                         $row['sb_status'] == 'Pending' && 
                                                         !isset($_SESSION['shown_notifications'][$notif_id]));
                                        ?>
                                        <span class="badge badge-<?php echo $should_notify ? 'success' : 'secondary'; ?>">
                                            <?php echo $should_notify ? 'YES' : 'NO'; ?>
                                        </span>
                                        <?php if (!$should_notify && isset($_SESSION['shown_notifications'][$notif_id])): ?>
                                        <small class="text-muted">(Already shown)</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-muted">No bookings in the last 5 minutes</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- API Test -->
                <div class="card test-card">
                    <div class="card-header"><strong>API Response Test</strong></div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="testAPI()">
                            <i class="fas fa-play"></i> Test API Call
                        </button>
                        <button class="btn btn-success" onclick="testSound()">
                            <i class="fas fa-volume-up"></i> Test Sound
                        </button>
                        <div id="apiResponse" class="mt-3"></div>
                    </div>
                </div>
                
                <!-- Live Log -->
                <div class="card test-card">
                    <div class="card-header">
                        <strong>Live Notification Log</strong>
                        <button class="btn btn-sm btn-secondary float-right" onclick="clearLog()">Clear Log</button>
                    </div>
                    <div class="card-body">
                        <div id="liveLog" style="max-height: 400px; overflow-y: auto;">
                            <div class="log-entry">Waiting for notifications...</div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <audio id="testSound">
        <source src="vendor/sounds/arived.mp3" type="audio/mpeg">
    </audio>
    
    <script>
    let logCount = 0;
    
    function addLog(message, type = 'info') {
        const logDiv = document.getElementById('liveLog');
        const entry = document.createElement('div');
        entry.className = 'log-entry ' + type;
        const timestamp = new Date().toLocaleTimeString();
        entry.textContent = `[${timestamp}] ${message}`;
        logDiv.insertBefore(entry, logDiv.firstChild);
        logCount++;
        
        // Keep only last 50 entries
        if (logCount > 50) {
            logDiv.removeChild(logDiv.lastChild);
        }
    }
    
    function clearLog() {
        document.getElementById('liveLog').innerHTML = '<div class="log-entry">Log cleared</div>';
        logCount = 0;
    }
    
    function testAPI() {
        addLog('Testing API call...', 'info');
        const lastCheck = Math.floor(Date.now() / 1000) - 60;
        
        fetch('api-unified-notifications.php?last_check=' + lastCheck)
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('apiResponse');
                responseDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
                if (data.success) {
                    addLog(`API Success: ${data.new_count} new notifications`, 'success');
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notif => {
                            addLog(`  - ${notif.type}: ${notif.message}`, 'success');
                        });
                    }
                } else {
                    addLog('API Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                addLog('API Call Failed: ' + error, 'error');
            });
    }
    
    function testSound() {
        const sound = document.getElementById('testSound');
        sound.currentTime = 0;
        sound.volume = 0.8;
        sound.play()
            .then(() => {
                addLog('Sound played successfully', 'success');
            })
            .catch(error => {
                addLog('Sound failed: ' + error, 'error');
            });
    }
    
    // Monitor API calls
    let checkCount = 0;
    setInterval(() => {
        checkCount++;
        const lastCheck = Math.floor(Date.now() / 1000) - 5;
        
        fetch('api-unified-notifications.php?last_check=' + lastCheck)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.new_count > 0) {
                        addLog(`Check #${checkCount}: ${data.new_count} new notification(s) found!`, 'warning');
                        data.notifications.forEach(notif => {
                            addLog(`  → ${notif.type}: ${notif.message}`, 'warning');
                        });
                    } else {
                        addLog(`Check #${checkCount}: No new notifications (${data.unread_count} total unread)`, 'info');
                    }
                }
            })
            .catch(error => {
                addLog(`Check #${checkCount}: Failed - ${error}`, 'error');
            });
    }, 5000); // Check every 5 seconds
    
    addLog('Test page loaded - monitoring started', 'success');
    </script>
</body>
</html>
