/**
 * Service Worker for Background Notifications
 * Enables notifications even when browser tab is not active
 */

const CACHE_NAME = 'admin-notifications-v1';

// Install service worker
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    self.skipWaiting();
});

// Activate service worker
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');
    event.waitUntil(clients.claim());
});

// Handle push notifications
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    
    const title = data.title || 'New Booking Notification';
    const options = {
        body: data.body || 'You have a new booking update',
        icon: 'vendor/img/logo.png',
        badge: 'vendor/img/logo.png',
        vibrate: [200, 100, 200],
        tag: data.tag || 'booking-notification',
        requireInteraction: true,
        data: {
            url: data.url || 'admin-dashboard.php'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    const urlToOpen = event.notification.data.url || 'admin-dashboard.php';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (let client of clientList) {
                    if (client.url.includes('admin') && 'focus' in client) {
                        client.focus();
                        client.navigate(urlToOpen);
                        return;
                    }
                }
                // Open new window if none exists
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Background sync for checking notifications
self.addEventListener('sync', (event) => {
    if (event.tag === 'check-notifications') {
        event.waitUntil(checkForNewNotifications());
    }
});

async function checkForNewNotifications() {
    try {
        const response = await fetch('api-realtime-notifications.php');
        const data = await response.json();
        
        if (data.success && (data.new_bookings.length > 0 || data.status_changes.length > 0)) {
            // Show notification
            self.registration.showNotification('New Updates', {
                body: `You have ${data.new_bookings.length} new bookings and ${data.status_changes.length} status changes`,
                icon: 'vendor/img/logo.png',
                badge: 'vendor/img/logo.png',
                tag: 'batch-notification'
            });
        }
    } catch (error) {
        console.error('Error checking notifications:', error);
    }
}
