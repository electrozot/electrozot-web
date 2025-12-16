/**
 * Comprehensive PWA Installation Fix
 * This script addresses common issues preventing PWA installation prompts
 */

console.log('🔧 PWA Installation Fix loaded');

// Global variables
let deferredPrompt = null;
let installPromptAvailable = false;
let debugMode = true; // Set to false in production

function debugLog(message, data = null) {
    if (debugMode) {
        console.log(`[PWA Debug] ${message}`, data || '');
    }
}

// 1. IMMEDIATE SETUP - Before any other scripts
(function immediateSetup() {
    debugLog('Setting up immediate PWA listeners...');
    
    // Listen for install prompt IMMEDIATELY
    window.addEventListener('beforeinstallprompt', (e) => {
        debugLog('🎉 beforeinstallprompt event fired!');
        e.preventDefault();
        deferredPrompt = e;
        installPromptAvailable = true;
        
        // Store globally for access from other scripts
        window.pwaInstallEvent = e;
        window.pwaPromptAvailable = true;
        
        // Show install button immediately
        showInstallButton();
        
        // Dispatch custom event for other scripts
        window.dispatchEvent(new CustomEvent('pwaInstallReady', { detail: e }));
        
        debugLog('Install prompt stored and ready');
    });

    // Listen for successful installation
    window.addEventListener('appinstalled', () => {
        debugLog('🎉 App installed successfully!');
        hideInstallButton();
        showInstalledMessage();
        
        // Clear stored prompt
        deferredPrompt = null;
        installPromptAvailable = false;
        window.pwaInstallEvent = null;
        window.pwaPromptAvailable = false;
    });
})();

// 2. SERVICE WORKER REGISTRATION - Improved version
async function registerServiceWorkerImproved() {
    if (!('serviceWorker' in navigator)) {
        debugLog('❌ Service Worker not supported');
        return false;
    }

    try {
        debugLog('🔄 Registering Service Worker...');
        
        // Register with minimal options first
        const registration = await navigator.serviceWorker.register('./sw.js', {
            scope: './'
        });
        
        debugLog('✅ Service Worker registered successfully', registration);
        
        // Wait for it to be ready
        await navigator.serviceWorker.ready;
        debugLog('✅ Service Worker is ready');
        
        // Check if it's controlling the page
        if (navigator.serviceWorker.controller) {
            debugLog('✅ Service Worker is controlling the page');
        } else {
            debugLog('⚠️ Service Worker not controlling page yet');
        }
        
        return true;
    } catch (error) {
        debugLog('❌ Service Worker registration failed:', error);
        return false;
    }
}

// 3. INSTALL BUTTON MANAGEMENT
function showInstallButton() {
    debugLog('Showing install button...');
    
    // Try to find install buttons by various selectors
    const selectors = [
        '#pwa-main-install-btn',
        '.pwa-install-btn',
        '[data-pwa-install]',
        '#installBtn'
    ];
    
    let buttonFound = false;
    
    selectors.forEach(selector => {
        const button = document.querySelector(selector);
        if (button) {
            button.style.display = 'inline-block';
            button.style.opacity = '1';
            button.disabled = false;
            button.classList.remove('hidden');
            buttonFound = true;
            debugLog(`Install button shown: ${selector}`);
        }
    });
    
    if (!buttonFound) {
        debugLog('⚠️ No install button found, creating one...');
        createInstallButton();
    }
}

function hideInstallButton() {
    debugLog('Hiding install button...');
    
    const selectors = [
        '#pwa-main-install-btn',
        '.pwa-install-btn',
        '[data-pwa-install]',
        '#installBtn',
        '#dynamicInstallBtn'
    ];
    
    selectors.forEach(selector => {
        const button = document.querySelector(selector);
        if (button) {
            button.style.display = 'none';
            debugLog(`Install button hidden: ${selector}`);
        }
    });
}

function createInstallButton() {
    // Create a floating install button
    const button = document.createElement('button');
    button.id = 'dynamicInstallBtn';
    button.innerHTML = '📱 Install App';
    button.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 15px 25px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    `;
    
    button.addEventListener('click', installPWA);
    button.addEventListener('mouseenter', () => {
        button.style.transform = 'scale(1.05)';
    });
    button.addEventListener('mouseleave', () => {
        button.style.transform = 'scale(1)';
    });
    
    document.body.appendChild(button);
    debugLog('✅ Dynamic install button created');
}

function showInstalledMessage() {
    debugLog('Showing installed message...');
    
    // Try to find installed message elements
    const installedMsg = document.querySelector('#pwa-installed-msg, .pwa-installed-msg');
    if (installedMsg) {
        installedMsg.style.display = 'block';
    } else {
        // Create a temporary success message
        const message = document.createElement('div');
        message.innerHTML = '🎉 App installed successfully!';
        message.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            z-index: 10001;
            font-weight: bold;
        `;
        document.body.appendChild(message);
        
        setTimeout(() => message.remove(), 5000);
    }
}

