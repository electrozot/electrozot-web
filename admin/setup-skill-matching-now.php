<?php
/**
 * Setup Skill-Based Matching System
 * Run this ONCE to enable skill-based technician matching
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Skill Matching System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .step {
            background: #ecf0f1;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 15px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #3498db;
            color: white;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Setup Skill-Based Matching System</h1>
        
        <?php
        // Step 1: Check if column exists
        echo "<div class='step'>";
        echo "<h3>Step 1: Checking Database Structure</h3>";
        
        $check_column = "SELECT COLUMN_NAME 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'tms_technician' 
                        AND COLUMN_NAME = 't_skills'";
        $result = $mysqli->query($check_column);
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='success'>✅ Column <code>t_skills</code> already exists!</div>";
            $column_exists = true;
        } else {
            echo "<div class='info'>⚠️ Column <code>t_skills</code> does not exist. Creating it now...</div>";
            $column_exists = false;
            
            // Create the column
            $add_column = "ALTER TABLE `tms_technician` 
                          ADD COLUMN `t_skills` TEXT DEFAULT NULL
                          COMMENT 'Comma-separated list of services technician can perform'";
            
            if ($mysqli->query($add_column)) {
                echo "<div class='success'>✅ Successfully created <code>t_skills</code> column!</div>";
                $column_exists = true;
            } else {
                echo "<div class='error'>❌ Error creating column: " . $mysqli->error . "</div>";
            }
        }
        echo "</div>";
        
        // Step 2: Add index for faster searching
        if ($column_exists) {
            echo "<div class='step'>";
            echo "<h3>Step 2: Adding Search Index</h3>";
            
            // Check if index exists
            $check_index = "SHOW INDEX FROM tms_technician WHERE Key_name = 'idx_t_skills'";
            $index_result = $mysqli->query($check_index);
            
            if ($index_result && $index_result->num_rows > 0) {
                echo "<div class='success'>✅ Search index already exists!</div>";
            } else {
                // Try to add fulltext index (may fail on some MySQL versions)
                $add_index = "ALTER TABLE `tms_technician` ADD FULLTEXT INDEX `idx_t_skills` (`t_skills`)";
                
                if ($mysqli->query($add_index)) {
                    echo "<div class='success'>✅ Successfully added search index!</div>";
                } else {
                    echo "<div class='info'>ℹ️ Could not add fulltext index (not critical): " . $mysqli->error . "</div>";
                }
            }
            echo "</div>";
        }
        
        // Step 3: Show current technicians
        if ($column_exists) {
            echo "<div class='step'>";
            echo "<h3>Step 3: Current Technicians</h3>";
            
            $tech_query = "SELECT t_id, t_name, t_category, t_specialization, t_skills, 
                          t_booking_limit, t_current_bookings, t_status
                          FROM tms_technician 
                          ORDER BY t_id";
            $tech_result = $mysqli->query($tech_query);
            
            if ($tech_result && $tech_result->num_rows > 0) {
                echo "<table>";
                echo "<tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Skills</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>";
                
                while ($tech = $tech_result->fetch_assoc()) {
                    $has_skills = !empty($tech['t_skills']);
                    $skills_display = $has_skills ? substr($tech['t_skills'], 0, 50) . '...' : '<em style="color: #999;">No skills set</em>';
                    $status_color = $has_skills ? '#28a745' : '#dc3545';
                    
                    echo "<tr>";
                    echo "<td>{$tech['t_id']}</td>";
                    echo "<td>{$tech['t_name']}</td>";
                    echo "<td>{$tech['t_category']}</td>";
                    echo "<td>{$skills_display}</td>";
                    echo "<td><span style='color: {$status_color}; font-weight: bold;'>" . 
                         ($has_skills ? '✅ Ready' : '⚠️ Needs Setup') . "</span></td>";
                    echo "<td><a href='admin-edit-technician-skills.php?t_id={$tech['t_id']}' class='btn btn-success'>Add Skills</a></td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<div class='info'>No technicians found in the system.</div>";
            }
            
            echo "</div>";
        }
        
        // Step 4: Instructions
        echo "<div class='step'>";
        echo "<h3>📋 Next Steps</h3>";
        echo "<ol>";
        echo "<li><strong>Add Skills to Each Technician:</strong> Click 'Add Skills' button above for each technician</li>";
        echo "<li><strong>Select Services:</strong> Check all services each technician can perform</li>";
        echo "<li><strong>Save:</strong> Skills will be saved automatically</li>";
        echo "<li><strong>Test:</strong> Create a booking and assign a technician - you'll see only qualified technicians!</li>";
        echo "</ol>";
        echo "</div>";
        
        // Step 5: How it works
        echo "<div class='info'>";
        echo "<h3>ℹ️ How Skill-Based Matching Works</h3>";
        echo "<p><strong>Example:</strong> If a customer books 'Wash Basin Installation':</p>";
        echo "<ol>";
        echo "<li>System searches for technicians with 'Wash Basin Installation' in their skills</li>";
        echo "<li>Shows only technicians who can perform this service</li>";
        echo "<li>Checks their availability and booking capacity</li>";
        echo "<li>Displays them sorted by best match and availability</li>";
        echo "</ol>";
        echo "<p><strong>Benefits:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Right technician for the right job</li>";
        echo "<li>✅ Better service quality</li>";
        echo "<li>✅ Reduced assignment errors</li>";
        echo "<li>✅ Automatic skill matching</li>";
        echo "</ul>";
        echo "</div>";
        
        // Navigation buttons
        echo "<div style='margin-top: 30px; text-align: center;'>";
        echo "<a href='admin-manage-technician.php' class='btn btn-success'>Manage Technicians</a>";
        echo "<a href='admin-dashboard.php' class='btn'>Back to Dashboard</a>";
        echo "<a href='?' class='btn'>Refresh Page</a>";
        echo "</div>";
        ?>
    </div>
</body>
</html>
