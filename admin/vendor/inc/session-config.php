<?php
/**
 * Session Configuration for Admin Login
 * 
 * Admin sessions last for 15 hours for security reasons.
 * After 15 hours, admin must re-login.
 * 
 * Include this file BEFORE session_start() in all pages.
 */

// Only set ini settings if session hasn't started yet
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie to last 15 hours (54000 seconds)
    ini_set('session.gc_maxlifetime', 54000);
    ini_set('session.cookie_lifetime', 54000);

    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 54000,  // 15 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,      // Set to true if using HTTPS
        'httponly' => true,     // Prevent JavaScript access to session cookie
        'samesite' => 'Lax'     // CSRF protection
    ]);
}

// Optional: Set session save path if needed
// session_save_path('/path/to/sessions');

/**
 * Track last visited page for auto-redirect after login
 * Call this function on every protected page
 */
function track_last_page() {
    // Get current page
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Don't track login, logout, or API pages
    $excluded_pages = [
        'index.php', 
        'admin-logout.php',
        'ajax-get-booking-details.php',
        'ajax-get-technicians-for-service.php',
        'api-admin-notifications.php',
        'api-assign-booking.php',
        'api-auto-assign-booking.php',
        'api-cancel-booking.php',
        'api-check-new-bookings.php',
        'api-check-skill-match.php',
        'api-generate-ez-id.php',
        'api-get-available-technicians.php',
        'api-get-live-booking-status.php',
        'api-mark-notification-read.php',
        'api-realtime-notifications.php',
        'api-send-id-card-whatsapp.php',
        'api-set-technician-leave.php',
        'api-unified-notifications.php',
        'api-update-technician-status.php',
        'get-available-technicians.php',
        'get-recent-notifications.php',
        'get-services-by-subcategory.php',
        'get-stats-ajax.php',
        'get-stats-data.php',
        'get-subcategories.php'
    ];
    
    // Also exclude any file that starts with 'api-', 'ajax-', or 'get-'
    $is_api_file = (strpos($current_page, 'api-') === 0) || 
                   (strpos($current_page, 'ajax-') === 0) ||
                   (strpos($current_page, 'get-') === 0) ||
                   (strpos($current_page, 'check-') === 0);
    
    // Only track valid admin pages that exist and start with 'admin-'
    if (!in_array($current_page, $excluded_pages) && !$is_api_file) {
        // Verify it's a valid admin page
        if(strpos($current_page, 'admin-') === 0 && file_exists($current_page)) {
            $_SESSION['last_page'] = $current_page;
            
            // Also store query string if present
            if (!empty($_SERVER['QUERY_STRING'])) {
                $_SESSION['last_page'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
    }
}
?>
