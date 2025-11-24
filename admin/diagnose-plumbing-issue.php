<?php
/**
 * Diagnose Plumbing Technician Issue
 * Check why plumbing technician is not showing
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnose Plumbing Issue</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #667eea; }
        .section { background: #f9fafb; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .pass { border-left-color: #10b981; background: #ecfdf5; }
        .fail { border-left-color: #ef4444; background: #fef2f2; }
        .warning { border-left-color: #f59e0b; background: #fffbeb; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; font-weight: bold; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Plumbing Technician Diagnostic</h2>
        <p>Checking why plumbing technician is not showing for plumbing bookings...</p>

        <?php
        // STEP 1: Check plumbing services
        echo "<div class='section'>";
        echo "<h3>STEP 1: Plumbing Services in Database</h3>";
        
        $service_query = "SELECT s_id, s_name, s_category, s_subcategory, s_status 
                         FROM tms_service 
                         WHERE s_category LIKE '%plumb%' OR s_name LIKE '%plumb%'
                         ORDER BY s_id";
        $services = $mysqli->query($service_query);
        
        if ($services->num_rows > 0) {
            echo "<p class='pass'>✅ Found " . $services->num_rows . " plumbing service(s)</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Service Name</th><th>Category</th><th>Subcategory</th><th>Status</th></tr>";
            while ($service = $services->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$service['s_id']}</td>";
                echo "<td><strong>{$service['s_name']}</strong></td>";
                echo "<td>{$service['s_category']}</td>";
                echo "<td>{$service['s_subcategory']}</td>";
                echo "<td>{$service['s_status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='fail'>❌ No plumbing services found!</p>";
        }
        echo "</div>";

        // STEP 2: Check plumbing technicians
        echo "<div class='section'>";
        echo "<h3>STEP 2: Plumbing Technicians in Database</h3>";
        
        $tech_query = "SELECT t_id, t_name, t_category, t_specialization, t_skills, t_status, 
                             t_booking_limit, t_current_bookings,
                             (t_booking_limit - t_current_bookings) as available_slots
                      FROM tms_technician 
                      WHERE t_category LIKE '%plumb%' OR t_specialization LIKE '%plumb%'
                      ORDER BY t_id";
        $techs = $mysqli->query($tech_query);
        
        if ($techs->num_rows > 0) {
            echo "<p class='pass'>✅ Found " . $techs->num_rows . " plumbing technician(s)</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Skills (t_skills column)</th><th>Status</th><th>Capacity</th></tr>";
            while ($tech = $techs->fetch_assoc()) {
                $capacity_class = $tech['available_slots'] > 0 ? 'pass' : 'fail';
                echo "<tr>";
                echo "<td>{$tech['t_id']}</td>";
                echo "<td><strong>{$tech['t_name']}</strong></td>";
                echo "<td>{$tech['t_category']}</td>";
                echo "<td>" . ($tech['t_skills'] ? htmlspecialchars($tech['t_skills']) : '<span style="color: red;">❌ NO SKILLS SET!</span>') . "</td>";
                echo "<td>{$tech['t_status']}</td>";
                echo "<td class='$capacity_class'>{$tech['t_current_bookings']}/{$tech['t_booking_limit']} (";
                echo $tech['available_slots'] > 0 ? "{$tech['available_slots']} free)" : "AT CAPACITY)";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='fail'>❌ No plumbing technicians found!</p>";
        }
        echo "</div>";

        // STEP 3: Check skill matching
        echo "<div class='section'>";
        echo "<h3>STEP 3: Skill Matching Test</h3>";
        echo "<p>Testing if technician skills match service names...</p>";
        
        // Get first plumbing service
        $test_service = $mysqli->query("SELECT s_id, s_name, s_category FROM tms_service WHERE s_category LIKE '%plumb%' LIMIT 1");
        if ($test_service && $test_service->num_rows > 0) {
            $service = $test_service->fetch_assoc();
            $service_name = $service['s_name'];
            $service_id = $service['s_id'];
            
            echo "<p><strong>Testing with service:</strong> {$service_name} (ID: {$service_id})</p>";
            
            // Test exact match
            $exact_match = "SELECT t_id, t_name, t_skills, t_category,
                                  FIND_IN_SET(?, t_skills) as skill_match
                           FROM tms_technician 
                           WHERE t_category LIKE '%plumb%'";
            $stmt = $mysqli->prepare($exact_match);
            $stmt->bind_param('s', $service_name);
            $stmt->execute();
            $matches = $stmt->get_result();
            
            if ($matches->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Technician</th><th>Category</th><th>Skills in t_skills</th><th>Match?</th></tr>";
                while ($match = $matches->fetch_assoc()) {
                    $has_match = $match['skill_match'] > 0;
                    $match_class = $has_match ? 'pass' : 'fail';
                    echo "<tr class='$match_class'>";
                    echo "<td>{$match['t_name']}</td>";
                    echo "<td>{$match['t_category']}</td>";
                    echo "<td>" . ($match['t_skills'] ? htmlspecialchars($match['t_skills']) : 'EMPTY') . "</td>";
                    echo "<td>" . ($has_match ? "✅ YES" : "❌ NO") . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        echo "</div>";

        // STEP 4: Show the problem
        echo "<div class='section warning'>";
        echo "<h3>⚠️ THE PROBLEM</h3>";
        echo "<p>The ultimate matcher uses <strong>EXACT service name matching</strong>:</p>";
        echo "<div class='code'>";
        echo "WHERE FIND_IN_SET('{service_name}', t_skills) > 0";
        echo "</div>";
        echo "<p><strong>This means:</strong></p>";
        echo "<ul>";
        echo "<li>Service name: <strong>\"Plumbing Service\"</strong></li>";
        echo "<li>Technician t_skills must contain: <strong>\"Plumbing Service\"</strong> (exact match)</li>";
        echo "<li>If t_skills is empty or has different name → NO MATCH ❌</li>";
        echo "</ul>";
        echo "</div>";

        // STEP 5: Solution
        echo "<div class='section pass'>";
        echo "<h3>✅ SOLUTION</h3>";
        echo "<p><strong>Option 1: Add Skills to Technician (Recommended)</strong></p>";
        echo "<ol>";
        echo "<li>Go to: <a href='admin-manage-technician.php'>Manage Technicians</a></li>";
        echo "<li>Find your plumbing technician</li>";
        echo "<li>Click 'Edit Skills' or 'Edit'</li>";
        echo "<li>Check the plumbing services from the 43+ service list</li>";
        echo "<li>Save</li>";
        echo "</ol>";
        
        echo "<p><strong>Option 2: Quick Fix SQL (Run this query)</strong></p>";
        
        // Get plumbing services for SQL
        $plumbing_services = $mysqli->query("SELECT s_name FROM tms_service WHERE s_category LIKE '%plumb%'");
        $service_names = [];
        while ($s = $plumbing_services->fetch_assoc()) {
            $service_names[] = $s['s_name'];
        }
        $skills_string = implode(',', $service_names);
        
        echo "<div class='code'>";
        echo "-- Add plumbing skills to plumbing technicians<br>";
        echo "UPDATE tms_technician<br>";
        echo "SET t_skills = '{$skills_string}'<br>";
        echo "WHERE t_category LIKE '%plumb%'<br>";
        echo "AND (t_skills IS NULL OR t_skills = '');";
        echo "</div>";
        
        echo "<p><strong>Option 3: Run Auto-Fix Script</strong></p>";
        echo "<a href='?auto_fix=1' class='btn' style='background: #10b981;'>🔧 Auto-Fix Now</a>";
        echo "</div>";

        // Auto-fix if requested
        if (isset($_GET['auto_fix'])) {
            echo "<div class='section pass'>";
            echo "<h3>🔧 Running Auto-Fix...</h3>";
            
            // Get all plumbing services
            $services_result = $mysqli->query("SELECT s_name FROM tms_service WHERE s_category LIKE '%plumb%'");
            $all_services = [];
            while ($s = $services_result->fetch_assoc()) {
                $all_services[] = $s['s_name'];
            }
            $skills = implode(',', $all_services);
            
            // Update plumbing technicians
            $update_query = "UPDATE tms_technician 
                            SET t_skills = ?
                            WHERE t_category LIKE '%plumb%'
                            AND (t_skills IS NULL OR t_skills = '')";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param('s', $skills);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            
            if ($affected > 0) {
                echo "<p class='pass'>✅ SUCCESS! Updated {$affected} plumbing technician(s)</p>";
                echo "<p>Skills added: <strong>{$skills}</strong></p>";
                echo "<p><a href='admin-assign-technician.php' class='btn'>Test Assignment Now</a></p>";
            } else {
                echo "<p class='warning'>⚠️ No technicians updated (they may already have skills set)</p>";
            }
            echo "</div>";
        }

        // STEP 6: Test the matcher
        echo "<div class='section'>";
        echo "<h3>STEP 6: Test Ultimate Matcher</h3>";
        
        $test_service = $mysqli->query("SELECT s_id, s_name FROM tms_service WHERE s_category LIKE '%plumb%' LIMIT 1");
        if ($test_service && $test_service->num_rows > 0) {
            $service = $test_service->fetch_assoc();
            
            require_once('vendor/inc/ultimate-technician-matcher.php');
            
            $test_date = date('Y-m-d');
            $test_time = '10:00:00';
            
            $available = getSmartAvailableTechnicians($mysqli, $service['s_id'], $test_date, $test_time);
            
            echo "<p>Testing matcher for: <strong>{$service['s_name']}</strong></p>";
            echo "<p>Date: {$test_date}, Time: {$test_time}</p>";
            
            if (empty($available)) {
                echo "<p class='fail'>❌ No technicians returned by matcher</p>";
                echo "<p><strong>Reason:</strong> Technicians don't have the exact service name in t_skills column</p>";
            } else {
                echo "<p class='pass'>✅ Found " . count($available) . " technician(s)</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>Match Type</th><th>Available?</th><th>Capacity</th><th>Reason</th></tr>";
                foreach ($available as $tech) {
                    $avail_class = $tech['is_available'] ? 'pass' : 'fail';
                    echo "<tr class='$avail_class'>";
                    echo "<td>{$tech['t_name']}</td>";
                    echo "<td>{$tech['match_type']}</td>";
                    echo "<td>" . ($tech['is_available'] ? '✅ YES' : '❌ NO') . "</td>";
                    echo "<td>{$tech['t_current_bookings']}/{$tech['t_booking_limit']}</td>";
                    echo "<td>" . ($tech['is_available'] ? 'Can assign' : $tech['unavailable_reason']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        echo "</div>";
        ?>

        <hr style="margin: 30px 0;">
        <h3>🔗 Quick Actions</h3>
        <a href="admin-manage-technician.php" class="btn">Manage Technicians</a>
        <a href="admin-edit-technician-skills.php" class="btn">Edit Technician Skills</a>
        <a href="admin-assign-technician.php" class="btn">Test Assignment</a>
        <a href="admin-dashboard.php" class="btn">Dashboard</a>
        <a href="?" class="btn">Refresh Diagnostic</a>
    </div>
</body>
</html>
