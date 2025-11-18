 <nav class="navbar navbar-expand navbar-dark static-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">

     <a class="navbar-brand mr-1" href="admin-dashboard.php" style="display: flex; align-items: center; gap: 12px; padding: 8px 15px;">
         <div class="logo-container" style="background: white; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2); padding: 5px;">
             <img src="../vendor/EZlogonew.png" alt="Electrozot Logo" style="width: 100%; height: 100%; object-fit: contain;">
         </div>
     </a>

     <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
         <i class="fas fa-bars"></i>
     </button>

     <!-- Quick Booking Button - Centered -->
     <div class="mx-auto">
         <a href="admin-quick-booking.php" class="btn btn-success btn-sm shadow-sm" style="padding: 10px 25px; border-radius: 25px; font-weight: 600; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease;">
             <i class="fas fa-phone-alt"></i>
             <span>Quick Booking</span>
         </a>
     </div>
     
     <!-- Navbar -->
     <ul class="navbar-nav">
         <!-- Notification Bell -->
         <li class="nav-item dropdown no-arrow mx-1">
             <a class="nav-link" href="admin-notifications.php" id="notificationBell" style="position: relative;" title="View All Notifications">
                 <i class="fas fa-bell fa-fw" style="font-size: 20px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.2) rotate(15deg)'" onmouseout="this.style.transform='scale(1) rotate(0deg)'"></i>
                 <span id="notificationBadge" class="badge badge-danger badge-counter" style="
                     position: absolute;
                     top: -5px;
                     right: -5px;
                     display: none;
                     padding: 3px 6px;
                     border-radius: 10px;
                     font-size: 10px;
                     animation: pulse 2s infinite;
                 ">0</span>
             </a>
         </li>

         <li class="nav-item dropdown no-arrow">
             <a style="display: flex; align-items: center; gap: 8px;" class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <?php if(isset($_SESSION['a_photo']) && !empty($_SESSION['a_photo'])): ?>
                     <img src="../vendor/img/<?php echo htmlspecialchars($_SESSION['a_photo']); ?>" 
                          class="rounded-circle" 
                          style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff;"
                          alt="Admin Photo">
                 <?php else: ?>
                     <i class="fas fa-user-circle fa-fw"></i>
                 <?php endif; ?>
                 <h6 style="margin: 0;">
                     <?php 
                     if(isset($_SESSION['a_name'])) {
                         echo htmlspecialchars($_SESSION['a_name']);
                     } else {
                         echo 'Admin';
                     }
                     ?>
                 </h6>
             </a>
             <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                 <a class="dropdown-item" href="admin-profile.php"><i class="fas fa-user"></i> Profile</a>
                 <a class="dropdown-item" href="admin-change-password.php"><i class="fas fa-key"></i> Change Password</a>
                 <div class="dropdown-divider"></div>
                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt"></i> Logout</a>
             </div>
         </li>
     </ul>
 </nav>

 <style>
 /* Enhanced Navbar Styling */
 .navbar-brand {
     transition: all 0.3s ease;
 }
 
 .navbar-brand:hover {
     transform: translateY(-3px);
     filter: brightness(1.1);
 }
 
 .navbar-brand:hover .logo-container {
     box-shadow: 0 8px 25px rgba(255,193,7,0.5);
     transform: rotate(5deg) scale(1.05);
 }
 
 /* Animated gradient background */
 @keyframes gradientShift {
     0% { background-position: 0% 50%; }
     50% { background-position: 100% 50%; }
     100% { background-position: 0% 50%; }
 }
 
 nav.navbar {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
     box-shadow: 0 4px 15px rgba(0,0,0,0.1);
 }
 
 /* Sidebar Styling */
 .sidebar {
     background: linear-gradient(180deg, #f7fafc 0%, #edf2f7 100%) !important;
     box-shadow: 2px 0 10px rgba(0,0,0,0.05);
 }
 
 .sidebar .nav-link {
     color: #2d3748 !important;
     font-weight: 600;
     padding: 12px 20px;
     margin: 5px 10px;
     border-radius: 10px;
     transition: all 0.3s ease;
 }
 
 .sidebar .nav-link:hover {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
     color: white !important;
     transform: translateX(5px);
     box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
 }
 
 .sidebar .nav-link i {
     color: #667eea;
     margin-right: 10px;
     transition: all 0.3s ease;
 }
 
 .sidebar .nav-link:hover i {
     color: white !important;
 }
 
 .sidebar .nav-item.active .nav-link {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
     color: white !important;
     box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
 }
 
 .sidebar .nav-item.active .nav-link i {
     color: white !important;
 }
 
 .sidebar .dropdown-menu {
     background: white;
     border: none;
     box-shadow: 0 4px 15px rgba(0,0,0,0.1);
     border-radius: 10px;
     margin-left: 10px;
 }
 
 .sidebar .dropdown-item {
     color: #4a5568;
     padding: 10px 20px;
     transition: all 0.3s ease;
     border-radius: 8px;
     margin: 3px 5px;
 }
 
 .sidebar .dropdown-item:hover {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
     color: white !important;
     transform: translateX(5px);
 }
 
 .sidebar .dropdown-item i {
     color: #667eea;
     margin-right: 8px;
     width: 20px;
     text-align: center;
 }
 
 .sidebar .dropdown-item:hover i {
     color: white;
 }
 
 .sidebar .dropdown-toggle::after {
     color: #667eea;
 }
 
 .sidebar .nav-link:hover .dropdown-toggle::after {
     color: white;
 }
 
 /* Logo animations */
 @keyframes logoPulse {
     0%, 100% { transform: scale(1) rotate(0deg); }
     50% { transform: scale(1.08) rotate(-5deg); }
 }
 
 @keyframes sparkle {
     0%, 100% { opacity: 1; transform: scale(1); }
     50% { opacity: 0.6; transform: scale(1.3); }
 }
 
 .logo-container {
     animation: logoPulse 3s ease-in-out infinite;
     transition: all 0.3s ease;
 }
 
 .logo-container > div {
     animation: sparkle 2s ease-in-out infinite;
 }
 
 .logo-container img {
     animation: logoPulse 2.5s ease-in-out infinite;
     transition: all 0.3s ease;
 }
 
 /* Glow effect on hover */
 .navbar-brand:hover .logo-container img {
     filter: drop-shadow(0 0 8px rgba(255,193,7,0.6)) drop-shadow(2px 2px 6px rgba(0,0,0,0.2));
     transform: scale(1.05);
 }
 
 /* Quick Booking Button Styling */
 .btn-success {
     background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
     border: none !important;
     box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3) !important;
 }
 
 .btn-success:hover {
     background: linear-gradient(135deg, #218838 0%, #1aa179 100%) !important;
     transform: translateY(-2px);
     box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
 }
 
 .btn-success:active {
     transform: translateY(0);
 }
 
 /* Responsive adjustments */
 @media (max-width: 768px) {
     .navbar-brand {
         padding: 5px 10px !important;
         gap: 8px !important;
     }
     .navbar-brand > div:last-child span:first-child {
         font-size: 14px !important;
     }
     .navbar-brand > div:last-child span:last-child {
         font-size: 9px !important;
     }
     .logo-container {
         width: 40px !important;
         height: 40px !important;
         padding: 3px !important;
     }
     .logo-container img {
         width: 100% !important;
         height: 100% !important;
     }
     
     /* Hide Quick Booking text on mobile */
     .btn-success span {
         display: none;
     }
     .btn-success {
         padding: 8px 12px !important;
     }
 }
 
 @media (max-width: 576px) {
     .navbar-brand > div:last-child span:first-child {
         font-size: 12px !important;
     }
     .navbar-brand > div:last-child span:last-child {
         display: none;
     }
 }
 </style>

<!-- Real-Time Notification System -->
<audio id="notificationSound" preload="auto">
    <source src="../vendor/sounds/notification.mp3" type="audio/mpeg">
</audio>

<script>
let lastNotificationCount = 0;

// Request notification permission
if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission();
}

