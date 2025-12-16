/**
 * PWA Installability Checker
 * Helps debug PWA installation issues
 */

function checkPWAInstallability() {
    const results = {
        timestamp: new Date().toISOString(),
        checks: []
    };
    
    // Check 1: HTTPS
    const isHTTPS = location.protocol === 'https:' || location.hostname === 'localhost';
    results.checks.push({
        name: 'HTTPS',
        passed: isHTTPS,
        message: isHTTPS ? 'Site is served over HTTPS' : 'Site must be served over HTTPS',
        required: true
    });
    
    // Check 2: Service Worker
    const hasSW = 'serviceWorker' in navigator;
    results.checks.push({
        name: 'Service Worker Support',
        passed: hasSW,
        message: hasSW ? 'Browser supports Service Workers' : 'Browser does not support Service Workers',
        required: true
    });
    
    // Check 3: Manifest
    const manifestLink = document.querySelector('link[rel="manifest"]');
    results.checks.push({
        name: 'Manifest Link',
        passed: !!manifestLink,
        message: manifestLink ? `Manifest linked: ${manifestLink.href}` : 'No manifest link found',
        required: true
    });
    
    // Check 4: Service Worker Registration
    navigator.serviceWorker.getRegistrations().then(registrations => {
        results.checks.push({
            name: 'Service Worker Registration',
            passed: registrations.length > 0,
            message: registrations.length > 0 ? `${registrations.length} service worker(s) registered` : 'No service workers registered',
            required: true
        });
        
        // Check 5: Manifest Content (async)
        if (manifestLink) {
            fetch(manifestLink.href)
                .then(response => response.json())
                .then(manifest => {
                    // Check required manifest fields
                    const requiredFields = ['name', 'short_name', 'start_url', 'display', 'icons'];
                    const missingFields = requiredFields.filter(field => !manifest[field]);
                    
                    results.checks.push({
                        name: 'Manifest Content',
                        passed: missingFields.length === 0,
                        message: missingFields.length === 0 ? 'All required manifest fields present' : `Missing fields: ${missingFields.join(', ')}`,
                        required: true
                    });
                    
                    // Check icons
                    const hasValidIcons = manifest.icons && manifest.icons.length > 0 && 
                                         manifest.icons.some(icon => icon.sizes && icon.sizes.includes('192x192'));
                    
                    results.checks.push({
                        name: 'Manifest Icons',
                        passed: hasValidIcons,
                        message: hasValidIcons ? `${manifest.icons.length} icons defined` : 'No valid icons (need at least 192x192)',
                        required: true
                    });
                    
                    // Display results
                    displayResults(results);
                })
                .catch(error => {
                    results.checks.push({
                        name: 'Manifest Loading',
                        passed: false,
                        message: `Failed to load manifest: ${error.message}`,
                        required: true
                    });
                    displayResults(results);
                });
        } else {
            displayResults(results);
        }
    });
    
    // Check 6: Install Prompt Event
    let installPromptReceived = false;
    window.addEventListener('beforeinstallprompt', () => {
        installPromptReceived = true;
        results.checks.push({
            name: 'Install Prompt',
            passed: true,
            message: 'beforeinstallprompt event received',
            required: false
        });
        displayResults(results);
    });
    
    // Wait a bit to see if install prompt fires
    setTimeout(() => {
        if (!installPromptReceived) {
            results.checks.push({
                name: 'Install Prompt',
                passed: false,
                message: 'beforeinstallprompt event not received (may indicate PWA criteria not met)',
                required: false
            });
            displayResults(results);
        }
    }, 3000);
    
    return results;
}

function displayResults(results) {
    console.group('🔍 PWA Installability Check');
    console.log('Timestamp:', results.timestamp);
    
    const passed = results.checks.filter(check => check.passed).length;
    const total = results.checks.length;
    const requiredPassed = results.checks.filter(check => check.required && check.passed).length;
    const requiredTotal = results.checks.filter(check => check.required).length;
    
    console.log(`Overall: ${passed}/${total} checks passed`);
    console.log(`Required: ${requiredPassed}/${requiredTotal} required checks passed`);
    
    results.checks.forEach(check => {
        const icon = check.passed ? '✅' : '❌';
        const required = check.required ? '[REQUIRED]' : '[OPTIONAL]';
        console.log(`${icon} ${check.name} ${required}: ${check.message}`);
    });
    
    if (requiredPassed === requiredTotal) {
        console.log('🎉 PWA should be installable!');
    } else {
        console.log('⚠️ PWA may not be installable - fix required checks above');
    }
    
    console.groupEnd();
    
    // Also display in UI if there's a results container
    const resultsContainer = document.getElementById('pwa-check-results');
    if (resultsContainer) {
        resultsContainer.innerHTML = `
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h3>PWA Installability Check</h3>
                <p><strong>Overall:</strong> ${passed}/${total} checks passed</p>
                <p><strong>Required:</strong> ${requiredPassed}/${requiredTotal} required checks passed</p>
                <div style="margin-top: 15px;">
                    ${results.checks.map(check => `
                        <div style="margin: 5px 0; padding: 8px; background: rgba(${check.passed ? '16,185,129' : '239,68,68'},0.2); border-radius: 5px;">
                            ${check.passed ? '✅' : '❌'} <strong>${check.name}</strong> ${check.required ? '[REQUIRED]' : '[OPTIONAL]'}<br>
                            <small>${check.message}</small>
                        </div>
                    `).join('')}
                </div>
                ${requiredPassed === requiredTotal ? 
                    '<p style="color: #10b981; font-weight: bold;">🎉 PWA should be installable!</p>' : 
                    '<p style="color: #ef4444; font-weight: bold;">⚠️ PWA may not be installable - fix required checks above</p>'
                }
            </div>
        `;
    }
}

// Auto-run check when script loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkPWAInstallability);
} else {
    checkPWAInstallability();
}

// Export for manual use
window.checkPWAInstallability = checkPWAInstallability;