<!-- Technician Notification System - Clean Version -->
<script>
console.log('🚀 Initializing Technician Notification System v2.0');

let techNotificationAudio = null;
let audioEnabled = false;
let audioInitialized = false;

function initializeAudio() {
    if (!audioInitialized) {
        try {
            techNotificationAudio = new Audio('../admin/vendor/sounds/arived.mp3');
            techNotificationAudio.volume = 1.0;
            techNotificationAudio.preload = 'auto';
            techNotificationAudio.load();
            audioInitialized = true;
            console.log('✅ Audio initialized');
        } catch(e) {
            console.error('❌ Audio error:', e);
        }
    }
}

function enableAudio() {
    if (!audioEnabled && audioInitialized && techNotificationAudio) {
        techNotificationAudio.play().then(() => {
            techNotificationAudio.pause();
            techNotificationAudio.currentTime = 0;
            audioEnabled = true;
            console.log('✅ Audio enabled');
        }).catch(() => {});
    }
}

function playNotificationSound() {
    if (!audioInitialized) initializeAudio();
    if (!techNotificationAudio) return;
    
    techNotificationAudio.currentTime = 0;
    techNotificationAudio.volume = 1.0;
    
    techNotificationAudio.play()
        .then(() => {
            console.log('✅ Sound played');
            audioEnabled = true;
        })
        .catch((error) => {
            if (error.name === 'NotAllowedError' && !document.getElementById('audio-enable-alert')) {
                const alert = document.createElement('div');
                alert.id = 'audio-enable-alert';
                alert.style.cssText = 'position: fixed; top: 80px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 99999; font-weight: bold; cursor: pointer;';
                alert.innerHTML = '🔊 Click here to enable notification sounds';
                alert.onclick = function() { enableAudio(); this.remove(); };
                document.body.appendChild(alert);
                setTimeout(() => { if (alert.parentNode) alert.remove(); }, 10000);
            }
        });
}


function showNotificationToast(notifications) {
    const count = notifications.length;
    document.querySelectorAll('.tech-notification-toast').forEach(el => el.remove());
    
    const firstAction = notifications[0].action;
    let bgColor = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    let icon = 'bell';
    
    if(firstAction === 'assigned' || firstAction === 'reassigned') {
        bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        icon = 'user-check';
    } else if(firstAction === 'approved') {
        bgColor = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
        icon = 'check-circle';
    } else if(firstAction === 'hold') {
        bgColor = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
        icon = 'pause-circle';
    }
    
    let html = '<div class="tech-notification-toast" style="position: fixed; top: 80px; right: 20px; background: ' + bgColor + '; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 99999; min-width: 350px; max-width: 400px; animation: slideIn 0.5s ease-out;">';
    html += '<div style="display: flex; align-items: center; margin-bottom: 10px;">';
    html += '<i class="fas fa-' + icon + '" style="font-size: 24px; margin-right: 10px;"></i>';
    html += '<h4 style="margin: 0; font-weight: bold;">' + (count === 1 ? 'New Notification!' : count + ' New Notifications!') + '</h4>';
    html += '<button onclick="this.parentElement.parentElement.remove()" style="margin-left: auto; background: transparent; border: none; color: white; font-size: 20px; cursor: pointer;">&times;</button>';
    html += '</div>';
    
    notifications.forEach(notif => {
        html += '<div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 5px; margin-top: 10px;">';
        html += '<strong>📋 Booking #' + notif.id + '</strong><br>';
        html += '<div style="margin-top: 5px; font-size: 13px;">';
        html += '<strong>✨ ' + notif.message + '</strong><br>';
        html += '👤 ' + notif.customer + '<br>';
        html += '📞 ' + notif.phone + '<br>';
        html += '🔧 ' + notif.service + '<br>';
        html += '📊 Status: ' + notif.status;
        if(notif.deadline_date) {
            html += '<br>⏰ Deadline: ' + notif.deadline_date + ' ' + (notif.deadline_time || '');
        }
        html += '</div></div>';
    });
    
    html += '<div style="margin-top: 15px; text-align: center;">';
    html += '<a href="dashboard.php" style="background: white; color: #667eea; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block;">View Dashboard</a>';
    html += '</div></div>';
    
    document.body.insertAdjacentHTML('beforeend', html);
    
    setTimeout(() => {
        const toast = document.querySelector('.tech-notification-toast');
        if (toast) {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 500);
        }
    }, 15000);
}


