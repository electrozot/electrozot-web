<?php
/**
 * COMPLETE FIX: Technician Assignment Issues
 * This script will fix all common issues preventing technician assignment
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$fixes_applied = [];
$errors = [];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Technician Assignment</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { 
            color: #667eea; 
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .fix-box {
            background: #f9fafb;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        .fix-box.success { border-left-color: #10b981; background: #ecfdf5; }
        .fix-box.error { border-left-color: #ef4444; background: #fef2f2; }
        .fix-box.warning { border-left-color: #f59e0b; background: #fffbeb; }
        .fix-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .fix-description {
            color: #4b5563;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 5px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 42px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #6b7280;
            margin-top: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Technician Assignment Issues</h1>
        <p class="subtitle">Automatically detecting and fixing common problems</p>
        
        <?php
        // FIX 1: Add missing columns
        echo "<div class='fix-box'>";
        echo "<div class='fix-title'>📋 Step 1: Checking Database Structure</div>";
        echo "<div class='fix-description'>";
        
        $columns_to_add = [
            "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_deadline_date DATE DEFAULT NULL",
            "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_deadline_time TIME DEFAULT NULL",
            "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejected_at TIMESTAMP NULL DEFAULT NULL",
            "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completed_at TIMESTAMP NULL DEFAULT NULL",
            "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL DEFAULT NULL",
            "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0",
            "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 5",
            "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_status VARCHAR(20) DEFAULT 'Available'",
            "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_booking_id INT NULL DEFAULT NULL"
        ];
        
        foreach($columns_to_add as $sql) {
            if($mysqli->query($sql)) {
                echo "✅ Column check passed<br>";
            } else {
                if(strpos($mysqli->error, 'Duplicate column') === false) {
                    echo "⚠️ " . $mysqli->error . "<br>";
                }
            }
        }
        
        echo "</div></div>";
        
        // FIX 2: Sync booking counts
        echo "<div class='fix-box success'>";
        echo "<div class='fix-title'>🔄 Step 2: Syncing Technician Booking Counts</div>";
        echo "<div class='fix-description'>";
        
        $sync_query = "UPDATE tms_technician t
                      SET t_current_bookings = (
                          SELECT COUNT(*)
                          FROM tms_service_booking sb
                          WHERE sb.sb_technician_id = t.t_id
                          AND sb.sb_status IN ('Pending', 'Approved', 'In Progress', 'Assigned')
                      )";
        
        if($mysqli->query($sync_query)) {
            $affected = $mysqli->affected_rows;
            echo "✅ Successfully synced booking counts for $affected technician(s)<br>";
            $fixes_applied[] = "Synced booking counts";
        } else {
            echo "❌ Error: " . $mysqli->error . "<br>";
            $errors[] = "Failed to sync booking counts";
        }
        
        echo "</div></div>";
        
        // FIX 3: Update technician status
        echo "<div class='fix-box success'>";
        echo "<div class='fix-title'>⚡ Step 3: Updating Technician Availability Status</div>";
        echo "<div class='fix-description'>";
        
        $status_query = "UPDATE tms_technician
                        SET t_status = CASE
                            WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
                            ELSE 'Available'
                        END";
        
        if($mysqli->query($status_query)) {
            $affected = $mysqli->affected_rows;
            echo "✅ Updated status for $affected technician(s)<br>";
            $fixes_applied[] = "Updated technician status";
        } else {
            echo "❌ Error: " . $mysqli->error . "<br>";
            $errors[] = "Failed to update status";
        }
        
        echo "</div></div>";
        
        // FIX 4: Set default booking limits
        echo "<div class='fix-box success'>";
        echo "<div class='fix-title'>📊 Step 4: Setting Default Booking Limits</div>";
        echo "<div class='fix-description'>";
        
        $limit_query = "UPDATE tms_technician 
                       SET t_booking_limit = 5 
                       WHERE t_booking_limit IS NULL OR t_booking_limit = 0";
        
        if($mysqli->query($limit_query)) {
            $affected = $mysqli->affected_rows;
            if($affected > 0) {
                echo "✅ Set default limit (5 bookings) for $affected technician(s)<br>";
                $fixes_applied[] = "Set default booking limits";
            } else {
                echo "✅ All technicians already have booking limits set<br>";
            }
        } else {
            echo "❌ Error: " . $mysqli->error . "<br>";
        }
        
        echo "</div></div>";
        
        // FIX 5: Clear stuck bookings
        echo "<div class='fix-box success'>";
        echo "<div class='fix-title'>🧹 Step 5: Clearing Stuck Assignments</div>";
        echo "<div class='fix-description'>";
        
        // Free up technicians from completed/rejected bookings
        $clear_query = "UPDATE tms_technician t
                       SET t_current_booking_id = NULL
                       WHERE t_current_booking_id IN (
                           SELECT sb_id FROM tms_service_booking 
                           WHERE sb_status IN ('Completed', 'Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done')
                       )";
        
        if($mysqli->query($clear_query)) {
            $affected = $mysqli->affected_rows;
            if($affected > 0) {
                echo "✅ Cleared $affected stuck assignment(s)<br>";
                $fixes_applied[] = "Cleared stuck assignments";
            } else {
                echo "✅ No stuck assignments found<br>";
            }
        } else {
            echo "❌ Error: " . $mysqli->error . "<br>";
        }
        
        echo "</div></div>";
        
        // RESULTS: Show current technician status
        echo "<div class='fix-box'>";
        echo "<div class='fix-title'>📊 Current Technician Status</div>";
        
        $tech_query = "SELECT t_id, t_name, t_category, t_status, t_booking_limit, t_current_bookings,
                      (t_booking_limit - t_current_bookings) as available_slots
                      FROM tms_technician 
                      ORDER BY t_name";
        $tech_result = $mysqli->query($tech_query);
        
        if($tech_result && $tech_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Name</th><th>Category</th><th>Status</th><th>Current</th><th>Limit</th><th>Available Slots</th></tr>";
            
            $total_available = 0;
            while($tech = $tech_result->fetch_assoc()) {
                $status_badge = $tech['t_status'] == 'Available' ? 'badge-success' : 'badge-warning';
                $slots_badge = $tech['available_slots'] > 0 ? 'badge-success' : 'badge-danger';
                
                if($tech['available_slots'] > 0) $total_available++;
                
                echo "<tr>";
                echo "<td><strong>{$tech['t_name']}</strong></td>";
                echo "<td>{$tech['t_category']}</td>";
                echo "<td><span class='badge $status_badge'>{$tech['t_status']}</span></td>";
                echo "<td>{$tech['t_current_bookings']}</td>";
                echo "<td>{$tech['t_booking_limit']}</td>";
                echo "<td><span class='badge $slots_badge'>{$tech['available_slots']}</span></td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<div class='stats'>";
            echo "<div class='stat-card'>";
            echo "<div class='stat-number'>$total_available</div>";
            echo "<div class='stat-label'>Available Technicians</div>";
            echo "</div>";
            echo "</div>";
        }
        
        echo "</div>";
        
        // SUMMARY
        if(count($fixes_applied) > 0) {
            echo "<div class='fix-box success'>";
            echo "<div class='fix-title'>✅ Fixes Applied Successfully</div>";
            echo "<div class='fix-description'>";
            echo "<ul>";
            foreach($fixes_applied as $fix) {
                echo "<li>$fix</li>";
            }
            echo "</ul>";
            echo "<p><strong>You can now assign technicians to bookings!</strong></p>";
            echo "</div></div>";
        }
        
        if(count($errors) > 0) {
            echo "<div class='fix-box error'>";
            echo "<div class='fix-title'>❌ Some Issues Remain</div>";
            echo "<div class='fix-description'>";
            echo "<ul>";
            foreach($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo "</div></div>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 10px;">
            <h3 style="color: #667eea; margin-bottom: 20px;">🎯 Next Steps</h3>
            <a href="admin-manage-service-booking.php" class="btn">📋 Manage Bookings</a>
            <a href="admin-assign-technician.php?sb_id=<?php 
                $pending = $mysqli->query("SELECT sb_id FROM tms_service_booking WHERE sb_status='Pending' LIMIT 1");
                if($pending && $pending->num_rows > 0) {
                    echo $pending->fetch_object()->sb_id;
                }
            ?>" class="btn">👨‍🔧 Assign Technician</a>
            <a href="admin-dashboard.php" class="btn">🏠 Dashboard</a>
        </div>
        
        <div class="fix-box warning" style="margin-top: 30px;">
            <div class="fix-title">💡 Tips for Successful Assignment</div>
            <div class="fix-description">
                <ol style="line-height: 2;">
                    <li><strong>Check technician availability:</strong> Make sure technician has available slots</li>
                    <li><strong>Match skills:</strong> Assign technicians with matching service skills</li>
                    <li><strong>Set deadline:</strong> Always set service deadline date and time</li>
                    <li><strong>Monitor capacity:</strong> Run this fix script if assignments fail again</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
