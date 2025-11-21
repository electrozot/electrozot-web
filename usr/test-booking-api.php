<?php
/**
 * Test file to check if booking status API works
 */
session_start();

// Simulate logged in user (replace with actual user ID for testing)
if(!isset($_SESSION['u_id'])) {
    echo "Please login first to test the API<br>";
    echo "<a href='index.php'>Go to Login</a>";
    exit();
}

echo "<h2>Testing Booking Status API</h2>";
echo "<p>User ID: " . $_SESSION['u_id'] . "</p>";

// Test the API
$api_url = 'get-all-bookings-status.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

echo "<h3>API Response:</h3>";
echo "<pre>";
$data = json_decode($response, true);
print_r($data);
echo "</pre>";

if(isset($data['success']) && $data['success']) {
    echo "<p style='color: green;'>✓ API is working correctly!</p>";
    echo "<p>Total bookings: " . count($data['bookings']) . "</p>";
    echo "<p>Active bookings: " . $data['active_count'] . "</p>";
    echo "<p>Completed bookings: " . $data['completed_count'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ API returned an error</p>";
    if(isset($data['error'])) {
        echo "<p>Error: " . $data['error'] . "</p>";
    }
}

echo "<br><a href='user-dashboard.php'>Back to Dashboard</a>";
?>
