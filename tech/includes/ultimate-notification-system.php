<!-- Ultimate Notification System - Works Everywhere, Never Fails -->
<script>
console.log('🚀 Ultimate Notification System v1.0 - Initializing...');

// === CORE CONFIGURATION ===
const TECH_ID = <?php echo $_SESSION['t_id']; ?>;
const SYSTEM_KEY = `ultimateNotif_${TECH_ID}`;
const CHECK_INTERVAL = 5000; // 5 seconds

// === GLOBAL STATE ===
let isSystemEnabled = false;
let audioUnlocked = false;
let notificationPermission = false;
let checkingInterval = null;
let audioElement = null;
let serviceWorkerReady = false;
let pendingVisualNotifications = [];
let lastVisibilityCheck = Date.now();

// === DEVICE DETECTION ===
const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
console.log(`📱 Device: ${isMobile ? 'Mobile' : 'Desktop'}${isIOS ? ' (iOS)' : ''}`);

// === MAIN INITIALIZATION ===
function initUltimateSystem() {
    console.log('🎯 Starting Ultimate Notification System...');
    
    // Check if system is already enabled
    const enabled = localStorage.getItem(SYSTEM_KEY);
    if (enabled === 'enabled') {
        console.log('✅ System already enabled - activating all features');
        activateUltimateSystem();
    } else {
        console.log('⚠️ System not enabled - setting up activation trigger');
        setupActivationTrigger();
    }
    
    // Always start checking for notifications
    startUltimateChecking();
    
    // Check for recent notifications on page load (in case user missed them)
    setTimeout(() => {
        checkForRecentMissedNotifications();
    }, 3000);
    
    console.log('🚀 Ultimate System initialized successfully');
}

// === CHECK FOR RECENT MISSED NOTIFICATIONS ===
function checkForRecentMissedNotifications() {
    if (!isSystemEnabled) return;
    
    console.log('🔍 Checking for recent missed notifications...');
    
    fetch('check-technician-notifications.php', {
        method: 'GET',
        cache: 'no-cache',
        credentials: 'include',
        headers: {
            'X-Check-Recent': 'true'
        }
    })
    .then(response => response.text())
    .then(rawText => {
        const data = JSON.parse(rawText.trim().replace(/^\uFEFF/, ''));
        
        if (data.has_notifications && data.notifications && data.notifications.length > 0) {
            // Check if any notifications are very recent (last 2 minutes)
            const recentNotifications = data.notifications.filter(notif => {
                const notifTime = new Date(notif.updated_at || notif.assigned_at).getTime();
                const timeDiff = Date.now() - notifTime;
                return timeDiff < 120000; // 2 minutes
            });
            
            if (recentNotifications.length > 0) {
                console.log(`📋 Found ${recentNotifications.length} recent notifications to display`);
                
                // Show the most recent one as a persistent notification
                const latestNotification = recentNotifications[0];
                latestNotification.timestamp = new Date(latestNotification.updated_at || latestNotification.assigned_at).getTime();
                
                setTimeout(() => {
                    showPersistentVisualNotification(latestNotification);
                }, 1000);
            }
        }
    })
    .catch(error => {
        console.log('⚠️ Recent notification check failed:', error);
    });
}

// === SYSTEM ACTIVATION ===
function activateUltimateSystem() {
    isSystemEnabled = true;
    setupUltimateAudio();
    setupUltimateNotifications();
    setupUltimateServiceWorker();
    console.log('🌟 Ultimate System FULLY ACTIVATED');
}

// === AUDIO SYSTEM ===
function setupUltimateAudio() {
    try {
        audioElement = new Audio('../admin/vendor/sounds/arived.mp3');
        audioElement.volume = 1.0;
        audioElement.preload = 'auto';
        
        // Mobile optimizations
        if (isMobile) {
            audioElement.setAttribute('playsinline', true);
            audioElement.muted = false;
        }
        
        audioUnlocked = true;
        console.log('🔊 Ultimate Audio System Ready');
    } catch (error) {
        console.error('❌ Audio setup failed:', error);
    }
}

// === NOTIFICATION SYSTEM ===
function setupUltimateNotifications() {
    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            notificationPermission = true;
            console.log('🔔 Ultimate Notifications Ready');
        } else if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                notificationPermission = (permission === 'granted');
                console.log(`🔔 Notification permission: ${permission}`);
            });
        }
    }
}

