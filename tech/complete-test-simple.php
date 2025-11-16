<?php
// Simple test - no session, no includes
echo "<!DOCTYPE html><html><head><title>Test</title></head><body>";
echo "<h1>Simple Test Page</h1>";
echo "<p>If you see this, PHP is working.</p>";

// Test GET parameters
if(isset($_GET['id'])){
    echo "<p>Booking ID received: " . htmlspecialchars($_GET['id']) . "</p>";
}

if(isset($_GET['action'])){
    echo "<p>Action received: " . htmlspecialchars($_GET['action']) . "</p>";
}

echo "<hr>";
echo "<p><a href='complete-test-simple.php?id=123&action=done'>Test Link with Parameters</a></p>";
echo "</body></html>";
?>
