<?php
/**
 * Push Notification Setup Checker
 * Verifies all requirements for push notifications
 */

session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Push Notification Setup - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 15px;
        }
        .setup-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .check-item {
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            border-left: 5px solid #ccc;
        }
        .check-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .check-item.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .check-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .check-item h5 {
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .check-item p {
            margin: 0;
            font-size: 14px;
        }
        .btn-test {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            margin: 10px 5px;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h2 style="text-align: center; color: #667eea; margin-bottom: 30px;">
            <i class="fas fa-bell"></i> Push Notification Setup
        </h2>
        
        <div id="checkResults">
            <p style="text-align: center; color: #666;">
                <i class="fas fa-spinner fa-spin"></i> Checking system requirements...
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <button onclick="requestPermission()" class="btn-test">
                <i class="fas fa-bell"></i> Enable Notifications
            </button>
            <button onclick="testNotification()" class="btn-test">
                <i class="fas fa-vial"></i> Test Notification
            </button>
            <a href="dashboard.php" class="btn-test" style="text-decoration: none; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Run checks
        window.addEventListener('load', () => {
            runChecks();
        });

        function runChecks() {
            const results = [];
            
            // Check 1: HTTPS
            const isHTTPS = window.location.protocol === 'https:';
            results.push({
                title: 'HTTPS Connection',
                status: isHTTPS ? 'success' : 'error',
                message: isHTTPS ? 
                    '✅ Site is using HTTPS (required for push notifications)' : 
                    '❌ Site must use HTTPS for push notifications to work',
                icon: 'lock'
            });
            
            // Check 2: Service Worker Support
            const hasServiceWorker = 'serviceWorker' in navigator;
            results.push({
                title: 'Service Worker Support',
                status: hasServiceWorker ? 'success' : 'error',
                message: hasServiceWorker ? 
                    '✅ Browser supports Service Workers' : 
                    '❌ Browser does not support Service Workers',
                icon: 'cog'
            });
            
            // Check 3: Push API Support
            const hasPushAPI = 'PushManager' in window;
            results.push({
                title: 'Push API Support',
                status: hasPushAPI ? 'success' : 'warning',
                message: hasPushAPI ? 
                    '✅ Browser supports Push API' : 
                    '⚠️ Browser has limited push notification support',
                icon: 'bell'
            });
            
            // Check 4: Notification Permission
            const notifPermission = Notification.permission;
            let permStatus = 'warning';
            let permMessage = '⚠️ Notification permission not granted yet';
            
            if (notifPermission === 'granted') {
                permStatus = 'success';
                permMessage = '✅ Notification permission granted';
            } else if (notifPermission === 'denied') {
                permStatus = 'error';
                permMessage = '❌ Notification permission denied - Please enable in browser settings';
            }
            
            results.push({
                title: 'Notification Permission',
                status: permStatus,
                message: permMessage,
                icon: 'user-check'
            });
            
            // Check 5: Service Worker Registration
            if (hasServiceWorker) {
                navigator.serviceWorker.getRegistrations().then(registrations => {
                    const isRegistered = registrations.length > 0;
                    results.push({
                        title: 'Service Worker Registration',
                        status: isRegistered ? 'success' : 'warning',
                        message: isRegistered ? 
                            `✅ Service Worker registered (${registrations.length} active)` : 
                            '⚠️ Service Worker not registered yet - Will register automatically',
                        icon: 'server'
                    });
                    displayResults(results);
                });
            } else {
                displayResults(results);
            }
        }

        function displayResults(results) {
            const container = document.getElementById('checkResults');
            let html = '';
            
            results.forEach(result => {
                html += `
                    <div class="check-item ${result.status}">
                        <h5><i class="fas fa-${result.icon}"></i> ${result.title}</h5>
                        <p>${result.message}</p>
                    </div>
                `;
            });
            
            // Overall status
            const allSuccess = results.every(r => r.status === 'success');
            const hasErrors = results.some(r => r.status === 'error');
            
            if (allSuccess) {
                html += `
                    <div class="check-item success" style="text-align: center; margin-top: 30px;">
                        <h4><i class="fas fa-check-circle"></i> All Systems Ready!</h4>
                        <p>Push notifications are fully configured and working.</p>
                    </div>
                `;
            } else if (hasErrors) {
                html += `
                    <div class="check-item error" style="text-align: center; margin-top: 30px;">
                        <h4><i class="fas fa-exclamation-triangle"></i> Action Required</h4>
                        <p>Please fix the errors above to enable push notifications.</p>
                    </div>
                `;
            } else {
                html += `
                    <div class="check-item warning" style="text-align: center; margin-top: 30px;">
                        <h4><i class="fas fa-info-circle"></i> Almost Ready</h4>
                        <p>Click "Enable Notifications" button below to complete setup.</p>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }

        function requestPermission() {
            if (!('Notification' in window)) {
                alert('This browser does not support notifications');
                return;
            }
            
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    alert('✅ Notifications enabled successfully!');
                    
                    // Register service worker
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.register('/tech/service-worker.js')
                            .then(registration => {
                                console.log('Service Worker registered:', registration);
                            })
                            .catch(error => {
                                console.error('Service Worker registration failed:', error);
                            });
                    }
                    
                    // Rerun checks
                    setTimeout(runChecks, 1000);
                } else if (permission === 'denied') {
                    alert('❌ Notification permission denied. Please enable it in your browser settings.');
                } else {
                    alert('⚠️ Notification permission not granted.');
                }
            });
        }

        function testNotification() {
            if (Notification.permission !== 'granted') {
                alert('Please enable notifications first');
                return;
            }
            
            // Test with service worker if available
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.ready.then(registration => {
                    registration.showNotification('Test Notification', {
                        body: 'This is a test notification from Electrozot!\nBooking #123 - AC Repair\nCustomer: Test Customer',
                        icon: '/vendor/img/icons/icon-192x192.png',
                        badge: '/vendor/img/icons/badge-72x72.png',
                        vibrate: [200, 100, 200],
                        tag: 'test-notification',
                        requireInteraction: true,
                        data: {
                            url: '/tech/dashboard.php'
                        }
                    });
                });
            } else {
                // Fallback to regular notification
                new Notification('Test Notification', {
                    body: 'This is a test notification from Electrozot!',
                    icon: '/vendor/img/icons/icon-192x192.png'
                });
            }
        }
    </script>
</body>
</html>
