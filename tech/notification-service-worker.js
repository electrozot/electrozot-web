// Notification Service Worker for Technician Dashboard
// Handles background notifications when app is closed or phone is locked

const CACHE_NAME = 'electrozot-tech-notifications-v1';
const NOTIFICATION_ENDPOINT = '/tech/check-technician-notifications.php';

// Install service worker
self.addEventListener('install', event => {
    console.log('📱 [SW] Service Worker installing...');
    self.skipWaiting();
});

// Activate service worker
self.addEventListener('activate', event => {
    console.log('📱 [SW] Service Worker activated');
    event.waitUntil(clients.claim());
});

// Handle background sync for notifications
self.addEventListener('sync', event => {
    if (event.tag === 'background-notification-check') {
        console.log('🔄 [SW] Background sync triggered');
        event.waitUntil(checkNotificationsInBackground());
    }
});

// Handle push notifications
self.addEventListener('push', event => {
    console.log('📨 [SW] Push notification received');
    
    let notificationData = {
        title: '🔔 New Booking Assignment!',
        body: 'You have a new booking assignment',
        icon: '/admin/vendor/img/logo.png',
        badge: '/admin/vendor/img/logo.png',
        vibrate: [300, 100, 300, 100, 300],
        requireInteraction: true,
        actions: [
            {
                action: 'view',
                title: 'View Dashboard',
                icon: '/admin/vendor/img/icons/view-icon.png'
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
                icon: '/admin/vendor/img/icons/dismiss-icon.png'
            }
        ],
        data: {
            url: '/tech/dashboard.php'
        }
    };
    
    if (event.data) {
        try {
            const pushData = event.data.json();
            notificationData = { ...notificationData, ...pushData };
        } catch (e) {
            console.error('📨 [SW] Error parsing push data:', e);
        }
    }
    
    event.waitUntil(
        self.registration.showNotification(notificationData.title, notificationData)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    console.log('👆 [SW] Notification clicked:', event.action);
    
    event.notification.close();
    
    if (event.action === 'view' || !event.action) {
        // Open or focus the dashboard
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then(clientList => {
                    // Check if dashboard is already open
                    for (let client of clientList) {
                        if (client.url.includes('/tech/dashboard.php') && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    
                    // Open new dashboard window
                    if (clients.openWindow) {
                        return clients.openWindow('/tech/dashboard.php');
                    }
                })
        );
    }
    // 'dismiss' action just closes the notification (already handled above)
});

// Background notification checking
async function checkNotificationsInBackground() {
    try {
        console.log('🔍 [SW] Checking notifications in background...');
        
        const response = await fetch(NOTIFICATION_ENDPOINT, {
            method: 'GET',
            cache: 'no-cache',
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.has_notifications && data.new_count > 0) {
            console.log('🎉 [SW] New notifications found in background:', data.new_count);
            
            // Show notification for each new booking
            for (let notification of data.notifications) {
                await self.registration.showNotification('🔔 New Booking Assignment!', {
                    body: `Booking #${notification.id}\n🔧 ${notification.service}\n👤 ${notification.customer}\n📞 ${notification.phone}`,
                    icon: '/admin/vendor/img/logo.png',
                    badge: '/admin/vendor/img/logo.png',
                    vibrate: [300, 100, 300, 100, 300],
                    tag: `booking-${notification.id}`,
                    requireInteraction: true,
                    silent: false,
                    actions: [
                        {
                            action: 'view',
                            title: 'View Details'
                        },
                        {
                            action: 'dismiss',
                            title: 'Dismiss'
                        }
                    ],
                    data: {
                        url: '/tech/dashboard.php',
                        bookingId: notification.id
                    }
                });
            }
        } else {
            console.log('✓ [SW] No new notifications in background');
        }
        
    } catch (error) {
        console.error('❌ [SW] Background notification check failed:', error);
    }
}

// Periodic background check (when supported)
self.addEventListener('periodicsync', event => {
    if (event.tag === 'notification-check') {
        console.log('⏰ [SW] Periodic sync triggered');
        event.waitUntil(checkNotificationsInBackground());
    }
});

// Handle messages from main thread
self.addEventListener('message', event => {
    console.log('💬 [SW] Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CHECK_NOTIFICATIONS') {
        event.waitUntil(checkNotificationsInBackground());
    }
});

console.log('📱 [SW] Notification Service Worker loaded and ready');