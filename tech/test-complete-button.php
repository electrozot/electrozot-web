<?php
session_start();
include('../admin/vendor/inc/config.php');

echo "<h2>Complete Button Debug Test</h2>";
echo "<hr>";

// Check if technician is logged in
if(!isset($_SESSION['t_id'])){
    echo "<p style='color:red;'>❌ NOT LOGGED IN - Session t_id not found</p>";
    echo "<p>Please login first at: <a href='index.php'>tech/index.php</a></p>";
    exit();
}

$t_id = $_SESSION['t_id'];
echo "<p style='color:green;'>✅ Logged in as Technician ID: <strong>$t_id</strong></p>";

// Get technician info
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$tech_stmt = $mysqli->prepare($tech_query);
$tech_stmt->bind_param('i', $t_id);
$tech_stmt->execute();
$tech_result = $tech_stmt->get_result();
$tech = $tech_result->fetch_object();

if($tech){
    echo "<p>✅ Technician Name: <strong>" . htmlspecialchars($tech->t_name) . "</strong></p>";
    echo "<p>✅ Status: <strong>" . $tech->t_status . "</strong></p>";
} else {
    echo "<p style='color:red;'>❌ Technician record not found</p>";
}

echo "<hr>";

// Get active bookings for this technician
$booking_query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name 
                  FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                  WHERE sb.sb_technician_id = ?
                  AND sb.sb_status NOT IN ('Completed', 'Not Done', 'Cancelled')
                  ORDER BY sb.sb_created_at DESC";
$booking_stmt = $mysqli->prepare($booking_query);
$booking_stmt->bind_param('i', $t_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();

echo "<h3>Active Bookings (Can be completed):</h3>";

if($booking_result->num_rows == 0){
    echo "<p style='color:orange;'>⚠️ No active bookings found for this technician</p>";
    echo "<p>Status must be: Pending, Approved, Assigned, or In Progress</p>";
} else {
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'>";
    echo "<th>Booking ID</th>";
    echo "<th>Customer</th>";
    echo "<th>Service</th>";
    echo "<th>Status</th>";
    echo "<th>Test Complete Button</th>";
    echo "</tr>";
    
    while($booking = $booking_result->fetch_object()){
        echo "<tr>";
        echo "<td>#" . $booking->sb_id . "</td>";
        echo "<td>" . htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname) . "</td>";
        echo "<td>" . htmlspecialchars($booking->s_name) . "</td>";
        echo "<td><strong>" . $booking->sb_status . "</strong></td>";
        echo "<td>";
        echo "<a href='complete-booking.php?id=" . $booking->sb_id . "&action=done' style='padding:8px 15px;background:#10b981;color:white;text-decoration:none;border-radius:5px;margin-right:5px;'>✅ Done</a>";
        echo "<a href='complete-booking.php?id=" . $booking->sb_id . "&action=not-done' style='padding:8px 15px;background:#ef4444;color:white;text-decoration:none;border-radius:5px;'>❌ Not Done</a>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<hr>";
echo "<h3>Check complete-booking.php file:</h3>";

if(file_exists('complete-booking.php')){
    echo "<p style='color:green;'>✅ File exists: complete-booking.php</p>";
    echo "<p>File size: " . filesize('complete-booking.php') . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime('complete-booking.php')) . "</p>";
} else {
    echo "<p style='color:red;'>❌ File NOT found: complete-booking.php</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php' style='padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>← Back to Dashboard</a></p>";
?>