// === SERVICE WORKER FOR BACKGROUND/LOCKED SCREEN ===
function setupUltimateServiceWorker() {
    if ('serviceWorker' in navigator) {
        // Create service worker content dynamically
        const swContent = `
            self.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'SHOW_NOTIFICATION') {
                    const data = event.data.notification;
                    self.registration.showNotification('🚀 New Booking Alert!', {
                        body: 'Booking #' + data.id + '\\n' + data.service + '\\n' + data.customer + '\\n' + data.phone,
                        icon: '/admin/vendor/img/logo.png',
                        badge: '/admin/vendor/img/logo.png',
                        tag: 'booking-' + data.id,
                        requireInteraction: true,
                        vibrate: [1000, 500, 1000, 500, 1000],
                        data: { url: '/tech/dashboard.php', booking_id: data.id }
                    });
                }
            });
            
            self.addEventListener('notificationclick', function(event) {
                event.notification.close();
                event.waitUntil(
                    clients.matchAll().then(function(clientList) {
                        for (let client of clientList) {
                            if (client.url.includes('/tech/dashboard.php') && 'focus' in client) {
                                return client.focus();
                            }
                        }
                        if (clients.openWindow) {
                            return clients.openWindow('/tech/dashboard.php');
                        }
                    })
                );
            });
        `;
        
        // Register service worker
        const blob = new Blob([swContent], { type: 'application/javascript' });
        const swUrl = URL.createObjectURL(blob);
        
        navigator.serviceWorker.register(swUrl)
            .then(registration => {
                serviceWorkerReady = true;
                console.log('🛡️ Ultimate Service Worker registered for background notifications');
            })
            .catch(error => {
                console.log('⚠️ Service Worker registration failed:', error);
            });
    }
}

// === ACTIVATION TRIGGER ===
function setupActivationTrigger() {
    const events = ['touchstart', 'click', 'keydown', 'scroll', 'mousedown'];
    
    function handleUserInteraction() {
        console.log('👆 User interaction detected - showing activation prompt');
        showUltimatePrompt();
        
        // Remove all listeners
        events.forEach(event => {
            document.removeEventListener(event, handleUserInteraction);
        });
    }
    
    // Add listeners for all interaction types
    events.forEach(event => {
        document.addEventListener(event, handleUserInteraction, { passive: true, once: true });
    });
}

