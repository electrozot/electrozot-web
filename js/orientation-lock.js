/**
 * PWA Orientation Lock Manager
 * Locks mobile devices to portrait, allows desktop landscape
 */

(function() {
    'use strict';

    // Detect if device is mobile or desktop
    function isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) 
            || window.innerWidth <= 1024;
    }

    // Lock orientation to portrait on mobile
    function lockOrientation() {
        if (!isMobileDevice()) {
            // Desktop - allow any orientation
            return;
        }

        // Try to lock orientation using Screen Orientation API
        if (screen.orientation && screen.orientation.lock) {
            screen.orientation.lock('portrait').catch(function(error) {

            });
        } 
        // Fallback for older browsers
        else if (screen.lockOrientation) {
            screen.lockOrientation('portrait');
        } 
        else if (screen.mozLockOrientation) {
            screen.mozLockOrientation('portrait');
        } 
        else if (screen.msLockOrientation) {
            screen.msLockOrientation('portrait');
        }
    }

    // Handle orientation change events - prevent any rotation
    function handleOrientationChange() {
        if (!isMobileDevice()) {
            return;
        }

        // Force viewport back to portrait dimensions
        const viewport = document.querySelector('meta[name="viewport"]');
        if (viewport) {
            viewport.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no');
        }
        
        // Force body dimensions
        document.body.style.width = '100vw';
        document.body.style.height = '100vh';
        document.body.style.maxWidth = '100vw';
        document.body.style.maxHeight = '100vh';
        document.body.style.transform = 'none';
        document.body.style.overflow = 'hidden auto';
        
        // Force html dimensions
        document.documentElement.style.width = '100vw';
        document.documentElement.style.height = '100vh';
        document.documentElement.style.maxWidth = '100vw';
        document.documentElement.style.maxHeight = '100vh';
        document.documentElement.style.transform = 'none';
    }

    // Initialize orientation lock
    function init() {
        if (!isMobileDevice()) {
            return;
        }
        
        // Lock orientation on page load
        lockOrientation();
        handleOrientationChange();

        // Prevent orientation change completely
        window.addEventListener('orientationchange', function(e) {
            e.preventDefault();
            handleOrientationChange();
            lockOrientation();
        });

        // Prevent resize that might indicate rotation
        window.addEventListener('resize', function(e) {
            if (isMobileDevice()) {
                handleOrientationChange();
                lockOrientation();
            }
        });

        // Monitor screen changes more aggressively
        if (screen.orientation) {
            screen.orientation.addEventListener('change', function(e) {
                e.preventDefault();
                handleOrientationChange();
                lockOrientation();
            });
        }

        // Force dimensions on any window focus
        window.addEventListener('focus', function() {
            handleOrientationChange();
            lockOrientation();
        });
        
        // Set up continuous monitoring for mobile
        setInterval(function() {
            if (isMobileDevice()) {
                handleOrientationChange();
            }
        }, 100);
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // For PWA: Lock orientation when app becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && isMobileDevice()) {
            lockOrientation();
        }
    });

})();
