/**
 * Disable Text Selection and Copying
 * Prevents users from copying content from the website
 */

(function() {
    'use strict';
    
    // Disable right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    }, false);
    
    // Disable text selection
    document.addEventListener('selectstart', function(e) {
        // Allow selection in input fields
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.isContentEditable) {
            return true;
        }
        e.preventDefault();
        return false;
    }, false);
    
    // Disable copy via keyboard (Ctrl+C, Cmd+C)
    document.addEventListener('copy', function(e) {
        // Allow copy in input fields
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.isContentEditable) {
            return true;
        }
        e.preventDefault();
        return false;
    }, false);
    
    // Disable cut via keyboard (Ctrl+X, Cmd+X)
    document.addEventListener('cut', function(e) {
        // Allow cut in input fields
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.isContentEditable) {
            return true;
        }
        e.preventDefault();
        return false;
    }, false);
    
    // Disable keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Allow shortcuts in input fields
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.isContentEditable) {
            return true;
        }
        
        // Ctrl+C, Cmd+C (Copy)
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 67) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+X, Cmd+X (Cut)
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 88) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+A, Cmd+A (Select All)
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 65) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+U, Cmd+U (View Source)
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 85) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+S, Cmd+S (Save)
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
            e.preventDefault();
            return false;
        }
        
        // F12 (Developer Tools)
        if (e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+Shift+I, Cmd+Option+I (Developer Tools)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.keyCode === 73) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+Shift+J, Cmd+Option+J (Console)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.keyCode === 74) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+Shift+C, Cmd+Option+C (Inspect Element)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.keyCode === 67) {
            e.preventDefault();
            return false;
        }
    }, false);
    
    // Disable drag and drop
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    }, false);
    
    // Disable text selection on mobile (touch)
    document.addEventListener('touchstart', function(e) {
        // Allow touch in input fields
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.isContentEditable) {
            return true;
        }
    }, false);
    
    // Clear selection if any exists
    function clearSelection() {
        if (window.getSelection) {
            if (window.getSelection().empty) {
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {
            document.selection.empty();
        }
    }
    
    // Clear selection periodically
    setInterval(clearSelection, 100);
    
    // Disable print screen (limited effectiveness)
    document.addEventListener('keyup', function(e) {
        if (e.key === 'PrintScreen') {
            navigator.clipboard.writeText('');
            alert('Screenshots are disabled on this website.');
        }
    });
    
    // Disable clipboard access
    if (navigator.clipboard && navigator.clipboard.writeText) {
        document.addEventListener('copy', function(e) {
            if (e.target.tagName !== 'INPUT' && 
                e.target.tagName !== 'TEXTAREA' && 
                !e.target.isContentEditable) {
                navigator.clipboard.writeText('');
            }
        });
    }
    

    
})();
