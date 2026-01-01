<?php
/**
 * PWA Session Fix for Technician Authentication
 * 
 * This file addresses PWA-specific session issues where technician
 * authentication works on localhost but fails in production PWA.
 * 
 * Issues addressed:
 * 1. PWA cookie isolation
 * 2. HTTPS cookie security requirements
 * 3. SameSite cookie policies
 * 4. Session persistence in PWA context
 */

// PWA-compatible session configuration
function configure_pwa_session() {
    // Only configure if session hasn't started
    if (session_status() === PHP_SESSION_NONE) {
        // Detect if running in PWA context or production
        $is_pwa = isset($_SERVER['HTTP_USER_AGENT']) && 
                  (strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false || // WebView
                   isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                   (isset($_GET['utm_source']) && $_GET['utm_source'] === 'pwa') ||
                   $_SERVER['HTTP_HOST'] !== 'localhost'); // Production environment
        
        // Detect HTTPS
        $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                    $_SERVER['SERVER_PORT'] == 443 ||
                    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                    $_SERVER['HTTP_HOST'] !== 'localhost'; // Assume production uses HTTPS
        
        // Permanent session lifetime for PWA (10 years), shorter for localhost
        $lifetime = $is_pwa ? 315360000 : 54000; // 10 years for PWA/production, 15 hours for localhost
        
        // Configure session settings
        ini_set('session.gc_maxlifetime', $lifetime);
        ini_set('session.cookie_lifetime', $lifetime);
        
        // PWA-compatible cookie parameters
        $samesite = 'Lax'; // Default to Lax for better compatibility
        if ($is_pwa && $is_https) {
            $samesite = 'None'; // Use None only for HTTPS PWA
        }
        
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '', // Let browser determine domain
            'secure' => $is_https, // Use secure cookies on HTTPS
            'httponly' => true,
            'samesite' => $samesite
        ]);
        
        // Set session name to avoid conflicts
        session_name('ELECTROZOT_TECH_SESSION');
    }
}

// Enhanced session validation for PWA
function validate_pwa_session() {
    // Check if session exists
    if (!isset($_SESSION['t_id']) || empty($_SESSION['t_id'])) {
        return false;
    }
    
    // Additional validation for PWA context
    $tech_id = $_SESSION['t_id'];
    
    // Verify technician still exists and is active
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT t_id, t_status FROM tms_technician WHERE t_id = ? LIMIT 1");
    $stmt->bind_param('i', $tech_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_assoc();
    
    if (!$tech || $tech['t_status'] === 'Locked') {
        // Clear invalid session
        session_destroy();
        return false;
    }
    
    return true;
}

// PWA-specific login function
function pwa_technician_login($phone, $password, $remember_me = false) {
    global $mysqli;
    
    // Configure PWA session first
    configure_pwa_session();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Find technician
    $stmt = $mysqli->prepare("SELECT * FROM tms_technician WHERE t_phone = ? OR t_id_no = ? LIMIT 1");
    $stmt->bind_param('ss', $phone, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_assoc();
    
    if (!$tech) {
        return ['success' => false, 'message' => 'Mobile number not registered'];
    }
    
    // Check if locked
    if ($tech['t_status'] === 'Locked') {
        if ($tech['t_blocked_until'] && strtotime($tech['t_blocked_until']) > time()) {
            return ['success' => false, 'message' => 'Account temporarily locked'];
        } else {
            // Auto-unlock expired lock
            $mysqli->query("UPDATE tms_technician SET t_status = 'Available', t_blocked_until = NULL WHERE t_id = {$tech['t_id']}");
        }
    }
    
    // Verify password
    $password_valid = false;
    if (!empty($tech['t_pwd'])) {
        // Check if hashed
        if (password_get_info($tech['t_pwd'])['algo']) {
            $password_valid = password_verify($password, $tech['t_pwd']);
        } else {
            $password_valid = ($password === $tech['t_pwd']);
        }
    } else {
        // Legacy: password equals ID
        $password_valid = ($password === $tech['t_id_no']);
    }
    
    if (!$password_valid) {
        return ['success' => false, 'message' => 'Incorrect password'];
    }
    
    // Create session
    $_SESSION['t_id'] = $tech['t_id'];
    $_SESSION['t_name'] = $tech['t_name'];
    $_SESSION['t_id_no'] = $tech['t_id_no'];
    $_SESSION['pwa_login'] = true; // Mark as PWA login
    $_SESSION['login_time'] = time();
    
    // Regenerate session ID
    session_regenerate_id(true);
    
    // Set long-term cookie for PWA if remember me is checked
    if ($remember_me) {
        $cookie_lifetime = time() + 315360000; // 10 years
        setcookie(session_name(), session_id(), $cookie_lifetime, '/', '', 
                 (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), true);
    }
    
    return ['success' => true, 'message' => 'Login successful'];
}

// Check if technician is logged in (PWA-compatible)
function is_technician_logged_in() {
    configure_pwa_session();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return validate_pwa_session();
}

// Get current technician info
function get_current_technician() {
    if (!is_technician_logged_in()) {
        return null;
    }
    
    return [
        't_id' => $_SESSION['t_id'],
        't_name' => $_SESSION['t_name'],
        't_id_no' => $_SESSION['t_id_no']
    ];
}

// PWA logout function
function pwa_technician_logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Clear session data
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}
?>