function checkNewNotifications() {
    fetch('api-admin-notifications.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const count = data.count;
                const badge = document.getElementById('notificationBadge');
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'block';
                    
                    // New notification arrived
                    if(count > lastNotificationCount && lastNotificationCount >= 0) {
                        playNotificationSound();
                        if(data.notifications && data.notifications.length > 0) {
                            showBrowserNotification(data.notifications[0]);
                        }
                    }
                } else {
                    badge.style.display = 'none';
                }
                
                lastNotificationCount = count;
            }
        })
        .catch(error => console.log('Notification check:', error));
}

function playNotificationSound() {
    try {
        // Create Web Audio API context
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Create oscillator for beep sound
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        // Set frequency (800Hz for notification sound)
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        // Set volume
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        // Play sound
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
        
        // Second beep
        setTimeout(() => {
            const osc2 = audioContext.createOscillator();
            const gain2 = audioContext.createGain();
            osc2.connect(gain2);
            gain2.connect(audioContext.destination);
            osc2.frequency.value = 1000;
            osc2.type = 'sine';
            gain2.gain.setValueAtTime(0.3, audioContext.currentTime);
            gain2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            osc2.start(audioContext.currentTime);
            osc2.stop(audioContext.currentTime + 0.3);
        }, 200);
    } catch(e) {
        console.log('Sound generation failed:', e);
    }
}

function showBrowserNotification(notification) {
    if ("Notification" in window && Notification.permission === "granted") {
        const notif = new Notification(notification.an_title, {
            body: notification.an_message,
            icon: '../vendor/EZlogonew.png',
            badge: '../vendor/EZlogonew.png',
            tag: 'booking-' + notification.an_booking_id,
            requireInteraction: true
        });
        
        notif.onclick = function() {
            window.focus();
            window.location.href = 'admin-manage-service-booking.php?highlight=' + notification.an_booking_id;
            notif.close();
        };
    }
}

// Check every 5 seconds
setInterval(checkNewNotifications, 5000);
checkNewNotifications(); // Initial check

// Check when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        checkNewNotifications();
    }
});
</script>

 <!-- Logout Modal-->
 <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                 <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">×</span>
                 </button>
             </div>
             <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
             <div class="modal-footer">
                 <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                 <a class="btn btn-danger" href="admin-logout.php">Logout</a>
             </div>
         </div>
     </div>
 </div>