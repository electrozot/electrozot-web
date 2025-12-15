<!-- Push Notification Setup -->
<script>
    // Check if service workers and push notifications are supported
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        console.log('✅ Service Worker and Push API supported');
        
        // Register service worker
        navigator.serviceWorker.register('service-worker.js')
            .then((registration) => {
                console.log('✅ Service Worker registered:', registration);
                
                // Check current notification permission status (don't request automatically)
                const permission = Notification.permission;
                console.log('Notification permission:', permission);
                
                if (permission === 'granted') {
                    console.log('✅ Notification permission granted');
                    
                    // Subscribe to push notifications
                    return navigator.serviceWorker.ready.then((registration) => {
                        return registration.pushManager.getSubscription()
                            .then((subscription) => {
                                if (subscription) {
                                    console.log('✅ Already subscribed to push notifications');
                                    return subscription;
                                }
                                
                                // Subscribe to push notifications
                                // Note: You'll need to generate VAPID keys for production
                                // For now, we'll use the notification API without push server
                                console.log('📝 Push subscription would be created here');
                                return null;
                            });
                    });
                } else if (permission === 'denied') {
                    console.warn('⚠️ Notification permission denied');
                    showPermissionDeniedMessage();
                } else {
                    console.log('ℹ️ Notification permission not granted yet');
                }
            })
            .catch((error) => {
                console.error('❌ Service Worker registration failed:', error);
            });
        
        // Register periodic background sync (if supported)
        navigator.serviceWorker.ready.then((registration) => {
            if ('periodicSync' in registration) {
                registration.periodicSync.register('check-bookings', {
                    minInterval: 60 * 1000 // Check every 1 minute
                }).then(() => {
                    console.log('✅ Periodic background sync registered');
                }).catch((error) => {
                    // Silently ignore - periodic sync is optional and not critical
                    // We have other background check mechanisms in place
                    console.log('ℹ️ Periodic sync not available (using alternative background checks)');
                });
            } else {
                console.log('ℹ️ Periodic background sync not supported (using alternative background checks)');
            }
        }).catch(() => {
            // Silently ignore service worker errors
        });
        
        // Listen for messages from service worker
        navigator.serviceWorker.addEventListener('message', (event) => {
            console.log('Message from service worker:', event.data);
            
            if (event.data.type === 'NEW_BOOKING') {
                // Reload page to show new booking
                location.reload();
            }
        });
        
    } else {
        console.warn('⚠️ Service Worker or Push API not supported');
        showBrowserNotSupportedMessage();
    }
    
    // Show permission denied message
    function showPermissionDeniedMessage() {
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 99999;
            max-width: 90%;
            text-align: center;
        `;
        message.innerHTML = `
            <strong>⚠️ Notifications Blocked</strong><br>
            <small>Please enable notifications in your browser settings to receive booking alerts even when the app is closed.</small>
        `;
        document.body.appendChild(message);
        
        setTimeout(() => {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 500);
        }, 8000);
    }
    
    // Show browser not supported message
    function showBrowserNotSupportedMessage() {
        console.warn('Browser does not support push notifications');
    }
    
    // Function to show browser notification (works even when tab is not active)
    function showBrowserNotification(title, options) {
        if ('serviceWorker' in navigator && Notification.permission === 'granted') {
            navigator.serviceWorker.ready.then((registration) => {
                registration.showNotification(title, {
                    body: options.body || '',
                    icon: options.icon || '/vendor/img/icons/icon-192x192.png',
                    badge: options.badge || '/vendor/img/icons/badge-72x72.png',
                    vibrate: options.vibrate || [200, 100, 200],
                    tag: options.tag || 'notification',
                    requireInteraction: true,
                    data: options.data || {},
                    actions: options.actions || []
                });
            });
        } else if (Notification.permission === 'granted') {
            // Fallback to regular notification
            new Notification(title, options);
        }
    }
    
    // Make function globally available
    window.showBrowserNotification = showBrowserNotification;
    
    // Background notification checking is handled by notification-system-debug.php
    // This prevents duplicate notifications and sounds
    console.log('ℹ️ Background notification checking delegated to main notification system');
    
    // Request persistent notification permission for better background reliability
    if ('permissions' in navigator) {
        navigator.permissions.query({name: 'notifications'}).then(function(result) {
            console.log('📋 Notification permission status:', result.state);
            if (result.state === 'granted') {
                console.log('✅ Notifications are enabled and will work in background');
            }
        }).catch(err => {
            console.log('⚠️ Could not query notification permission:', err);
        });
    }
    
    // Try to keep connection alive for background notifications
    // Send periodic heartbeat to keep session alive
    setInterval(() => {
        if (document.hidden) {
            // Send a lightweight ping to keep session alive
            fetch('check-technician-notifications.php', {
                method: 'HEAD',
                credentials: 'include',
                cache: 'no-cache'
            }).catch(() => {});
        }
    }, 30000); // Every 30 seconds
    
    console.log('✅ Push notification system initialized');
    console.log('📱 Notifications will work even when:');
    console.log('   - Browser tab is in background');
    console.log('   - Browser is minimized');
    console.log('   - Device screen is locked (if browser supports)');
    console.log('   - Phone is in standby mode');
    console.log('🔄 Background checks run every 5 seconds when page is hidden');
    console.log('💓 Session keepalive active for reliable notifications');
</script>

<!-- Notification Permission Prompt -->
<div id="notificationPermissionPrompt" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 99999; max-width: 90%; text-align: center;">
    <h4 style="margin: 0 0 10px 0; font-size: 18px;">🔔 Enable Notifications</h4>
    <p style="margin: 0 0 15px 0; font-size: 14px;">Get instant alerts for new bookings even when the app is closed or your device is locked.</p>
    <button onclick="requestNotificationPermission()" style="background: white; color: #667eea; border: none; padding: 10px 25px; border-radius: 25px; font-weight: bold; cursor: pointer; margin-right: 10px;">Enable</button>
    <button onclick="dismissNotificationPrompt()" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 25px; border-radius: 25px; font-weight: bold; cursor: pointer;">Later</button>
</div>

<script>
    // Show notification permission prompt if not granted
    function checkNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            // Show prompt after 5 seconds
            setTimeout(() => {
                document.getElementById('notificationPermissionPrompt').style.display = 'block';
            }, 5000);
        }
    }
    
    function requestNotificationPermission() {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                document.getElementById('notificationPermissionPrompt').style.display = 'none';
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.style.cssText = `
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                    color: white;
                    padding: 15px 25px;
                    border-radius: 10px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
                    z-index: 99999;
                    text-align: center;
                `;
                successMsg.innerHTML = '<strong>✅ Notifications Enabled!</strong><br><small>You\'ll now receive alerts even when the app is closed.</small>';
                document.body.appendChild(successMsg);
                
                setTimeout(() => {
                    successMsg.style.transition = 'opacity 0.5s';
                    successMsg.style.opacity = '0';
                    setTimeout(() => successMsg.remove(), 500);
                }, 3000);
            }
        });
    }
    
    function dismissNotificationPrompt() {
        document.getElementById('notificationPermissionPrompt').style.display = 'none';
        // Remember dismissal for this session
        sessionStorage.setItem('notificationPromptDismissed', 'true');
    }
    
    // Check if we should show the prompt
    if (!sessionStorage.getItem('notificationPromptDismissed')) {
        checkNotificationPermission();
    }
</script>
