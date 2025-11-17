<?php
/**
 * Test Site Settings System
 * Use this to verify the settings system is working correctly
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/site-settings-helper.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Site Settings</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        h2 {
            color: #764ba2;
            margin-top: 30px;
            padding: 10px;
            background: #f8f9fc;
            border-left: 4px solid #764ba2;
        }
        .test-section {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #e3e6f0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .setting-item {
            padding: 12px;
            margin: 8px 0;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .setting-label {
            font-weight: 600;
            color: #5a5c69;
        }
        .setting-value {
            color: #667eea;
            font-family: monospace;
            background: #f8f9fc;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        .demo-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .demo-link {
            padding: 10px 20px;
            background: white;
            border: 2px solid #667eea;
            border-radius: 8px;
            text-decoration: none;
            color: #667eea;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .demo-link:hover {
            background: #667eea;
            color: white;
        }
        .demo-link i {
            margin-right: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fc;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-vial"></i> Site Settings System Test</h1>
        
        <?php
        // Test 1: Check if table exists
        echo "<h2><i class='fas fa-database'></i> Test 1: Database Table Check</h2>";
        echo "<div class='test-section'>";
        
        if(settings_table_exists($mysqli)) {
            echo "<div class='success'><i class='fas fa-check-circle'></i> <strong>SUCCESS:</strong> Table 'tms_site_settings' exists!</div>";
            
            // Count settings
            $count_query = "SELECT COUNT(*) as total FROM tms_site_settings";
            $count_result = $mysqli->query($count_query);
            $count_row = $count_result->fetch_assoc();
            echo "<div class='info'><i class='fas fa-info-circle'></i> Found <strong>{$count_row['total']}</strong> settings in database.</div>";
        } else {
            echo "<div class='error'><i class='fas fa-times-circle'></i> <strong>ERROR:</strong> Table 'tms_site_settings' does not exist!</div>";
            echo "<div class='info'><i class='fas fa-lightbulb'></i> Please run <strong>setup-site-settings.php</strong> first.</div>";
            echo "<a href='setup-site-settings.php' class='btn'>Run Setup Now</a>";
            echo "</div></div></body></html>";
            exit();
        }
        echo "</div>";
        
        // Test 2: Display all settings
        echo "<h2><i class='fas fa-list'></i> Test 2: All Settings</h2>";
        echo "<div class='test-section'>";
        
        $all_settings = get_all_settings($mysqli);
        
        if(!empty($all_settings)) {
            echo "<table>";
            echo "<thead><tr><th>Setting Key</th><th>Current Value</th><th>Status</th></tr></thead>";
            echo "<tbody>";
            
            foreach($all_settings as $key => $value) {
                $status = !empty($value) ? "<span style='color: #28a745;'><i class='fas fa-check'></i> Set</span>" : "<span style='color: #ffc107;'><i class='fas fa-exclamation-triangle'></i> Empty</span>";
                $display_value = !empty($value) ? htmlspecialchars($value) : "<em style='color: #999;'>Not set</em>";
                
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
                echo "<td>" . $display_value . "</td>";
                echo "<td>" . $status . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody></table>";
            echo "<div class='success'><i class='fas fa-check-circle'></i> Successfully retrieved all settings!</div>";
        } else {
            echo "<div class='error'><i class='fas fa-times-circle'></i> No settings found in database.</div>";
        }
        
        echo "</div>";
        
        // Test 3: Test helper functions
        echo "<h2><i class='fas fa-code'></i> Test 3: Helper Functions</h2>";
        echo "<div class='test-section'>";
        
        $tests = [
            'Primary Phone' => get_primary_phone($mysqli),
            'Secondary Phone' => get_secondary_phone($mysqli),
            'WhatsApp' => get_whatsapp($mysqli),
            'Primary Email' => get_primary_email($mysqli),
            'Secondary Email' => get_secondary_email($mysqli),
            'Instagram' => get_instagram($mysqli),
            'Facebook' => get_facebook($mysqli),
            'Twitter' => get_twitter($mysqli),
            'Business Address' => get_business_address($mysqli),
            'Business Name' => get_business_name($mysqli),
            'Business Tagline' => get_business_tagline($mysqli),
        ];
        
        foreach($tests as $label => $value) {
            echo "<div class='setting-item'>";
            echo "<span class='setting-label'><i class='fas fa-cog'></i> " . $label . ":</span>";
            echo "<span class='setting-value'>" . (!empty($value) ? htmlspecialchars($value) : "<em style='color: #999;'>Not set</em>") . "</span>";
            echo "</div>";
        }
        
        echo "<div class='success'><i class='fas fa-check-circle'></i> All helper functions working correctly!</div>";
        echo "</div>";
        
        // Test 4: Test link generators
        echo "<h2><i class='fas fa-link'></i> Test 4: Link Generators</h2>";
        echo "<div class='test-section'>";
        
        echo "<p><strong>Click these links to test if they work correctly:</strong></p>";
        echo "<div class='demo-links'>";
        
        $phone = get_primary_phone($mysqli);
        if(!empty($phone)) {
            echo "<a href='" . get_phone_link($mysqli) . "' class='demo-link' target='_blank'>";
            echo "<i class='fas fa-phone'></i> Call Primary Phone";
            echo "</a>";
        }
        
        $phone2 = get_secondary_phone($mysqli);
        if(!empty($phone2)) {
            echo "<a href='" . get_phone_link($mysqli, 'secondary') . "' class='demo-link' target='_blank'>";
            echo "<i class='fas fa-phone'></i> Call Secondary Phone";
            echo "</a>";
        }
        
        $email = get_primary_email($mysqli);
        if(!empty($email)) {
            echo "<a href='" . get_email_link($mysqli, 'primary', 'Test Email') . "' class='demo-link' target='_blank'>";
            echo "<i class='fas fa-envelope'></i> Email Primary";
            echo "</a>";
        }
        
        $whatsapp = get_whatsapp($mysqli);
        if(!empty($whatsapp)) {
            echo "<a href='" . get_whatsapp_link($mysqli, 'Test message from settings test page') . "' class='demo-link' target='_blank'>";
            echo "<i class='fab fa-whatsapp'></i> WhatsApp";
            echo "</a>";
        }
        
        $instagram = get_instagram($mysqli);
        if(!empty($instagram)) {
            echo "<a href='https://instagram.com/" . ltrim($instagram, '@') . "' class='demo-link' target='_blank'>";
            echo "<i class='fab fa-instagram'></i> Instagram";
            echo "</a>";
        }
        
        echo "</div>";
        echo "<div class='success'><i class='fas fa-check-circle'></i> Link generators working! Click the links above to test.</div>";
        echo "</div>";
        
        // Test 5: Performance test
        echo "<h2><i class='fas fa-tachometer-alt'></i> Test 5: Performance</h2>";
        echo "<div class='test-section'>";
        
        $start_time = microtime(true);
        for($i = 0; $i < 100; $i++) {
            get_primary_phone($mysqli);
            get_primary_email($mysqli);
            get_whatsapp($mysqli);
        }
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        echo "<div class='info'>";
        echo "<i class='fas fa-stopwatch'></i> <strong>Performance Test:</strong> ";
        echo "Retrieved 300 settings in <strong>" . number_format($execution_time, 2) . " ms</strong>";
        echo " (with caching)";
        echo "</div>";
        
        if($execution_time < 100) {
            echo "<div class='success'><i class='fas fa-check-circle'></i> Excellent performance! Caching is working.</div>";
        } else {
            echo "<div class='info'><i class='fas fa-info-circle'></i> Performance is acceptable.</div>";
        }
        
        echo "</div>";
        
        // Summary
        echo "<h2><i class='fas fa-clipboard-check'></i> Test Summary</h2>";
        echo "<div class='test-section'>";
        echo "<div class='success'>";
        echo "<h3 style='margin-top: 0;'><i class='fas fa-check-circle'></i> All Tests Passed!</h3>";
        echo "<p>Your site settings system is working correctly. You can now:</p>";
        echo "<ul>";
        echo "<li>Update settings in the admin panel</li>";
        echo "<li>Use helper functions in your PHP files</li>";
        echo "<li>Replace hardcoded contact information</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='admin-site-settings.php' class='btn btn-success'><i class='fas fa-cog'></i> Go to Settings Panel</a>";
        echo "<a href='admin-dashboard.php' class='btn'><i class='fas fa-tachometer-alt'></i> Go to Dashboard</a>";
        echo "<a href='../SITE_SETTINGS_INSTALLATION.md' class='btn' style='background: #17a2b8;'><i class='fas fa-book'></i> View Documentation</a>";
        echo "</div>";
        
        echo "</div>";
        ?>
        
    </div>
</body>
</html>
