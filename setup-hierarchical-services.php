<?php
/**
 * Setup Script for Hierarchical Service System
 * Run this file once to set up the new structure
 */

include('admin/vendor/inc/config.php');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Hierarchical Service System Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; margin: 10px 0; border-radius: 5px; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; margin: 10px 0; border-radius: 5px; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; margin: 10px 0; border-radius: 5px; }
        h1 { color: #333; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <h1>🚀 Hierarchical Service System Setup</h1>
    <p>This script will set up the new hierarchical service structure.</p>
    <hr>";

// Step 1: Add subcategory column
echo "<div class='step'><strong>Step 1:</strong> Adding s_subcategory column...</div>";
$result1 = $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_subcategory VARCHAR(200) NULL AFTER s_category");
if($result1) {
    echo "<div class='success'>✓ s_subcategory column added successfully</div>";
} else {
    echo "<div class='error'>✗ Error: " . $mysqli->error . "</div>";
}

// Step 2: Add gadget_name column
echo "<div class='step'><strong>Step 2:</strong> Adding s_gadget_name column...</div>";
$result2 = $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_gadget_name VARCHAR(200) NULL AFTER s_subcategory");
if($result2) {
    echo "<div class='success'>✓ s_gadget_name column added successfully</div>";
} else {
    echo "<div class='error'>✗ Error: " . $mysqli->error . "</div>";
}

// Step 3: Create categories reference table
echo "<div class='step'><strong>Step 3:</strong> Creating tms_service_categories table...</div>";
$result3 = $mysqli->query("CREATE TABLE IF NOT EXISTS tms_service_categories (
  sc_id INT AUTO_INCREMENT PRIMARY KEY,
  sc_category VARCHAR(200) NOT NULL,
  sc_subcategory VARCHAR(200) NOT NULL,
  sc_status ENUM('Active', 'Inactive') DEFAULT 'Active',
  UNIQUE KEY unique_category_subcategory (sc_category, sc_subcategory)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if($result3) {
    echo "<div class='success'>✓ tms_service_categories table created successfully</div>";
} else {
    echo "<div class='error'>✗ Error: " . $mysqli->error . "</div>";
}

// Step 4: Insert category-subcategory mappings
echo "<div class='step'><strong>Step 4:</strong> Inserting category-subcategory mappings...</div>";
$categories = [
    ['Basic Electrical Work', 'Wiring & Fixtures'],
    ['Basic Electrical Work', 'Safety & Power'],
    ['Electronic Repair', 'Major Appliances'],
    ['Electronic Repair', 'Small Gadgets'],
    ['Installation & Setup', 'Appliance Setup'],
    ['Installation & Setup', 'Tech & Security'],
    ['Servicing & Maintenance', 'Routine Care'],
    ['Plumbing Work', 'Fixtures & Taps']
];

$inserted = 0;
foreach($categories as $cat) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO tms_service_categories (sc_category, sc_subcategory) VALUES (?, ?)");
    $stmt->bind_param('ss', $cat[0], $cat[1]);
    if($stmt->execute()) {
        if($stmt->affected_rows > 0) {
            $inserted++;
        }
    }
}
echo "<div class='success'>✓ Inserted $inserted category-subcategory mappings</div>";

// Step 5: Check existing services
echo "<div class='step'><strong>Step 5:</strong> Checking existing services...</div>";
$check = $mysqli->query("SELECT COUNT(*) as total FROM tms_service");
$total = $check->fetch_object()->total;
echo "<div class='info'>ℹ Found $total existing services in database</div>";

$check_no_subcat = $mysqli->query("SELECT COUNT(*) as total FROM tms_service WHERE s_subcategory IS NULL OR s_subcategory = ''");
$no_subcat = $check_no_subcat->fetch_object()->total;
if($no_subcat > 0) {
    echo "<div class='info'>⚠ $no_subcat services need subcategory assignment</div>";
    echo "<div class='info'>💡 You can update these manually via Admin Panel → Manage Services → Edit Service</div>";
} else {
    echo "<div class='success'>✓ All services have subcategories assigned</div>";
}

// Summary
echo "<hr>
    <h2>✅ Setup Complete!</h2>
    <div class='success'>
        <strong>Next Steps:</strong>
        <ol>
            <li>Go to Admin Panel → Add Service to test the new hierarchical form</li>
            <li>Update existing services with subcategories (if needed)</li>
            <li>Test Quick Booking with the new cascading dropdowns</li>
            <li>Test Guest Booking on the homepage</li>
        </ol>
    </div>
    
    <div class='info'>
        <strong>📚 Documentation:</strong><br>
        See <code>HIERARCHICAL_SERVICE_SYSTEM_GUIDE.md</code> for complete documentation.
    </div>
    
    <p><a href='admin/admin-dashboard.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a></p>
</body>
</html>";

$mysqli->close();
?>
