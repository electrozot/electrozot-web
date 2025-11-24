<?php
/**
 * Session Configuration for Persistent Login
 * 
 * This file configures PHP sessions to last for 30 days,
 * so users don't need to log in repeatedly on the same device/browser.
 * 
 * Include this file BEFORE session_start() in all pages.
 */

// Set session cookie to last 30 days (2592000 seconds)
ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 2592000);

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 2592000,  // 30 days
    'path' => '/',
    'domain' => '',
    'secure' => false,      // Set to true if using HTTPS
    'httponly' => true,     // Prevent JavaScript access to session cookie
    'samesite' => 'Lax'     // CSRF protection
]);

// Optional: Set session save path if needed
// session_save_path('/path/to/sessions');
?>
