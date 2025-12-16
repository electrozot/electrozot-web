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
    let browserIcon = '🌐';
    
    if (userAgent.includes('chrome') && userAgent.includes('mobile')) {
        browserIcon = '🔵';
        instructions = '1. Tap the menu (⋮) in the top right corner\n2. Select "Add to Home screen"\n3. Tap "Add" to install the app';
    } else if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
        browserIcon = '🔵';
        instructions = '1. Look for the install icon (⊕) in the address bar\n2. Or go to Menu (⋮) > Install ElectroZot\n3. Click "Install" to add to your device';
    } else if (userAgent.includes('firefox')) {
        browserIcon = '🦊';
        instructions = '1. Look for the install prompt notification\n2. Or go to Menu > Install this site as an app\n3. Follow the installation steps';
    } else if (userAgent.includes('safari')) {
        browserIcon = '🧭';
        instructions = '1. Tap the Share button (□↗) at the bottom\n2. Scroll down and tap "Add to Home Screen"\n3. Tap "Add" to install the app';
    } else if (userAgent.includes('edg')) {
        browserIcon = '🔷';
        instructions = '1. Look for the install icon in the address bar\n2. Or go to Menu (⋯) > Apps > Install ElectroZot\n3. Click "Install" to add to your device';
    } else {
        instructions = '1. Look for an "Install" or "Add to Home Screen" option in your browser menu\n2. Follow your browser\'s installation process';
    }
    
    // Create a better mobile-optimized modal
    const guideModal = document.createElement('div');
    guideModal.id = 'pwa-install-modal';
    guideModal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                    background: rgba(0,0,0,0.85); z-index: 999999; display: flex; 
                    align-items: center; justify-content: center; padding: 20px;
                    backdrop-filter: blur(5px); animation: fadeIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                        border-radius: 20px; padding: 0; max-width: 380px; width: 100%; 
                        box-shadow: 0 25px 50px rgba(0,0,0,0.5); 
                        animation: slideUp 0.4s ease; overflow: hidden;">
                
                <!-- Header -->
                <div style="background: rgba(255,255,255,0.1); padding: 25px 30px 20px; 
                            text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <div style="font-size: 3rem; margin-bottom: 10px;">${browserIcon}</div>
                    <h3 style="color: white; margin: 0; font-size: 1.4rem; font-weight: 600;">
                        Install ElectroZot App
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); margin: 8px 0 0; font-size: 0.9rem;">
                        Get quick access and work offline
                    </p>
                </div>
                
                <!-- Instructions -->
                <div style="padding: 25px 30px;">
                    <div style="background: rgba(255,255,255,0.1); border-radius: 12px; 
                                padding: 20px; margin-bottom: 25px;">
                        <h4 style="color: white; margin: 0 0 15px; font-size: 1.1rem;">
                            📋 Installation Steps:
                        </h4>
                        <div style="color: rgba(255,255,255,0.9); line-height: 1.6; 
                                    font-size: 0.95rem; white-space: pre-line;">${instructions}</div>
                    </div>
                    
                    <!-- Buttons -->
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button onclick="document.getElementById('pwa-install-modal').remove()" 
                                style="background: rgba(255,255,255,0.2); color: white; 
                                       border: 2px solid rgba(255,255,255,0.3); padding: 12px 20px; 
                                       border-radius: 25px; cursor: pointer; font-weight: 600;
                                       transition: all 0.3s; font-size: 0.9rem;">
                            Maybe Later
                        </button>
                        <button onclick="document.getElementById('pwa-install-modal').remove()" 
                                style="background: white; color: #667eea; border: none; 
                                       padding: 12px 24px; border-radius: 25px; cursor: pointer; 
                                       font-weight: 600; transition: all 0.3s; font-size: 0.9rem;
                                       box-shadow: 0 4px 15px rgba(255,255,255,0.3);">
                            Got it! 👍
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideUp {
                from { 
                    opacity: 0; 
                    transform: translateY(50px) scale(0.9); 
                }
                to { 
                    opacity: 1; 
                    transform: translateY(0) scale(1); 
                }
            }
            
            #pwa-install-modal button:hover {
                transform: translateY(-2px);
            }
            
            #pwa-install-modal button:active {
                transform: translateY(0) scale(0.95);
            }
        </style>
    `;
    
    document.body.appendChild(guideModal);
    
    // Auto-close after 30 seconds
    setTimeout(() => {
        const modal = document.getElementById('pwa-install-modal');
        if (modal) modal.remove();
    }, 30000);
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
    successMsg.id = 'pwa-success-modal';
    successMsg.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                    background: rgba(0,0,0,0.7); z-index: 999999; display: flex; 
                    align-items: center; justify-content: center; padding: 20px;
                    animation: fadeIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                        border-radius: 20px; padding: 40px 30px; max-width: 350px; width: 100%; 
                        text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.3);
                        animation: bounceIn 0.5s ease;">
                
                <div style="font-size: 4rem; margin-bottom: 20px; animation: bounce 1s ease;">
                    🎉
                </div>
                
                <h3 style="color: white; margin: 0 0 15px; font-size: 1.5rem; font-weight: 600;">
                    App Installed Successfully!
                </h3>
                
                <p style="color: rgba(255,255,255,0.9); margin: 0 0 25px; font-size: 1rem; line-height: 1.5;">
                    ElectroZot is now installed on your device. You can access it from your home screen and use it offline!
                </p>
                
                <button onclick="document.getElementById('pwa-success-modal').remove()" 
                        style="background: white; color: #10b981; border: none; 
                               padding: 15px 30px; border-radius: 25px; cursor: pointer; 
                               font-weight: 600; font-size: 1rem; transition: all 0.3s;
                               box-shadow: 0 4px 15px rgba(255,255,255,0.3);">
                    Awesome! 🚀
                </button>
            </div>
        </div>
        
        <style>
            @keyframes bounceIn {
                0% { 
                    opacity: 0; 
                    transform: scale(0.3) translateY(-50px); 
                }
                50% { 
                    opacity: 1; 
                    transform: scale(1.05) translateY(0); 
                }
                70% { 
                    transform: scale(0.95); 
                }
                100% { 
                    transform: scale(1); 
                }
            }
            
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { 
                    transform: translateY(0); 
                }
                40% { 
                    transform: translateY(-20px); 
                }
                60% { 
                    transform: translateY(-10px); 
                }
            }
            
            #pwa-success-modal button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255,255,255,0.4);
            }
            
            #pwa-success-modal button:active {
                transform: translateY(0) scale(0.95);
            }
        </style>
    `;
    document.body.appendChild(successMsg);
    
    // Auto-close after 5 seconds
    setTimeout(() => {
        const modal = document.getElementById('pwa-success-modal');
        if (modal) modal.remove();
    }, 5000);
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

