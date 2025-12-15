<!-- Brilliant Notification System - Simple, Reliable, Works Everywhere -->
<script>
console.log('🌟 Brilliant Notification System v1.0 - Loading...');

// === CONFIGURATION ===
const TECH_ID = <?php echo $_SESSION['t_id']; ?>;
const SYSTEM_ENABLED_KEY = `brilliantNotif_${TECH_ID}`;
const CHECK_INTERVAL = 5000; // 5 seconds

// === STATE ===
let systemEnabled = false;
let audioReady = false;
let notificationReady = false;
let checkInterval = null;
let audioElement = null;

// === DEVICE DETECTION ===
const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

console.log(`📱 Device: ${isMobile ? 'Mobile' : 'Desktop'} ${isIOS ? '(iOS)' : ''}`);

// === INITIALIZATION ===
function initBrilliantSystem() {
    console.log('🚀 Initializing Brilliant System...');
    
    // Check if already enabled
    const enabled = localStorage.getItem(SYSTEM_ENABLED_KEY);
    if (enabled === 'true') {
        console.log('✅ System already enabled - activating...');
        activateSystem();
    } else {
        console.log('⚠️ System not enabled - will show prompt on interaction');
        setupInteractionCapture();
    }
    
    // Always start checking (works even if not enabled)
    startNotificationChecking();
    
    console.log('✅ Brilliant System initialized');
}

// === SYSTEM ACTIVATION ===
function activateSystem() {
    console.log('🔥 Activating Brilliant System...');
    
    systemEnabled = true;
    setupAudio();
    setupNotifications();
    registerServiceWorker();
    
    console.log('🌟 Brilliant System ACTIVE - ready for all notifications!');
}

// === AUDIO SETUP ===
function setupAudio() {
    try {
        audioElement = new Audio('../admin/vendor/sounds/arived.mp3');
        audioElement.volume = 1.0;
        audioElement.preload = 'auto';
        
        if (isMobile) {
            audioElement.setAttribute('playsinline', true);
        }
        
        audioReady = true;
        console.log('🔊 Audio system ready');
    } catch (error) {
        console.error('❌ Audio setup failed:', error);
    }
}

// === NOTIFICATION SETUP ===
function setupNotifications() {
    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            notificationReady = true;
            console.log('🔔 Notifications ready');
        } else if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                notificationReady = (permission === 'granted');
                console.log(`🔔 Notification permission: ${permission}`);
            });
        }
    }
}

// === SERVICE WORKER FOR BACKGROUND ===
function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('brilliant-service-worker.js')
            .then(registration => {
                console.log('🛡️ Service Worker registered for background notifications');
            })
            .catch(error => {
                console.log('⚠️ Service Worker registration failed:', error);
            });
    }
}

// === INTERACTION CAPTURE ===
function setupInteractionCapture() {
    const events = ['touchstart', 'click', 'keydown', 'scroll'];
    
    function handleInteraction() {
        console.log('👆 User interaction detected - showing enable prompt');
        showEnablePrompt();
        
        // Remove listeners
        events.forEach(event => {
            document.removeEventListener(event, handleInteraction);
        });
    }
    
    events.forEach(event => {
        document.addEventListener(event, handleInteraction, { passive: true, once: true });
    });
}

