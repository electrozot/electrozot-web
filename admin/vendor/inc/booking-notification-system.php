<!-- Real-Time Booking Notification System -->
<style>
.notification-popup {
    position: fixed;
    top: 80px;
    right: 20px;
    width: 350px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    z-index: 9999;
    display: none;
    animation: slideIn 0.3s ease;
}
@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.notification-popup.show {
    display: block;
}
.notification-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 10px 10px 0 0;
    font-weight: bold;
}
.notification-body {
    padding: 15px;
}
.notification-close {
    float: right;
    cursor: pointer;
    font-size: 20px;
}
</style>

<div class="notification-popup" id="bookingNotificationPopup">
    <div class="notification-header">
        <span class="notification-close" onclick="closeNotificationPopup()">&times;</span>
        <i class="fas fa-bell"></i> <span id="notifTitle">New Booking!</span>
    </div>
    <div class="notification-body" id="notifBody">
        Loading...
    </div>
</div>

<script>
let lastBookingCheck = Date.now();
let bookingNotificationCheckInterval;

// Request notification permission on load
if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission();
}

function checkNewBookings() {
    fetch('api-check-new-bookings.php')
        .then(response => response.json())
        .then(data => {
            if(data.success && data.new_count > 0) {
                // New bookings found!
                console.log('New bookings detected:', data.new_count);
                
                // Play sound
                playBookingSound();
                
                // Show popup for each new booking
                data.new_bookings.forEach((booking, index) => {
                    setTimeout(() => {
                        showBookingPopup(booking);
                        showBrowserNotification(booking);
                    }, index * 1000); // Stagger notifications
                });
                
                // Update badge if exists
                updateBadge(data.total_pending);
            }
        })
        .catch(error => console.error('Booking check error:', error));
}

function playBookingSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Triple beep for booking notification - more noticeable
        [0, 300, 600].forEach((delay, index) => {
            setTimeout(() => {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                // Vary frequency for each beep
                oscillator.frequency.value = 800 + (index * 200);
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.3);
            }, delay);
        });
        
        console.log('🔊 Notification sound played');
    } catch(e) {
        console.error('❌ Sound error:', e);
    }
}

function showBookingPopup(booking) {
    const popup = document.getElementById('bookingNotificationPopup');
    const title = document.getElementById('notifTitle');
    const body = document.getElementById('notifBody');
    
    title.innerHTML = '<i class="fas fa-bell"></i> ' + booking.booking_type;
    
    body.innerHTML = `
        <div style="margin-bottom:10px;">
            <strong>Customer:</strong> ${booking.u_fname} ${booking.u_lname}<br>
            <strong>Phone:</strong> ${booking.u_phone}<br>
            <strong>Service:</strong> ${booking.s_name || 'Custom Service'}<br>
            <strong>Booking ID:</strong> #${booking.sb_id}<br>
            <strong>Time:</strong> ${new Date(booking.sb_created_at).toLocaleString()}
        </div>
        <a href="admin-manage-service-booking.php?highlight=${booking.sb_id}" 
           class="btn btn-primary btn-sm btn-block">
            <i class="fas fa-eye"></i> View Booking
        </a>
    `;
    
    popup.classList.add('show');
    
    console.log('📢 New booking notification displayed:', booking.sb_id);
    
    // Auto close after 10 seconds
    setTimeout(() => {
        popup.classList.remove('show');
    }, 10000);
}

function showBrowserNotification(booking) {
    if ("Notification" in window && Notification.permission === "granted") {
        const notif = new Notification(booking.booking_type, {
            body: `Customer: ${booking.u_fname} ${booking.u_lname}\nPhone: ${booking.u_phone}\nService: ${booking.s_name || 'Custom Service'}`,
            icon: '../vendor/EZlogonew.png',
            tag: 'booking-' + booking.sb_id,
            requireInteraction: true,
            vibrate: [200, 100, 200]
        });
        
        notif.onclick = function() {
            window.focus();
            window.location.href = 'admin-manage-service-booking.php?highlight=' + booking.sb_id;
            notif.close();
        };
    }
}

function closeNotificationPopup() {
    document.getElementById('bookingNotificationPopup').classList.remove('show');
}

function updateBadge(count) {
    const badge = document.getElementById('notificationBadge');
    const bell = document.querySelector('#notificationBell i');
    
    if(badge) {
        if(count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
            
            // Shake the bell icon
            if(bell) {
                bell.classList.add('bell-shake');
                setTimeout(() => bell.classList.remove('bell-shake'), 500);
            }
        } else {
            badge.style.display = 'none';
        }
    }
}

// Check every 3 seconds for new bookings
bookingNotificationCheckInterval = setInterval(checkNewBookings, 3000);

// Initial check after 1 second
setTimeout(checkNewBookings, 1000);

console.log('🔔 Real-time notification system active with sound (checking every 3s)');

// Check when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        checkNewBookings();
    }
});

// Check when window gains focus
window.addEventListener('focus', function() {
    checkNewBookings();
});
</script>
