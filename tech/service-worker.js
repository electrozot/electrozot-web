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

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Notification clicked');
    event.notification.close();
    
    if (event.action === 'view' || !event.action) {
        // Open or focus the dashboard
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then((clientList) => {
                    // Check if dashboard is already open
                    for (let client of clientList) {
                        if (client.url.includes('/tech/dashboard.php') && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    // Open new window if not found
                    if (clients.openWindow) {
                        return clients.openWindow('/tech/dashboard.php');
                    }
                })
        );
    }
});

// Message from client
self.addEventListener('message', (event) => {
    console.log('Service Worker: Message received from client', event.data);
    
    if (event.data.type === 'NEW_BOOKING') {
        // Show notification for new booking
        const notifications = event.data.notifications || [];
        notifications.forEach(notif => {
            self.registration.showNotification('🔔 New Booking Assignment!', {
                body: `Booking #${notif.id}\n🔧 ${notif.service}\n👤 ${notif.customer}\n📞 ${notif.phone}`,
                icon: '/vendor/img/icons/icon-192x192.png',
                badge: '/vendor/img/icons/badge-72x72.png',
                vibrate: [300, 100, 300, 100, 300],
                tag: `booking-${notif.id}`,
                requireInteraction: true,
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

// Check for new bookings in background
async function checkForNewBookings() {
    console.log('Service Worker: Checking for new bookings...');
    
    try {
        const response = await fetch('/tech/check-technician-notifications.php', {
            credentials: 'include',
            cache: 'no-cache',
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        });
        
        if (!response.ok) {
            console.error('Service Worker: Failed to check notifications');
            return;
        }
        
        const data = await response.json();
        
        if (data.has_notifications && data.notifications.length > 0) {
            console.log('Service Worker: New notifications found:', data.notification_count);
            
            // Show notification for each new booking
            for (const notif of data.notifications) {
                await self.registration.showNotification('🔔 New Booking Assignment!', {
                    body: `Booking #${notif.id}\n🔧 ${notif.service}\n👤 ${notif.customer}\n📞 ${notif.phone}`,
                    icon: '/vendor/img/icons/icon-192x192.png',
                    badge: '/vendor/img/icons/badge-72x72.png',
                    vibrate: [300, 100, 300, 100, 300],
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
            }
            
            // Notify all open clients
            const clients = await self.clients.matchAll({ includeUncontrolled: true });
            clients.forEach(client => {
                client.postMessage({
                    type: 'NEW_BOOKING',
                    notifications: data.notifications
                });
            });
        } else {
            console.log('Service Worker: No new notifications');
        }
    } catch (error) {
        console.error('Service Worker: Error checking bookings:', error);
    }
}

// Keep service worker alive with periodic checks
// This helps ensure notifications work even when device is locked
let keepAliveInterval;

self.addEventListener('activate', (event) => {
    console.log('Service Worker: Starting keepalive checks');
    
    // Clear any existing interval
    if (keepAliveInterval) {
        clearInterval(keepAliveInterval);
    }
    
    // Check for notifications every 10 seconds
    keepAliveInterval = setInterval(() => {
        checkForNewBookings();
    }, NOTIFICATION_CHECK_INTERVAL);
    
    // Do an immediate check
    checkForNewBookings();
});

console.log('Service Worker: Loaded and ready for background notifications');
