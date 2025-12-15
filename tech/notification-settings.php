<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$page_title = "Notification Settings";

// Handle settings update
if ($_POST && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    if ($_POST['action'] === 'reset_audio') {
        // This will be handled by JavaScript localStorage
        $response = ['success' => true, 'message' => 'Audio settings reset successfully'];
    } elseif ($_POST['action'] === 'test_notification') {
        // Create a test notification entry
        try {
            $test_query = "INSERT INTO tms_technician_notifications (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message, tn_is_read) VALUES (?, 0, 'test', 'Test Notification', 'This is a test notification to check your sound alerts', 0)";
            $test_stmt = $mysqli->prepare($test_query);
            $test_stmt->bind_param('i', $t_id);
            $test_stmt->execute();
            $response = ['success' => true, 'message' => 'Test notification sent'];
        } catch(Exception $e) {
            $response = ['success' => false, 'message' => 'Failed to send test notification: ' . $e->getMessage()];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Settings - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        
        .settings-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .settings-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .settings-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .settings-body {
            padding: 30px;
        }
        
        .setting-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .setting-item:hover {
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }
        
        .setting-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .setting-description {
            color: #6b7280;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .setting-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .status-enabled {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-disabled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-unknown {
            background: #fef3c7;
            color: #92400e;
        }
        
        .setting-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-setting {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-link a {
            color: #6b7280;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link a:hover {
            color: #10b981;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <div class="settings-header">
            <h1><i class="fas fa-cog"></i> Notification Settings</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Manage your sound alerts and notification preferences</p>
        </div>
        
        <div class="settings-body">
            <div id="alert-container"></div>
            
            <!-- Sound Alerts Setting -->
            <div class="setting-item">
                <div class="setting-title">
                    <i class="fas fa-volume-up"></i>
                    Sound Alerts
                </div>
                <div class="setting-description">
                    Enable sound notifications for new booking assignments. Once enabled, you'll hear a sound alert whenever you receive a new booking.
                </div>
                <div id="sound-status" class="setting-status status-unknown">
                    <i class="fas fa-spinner fa-spin"></i> Checking...
                </div>
                <div class="setting-actions">
                    <button id="enable-sound-btn" class="btn-setting btn-primary">
                        <i class="fas fa-volume-up"></i> Enable Sound
                    </button>
                    <button id="test-sound-btn" class="btn-setting btn-secondary">
                        <i class="fas fa-play"></i> Test Sound
                    </button>
                    <button id="reset-sound-btn" class="btn-setting btn-danger">
                        <i class="fas fa-undo"></i> Reset Settings
                    </button>
                </div>
            </div>
            
            <!-- Browser Notifications Setting -->
            <div class="setting-item">
                <div class="setting-title">
                    <i class="fas fa-bell"></i>
                    Browser Notifications
                </div>
                <div class="setting-description">
                    Receive notifications even when the app is in background or your phone is locked. Essential for mobile users.
                </div>
                <div id="browser-status" class="setting-status status-unknown">
                    <i class="fas fa-spinner fa-spin"></i> Checking...
                </div>
                <div class="setting-actions">
                    <button id="enable-browser-btn" class="btn-setting btn-primary">
                        <i class="fas fa-bell"></i> Enable Notifications
                    </button>
                    <button id="test-notification-btn" class="btn-setting btn-secondary">
                        <i class="fas fa-paper-plane"></i> Send Test
                    </button>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="setting-item">
                <div class="setting-title">
                    <i class="fas fa-info-circle"></i>
                    System Status
                </div>
                <div class="setting-description">
                    Current status of your notification system components.
                </div>
                <div id="system-info">
                    <div style="font-family: monospace; font-size: 0.9rem; background: #f3f4f6; padding: 15px; border-radius: 8px; margin-top: 10px;">
                        <div>🔊 Audio System: <span id="audio-system-status">Checking...</span></div>
                        <div>📱 Service Worker: <span id="sw-status">Checking...</span></div>
                        <div>🔔 Notification Permission: <span id="notif-permission-status">Checking...</span></div>
                        <div>💾 Settings Saved: <span id="settings-saved-status">Checking...</span></div>
                    </div>
                </div>
            </div>
            
            <div class="back-link">
                <a href="dashboard.php">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
    const TECH_ID = <?php echo $t_id; ?>;
    const AUDIO_STORAGE_KEY = `techAudio_${TECH_ID}_enabled`;
    const AUDIO_SETUP_KEY = `techAudio_${TECH_ID}_setup_complete`;
    const NOTIFICATION_PERMISSION_KEY = `techNotif_${TECH_ID}_permission`;
    
    let testAudio = null;
    
    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alert-container');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        alertContainer.innerHTML = `<div class="alert ${alertClass}"><i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }
    
    function updateStatus() {
        // Check audio status
        const audioEnabled = localStorage.getItem(AUDIO_STORAGE_KEY) === 'true';
        const setupComplete = localStorage.getItem(AUDIO_SETUP_KEY);
        
        const soundStatus = document.getElementById('sound-status');
        if (audioEnabled) {
            soundStatus.className = 'setting-status status-enabled';
            soundStatus.innerHTML = '<i class="fas fa-check-circle"></i> Enabled';
        } else if (setupComplete === 'skipped') {
            soundStatus.className = 'setting-status status-disabled';
            soundStatus.innerHTML = '<i class="fas fa-times-circle"></i> Disabled (Skipped)';
        } else {
            soundStatus.className = 'setting-status status-disabled';
            soundStatus.innerHTML = '<i class="fas fa-times-circle"></i> Not Enabled';
        }
        
        // Check browser notification status
        const browserStatus = document.getElementById('browser-status');
        if (Notification.permission === 'granted') {
            browserStatus.className = 'setting-status status-enabled';
            browserStatus.innerHTML = '<i class="fas fa-check-circle"></i> Enabled';
        } else if (Notification.permission === 'denied') {
            browserStatus.className = 'setting-status status-disabled';
            browserStatus.innerHTML = '<i class="fas fa-times-circle"></i> Blocked';
        } else {
            browserStatus.className = 'setting-status status-unknown';
            browserStatus.innerHTML = '<i class="fas fa-question-circle"></i> Not Requested';
        }
        
        // Update system info
        document.getElementById('audio-system-status').textContent = audioEnabled ? 'Ready' : 'Not Enabled';
        document.getElementById('notif-permission-status').textContent = Notification.permission;
        document.getElementById('settings-saved-status').textContent = setupComplete ? 'Yes' : 'No';
        
        // Check service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration().then(registration => {
                document.getElementById('sw-status').textContent = registration ? 'Active' : 'Not Registered';
            });
        } else {
            document.getElementById('sw-status').textContent = 'Not Supported';
        }
    }
    
    function initAudio() {
        if (!testAudio) {
            testAudio = new Audio('../admin/vendor/sounds/arived.mp3');
            testAudio.volume = 1.0;
            testAudio.preload = 'auto';
        }
    }
    
    // Enable sound button
    document.getElementById('enable-sound-btn').onclick = function() {
        initAudio();
        
        testAudio.play().then(() => {
            testAudio.pause();
            testAudio.currentTime = 0;
            
            localStorage.setItem(AUDIO_STORAGE_KEY, 'true');
            localStorage.setItem(AUDIO_SETUP_KEY, 'true');
            
            showAlert('Sound alerts enabled successfully!');
            updateStatus();
        }).catch(error => {
            showAlert('Failed to enable sound: ' + error.message, 'error');
        });
    };
    
    // Test sound button
    document.getElementById('test-sound-btn').onclick = function() {
        initAudio();
        
        testAudio.currentTime = 0;
        testAudio.play().then(() => {
            showAlert('Test sound played successfully!');
        }).catch(error => {
            showAlert('Failed to play test sound: ' + error.message, 'error');
        });
    };
    
    // Reset sound button
    document.getElementById('reset-sound-btn').onclick = function() {
        if (confirm('Are you sure you want to reset all sound settings? You will need to enable them again.')) {
            localStorage.removeItem(AUDIO_STORAGE_KEY);
            localStorage.removeItem(AUDIO_SETUP_KEY);
            localStorage.removeItem(NOTIFICATION_PERMISSION_KEY);
            
            showAlert('Sound settings reset successfully!');
            updateStatus();
        }
    };
    
    // Enable browser notifications button
    document.getElementById('enable-browser-btn').onclick = function() {
        if (Notification.permission === 'granted') {
            showAlert('Browser notifications are already enabled!');
            return;
        }
        
        Notification.requestPermission().then(permission => {
            localStorage.setItem(NOTIFICATION_PERMISSION_KEY, permission);
            if (permission === 'granted') {
                showAlert('Browser notifications enabled successfully!');
            } else {
                showAlert('Browser notifications were denied. Please enable them in your browser settings.', 'error');
            }
            updateStatus();
        });
    };
    
    // Test notification button
    document.getElementById('test-notification-btn').onclick = function() {
        // Send test notification via server
        fetch('notification-settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=test_notification'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Test notification sent! Check your notifications in a few seconds.');
                
                // Also show browser notification if permission is granted
                if (Notification.permission === 'granted') {
                    setTimeout(() => {
                        new Notification('🔔 Test Notification', {
                            body: 'This is a test notification from Electrozot Technician Dashboard',
                            icon: '../admin/vendor/img/logo.png',
                            vibrate: [300, 100, 300]
                        });
                    }, 1000);
                }
            } else {
                showAlert('Failed to send test notification: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('Error sending test notification: ' + error.message, 'error');
        });
    };
    
    // Initialize status on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStatus();
        initAudio();
    });
    </script>
</body>
</html>