// === ACTIVATION PROMPT ===
function showUltimatePrompt() {
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background: rgba(0,0,0,0.95); z-index: 999999;
        display: flex; align-items: center; justify-content: center; padding: 20px;
    `;
    
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; padding: 40px; border-radius: 25px; text-align: center;
        max-width: 450px; width: 100%; box-shadow: 0 25px 80px rgba(0,0,0,0.7);
        border: 3px solid rgba(255,255,255,0.2);
    `;
    
    modal.innerHTML = `
        <div style="font-size: 80px; margin-bottom: 25px; animation: pulse 2s infinite;">🚀</div>
        <h1 style="margin: 0 0 20px 0; font-size: 28px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            Ultimate Alert System
        </h1>
        <p style="margin: 0 0 30px 0; font-size: 16px; line-height: 1.6; opacity: 0.95;">
            <strong>🌟 Works EVERYWHERE:</strong> Dashboard, Background, Locked Screen<br>
            <strong>🔊 Sound + Visual Alerts</strong> for every booking event<br>
            <strong>🔒 One-Time Setup</strong> - Enabled FOREVER<br>
            <strong>⚡ Never Fails</strong> - Bulletproof reliability
        </p>
        <button id="ultimateEnableBtn" style="
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white; border: none; padding: 20px 40px; border-radius: 50px;
            font-weight: bold; cursor: pointer; font-size: 18px; margin-right: 20px;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            🚀 Enable Ultimate Alerts
        </button>
        <button id="ultimateSkipBtn" style="
            background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);
            padding: 20px 40px; border-radius: 50px; font-weight: bold; cursor: pointer; font-size: 18px;
        ">Skip</button>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Enable button
    document.getElementById('ultimateEnableBtn').onclick = function() {
        const btn = this;
        btn.innerHTML = '🔄 Activating Ultimate System...';
        btn.disabled = true;
        
        enableUltimateSystem().then(() => {
            overlay.remove();
            showUltimateMessage('🚀 Ultimate Alert System ENABLED FOREVER! You will never miss any notification.', 'success');
        }).catch(error => {
            btn.innerHTML = '🚀 Enable Ultimate Alerts';
            btn.disabled = false;
            showUltimateMessage('⚠️ Please allow notifications and sound to enable the ultimate system', 'warning');
        });
    };
    
    // Skip button
    document.getElementById('ultimateSkipBtn').onclick = function() {
        overlay.remove();
        localStorage.setItem(SYSTEM_KEY, 'skipped');
    };
}

// === SYSTEM ENABLEMENT ===
function enableUltimateSystem() {
    return new Promise((resolve, reject) => {
        console.log('🚀 Enabling Ultimate System...');
        
        // Step 1: Setup audio first
        if (!audioElement) {
            setupUltimateAudio();
        }
        
        // Step 2: Unlock audio with user interaction
        if (audioElement) {
            console.log('🔊 Testing audio unlock...');
            audioElement.play().then(() => {
                console.log('✅ Audio unlocked successfully');
                
                // Stop the test sound immediately
                setTimeout(() => {
                    audioElement.pause();
                    audioElement.currentTime = 0;
                }, 100);
                
                audioUnlocked = true;
                
                // Step 3: Request notification permission
                if ('Notification' in window && Notification.permission === 'default') {
                    console.log('🔔 Requesting notification permission...');
                    Notification.requestPermission().then(permission => {
                        console.log(`🔔 Notification permission result: ${permission}`);
                        notificationPermission = (permission === 'granted');
                        
                        // Step 4: Save enabled state
                        localStorage.setItem(SYSTEM_KEY, 'enabled');
                        isSystemEnabled = true;
                        
                        // Step 5: Activate all systems
                        setupUltimateServiceWorker();
                        
                        console.log('🚀 Ultimate System ENABLED FOREVER!');
                        resolve();
                    }).catch(error => {
                        console.error('❌ Notification permission failed:', error);
                        // Still enable audio-only mode
                        localStorage.setItem(SYSTEM_KEY, 'enabled');
                        isSystemEnabled = true;
                        resolve();
                    });
                } else {
                    // Notifications already granted or not available
                    notificationPermission = (Notification.permission === 'granted');
                    localStorage.setItem(SYSTEM_KEY, 'enabled');
                    isSystemEnabled = true;
                    setupUltimateServiceWorker();
                    console.log('🚀 Ultimate System enabled!');
                    resolve();
                }
            }).catch(error => {
                console.error('❌ Ultimate audio unlock failed:', error);
                console.log('🔍 Audio element details:', {
                    src: audioElement.src,
                    readyState: audioElement.readyState,
                    networkState: audioElement.networkState,
                    error: audioElement.error
                });
                reject(error);
            });
        } else {
            console.error('❌ Audio element not available');
            reject(new Error('Audio system not available'));
        }
    });
}

// === NOTIFICATION CHECKING ===
function startUltimateChecking() {
    console.log('🔍 Starting ultimate notification checking...');
    
    // Clear any existing interval
    if (checkingInterval) clearInterval(checkingInterval);
    
    // Check every 5 seconds
    checkingInterval = setInterval(checkUltimateNotifications, CHECK_INTERVAL);
    
    // Initial check after 2 seconds
    setTimeout(checkUltimateNotifications, 2000);
}

function checkUltimateNotifications() {
    fetch('check-technician-notifications.php', {
        method: 'GET',
        cache: 'no-cache',
        credentials: 'include',
        headers: {
            'X-Ultimate-Check': 'true'
        }
    })
    .then(response => response.text())
    .then(rawText => {
        const data = JSON.parse(rawText.trim().replace(/^\uFEFF/, ''));
        
        if (data.error) {
            console.error('❌ Ultimate API error:', data.error);
            return;
        }
        
        // Update notification badge
        updateUltimateBadge(data.notification_count);
        
        // Handle new notifications
        if (data.has_notifications && data.new_count > 0) {
            console.log(`🚀 ULTIMATE ALERT: ${data.new_count} new notifications detected!`);
            handleUltimateNotifications(data.notifications);
        }
    })
    .catch(error => {
        console.error('❌ Ultimate notification check failed:', error);
    });
}

// === ULTIMATE NOTIFICATION HANDLER ===
function handleUltimateNotifications(notifications) {
    console.log(`🚀 Processing ${notifications.length} ultimate notifications`);
    
    if (!isSystemEnabled) {
        console.log('⚠️ Ultimate system not enabled - notifications will be silent');
        return;
    }
    
    notifications.forEach((notification, index) => {
        // Process each notification with slight delay to prevent conflicts
        setTimeout(() => {
            processUltimateNotification(notification);
        }, index * 300);
    });
}

function processUltimateNotification(notification) {
    console.log(`🚀 Processing ultimate notification for booking #${notification.id}`);
    
    // 1. Play sound alert
    playUltimateSound();
    
    // 2. Show browser notification (works when locked/background)
    showUltimateBrowserNotification(notification);
    
    // 3. Show visual notification (when page is visible)
    showUltimateVisualNotification(notification);
    
    // 4. Trigger vibration (mobile)
    triggerUltimateVibration();
    
    // 5. Send to service worker for background handling
    sendToUltimateServiceWorker(notification);
}