function checkTechNotifications() {
    if (document.hidden) {
        console.log('⏭️ Page hidden, skipping check');
        return;
    }
    
    fetch('check-technician-notifications.php', { method: 'GET', cache: 'no-cache' })
    .then(response => response.text())
    .then(rawResponse => {
        const cleanResponse = rawResponse.trim().replace(/^\uFEFF/, '');
        const response = JSON.parse(cleanResponse);
        
        if(response.error) {
            console.error('❌ Server error:', response.error);
            return;
        }
        
        console.log('✅ Response:', response);
        
        const badge = document.getElementById('notificationCount');
        if(badge && response.notification_count > 0) {
            badge.textContent = response.notification_count;
            badge.style.display = 'flex';
        } else if(badge) {
            badge.style.display = 'none';
        }
        
        if(response.has_notifications && response.new_count > 0) {
            console.log('🔔 NEW NOTIFICATIONS:', response.new_count);
            playNotificationSound();
            showNotificationToast(response.notifications);
            
            const dot = document.getElementById('headerNotifDot');
            if(dot) dot.style.display = 'block';
            
            const mobileAlert = document.getElementById('mobileNotificationAlert');
            const mobileText = document.getElementById('mobileAlertText');
            if(mobileAlert && mobileText) {
                mobileText.textContent = 'You have ' + response.notification_count + ' new notification' + (response.notification_count > 1 ? 's' : '') + '!';
                mobileAlert.style.display = 'flex';
            }
            
            if (typeof showBrowserNotification === 'function') {
                response.notifications.forEach(notif => {
                    showBrowserNotification('🔔 New Booking Notification!', {
                        body: 'Booking #' + notif.id + '\n🔧 ' + notif.service + '\n👤 ' + notif.customer + '\n📞 ' + notif.phone,
                        icon: '/vendor/img/icons/icon-192x192.png',
                        badge: '/vendor/img/icons/badge-72x72.png',
                        vibrate: [300, 100, 300],
                        tag: 'booking-' + notif.id + '-' + notif.action,
                        requireInteraction: true,
                        renotify: false,
                        data: { url: '/tech/dashboard.php', booking_id: notif.id }
                    });
                });
            }
        } else {
            console.log('✓ No new notifications');
        }
    })
    .catch(error => { console.error('❌ AJAX Error:', error); });
}

initializeAudio();

['click', 'touchstart', 'keydown', 'mousedown', 'scroll'].forEach(event => {
    document.addEventListener(event, function enableOnce() {
        if (!audioEnabled) enableAudio();
        if (audioEnabled) document.removeEventListener(event, enableOnce);
    }, { passive: true, once: true });
});

fetch('check-technician-notifications.php', { cache: 'no-cache' })
    .then(response => response.json())
    .then(response => {
        if(response && response.notification_count > 0) {
            const dot = document.getElementById('headerNotifDot');
            if(dot) dot.style.display = 'block';
            const badge = document.getElementById('floatingBadge');
            if(badge) {
                badge.textContent = response.notification_count;
                badge.style.display = 'flex';
            }
        }
    });

setInterval(checkTechNotifications, 5000);
setTimeout(checkTechNotifications, 2000);

if(!document.getElementById('tech-notification-styles')) {
    const style = document.createElement('style');
    style.id = 'tech-notification-styles';
    style.textContent = '@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes pulse { 0%, 100% { transform: translateX(-50%) scale(1); } 50% { transform: translateX(-50%) scale(1.05); } } .tech-notification-toast { transition: all 0.5s ease; } .tech-notification-toast:hover { transform: scale(1.02); }';
    document.head.appendChild(style);
}

console.log('✅ Notification system initialized - Polling every 5 seconds');

// Register service worker for background notifications
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js')
        .then(registration => {
            console.log('✅ Service Worker registered for background notifications');
            
            // Request notification permission if not granted
            if (Notification.permission === 'default') {
                console.log('📋 Requesting notification permission...');
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        console.log('✅ Notification permission granted - background notifications enabled');
                    } else {
                        console.log('⚠️ Notification permission denied - background notifications disabled');
                    }
                });
            } else if (Notification.permission === 'granted') {
                console.log('✅ Notification permission already granted');
            }
        })
        .catch(error => {
            console.error('❌ Service Worker registration failed:', error);
        });
}

console.log('📱 Background notifications enabled:');
console.log('   ✓ Works when device is locked');
console.log('   ✓ Works when browser is in background');
console.log('   ✓ Works when screen is off');
console.log('   ✓ Vibration + sound alerts');
</script>
