<?php
/**
 * Central Database Configuration
 * Change these settings once for the entire project
 */

// Set timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

// ============================================
// TINYMCE CONFIGURATION
// ============================================

// TinyMCE API Key for rich text editor
$tinymce_api_key = "p06fobmdfwb9p9piooby6kip531y3o8cmmmvidr9cg8rdd09";

// ============================================
// DATABASE CONFIGURATION
// ============================================

// Detect environment and use appropriate settings
$is_localhost = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost');

if ($is_localhost) {
    // LOCAL DEVELOPMENT SETTINGS
    $dbuser = "root";
    $dbpass = "";
    $host = "localhost";
    $db = "electrozot_db";
} else {
    // PRODUCTION SETTINGS
    $dbuser = "u848820288_Mohit";
    $dbpass = "Moh2020@#@";
    $host = "localhost";
    $db = "u848820288_electrozot";
}

// ============================================
// DATABASE CONNECTION
// ============================================
$mysqli = new mysqli($host, $dbuser, $dbpass, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Database Connection Failed: " . $mysqli->connect_error);
}

// Set charset to UTF-8
$mysqli->set_charset("utf8mb4");

?>
