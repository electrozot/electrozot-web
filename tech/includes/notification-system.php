<!-- Technician Notification System -->
<script>
    // Create audio element for notification sound
    let techNotificationAudio = null;
    let audioEnabled = false;
    let audioInitialized = false;
    let userInteracted = false;
    
    // Initialize audio
    function initializeAudio() {
        if (!audioInitialized) {
            try {
                techNotificationAudio = new Audio('../admin/vendor/sounds/arived.mp3');
                techNotificationAudio.volume = 1.0; // Maximum volume
                techNotificationAudio.preload = 'auto';
                techNotificationAudio.load();
                audioInitialized = true;
                console.log('✅ Audio initialized - Path: ../admin/vendor/sounds/arived.mp3');
            } catch(e) {
                console.error('❌ Audio initialization error:', e);
            }
        }
    }
    
    // Enable audio on first user interaction
    function enableAudio() {
        if (!userInteracted) {
            userInteracted = true;
            console.log('👆 User interaction detected - enabling audio');
        }
        
        if (!audioEnabled && audioInitialized && techNotificationAudio) {
            techNotificationAudio.play().then(() => {
                techNotificationAudio.pause();
                techNotificationAudio.currentTime = 0;
                audioEnabled = true;
                console.log('✅ Audio enabled and ready to play');
            }).catch((err) => {
                console.log('⚠️ Audio enable attempt:', err.message);
                // Try again on next interaction
                setTimeout(() => {
                    if (!audioEnabled && techNotificationAudio) {
                        techNotificationAudio.play().then(() => {
                            techNotificationAudio.pause();
                            techNotificationAudio.currentTime = 0;
                            audioEnabled = true;
                            console.log('✅ Audio enabled on retry');
                        }).catch(() => {});
                    }
                }, 500);
            });
        }
    }
    
    // Initialize audio immediately
    initializeAudio();
    
    // Enable audio on any user interaction - more aggressive approach
    ['click', 'touchstart', 'touchend', 'keydown', 'keyup', 'mousedown', 'scroll'].forEach(event => {
        document.addEventListener(event, function enableOnInteraction() {
            if (!audioEnabled) {
                enableAudio();
            }
            // Keep trying until audio is enabled
            if (audioEnabled) {
                document.removeEventListener(event, enableOnInteraction);
            }
        }, { passive: true });
    });
    
    // Force enable audio after 1 second
    setTimeout(() => {
        if (!audioEnabled) {
            console.log('⏱️ Attempting to enable audio after 1 second...');
            enableAudio();
        }
    }, 1000);
    
    // Try again after 3 seconds
    setTimeout(() => {
        if (!audioEnabled) {
            console.log('⏱️ Attempting to enable audio after 3 seconds...');
            enableAudio();
        }
    }, 3000);
    
    function playTechNotificationSound() {
        console.log('🔊 playTechNotificationSound() called');
        console.log('📊 Audio Status - Initialized:', audioInitialized, 'Enabled:', audioEnabled, 'User Interacted:', userInteracted);
        
        try {
            if (!audioInitialized) {
                console.log('⚠️ Audio not initialized, initializing now...');
                initializeAudio();
            }
            
            if (!techNotificationAudio) {
                console.error('❌ Audio object is null');
                return;
            }
            
            // Reset and play
            techNotificationAudio.currentTime = 0;
            techNotificationAudio.volume = 1.0;
            
            console.log('▶️ Attempting to play sound...');
            const playPromise = techNotificationAudio.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        console.log('✅ 🔊 NOTIFICATION SOUND PLAYED SUCCESSFULLY!');
                        audioEnabled = true;
                    })
                    .catch((error) => {
                        console.error('❌ Error playing sound:', error.name, '-', error.message);
                        
                        if (error.name === 'NotAllowedError') {
                            console.log('🚫 Browser blocked autoplay. User interaction required.');
                            console.log('💡 Please click anywhere on the page to enable sound.');
                            
                            // Show visual alert to user
                            if (!document.getElementById('audio-enable-alert')) {
                                const alert = document.createElement('div');
                                alert.id = 'audio-enable-alert';
                                alert.style.cssText = `
                                    position: fixed;
                                    top: 80px;
                                    left: 50%;
                                    transform: translateX(-50%);
                                    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                                    color: white;
                                    padding: 15px 25px;
                                    border-radius: 10px;
                                    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
                                    z-index: 99999;
                                    font-weight: bold;
                                    text-align: center;
                                    cursor: pointer;
                                    animation: pulse 1s infinite;
                                `;
                                alert.innerHTML = '🔊 Click here to enable notification sounds';
                                alert.onclick = function() {
                                    enableAudio();
                                    this.remove();
                                    // Try playing again
                                    setTimeout(() => playTechNotificationSound(), 200);
                                };
                                document.body.appendChild(alert);
                                
                                // Auto-remove after 10 seconds
                                setTimeout(() => {
                                    if (alert.parentNode) alert.remove();
                                }, 10000);
                            }
                        }
                        
                        // Try to enable audio and play again
                        if (!audioEnabled) {
                            console.log('💡 Attempting to enable audio...');
                            enableAudio();
                            setTimeout(() => {
                                if (techNotificationAudio) {
                                    console.log('🔄 Retrying sound playback...');
                                    techNotificationAudio.play()
                                        .then(() => console.log('✅ Sound played on retry!'))
                                        .catch(e => console.error('❌ Retry failed:', e.message));
                                }
                            }, 500);
                        }
                    });
            }
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
            timeout: 5000,
            success: function(rawResponse) {
                console.log('📡 Raw response received:', rawResponse.substring(0, 100));
                
                try {
                    // Clean response - remove any whitespace or BOM
                    const cleanResponse = rawResponse.trim().replace(/^\uFEFF/, '');
                    const response = JSON.parse(cleanResponse);
                    
                    if(response.error) {
                        console.error('❌ Server error:', response.error);
                        return;
                    }
                    
                    console.log('✅ Parsed response:', response);
                    
                    // Update notification badge count
                    const notifCountElement = document.getElementById('notificationCount');
                    if(notifCountElement && response.notification_count > 0) {
                        notifCountElement.textContent = response.notification_count;
                        notifCountElement.style.display = 'flex';
                    } else if(notifCountElement) {
                        notifCountElement.style.display = 'none';
                    }
                    
                    if(response.has_notifications && response.notification_count > 0) {
                        console.log('🔔 NEW NOTIFICATIONS DETECTED:', response.notification_count);
                        console.log('📋 Notification details:', response.notifications);
                        
                        // Play sound FIRST
                        console.log('🔊 Attempting to play sound...');
                        playTechNotificationSound();
                        
                        // Show visual notification
                        showTechNotification(response.notifications);
                        
                        // Update header notification dot
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
                        
                        // Browser notification (works even when tab is not active)
                        if (typeof showBrowserNotification === 'function') {
                            const firstNotif = response.notifications[0];
                            showBrowserNotification(firstNotif.message, {
                                body: `Booking #${firstNotif.id} - ${firstNotif.service}\nCustomer: ${firstNotif.customer}\nPhone: ${firstNotif.phone}`,
                                icon: '/vendor/img/icons/icon-192x192.png',
                                badge: '/vendor/img/icons/badge-72x72.png',
                                vibrate: [200, 100, 200, 100, 200],
                                tag: 'tech-notification-' + firstNotif.id,
                                requireInteraction: true,
                                data: {
                                    url: '/tech/dashboard.php',
                                    booking_id: firstNotif.id
                                }
                            });
                        } else if ('Notification' in window && Notification.permission === 'granted') {
                            // Fallback to regular notification
                            const firstNotif = response.notifications[0];
                            new Notification(firstNotif.message, {
                                body: `Booking #${firstNotif.id} - ${firstNotif.service}`,
                                icon: '/vendor/img/icons/icon-192x192.png',
                                tag: 'tech-notification-' + firstNotif.id,
                                requireInteraction: true
                            });
                        }
                        
                        // Reload page after 5 seconds to show updated data
                        setTimeout(() => {
                            console.log('🔄 Reloading page to show new bookings...');
                            location.reload();
                        }, 5000);
                    } else {
                        console.log('✓ No new notifications (count: ' + response.notification_count + ')');
                    }
                    
                } catch(e) {
                    console.error('❌ JSON Parse Error:', e);
                    console.error('Raw response:', rawResponse);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
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
    
    // Start checking every 5 seconds (more frequent for better responsiveness)
    setInterval(checkTechNotifications, 5000);
    
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
            
            @keyframes pulse {
                0%, 100% {
                    transform: translateX(-50%) scale(1);
                }
                50% {
                    transform: translateX(-50%) scale(1.05);
                }
            }
            
            .tech-notification-toast:hover {
                transform: scale(1.02);
                transition: transform 0.2s;
            }
        `;
        document.head.appendChild(style);
    }
    
    console.log('✅ Technician notification system initialized');
    console.log('🔊 Audio file path: ../admin/vendor/sounds/arived.mp3');
    console.log('⏱️ Checking for notifications every 5 seconds');
    console.log('💡 Click anywhere on the page to enable sound notifications');
</script>
