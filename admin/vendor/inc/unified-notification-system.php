<!-- 
    Unified Admin Notification System
    Shows real-time notifications with sound on ALL admin pages
    Triggers: New booking, Rejected, Cancelled, Completed
-->
<style>
/* Notification Bell Icon - Enhanced */
.unified-notification-bell {
    position: relative;
    cursor: pointer;
    font-size: 24px;
    color: #fff;
    margin-right: 15px;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    padding: 8px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.unified-notification-bell:hover {
    transform: scale(1.15) rotate(15deg);
    background: rgba(255,255,255,0.1);
}
.unified-notification-bell.ringing {
    animation: ring 0.5s ease-in-out;
}
@keyframes ring {
    0%, 100% { transform: rotate(0deg); }
    10%, 30%, 50%, 70%, 90% { transform: rotate(-10deg); }
    20%, 40%, 60%, 80% { transform: rotate(10deg); }
}

/* Badge - Modern Design */
.unified-notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: linear-gradient(135deg, #ff4757 0%, #ff3838 100%);
    color: white;
    border-radius: 12px;
    padding: 2px 7px;
    font-size: 11px;
    font-weight: 700;
    animation: pulse-badge 2s infinite;
    min-width: 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.6), 0 0 0 3px rgba(255, 255, 255, 0.3);
    border: 2px solid white;
}
@keyframes pulse-badge {
    0%, 100% { 
        transform: scale(1); 
        box-shadow: 0 2px 8px rgba(255, 71, 87, 0.6), 0 0 0 3px rgba(255, 255, 255, 0.3);
    }
    50% { 
        transform: scale(1.1); 
        box-shadow: 0 4px 12px rgba(255, 71, 87, 0.8), 0 0 0 6px rgba(255, 71, 87, 0.2);
    }
}

/* Popup Notification - Modern Design */
.unified-notification-popup {
    position: fixed;
    top: 80px;
    right: 20px;
    min-width: 380px;
    max-width: 420px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.05);
    z-index: 99999;
    animation: slideInBounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    overflow: hidden;
    backdrop-filter: blur(10px);
}
@keyframes slideInBounce {
    0% { transform: translateX(500px) scale(0.8); opacity: 0; }
    60% { transform: translateX(-10px) scale(1.02); opacity: 1; }
    80% { transform: translateX(5px) scale(0.98); }
    100% { transform: translateX(0) scale(1); }
}
.unified-notification-popup.closing {
    animation: slideOutFade 0.4s ease-in forwards;
}
@keyframes slideOutFade {
    0% { transform: translateX(0) scale(1); opacity: 1; }
    100% { transform: translateX(450px) scale(0.9); opacity: 0; }
}

/* Notification Header - Gradient based on type */
.unified-notification-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 20px;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}
.unified-notification-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    animation: shimmer 3s infinite;
}
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
.unified-notification-header.new {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}
.unified-notification-header.rejected {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}
.unified-notification-header.completed {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}
.unified-notification-header.cancelled {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

/* Notification Body */
.unified-notification-body {
    padding: 24px 20px;
    color: #1f2937;
    background: white;
}

/* Close Button */
.unified-notification-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    z-index: 1;
}
.unified-notification-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

/* Icon Styles */
.unified-notification-icon {
    font-size: 56px;
    margin-bottom: 12px;
    animation: iconPop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
}
@keyframes iconPop {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}
.unified-notification-icon.new { 
    color: #10b981;
    animation: iconPop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55), pulse 2s infinite;
}
.unified-notification-icon.rejected { 
    color: #ef4444;
    animation: iconPop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55), shake 0.5s;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}
.unified-notification-icon.completed { 
    color: #3b82f6;
    animation: iconPop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55), bounce 1s;
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.unified-notification-icon.cancelled { 
    color: #f59e0b;
}

/* Message Text */
.unified-notification-message {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
    line-height: 1.5;
}
.unified-notification-details {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 16px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #e5e7eb;
}
.unified-notification-details strong {
    color: #374151;
}

/* Action Button */
.unified-notification-action {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
.unified-notification-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    color: white;
    text-decoration: none;
}
.unified-notification-action i {
    margin-right: 6px;
}