// 4. IMPROVED INSTALL FUNCTION
async function installPWA() {
    debugLog('🚀 Install PWA function called');
    debugLog('Deferred prompt available:', !!deferredPrompt);
    debugLog('Install prompt available:', installPromptAvailable);
    
    if (!deferredPrompt) {
        debugLog('❌ No deferred prompt available');
        showManualInstallInstructions();
        return;
    }
    
    try {
        debugLog('Triggering install prompt...');
        
        // Show the install prompt
        await deferredPrompt.prompt();
        
        // Wait for user choice
        const { outcome } = await deferredPrompt.userChoice;
        debugLog('User choice:', outcome);
        
        if (outcome === 'accepted') {
            debugLog('✅ User accepted installation');
            // The appinstalled event will handle UI updates
        } else {
            debugLog('❌ User declined installation');
        }
        
        // Clear the prompt
        deferredPrompt = null;
        installPromptAvailable = false;
        window.pwaInstallEvent = null;
        window.pwaPromptAvailable = false;
        
    } catch (error) {
        debugLog('❌ Install error:', error);
        showManualInstallInstructions();
    }
}

// 5. MANUAL INSTALL INSTRUCTIONS
function showManualInstallInstructions() {
    const userAgent = navigator.userAgent.toLowerCase();
    let instructions = '';
    
    if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
        instructions = 'Chrome: Look for the install icon (⊕) in the address bar, or go to Menu → Install';
    } else if (userAgent.includes('safari')) {
        instructions = 'Safari: Tap the Share button and select "Add to Home Screen"';
    } else if (userAgent.includes('firefox')) {
        instructions = 'Firefox: Look for the install prompt or "Add to Home Screen" option';
    } else if (userAgent.includes('edg')) {
        instructions = 'Edge: Look for the install icon in the address bar, or go to Menu → Apps → Install';
    } else {
        instructions = 'Look for an "Install" or "Add to Home Screen" option in your browser menu';
    }
    
    alert(`Install ElectroZot App\n\n${instructions}`);
}

// 6. PWA CRITERIA CHECKER
async function checkPWACriteria() {
    debugLog('🔍 Checking PWA installation criteria...');
    
    const checks = {
        https: location.protocol === 'https:' || location.hostname === 'localhost',
        serviceWorker: 'serviceWorker' in navigator,
        manifest: !!document.querySelector('link[rel="manifest"]'),
        installPromptSupport: 'onbeforeinstallprompt' in window
    };
    
    debugLog('PWA Criteria Check:', checks);
    
    // Check manifest validity
    try {
        const manifestLink = document.querySelector('link[rel="manifest"]');
        if (manifestLink) {
            const response = await fetch(manifestLink.href);
            if (response.ok) {
                const manifest = await response.json();
                checks.manifestValid = true;
                checks.manifestHasRequiredIcons = manifest.icons && 
                    manifest.icons.some(icon => icon.sizes.includes('192x192')) &&
                    manifest.icons.some(icon => icon.sizes.includes('512x512'));
                debugLog('Manifest check:', { valid: true, hasRequiredIcons: checks.manifestHasRequiredIcons });
            } else {
                checks.manifestValid = false;
                debugLog('❌ Manifest not accessible');
            }
        }
    } catch (error) {
        checks.manifestValid = false;
        debugLog('❌ Manifest error:', error);
    }
    
    // Check if already installed
    checks.alreadyInstalled = window.matchMedia('(display-mode: standalone)').matches || 
                             window.navigator.standalone === true;
    
    debugLog('Final PWA criteria:', checks);
    return checks;
}

// 7. INITIALIZATION
async function initializePWAFix() {
    debugLog('🚀 Initializing PWA Fix...');
    
    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        debugLog('✅ App is already installed');
        showInstalledMessage();
        return;
    }
    
    // Register service worker
    const swRegistered = await registerServiceWorkerImproved();
    if (!swRegistered) {
        debugLog('⚠️ Service Worker registration failed, but PWA might still work');
    }
    
    // Check PWA criteria
    const criteria = await checkPWACriteria();
    
    // Wait a bit for install prompt
    setTimeout(() => {
        if (!deferredPrompt && !criteria.alreadyInstalled) {
            debugLog('⚠️ Install prompt not available after 3 seconds');
            debugLog('This might be normal - the prompt may appear later or the app may already be installable');
            
            // Still show install button for manual installation
            showInstallButton();
        }
    }, 3000);
    
    debugLog('✅ PWA Fix initialization complete');
}

// 8. GLOBAL FUNCTIONS
window.installPWA = installPWA;
window.checkPWACriteria = checkPWACriteria;
window.debugPWA = () => {
    console.log('PWA Debug Info:', {
        deferredPrompt: !!deferredPrompt,
        installPromptAvailable,
        isInstalled: window.matchMedia('(display-mode: standalone)').matches,
        serviceWorkerSupported: 'serviceWorker' in navigator,
        manifestLink: !!document.querySelector('link[rel="manifest"]')
    });
};

// 9. AUTO-INITIALIZE
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePWAFix);
} else {
    initializePWAFix();
}

debugLog('✅ PWA Installation Fix script loaded successfully');