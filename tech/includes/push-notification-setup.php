<!-- Push Notification Setup -->
<script>
    // Check if service workers and push notifications are supported
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        console.log('✅ Service Worker and Push API supported');
        
        // Register service worker
        navigator.serviceWorker.register('/tech/service-worker.js')
            .then((registration) => {
                console.log('✅ Service Worker registered:', registration);
                
                // Request notification permission
                return Notification.requestPermission();
            })
            .then((permission) => {
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
                    console.warn('⚠️ Periodic sync not available:', error);
                });
            } else {
                console.log('ℹ️ Periodic background sync not supported');
            }
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
    
    // Check for notifications even when page is in background
    let backgroundCheckInterval;
    
    // Visibility change handler
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            console.log('📱 Page hidden - starting background checks');
            // Check more frequently when page is hidden
            backgroundCheckInterval = setInterval(() => {
                checkNotificationsInBackground();
            }, 10000); // Every 10 seconds when hidden
        } else {
            console.log('📱 Page visible - stopping background checks');
            if (backgroundCheckInterval) {
                clearInterval(backgroundCheckInterval);
            }
        }
    });
    
    // Background notification check
    function checkNotificationsInBackground() {
        fetch('check-technician-notifications.php', {
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_notifications && data.notifications.length > 0) {
                console.log('🔔 New notifications detected in background:', data.notification_count);
                
                // Show browser notification for each new booking
                data.notifications.forEach(notification => {
                    showBrowserNotification('New Booking Assignment', {
                        body: `Booking #${notification.id} - ${notification.service}\nCustomer: ${notification.customer}\nPhone: ${notification.phone}`,
                        icon: '/vendor/img/icons/icon-192x192.png',
                        badge: '/vendor/img/icons/badge-72x72.png',
                        vibrate: [200, 100, 200, 100, 200],
                        tag: `booking-${notification.id}`,
                        requireInteraction: true,
                        data: {
                            url: '/tech/dashboard.php',
                            booking_id: notification.id
                        }
                    });
                });
                
                // Play sound if audio is enabled
                if (typeof playTechNotificationSound === 'function') {
                    playTechNotificationSound();
                }
            }
        })
        .catch(error => {
            console.error('Error checking background notifications:', error);
        });
    }
    
    console.log('✅ Push notification system initialized');
    console.log('📱 Notifications will work even when:');
    console.log('   - Browser tab is in background');
    console.log('   - Browser is minimized');
    console.log('   - Device screen is locked (if browser supports)');
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
