<?php
/**
 * DEBUG: Why are technicians not showing?
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug - No Technicians</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #667eea; }
        .section { background: #f9fafb; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .pass { border-left-color: #10b981; background: #ecfdf5; }
        .fail { border-left-color: #ef4444; background: #fef2f2; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
        .btn { padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-fix { background: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Debug: Why No Technicians Showing?</h2>
        
        <?php
        // CHECK 1: Do technicians exist?
        echo "<div class='section'>";
        echo "<h3>CHECK 1: Do Technicians Exist?</h3>";
        $tech_count = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_status != 'Inactive'")->fetch_object()->count;
        if ($tech_count > 0) {
            echo "<p class='pass'>✅ Found {$tech_count} active technician(s)</p>";
        } else {
            echo "<p class='fail'>❌ No active technicians found!</p>";
        }
        echo "</div>";
        
        // CHECK 2: Do they have t_skills column?
        echo "<div class='section'>";
        echo "<h3>CHECK 2: Does t_skills Column Exist?</h3>";
        $columns = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_skills'");
        if ($columns->num_rows > 0) {
            echo "<p class='pass'>✅ t_skills column exists</p>";
            
            // Check if it has data
            $with_skills = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_skills IS NOT NULL AND t_skills != ''")->fetch_object()->count;
            $without_skills = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_skills IS NULL OR t_skills = ''")->fetch_object()->count;
            
            echo "<p>Technicians WITH skills: <strong>{$with_skills}</strong></p>";
            echo "<p>Technicians WITHOUT skills: <strong>{$without_skills}</strong></p>";
            
            if ($without_skills > 0) {
                echo "<p class='fail'>⚠️ {$without_skills} technician(s) have empty t_skills!</p>";
            }
        } else {
            echo "<p class='fail'>❌ t_skills column does NOT exist!</p>";
        }
        echo "</div>";
        
        // CHECK 3: Show technician details
        echo "<div class='section'>";
        echo "<h3>CHECK 3: Technician Details</h3>";
        $techs = $mysqli->query("SELECT t_id, t_name, t_category, t_skills, t_status, t_booking_limit, t_current_bookings FROM tms_technician WHERE t_status != 'Inactive' LIMIT 10");
        
        if ($techs->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Skills</th><th>Status</th><th>Capacity</th></tr>";
            while ($tech = $techs->fetch_assoc()) {
                $skills_display = empty($tech['t_skills']) ? '<span style="color: red;">EMPTY!</span>' : substr($tech['t_skills'], 0, 50) . '...';
                $capacity = "{$tech['t_current_bookings']}/{$tech['t_booking_limit']}";
                echo "<tr>";
                echo "<td>{$tech['t_id']}</td>";
                echo "<td>{$tech['t_name']}</td>";
                echo "<td>{$tech['t_category']}</td>";
                echo "<td>{$skills_display}</td>";
                echo "<td>{$tech['t_status']}</td>";
                echo "<td>{$capacity}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "</div>";
        
        // CHECK 4: Do services exist?
        echo "<div class='section'>";
        echo "<h3>CHECK 4: Do Services Exist?</h3>";
        $service_count = $mysqli->query("SELECT COUNT(*) as count FROM tms_service WHERE s_status = 'Active'")->fetch_object()->count;
        if ($service_count > 0) {
            echo "<p class='pass'>✅ Found {$service_count} active service(s)</p>";
            
            $services = $mysqli->query("SELECT s_id, s_name, s_category FROM tms_service WHERE s_status = 'Active' LIMIT 5");
            echo "<table>";
            echo "<tr><th>ID</th><th>Service Name</th><th>Category</th></tr>";
            while ($service = $services->fetch_assoc()) {
                echo "<tr><td>{$service['s_id']}</td><td>{$service['s_name']}</td><td>{$service['s_category']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='fail'>❌ No active services found!</p>";
        }
        echo "</div>";
        
        // CHECK 5: Test the matcher
        echo "<div class='section'>";
        echo "<h3>CHECK 5: Test Ultimate Matcher</h3>";
        
        $test_service = $mysqli->query("SELECT s_id, s_name, s_category FROM tms_service WHERE s_status = 'Active' LIMIT 1");
        if ($test_service && $test_service->num_rows > 0) {
            $service = $test_service->fetch_assoc();
            
            echo "<p>Testing with: <strong>{$service['s_name']}</strong> ({$service['s_category']})</p>";
            
            require_once('vendor/inc/ultimate-technician-matcher.php');
            
            $test_date = date('Y-m-d');
            $test_time = '10:00:00';
            
            try {
                $available = getSmartAvailableTechnicians($mysqli, $service['s_id'], $test_date, $test_time);
                
                if (empty($available)) {
                    echo "<p class='fail'>❌ Matcher returned ZERO technicians</p>";
                    echo "<p><strong>This is the problem!</strong></p>";
                } else {
                    echo "<p class='pass'>✅ Matcher found " . count($available) . " technician(s)</p>";
                    echo "<table>";
                    echo "<tr><th>Name</th><th>Category</th><th>Match Type</th><th>Available?</th><th>Capacity</th></tr>";
                    foreach ($available as $tech) {
                        $avail = $tech['is_available'] ? '✅ YES' : '❌ NO';
                        echo "<tr>";
                        echo "<td>{$tech['t_name']}</td>";
                        echo "<td>{$tech['t_category']}</td>";
                        echo "<td>{$tech['match_type']}</td>";
                        echo "<td>{$avail}</td>";
                        echo "<td>{$tech['t_current_bookings']}/{$tech['t_booking_limit']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } catch (Exception $e) {
                echo "<p class='fail'>❌ Matcher ERROR: " . $e->getMessage() . "</p>";
            }
        }
        echo "</div>";
        
        // CHECK 6: Test direct query
        echo "<div class='section'>";
        echo "<h3>CHECK 6: Test Direct Query (What Matcher Uses)</h3>";
        
        $test_service = $mysqli->query("SELECT s_id, s_name, s_category FROM tms_service WHERE s_status = 'Active' LIMIT 1");
        if ($test_service && $test_service->num_rows > 0) {
            $service = $test_service->fetch_assoc();
            $service_name = $service['s_name'];
            $service_category = $service['s_category'];
            
            echo "<p>Service: <strong>{$service_name}</strong></p>";
            echo "<p>Category: <strong>{$service_category}</strong></p>";
            
            $query = "SELECT t_id, t_name, t_category, t_skills,
                            CASE
                                WHEN FIND_IN_SET(?, t_skills) > 0 THEN 'exact_skill'
                                WHEN t_skills LIKE CONCAT('%', ?, '%') THEN 'partial_skill'
                                WHEN t_category = ? THEN 'same_category'
                                ELSE 'no_match'
                            END as match_type
                     FROM tms_technician t
                     WHERE (
                        FIND_IN_SET(?, t_skills) > 0
                        OR t_skills LIKE CONCAT('%', ?, '%')
                        OR t_category = ?
                     )
                     AND t_status != 'Inactive'
                     LIMIT 10";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ssssss', $service_name, $service_name, $service_category, $service_name, $service_name, $service_category);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<p class='pass'>✅ Direct query found " . $result->num_rows . " technician(s)</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>Category</th><th>Skills</th><th>Match Type</th></tr>";
                while ($tech = $result->fetch_assoc()) {
                    $skills = empty($tech['t_skills']) ? 'EMPTY' : substr($tech['t_skills'], 0, 50);
                    echo "<tr>";
                    echo "<td>{$tech['t_name']}</td>";
                    echo "<td>{$tech['t_category']}</td>";
                    echo "<td>{$skills}</td>";
                    echo "<td>{$tech['match_type']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='fail'>❌ Direct query found ZERO technicians</p>";
                echo "<p><strong>Problem: No technicians match the service!</strong></p>";
            }
        }
        echo "</div>";
        
        // SOLUTION
        echo "<div class='section fail'>";
        echo "<h3>🔧 SOLUTION</h3>";
        echo "<p><strong>If technicians have EMPTY t_skills:</strong></p>";
        echo "<ol>";
        echo "<li>Run the SQL fix to add skills automatically</li>";
        echo "<li>Or manually add skills via admin panel</li>";
        echo "</ol>";
        echo "<a href='?run_fix=1' class='btn btn-fix'>🚀 Run Auto-Fix Now</a>";
        echo "</div>";
        
        // Auto-fix
        if (isset($_GET['run_fix'])) {
            echo "<div class='section pass'>";
            echo "<h3>🔧 Running Auto-Fix...</h3>";
            
            // Add t_skills column if missing
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_skills TEXT NULL");
            
            // Get all categories
            $categories = $mysqli->query("SELECT DISTINCT t_category FROM tms_technician WHERE t_status != 'Inactive' AND t_category IS NOT NULL");
            
            $updated = 0;
            while ($cat = $categories->fetch_assoc()) {
                $category = $cat['t_category'];
                
                // Get services for this category
                $services_query = "SELECT GROUP_CONCAT(s_name SEPARATOR ',') as skills FROM tms_service WHERE s_category LIKE ? AND s_status = 'Active'";
                $stmt = $mysqli->prepare($services_query);
                $search = "%{$category}%";
                $stmt->bind_param('s', $search);
                $stmt->execute();
                $skills_result = $stmt->get_result()->fetch_assoc();
                
                if (!empty($skills_result['skills'])) {
                    // Update technicians
                    $update = "UPDATE tms_technician SET t_skills = ? WHERE t_category = ? AND (t_skills IS NULL OR t_skills = '')";
                    $stmt = $mysqli->prepare($update);
                    $stmt->bind_param('ss', $skills_result['skills'], $category);
                    $stmt->execute();
                    $updated += $stmt->affected_rows;
                    
                    echo "<p>✅ Updated {$stmt->affected_rows} {$category} technician(s)</p>";
                }
            }
            
            echo "<p class='pass'><strong>✅ Fixed {$updated} technician(s)!</strong></p>";
            echo "<a href='admin-assign-technician.php' class='btn'>Test Assignment Now</a>";
            echo "</div>";
        }
        ?>
        
        <hr>
        <h3>Quick Actions:</h3>
        <a href="admin-assign-technician.php" class="btn">Try Assignment</a>
        <a href="admin-manage-technician.php" class="btn">Manage Technicians</a>
        <a href="?" class="btn">Refresh Debug</a>
    </div>
</body>
</html>
