/**
 * PWA Installation Checker
 * This script helps diagnose PWA installation issues
 */

console.log('🔧 PWA Installation Checker Started');

// Check basic PWA requirements
function checkPWARequirements() {
    const results = [];
    
    // 1. HTTPS requirement (except localhost)
    const isHTTPS = location.protocol === 'https:' || location.hostname === 'localhost';
    results.push({
        test: 'HTTPS/Localhost',
        passed: isHTTPS,
        message: isHTTPS ? '✅ HTTPS or localhost detected' : '❌ PWA requires HTTPS in production'
    });
    
    // 2. Service Worker support
    const hasSW = 'serviceWorker' in navigator;
    results.push({
        test: 'Service Worker Support',
        passed: hasSW,
        message: hasSW ? '✅ Service Worker supported' : '❌ Service Worker not supported'
    });
    
    // 3. Manifest support
    const hasManifest = document.querySelector('link[rel="manifest"]') !== null;
    results.push({
        test: 'Manifest Link',
        passed: hasManifest,
        message: hasManifest ? '✅ Manifest link found' : '❌ Manifest link missing'
    });
    
    // 4. Install prompt support
    const hasInstallPrompt = 'onbeforeinstallprompt' in window;
    results.push({
        test: 'Install Prompt Support',
        passed: hasInstallPrompt,
        message: hasInstallPrompt ? '✅ Install prompt supported' : '⚠️ Install prompt not supported (may still be installable)'
    });
    
    return results;
}

// Check manifest validity
async function checkManifest() {
    try {
        const manifestLink = document.querySelector('link[rel="manifest"]');
        if (!manifestLink) {
            return { error: 'No manifest link found' };
        }
        
        const response = await fetch(manifestLink.href);
        if (!response.ok) {
            return { error: `Manifest fetch failed: ${response.status}` };
        }
        
        const manifest = await response.json();
        
        const checks = [];
        
        // Required fields
        checks.push({
            test: 'Name',
            passed: !!manifest.name,
            message: manifest.name ? `✅ Name: ${manifest.name}` : '❌ Name missing'
        });
        
        checks.push({
            test: 'Start URL',
            passed: !!manifest.start_url,
            message: manifest.start_url ? `✅ Start URL: ${manifest.start_url}` : '❌ Start URL missing'
        });
        
        checks.push({
            test: 'Display Mode',
            passed: !!manifest.display,
            message: manifest.display ? `✅ Display: ${manifest.display}` : '❌ Display mode missing'
        });
        
        // Icons check
        const hasIcons = manifest.icons && manifest.icons.length > 0;
        checks.push({
            test: 'Icons',
            passed: hasIcons,
            message: hasIcons ? `✅ ${manifest.icons.length} icons defined` : '❌ No icons defined'
        });
        
        if (hasIcons) {
            // Check for required icon sizes
            const has192 = manifest.icons.some(icon => icon.sizes.includes('192x192'));
            const has512 = manifest.icons.some(icon => icon.sizes.includes('512x512'));
            
            checks.push({
                test: '192x192 Icon',
                passed: has192,
                message: has192 ? '✅ 192x192 icon found' : '❌ 192x192 icon missing (required)'
            });
            
            checks.push({
                test: '512x512 Icon',
                passed: has512,
                message: has512 ? '✅ 512x512 icon found' : '❌ 512x512 icon missing (required)'
            });
        }
        
        return { manifest, checks };
    } catch (error) {
        return { error: error.message };
    }
}

// Check service worker
async function checkServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        return { error: 'Service Worker not supported' };
    }
    
    try {
        const registration = await navigator.serviceWorker.getRegistration();
        const checks = [];
        
        if (registration) {
            checks.push({
                test: 'Registration',
                passed: true,
                message: '✅ Service Worker registered'
            });
            
            checks.push({
                test: 'State',
                passed: registration.active !== null,
                message: registration.active ? '✅ Service Worker active' : '⚠️ Service Worker not active'
            });
            
            checks.push({
                test: 'Scope',
                passed: true,
                message: `✅ Scope: ${registration.scope}`
            });
        } else {
            checks.push({
                test: 'Registration',
                passed: false,
                message: '❌ Service Worker not registered'
            });
        }
        
        return { registration, checks };
    } catch (error) {
        return { error: error.message };
    }
}

// Check icon accessibility
async function checkIcons() {
    try {
        const manifestLink = document.querySelector('link[rel="manifest"]');
        if (!manifestLink) return { error: 'No manifest found' };
        
        const response = await fetch(manifestLink.href);
        const manifest = await response.json();
        
        if (!manifest.icons) return { error: 'No icons in manifest' };
        
        const checks = [];
        
        for (const icon of manifest.icons) {
            try {
                const iconResponse = await fetch(icon.src);
                checks.push({
                    test: `Icon ${icon.sizes}`,
                    passed: iconResponse.ok,
                    message: iconResponse.ok ? 
                        `✅ ${icon.sizes} (${icon.src})` : 
                        `❌ ${icon.sizes} not accessible (${icon.src})`
                });
            } catch (error) {
                checks.push({
                    test: `Icon ${icon.sizes}`,
                    passed: false,
                    message: `❌ ${icon.sizes} error: ${error.message}`
                });
            }
        }
        
        return { checks };
    } catch (error) {
        return { error: error.message };
    }
}

// Main diagnostic function
async function runPWADiagnostics() {
    console.log('🔍 Running PWA Diagnostics...');
    
    // Basic requirements
    console.log('\n📋 Basic Requirements:');
    const basicChecks = checkPWARequirements();
    basicChecks.forEach(check => console.log(check.message));
    
    // Manifest check
    console.log('\n📄 Manifest Check:');
    const manifestResult = await checkManifest();
    if (manifestResult.error) {
        console.log(`❌ Manifest Error: ${manifestResult.error}`);
    } else {
        manifestResult.checks.forEach(check => console.log(check.message));
    }
    
    // Service Worker check
    console.log('\n⚙️ Service Worker Check:');
    const swResult = await checkServiceWorker();
    if (swResult.error) {
        console.log(`❌ Service Worker Error: ${swResult.error}`);
    } else {
        swResult.checks.forEach(check => console.log(check.message));
    }
    
    // Icons check
    console.log('\n🖼️ Icons Check:');
    const iconsResult = await checkIcons();
    if (iconsResult.error) {
        console.log(`❌ Icons Error: ${iconsResult.error}`);
    } else {
        iconsResult.checks.forEach(check => console.log(check.message));
    }
    
    // Installation status
    console.log('\n📱 Installation Status:');
    const isInstalled = window.matchMedia('(display-mode: standalone)').matches || 
                       window.navigator.standalone === true;
    console.log(isInstalled ? '✅ App is installed' : '⚠️ App is not installed');
    
    // Browser-specific advice
    console.log('\n🌐 Browser-Specific Advice:');
    const userAgent = navigator.userAgent.toLowerCase();
    if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
        console.log('Chrome: Look for install icon in address bar or Menu > Install');
    } else if (userAgent.includes('safari')) {
        console.log('Safari: Use Share > Add to Home Screen');
    } else if (userAgent.includes('firefox')) {
        console.log('Firefox: Look for install prompt or Add to Home Screen');
    } else if (userAgent.includes('edg')) {
        console.log('Edge: Look for install icon in address bar or Menu > Apps > Install');
    }
    
    console.log('\n✅ PWA Diagnostics Complete');
}

// Auto-run diagnostics
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runPWADiagnostics);
} else {
    runPWADiagnostics();
}

// Export for manual use
window.runPWADiagnostics = runPWADiagnostics;