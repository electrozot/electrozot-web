<?php
/**
 * Central Database Configuration
 * Change these settings once for the entire project
 */

// Set timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

// ============================================
// DATABASE CONFIGURATION
// ============================================

// LOCAL DEVELOPMENT SETTINGS
$dbuser = "root";
$dbpass = "";
$host = "localhost";
$db = "electrozot_db";

// PRODUCTION SETTINGS (Uncomment when deploying to production)
// $dbuser = "u848820288_Mohit";
// $dbpass = "Moh2020@#@";
// $host = "localhost";
// $db = "u848820288_electrozot";

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
