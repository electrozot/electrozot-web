<?php
/**
 * TEST MATCHER FIX - Quick test to see if technicians are showing
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Matcher Fix</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #667eea; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; }
        .pass { background: #ecfdf5; color: #065f46; }
        .fail { background: #fef2f2; color: #991b1b; }
        .btn { padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧪 Test Matcher Fix</h2>
        
        <?php
        require_once('vendor/inc/ultimate-technician-matcher.php');
        
        // Get first service
        $service_query = "SELECT s_id, s_name, s_category FROM tms_service WHERE s_status = 'Active' LIMIT 5";
        $services = $mysqli->query($service_query);
        
        echo "<h3>Testing with Services:</h3>";
        
        while ($service = $services->fetch_assoc()) {
            echo "<div style='background: #f9fafb; padding: 15px; margin: 15px 0; border-radius: 8px;'>";
            echo "<h4>{$service['s_name']} ({$service['s_category']})</h4>";
            
            $test_date = date('Y-m-d');
            $test_time = '10:00:00';
            
            $available = getSmartAvailableTechnicians($mysqli, $service['s_id'], $test_date, $test_time);
            
            if (empty($available)) {
                echo "<p class='fail'>❌ No technicians found</p>";
            } else {
                echo "<p class='pass'>✅ Found " . count($available) . " technician(s)</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>Category</th><th>Match Type</th><th>Available?</th><th>Capacity</th></tr>";
                foreach ($available as $tech) {
                    $class = $tech['is_available'] ? 'pass' : 'fail';
                    echo "<tr class='$class'>";
                    echo "<td>{$tech['t_name']}</td>";
                    echo "<td>{$tech['t_category']}</td>";
                    echo "<td>{$tech['match_type']}</td>";
                    echo "<td>" . ($tech['is_available'] ? '✅ YES' : '❌ NO') . "</td>";
                    echo "<td>{$tech['t_current_bookings']}/{$tech['t_booking_limit']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            echo "</div>";
        }
        ?>
        
        <hr>
        <h3>Quick Actions:</h3>
        <a href="admin-assign-technician.php" class="btn">Test Real Assignment</a>
        <a href="admin-manage-technician.php" class="btn">Manage Technicians</a>
        <a href="admin-dashboard.php" class="btn">Dashboard</a>
    </div>
</body>
</html>