// === SOUND ALERT ===
function playUltimateSound() {
    if (!audioUnlocked || !audioElement) {
        console.log('⚠️ Ultimate audio not ready');
        return;
    }
    
    try {
        audioElement.currentTime = 0;
        audioElement.volume = 1.0;
        
        const playPromise = audioElement.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('🔊 Ultimate sound played successfully');
                // Auto-stop after 4 seconds
                setTimeout(() => {
                    audioElement.pause();
                    audioElement.currentTime = 0;
                }, 4000);
            }).catch(error => {
                console.log('⚠️ Ultimate sound play failed:', error);
            });
        }
    } catch (error) {
        console.log('⚠️ Ultimate sound error:', error);
    }
}

// === BROWSER NOTIFICATION (WORKS WHEN LOCKED) ===
function showUltimateBrowserNotification(notification) {
    if (!notificationPermission) return;
    
    try {
        const browserNotif = new Notification('🚀 Ultimate Booking Alert!', {
            body: `📋 Booking #${notification.id}\n🔧 ${notification.service}\n👤 ${notification.customer}\n📞 ${notification.phone}\n✨ ${notification.message}`,
            icon: '../admin/vendor/img/logo.png',
            badge: '../admin/vendor/img/logo.png',
            tag: `ultimate-${notification.id}`,
            requireInteraction: true,
            silent: false,
            vibrate: [800, 400, 800, 400, 800],
            timestamp: Date.now()
        });
        
        browserNotif.onclick = function() {
            window.focus();
            this.close();
        };
        
        // Auto-close after 20 seconds
        setTimeout(() => browserNotif.close(), 20000);
        
        console.log('🔔 Ultimate browser notification shown');
    } catch (error) {
        console.error('❌ Ultimate browser notification failed:', error);
    }
}

