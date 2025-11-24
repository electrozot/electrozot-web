<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Test skill matching
$test_service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 1;

// Get service details
$service_query = "SELECT s_id, s_name, s_category FROM tms_service WHERE s_id = ?";
$stmt = $mysqli->prepare($service_query);
$stmt->bind_param('i', $test_service_id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();

if (!$service) {
    die("Service not found");
}

$service_name = $service['s_name'];
$service_category = $service['s_category'];

echo "<h2>Testing Skill Matching for: {$service_name}</h2>";
echo "<p><strong>Category:</strong> {$service_category}</p>";
echo "<hr>";

// Test 1: Check all technicians and their skills
echo "<h3>All Technicians and Their Skills:</h3>";
$all_techs = $mysqli->query("SELECT t_id, t_name, t_category, t_skills, t_current_bookings, t_booking_limit FROM tms_technician WHERE t_status != 'Inactive'");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Skills (t_skills column)</th><th>Capacity</th></tr>";
while ($tech = $all_techs->fetch_assoc()) {
    $skills_display = $tech['t_skills'] ? $tech['t_skills'] : '<span style="color:red;">EMPTY</span>';
    $capacity = "{$tech['t_current_bookings']}/{$tech['t_booking_limit']}";
    echo "<tr>";
    echo "<td>{$tech['t_id']}</td>";
    echo "<td>{$tech['t_name']}</td>";
    echo "<td>{$tech['t_category']}</td>";
    echo "<td>{$skills_display}</td>";
    echo "<td>{$capacity}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<hr>";

// Test 2: FIND_IN_SET matching
echo "<h3>Technicians Matching with FIND_IN_SET (Exact Match):</h3>";
$exact_match_query = "SELECT t_id, t_name, t_skills, 
                             FIND_IN_SET(?, t_skills) as find_result
                      FROM tms_technician 
                      WHERE FIND_IN_SET(?, t_skills) > 0
                      AND t_status != 'Inactive'";
$stmt = $mysqli->prepare($exact_match_query);
$stmt->bind_param('ss', $service_name, $service_name);
$stmt->execute();
$exact_result = $stmt->get_result();

if ($exact_result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Skills</th><th>FIND_IN_SET Result</th></tr>";
    while ($tech = $exact_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$tech['t_id']}</td>";
        echo "<td>{$tech['t_name']}</td>";
        echo "<td>{$tech['t_skills']}</td>";
        echo "<td>{$tech['find_result']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ No technicians found with exact skill match using FIND_IN_SET</p>";
}
echo "<hr>";

// Test 3: LIKE matching (partial)
echo "<h3>Technicians Matching with LIKE (Partial Match):</h3>";
$like_match_query = "SELECT t_id, t_name, t_skills
                     FROM tms_technician 
                     WHERE t_skills LIKE CONCAT('%', ?, '%')
                     AND t_status != 'Inactive'";
$stmt = $mysqli->prepare($like_match_query);
$stmt->bind_param('s', $service_name);
$stmt->execute();
$like_result = $stmt->get_result();

if ($like_result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Skills</th></tr>";
    while ($tech = $like_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$tech['t_id']}</td>";
        echo "<td>{$tech['t_name']}</td>";
        echo "<td>{$tech['t_skills']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ No technicians found with partial match using LIKE</p>";
}
echo "<hr>";

// Test 4: Category matching
echo "<h3>Technicians Matching by Category:</h3>";
$category_match_query = "SELECT t_id, t_name, t_category, t_skills
                         FROM tms_technician 
                         WHERE t_category = ?
                         AND t_status != 'Inactive'";
$stmt = $mysqli->prepare($category_match_query);
$stmt->bind_param('s', $service_category);
$stmt->execute();
$category_result = $stmt->get_result();

if ($category_result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Skills</th></tr>";
    while ($tech = $category_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$tech['t_id']}</td>";
        echo "<td>{$tech['t_name']}</td>";
        echo "<td>{$tech['t_category']}</td>";
        echo "<td>{$tech['t_skills']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ No technicians found with category match</p>";
}
echo "<hr>";

// Test 5: What the ultimate matcher returns
echo "<h3>What Ultimate Matcher Returns:</h3>";
require_once('vendor/inc/ultimate-technician-matcher.php');
$matched_techs = getSmartAvailableTechnicians($mysqli, $test_service_id, date('Y-m-d'), '10:00:00', null);

if (!empty($matched_techs)) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Match Type</th><th>Available</th><th>Slots</th><th>Message</th></tr>";
    foreach ($matched_techs as $tech) {
        $available = $tech['is_available'] ? '✅ Yes' : '❌ No';
        echo "<tr>";
        echo "<td>{$tech['t_id']}</td>";
        echo "<td>{$tech['t_name']}</td>";
        echo "<td>{$tech['match_type']}</td>";
        echo "<td>{$available}</td>";
        echo "<td>{$tech['available_slots']}</td>";
        echo "<td>{$tech['slot_message']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ Ultimate matcher returned NO technicians</p>";
}

echo "<hr>";
echo "<h3>Test Another Service:</h3>";
$services = $mysqli->query("SELECT s_id, s_name FROM tms_service WHERE s_status = 'Active' LIMIT 10");
echo "<ul>";
while ($svc = $services->fetch_assoc()) {
    echo "<li><a href='?service_id={$svc['s_id']}'>{$svc['s_name']}</a></li>";
}
echo "</ul>";
?>
