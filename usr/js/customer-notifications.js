/**
 * Customer Real-time Notification System
 * Checks for booking updates and shows notifications with sound
 */

let lastCheckTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
let checkInterval = null;

function checkBookingUpdates() {
    fetch('api-check-booking-updates.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'last_check=' + encodeURIComponent(lastCheckTime)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.count > 0) {
            // Show notifications
            data.notifications.forEach((notif, index) => {
                setTimeout(() => {
                    showNotification(notif);
                }, index * 3000); // Show each notification 3 seconds apart
            });
            
            // Update last check time
            lastCheckTime = data.timestamp;
        }
    })
    .catch(error => {
        console.error('Error checking updates:', error);
    });
}

function showNotification(notif) {
    const toast = document.getElementById('notificationToast');
    const title = document.getElementById('notifTitle');
    const message = document.getElementById('notifMessage');
    const sound = document.getElementById('notificationSound');
    
    // Set content
    title.textContent = notif.title;
    message.textContent = notif.message;
    
    // Set type class
    toast.className = 'notification-toast show ' + notif.type;
    
    // DISABLED: Sound alerts turned off
    // if (sound) {
    //     sound.play().catch(e => console.log('Sound play failed:', e));
    // }
    
    // Hide after 5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 5000);
}

// DISABLED: Notification system turned off
// Start checking every 10 seconds
document.addEventListener('DOMContentLoaded', function() {
    // DISABLED: No automatic notifications
    // setTimeout(checkBookingUpdates, 2000);
    // checkInterval = setInterval(checkBookingUpdates, 10000);
});

// Stop checking when page is hidden
document.addEventListener('visibilitychange', function() {
    // DISABLED
    // if (document.hidden) {
    //     if (checkInterval) {
    //         clearInterval(checkInterval);
    //     }
    // } else {
    //     checkInterval = setInterval(checkBookingUpdates, 10000);
    // }
});
