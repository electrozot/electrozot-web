// Service Worker for Push Notifications
const CACHE_NAME = 'electrozot-tech-v1';

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activated');
    event.waitUntil(clients.claim());
});

// Push notification event
self.addEventListener('push', (event) => {
    console.log('Push notification received:', event);
    
    let notificationData = {
        title: 'New Booking Assignment',
        body: 'You have been assigned a new booking',
        icon: '/vendor/img/icons/icon-192x192.png',
        badge: '/vendor/img/icons/badge-72x72.png',
        vibrate: [200, 100, 200, 100, 200],
        tag: 'booking-notification',
        requireInteraction: true,
        data: {
            url: '/tech/dashboard.php'
        }
    };
    
    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = {
                title: data.title || notificationData.title,
                body: data.body || notificationData.body,
                icon: data.icon || notificationData.icon,
                badge: data.badge || notificationData.badge,
                vibrate: data.vibrate || notificationData.vibrate,
                tag: data.tag || notificationData.tag,
                requireInteraction: true,
                data: {
                    url: data.url || notificationData.data.url,
                    booking_id: data.booking_id
                },
                actions: [
                    {
                        action: 'view',
                        title: 'View Booking',
                        icon: '/vendor/img/icons/view-icon.png'
                    },
                    {
                        action: 'dismiss',
                        title: 'Dismiss',
                        icon: '/vendor/img/icons/close-icon.png'
                    }
                ]
            };
        } catch (e) {
            console.error('Error parsing push data:', e);
        }
    }
    
    event.waitUntil(
        self.registration.showNotification(notificationData.title, notificationData)
    );
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    if (event.action === 'view' || !event.action) {
        const urlToOpen = event.notification.data.url || '/tech/dashboard.php';
        
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then((clientList) => {
                    // Check if there's already a window open
                    for (let client of clientList) {
                        if (client.url.includes('/tech/') && 'focus' in client) {
                            return client.focus().then(() => {
                                return client.navigate(urlToOpen);
                            });
                        }
                    }
                    // If no window is open, open a new one
                    if (clients.openWindow) {
                        return clients.openWindow(urlToOpen);
                    }
                })
        );
    }
});

// Background sync for offline notifications
self.addEventListener('sync', (event) => {
    if (event.tag === 'check-notifications') {
        event.waitUntil(checkForNewNotifications());
    }
});

async function checkForNewNotifications() {
    try {
        const response = await fetch('/tech/check-technician-notifications.php', {
            credentials: 'include'
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.has_notifications && data.notifications.length > 0) {
                // Show notification for each new booking
                for (const notification of data.notifications) {
                    await self.registration.showNotification('New Booking Assignment', {
                        body: `Booking #${notification.id} - ${notification.service}\nCustomer: ${notification.customer}`,
                        icon: '/vendor/img/icons/icon-192x192.png',
                        badge: '/vendor/img/icons/badge-72x72.png',
                        vibrate: [200, 100, 200],
                        tag: `booking-${notification.id}`,
                        requireInteraction: true,
                        data: {
                            url: '/tech/dashboard.php',
                            booking_id: notification.id
                        }
                    });
                }
            }
        }
    } catch (error) {
        console.error('Error checking notifications:', error);
    }
}

// Periodic background sync (if supported)
self.addEventListener('periodicsync', (event) => {
    if (event.tag === 'check-bookings') {
        event.waitUntil(checkForNewNotifications());
    }
});

console.log('Service Worker: Loaded and ready');
