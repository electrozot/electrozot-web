<?php
/**
 * Session Configuration for Persistent Login
 * 
 * This file configures PHP sessions to last permanently (10 years),
 * so users don't need to log in repeatedly on the same device/browser.
 * 
 * Include this file BEFORE session_start() in all pages.
 */

// Set session cookie to last 10 years (315360000 seconds) - effectively permanent
ini_set('session.gc_maxlifetime', 315360000);
ini_set('session.cookie_lifetime', 315360000);

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 315360000,  // 10 years (effectively permanent)
    'path' => '/',
    'domain' => '',
    'secure' => false,      // Set to true if using HTTPS
    'httponly' => true,     // Prevent JavaScript access to session cookie
    'samesite' => 'Lax'     // CSRF protection
]);

// Optional: Set session save path if needed
// session_save_path('/path/to/sessions');

/**
 * Track last visited page for auto-redirect after login
 * Call this function on every protected page
 */
function track_last_page() {
    // Get current page
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Don't track login, logout, registration, or API pages
    $excluded_pages = [
        'index.php', 
        'user-logout.php', 
        'usr-register.php', 
        'usr-forgot-password.php',
        'check-phone-availability.php',
        'get-booking-status.php',
        'get-all-bookings-status.php',
        'api-rate-technician.php',
        'api-modify-booking.php',
        'api-get-booking-status.php'
    ];
    
    // Also exclude any file that starts with 'api-', 'ajax-', or 'get-'
    $is_api_file = (strpos($current_page, 'api-') === 0) || 
                   (strpos($current_page, 'ajax-') === 0) ||
                   (strpos($current_page, 'get-') === 0) ||
                   (strpos($current_page, 'check-') === 0);
    
    if (!in_array($current_page, $excluded_pages) && !$is_api_file) {
        $_SESSION['last_page'] = $current_page;
        
        // Also store query string if present
        if (!empty($_SERVER['QUERY_STRING'])) {
            $_SESSION['last_page'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
}
?>
