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
    animation: pulse 2s infinite;
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
    overflow-y: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    display: none;
    z-index: 9999;
}
.notification-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
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

<div class="notification-bell" onclick="toggleNotifications()">
    <i class="fas fa-bell"></i>
    <span class="notification-badge" id="notificationCount" style="display:none;">0</span>
</div>

<div class="notification-dropdown" id="notificationDropdown">
    <div style="padding: 15px; border-bottom: 2px solid #007bff; background: #f8f9fa;">
        <strong>Notifications</strong>
        <button onclick="markAllRead()" style="float: right; border: none; background: none; color: #007bff; cursor: pointer;">Mark all read</button>
    </div>
    <div id="notificationList"></div>
</div>

<audio id="notificationSound" preload="auto">
    <source src="../vendor/notification.mp3" type="audio/mpeg">
</audio>

<script>
let lastNotificationCount = 0;

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function checkNotifications() {
    fetch('api-admin-notifications.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const count = data.count;
                const badge = document.getElementById('notificationCount');
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'block';
                    
                    // Play sound if new notification
                    if(count > lastNotificationCount && lastNotificationCount > 0) {
                        document.getElementById('notificationSound').play();
                    }
                } else {
                    badge.style.display = 'none';
                }
                
                lastNotificationCount = count;
                displayNotifications(data.notifications);
            }
        });
}

function displayNotifications(notifications) {
    const list = document.getElementById('notificationList');
    if(notifications.length === 0) {
        list.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No new notifications</div>';
        return;
    }
    
    list.innerHTML = notifications.map(n => `
        <div class="notification-item ${n.an_is_read == 0 ? 'unread' : ''}" onclick="viewNotification(${n.an_id}, ${n.an_booking_id})">
            <div style="font-weight: bold; color: ${n.an_type === 'BOOKING_REJECTED' ? '#ff4757' : '#38ef7d'};">
                ${n.an_title}
            </div>
            <div style="font-size: 13px; color: #666; margin-top: 5px;">
                ${n.an_message}
            </div>
            <div style="font-size: 11px; color: #999; margin-top: 5px;">
                ${new Date(n.an_created_at).toLocaleString()}
            </div>
        </div>
    `).join('');
}

function viewNotification(notificationId, bookingId) {
    markAsRead(notificationId);
    if(bookingId) {
        window.location.href = 'admin-assign-technician.php?sb_id=' + bookingId;
    }
}

function markAsRead(notificationId) {
    const formData = new FormData();
    formData.append('notification_id', notificationId);
    fetch('api-mark-notification-read.php', {
        method: 'POST',
        body: formData
    }).then(() => checkNotifications());
}

function markAllRead() {
    fetch('api-mark-notification-read.php', {
        method: 'POST',
        body: new FormData()
    }).then(() => checkNotifications());
}

// Check every 5 seconds
setInterval(checkNotifications, 5000);
checkNotifications();

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const bell = document.querySelector('.notification-bell');
    const dropdown = document.getElementById('notificationDropdown');
    if(!bell.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
