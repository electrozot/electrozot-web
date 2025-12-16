/**
 * PWA Installation Handler
 * Handles app installation prompt and provides user-friendly install experience
 */

let deferredPrompt;
let installButton;
let installPromptShown = false;

// Listen for the beforeinstallprompt event
window.addEventListener('beforeinstallprompt', (e) => {
    console.log('PWA: beforeinstallprompt event fired');
    
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    window.pwaInstallEvent = e; // Store globally for access
    
    // Show install button/banner
    showInstallPromotion();
    
    // Notify any waiting install buttons
    const installButtons = document.querySelectorAll('[data-pwa-install]');
    installButtons.forEach(btn => {
        btn.style.display = 'inline-block';
        btn.disabled = false;
    });
});

// Show install promotion UI - Only show if main section is not visible
function showInstallPromotion() {
    // Check if main PWA section exists and is visible
    const mainPwaSection = document.querySelector('.pwa-install-section');
    if (mainPwaSection) {

        return; // Don't show banner if main section exists
    }
    
    // Create install banner if it doesn't exist and main section is not present
    if (!document.getElementById('pwa-install-banner')) {
        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.innerHTML = `
            <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); 
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                        color: white; padding: 15px 25px; border-radius: 50px; 
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; 
                        display: flex; align-items: center; gap: 15px; 
                        animation: slideUp 0.5s ease-out; max-width: 90%; width: auto;">
                <style>
                    @keyframes slideUp {
                        from { transform: translateX(-50%) translateY(100px); opacity: 0; }
                        to { transform: translateX(-50%) translateY(0); opacity: 1; }
                    }
                    @media (max-width: 576px) {
                        #pwa-install-banner > div {
                            flex-direction: column !important;
                            text-align: center;
                            padding: 12px 20px !important;
                        }
                        #pwa-install-banner button {
                            width: 100%;
                            margin-top: 8px;
                        }
                    }
                </style>
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 5px;">📱 Install ElectroZot App</strong>
                    <small style="opacity: 0.9;">Get quick access and work offline</small>
                </div>
                <button id="pwa-install-btn" style="background: white; color: #667eea; 
                        border: none; padding: 10px 20px; border-radius: 25px; 
                        font-weight: bold; cursor: pointer; transition: all 0.3s;">
                    Install
                </button>
                <button id="pwa-dismiss-btn" style="background: transparent; color: white; 
                        border: 2px solid white; padding: 10px 20px; border-radius: 25px; 
                        font-weight: bold; cursor: pointer; transition: all 0.3s;">
                    Later
                </button>
            </div>
        `;
        document.body.appendChild(banner);
        
        // Add event listeners
        document.getElementById('pwa-install-btn').addEventListener('click', installPWA);
        document.getElementById('pwa-dismiss-btn').addEventListener('click', dismissInstallPromotion);
    }
}

// Install PWA - Direct installation
async function installPWA() {
    console.log('PWA: Install function called');
    
    if (!deferredPrompt) {
        console.log('PWA: No deferred prompt available');
        // Try to use global install event if available
        if (window.pwaInstallEvent) {
            deferredPrompt = window.pwaInstallEvent;
        } else {
            console.log('PWA: No install event available, showing manual instructions');
            showManualInstallGuide();
            return;
        }
    }
    
    try {
        console.log('PWA: Triggering install prompt');
        await deferredPrompt.prompt();
        
        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;
        console.log('PWA: User choice:', outcome);
        
        if (outcome === 'accepted') {
            console.log('PWA: Installation accepted');
            showInstallSuccess();
        } else {
            console.log('PWA: Installation dismissed');
        }
        
        // Clear the deferredPrompt
        deferredPrompt = null;
        window.pwaInstallEvent = null;
        
        // Hide the install promotion
        dismissInstallPromotion();
    } catch (error) {
        console.error('PWA: Install error:', error);
        showManualInstallGuide();
    }
}

