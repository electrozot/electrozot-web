/**
 * PWA Update Notification Handler
 * Notifies users when a new version of the app is available
 */

let newWorker;

// Check for service worker updates
if ('serviceWorker' in navigator) {
    // Use relative path to work in any directory
    const swPath = new URL('./sw.js', document.baseURI).pathname;
    navigator.serviceWorker.register(swPath).then((registration) => {
        console.log('✅ Service Worker registered');
        
        // Check for updates every hour
        setInterval(() => {
            registration.update();
        }, 60 * 60 * 1000);
        
        // Listen for updates
        registration.addEventListener('updatefound', () => {
            newWorker = registration.installing;
            console.log('🔄 New Service Worker installing...');
            
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    // New service worker available
                    console.log('✨ New version available!');
                    showUpdateNotification();
                }
            });
        });
    });
    
    // Handle controller change (when new SW takes over)
    let refreshing = false;
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (!refreshing) {
            refreshing = true;
            window.location.reload();
        }
    });
}

// Show update notification
function showUpdateNotification() {
    // Remove existing notification if any
    const existing = document.getElementById('pwa-update-notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.id = 'pwa-update-notification';
    notification.innerHTML = `
        <div style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); 
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                    color: white; padding: 15px 25px; border-radius: 50px; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; 
                    display: flex; align-items: center; gap: 15px; 
                    animation: slideDown 0.5s ease-out; max-width: 90%; width: auto;">
            <style>
                @keyframes slideDown {
                    from { transform: translateX(-50%) translateY(-100px); opacity: 0; }
                    to { transform: translateX(-50%) translateY(0); opacity: 1; }
                }
                @keyframes slideUp {
                    from { transform: translateX(-50%) translateY(0); opacity: 1; }
                    to { transform: translateX(-50%) translateY(-100px); opacity: 0; }
                }
                @media (max-width: 576px) {
                    #pwa-update-notification > div {
                        flex-direction: column !important;
                        text-align: center;
                        padding: 12px 20px !important;
                    }
                    #pwa-update-notification button {
                        width: 100%;
                        margin-top: 8px;
                    }
                }
            </style>
            <div style="flex: 1;">
                <strong style="display: block; margin-bottom: 5px;">🎉 New Version Available!</strong>
                <small style="opacity: 0.9;">Update now to get the latest features</small>
            </div>
            <button id="pwa-update-btn" style="background: white; color: #10b981; 
                    border: none; padding: 10px 20px; border-radius: 25px; 
                    font-weight: bold; cursor: pointer; transition: all 0.3s;">
                Update Now
            </button>
            <button id="pwa-update-dismiss-btn" style="background: transparent; color: white; 
                    border: 2px solid white; padding: 10px 20px; border-radius: 25px; 
                    font-weight: bold; cursor: pointer; transition: all 0.3s;">
                Later
            </button>
        </div>
    `;
    document.body.appendChild(notification);
    
    // Add event listeners
    document.getElementById('pwa-update-btn').addEventListener('click', updatePWA);
    document.getElementById('pwa-update-dismiss-btn').addEventListener('click', dismissUpdateNotification);
    
    // Add hover effects
    const updateBtn = document.getElementById('pwa-update-btn');
    const dismissBtn = document.getElementById('pwa-update-dismiss-btn');
    
    updateBtn.addEventListener('mouseenter', () => {
        updateBtn.style.transform = 'scale(1.05)';
        updateBtn.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    });
    
    updateBtn.addEventListener('mouseleave', () => {
        updateBtn.style.transform = 'scale(1)';
        updateBtn.style.boxShadow = 'none';
    });
    
    dismissBtn.addEventListener('mouseenter', () => {
        dismissBtn.style.background = 'rgba(255,255,255,0.1)';
    });
    
    dismissBtn.addEventListener('mouseleave', () => {
        dismissBtn.style.background = 'transparent';
    });
}

// Update PWA
function updatePWA() {
    if (newWorker) {
        // Tell the new service worker to skip waiting
        newWorker.postMessage({ type: 'SKIP_WAITING' });
    }
    
    // Show loading indicator
    const notification = document.getElementById('pwa-update-notification');
    if (notification) {
        notification.innerHTML = `
            <div style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); 
                        background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                        color: white; padding: 15px 25px; border-radius: 50px; 
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; 
                        text-align: center;">
                <strong>🔄 Updating...</strong>
            </div>
        `;
    }
}

// Dismiss update notification
function dismissUpdateNotification() {
    const notification = document.getElementById('pwa-update-notification');
    if (notification) {
        notification.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }
}

// Listen for skip waiting message in service worker
if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
    navigator.serviceWorker.addEventListener('message', (event) => {
        if (event.data && event.data.type === 'RELOAD_PAGE') {
            window.location.reload();
        }
    });
}

// Export functions
window.PWAUpdater = {
    update: updatePWA,
    dismiss: dismissUpdateNotification
};
