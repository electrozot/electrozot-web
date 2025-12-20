/**
 * Mobile PWA Installation Fix
 * Enhanced mobile-specific PWA installation handler
 */

(function() {
    'use strict';
    
    let mobileInstallPrompt = null;
    let isIOS = false;
    let isAndroid = false;
    let isMobile = false;
    
    // Detect mobile platform
    function detectMobilePlatform() {
        const userAgent = navigator.userAgent.toLowerCase();
        isIOS = /iphone|ipad|ipod/.test(userAgent);
        isAndroid = /android/.test(userAgent);
        isMobile = isIOS || isAndroid || /mobile/.test(userAgent);
        
        console.log('Mobile PWA: Platform detection -', {
            isIOS, isAndroid, isMobile, userAgent: userAgent.substring(0, 50)
        });
        
        return { isIOS, isAndroid, isMobile };
    }
    
    // Enhanced beforeinstallprompt handler for mobile
    function handleBeforeInstallPrompt(e) {
        console.log('Mobile PWA: beforeinstallprompt event fired');
        
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        
        // Store the event for later use
        mobileInstallPrompt = e;
        window.mobileInstallEvent = e;
        
        // Show mobile-specific install UI
        if (isMobile) {
            showMobileInstallBanner();
        }
        
        // Enable install buttons
        enableInstallButtons();
    }
    
    // Show mobile-optimized install banner
    function showMobileInstallBanner() {
        // Don't show if already exists or if main PWA section is visible
        if (document.getElementById('mobile-pwa-banner') || 
            document.querySelector('.pwa-install-section')) {
            return;
        }
        
        const banner = document.createElement('div');
        banner.id = 'mobile-pwa-banner';
        banner.innerHTML = `
            <div style="position: fixed; bottom: 0; left: 0; right: 0; 
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                        color: white; padding: 15px 20px; z-index: 99999; 
                        box-shadow: 0 -5px 20px rgba(0,0,0,0.3);
                        animation: slideUpMobile 0.5s ease-out;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; 
                            max-width: 100%; gap: 15px;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 3px;">
                            📱 Install ElectroZot App
                        </div>
                        <div style="font-size: 0.8rem; opacity: 0.9; line-height: 1.3;">
                            Quick access & offline use
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; flex-shrink: 0;">
                        <button id="mobile-install-btn" 
                                style="background: white; color: #667eea; border: none; 
                                       padding: 10px 16px; border-radius: 20px; font-weight: 600; 
                                       font-size: 0.85rem; cursor: pointer; transition: all 0.3s;
                                       white-space: nowrap;">
                            Install
                        </button>
                        <button id="mobile-dismiss-btn" 
                                style="background: transparent; color: white; border: 2px solid white; 
                                       padding: 8px 14px; border-radius: 20px; font-weight: 600; 
                                       font-size: 0.85rem; cursor: pointer; transition: all 0.3s;
                                       white-space: nowrap;">
                            Later
                        </button>
                    </div>
                </div>
                
                <style>
                    @keyframes slideUpMobile {
                        from { transform: translateY(100%); opacity: 0; }
                        to { transform: translateY(0); opacity: 1; }
                    }
                    
                    @keyframes slideDownMobile {
                        from { transform: translateY(0); opacity: 1; }
                        to { transform: translateY(100%); opacity: 0; }
                    }
                    
                    #mobile-install-btn:active {
                        transform: scale(0.95);
                    }
                    
                    #mobile-dismiss-btn:active {
                        transform: scale(0.95);
                        background: rgba(255,255,255,0.1);
                    }
                </style>
            </div>
        `;
        
        document.body.appendChild(banner);
        
        // Add event listeners
        document.getElementById('mobile-install-btn').addEventListener('click', handleMobileInstall);
        document.getElementById('mobile-dismiss-btn').addEventListener('click', dismissMobileBanner);
        
        // Auto-dismiss after 15 seconds on mobile
        setTimeout(() => {
            dismissMobileBanner();
        }, 15000);
    }
    
    // Handle mobile installation
    async function handleMobileInstall() {
        console.log('Mobile PWA: Install button clicked');
        
        if (mobileInstallPrompt) {
            try {
                console.log('Mobile PWA: Triggering install prompt');
                await mobileInstallPrompt.prompt();
                
                const { outcome } = await mobileInstallPrompt.userChoice;
                console.log('Mobile PWA: User choice:', outcome);
                
                if (outcome === 'accepted') {
                    showMobileInstallSuccess();
                } else {
                    console.log('Mobile PWA: Installation declined');
                }
                
                mobileInstallPrompt = null;
                dismissMobileBanner();
                
            } catch (error) {
                console.error('Mobile PWA: Install error:', error);
                showMobileInstallGuide();
            }
        } else {
            console.log('Mobile PWA: No install prompt available, showing manual guide');
            showMobileInstallGuide();
        }
    }
    
    // Show mobile-specific install guide
    function showMobileInstallGuide() {
        const platform = detectMobilePlatform();
        let instructions = '';
        let browserIcon = '📱';
        
        if (platform.isIOS) {
            const isSafari = /safari/.test(navigator.userAgent.toLowerCase()) && 
                           !/chrome|crios|fxios/.test(navigator.userAgent.toLowerCase());
            
            if (isSafari) {
                browserIcon = '🧭';
                instructions = `1. Tap the Share button (□↗) at the bottom of Safari
2. Scroll down and tap "Add to Home Screen"
3. Tap "Add" to install ElectroZot app
4. Find the app icon on your home screen`;
            } else {
                browserIcon = '📱';
                instructions = `For best experience on iOS:
1. Open this site in Safari browser
2. Tap Share (□↗) → "Add to Home Screen"
3. Or look for install option in your current browser`;
            }
        } else if (platform.isAndroid) {
            const isChrome = /chrome/.test(navigator.userAgent.toLowerCase());
            
            if (isChrome) {
                browserIcon = '🔵';
                instructions = `1. Tap the menu (⋮) in the top right corner
2. Select "Add to Home screen" or "Install app"
3. Tap "Add" or "Install" to confirm
4. Find ElectroZot app on your home screen`;
            } else {
                browserIcon = '🤖';
                instructions = `1. Look for "Add to Home screen" in your browser menu
2. Or try opening this site in Chrome browser
3. Chrome offers the best PWA installation experience`;
            }
        } else {
            instructions = `1. Look for "Install" or "Add to Home Screen" option
2. Check your browser's menu for app installation
3. Follow your browser's installation process`;
        }
        
        const modal = document.createElement('div');
        modal.id = 'mobile-install-guide';
        modal.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                        background: rgba(0,0,0,0.9); z-index: 999999; 
                        display: flex; align-items: center; justify-content: center; 
                        padding: 20px; animation: fadeInMobile 0.3s ease;">
                
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                            border-radius: 20px; max-width: 350px; width: 100%; 
                            overflow: hidden; animation: slideUpModal 0.4s ease;">
                    
                    <!-- Header -->
                    <div style="background: rgba(255,255,255,0.1); padding: 25px 20px; 
                                text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2);">
                        <div style="font-size: 3rem; margin-bottom: 10px;">${browserIcon}</div>
                        <h3 style="color: white; margin: 0; font-size: 1.3rem; font-weight: 600;">
                            Install ElectroZot App
                        </h3>
                        <p style="color: rgba(255,255,255,0.8); margin: 8px 0 0; font-size: 0.85rem;">
                            Get quick access and work offline
                        </p>
                    </div>
                    
                    <!-- Instructions -->
                    <div style="padding: 20px;">
                        <div style="background: rgba(255,255,255,0.1); border-radius: 12px; 
                                    padding: 18px; margin-bottom: 20px;">
                            <h4 style="color: white; margin: 0 0 12px; font-size: 1rem;">
                                📋 Installation Steps:
                            </h4>
                            <div style="color: rgba(255,255,255,0.9); line-height: 1.5; 
                                        font-size: 0.85rem; white-space: pre-line;">${instructions}</div>
                        </div>
                        
                        <!-- Buttons -->
                        <div style="display: flex; gap: 10px;">
                            <button onclick="document.getElementById('mobile-install-guide').remove()" 
                                    style="flex: 1; background: rgba(255,255,255,0.2); color: white; 
                                           border: 2px solid rgba(255,255,255,0.3); padding: 12px; 
                                           border-radius: 20px; cursor: pointer; font-weight: 600;
                                           font-size: 0.85rem; transition: all 0.3s;">
                                Maybe Later
                            </button>
                            <button onclick="document.getElementById('mobile-install-guide').remove()" 
                                    style="flex: 1; background: white; color: #667eea; border: none; 
                                           padding: 12px; border-radius: 20px; cursor: pointer; 
                                           font-weight: 600; font-size: 0.85rem; transition: all 0.3s;">
                                Got it! 👍
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>
                @keyframes fadeInMobile {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                @keyframes slideUpModal {
                    from { 
                        opacity: 0; 
                        transform: translateY(50px) scale(0.9); 
                    }
                    to { 
                        opacity: 1; 
                        transform: translateY(0) scale(1); 
                    }
                }
            </style>
        `;
        
        document.body.appendChild(modal);
        
        // Auto-close after 30 seconds
        setTimeout(() => {
            const guide = document.getElementById('mobile-install-guide');
            if (guide) guide.remove();
        }, 30000);
    }
    
    // Show mobile install success
    function showMobileInstallSuccess() {
        const success = document.createElement('div');
        success.id = 'mobile-install-success';
        success.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                        background: rgba(0,0,0,0.8); z-index: 999999; 
                        display: flex; align-items: center; justify-content: center; 
                        padding: 20px; animation: fadeInMobile 0.3s ease;">
                
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                            border-radius: 20px; padding: 30px 25px; max-width: 320px; width: 100%; 
                            text-align: center; animation: bounceInMobile 0.5s ease;">
                    
                    <div style="font-size: 3.5rem; margin-bottom: 15px; animation: bounceMobile 1s ease;">
                        🎉
                    </div>
                    
                    <h3 style="color: white; margin: 0 0 12px; font-size: 1.3rem; font-weight: 600;">
                        App Installed!
                    </h3>
                    
                    <p style="color: rgba(255,255,255,0.9); margin: 0 0 20px; font-size: 0.9rem; line-height: 1.4;">
                        ElectroZot is now on your home screen. Access it anytime, even offline!
                    </p>
                    
                    <button onclick="document.getElementById('mobile-install-success').remove()" 
                            style="background: white; color: #10b981; border: none; 
                                   padding: 12px 25px; border-radius: 20px; cursor: pointer; 
                                   font-weight: 600; font-size: 0.9rem; transition: all 0.3s;">
                        Awesome! 🚀
                    </button>
                </div>
            </div>
            
            <style>
                @keyframes bounceInMobile {
                    0% { opacity: 0; transform: scale(0.3) translateY(-50px); }
                    50% { opacity: 1; transform: scale(1.05) translateY(0); }
                    70% { transform: scale(0.95); }
                    100% { transform: scale(1); }
                }
                
                @keyframes bounceMobile {
                    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                    40% { transform: translateY(-15px); }
                    60% { transform: translateY(-8px); }
                }
            </style>
        `;
        
        document.body.appendChild(success);
        
        // Auto-close after 4 seconds
        setTimeout(() => {
            const successEl = document.getElementById('mobile-install-success');
            if (successEl) successEl.remove();
        }, 4000);
    }
    
    // Dismiss mobile banner
    function dismissMobileBanner() {
        const banner = document.getElementById('mobile-pwa-banner');
        if (banner) {
            banner.style.animation = 'slideDownMobile 0.3s ease-out';
            setTimeout(() => banner.remove(), 300);
        }
    }
    
    // Enable install buttons
    function enableInstallButtons() {
        const buttons = document.querySelectorAll('[data-pwa-install], #pwa-install-nav-btn, #pwa-install-mobile-btn');
        buttons.forEach(btn => {
            btn.style.display = 'inline-block';
            btn.disabled = false;
        });
    }
    
    // Check if running as PWA
    function isRunningAsPWA() {
        return window.matchMedia('(display-mode: standalone)').matches || 
               window.navigator.standalone === true;
    }
    
    // Enhanced mobile PWA detection and setup
    function initializeMobilePWA() {
        console.log('Mobile PWA: Initializing...');
        
        // Detect platform
        detectMobilePlatform();
        
        // Add PWA class to body if running as PWA
        if (isRunningAsPWA()) {
            document.body.classList.add('pwa-mode', 'mobile-pwa-mode');
            console.log('Mobile PWA: Running as installed PWA');
        }
        
        // Listen for install prompt
        window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
        
        // Listen for successful installation
        window.addEventListener('appinstalled', () => {
            console.log('Mobile PWA: App installed successfully');
            dismissMobileBanner();
            
            // Track installation
            if (typeof gtag !== 'undefined') {
                gtag('event', 'mobile_pwa_install', {
                    event_category: 'engagement',
                    event_label: 'Mobile PWA Installation'
                });
            }
        });
        
        // Enhanced service worker registration for mobile
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('./sw.js')
                .then(registration => {
                    console.log('Mobile PWA: Service Worker registered successfully:', registration.scope);
                    
                    // Check for updates more frequently on mobile
                    registration.addEventListener('updatefound', () => {
                        console.log('Mobile PWA: Service Worker update found');
                        const newWorker = registration.installing;
                        
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                console.log('Mobile PWA: New content available, will refresh on next visit');
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Mobile PWA: Service Worker registration failed:', error);
                });
        }
        
        // Mobile-specific PWA enhancements
        if (isMobile) {
            // Prevent zoom on double tap for better PWA experience
            let lastTouchEnd = 0;
            document.addEventListener('touchend', function (event) {
                const now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    event.preventDefault();
                }
                lastTouchEnd = now;
            }, false);
            
            // Add mobile PWA styles
            const mobileStyles = document.createElement('style');
            mobileStyles.textContent = `
                .mobile-pwa-mode {
                    /* Enhanced mobile PWA styles */
                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
                    -webkit-tap-highlight-color: transparent;
                }
                
                .mobile-pwa-mode .navbar {
                    /* Better navbar for mobile PWA */
                    -webkit-backdrop-filter: blur(10px);
                    backdrop-filter: blur(10px);
                }
                
                @media (max-width: 768px) {
                    .mobile-pwa-mode {
                        /* Mobile-specific PWA optimizations */
                        overflow-x: hidden;
                    }
                }
            `;
            document.head.appendChild(mobileStyles);
        }
        
        console.log('Mobile PWA: Initialization complete');
    }
    
    // Global mobile PWA functions
    window.MobilePWA = {
        install: handleMobileInstall,
        showGuide: showMobileInstallGuide,
        isInstalled: isRunningAsPWA,
        isPlatform: detectMobilePlatform,
        dismiss: dismissMobileBanner
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeMobilePWA);
    } else {
        initializeMobilePWA();
    }
    
})();