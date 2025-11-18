<!DOCTYPE html>
<html>
<head>
    <title>Setup Site Settings Table</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        h2 { color: #333; }
        a { color: #007bff; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
<?php
// This script creates the tms_site_settings table
// Run this file once by visiting: http://localhost/electrozot/admin/setup-site-settings.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Setting up Site Settings Table...</h2>";

// Database connection
$dbuser = "root";
$dbpass = "";
$host = "localhost";
$db = "electrozot_db";

try {
    $mysqli = new mysqli($host, $dbuser, $dbpass, $db);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "<div class='success'>✓ Database connection successful!</div>";
    
    // Create table
    $create_table = "CREATE TABLE IF NOT EXISTS `tms_site_settings` (
      `id` int NOT NULL AUTO_INCREMENT,
      `setting_key` varchar(100) NOT NULL,
      `setting_value` text,
      `setting_label` varchar(200) NOT NULL,
      `setting_type` varchar(50) NOT NULL DEFAULT 'text',
      `setting_group` varchar(50) NOT NULL DEFAULT 'general',
      `display_order` int NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if($mysqli->query($create_table)) {
        echo "<div class='success'>✓ Table 'tms_site_settings' created successfully!</div>";
    } else {
        throw new Exception("Error creating table: " . $mysqli->error);
    }
    
    // Check if data already exists
    $check = $mysqli->query("SELECT COUNT(*) as count FROM tms_site_settings");
    $row = $check->fetch_assoc();
    
    if($row['count'] > 0) {
        echo "<div class='info'>ℹ Table already has data. Skipping insert.</div>";
    } else {
        // Insert default data
        $settings = [
            ['site_name', 'Electrozot', 'Site Name', 'text', 'general', 1],
            ['site_tagline', 'We Make Perfect', 'Site Tagline', 'text', 'general', 2],
            ['business_address', 'Your Business Address Here', 'Business Address', 'textarea', 'contact', 3],
            ['primary_phone', '7559606925', 'Primary Phone', 'tel', 'contact', 4],
            ['secondary_phone', '', 'Secondary Phone', 'tel', 'contact', 5],
            ['whatsapp_number', '7559606925', 'WhatsApp Number', 'tel', 'contact', 6],
            ['primary_email', 'info@electrozot.com', 'Primary Email', 'email', 'contact', 7],
            ['support_email', 'support@electrozot.com', 'Support Email', 'email', 'contact', 8],
            ['facebook_url', '', 'Facebook URL', 'url', 'social', 9],
            ['instagram_url', '', 'Instagram URL', 'url', 'social', 10],
            ['twitter_url', '', 'Twitter URL', 'url', 'social', 11],
            ['linkedin_url', '', 'LinkedIn URL', 'url', 'social', 12]
        ];
        
        $insert_stmt = $mysqli->prepare("INSERT INTO tms_site_settings (setting_key, setting_value, setting_label, setting_type, setting_group, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        
        if(!$insert_stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
        
        $success_count = 0;
        foreach($settings as $setting) {
            $insert_stmt->bind_param('sssssi', $setting[0], $setting[1], $setting[2], $setting[3], $setting[4], $setting[5]);
            if($insert_stmt->execute()) {
                $success_count++;
            } else {
                echo "<div class='error'>✗ Error inserting {$setting[0]}: " . $insert_stmt->error . "</div>";
            }
        }
        
        echo "<div class='success'>✓ Inserted $success_count default settings!</div>";
    }
    
    echo "<h3 style='color: green;'>✓ Setup Complete!</h3>";
    echo "<p>You can now go to <a href='admin-site-settings.php'>Site Contact Settings</a></p>";
    echo "<div class='error'><strong>⚠ Important:</strong> Delete this file (setup-site-settings.php) after setup for security.</div>";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>Please check your database connection and try again.</div>";
}
?>
</div>
</body>
</html>
