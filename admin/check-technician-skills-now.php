<?php
include('vendor/inc/config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Technician Skills</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Technician Skills Check</h1>
    
    <?php
    // Check technicians
    $tech_query = "SELECT t_id, t_name, t_ez_id, t_category FROM tms_technician ORDER BY t_id DESC LIMIT 10";
    $tech_result = $mysqli->query($tech_query);
    
    echo "<h2>Recent Technicians:</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>EZ ID</th><th>Category</th><th>Skills Count</th><th>Skills</th></tr>";
    
    while($tech = $tech_result->fetch_assoc()) {
        // Count skills for this technician
        $skill_query = "SELECT COUNT(*) as count FROM tms_technician_skills WHERE ts_technician_id = ?";
        $skill_stmt = $mysqli->prepare($skill_query);
        $skill_stmt->bind_param('i', $tech['t_id']);
        $skill_stmt->execute();
        $skill_count = $skill_stmt->get_result()->fetch_assoc()['count'];
        
        // Get skill names
        $skills_query = "SELECT s.s_name FROM tms_technician_skills ts 
                        JOIN tms_service s ON ts.ts_service_id = s.s_id 
                        WHERE ts.ts_technician_id = ? LIMIT 5";
        $skills_stmt = $mysqli->prepare($skills_query);
        $skills_stmt->bind_param('i', $tech['t_id']);
        $skills_stmt->execute();
        $skills_result = $skills_stmt->get_result();
        
        $skills = [];
        while($skill = $skills_result->fetch_assoc()) {
            $skills[] = $skill['s_name'];
        }
        
        $color = $skill_count > 0 ? 'success' : 'error';
        
        echo "<tr>";
        echo "<td>" . $tech['t_id'] . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_name']) . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_ez_id']) . "</td>";
        echo "<td>" . htmlspecialchars($tech['t_category']) . "</td>";
        echo "<td class='$color'>" . $skill_count . "</td>";
        echo "<td>" . ($skill_count > 0 ? implode(", ", $skills) . ($skill_count > 5 ? "..." : "") : "NO SKILLS") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check a specific booking
    echo "<hr><h2>Check Specific Booking:</h2>";
    $booking_query = "SELECT sb.sb_id, sb.sb_service_id, s.s_name, s.s_category 
                      FROM tms_service_booking sb 
                      LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                      WHERE sb.sb_status = 'Pending' 
                      ORDER BY sb.sb_id DESC LIMIT 1";
    $booking_result = $mysqli->query($booking_query);
    
    if($booking = $booking_result->fetch_assoc()) {
        echo "<p><strong>Latest Pending Booking:</strong></p>";
        echo "<p>Booking ID: " . $booking['sb_id'] . "</p>";
        echo "<p>Service: " . htmlspecialchars($booking['s_name']) . "</p>";
        echo "<p>Service ID: " . $booking['sb_service_id'] . "</p>";
        
        // Find technicians with this skill
        $match_query = "SELECT t.t_id, t.t_name, t.t_ez_id 
                       FROM tms_technician t
                       INNER JOIN tms_technician_skills ts ON t.t_id = ts.ts_technician_id
                       WHERE ts.ts_service_id = ?";
        $match_stmt = $mysqli->prepare($match_query);
        $match_stmt->bind_param('i', $booking['sb_service_id']);
        $match_stmt->execute();
        $match_result = $match_stmt->get_result();
        
        echo "<h3>Technicians who can do this service:</h3>";
        if($match_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>EZ ID</th></tr>";
            while($match = $match_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $match['t_id'] . "</td>";
                echo "<td>" . htmlspecialchars($match['t_name']) . "</td>";
                echo "<td>" . htmlspecialchars($match['t_ez_id']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ NO TECHNICIANS FOUND with this skill!</p>";
            echo "<p>This means no technician has service ID " . $booking['sb_service_id'] . " in their skills.</p>";
        }
    } else {
        echo "<p>No pending bookings found.</p>";
    }
    ?>
    
    <hr>
    <p><a href="admin-add-technician.php">Add Technician with Skills</a></p>
    <p><a href="admin-manage-technician.php">Manage Technicians</a></p>
</body>
</html>
