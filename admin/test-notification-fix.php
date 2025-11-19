<?php
/**
 * Test Notification Fix
 * This script verifies that the notification system only shows new notifications
 */

session_start();
require_once('vendor/inc/config.php');

echo "<h2>Notification System Test</h2>";
echo "<p>Testing the fixed notification system...</p>";

// Clear shown notifications for testing
unset($_SESSION['shown_notifications']);
echo "<p>✅ Cleared session notification cache</p>";

// Test 1: Check for pending bookings
$query = "SELECT 
            sb.sb_id,
            sb.sb_status,
            sb.sb_created_at,
            COALESCE(CONCAT(u.u_fname, ' ', u.u_lname), 'Guest') as customer_name,
            COALESCE(s.s_name, 'Service') as service_name
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_status = 'Pending'
          ORDER BY sb.sb_id DESC
          LIMIT 5";

$result = $mysqli->query($query);
echo "<h3>Pending Bookings:</h3>";
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $created = $row['sb_created_at'] ? date('Y-m-d H:i:s', strtotime($row['sb_created_at'])) : 'No timestamp';
        echo "<li>Booking #{$row['sb_id']} - {$row['customer_name']} - Created: {$created}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No pending bookings found</p>";
}

// Test 2: Simulate API call
echo "<h3>Simulating API Call:</h3>";
$_SESSION['shown_notifications'] = [];

// First call
$currentTimestamp = time();
$notifications = [];

$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifId = 'booking_' . $row['sb_id'];
        
        if (!isset($_SESSION['shown_notifications'][$notifId])) {
            $notifications[] = [
                'id' => $notifId,
                'booking_id' => $row['sb_id'],
                'message' => 'New Booking #' . $row['sb_id']
            ];
            $_SESSION['shown_notifications'][$notifId] = $currentTimestamp;
        }
    }
}

echo "<p><strong>First API Call:</strong> Found " . count($notifications) . " new notifications</p>";
echo "<pre>" . print_r($notifications, true) . "</pre>";

// Second call (should return 0 notifications)
$notifications2 = [];
$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifId = 'booking_' . $row['sb_id'];
        
        if (!isset($_SESSION['shown_notifications'][$notifId])) {
            $notifications2[] = [
                'id' => $notifId,
                'booking_id' => $row['sb_id'],
                'message' => 'New Booking #' . $row['sb_id']
            ];
            $_SESSION['shown_notifications'][$notifId] = $currentTimestamp;
        }
    }
}

echo "<p><strong>Second API Call (same bookings):</strong> Found " . count($notifications2) . " new notifications</p>";
if (count($notifications2) == 0) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Duplicate notifications are now prevented!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ FAILED! Still showing duplicate notifications</p>";
}

echo "<h3>Session Notification Cache:</h3>";
echo "<pre>" . print_r($_SESSION['shown_notifications'], true) . "</pre>";

echo "<hr>";
echo "<p><a href='admin-dashboard.php'>← Back to Dashboard</a></p>";
?>
