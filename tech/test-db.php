<?php
session_start();
include('../admin/vendor/inc/config.php');

echo "<h2>Database Column Check</h2>";

// Check if columns exist
$check_query = "SHOW COLUMNS FROM tms_service_booking LIKE 'sb_service_image'";
$result = $mysqli->query($check_query);
echo "sb_service_image exists: " . ($result->num_rows > 0 ? "YES" : "NO") . "<br>";

$check_query = "SHOW COLUMNS FROM tms_service_booking LIKE 'sb_bill_image'";
$result = $mysqli->query($check_query);
echo "sb_bill_image exists: " . ($result->num_rows > 0 ? "YES" : "NO") . "<br>";

$check_query = "SHOW COLUMNS FROM tms_service_booking LIKE 'sb_amount_charged'";
$result = $mysqli->query($check_query);
echo "sb_amount_charged exists: " . ($result->num_rows > 0 ? "YES" : "NO") . "<br>";

$check_query = "SHOW COLUMNS FROM tms_service_booking LIKE 'sb_completed_at'";
$result = $mysqli->query($check_query);
echo "sb_completed_at exists: " . ($result->num_rows > 0 ? "YES" : "NO") . "<br>";

echo "<h2>Booking Status Check</h2>";

// Check booking statuses
$status_query = "SELECT sb_id, sb_status, sb_created_at FROM tms_service_booking ORDER BY sb_id DESC LIMIT 10";
$result = $mysqli->query($status_query);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Status</th><th>Created</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['sb_id'] . "</td>";
    echo "<td>" . $row['sb_status'] . "</td>";
    echo "<td>" . $row['sb_created_at'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Completed Bookings Count</h2>";
$count_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_status = 'Completed'";
$result = $mysqli->query($count_query);
$row = $result->fetch_assoc();
echo "Total Completed: " . $row['count'];
?>
