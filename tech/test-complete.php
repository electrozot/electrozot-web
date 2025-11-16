<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];

echo "<h2>Test Complete Booking</h2>";

// Get a pending booking
$query = "SELECT sb_id, sb_status FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status != 'Completed' LIMIT 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $booking = $result->fetch_object();
    echo "Found booking ID: " . $booking->sb_id . " with status: " . $booking->sb_status . "<br><br>";
    
    echo "<a href='complete-booking.php?id=" . $booking->sb_id . "&action=done' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Test Complete This Booking</a>";
} else {
    echo "No pending bookings found for testing.";
}

echo "<br><br><h3>Recent Bookings:</h3>";
$query2 = "SELECT sb_id, sb_status, sb_created_at FROM tms_service_booking WHERE sb_technician_id = ? ORDER BY sb_id DESC LIMIT 5";
$stmt2 = $mysqli->prepare($query2);
$stmt2->bind_param('i', $t_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Status</th><th>Created</th><th>Action</th></tr>";
while($row = $result2->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['sb_id'] . "</td>";
    echo "<td>" . $row['sb_status'] . "</td>";
    echo "<td>" . $row['sb_created_at'] . "</td>";
    echo "<td><a href='complete-booking.php?id=" . $row['sb_id'] . "&action=done'>Complete</a></td>";
    echo "</tr>";
}
echo "</table>";
?>