// Show manual install guide when automatic install fails
function showManualInstallGuide() {
    const userAgent = navigator.userAgent.toLowerCase();
    let instructions = '';
    
    if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
        instructions = '1. Look for the install icon (⊕) in the address bar\n2. Or go to Menu (⋮) > Install ElectroZot\n3. Click "Install" to add to your device';
    } else if (userAgent.includes('firefox')) {
        instructions = '1. Look for the install prompt notification\n2. Or go to Menu > Install this site as an app\n3. Follow the installation steps';
    } else if (userAgent.includes('safari')) {
        instructions = '1. Tap the Share button (□↗)\n2. Scroll down and tap "Add to Home Screen"\n3. Tap "Add" to install the app';
    } else if (userAgent.includes('edg')) {
        instructions = '1. Look for the install icon in the address bar\n2. Or go to Menu (⋯) > Apps > Install ElectroZot\n3. Click "Install" to add to your device';
    } else {
        instructions = '1. Look for an "Install" or "Add to Home Screen" option in your browser menu\n2. Follow your browser\'s installation process';
    }
    
    // Create a better modal-style guide
    const guideModal = document.createElement('div');
    guideModal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                    background: rgba(0,0,0,0.8); z-index: 99999; display: flex; 
                    align-items: center; justify-content: center; padding: 20px;">
            <div style="background: white; border-radius: 15px; padding: 30px; 
                        max-width: 400px; width: 100%; text-align: center; 
                        box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
                <h3 style="color: #333; margin-bottom: 20px;">📱 Install ElectroZot App</h3>
                <p style="color: #666; margin-bottom: 20px; white-space: pre-line; text-align: left;">${instructions}</p>
                <button onclick="this.closest('div').parentElement.remove()" 
                        style="background: #667eea; color: white; border: none; 
                               padding: 12px 24px; border-radius: 25px; cursor: pointer; 
                               font-weight: bold;">Got it!</button>
            </div>
        </div>
    `;
    document.body.appendChild(guideModal);
}

// Dismiss install promotion
function dismissInstallPromotion() {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) {
        banner.style.animation = 'slideDown 0.3s ease-out';
        setTimeout(() => banner.remove(), 300);
    }
}

// Show install success message
function showInstallSuccess() {
    const successMsg = document.createElement('div');
    successMsg.innerHTML = `
        <div style="position: fixed; top: 20px; right: 20px; 
                    background: #10b981; color: white; padding: 15px 25px; 
                    border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
                    z-index: 10000; animation: fadeIn 0.3s ease-out;">
            <strong>✅ App Installed!</strong>
            <p style="margin: 5px 0 0 0; font-size: 14px;">You can now use ElectroZot offline</p>
        </div>
    `;
    document.body.appendChild(successMsg);
    
    setTimeout(() => {
        successMsg.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => successMsg.remove(), 300);
    }, 3000);
}

// Detect if app is already installed
window.addEventListener('appinstalled', () => {

    dismissInstallPromotion();
    
    // Track installation (optional analytics)
    if (typeof gtag !== 'undefined') {
        gtag('event', 'pwa_install', {
            event_category: 'engagement',
            event_label: 'PWA Installation'
        });
    }
});

// Check if running as installed PWA
function isRunningAsPWA() {
    return window.matchMedia('(display-mode: standalone)').matches || 
           window.navigator.standalone === true;
}

// Show different UI if running as PWA
if (isRunningAsPWA()) {

    document.body.classList.add('pwa-mode');
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from { transform: translateX(-50%) translateY(0); opacity: 1; }
        to { transform: translateX(-50%) translateY(100px); opacity: 0; }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-20px); }
    }
    
    /* PWA mode specific styles */
    .pwa-mode {
        /* Add any PWA-specific styling here */
    }
    
    /* Install button hover effects */
    #pwa-install-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    #pwa-dismiss-btn:hover {
        background: rgba(255,255,255,0.1);
    }
`;
document.head.appendChild(style);

// Export functions for external use
window.PWAInstaller = {
    install: installPWA,
    dismiss: dismissInstallPromotion,
    isInstalled: isRunningAsPWA
};
