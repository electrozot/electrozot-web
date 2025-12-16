/**
 * PWA Diagnostic Tool
 * Helps debug PWA installation issues in production
 */

function runPWADiagnostic() {
    console.log('🔍 PWA Diagnostic Starting...');
    
    const results = {
        https: false,
        serviceWorker: false,
        manifest: false,
        icons: false,
        installPrompt: false,
        alreadyInstalled: false
    };
    
    // Check HTTPS
    results.https = location.protocol === 'https:' || location.hostname === 'localhost';
    console.log(`🔒 HTTPS: ${results.https ? '✅' : '❌'} (${location.protocol})`);
    
    // Check if already installed
    results.alreadyInstalled = window.matchMedia('(display-mode: standalone)').matches || 
                              window.navigator.standalone === true;
    console.log(`📱 Already Installed: ${results.alreadyInstalled ? '✅' : '❌'}`);
    
    // Check Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then(registration => {
            results.serviceWorker = !!registration;
            console.log(`🛠️ Service Worker: ${results.serviceWorker ? '✅' : '❌'}`);
            if (registration) {
                console.log(`   State: ${registration.active ? registration.active.state : 'none'}`);
                console.log(`   Scope: ${registration.scope}`);
            }
        });
    } else {
        console.log('🛠️ Service Worker: ❌ (Not supported)');
    }
    
    // Check Manifest
    const manifestLink = document.querySelector('link[rel="manifest"]');
    if (manifestLink) {
        fetch(manifestLink.href)
            .then(response => response.json())
            .then(manifest => {
                results.manifest = true;
                console.log('📄 Manifest: ✅');
                console.log('   Name:', manifest.name);
                console.log('   Start URL:', manifest.start_url);
                console.log('   Display:', manifest.display);
                console.log('   Icons:', manifest.icons?.length || 0);
                
                // Check icons
                if (manifest.icons && manifest.icons.length > 0) {
                    const has192 = manifest.icons.some(icon => icon.sizes.includes('192'));
                    const has512 = manifest.icons.some(icon => icon.sizes.includes('512'));
                    results.icons = has192 && has512;
                    console.log(`🖼️ Icons (192x192 & 512x512): ${results.icons ? '✅' : '❌'}`);
                }
            })
            .catch(error => {
                console.log('📄 Manifest: ❌', error);
            });
    } else {
        console.log('📄 Manifest: ❌ (Link not found)');
    }
    
    // Check install prompt availability
    results.installPrompt = !!window.deferredPrompt || !!window.pwaInstallEvent;
    console.log(`⬇️ Install Prompt: ${results.installPrompt ? '✅' : '❌'}`);
    
    // Browser-specific checks
    const userAgent = navigator.userAgent.toLowerCase();
    let browserInfo = '';
    if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
        browserInfo = 'Chrome';
    } else if (userAgent.includes('firefox')) {
        browserInfo = 'Firefox';
    } else if (userAgent.includes('safari')) {
        browserInfo = 'Safari';
    } else if (userAgent.includes('edg')) {
        browserInfo = 'Edge';
    } else {
        browserInfo = 'Unknown';
    }
    console.log(`🌐 Browser: ${browserInfo}`);
    
    // Overall assessment
    setTimeout(() => {
        const readyForInstall = results.https && results.serviceWorker && results.manifest && results.icons;
        console.log(`\n🎯 PWA Install Ready: ${readyForInstall ? '✅' : '❌'}`);
        
        if (!readyForInstall) {
            console.log('\n🔧 Issues to fix:');
            if (!results.https) console.log('   - Enable HTTPS');
            if (!results.serviceWorker) console.log('   - Register Service Worker');
            if (!results.manifest) console.log('   - Fix Manifest file');
            if (!results.icons) console.log('   - Add required icons (192x192, 512x512)');
        }
        
        if (results.alreadyInstalled) {
            console.log('\n✅ PWA is already installed!');
        } else if (readyForInstall && !results.installPrompt) {
            console.log('\n⏳ PWA is ready but install prompt not available yet. Try:');
            console.log('   - Browse the site for 2-3 minutes');
            console.log('   - Interact with the page (scroll, click)');
            console.log('   - Check browser menu for install option');
        }
    }, 2000);
    
    return results;
}

// Auto-run diagnostic in development
if (location.hostname === 'localhost' || location.hostname.includes('127.0.0.1')) {
    setTimeout(runPWADiagnostic, 3000);
}

// Make available globally
window.runPWADiagnostic = runPWADiagnostic;