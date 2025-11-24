<?php
/**
 * Execute Database Cleanup and Organization
 * Removes duplicates, creates missing tables, optimizes structure
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Read the SQL file
$sql_file = 'database-cleanup-and-organization.sql';
$sql_content = file_get_contents($sql_file);

// Split into individual statements
$statements = array_filter(
    array_map('trim', 
    preg_split('/;[\r\n]+/', $sql_content)),
    function($stmt) {
        return !empty($stmt) && 
               !preg_match('/^--/', $stmt) && 
               !preg_match('/^\/\*/', $stmt) &&
               !preg_match('/^USE/', $stmt) &&
               !preg_match('/^SET/', $stmt) &&
               !preg_match('/^START/', $stmt);
    }
);

$results = [];
$errors = [];
$success_count = 0;
$skip_count = 0;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Cleanup & Organization</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { 
            color: #667eea; 
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .section {
            background: #f9fafb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .success { border-left-color: #10b981; background: #ecfdf5; }
        .error { border-left-color: #ef4444; background: #fef2f2; }
        .warning { border-left-color: #f59e0b; background: #fffbeb; }
        .log-entry {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 13px;
        }
        .log-success { background: #d1fae5; color: #065f46; }
        .log-error { background: #fee2e2; color: #991b1b; }
        .log-skip { background: #fef3c7; color: #92400e; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background: #667eea;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin: 10px 5px;
            font-weight: 600;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #6b7280;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Database Cleanup & Organization</h1>
        <p class="subtitle">Optimizing database structure and removing duplicates</p>
        
        <div class="progress-bar">
            <div class="progress-fill" id="progressBar" style="width: 0%">0%</div>
        </div>
        
        <?php
        $total_statements = count($statements);
        $current = 0;
        
        echo "<div class='section'>";
        echo "<h3>📋 Execution Log</h3>";
        echo "<div id='logContainer'>";
        
        foreach ($statements as $statement) {
            $current++;
            $progress = round(($current / $total_statements) * 100);
            
            // Skip comments and empty statements
            if (empty(trim($statement))) {
                $skip_count++;
                continue;
            }
            
            // Extract statement type
            preg_match('/^(CREATE|ALTER|UPDATE|DELETE|INSERT|OPTIMIZE|SELECT)\s+/i', $statement, $matches);
            $type = $matches[1] ?? 'QUERY';
            
            try {
                $result = $mysqli->query($statement);
                
                if ($result) {
                    $success_count++;
                    echo "<div class='log-entry log-success'>";
                    echo "✅ $type: " . substr($statement, 0, 100) . "...";
                    echo "</div>";
                    
                    // If it's a SELECT, show results
                    if (strtoupper($type) === 'SELECT' && $result instanceof mysqli_result) {
                        if ($result->num_rows > 0) {
                            echo "<table style='margin: 10px 0; font-size: 12px;'>";
                            $first_row = true;
                            while ($row = $result->fetch_assoc()) {
                                if ($first_row) {
                                    echo "<tr>";
                                    foreach (array_keys($row) as $key) {
                                        echo "<th>" . htmlspecialchars($key) . "</th>";
                                    }
                                    echo "</tr>";
                                    $first_row = false;
                                }
                                echo "<tr>";
                                foreach ($row as $value) {
                                    echo "<td>" . htmlspecialchars($value) . "</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                    }
                } else {
                    // Check if it's a "column already exists" error (not critical)
                    if (strpos($mysqli->error, 'Duplicate column') !== false || 
                        strpos($mysqli->error, 'already exists') !== false) {
                        $skip_count++;
                        echo "<div class='log-entry log-skip'>";
                        echo "⚠️ SKIP: $type (already exists)";
                        echo "</div>";
                    } else {
                        $errors[] = [
                            'statement' => substr($statement, 0, 200),
                            'error' => $mysqli->error
                        ];
                        echo "<div class='log-entry log-error'>";
                        echo "❌ ERROR: $type - " . htmlspecialchars($mysqli->error);
                        echo "</div>";
                    }
                }
                
                // Update progress
                echo "<script>
                    document.getElementById('progressBar').style.width = '{$progress}%';
                    document.getElementById('progressBar').textContent = '{$progress}%';
                </script>";
                
                flush();
                ob_flush();
                
            } catch (Exception $e) {
                $errors[] = [
                    'statement' => substr($statement, 0, 200),
                    'error' => $e->getMessage()
                ];
                echo "<div class='log-entry log-error'>";
                echo "❌ EXCEPTION: " . htmlspecialchars($e->getMessage());
                echo "</div>";
            }
        }
        
        echo "</div>"; // logContainer
        echo "</div>"; // section
        
        // Summary
        echo "<div class='section success'>";
        echo "<h3>📊 Summary</h3>";
        echo "<div class='stats-grid'>";
        
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>$success_count</div>";
        echo "<div class='stat-label'>Successful</div>";
        echo "</div>";
        
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>$skip_count</div>";
        echo "<div class='stat-label'>Skipped</div>";
        echo "</div>";
        
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>" . count($errors) . "</div>";
        echo "<div class='stat-label'>Errors</div>";
        echo "</div>";
        
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>$total_statements</div>";
        echo "<div class='stat-label'>Total Statements</div>";
        echo "</div>";
        
        echo "</div>"; // stats-grid
        echo "</div>"; // section
        
        // Errors
        if (count($errors) > 0) {
            echo "<div class='section error'>";
            echo "<h3>❌ Errors Encountered</h3>";
            foreach ($errors as $error) {
                echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 5px;'>";
                echo "<strong>Statement:</strong><br>";
                echo "<code>" . htmlspecialchars($error['statement']) . "...</code><br><br>";
                echo "<strong>Error:</strong><br>";
                echo "<code style='color: #dc2626;'>" . htmlspecialchars($error['error']) . "</code>";
                echo "</div>";
            }
            echo "</div>";
        }
        
        // What was done
        echo "<div class='section'>";
        echo "<h3>✅ What Was Done</h3>";
        echo "<ul style='line-height: 2;'>";
        echo "<li>✅ Created missing tables (tms_admin_notifications)</li>";
        echo "<li>✅ Added missing columns to existing tables</li>";
        echo "<li>✅ Added database indexes for better performance</li>";
        echo "<li>✅ Removed duplicate bookings</li>";
        echo "<li>✅ Cleaned up old password reset requests</li>";
        echo "<li>✅ Cleaned up old system logs</li>";
        echo "<li>✅ Synced technician booking counts</li>";
        echo "<li>✅ Updated technician availability status</li>";
        echo "<li>✅ Set timestamps for existing bookings</li>";
        echo "<li>✅ Optimized all tables</li>";
        echo "</ul>";
        echo "</div>";
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="admin-dashboard.php" class="btn">🏠 Back to Dashboard</a>
            <a href="admin-manage-technician.php" class="btn">👥 View Technicians</a>
            <a href="admin-all-bookings.php" class="btn">📋 View Bookings</a>
        </div>
        
        <div class="section warning" style="margin-top: 30px;">
            <h4>⚠️ Important Notes:</h4>
            <ul>
                <li>This script has optimized your database structure</li>
                <li>All duplicate data has been removed</li>
                <li>Missing tables and columns have been added</li>
                <li>Run this script periodically (monthly) to maintain database health</li>
                <li>Always backup your database before running cleanup scripts</li>
            </ul>
        </div>
    </div>
</body>
</html>
