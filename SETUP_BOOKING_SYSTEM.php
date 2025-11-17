<?php
/**
 * QUICK SETUP SCRIPT
 * Run this once to set up the complete booking system
 */

// Database configuration
$host = 'localhost';
$dbname = 'electrozot_db';
$username = 'root';
$password = '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking System Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .content { padding: 30px; }
        .step {
            background: #f8f9fa;
            border-left: 5px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .step h3 { color: #667eea; margin-bottom: 10px; }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        .btn {
            padding: 15px 40px;
            font-size: 1.1em;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3); }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        ul { margin-left: 20px; margin-top: 10px; }
        li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Booking System Setup</h1>
            <p>Complete Implementation in 3 Steps</p>
        </div>
        
        <div class="content">
            
            <?php if (!isset($_GET['step'])): ?>
            
            <!-- STEP 0: Introduction -->
            <div class="step warning">
                <h3>⚠️ Before You Start</h3>
                <ul>
                    <li>Backup your database first!</li>
                    <li>Make sure you have phpMyAdmin access</li>
                    <li>This will add new tables and update existing ones</li>
                    <li>Estimated time: 5 minutes</li>
                </ul>
            </div>
            
            <div class="step">
                <h3>📋 What Will Be Installed</h3>
                <ul>
                    <li>✅ Booking limits system (1-5 bookings per technician)</li>
                    <li>✅ Real-time notifications for admin</li>
                    <li>✅ Technician accept/reject/complete system</li>
                    <li>✅ Automatic status updates</li>
                    <li>✅ Booking history tracking</li>
                    <li>✅ Guest user permanent records</li>
                    <li>✅ Daily technician statistics</li>
                    <li>✅ Complete API endpoints</li>
                </ul>
            </div>
            
            <div class="step">
                <h3>🎯 Features You'll Get</h3>
                <ul>
                    <li>Admin can assign bookings to technicians</li>
                    <li>Admin can set booking limits (1-5) per technician</li>
                    <li>Admin can reassign bookings anytime</li>
                    <li>Technicians can accept/reject/complete bookings</li>
                    <li>Automatic slot management</li>
                    <li>Real-time notifications with sound alerts</li>
                    <li>User cannot cancel after technician assigned</li>
                    <li>Complete booking history</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="?step=1" class="btn">Start Setup →</a>
            </div>
            
            <?php elseif ($_GET['step'] == 1): ?>
            
            <!-- STEP 1: Database Update -->
            <div class="step">
                <h3>Step 1: Update Database</h3>
                <p>You need to run the SQL file to update your database structure.</p>
            </div>
            
            <div class="step warning">
                <h3>📝 Instructions:</h3>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Open phpMyAdmin in your browser</li>
                    <li>Select database: <code>electrozot_db</code></li>
                    <li>Click on "Import" tab</li>
                    <li>Choose file: <code>DATABASE FILE/COMPLETE_SYSTEM_UPDATE.sql</code></li>
                    <li>Click "Go" button</li>
                    <li>Wait for success message</li>
                </ol>
            </div>
            
            <div class="step">
                <h3>Alternative: Manual SQL Execution</h3>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Open phpMyAdmin</li>
                    <li>Select database: <code>electrozot_db</code></li>
                    <li>Click on "SQL" tab</li>
                    <li>Open file: <code>DATABASE FILE/COMPLETE_SYSTEM_UPDATE.sql</code></li>
                    <li>Copy all content</li>
                    <li>Paste into SQL tab</li>
                    <li>Click "Go"</li>
                </ol>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="?step=2" class="btn">I've Updated the Database →</a>
            </div>
            
            <?php elseif ($_GET['step'] == 2): ?>
            
            <!-- STEP 2: Verify Installation -->
            <div class="step">
                <h3>Step 2: Verify Installation</h3>
                <p>Let's check if everything was installed correctly.</p>
            </div>
            
            <?php
            try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $tables_to_check = [
                    'tms_booking_history' => 'Booking History',
                    'tms_admin_notifications' => 'Admin Notifications',
                    'tms_technician_notifications' => 'Technician Notifications',
                    'tms_user_notifications' => 'User Notifications',
                    'tms_technician_daily_stats' => 'Daily Statistics',
                    'tms_guest_users' => 'Guest Users',
                    'tms_settings' => 'System Settings'
                ];
                
                $all_good = true;
                
                foreach ($tables_to_check as $table => $name) {
                    $stmt = $conn->query("SHOW TABLES LIKE '$table'");
                    if ($stmt->rowCount() > 0) {
                        echo "<div class='step success'>";
                        echo "<h3>✅ $name Table</h3>";
                        echo "<p>Table <code>$table</code> exists and ready to use.</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='step error'>";
                        echo "<h3>❌ $name Table</h3>";
                        echo "<p>Table <code>$table</code> not found. Please run the SQL file again.</p>";
                        echo "</div>";
                        $all_good = false;
                    }
                }
                
                // Check if columns were added
                $stmt = $conn->query("SHOW COLUMNS FROM tms_technician LIKE 't_booking_limit'");
                if ($stmt->rowCount() > 0) {
                    echo "<div class='step success'>";
                    echo "<h3>✅ Technician Table Updated</h3>";
                    echo "<p>Booking limit columns added successfully.</p>";
                    echo "</div>";
                } else {
                    echo "<div class='step error'>";
                    echo "<h3>❌ Technician Table Not Updated</h3>";
                    echo "<p>Please run the SQL file again.</p>";
                    echo "</div>";
                    $all_good = false;
                }
                
                if ($all_good) {
                    echo "<div class='step success'>";
                    echo "<h3>🎉 Installation Successful!</h3>";
                    echo "<p>All database tables and columns are ready.</p>";
                    echo "</div>";
                    
                    echo "<div style='text-align: center; margin-top: 30px;'>";
                    echo "<a href='?step=3' class='btn'>Continue to Final Step →</a>";
                    echo "</div>";
                } else {
                    echo "<div style='text-align: center; margin-top: 30px;'>";
                    echo "<a href='?step=1' class='btn'>← Back to Step 1</a>";
                    echo "</div>";
                }
                
            } catch (PDOException $e) {
                echo "<div class='step error'>";
                echo "<h3>❌ Database Connection Error</h3>";
                echo "<p>Could not connect to database. Please check your configuration.</p>";
                echo "<p>Error: " . $e->getMessage() . "</p>";
                echo "</div>";
            }
            ?>
            
            <?php elseif ($_GET['step'] == 3): ?>
            
            <!-- STEP 3: Final Instructions -->
            <div class="step success">
                <h3>🎉 Setup Complete!</h3>
                <p>Your booking system is now ready to use.</p>
            </div>
            
            <div class="step">
                <h3>📁 Files Created</h3>
                <ul>
                    <li><code>admin/BookingSystem.php</code> - Core booking logic</li>
                    <li><code>admin/api-*.php</code> - Admin API endpoints (4 files)</li>
                    <li><code>tech/api-*.php</code> - Technician API endpoints (4 files)</li>
                    <li><code>DATABASE FILE/COMPLETE_SYSTEM_UPDATE.sql</code> - Database structure</li>
                    <li><code>IMPLEMENTATION_GUIDE.md</code> - Complete documentation</li>
                </ul>
            </div>
            
            <div class="step">
                <h3>🚀 Next Steps</h3>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li><strong>Read the Implementation Guide:</strong>
                        <br>Open <code>IMPLEMENTATION_GUIDE.md</code> for detailed instructions
                    </li>
                    <li><strong>Set Technician Booking Limits:</strong>
                        <br>Go to Admin → Manage Technicians → Set limits (1-5)
                    </li>
                    <li><strong>Test the System:</strong>
                        <br>Create a test booking and assign to technician
                    </li>
                    <li><strong>Integrate Real-time Notifications:</strong>
                        <br>Add notification polling to admin dashboard
                    </li>
                </ol>
            </div>
            
            <div class="step warning">
                <h3>⚡ Quick Test</h3>
                <p>To test if everything works:</p>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Go to Admin Dashboard</li>
                    <li>Create a new booking</li>
                    <li>Assign it to a technician</li>
                    <li>Check technician's current bookings count</li>
                    <li>Login as technician and accept/reject/complete</li>
                </ol>
            </div>
            
            <div class="step">
                <h3>📚 Documentation</h3>
                <ul>
                    <li><strong>IMPLEMENTATION_GUIDE.md</strong> - Complete setup guide</li>
                    <li><strong>Database Schema</strong> - All tables documented in SQL file</li>
                    <li><strong>API Endpoints</strong> - All endpoints ready to use</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="admin/admin-dashboard.php" class="btn">Go to Admin Dashboard</a>
                <a href="IMPLEMENTATION_GUIDE.md" class="btn" target="_blank">Read Full Guide</a>
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>
