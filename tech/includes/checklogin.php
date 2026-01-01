<?php
// Check if technician is logged in using PWA-compatible validation
if (function_exists('is_technician_logged_in')) {
    // Use PWA session validation if available
    if (!is_technician_logged_in()) {
        header('Location: index.php');
        exit();
    }
} else {
    // Fallback to regular session check
    if(!isset($_SESSION['t_id']) || !isset($_SESSION['t_name'])){
        header('Location: index.php');
        exit();
    }
}

// Track this page as the last visited page
$current_page = basename($_SERVER['PHP_SELF']);

// Exclude login, logout, API endpoints, and AJAX files from tracking
$excluded_pages = [
    'index.php', 
    'logout.php', 
    'register.php',
    'check-technician-notifications.php',
    'api-accept-booking.php',
    'api-complete-booking.php',
    'api-get-my-bookings.php',
    'api-reject-booking.php'
];

// Also exclude any file that starts with 'api-' or contains 'ajax'
$is_api_file = (strpos($current_page, 'api-') === 0) || 
               (strpos($current_page, 'ajax') !== false) ||
               (strpos($current_page, 'check-') === 0);

if (!in_array($current_page, $excluded_pages) && !$is_api_file) {
    $_SESSION['last_page'] = $current_page;
    
    // Also store query string if present
    if (!empty($_SERVER['QUERY_STRING'])) {
        $_SESSION['last_page'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}
?>
