<?php
include('admin/vendor/inc/config.php');

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    h2, h3 { color: #333; }
    table { background: white; border-collapse: collapse; width: 100%; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
    td { padding: 10px; border-bottom: 1px solid #ddd; }
    tr:hover { background: #f5f5f5; }
    .status-active { color: green; font-weight: bold; }
    .status-inactive { color: red; font-weight: bold; }
    .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
    .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .alert-warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
</style>";

echo "<h2>🔍 Mixer Grinder Service Diagnostic</h2>";

// Check for mixer grinder service
$query = "SELECT * FROM tms_service WHERE s_name LIKE '%mixer%' OR s_name LIKE '%grinder%' OR s_gadget_name LIKE '%mixer%' OR s_gadget_name LIKE '%grinder%'";
$result = $mysqli->query($query);

if($result && $result->num_rows > 0) {
    echo "<div class='alert alert-success'><strong>✓ Found " . $result->num_rows . " mixer/grinder service(s) in database</strong></div>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Subcategory</th><th>Gadget Name</th><th>Status</th><th>Price</th><th>Deleted</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        $deleted_status = isset($row['is_deleted']) ? ($row['is_deleted'] == 1 ? 'YES' : 'NO') : 'N/A';
        $status_class = $row['s_status'] == 'Active' ? 'status-active' : 'status-inactive';
        
        echo "<tr>";
        echo "<td>" . $row['s_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['s_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['s_category']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['s_subcategory'] ?? 'N/A') . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['s_gadget_name'] ?? 'N/A') . "</td>";
        echo "<td class='$status_class'>" . $row['s_status'] . "</td>";
        echo "<td>₹" . $row['s_price'] . "</td>";
        echo "<td>" . $deleted_status . "</td>";
        echo "</tr>";
        
        // Diagnostic messages
        if($row['s_status'] != 'Active') {
            echo "<tr><td colspan='8' style='background: #fff3cd; color: #856404;'><strong>⚠️ ISSUE:</strong> Service status is '{$row['s_status']}'. Change it to 'Active' to show in dropdown.</td></tr>";
        }
        if(empty($row['s_subcategory'])) {
            echo "<tr><td colspan='8' style='background: #f8d7da; color: #721c24;'><strong>❌ ISSUE:</strong> Subcategory is empty! This service won't appear in any dropdown.</td></tr>";
        }
        if($row['s_subcategory'] != 'Other Gadgets') {
            echo "<tr><td colspan='8' style='background: #fff3cd; color: #856404;'><strong>⚠️ NOTE:</strong> Subcategory is '{$row['s_subcategory']}'. Make sure you're selecting the correct category in the booking form.</td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "<div class='alert alert-danger'><strong>❌ No mixer grinder service found in database!</strong><br>The service was never added or was permanently deleted.</div>";
}

// Test the exact query used by the dropdown
echo "<hr><h3>🧪 Testing Dropdown Query (for 'Other Gadgets' subcategory)</h3>";
$test_query = "SELECT s_id as id, s_name as name, s_gadget_name as gadget_name, s_price as price 
              FROM tms_service 
              WHERE s_subcategory = 'Other Gadgets' AND s_status = 'Active' 
              ORDER BY s_name ASC";
$test_result = $mysqli->query($test_query);

if($test_result && $test_result->num_rows > 0) {
    echo "<div class='alert alert-success'><strong>✓ Found " . $test_result->num_rows . " active service(s) in 'Other Gadgets' subcategory</strong></div>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Service Name</th><th>Gadget Name (shown in dropdown)</th><th>Price</th></tr>";
    
    $found_mixer = false;
    while($row = $test_result->fetch_assoc()) {
        $is_mixer = (stripos($row['name'], 'mixer') !== false || stripos($row['gadget_name'], 'mixer') !== false);
        if($is_mixer) $found_mixer = true;
        
        $highlight = $is_mixer ? "background: #d4edda; font-weight: bold;" : "";
        echo "<tr style='$highlight'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gadget_name'] ?? 'N/A') . "</td>";
        echo "<td>₹" . $row['price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if($found_mixer) {
        echo "<div class='alert alert-success'><strong>✓ Mixer Grinder service SHOULD appear in the dropdown!</strong></div>";
    } else {
        echo "<div class='alert alert-warning'><strong>⚠️ Mixer Grinder service NOT found in this query result.</strong></div>";
    }
} else {
    echo "<div class='alert alert-danger'><strong>❌ No active services found in 'Other Gadgets' subcategory</strong></div>";
}

// Check all services in ELECTRONIC REPAIR category
echo "<hr><h3>📋 All services in ELECTRONIC REPAIR category</h3>";
$query2 = "SELECT s_id, s_name, s_subcategory, s_gadget_name, s_status FROM tms_service WHERE s_category = 'ELECTRONIC REPAIR' ORDER BY s_subcategory, s_name";
$result2 = $mysqli->query($query2);

if($result2 && $result2->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Subcategory</th><th>Gadget Name</th><th>Status</th></tr>";
    
    while($row = $result2->fetch_assoc()) {
        $status_class = $row['s_status'] == 'Active' ? 'status-active' : 'status-inactive';
        echo "<tr>";
        echo "<td>" . $row['s_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['s_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['s_subcategory'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['s_gadget_name'] ?? 'N/A') . "</td>";
        echo "<td class='$status_class'>" . $row['s_status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No services found in ELECTRONIC REPAIR category.</p>";
}

$mysqli->close();
?>
