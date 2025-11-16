<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];

echo "<h2>Debug Complete Booking System</h2>";
echo "<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{border:1px solid #ddd;padding:10px;text-align:left;} th{background:#667eea;color:white;} .success{color:#10b981;font-weight:bold;} .error{color:#ef4444;font-weight:bold;}</style>";

// Check if columns exist
echo "<h3>1. Database Columns Check</h3>";
echo "<table><tr><th>Column</th><th>Status</th></tr>";

$columns = ['sb_service_image', 'sb_bill_image', 'sb_amount_charged', 'sb_completed_at', 'sb_not_done_reason', 'sb_not_done_at'];
foreach($columns as $col) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$col'");
    $status = $check->num_rows > 0 ? "<span class='success'>EXISTS</span>" : "<span class='error'>MISSING</span>";
    echo "<tr><td>$col</td><td>$status</td></tr>";
}
echo "</table>";

// Check technician columns
echo "<h3>2. Technician Availability Columns</h3>";
echo "<table><tr><th>Column</th><th>Status</th></tr>";
$tech_cols = ['t_is_available', 't_current_booking_id'];
foreach($tech_cols as $col) {
    $check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE '$col'");
    $status = $check->num_rows > 0 ? "<span class='success'>EXISTS</span>" : "<span class='error'>MISSING</span>";
    echo "<tr><td>$col</td><td>$status</td></tr>";
}
echo "</table>";

// Check current technician status
echo "<h3>3. Your Technician Status</h3>";
$tech_query = "SELECT t_id, t_name, t_is_available, t_current_booking_id FROM tms_technician WHERE t_id = ?";
$stmt = $mysqli->prepare($tech_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$tech_result = $stmt->get_result();
if($tech_result->num_rows > 0) {
    $tech = $tech_result->fetch_assoc();
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>Technician ID</td><td>" . $tech['t_id'] . "</td></tr>";
    echo "<tr><td>Name</td><td>" . $tech['t_name'] . "</td></tr>";
    echo "<tr><td>Available</td><td>" . ($tech['t_is_available'] ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "</td></tr>";
    echo "<tr><td>Current Booking</td><td>" . ($tech['t_current_booking_id'] ? $tech['t_current_booking_id'] : "None") . "</td></tr>";
    echo "</table>";
}

// Check bookings
echo "<h3>4. Your Recent Bookings</h3>";
$booking_query = "SELECT sb_id, sb_status, sb_service_image, sb_bill_image, sb_amount_charged, sb_completed_at, sb_not_done_reason, sb_created_at 
                  FROM tms_service_booking 
                  WHERE sb_technician_id = ? 
                  ORDER BY sb_id DESC LIMIT 5";
$stmt = $mysqli->prepare($booking_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>ID</th><th>Status</th><th>Service Img</th><th>Bill Img</th><th>Amount</th><th>Completed At</th><th>Not Done Reason</th><th>Action</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['sb_id'] . "</td>";
    echo "<td><strong>" . $row['sb_status'] . "</strong></td>";
    echo "<td>" . ($row['sb_service_image'] ? "✓" : "-") . "</td>";
    echo "<td>" . ($row['sb_bill_image'] ? "✓" : "-") . "</td>";
    echo "<td>" . ($row['sb_amount_charged'] ? "₹" . $row['sb_amount_charged'] : "-") . "</td>";
    echo "<td>" . ($row['sb_completed_at'] ? $row['sb_completed_at'] : "-") . "</td>";
    echo "<td>" . ($row['sb_not_done_reason'] ? substr($row['sb_not_done_reason'], 0, 30) . "..." : "-") . "</td>";
    echo "<td>";
    if($row['sb_status'] != 'Completed' && $row['sb_status'] != 'Not Done') {
        echo "<a href='complete-booking.php?id=" . $row['sb_id'] . "&action=done' style='padding:5px 10px;background:#10b981;color:white;text-decoration:none;border-radius:3px;margin-right:5px;'>Complete</a>";
        echo "<a href='complete-booking.php?id=" . $row['sb_id'] . "&action=not-done' style='padding:5px 10px;background:#ef4444;color:white;text-decoration:none;border-radius:3px;'>Not Done</a>";
    } else {
        echo "<span style='color:#999;'>Locked</span>";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

// Test form submission
echo "<h3>5. Test Complete Booking Form</h3>";
$test_booking = $mysqli->query("SELECT sb_id FROM tms_service_booking WHERE sb_technician_id = $t_id AND sb_status NOT IN ('Completed', 'Not Done') LIMIT 1");
if($test_booking->num_rows > 0) {
    $test = $test_booking->fetch_assoc();
    echo "<p>Test with Booking ID: <strong>" . $test['sb_id'] . "</strong></p>";
    echo "<form method='POST' action='complete-booking.php?id=" . $test['sb_id'] . "&action=done' enctype='multipart/form-data' style='border:2px solid #ddd;padding:20px;border-radius:5px;'>";
    echo "<p><label>Service Image: <input type='file' name='service_image' accept='image/*' required></label></p>";
    echo "<p><label>Bill Image: <input type='file' name='bill_image' accept='image/*' required></label></p>";
    echo "<p><label>Amount: <input type='number' name='amount_charged' step='0.01' min='1' placeholder='Enter amount' required></label></p>";
    echo "<p><button type='submit' name='mark_done' style='padding:10px 20px;background:#10b981;color:white;border:none;border-radius:5px;cursor:pointer;'>Submit Test</button></p>";
    echo "</form>";
} else {
    echo "<p style='color:#999;'>No pending bookings available for testing.</p>";
}

// Check uploads folder
echo "<h3>6. Upload Folders Check</h3>";
echo "<table><tr><th>Folder</th><th>Status</th><th>Writable</th></tr>";
$folders = ['../uploads/service_images/', '../uploads/bill_images/'];
foreach($folders as $folder) {
    $exists = file_exists($folder);
    $writable = is_writable($folder);
    echo "<tr>";
    echo "<td>$folder</td>";
    echo "<td>" . ($exists ? "<span class='success'>EXISTS</span>" : "<span class='error'>MISSING</span>") . "</td>";
    echo "<td>" . ($writable ? "<span class='success'>WRITABLE</span>" : "<span class='error'>NOT WRITABLE</span>") . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><br>";
echo "<a href='dashboard.php' style='padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>Back to Dashboard</a>";
?>
