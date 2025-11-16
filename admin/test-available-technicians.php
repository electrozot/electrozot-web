<?php
session_start();
include('vendor/inc/config.php');

echo "<h1>🔍 Available Technicians Test</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
th { background: #667eea; color: white; }
.available { color: #10b981; font-weight: bold; }
.busy { color: #ef4444; font-weight: bold; }
.section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
</style>";

// 1. Check if t_is_available column exists
echo "<div class='section'>";
echo "<h2>1. Database Column Check</h2>";
$check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_is_available'");
if($check->num_rows > 0) {
    echo "<p class='available'>✓ Column 't_is_available' exists</p>";
} else {
    echo "<p class='busy'>✗ Column 't_is_available' missing - Creating it now...</p>";
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_is_available TINYINT(1) DEFAULT 1");
    echo "<p class='available'>✓ Column created</p>";
}
echo "</div>";

// 2. Show all technicians with availability status
echo "<div class='section'>";
echo "<h2>2. All Technicians Status</h2>";
$query = "SELECT t_id, t_name, t_id_no, t_category, t_status, t_is_available, t_current_booking_id FROM tms_technician ORDER BY t_name";
$result = $mysqli->query($query);

echo "<table>";
echo "<tr><th>ID</th><th>Name</th><th>ID No</th><th>Category</th><th>Old Status</th><th>New Available</th><th>Current Booking</th><th>Status</th></tr>";

$available_count = 0;
$busy_count = 0;

while($tech = $result->fetch_assoc()) {
    $is_available = isset($tech['t_is_available']) && $tech['t_is_available'] == 1;
    $old_available = isset($tech['t_status']) && $tech['t_status'] == 'Available';
    
    if($is_available || $old_available) {
        $available_count++;
        $status_class = 'available';
        $status_text = '✓ AVAILABLE';
    } else {
        $busy_count++;
        $status_class = 'busy';
        $status_text = '✗ BUSY';
    }
    
    echo "<tr>";
    echo "<td>" . $tech['t_id'] . "</td>";
    echo "<td>" . htmlspecialchars($tech['t_name']) . "</td>";
    echo "<td>" . htmlspecialchars($tech['t_id_no']) . "</td>";
    echo "<td>" . htmlspecialchars($tech['t_category']) . "</td>";
    echo "<td>" . (isset($tech['t_status']) ? $tech['t_status'] : 'N/A') . "</td>";
    echo "<td>" . (isset($tech['t_is_available']) ? ($tech['t_is_available'] ? 'Yes' : 'No') : 'N/A') . "</td>";
    echo "<td>" . (isset($tech['t_current_booking_id']) && $tech['t_current_booking_id'] ? $tech['t_current_booking_id'] : 'None') . "</td>";
    echo "<td class='$status_class'>$status_text</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><strong>Summary:</strong> $available_count Available, $busy_count Busy</p>";
echo "</div>";

// 3. Test the get-technicians.php endpoint
echo "<div class='section'>";
echo "<h2>3. Test Get Technicians Endpoint</h2>";

echo "<h3>Test 1: Get all available technicians (no parameters)</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/vendor/inc/get-technicians.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<div style='background:#f8f9fa;padding:10px;border:1px solid #ddd;border-radius:5px;'>";
echo "<strong>Response:</strong><br>";
echo htmlspecialchars($response);
echo "</div>";

echo "<h3>Test 2: Get technicians by category (Electrician)</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/vendor/inc/get-technicians.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['category' => 'Electrician']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<div style='background:#f8f9fa;padding:10px;border:1px solid #ddd;border-radius:5px;'>";
echo "<strong>Response:</strong><br>";
echo htmlspecialchars($response);
echo "</div>";

echo "</div>";

// 4. Quick fix button
echo "<div class='section'>";
echo "<h2>4. Quick Fix</h2>";

if(isset($_GET['fix'])) {
    echo "<p>Running fixes...</p>";
    
    // Set all technicians to available
    $mysqli->query("UPDATE tms_technician SET t_is_available = 1, t_current_booking_id = NULL");
    echo "<p class='available'>✓ All technicians set to available</p>";
    
    // Update old status column
    $mysqli->query("UPDATE tms_technician SET t_status = 'Available'");
    echo "<p class='available'>✓ Old status column updated</p>";
    
    echo "<p><a href='test-available-technicians.php'>Refresh to see changes</a></p>";
} else {
    echo "<p><a href='?fix=1' style='padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>Set All Technicians to Available</a></p>";
}

echo "</div>";

// 5. Test reassignment form
echo "<div class='section'>";
echo "<h2>5. Test Reassignment Form</h2>";
echo "<form method='POST' action='admin-rejected-bookings.php'>";
echo "<input type='hidden' name='booking_id' value='1'>";
echo "<label>Select Technician:</label><br>";
echo "<select name='new_tech_id' style='width:100%;padding:10px;margin:10px 0;'>";

$query = "SELECT t_id, t_name, t_category FROM tms_technician WHERE t_is_available = 1 OR t_status = 'Available' ORDER BY t_name";
$result = $mysqli->query($query);

if($result->num_rows > 0) {
    echo "<option value=''>-- Select Technician --</option>";
    while($tech = $result->fetch_assoc()) {
        echo "<option value='" . $tech['t_id'] . "'>" . htmlspecialchars($tech['t_name']) . " (" . $tech['t_category'] . ")</option>";
    }
} else {
    echo "<option>No available technicians</option>";
}

echo "</select><br>";
echo "<button type='submit' name='reassign' style='padding:10px 20px;background:#10b981;color:white;border:none;border-radius:5px;cursor:pointer;'>Test Reassign</button>";
echo "</form>";
echo "</div>";

echo "<div class='section'>";
echo "<p><a href='admin-rejected-bookings.php'>← Back to Rejected Bookings</a></p>";
echo "<p><a href='admin-dashboard.php'>← Back to Dashboard</a></p>";
echo "</div>";
?>
