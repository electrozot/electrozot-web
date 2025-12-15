// Technician Service Worker - Background Notifications
// This enables notifications even when device is locked or browser is closed

const CACHE_NAME = 'electrozot-tech-v1';
const NOTIFICATION_CHECK_INTERVAL = 10000; // 10 seconds

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    event.waitUntil(self.clients.claim());
});

// Background sync for checking notifications
self.addEventListener('sync', (event) => {
    console.log('Service Worker: Sync event triggered');
    if (event.tag === 'check-bookings') {
        event.waitUntil(checkForNewBookings());
    }
});

// Periodic background sync (if supported)
self.addEventListener('periodicsync', (event) => {
    console.log('Service Worker: Periodic sync triggered');
    if (event.tag === 'check-bookings') {
        event.waitUntil(checkForNewBookings());
    }
});

// Push notification received
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push notification received');
    
    let data = {};
    if (event.data) {
        data = event.data.json();
    }
    
    const title = data.title || '🔔 New Booking Assignment!';
    const options = {
        body: data.body || 'You have a new booking',
        icon: '/vendor/img/icons/icon-192x192.png',
        badge: '/vendor/img/icons/badge-72x72.png',
        vibrate: [300, 100, 300, 100, 300],
        tag: data.tag || 'booking-notification',
        requireInteraction: true,
        data: data.data || {},
        actions: [
            {
                action: 'view',
                title: 'View Booking'
            },
            {
                action: 'dismiss',
                title: 'Dismiss'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Enhanced notification click handler for locked screen
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Locked-screen notification clicked, action:', event.action);
    event.notification.close();
    
    const notificationData = event.notification.data || {};
    
    if (event.action === 'view' || !event.action) {
        // Open or focus the dashboard
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then((clientList) => {
                    console.log('Service Worker: Found', clientList.length, 'open clients');
                    
                    // Check if dashboard is already open
                    for (let client of clientList) {
                        if (client.url.includes('/tech/dashboard.php') && 'focus' in client) {
                            console.log('Service Worker: Focusing existing dashboard');
                            return client.focus();
                        }
                    }
                    
                    // Open new dashboard window
                    if (clients.openWindow) {
                        console.log('Service Worker: Opening new dashboard window');
                        return clients.openWindow('/tech/dashboard.php');
                    }
                })
        );
    } else if (event.action === 'call') {
        // Handle call action
        const phoneNumber = notificationData.customer_phone;
        if (phoneNumber) {
            event.waitUntil(
                clients.openWindow(`tel:${phoneNumber}`)
            );
        }
    } else if (event.action === 'dismiss') {
        // Just close the notification (already handled above)
        console.log('Service Worker: Notification dismissed by user');
    }
});

// Message from unified notification system
self.addEventListener('message', (event) => {
    console.log('Service Worker: Message received from unified system', event.data);
    
    if (event.data && event.data.type === 'PAGE_VISIBILITY_CHANGE') {
        isBackgroundActive = event.data.hidden;
        console.log('Service Worker: Page visibility changed, background active:', isBackgroundActive);
        
        if (isBackgroundActive) {
            // Page is hidden, increase check frequency
            clearInterval(keepAliveInterval);
            keepAliveInterval = setInterval(() => {
                checkForNewBookings();
            }, 6000); // More frequent when in background
        } else {
            // Page is visible, normal frequency
            clearInterval(keepAliveInterval);
            keepAliveInterval = setInterval(() => {
                checkForNewBookings();
            }, 8000);
        }
    }
    
    if (event.data && event.data.type === 'NEW_BOOKING') {
        // Show notification for new booking
        const notifications = event.data.notifications || [];
        notifications.forEach(notif => {
            self.registration.showNotification('🔔 New Booking Assignment!', {
                body: `Booking #${notif.id}\n🔧 ${notif.service}\n👤 ${notif.customer}\n📞 ${notif.phone}`,
                icon: '/vendor/img/icons/icon-192x192.png',
                badge: '/vendor/img/icons/badge-72x72.png',
                vibrate: [500, 200, 500, 200, 500],
                tag: `booking-${notif.id}`,
                requireInteraction: true,
                silent: false,
                data: {
                    url: '/tech/dashboard.php',
                    booking_id: notif.id
                },
                actions: [
                    {
                        action: 'view',
                        title: 'View Booking'
                    },
                    {
                        action: 'dismiss',
                        title: 'Dismiss'
                    }
                ]
            });
        });
    }
});

