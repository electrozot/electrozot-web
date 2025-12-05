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
                console.log('Orientation lock not supported:', error);
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

    // Handle orientation change events
    function handleOrientationChange() {
        if (!isMobileDevice()) {
            return;
        }

        const orientation = window.orientation || screen.orientation?.angle || 0;
        
        // If device is in landscape mode on mobile, show warning
        if (Math.abs(orientation) === 90) {
            document.body.classList.add('landscape-warning');
        } else {
            document.body.classList.remove('landscape-warning');
        }
    }

    // Initialize orientation lock
    function init() {
        // Lock orientation on page load
        lockOrientation();

        // Listen for orientation changes
        window.addEventListener('orientationchange', function() {
            handleOrientationChange();
            // Try to lock again after orientation change
            setTimeout(lockOrientation, 100);
        });

        // Also listen to resize events
        window.addEventListener('resize', function() {
            handleOrientationChange();
        });

        // Initial check
        handleOrientationChange();
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
