<?php
/**
 * Simplify Technician Availability System
 * Removes redundant status fields and uses only booking capacity for availability
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simplify Technician Availability</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #667eea; }
        .section { background: #f9fafb; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea; border-radius: 5px; }
        .success { border-left-color: #10b981; background: #ecfdf5; }
        .info { border-left-color: #3b82f6; background: #dbeafe; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .btn { padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-success { background: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔄 Simplify Technician Availability System</h2>
        
        <div class="info">
            <h3>Current Problem</h3>
            <p>Your system has <strong>TWO redundant fields</strong> for tracking technician availability:</p>
            <ul>
                <li><code>t_status</code> - VARCHAR field (e.g., "Available", "Booked", "On Leave")</li>
                <li><code>t_is_available</code> - TINYINT field (0 or 1)</li>
            </ul>
            <p><strong>This causes confusion and inconsistency!</strong></p>
        </div>
        
        <div class="section success">
            <h3>✅ Simplified Solution</h3>
            <p><strong>Use ONLY booking capacity to determine availability:</strong></p>
            <div class="code">
-- Technician is AVAILABLE if:
t_current_bookings < t_booking_limit

-- Technician is AT CAPACITY if:
t_current_bookings >= t_booking_limit
            </div>
            <p><strong>Benefits:</strong></p>
            <ul>
                <li>✓ Single source of truth</li>
                <li>✓ No manual status updates needed</li>
                <li>✓ Automatically accurate</li>
                <li>✓ No confusion between fields</li>
            </ul>
        </div>
        
        <?php
        
        $apply_fix = isset($_GET['apply']) && $_GET['apply'] == 'yes';
        
        if (!$apply_fix) {
            // Show current state
            echo "<div class='section'>";
            echo "<h3>📊 Current Technician Status</h3>";
            
            $query = "SELECT 
                        t_id,
                        t_name,
                        t_status,
                        t_is_available,
                        t_booking_limit,
                        t_current_bookings,
                        (t_booking_limit - t_current_bookings) as available_slots,
                        CASE 
                            WHEN t_current_bookings < t_booking_limit THEN 'Available'
                            ELSE 'At Capacity'
                        END as calculated_status
                      FROM tms_technician
                      ORDER BY t_name";
            
            $result = $mysqli->query($query);
            
            if ($result) {
                echo "<table>";
                echo "<tr>
                        <th>Name</th>
                        <th>t_status<br><small>(Old Field)</small></th>
                        <th>t_is_available<br><small>(Old Field)</small></th>
                        <th>Current/Limit</th>
                        <th>Calculated Status<br><small>(New Logic)</small></th>
                        <th>Match?</th>
                      </tr>";
                
                $mismatches = 0;
                while ($row = $result->fetch_assoc()) {
                    $old_status = $row['t_status'];
                    $old_available = $row['t_is_available'];
                    $new_status = $row['calculated_status'];
                    
                    // Check if old fields match new logic
                    $matches = (
                        ($new_status == 'Available' && $old_status == 'Available' && $old_available == 1) ||
                        ($new_status == 'At Capacity' && ($old_status == 'Booked' || $old_status == 'Busy') && $old_available == 0)
                    );
                    
                    if (!$matches) $mismatches++;
                    
                    $match_icon = $matches ? "✓" : "✗";
                    $row_color = $matches ? "" : "background: #fffbeb;";
                    
                    echo "<tr style='$row_color'>";
                    echo "<td><strong>{$row['t_name']}</strong></td>";
                    echo "<td>{$old_status}</td>";
                    echo "<td>" . ($old_available ? 'Yes' : 'No') . "</td>";
                    echo "<td>{$row['t_current_bookings']}/{$row['t_booking_limit']}</td>";
                    echo "<td><strong>{$new_status}</strong></td>";
                    echo "<td>$match_icon</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                if ($mismatches > 0) {
                    echo "<p style='color: #f59e0b;'><strong>⚠ Found $mismatches mismatch(es) between old fields and new logic</strong></p>";
                }
            }
            echo "</div>";
            
            // Show what will be done
            echo "<div class='section'>";
            echo "<h3>🔧 What Will Be Changed</h3>";
            echo "<ol>";
            echo "<li><strong>Remove redundant fields:</strong> Drop <code>t_status</code> and <code>t_is_available</code></li>";
            echo "<li><strong>Use capacity-based availability:</strong> Check <code>t_current_bookings < t_booking_limit</code></li>";
            echo "<li><strong>Update all queries:</strong> Replace status checks with capacity checks</li>";
            echo "<li><strong>Simplify code:</strong> No more manual status updates needed</li>";
            echo "</ol>";
            echo "</div>";
            
            echo "<div class='section info'>";
            echo "<h3>📝 New Availability Logic</h3>";
            echo "<div class='code'>";
            echo "-- Get available technicians<br>";
            echo "SELECT * FROM tms_technician<br>";
            echo "WHERE t_current_bookings < t_booking_limit<br>";
            echo "ORDER BY (t_booking_limit - t_current_bookings) DESC;<br><br>";
            echo "-- Check if specific technician is available<br>";
            echo "SELECT <br>";
            echo "  CASE <br>";
            echo "    WHEN t_current_bookings < t_booking_limit THEN 'Available'<br>";
            echo "    ELSE 'At Capacity'<br>";
            echo "  END as availability_status<br>";
            echo "FROM tms_technician<br>";
            echo "WHERE t_id = ?;";
            echo "</div>";
            echo "</div>";
            
            echo "<hr style='margin: 30px 0;'>";
            echo "<h3>⚠️ Ready to Simplify?</h3>";
            echo "<p><strong>This will:</strong></p>";
            echo "<ul>";
            echo "<li>Remove <code>t_status</code> column (no longer needed)</li>";
            echo "<li>Remove <code>t_is_available</code> column (no longer needed)</li>";
            echo "<li>Use only <code>t_current_bookings</code> and <code>t_booking_limit</code></li>";
            echo "</ul>";
            echo "<p style='color: #ef4444;'><strong>Note:</strong> This will modify your database structure. Make sure you have a backup!</p>";
            echo "<a href='?apply=yes' class='btn btn-success' onclick='return confirm(\"Are you sure? This will remove t_status and t_is_available columns.\")'>Apply Simplification</a>";
            echo "<a href='admin-dashboard.php' class='btn'>Cancel</a>";
            
        } else {
            // Apply the fix
            echo "<div class='section'>";
            echo "<h3>🔧 Applying Simplification...</h3>";
            
            $steps = [];
            $errors = [];
            
            // Step 1: Backup current values (just in case)
            echo "<p>Step 1: Creating backup of current status values...</p>";
            $backup_sql = "CREATE TABLE IF NOT EXISTS tms_technician_status_backup AS 
                          SELECT t_id, t_status, t_is_available, NOW() as backup_date 
                          FROM tms_technician";
            if ($mysqli->query($backup_sql)) {
                echo "<p style='color: green;'>✓ Backup created</p>";
                $steps[] = "Backup created";
            } else {
                echo "<p style='color: orange;'>⚠ Backup may already exist</p>";
            }
            
            // Step 2: Drop t_status column
            echo "<p>Step 2: Removing t_status column...</p>";
            $drop_status = "ALTER TABLE tms_technician DROP COLUMN IF EXISTS t_status";
            if ($mysqli->query($drop_status)) {
                echo "<p style='color: green;'>✓ t_status column removed</p>";
                $steps[] = "Removed t_status column";
            } else {
                echo "<p style='color: red;'>✗ Error: " . $mysqli->error . "</p>";
                $errors[] = "Failed to remove t_status";
            }
            
            // Step 3: Drop t_is_available column
            echo "<p>Step 3: Removing t_is_available column...</p>";
            $drop_available = "ALTER TABLE tms_technician DROP COLUMN IF EXISTS t_is_available";
            if ($mysqli->query($drop_available)) {
                echo "<p style='color: green;'>✓ t_is_available column removed</p>";
                $steps[] = "Removed t_is_available column";
            } else {
                echo "<p style='color: red;'>✗ Error: " . $mysqli->error . "</p>";
                $errors[] = "Failed to remove t_is_available";
            }
            
            // Step 4: Create view for backward compatibility (optional)
            echo "<p>Step 4: Creating compatibility view...</p>";
            $view_sql = "CREATE OR REPLACE VIEW v_technician_availability AS
                        SELECT 
                            t_id,
                            t_name,
                            t_phone,
                            t_email,
                            t_category,
                            t_specialization,
                            t_booking_limit,
                            t_current_bookings,
                            (t_booking_limit - t_current_bookings) as available_slots,
                            CASE 
                                WHEN t_current_bookings < t_booking_limit THEN 'Available'
                                ELSE 'At Capacity'
                            END as availability_status,
                            CASE 
                                WHEN t_current_bookings < t_booking_limit THEN 1
                                ELSE 0
                            END as is_available
                        FROM tms_technician";
            
            if ($mysqli->query($view_sql)) {
                echo "<p style='color: green;'>✓ Compatibility view created</p>";
                echo "<p><small>You can now use <code>v_technician_availability</code> for queries</small></p>";
                $steps[] = "Created compatibility view";
            } else {
                echo "<p style='color: orange;'>⚠ View creation failed (optional): " . $mysqli->error . "</p>";
            }
            
            echo "</div>";
            
            // Summary
            echo "<div class='section success'>";
            echo "<h3>✅ Simplification Complete!</h3>";
            echo "<p><strong>Changes Applied:</strong></p>";
            echo "<ul>";
            foreach ($steps as $step) {
                echo "<li>$step</li>";
            }
            echo "</ul>";
            
            if (count($errors) > 0) {
                echo "<p style='color: red;'><strong>Errors:</strong></p>";
                echo "<ul>";
                foreach ($errors as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul>";
            }
            
            echo "<h4>🎯 New Availability Logic</h4>";
            echo "<p>Technicians are now considered available based ONLY on their booking capacity:</p>";
            echo "<div class='code'>";
            echo "Available = t_current_bookings < t_booking_limit<br>";
            echo "At Capacity = t_current_bookings >= t_booking_limit";
            echo "</div>";
            echo "</div>";
            
            // Show updated technician list
            echo "<div class='section'>";
            echo "<h3>📊 Updated Technician List</h3>";
            
            $query = "SELECT * FROM v_technician_availability ORDER BY t_name";
            $result = $mysqli->query($query);
            
            if ($result) {
                echo "<table>";
                echo "<tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Current Bookings</th>
                        <th>Booking Limit</th>
                        <th>Available Slots</th>
                        <th>Status</th>
                      </tr>";
                
                while ($row = $result->fetch_assoc()) {
                    $status_color = $row['is_available'] ? 'green' : 'red';
                    echo "<tr>";
                    echo "<td><strong>{$row['t_name']}</strong></td>";
                    echo "<td>{$row['t_category']}</td>";
                    echo "<td>{$row['t_current_bookings']}</td>";
                    echo "<td>{$row['t_booking_limit']}</td>";
                    echo "<td><strong>{$row['available_slots']}</strong></td>";
                    echo "<td style='color: $status_color;'><strong>{$row['availability_status']}</strong></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            echo "</div>";
            
            echo "<hr style='margin: 30px 0;'>";
            echo "<h3>🔧 Next Steps</h3>";
            echo "<a href='verify-database-structure.php' class='btn btn-success'>Verify Database</a>";
            echo "<a href='test-booking-limit-system.php' class='btn'>Run Tests</a>";
            echo "<a href='admin-manage-technician.php' class='btn'>Manage Technicians</a>";
            echo "<a href='admin-dashboard.php' class='btn'>Dashboard</a>";
        }
        
        ?>
        
        <hr style="margin: 30px 0;">
        <h3>📖 How It Works Now</h3>
        <div class="code">
// OLD WAY (Redundant):<br>
if ($tech['t_status'] == 'Available' && $tech['t_is_available'] == 1) {<br>
&nbsp;&nbsp;// Show technician<br>
}<br>
<br>
// NEW WAY (Simple):<br>
if ($tech['t_current_bookings'] < $tech['t_booking_limit']) {<br>
&nbsp;&nbsp;// Show technician - they have capacity!<br>
}
        </div>
        
        <h3>📝 Query Examples</h3>
        <div class="code">
-- Get all available technicians<br>
SELECT * FROM tms_technician<br>
WHERE t_current_bookings < t_booking_limit<br>
ORDER BY (t_booking_limit - t_current_bookings) DESC;<br>
<br>
-- Or use the view for easier queries<br>
SELECT * FROM v_technician_availability<br>
WHERE is_available = 1;
        </div>
    </div>
</body>
</html>