// Enhanced mobile-optimized background notification checking
async function checkForNewBookings() {
    console.log('Service Worker: Checking for new bookings (mobile-optimized)...');
    
    try {
        const response = await fetch('/tech/check-technician-notifications.php', {
            credentials: 'include',
            cache: 'no-cache',
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
                'X-Background-Check': 'true',
                'X-Mobile-Request': 'true'
            }
        });
        
        if (!response.ok) {
            console.error('Service Worker: Failed to check notifications, status:', response.status);
            return;
        }
        
        const rawResponse = await response.text();
        const cleanResponse = rawResponse.trim().replace(/^\uFEFF/, '');
        const data = JSON.parse(cleanResponse);
        
        if (data.error) {
            console.error('Service Worker: Server error:', data.error);
            return;
        }
        
        if (data.has_notifications && data.notifications.length > 0) {
            console.log('Service Worker: New notifications found in background:', data.notification_count);
            
            // Show mobile-optimized locked-screen notifications
            for (const notif of data.notifications) {
                const notificationOptions = {
                    body: `📋 Booking #${notif.id}\n🔧 ${notif.service}\n👤 ${notif.customer}\n📞 ${notif.phone}\n\n⚡ Tap to open dashboard`,
                    icon: '/admin/vendor/img/logo.png',
                    badge: '/admin/vendor/img/logo.png',
                    vibrate: [1000, 500, 1000, 500, 1000, 500, 1000], // Strong vibration pattern for mobile
                    tag: `mobile-booking-${notif.id}`,
                    requireInteraction: true, // Keep notification until user interacts
                    silent: false, // Allow system notification sound
                    renotify: true, // Allow re-notification
                    timestamp: Date.now(),
                    image: '/admin/vendor/img/logo.png',
                    data: {
                        url: '/tech/dashboard.php',
                        booking_id: notif.id,
                        customer_phone: notif.phone,
                        customer_name: notif.customer,
                        service_name: notif.service,
                        type: 'mobile-locked-screen-notification',
                        priority: notif.is_high_priority ? 'high' : 'normal'
                    },
                    actions: [
                        {
                            action: 'view',
                            title: '📱 Open App',
                            icon: '/admin/vendor/img/logo.png'
                        },
                        {
                            action: 'call',
                            title: `📞 Call ${notif.customer}`,
                            icon: '/admin/vendor/img/logo.png'
                        },
                        {
                            action: 'dismiss',
                            title: '❌ Dismiss',
                            icon: '/admin/vendor/img/logo.png'
                        }
                    ]
                };
                
                // Add priority styling for high priority bookings
                if (notif.is_high_priority) {
                    notificationOptions.body = `🔥 HIGH PRIORITY 🔥\n` + notificationOptions.body;
                    notificationOptions.tag = `priority-mobile-booking-${notif.id}`;
                }
                
                await self.registration.showNotification('🔔 New Booking Assignment!', notificationOptions);
                
                console.log(`Service Worker: Mobile notification shown for booking ${notif.id}`);
            }
            
            // Notify all open clients about new bookings with mobile context
            const clients = await self.clients.matchAll({ 
                includeUncontrolled: true,
                type: 'window'
            });
            
            clients.forEach(client => {
                client.postMessage({
                    type: 'NEW_BOOKING_FROM_BACKGROUND',
                    notifications: data.notifications,
                    timestamp: Date.now(),
                    source: 'mobile-service-worker',
                    count: data.notifications.length
                });
            });
            
            console.log(`Service Worker: Notified ${clients.length} open clients about mobile notifications`);
        } else {
            console.log('Service Worker: No new notifications in mobile background check');
        }
    } catch (error) {
        console.error('Service Worker: Error in mobile background check:', error);
    }
}

// Unified service worker for mobile notifications
let keepAliveInterval;
let isBackgroundActive = false;

self.addEventListener('activate', (event) => {
    console.log('Service Worker: Starting unified notification system');
    
    // Clear any existing interval
    if (keepAliveInterval) {
        clearInterval(keepAliveInterval);
    }
    
    // Optimized check interval
    const checkInterval = 8000; // 8 seconds
    
    // Start periodic checking
    keepAliveInterval = setInterval(() => {
        checkForNewBookings();
    }, checkInterval);
    
    // Immediate check
    checkForNewBookings();
});

console.log('Service Worker: Loaded and ready for background notifications');
