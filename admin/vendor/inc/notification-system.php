<!-- Real-time Notification System - Universal -->
<script>
    // Create audio element for notification sound
    const notificationAudio = new Audio('vendor/sounds/arived.mp3');
    notificationAudio.volume = 0.7; // Set volume to 70%
    
    function playNotificationSound() {
        try {
            // Reset audio to start if already playing
            notificationAudio.currentTime = 0;
            
            // Play the sound
            notificationAudio.play()
                .then(() => {
                    console.log('🔊 Notification sound played');
                })
                .catch((error) => {
                    console.error('❌ Error playing sound:', error);
                    console.log('💡 Tip: Click anywhere on the page first to enable audio');
                });
        } catch(e) {
            console.error('❌ Sound error:', e);
        }
    }

    // Show notification toast
    function showNotification(title, bookings, type = 'new') {
        const count = bookings.length;
        const bgColor = type === 'update' ? 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        const icon = type === 'update' ? 'sync-alt' : 'bell';
        
        // Remove existing notifications
        $('.notification-toast').remove();
        
        // Create notification HTML
        let notificationHTML = `
            <div class="notification-toast" style="
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
                    <h4 style="margin: 0; font-weight: bold;">${title}</h4>
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
        
        bookings.forEach(booking => {
            notificationHTML += `
                <div style="
                    background: rgba(255,255,255,0.2);
                    padding: 10px;
                    border-radius: 5px;
                    margin-top: 10px;
                ">
                    <strong>Booking #${booking.id}</strong><br>
                    <small>
                        👤 ${booking.customer}<br>
                        📞 ${booking.phone}<br>
                        🔧 ${booking.service}<br>
                        ${booking.status ? '📊 Status: ' + booking.status : ''}
                    </small>
                </div>
            `;
        });
        
        notificationHTML += `
                <div style="margin-top: 15px; text-align: center;">
                    <a href="admin-all-bookings.php" style="
                        background: white;
                        color: #667eea;
                        padding: 8px 20px;
                        border-radius: 5px;
                        text-decoration: none;
                        font-weight: bold;
                        display: inline-block;
                    ">View All Bookings</a>
                </div>
            </div>
        `;
        
        // Add to page
        $('body').append(notificationHTML);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            $('.notification-toast').fadeOut(500, function() {
                $(this).remove();
            });
        }, 10000);
    }

    // Check for new bookings and updates
    function checkNotifications() {
        $.ajax({
            url: 'check-new-bookings.php',
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
                    
                    // Check for new bookings
                    if(response.has_new && response.new_count > 0) {
                        console.log('🔔 NEW BOOKINGS:', response.new_count);
                        playNotificationSound();
                        showNotification(
                            response.new_count === 1 ? 'New Booking!' : `${response.new_count} New Bookings!`,
                            response.bookings,
                            'new'
                        );
                        
                        // Update badge
                        $('#notificationBadge').text(response.new_count).show();
                        
                        // Browser notification
                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification('New Booking Received!', {
                                body: `${response.new_count} new booking(s) received`,
                                icon: '/vendor/img/icons/icon-192x192.png',
                                tag: 'new-booking'
                            });
                        }
                    }
                    
                    // Check for status updates
                    if(response.has_updates && response.update_count > 0) {
                        console.log('🔄 STATUS UPDATES:', response.update_count);
                        playNotificationSound();
                        showNotification(
                            response.update_count === 1 ? 'Booking Updated!' : `${response.update_count} Bookings Updated!`,
                            response.updates,
                            'update'
                        );
                        
                        // Update badge
                        const totalCount = (response.new_count || 0) + response.update_count;
                        $('#notificationBadge').text(totalCount).show();
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

    // Start checking every 10 seconds
    setInterval(checkNotifications, 10000);
    
    // Check immediately after 2 seconds
    setTimeout(checkNotifications, 2000);
    
    // Add CSS animation
    if(!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
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
            
            .notification-toast:hover {
                transform: scale(1.02);
                transition: transform 0.2s;
            }
        `;
        document.head.appendChild(style);
    }
    
    console.log('✅ Notification system active on this page');
</script>
