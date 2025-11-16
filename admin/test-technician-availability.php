<?php
session_start();
include('vendor/inc/config.php');

echo "<h1>🔍 Technician Availability Test</h1>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
th { background: #667eea; color: white; }
.available { background: #d4edda; color: #155724; font-weight: bold; }
.busy { background: #f8d7da; color: #721c24; font-weight: bold; }
.section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.btn { padding: 8px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
.btn-success { background: #10b981; }
.btn-danger { background: #ef4444; }
</style>";

// Handle actions
if(isset($_GET['action'])) {
    if($_GET['action'] == 'free_all') {
        $mysqli->query("UPDATE tms_technician SET t_is_available = 1, t_current_booking_id = NULL");
        echo "<div class='section' style='background:#d4edda;color:#155724;'>✅ All technicians set to AVAILABLE</div>";
    } elseif($_GET['action'] == 'assign' && isset($_GET['tech_id']) && isset($_GET['booking_id'])) {
        $tech_id = intval($_GET['tech_id']);
        $booking_id = intval($_GET['booking_id']);
        $mysqli->query("UPDATE tms_technician SET t_is_available = 0, t_current_booking_id = $booking_id WHERE t_id = $tech_id");
        echo "<div class='section' style='background:#fff3cd;color:#856404;'>⚠️ Technician #$tech_id assigned to booking #$booking_id (now BUSY)</div>";
    }
}

// Show all technicians with their status
echo "<div class='section'>";
echo "<h2>All Technicians Status</h2>";

$query = "SELECT t_id, t_name, t_id_no, t_category, t_status, t_is_available, t_current_booking_id 
          FROM tms_technician 
          ORDER BY t_name";
$result = $mysqli->query($query);

echo "<table>";
echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Available?</th>
        <th>Current Booking</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>";

$available_count = 0;
$busy_count = 0;

while($tech = $result->fetch_assoc()) {
    $is_available = ($tech['t_is_available'] == 1 && ($tech['t_current_booking_id'] == NULL || $tech['t_current_booking_id'] == 0));
    
    if($is_available) {
        $available_count++;
        $status_class = 'available';
        $status_text = '✅ AVAILABLE';
    } else {
        $busy_count++;
        $status_class = 'busy';
        $status_text = '🔒 BUSY';
    }
    
    echo "<tr>";
    echo "<td>" . $tech['t_id'] . "</td>";
    echo "<td><strong>" . htmlspecialchars($tech['t_name']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($tech['t_category']) . "</td>";
    echo "<td>" . ($tech['t_is_available'] ? 'Yes' : 'No') . "</td>";
    echo "<td>" . ($tech['t_current_booking_id'] ? '<strong>Booking #' . $tech['t_current_booking_id'] . '</strong>' : '-') . "</td>";
    echo "<td class='$status_class'>$status_text</td>";
    echo "<td>";
    if($is_available) {
        echo "<a href='?action=assign&tech_id=" . $tech['t_id'] . "&booking_id=999' class='btn btn-danger'>Test: Make Busy</a>";
    } else {
        echo "<a href='?action=free_all' class='btn btn-success'>Free Up</a>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p><strong>Summary:</strong> $available_count Available (can be assigned) | $busy_count Busy (cannot be assigned)</p>";
echo "</div>";

// Test the get-technicians endpoint
echo "<div class='section'>";
echo "<h2>Test Reassignment Dropdown</h2>";
echo "<p>This simulates what admin sees when reassigning a booking:</p>";

echo "<h3>Test 1: Get technicians for 'Basic Electrical Work'</h3>";
echo "<select style='width:100%;padding:10px;font-size:14px;'>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/vendor/inc/get-technicians.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['service_name' => 'Basic Electrical Work', 'category' => 'Electrician']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
echo "</select>";

echo "<h3>Test 2: Get all available technicians</h3>";
echo "<select style='width:100%;padding:10px;font-size:14px;'>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/vendor/inc/get-technicians.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
echo "</select>";

echo "</div>";

// Show which technicians would appear in dropdown
echo "<div class='section'>";
echo "<h2>Technicians Available for Reassignment</h2>";
echo "<p>These technicians will appear in the reassignment dropdown:</p>";

$available_query = "SELECT t_id, t_name, t_category, t_current_booking_id 
                    FROM tms_technician 
                    WHERE (t_is_available = 1 OR t_status = 'Available') 
                    AND (t_current_booking_id IS NULL OR t_current_booking_id = 0)
                    ORDER BY t_name";
$available_result = $mysqli->query($available_query);

if($available_result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Status</th></tr>";
    while($tech = $available_result->fetch_assoc()) {
        echo "<tr class='available'>";
        echo "<td>" . $tech['t_id'] . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_name']) . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_category']) . "</td>";
        echo "<td>✅ Will appear in dropdown</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:#ef4444;font-weight:bold;'>❌ NO TECHNICIANS AVAILABLE - All are busy with bookings!</p>";
}

echo "</div>";

// Show busy technicians
echo "<div class='section'>";
echo "<h2>Busy Technicians (NOT Available for Reassignment)</h2>";
echo "<p>These technicians will NOT appear in the reassignment dropdown:</p>";

$busy_query = "SELECT t_id, t_name, t_category, t_current_booking_id 
               FROM tms_technician 
               WHERE t_is_available = 0 
               OR (t_current_booking_id IS NOT NULL AND t_current_booking_id != 0)
               ORDER BY t_name";
$busy_result = $mysqli->query($busy_query);

if($busy_result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Current Booking</th><th>Status</th></tr>";
    while($tech = $busy_result->fetch_assoc()) {
        echo "<tr class='busy'>";
        echo "<td>" . $tech['t_id'] . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_name']) . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_category']) . "</td>";
        echo "<td>Booking #" . $tech['t_current_booking_id'] . "</td>";
        echo "<td>🔒 Will NOT appear in dropdown</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:#10b981;font-weight:bold;'>✅ No busy technicians - All are available!</p>";
}

echo "</div>";

// Quick actions
echo "<div class='section'>";
echo "<h2>Quick Actions</h2>";
echo "<a href='?action=free_all' class='btn btn-success'>Free Up All Technicians</a>";
echo "<a href='admin-rejected-bookings.php' class='btn'>Go to Rejected Bookings</a>";
echo "<a href='admin-dashboard.php' class='btn'>Go to Dashboard</a>";
echo "</div>";
?>
