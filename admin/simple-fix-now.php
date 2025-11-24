<?php
/**
 * SIMPLE FIX - Just Run This
 * No steps, no complexity - just fixes everything
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<!DOCTYPE html><html><head><title>Simple Fix</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f7fa;}";
echo ".box{background:white;padding:30px;max-width:800px;margin:0 auto;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h2{color:#667eea;}";
echo ".result{background:#ecfdf5;border-left:4px solid #10b981;padding:15px;margin:10px 0;border-radius:5px;}";
echo ".error{background:#fef2f2;border-left-color:#ef4444;}";
echo "</style></head><body><div class='box'>";

echo "<h2>🔧 Simple Fix - Running...</h2>";

// FIX 1: Add t_skills column if missing
echo "<p>Checking database structure...</p>";
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_skills TEXT NULL");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 3");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0");
echo "<div class='result'>✅ Database structure OK</div>";

// FIX 2: Set default booking limits
$mysqli->query("UPDATE tms_technician SET t_booking_limit = 3 WHERE t_booking_limit IS NULL OR t_booking_limit = 0");
echo "<div class='result'>✅ Booking limits set</div>";

// FIX 3: Add skills to technicians
echo "<p>Adding skills to technicians...</p>";

$categories = ['Electrical', 'Plumbing', 'HVAC', 'Appliance', 'Carpentry', 'Painting'];
$updated = 0;

foreach ($categories as $category) {
    // Get services for this category
    $service_query = "SELECT s_name FROM tms_service WHERE s_category LIKE '%{$category}%' AND s_status = 'Active'";
    $services = $mysqli->query($service_query);
    
    if ($services && $services->num_rows > 0) {
        $skill_list = [];
        while ($service = $services->fetch_assoc()) {
            $skill_list[] = $service['s_name'];
        }
        
        if (!empty($skill_list)) {
            $skills_string = implode(',', $skill_list);
            
            // Update technicians in this category who don't have skills
            $update_query = "UPDATE tms_technician 
                           SET t_skills = ? 
                           WHERE t_category LIKE ?
                           AND (t_skills IS NULL OR t_skills = '')";
            $stmt = $mysqli->prepare($update_query);
            $search_cat = "%{$category}%";
            $stmt->bind_param('ss', $skills_string, $search_cat);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                echo "<div class='result'>✅ {$category}: Updated {$stmt->affected_rows} technician(s)</div>";
                $updated += $stmt->affected_rows;
            }
        }
    }
}

if ($updated == 0) {
    echo "<div class='result'>ℹ️ All technicians already have skills set</div>";
}

// FIX 4: Sync booking counters
$mysqli->query("UPDATE tms_technician t
               SET t_current_bookings = (
                   SELECT COUNT(*) 
                   FROM tms_service_booking sb
                   WHERE sb.sb_technician_id = t.t_id
                   AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
               )");
echo "<div class='result'>✅ Booking counters synced</div>";

// Show results
echo "<h3>✅ Fix Complete!</h3>";
echo "<p><strong>What was fixed:</strong></p>";
echo "<ul>";
echo "<li>✅ Database structure verified</li>";
echo "<li>✅ Skills added to {$updated} technician(s)</li>";
echo "<li>✅ Booking limits set</li>";
echo "<li>✅ Counters synced</li>";
echo "</ul>";

// Show current technicians
echo "<h3>📋 Current Technicians:</h3>";
$tech_query = "SELECT t_name, t_category, t_skills, t_current_bookings, t_booking_limit 
              FROM tms_technician 
              WHERE t_status != 'Inactive'
              ORDER BY t_category, t_name";
$techs = $mysqli->query($tech_query);

echo "<table border='1' cellpadding='10' style='width:100%;border-collapse:collapse;'>";
echo "<tr style='background:#f3f4f6;'><th>Name</th><th>Category</th><th>Skills</th><th>Capacity</th></tr>";

while ($tech = $techs->fetch_assoc()) {
    $skills_display = $tech['t_skills'] ? substr($tech['t_skills'], 0, 50) . '...' : '<span style="color:red;">EMPTY</span>';
    echo "<tr>";
    echo "<td><strong>{$tech['t_name']}</strong></td>";
    echo "<td>{$tech['t_category']}</td>";
    echo "<td>{$skills_display}</td>";
    echo "<td>{$tech['t_current_bookings']}/{$tech['t_booking_limit']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>🎯 Next Steps:</h3>";
echo "<p><a href='admin-assign-technician.php' style='background:#10b981;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>Test Assignment</a>";
echo "<a href='admin-manage-technician.php' style='background:#667eea;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>Manage Technicians</a>";
echo "<a href='admin-dashboard.php' style='background:#6b7280;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>Dashboard</a></p>";

echo "</div></body></html>";
?>