/* Progress Bar */
.unified-notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: rgba(255,255,255,0.3);
    animation: progressBar 10s linear forwards;
}
@keyframes progressBar {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<!-- Notification Container for Popups -->
<div id="unifiedNotificationContainer" style="position: fixed; top: 0; right: 0; z-index: 99999;"></div>

<!-- Audio Element -->
<audio id="unifiedNotificationSound" preload="auto">
    <source src="vendor/sounds/arived.mp3" type="audio/mpeg">
</audio>

<script>
(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        checkInterval: 3000, // Check every 3 seconds
        soundEnabled: true,
        popupDuration: 10000, // 10 seconds
        apiEndpoint: 'api-unified-notifications.php'
    };
    
    // State
    let lastCheckTimestamp = Math.floor(Date.now() / 1000);
    let notificationCount = 0;
    let checkInterval = null;
    let soundElement = null;
    let shownNotifications = new Set(); // Track shown notification IDs
    
    // Initialize
    function init() {
        soundElement = document.getElementById('unifiedNotificationSound');
        
        // Enable sound on first user interaction (to bypass autoplay policy)
        const enableSoundOnInteraction = function() {
            if (soundElement) {
                // Preload and prepare audio
                soundElement.load();
                soundElement.volume = 0.8;
                console.log('✅ Sound ready for playback');
            }
            // Remove listeners after first interaction
            document.removeEventListener('click', enableSoundOnInteraction);
            document.removeEventListener('keydown', enableSoundOnInteraction);
            document.removeEventListener('touchstart', enableSoundOnInteraction);
        };
        
        // Listen for any user interaction
        document.addEventListener('click', enableSoundOnInteraction, { once: true });
        document.addEventListener('keydown', enableSoundOnInteraction, { once: true });
        document.addEventListener('touchstart', enableSoundOnInteraction, { once: true });
        
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        // Start checking for notifications
        checkForNotifications();
        checkInterval = setInterval(checkForNotifications, CONFIG.checkInterval);
        
        // Check when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkForNotifications();
            }
        });
        
        console.log('✅ Unified Notification System initialized');
        console.log('💡 Click anywhere on page to enable notification sound');
    }
    
    // Check for new notifications
    function checkForNotifications() {
        fetch(CONFIG.apiEndpoint + '?last_check=' + lastCheckTimestamp)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    handleNotifications(data.notifications);
                    updateBadge(data.unread_count);
                    lastCheckTimestamp = data.current_timestamp;
                }
            })
            .catch(error => {
                console.error('Notification check failed:', error);
            });
    }
    
    // Handle incoming notifications
    function handleNotifications(notifications) {
        if (!notifications || notifications.length === 0) return;
        
        // Filter out already shown notifications
        const newNotifications = notifications.filter(notif => {
            if (shownNotifications.has(notif.id)) {
                return false; // Already shown
            }
            shownNotifications.add(notif.id);
            return true;
        });
        
        if (newNotifications.length === 0) return;
        
        // Ring the bell icon in nav
        const bellIcon = document.querySelector('#notificationBell i');
        if (bellIcon) {
            bellIcon.classList.add('bell-shake');
            setTimeout(() => bellIcon.classList.remove('bell-shake'), 500);
        }
        
        // Play sound only once for batch of notifications
        let soundPlayed = false;
        
        newNotifications.forEach(notification => {
            showNotification(notification);
            if (!soundPlayed) {
                playSound();
                soundPlayed = true;
            }
            showBrowserNotification(notification);
        });
        
        // Clean up old notification IDs (keep last 100)
        if (shownNotifications.size > 100) {
            const arr = Array.from(shownNotifications);
            shownNotifications = new Set(arr.slice(-100));
        }
    }
    
    // Show popup notification
    function showNotification(data) {
        const container = document.getElementById('unifiedNotificationContainer');
        if (!container) return;
        
        const popup = document.createElement('div');
        popup.className = 'unified-notification-popup';
        popup.id = 'notification-' + data.id;
        
        let icon = '';
        let iconClass = '';
        let title = '';
        
        switch(data.type) {
            case 'NEW_BOOKING':
                icon = 'fa-bell';
                iconClass = 'new';
                title = '🆕 New Booking Received!';
                break;
            case 'BOOKING_REJECTED':
                icon = 'fa-times-circle';
                iconClass = 'rejected';
                title = '❌ Booking Rejected';
                break;
            case 'BOOKING_COMPLETED':
                icon = 'fa-check-circle';
                iconClass = 'completed';
                title = '✅ Booking Completed';
                break;
            case 'BOOKING_CANCELLED':
                icon = 'fa-ban';
                iconClass = 'cancelled';
                title = '⚠️ Booking Cancelled';
                break;
            default:
                icon = 'fa-info-circle';
                iconClass = 'new';
                title = '📋 Booking Update';
        }
        
        popup.innerHTML = `
            <div class="unified-notification-header ${iconClass}">
                <span><i class="fas ${icon}"></i> ${title}</span>
                <button class="unified-notification-close" onclick="closeNotification('${popup.id}')">&times;</button>
            </div>
            <div class="unified-notification-body">
                <div class="text-center">
                    <i class="fas ${icon} unified-notification-icon ${iconClass}"></i>
                </div>
                <div class="unified-notification-message">
                    ${data.message}
                </div>
                ${data.details ? `<div class="unified-notification-details">${data.details.replace(/\|/g, '<br>')}</div>` : ''}
                <div class="text-center">
                    <a href="admin-view-service-booking.php?sb_id=${data.booking_id}" 
                       class="unified-notification-action">
                        <i class="fas fa-eye"></i> View Booking Details
                    </a>
                </div>
            </div>
            <div class="unified-notification-progress"></div>
        `;
        
        container.appendChild(popup);
        
        // Auto-close after duration
        setTimeout(() => {
            closeNotification(popup.id);
        }, CONFIG.popupDuration);
    }
    
    // Close notification popup
    window.closeNotification = function(popupId) {
        const popup = document.getElementById(popupId);
        if (popup) {
            popup.classList.add('closing');
            setTimeout(() => popup.remove(), 300);
        }
    };
    
    // Play notification sound
    function playSound() {
        if (!CONFIG.soundEnabled || !soundElement) return;
        
        try {
            // Reset audio to beginning
            soundElement.currentTime = 0;
            soundElement.volume = 0.8;
            
            // Play with promise handling
            const playPromise = soundElement.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        console.log('🔊 Notification sound played successfully');
                    })
                    .catch(error => {
                        console.warn('⚠️ Sound autoplay blocked. Click anywhere on page to enable sound.');
                        // Enable sound on first user interaction
                        document.addEventListener('click', function enableSound() {
                            soundElement.play().then(() => {
                                console.log('✅ Sound enabled after user interaction');
                            }).catch(() => {});
                            document.removeEventListener('click', enableSound);
                        }, { once: true });
                    });
            }
        } catch (e) {
            console.error('Sound error:', e);
        }
    }
    
    // Show browser notification
    function showBrowserNotification(data) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        
        let title = '';
        let body = data.message;
        
        switch(data.type) {
            case 'NEW_BOOKING':
                title = '🆕 New Booking';
                break;
            case 'BOOKING_REJECTED':
                title = '❌ Booking Rejected';
                break;
            case 'BOOKING_COMPLETED':
                title = '✅ Booking Completed';
                break;
            case 'BOOKING_CANCELLED':
                title = '⚠️ Booking Cancelled';
                break;
            default:
                title = '📋 Booking Update';
        }
        
        const notification = new Notification(title, {
            body: body + (data.details ? '\n' + data.details : ''),
            icon: 'vendor/img/logo.png',
            badge: 'vendor/img/logo.png',
            tag: 'booking-' + data.booking_id,
            requireInteraction: false,
            vibrate: [200, 100, 200]
        });
        
        notification.onclick = function() {
            window.focus();
            window.location.href = 'admin-view-service-booking.php?sb_id=' + data.booking_id;
            notification.close();
        };
        
        // Auto-close browser notification after 8 seconds
        setTimeout(() => notification.close(), 8000);
    }
    
    // Update notification badge (uses existing nav badge)
    function updateBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;
        
        notificationCount = count;
        
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'block';
            
            // Ring the bell icon in nav
            const bellIcon = document.querySelector('#notificationBell i');
            if (bellIcon) {
                bellIcon.classList.add('bell-shake');
                setTimeout(() => bellIcon.classList.remove('bell-shake'), 500);
            }
        } else {
            badge.style.display = 'none';
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Test notification sound (called when clicking bell icon)
    window.testNotificationSound = function() {
        const sound = document.getElementById('unifiedNotificationSound');
        if (sound) {
            sound.currentTime = 0;
            sound.volume = 0.8;
            sound.play()
                .then(() => {
                    console.log('🔊 Test sound played successfully');
                    // Show brief confirmation on nav bell
                    const bellIcon = document.querySelector('#notificationBell i');
                    if (bellIcon) {
                        bellIcon.style.color = '#10b981';
                        setTimeout(() => {
                            bellIcon.style.color = '';
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('❌ Sound test failed:', error);
                    alert('Sound is blocked by browser. Please allow audio autoplay for this site.');
                });
        }
    };
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });
    
})();
</script>
