<?php
/**
 * Post-Redirect-Get (PRG) Pattern Helper
 * 
 * This helper prevents form resubmission issues by:
 * 1. Storing messages in session
 * 2. Redirecting after POST
 * 3. Displaying messages from session
 * 
 * Usage:
 * - After processing POST: prg_redirect('page.php', 'Success message', 'success');
 * - At top of page: prg_get_messages($succ, $err);
 */

/**
 * Redirect after POST with message
 * 
 * @param string $url - URL to redirect to
 * @param string $message - Message to display
 * @param string $type - 'success' or 'error'
 */
function prg_redirect($url, $message, $type = 'success') {
    if($type === 'success') {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = $message;
    }
    header("Location: " . $url);
    exit();
}

/**
 * Get messages from session and clear them
 * 
 * @param string &$succ - Variable to store success message
 * @param string &$err - Variable to store error message
 */
function prg_get_messages(&$succ, &$err) {
    if(isset($_SESSION['success'])) {
        $succ = $_SESSION['success'];
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])) {
        $err = $_SESSION['error'];
        unset($_SESSION['error']);
    }
}

/**
 * Set success message
 */
function prg_success($message) {
    $_SESSION['success'] = $message;
}

/**
 * Set error message
 */
function prg_error($message) {
    $_SESSION['error'] = $message;
}

/**
 * Redirect to current page (useful after POST)
 */
function prg_redirect_self() {
    $current_page = basename($_SERVER['PHP_SELF']);
    header("Location: " . $current_page);
    exit();
}
?>
