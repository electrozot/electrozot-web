<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sound Notification</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .test-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .test-btn {
            width: 100%;
            padding: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .test-btn:hover {
            transform: scale(1.02);
        }
        
        .btn-sound {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .btn-notification {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .btn-check {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .status-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 0.9rem;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .status-box .log-entry {
            margin-bottom: 8px;
            padding: 5px;
            border-left: 3px solid #667eea;
            padding-left: 10px;
        }
        
        .status-box .log-success {
            border-left-color: #28a745;
            color: #28a745;
        }
        
        .status-box .log-error {
            border-left-color: #dc3545;
            color: #dc3545;
        }
        
        .status-box .log-info {
            border-left-color: #17a2b8;
            color: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1><i class="fas fa-volume-up"></i> Sound Notification Test</h1>
        
        <button class="test-btn btn-sound" onclick="testSound()">
            <i class="fas fa-play"></i> Test Sound Only
        </button>
        
        <button class="test-btn btn-notification" onclick="testNotification()">
            <i class="fas fa-bell"></i> Test Visual Notification
        </button>
        
        <button class="test-btn btn-check" onclick="testCheckAPI()">
            <i class="fas fa-sync"></i> Test Check API
        </button>
        
        <button class="test-btn btn-back" onclick="window.location.href='dashboard.php'">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </button>
        
        <div class="status-box" id="statusBox">
            <div class="log-entry log-info">Ready to test...</div>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script>
        const audio = new Audio('../admin/vendor/sounds/arived.mp3');
        audio.volume = 0.8;
        
        function log(message, type = 'info') {
            const statusBox = document.getElementById('statusBox');
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.textContent = new Date().toLocaleTimeString() + ' - ' + message;
            statusBox.appendChild(entry);
            statusBox.scrollTop = statusBox.scrollHeight;
        }
        
        function testSound() {
            log('Testing sound...', 'info');
            audio.currentTime = 0;
            audio.play()
                .then(() => {
                    log('✅ Sound played successfully!', 'success');
                })
                .catch((error) => {
                    log('❌ Error playing sound: ' + error.message, 'error');
                    log('💡 Try clicking on the page first', 'info');
                });
        }
        
        function testNotification() {
            log('Testing visual notification...', 'info');
            
            const notif = document.createElement('div');
            notif.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                z-index: 99999;
                min-width: 300px;
                animation: slideIn 0.5s ease-out;
            `;
            notif.innerHTML = `
                <h4 style="margin: 0 0 10px 0;">
                    <i class="fas fa-bell"></i> Test Notification
                </h4>
                <p style="margin: 0;">This is a test notification!</p>
            `;
            document.body.appendChild(notif);
            
            log('✅ Visual notification displayed', 'success');
            
            setTimeout(() => {
                notif.remove();
                log('Notification removed', 'info');
            }, 5000);
        }
        
        function testCheckAPI() {
            log('Testing check-technician-notifications.php API...', 'info');
            
            $.ajax({
                url: 'check-technician-notifications.php',
                method: 'GET',
                dataType: 'text',
                cache: false,
                success: function(rawResponse) {
                    log('✅ API response received', 'success');
                    log('Raw response: ' + rawResponse.substring(0, 100), 'info');
                    
                    try {
                        const response = JSON.parse(rawResponse.trim());
                        log('✅ JSON parsed successfully', 'success');
                        log('Notification count: ' + response.notification_count, 'info');
                        log('Has notifications: ' + response.has_notifications, 'info');
                        
                        if (response.notifications && response.notifications.length > 0) {
                            log('Notifications: ' + JSON.stringify(response.notifications), 'info');
                        }
                    } catch(e) {
                        log('❌ JSON parse error: ' + e.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    log('❌ AJAX error: ' + error, 'error');
                    log('Status: ' + status, 'error');
                }
            });
        }
        
        // Enable audio on page load
        document.addEventListener('click', function() {
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                log('✅ Audio context enabled', 'success');
            }).catch(() => {});
        }, { once: true });
        
        log('Page loaded. Click anywhere to enable audio.', 'info');
    </script>
</body>
</html>
