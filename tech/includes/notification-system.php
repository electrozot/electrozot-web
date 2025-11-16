<!-- Technician Notification System -->
<script>
    // Create audio element for notification sound
    const techNotificationAudio = new Audio('../admin/vendor/sounds/arived.mp3');
    techNotificationAudio.volume = 0.7;
    
    function playTechNotificationSound() {
        try {
            techNotificationAudio.currentTime = 0;
            techNotificationAudio.play()
                .then(() => console.log('🔊 Notification sound played'))
                .catch((error) => {
                    console.error('❌ Error playing sound:', error);
                    console.log('💡 Tip: Click anywhere on the page first to enable audio');
                });
        } catch(e) {
            console.error('❌ Sound error:', e);
        }
    }

    // Show notification toast
    function showTechNotification(notifications) {
        const count = notifications.length;
        
        // Remove existing notifications
        $('.tech-notification-toast').remove();
        
        // Determine notification color based on action
        const firstAction = notifications[0].action;
        let bgColor = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        let icon = 'bell';
        
        if(firstAction === 'assigned') {
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
            icon = 'user-check';
        } else if(firstAction === 'approved') {
            bgColor = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
            icon = 'check-circle';
        } else if(firstAction === 'rejected') {
            bgColor = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
            icon = 'times-circle';
        }
        
        // Create notification HTML
        let notificationHTML = `
            <div class="tech-notification-toast" style="
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                z-index: 99999;
                min-width: 350px;
                max-width: 400px;
                animation: slideIn 0.5s ease-out;
            ">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <i class="fas fa-${icon}" style="font-size: 24px; margin-right: 10px;"></i>
                    <h4 style="margin: 0; font-weight: bold;">
                        ${count === 1 ? 'New Notification!' : count + ' New Notifications!'}
                    </h4>
                    <button onclick="this.parentElement.parentElement.remove()" style="
                        margin-left: auto;
                        background: transparent;
                        border: none;
                        color: white;
                        font-size: 20px;
                        cursor: pointer;
                    ">&times;</button>
                </div>
        `;
        
        notifications.forEach(notification => {
            notificationHTML += `
                <div style="
                    background: rgba(255,255,255,0.2);
                    padding: 10px;
                    border-radius: 5px;
                    margin-top: 10px;
                ">
                    <strong>📋 Booking #${notification.id}</strong><br>
                    <div style="margin-top: 5px; font-size: 13px;">
                        <strong>✨ ${notification.message}</strong><br>
                        👤 ${notification.customer}<br>
                        📞 ${notification.phone}<br>
                        🔧 ${notification.service}<br>
                        📊 Status: ${notification.status}
                        ${notification.deadline_date ? '<br>⏰ Deadline: ' + notification.deadline_date + ' ' + (notification.deadline_time || '') : ''}
                    </div>
                </div>
            `;
        });
        
        notificationHTML += `
                <div style="margin-top: 15px; text-align: center;">
                    <a href="dashboard.php" style="
                        background: white;
                        color: #667eea;
                        padding: 8px 20px;
                        border-radius: 5px;
                        text-decoration: none;
                        font-weight: bold;
                        display: inline-block;
                    ">View Dashboard</a>
                </div>
            </div>
        `;
        
        // Add to page
        $('body').append(notificationHTML);
        
        // Auto-remove after 15 seconds
        setTimeout(() => {
            $('.tech-notification-toast').fadeOut(500, function() {
                $(this).remove();
            });
        }, 15000);
    }

    // Check for notifications
    function checkTechNotifications() {
        $.ajax({
            url: 'check-technician-notifications.php',
            method: 'GET',
            dataType: 'text',
            cache: false,
            success: function(rawResponse) {
                try {
                    const response = JSON.parse(rawResponse);
                    
                    if(response.error) {
                        console.error('Server error:', response.error);
                        return;
                    }
                    
                    // Update notification badge count
                    const notifCountElement = document.getElementById('notificationCount');
                    if(notifCountElement && response.notification_count > 0) {
                        notifCountElement.textContent = response.notification_count;
                        notifCountElement.style.display = 'flex';
                    } else if(notifCountElement) {
                        notifCountElement.style.display = 'none';
                    }
                    
                    if(response.has_notifications && response.notification_count > 0) {
                        console.log('🔔 NEW NOTIFICATIONS:', response.notification_count);
                        console.log('📋 Details:', response.notifications);
                        
                        // Play sound
                        playTechNotificationSound();
                        
                        // Show notification
                        showTechNotification(response.notifications);
                        
                        // Browser notification
                        if ('Notification' in window && Notification.permission === 'granted') {
                            const firstNotif = response.notifications[0];
                            new Notification(firstNotif.message, {
                                body: `Booking #${firstNotif.id} - ${firstNotif.service}`,
                                icon: '/vendor/img/icons/icon-192x192.png',
                                tag: 'tech-notification'
                            });
                        }
                        
                        // Reload page after 3 seconds to show updated data
                        setTimeout(() => {
                            console.log('🔄 Reloading page...');
                            location.reload();
                        }, 3000);
                    }
                    
                } catch(e) {
                    console.error('JSON Parse Error:', e);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }

    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Get initial notification count on page load
    function getInitialNotificationCount() {
        $.ajax({
            url: 'check-technician-notifications.php',
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function(response) {
                if(response && response.notification_count > 0) {
                    // Show header notification dot
                    const headerNotifDot = document.getElementById('headerNotifDot');
                    if(headerNotifDot) {
                        headerNotifDot.style.display = 'block';
                    }
                    
                    // Show mobile notification alert
                    const mobileAlert = document.getElementById('mobileNotificationAlert');
                    const mobileAlertText = document.getElementById('mobileAlertText');
                    if(mobileAlert && mobileAlertText) {
                        mobileAlertText.textContent = `You have ${response.notification_count} new notification${response.notification_count > 1 ? 's' : ''}!`;
                        mobileAlert.style.display = 'flex';
                    }
                    
                    // Update floating button badge
                    const floatingBadge = document.getElementById('floatingBadge');
                    if(floatingBadge) {
                        floatingBadge.textContent = response.notification_count;
                        floatingBadge.style.display = 'flex';
                    }
                }
            }
        });
    }
    
    // Get initial count immediately
    getInitialNotificationCount();
    
    // Start checking every 10 seconds
    setInterval(checkTechNotifications, 10000);
    
    // Check immediately after 2 seconds
    setTimeout(checkTechNotifications, 2000);
    
    // Add CSS animation
    if(!document.getElementById('tech-notification-styles')) {
        const style = document.createElement('style');
        style.id = 'tech-notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            .tech-notification-toast:hover {
                transform: scale(1.02);
                transition: transform 0.2s;
            }
        `;
        document.head.appendChild(style);
    }
    
    console.log('✅ Technician notification system active');
</script>