// === ENABLE PROMPT ===
function showEnablePrompt() {
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.9); z-index: 999999; 
        display: flex; align-items: center; justify-content: center; padding: 20px;
    `;
    
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: white; padding: 30px; border-radius: 20px; text-align: center; 
        max-width: 400px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    `;
    
    modal.innerHTML = `
        <div style="font-size: 60px; margin-bottom: 20px;">🌟📱</div>
        <h2 style="margin: 0 0 15px 0; color: #1f2937;">Enable Brilliant Alerts</h2>
        <p style="margin: 0 0 25px 0; color: #6b7280; line-height: 1.5;">
            Get instant sound + visual alerts for ALL booking events.<br>
            <strong style="color: #10b981;">✨ Works everywhere - locked screen, background, dashboard!</strong><br>
            <strong style="color: #ef4444;">🔒 One-time setup - enabled FOREVER!</strong>
        </p>
        <button id="enableBtn" style="
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; border: none; padding: 18px 35px; border-radius: 25px; 
            font-weight: bold; cursor: pointer; margin-right: 15px; font-size: 16px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        ">🌟 Enable Forever</button>
        <button id="skipBtn" style="
            background: #6b7280; color: white; border: none; padding: 18px 35px; 
            border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 16px;
        ">Skip</button>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Enable button
    document.getElementById('enableBtn').onclick = function() {
        this.innerHTML = '🔄 Enabling...';
        this.disabled = true;
        
        enableBrilliantSystem().then(() => {
            overlay.remove();
            showMessage('🌟 Brilliant Alerts enabled FOREVER! You will never miss a notification.', 'success');
        }).catch(error => {
            this.innerHTML = '🌟 Enable Forever';
            this.disabled = false;
            showMessage('⚠️ Please allow notifications and sound', 'warning');
        });
    };
    
    // Skip button
    document.getElementById('skipBtn').onclick = function() {
        overlay.remove();
        localStorage.setItem(SYSTEM_ENABLED_KEY, 'skipped');
    };
}

// === ENABLE SYSTEM ===
function enableBrilliantSystem() {
    return new Promise((resolve, reject) => {
        console.log('🌟 Enabling Brilliant System...');
        
        // Test audio
        if (audioElement) {
            audioElement.play().then(() => {
                audioElement.pause();
                audioElement.currentTime = 0;
                audioReady = true;
                console.log('✅ Audio unlocked');
                
                // Request notifications
                if ('Notification' in window) {
                    Notification.requestPermission().then(permission => {
                        notificationReady = (permission === 'granted');
                        console.log(`✅ Notifications: ${permission}`);
                        
                        // Save enabled state
                        localStorage.setItem(SYSTEM_ENABLED_KEY, 'true');
                        systemEnabled = true;
                        
                        // Activate full system
                        registerServiceWorker();
                        
                        console.log('🌟 Brilliant System ENABLED FOREVER!');
                        resolve();
                    });
                } else {
                    // No notifications but audio works
                    localStorage.setItem(SYSTEM_ENABLED_KEY, 'true');
                    systemEnabled = true;
                    console.log('🌟 Brilliant System enabled (audio only)');
                    resolve();
                }
            }).catch(error => {
                console.error('❌ Audio unlock failed:', error);
                reject(error);
            });
        } else {
            reject(new Error('Audio not available'));
        }
    });
}

// === NOTIFICATION CHECKING ===
function startNotificationChecking() {
    console.log('🔍 Starting notification checking...');
    
    // Clear existing interval
    if (checkInterval) clearInterval(checkInterval);
    
    // Check every 5 seconds
    checkInterval = setInterval(checkForNotifications, CHECK_INTERVAL);
    
    // Initial check
    setTimeout(checkForNotifications, 2000);
}

function checkForNotifications() {
    fetch('check-technician-notifications.php', {
        method: 'GET',
        cache: 'no-cache',
        credentials: 'include'
    })
    .then(response => response.text())
    .then(rawText => {
        const data = JSON.parse(rawText.trim().replace(/^\uFEFF/, ''));
        
        if (data.error) {
            console.error('❌ API error:', data.error);
            return;
        }
        
        // Update badge count
        updateNotificationBadge(data.notification_count);
        
        // Handle new notifications
        if (data.has_notifications && data.new_count > 0) {
            console.log(`🎉 NEW NOTIFICATIONS: ${data.new_count}`);
            handleBrilliantNotification(data.notifications);
        }
    })
    .catch(error => {
        console.error('❌ Notification check failed:', error);
    });
}

// === BRILLIANT NOTIFICATION HANDLER ===
function handleBrilliantNotification(notifications) {
    console.log(`🌟 Handling ${notifications.length} brilliant notifications`);
    
    if (!systemEnabled) {
        console.log('⚠️ System not enabled - notifications will be silent');
        return;
    }
    
    notifications.forEach((notification, index) => {
        // Delay each notification slightly to prevent conflicts
        setTimeout(() => {
            playBrilliantSound();
            showBrilliantNotification(notification);
            showBrilliantVisual(notification);
            triggerVibration();
        }, index * 200);
    });
}

// === SOUND ALERT ===
function playBrilliantSound() {
    if (!audioReady || !audioElement) return;
    
    try {
        audioElement.currentTime = 0;
        audioElement.volume = 1.0;
        
        audioElement.play().then(() => {
            console.log('🔊 Brilliant sound played');
            setTimeout(() => {
                audioElement.pause();
                audioElement.currentTime = 0;
            }, 3000);
        }).catch(error => {
            console.log('⚠️ Sound play failed:', error);
        });
    } catch (error) {
        console.log('⚠️ Sound error:', error);
    }
}

// === BROWSER NOTIFICATION (WORKS WHEN LOCKED) ===
function showBrilliantNotification(notification) {
    if (!notificationReady) return;
    
    try {
        const browserNotif = new Notification('🌟 New Booking Alert!', {
            body: `📋 Booking #${notification.id}\n🔧 ${notification.service}\n👤 ${notification.customer}\n📞 ${notification.phone}\n✨ ${notification.message}`,
            icon: '../admin/vendor/img/logo.png',
            badge: '../admin/vendor/img/logo.png',
            tag: `booking-${notification.id}`,
            requireInteraction: true,
            silent: false,
            vibrate: [500, 200, 500, 200, 500]
        });
        
        browserNotif.onclick = function() {
            window.focus();
            this.close();
        };
        
        setTimeout(() => browserNotif.close(), 15000);
        
        console.log('🔔 Browser notification shown');
    } catch (error) {
        console.error('❌ Browser notification failed:', error);
    }
}