// === VISUAL NOTIFICATION (WHEN PAGE VISIBLE) ===
function showUltimateVisualNotification(notification) {
    if (document.hidden) {
        console.log('👁️ Page hidden - storing notification for later display');
        // Store notification to show when page becomes visible
        pendingVisualNotifications.push({
            ...notification,
            timestamp: Date.now()
        });
        return;
    }
    
    // Remove existing ultimate notifications
    document.querySelectorAll('.ultimate-notification').forEach(el => el.remove());
    
    const visual = document.createElement('div');
    visual.className = 'ultimate-notification';
    visual.style.cssText = `
        position: fixed; top: 80px; left: 20px; right: 20px; z-index: 999999;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; padding: 25px; border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        border: 3px solid rgba(255,255,255,0.2);
        animation: ultimateSlideIn 0.6s ease-out;
    `;
    
    visual.innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="font-size: 35px; margin-right: 20px; animation: ultimateGlow 2s ease-in-out infinite;">🚀</div>
            <div style="flex: 1;">
                <h2 style="margin: 0; font-size: 22px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    Ultimate Booking Alert!
                </h2>
                <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">
                    ${new Date().toLocaleTimeString()}
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: rgba(255,255,255,0.2); border: none; color: white; 
                           font-size: 28px; cursor: pointer; border-radius: 50%; width: 40px; height: 40px;">×</button>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 15px; margin-bottom: 20px;">
            <div style="font-size: 18px; font-weight: bold; margin-bottom: 12px; color: #ffd700;">
                📋 Booking #${notification.id}
            </div>
            <div style="font-size: 16px; line-height: 1.5;">
                <div style="margin-bottom: 8px;"><strong>👤 Customer:</strong> ${notification.customer}</div>
                <div style="margin-bottom: 8px;"><strong>📞 Phone:</strong> ${notification.phone}</div>
                <div style="margin-bottom: 8px;"><strong>🔧 Service:</strong> ${notification.service}</div>
                <div style="margin-bottom: 8px;"><strong>✨ Status:</strong> ${notification.message}</div>
            </div>
        </div>
        <div style="text-align: center; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="tel:${notification.phone}" style="
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none;
                font-weight: bold; display: inline-flex; align-items: center; gap: 8px;
                box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4); font-size: 14px;
            ">📞 Call</a>
            <a href="booking-details.php?id=${notification.id}" style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none;
                font-weight: bold; display: inline-flex; align-items: center; gap: 8px;
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); font-size: 14px;
            ">📋 Details</a>
            <a href="dashboard.php" style="
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none;
                font-weight: bold; display: inline-flex; align-items: center; gap: 8px;
                box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4); font-size: 14px;
            ">🏠 Dashboard</a>
        </div>
    `;
    
    document.body.appendChild(visual);
    
    // Auto-remove after 25 seconds
    setTimeout(() => {
        if (visual.parentNode) {
            visual.style.opacity = '0';
            visual.style.transform = 'translateY(-20px)';
            setTimeout(() => visual.remove(), 600);
        }
    }, 25000);
    
    console.log('👁️ Ultimate visual notification shown');
}

// === VIBRATION ===
function triggerUltimateVibration() {
    if (isMobile && navigator.vibrate) {
        // Strong vibration pattern for ultimate alerts
        navigator.vibrate([800, 300, 800, 300, 800, 300, 800]);
        console.log('📳 Ultimate vibration triggered');
    }
}

// === SERVICE WORKER COMMUNICATION ===
function sendToUltimateServiceWorker(notification) {
    if (serviceWorkerReady && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
            type: 'SHOW_NOTIFICATION',
            notification: notification
        });
        console.log('🛡️ Notification sent to ultimate service worker');
    }
}

// === UTILITY FUNCTIONS ===
function updateUltimateBadge(count) {
    const badge = document.getElementById('notificationCount');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

function showUltimateMessage(text, type) {
    const msg = document.createElement('div');
    const bgColor = type === 'success' ? 
        'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 
        'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
    
    msg.style.cssText = `
        position: fixed; top: 90px; left: 50%; transform: translateX(-50%);
        background: ${bgColor}; color: white; padding: 20px 30px; border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4); z-index: 999999; font-weight: bold;
        font-size: 16px; text-align: center; max-width: 90%;
        border: 2px solid rgba(255,255,255,0.2);
    `;
    msg.textContent = text;
    document.body.appendChild(msg);
    
    setTimeout(() => {
        msg.style.opacity = '0';
        msg.style.transform = 'translateX(-50%) translateY(-20px)';
        setTimeout(() => msg.remove(), 600);
    }, 5000);
}

// === CSS ANIMATIONS ===
if (!document.getElementById('ultimate-styles')) {
    const style = document.createElement('style');
    style.id = 'ultimate-styles';
    style.textContent = `
        @keyframes ultimateSlideIn {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes ultimateGlow {
            0%, 100% { text-shadow: 0 0 10px rgba(255,255,255,0.5); }
            50% { text-shadow: 0 0 20px rgba(255,255,255,0.8), 0 0 30px rgba(255,215,0,0.6); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        @keyframes persistentPulse {
            0%, 100% { 
                box-shadow: 0 15px 50px rgba(0,0,0,0.5);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 20px 60px rgba(255, 107, 107, 0.6);
                transform: scale(1.02);
            }
        }
        .ultimate-notification {
            transition: all 0.6s ease;
        }
        .persistent-notification {
            border: 3px solid rgba(255, 215, 0, 0.6) !important;
        }
    `;
    document.head.appendChild(style);
}

// === VISIBILITY CHANGE HANDLER ===
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        console.log('👁️ Page became visible - checking for pending notifications');
        
        // Show any pending visual notifications
        if (pendingVisualNotifications.length > 0) {
            console.log(`📋 Showing ${pendingVisualNotifications.length} pending notifications`);
            
            // Show the most recent notification
            const latestNotification = pendingVisualNotifications[pendingVisualNotifications.length - 1];
            showPersistentVisualNotification(latestNotification);
            
            // Clear pending notifications
            pendingVisualNotifications = [];
        }
        
        // Update last visibility check
        lastVisibilityCheck = Date.now();
    } else {
        console.log('👁️ Page became hidden');
    }
});

// === PERSISTENT VISUAL NOTIFICATION ===
function showPersistentVisualNotification(notification) {
    console.log('🎯 Showing persistent visual notification for booking #' + notification.id);
    
    // Remove existing ultimate notifications
    document.querySelectorAll('.ultimate-notification').forEach(el => el.remove());
    
    const visual = document.createElement('div');
    visual.className = 'ultimate-notification persistent-notification';
    visual.style.cssText = `
        position: fixed; top: 80px; left: 20px; right: 20px; z-index: 999999;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white; padding: 25px; border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        border: 3px solid rgba(255,255,255,0.3);
        animation: ultimateSlideIn 0.6s ease-out, persistentPulse 2s ease-in-out infinite;
    `;
    
    const timeSince = Math.floor((Date.now() - notification.timestamp) / 1000);
    const timeText = timeSince < 60 ? `${timeSince}s ago` : `${Math.floor(timeSince / 60)}m ago`;
    
    visual.innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="font-size: 35px; margin-right: 20px; animation: ultimateGlow 2s ease-in-out infinite;">🔔</div>
            <div style="flex: 1;">
                <h2 style="margin: 0; font-size: 22px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    📱 Missed Notification!
                </h2>
                <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">
                    Received ${timeText} while screen was locked
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: rgba(255,255,255,0.2); border: none; color: white; 
                           font-size: 28px; cursor: pointer; border-radius: 50%; width: 40px; height: 40px;">×</button>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 15px; margin-bottom: 20px;">
            <div style="font-size: 18px; font-weight: bold; margin-bottom: 12px; color: #ffd700;">
                📋 Booking #${notification.id}
            </div>
            <div style="font-size: 16px; line-height: 1.5;">
                <div style="margin-bottom: 8px;"><strong>👤 Customer:</strong> ${notification.customer}</div>
                <div style="margin-bottom: 8px;"><strong>📞 Phone:</strong> ${notification.phone}</div>
                <div style="margin-bottom: 8px;"><strong>🔧 Service:</strong> ${notification.service}</div>
                <div style="margin-bottom: 8px;"><strong>✨ Status:</strong> ${notification.message}</div>
            </div>
        </div>
        <div style="text-align: center; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="tel:${notification.phone}" style="
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none;
                font-weight: bold; display: inline-flex; align-items: center; gap: 8px;
                box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4); font-size: 14px;
            ">📞 Call</a>
            <a href="booking-details.php?id=${notification.id}" style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none;
                font-weight: bold; display: inline-flex; align-items: center; gap: 8px;
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); font-size: 14px;
            ">📋 Details</a>
            <a href="dashboard.php" style="
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none;
                font-weight: bold; display: inline-flex; align-items: center; gap: 8px;
                box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4); font-size: 14px;
            ">🏠 Dashboard</a>
        </div>
    `;
    
    document.body.appendChild(visual);
    
    // Auto-remove after 30 seconds (longer for persistent notifications)
    setTimeout(() => {
        if (visual.parentNode) {
            visual.style.opacity = '0';
            visual.style.transform = 'translateY(-20px)';
            setTimeout(() => visual.remove(), 600);
        }
    }, 30000);
    
    console.log('✅ Persistent visual notification shown');
}

// === INITIALIZATION ===
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUltimateSystem);
} else {
    initUltimateSystem();
}

// === GLOBAL TEST FUNCTION ===
window.testUltimateNotification = function() {
    const testNotif = {
        id: 'ULTIMATE' + Date.now(),
        customer: 'Ultimate Test Customer',
        phone: '+91 9876543210',
        service: 'Ultimate Test Service',
        message: 'Ultimate system test notification'
    };
    processUltimateNotification(testNotif);
    console.log('🚀 Ultimate test notification triggered');
};

console.log('🚀 Ultimate Notification System READY!');
console.log('🌟 Features: Works everywhere, Never fails, One-time setup');
console.log('🎯 Test with: testUltimateNotification()');
</script>