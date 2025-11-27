<?php
/**
 * Quick Fix: Clear corrupted last_page session
 * 
 * Run this file once if you're stuck on a JSON page
 * Then try logging in again
 */

session_start();

// Clear the last_page session variable
if(isset($_SESSION['last_page'])) {
    $old_page = $_SESSION['last_page'];
    unset($_SESSION['last_page']);
    echo "✅ Cleared last_page session: " . htmlspecialchars($old_page) . "<br>";
} else {
    echo "ℹ️ No last_page session found<br>";
}

echo "<br>";
echo "✅ Session fixed!<br>";
echo "<br>";
echo "<a href='admin-dashboard.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";
echo " ";
echo "<a href='index.php' style='padding: 10px 20px; background: #764ba2; color: white; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
?>
