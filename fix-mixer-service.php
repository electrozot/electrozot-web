<?php
// Quick fix script for mixer grinder service
include('admin/vendor/inc/config.php');

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { background: white; border-collapse: collapse; width: 100%; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
    td { padding: 10px; border-bottom: 1px solid #ddd; }
</style>";

echo "<h2>🔧 Fixing Mixer Grinder Service</h2>";

// First, show what we're fixing
$before = $mysqli->query("SELECT s_id, s_name, s_subcategory, s_status FROM tms_service WHERE s_id = 91");
if($before && $row = $before->fetch_assoc()) {
    echo "<h3>Before Fix:</h3>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Subcategory</th><th>Status</th></tr>";
    echo "<tr>";
    echo "<td>" . $row['s_id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['s_name']) . "</td>";
    echo "<td><span style='color: red;'>" . htmlspecialchars($row['s_subcategory']) . "</span> (WRONG - not in dropdown)</td>";
    echo "<td><span style='color: red;'>" . htmlspecialchars($row['s_status']) . "</span> (WRONG - should be 'Active')</td>";
    echo "</tr>";
    echo "</table>";
}

// Update the mixer grinder service
$query = "UPDATE tms_service 
          SET s_subcategory = 'Other Gadgets', 
              s_status = 'Active' 
          WHERE s_id = 91";

if($mysqli->query($query)) {
    echo "<div class='success'>";
    echo "<h3>✓ SUCCESS! Mixer Grinder service has been fixed.</h3>";
    echo "<p><strong>Changes made:</strong></p>";
    echo "<ul>";
    echo "<li>Subcategory changed from: <del>Small Gadgets</del> → <strong>Other Gadgets</strong></li>";
    echo "<li>Status changed from: <del>0</del> → <strong>Active</strong></li>";
    echo "</ul>";
    echo "</div>";
    
    // Verify the fix
    $verify = $mysqli->query("SELECT s_id, s_name, s_subcategory, s_status FROM tms_service WHERE s_id = 91");
    if($verify && $row = $verify->fetch_assoc()) {
        echo "<h3>After Fix:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Subcategory</th><th>Status</th></tr>";
        echo "<tr>";
        echo "<td>" . $row['s_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['s_name']) . "</td>";
        echo "<td><span style='color: green; font-weight: bold;'>" . htmlspecialchars($row['s_subcategory']) . "</span></td>";
        echo "<td><span style='color: green; font-weight: bold;'>" . htmlspecialchars($row['s_status']) . "</span></td>";
        echo "</tr>";
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<div class='success'>";
    echo "<h3>✓ The mixer grinder service should now appear in the booking form dropdown!</h3>";
    echo "<p><strong>How to test:</strong></p>";
    echo "<ol>";
    echo "<li>Go to your homepage (index.php)</li>";
    echo "<li>In the booking form, select category: <strong>Other Gadgets</strong> (under 🔧 REPAIR section)</li>";
    echo "<li>You should now see <strong>'Mixer Grinder / Juicer Repair and Clean'</strong> in the service dropdown</li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<div class='error'>";
    echo "<h3>❌ ERROR</h3>";
    echo "<p>" . $mysqli->error . "</p>";
    echo "</div>";
}

$mysqli->close();
?>