// Handle navigation button clicks
function handleNavInstallClick() {
    console.log('PWA: Navigation install button clicked');
    
    // Check if already installed
    if (isRunningAsPWA()) {
        showAlreadyInstalledMessage();
        return;
    }
    
    // Try to install
    installPWA();
}

// Show already installed message
function showAlreadyInstalledMessage() {
    const alreadyInstalledMsg = document.createElement('div');
    alreadyInstalledMsg.id = 'pwa-already-installed-modal';
    alreadyInstalledMsg.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                    background: rgba(0,0,0,0.7); z-index: 999999; display: flex; 
                    align-items: center; justify-content: center; padding: 20px;
                    animation: fadeIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); 
                        border-radius: 20px; padding: 35px 30px; max-width: 350px; width: 100%; 
                        text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.3);
                        animation: slideUp 0.4s ease;">
                
                <div style="font-size: 3.5rem; margin-bottom: 20px;">
                    ✅
                </div>
                
                <h3 style="color: white; margin: 0 0 15px; font-size: 1.4rem; font-weight: 600;">
                    Already Installed!
                </h3>
                
                <p style="color: rgba(255,255,255,0.9); margin: 0 0 25px; font-size: 0.95rem; line-height: 1.5;">
                    ElectroZot is already installed on your device. You can find it on your home screen or app drawer.
                </p>
                
                <button onclick="document.getElementById('pwa-already-installed-modal').remove()" 
                        style="background: white; color: #3b82f6; border: none; 
                               padding: 12px 25px; border-radius: 25px; cursor: pointer; 
                               font-weight: 600; font-size: 0.95rem; transition: all 0.3s;
                               box-shadow: 0 4px 15px rgba(255,255,255,0.3);">
                    Got it! 👍
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(alreadyInstalledMsg);
    
    // Auto-close after 4 seconds
    setTimeout(() => {
        const modal = document.getElementById('pwa-already-installed-modal');
        if (modal) modal.remove();
    }, 4000);
}

// Initialize navigation button handlers when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle desktop navigation button
    const navInstallBtn = document.getElementById('pwa-install-nav-btn');
    if (navInstallBtn) {
        navInstallBtn.addEventListener('click', handleNavInstallClick);
    }
    
    // Handle mobile navigation button
    const mobileInstallBtn = document.getElementById('pwa-install-mobile-btn');
    if (mobileInstallBtn) {
        mobileInstallBtn.addEventListener('click', handleNavInstallClick);
    }
    
    // Update button states if already installed
    if (isRunningAsPWA()) {
        if (navInstallBtn) {
            navInstallBtn.innerHTML = '<i class="fas fa-check-circle"></i> Installed';
            navInstallBtn.style.background = 'rgba(76, 175, 80, 0.3)';
            navInstallBtn.style.borderColor = 'rgba(76, 175, 80, 0.5)';
        }
        if (mobileInstallBtn) {
            mobileInstallBtn.innerHTML = '<i class="fas fa-check-circle" style="font-size: 0.65rem; margin-bottom: 1px;"></i><span style="font-weight: 600; font-size: 0.5rem;">Installed</span>';
            mobileInstallBtn.style.background = 'rgba(76, 175, 80, 0.3)';
        }
    }
});

// Export functions for external use
window.PWAInstaller = {
    install: installPWA,
    dismiss: dismissInstallPromotion,
    isInstalled: isRunningAsPWA,
    handleNavClick: handleNavInstallClick
};
