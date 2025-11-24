<!-- Admin Real-Time Notification System -->
<style>
.notification-bell {
    position: relative;
    cursor: pointer;
    font-size: 20px;
    color: #fff;
    margin-right: 20px;
}
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: bold;
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
.notification-dropdown {
    position: absolute;
    top: 50px;
    right: 20px;
    width: 350px;
    max-height: 400px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    display: none;
    z-index: 9999;
    overflow: hidden;
}
.notification-dropdown.show {
    display: block;
    animation: slideDown 0.3s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.notification-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    font-weight: bold;
}
.notification-list {
    max-height: 300px;
    overflow-y: auto;
}
.notification-item {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.2s;
}
.notification-item:hover {
    background: #f8f9fa;
}
.notification-item.unread {
    background: #e3f2fd;
}
</style>

<div class="notification-bell" id="notificationBell" onclick="toggleNotifications()">
    <i class="fas fa-bell"></i>
    <span class="notification-badge" id="notificationCount" style="display:none;">0</span>
</div>

<div class="notification-dropdown" id="notificationDropdown">
    <div class="notification-header">
        <i class="fas fa-bell"></i> Notifications
        <button onclick="markAllRead()" style="float:right; background:none; border:none; color:white; cursor:pointer;">
            <i class="fas fa-check-double"></i> Mark all read
        </button>
    </div>
    <div class="notification-list" id="notificationList">
        <div style="padding:20px; text-align:center; color:#999;">
            <i class="fas fa-inbox"></i><br>No new notifications
        </div>
    </div>
</div>

<!-- Audio element removed - using Web Audio API instead -->

<script>
let lastNotificationCount = 0;
let adminNotificationCheckInterval;

// Request notification permission on page load
if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission();
}

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
    if(dropdown.classList.contains('show')) {
        loadNotifications();
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    if (!bell.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

function loadNotifications() {
    fetch('api-admin-notifications.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                displayNotifications(data.notifications);
            }
        });
}

function displayNotifications(notifications) {
    const list = document.getElementById('notificationList');
    
    if(notifications.length === 0) {
        list.innerHTML = '<div style="padding:20px; text-align:center; color:#999;"><i class="fas fa-inbox"></i><br>No new notifications</div>';
        return;
    }
    
    list.innerHTML = notifications.map(notif => `
        <div class="notification-item unread" onclick="markAsRead(${notif.an_id}, ${notif.an_booking_id})">
            <div style="font-weight:bold; color:#333; margin-bottom:5px;">
                <i class="fas fa-${notif.an_type === 'BOOKING_REJECTED' ? 'times-circle' : notif.an_type === 'BOOKING_COMPLETED' ? 'check-circle' : 'bell'}"></i>
                ${notif.an_title}
            </div>
            <div style="font-size:13px; color:#666;">${notif.an_message}</div>
            <div style="font-size:11px; color:#999; margin-top:5px;">
                <i class="fas fa-clock"></i> ${formatTime(notif.an_created_at)}
            </div>
        </div>
    `).join('');
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds
    
    if(diff < 60) return 'Just now';
    if(diff < 3600) return Math.floor(diff / 60) + ' min ago';
    if(diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    return date.toLocaleDateString();
}

function markAsRead(notificationId, bookingId) {
    fetch('api-mark-notification-read.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'notification_id=' + notificationId
    }).then(() => {
        checkNewNotifications();
        if(bookingId) {
            window.location.href = 'admin-manage-service-booking.php?highlight=' + bookingId;
        }
    });
}

function markAllRead() {
    fetch('api-mark-notification-read.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: ''
    }).then(() => {
        checkNewNotifications();
        document.getElementById('notificationDropdown').classList.remove('show');
    });
}

function checkNewNotifications() {
    fetch('api-admin-notifications.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const count = data.count;
                const badge = document.getElementById('notificationCount');
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'block';
                    
                    // New notification arrived
                    if(count > lastNotificationCount && lastNotificationCount > 0) {
                        playNotificationSound();
                        showBrowserNotification(data.notifications[0]);
                    }
                } else {
                    badge.style.display = 'none';
                }
                
                lastNotificationCount = count;
            }
        })
        .catch(error => console.error('Notification check failed:', error));
}

function playNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    } catch(e) {
        console.log('Sound play failed:', e);
    }
}

function showBrowserNotification(notification) {
    if ("Notification" in window && Notification.permission === "granted") {
        const notif = new Notification(notification.an_title, {
            body: notification.an_message,
            icon: '../vendor/EZlogonew.png',
            badge: '../vendor/EZlogonew.png',
            tag: 'booking-' + notification.an_booking_id,
            requireInteraction: true,
            vibrate: [200, 100, 200]
        });
        
        notif.onclick = function() {
            window.focus();
            markAsRead(notification.an_id, notification.an_booking_id);
            notif.close();
        };
    }
}

// Check for new notifications every 5 seconds
adminNotificationCheckInterval = setInterval(checkNewNotifications, 5000);

// Initial check
checkNewNotifications();

// Check immediately when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        checkNewNotifications();
    }
});
</script>
