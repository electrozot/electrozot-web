<?php
/**
 * Site Settings Database Setup
 * Creates table for centralized contact information management
 * Run this file once to set up the system
 */

session_start();
include('vendor/inc/config.php');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Site Settings Setup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Site Settings Setup</h1>";

try {
    // Create site_settings table
    $create_table = "CREATE TABLE IF NOT EXISTS tms_site_settings (
        setting_id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        setting_label VARCHAR(255),
        setting_type VARCHAR(50) DEFAULT 'text',
        setting_group VARCHAR(100) DEFAULT 'general',
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if($mysqli->query($create_table)) {
        echo "<div class='success'>✓ Site settings table created successfully!</div>";
    }
    
    // Insert default contact settings
    $default_settings = [
        ['contact_phone_1', '7559606925', 'Primary Phone Number', 'tel', 'contact', 1],
        ['contact_phone_2', '', 'Secondary Phone Number', 'tel', 'contact', 2],
        ['contact_whatsapp', '7559606925', 'WhatsApp Number', 'tel', 'contact', 3],
        ['contact_email_1', 'info@electrozot.com', 'Primary Email', 'email', 'contact', 4],
        ['contact_email_2', 'support@electrozot.com', 'Secondary Email', 'email', 'contact', 5],
        ['contact_instagram', '@electrozot', 'Instagram Handle', 'text', 'social', 6],
        ['contact_facebook', '', 'Facebook Page', 'url', 'social', 7],
        ['contact_twitter', '', 'Twitter Handle', 'text', 'social', 8],
        ['contact_address', 'Himachal Pradesh, India', 'Business Address', 'textarea', 'contact', 9],
        ['business_name', 'ElectroZot', 'Business Name', 'text', 'general', 10],
        ['business_tagline', 'Professional Technician Services', 'Business Tagline', 'text', 'general', 11]
    ];
    
    $insert_stmt = $mysqli->prepare("INSERT INTO tms_site_settings (setting_key, setting_value, setting_label, setting_type, setting_group, display_order) 
                                     VALUES (?, ?, ?, ?, ?, ?) 
                                     ON DUPLICATE KEY UPDATE setting_label=VALUES(setting_label), setting_type=VALUES(setting_type)");
    
    $inserted = 0;
    foreach($default_settings as $setting) {
        $insert_stmt->bind_param('sssssi', $setting[0], $setting[1], $setting[2], $setting[3], $setting[4], $setting[5]);
        if($insert_stmt->execute()) {
            $inserted++;
        }
    }
    
    echo "<div class='success'>✓ Inserted/Updated $inserted default settings!</div>";
    
    echo "<div class='info'>
        <strong>📋 What's Next?</strong><br>
        • Go to Admin Dashboard → Settings → Site Contact Info<br>
        • Update your contact information<br>
        • Changes will reflect across the entire website automatically
    </div>";
    
    echo "<a href='admin-site-settings.php' class='btn'>Go to Site Settings</a>";
    echo " <a href='admin-dashboard.php' class='btn' style='background: #28a745;'>Go to Dashboard</a>";
    
} catch(Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";
?>
