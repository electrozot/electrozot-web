/**
 * Real-time Notification System for Admin
 * Features: Sound alerts, Push notifications, Background monitoring
 */

class AdminNotificationSystem {
    constructor() {
        this.lastCheckTime = Date.now();
        this.checkInterval = 5000; // Check every 5 seconds
        this.notificationSound = null;
        this.isInitialized = false;
        this.lastBookingId = 0;
        this.init();
    }

    async init() {
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            await Notification.requestPermission();
        }

        // Load notification sound
        this.notificationSound = new Audio('sounds/notification.mp3');
        this.notificationSound.volume = 0.7;

        // Register service worker for background notifications
        if ('serviceWorker' in navigator) {
            try {
                await navigator.serviceWorker.register('service-worker.js');
                console.log('Service Worker registered');
            } catch (error) {
                console.log('Service Worker registration failed:', error);
            }
        }

        this.isInitialized = true;
        this.startMonitoring();
    }

    startMonitoring() {
        // Check immediately
        this.checkForUpdates();
        
        // Then check periodically
        setInterval(() => this.checkForUpdates(), this.checkInterval);
    }

    async checkForUpdates() {
        try {
            const response = await fetch('api-realtime-notifications.php');
            const data = await response.json();
            
            if (data.success) {
                this.handleNewBookings(data.new_bookings);
                this.handleStatusChanges(data.status_changes);
                this.updateBadge(data.total_unread);
            }
        } catch (error) {
            console.error('Error checking for updates:', error);
        }
    }

    handleNewBookings(bookings) {
        if (!bookings || bookings.length === 0) return;

        bookings.forEach(booking => {
            if (booking.sb_id > this.lastBookingId) {
                this.lastBookingId = booking.sb_id;
                this.showNotification('New Booking', booking);
                this.playSound();
            }
        });
    }

    handleStatusChanges(changes) {
        if (!changes || changes.length === 0) return;

        changes.forEach(change => {
            this.showNotification(change.type, change);
            this.playSound();
        });
    }

    showNotification(type, data) {
        let title = '';
        let body = '';
        let icon = 'vendor/img/logo.png';

        switch(type) {
            case 'New Booking':
                title = '🆕 New Booking Received!';
                body = `Booking #${data.sb_id} - ${data.service_name}\nCustomer: ${data.customer_name}\nPhone: ${data.sb_phone}`;
                break;
            case 'Rejected':
                title = '❌ Booking Rejected';
                body = `Booking #${data.sb_id} rejected by technician\nReason: ${data.reason}`;
                break;
            case 'Completed':
                title = '✅ Booking Completed';
                body = `Booking #${data.sb_id} completed successfully\nTechnician: ${data.technician_name}`;
                break;
            default:
                title = '📋 Booking Update';
                body = `Booking #${data.sb_id} - ${data.status}`;
        }

        // Show browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(title, {
                body: body,
                icon: icon,
                badge: icon,
                tag: `booking-${data.sb_id}`,
                requireInteraction: true,
                vibrate: [200, 100, 200]
            });

            notification.onclick = () => {
                window.focus();
                window.location.href = `admin-view-service-booking.php?sb_id=${data.sb_id}`;
                notification.close();
            };
        }

        // Show in-page notification
        this.showInPageNotification(title, body);
    }

    showInPageNotification(title, body) {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show notification-popup';
        notification.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out;';
        notification.innerHTML = `
            <strong>${title}</strong><br>
            ${body.replace(/\n/g, '<br>')}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        `;

        container.appendChild(notification);

        // Auto remove after 10 seconds
        setTimeout(() => {
            notification.remove();
        }, 10000);
    }

    playSound() {
        if (this.notificationSound) {
            this.notificationSound.play().catch(e => console.log('Sound play failed:', e));
        }
    }

    updateBadge(count) {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new AdminNotificationSystem();
});