// === VISUAL NOTIFICATION (WHEN PAGE VISIBLE) ===
function showBrilliantVisual(notification) {
    if (document.hidden) return;
    
    // Remove existing visuals
    document.querySelectorAll('.brilliant-toast').forEach(el => el.remove());
    
    const toast = document.createElement('div');
    toast.className = 'brilliant-toast';
    toast.style.cssText = `
        position: fixed; top: 80px; left: 20px; right: 20px; z-index: 999999;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white; padding: 20px; border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.4);
        animation: slideDown 0.5s ease-out;
    `;
    
    toast.innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <i class="fas fa-star" style="font-size: 28px; margin-right: 15px; animation: spin 2s linear infinite;"></i>
            <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 18px;">🌟 New Booking Alert!</h3>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer;">×</button>
        </div>
        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px;">
            <div style="font-size: 16px; font-weight: bold; margin-bottom: 8px;">📋 Booking #${notification.id}</div>
            <div style="font-size: 14px; line-height: 1.4;">
                👤 ${notification.customer}<br>
                📞 ${notification.phone}<br>
                🔧 ${notification.service}<br>
                ✨ ${notification.message}
            </div>
        </div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="dashboard.php" style="background: white; color: #10b981; padding: 12px 25px; 
               border-radius: 25px; text-decoration: none; font-weight: bold;">View Dashboard</a>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remove after 20 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }
    }, 20000);
    
    console.log('👁️ Visual notification shown');
}

// === VIBRATION ===
function triggerVibration() {
    if (isMobile && navigator.vibrate) {
        navigator.vibrate([500, 200, 500, 200, 500]);
        console.log('📳 Vibration triggered');
    }
}

// === UTILITY FUNCTIONS ===
function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationCount');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

function showMessage(text, type) {
    const msg = document.createElement('div');
    const bgColor = type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#ef4444';
    msg.style.cssText = `
        position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
        background: ${bgColor}; color: white; padding: 15px 25px; border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 999999; font-weight: bold;
    `;
    msg.textContent = text;
    document.body.appendChild(msg);
    
    setTimeout(() => {
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 500);
    }, 4000);
}

// === CSS ANIMATIONS ===
if (!document.getElementById('brilliant-styles')) {
    const style = document.createElement('style');
    style.id = 'brilliant-styles';
    style.textContent = `
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .brilliant-toast {
            transition: all 0.5s ease;
        }
    `;
    document.head.appendChild(style);
}

// === INITIALIZATION ===
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBrilliantSystem);
} else {
    initBrilliantSystem();
}

// === EXPOSE GLOBAL FUNCTION ===
window.testBrilliantNotification = function() {
    const testNotification = {
        id: 'TEST' + Date.now(),
        customer: 'Test Customer',
        phone: '+91 9876543210',
        service: 'Test Service',
        message: 'This is a test notification'
    };
    handleBrilliantNotification([testNotification]);
};

console.log('🌟 Brilliant Notification System Ready!');
console.log('📱 Works everywhere: Dashboard, Background, Locked Screen');
console.log('🔒 One-time enable = Forever enabled');
console.log('🎯 Test with: testBrilliantNotification()');
</script>