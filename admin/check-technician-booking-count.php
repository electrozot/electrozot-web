<?php
/**
 * Check and Fix Individual Technician Booking Count
 * Quick diagnostic tool for admins
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$technician_id = isset($_GET['t_id']) ? intval($_GET['t_id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Technician Booking Count Checker</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #667eea; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .correct { background: #e8f5e9; }
        .incorrect { background: #ffebee; }
        .btn { padding: 8px 15px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-success { background: #10b981; }
        .btn-danger { background: #ef4444; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .alert-success { background: #d1fae5; border-left: 4px solid #10b981; }
        .alert-danger { background: #fee2e2; border-left: 4px solid #ef4444; }
        .alert-info { background: #dbeafe; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Technician Booking Count Checker</h2>
        
        <?php
        // Handle fix action
        if ($action == 'fix' && $technician_id > 0) {
            $fix_sql = "UPDATE tms_technician t
                       SET t_current_bookings = (
                           SELECT COUNT(*) 
                           FROM tms_service_booking sb 
                           WHERE sb.sb_technician_id = t.t_id 
                           AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
                       )
                       WHERE t.t_id = ?";
            
            $stmt = $mysqli->prepare($fix_sql);
            $stmt->bind_param('i', $technician_id);
            
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>✓ Fixed booking count for technician #$technician_id</div>";
            } else {
                echo "<div class='alert alert-danger'>✗ Error fixing count: " . $mysqli->error . "</div>";
            }
        }
        
        // Handle fix all action
        if ($action == 'fix_all') {
            $fix_all_sql = "UPDATE tms_technician t
                           SET t_current_bookings = (
                               SELECT COUNT(*) 
                               FROM tms_service_booking sb 
                               WHERE sb.sb_technician_id = t.t_id 
                               AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
                           )";
            
            if ($mysqli->query($fix_all_sql)) {
                $affected = $mysqli->affected_rows;
                echo "<div class='alert alert-success'>✓ Fixed booking counts for all technicians ($affected updated)</div>";
            } else {
                echo "<div class='alert alert-danger'>✗ Error: " . $mysqli->error . "</div>";
            }
        }
        
        // Get all technicians with their booking counts
        $query = "SELECT 
                    t.t_id,
                    t.t_name,
                    t.t_phone,
                    t.t_category,
                    t.t_booking_limit,
                    t.t_current_bookings,
                    (t.t_booking_limit - t.t_current_bookings) as available_slots,
                    (SELECT COUNT(*) 
                     FROM tms_service_booking sb 
                     WHERE sb.sb_technician_id = t.t_id 
                     AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
                    ) as actual_active_bookings,
                    (SELECT GROUP_CONCAT(sb_id SEPARATOR ', ')
                     FROM tms_service_booking sb 
                     WHERE sb.sb_technician_id = t.t_id 
                     AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
                    ) as active_booking_ids
                  FROM tms_technician t
                  ORDER BY t.t_name";
        
        $result = $mysqli->query($query);
        
        if (!$result) {
            echo "<div class='alert alert-danger'>Error: " . $mysqli->error . "</div>";
        } else {
            $total = 0;
            $correct = 0;
            $incorrect = 0;
            
            echo "<div class='alert alert-info'>";
            echo "<strong>📊 System Status:</strong><br>";
            echo "This tool checks if the booking counter matches the actual active bookings for each technician.";
            echo "</div>";
            
            echo "<table>";
            echo "<thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Limit</th>
                        <th>Counter</th>
                        <th>Actual</th>
                        <th>Available</th>
                        <th>Active Bookings</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                $total++;
                $is_correct = ($row['t_current_bookings'] == $row['actual_active_bookings']);
                
                if ($is_correct) {
                    $correct++;
                    $status = "<span style='color: green; font-weight: bold;'>✓ Correct</span>";
                    $row_class = "correct";
                    $action_btn = "";
                } else {
                    $incorrect++;
                    $status = "<span style='color: red; font-weight: bold;'>✗ Mismatch</span>";
                    $row_class = "incorrect";
                    $action_btn = "<a href='?action=fix&t_id={$row['t_id']}' class='btn btn-danger' onclick='return confirm(\"Fix booking count for {$row['t_name']}?\")'>Fix Now</a>";
                }
                
                $booking_ids = $row['active_booking_ids'] ? $row['active_booking_ids'] : 'None';
                
                echo "<tr class='$row_class'>
                        <td>{$row['t_id']}</td>
                        <td><strong>{$row['t_name']}</strong></td>
                        <td>{$row['t_category']}</td>
                        <td>{$row['t_booking_limit']}</td>
                        <td><strong>{$row['t_current_bookings']}</strong></td>
                        <td><strong>{$row['actual_active_bookings']}</strong></td>
                        <td>{$row['available_slots']}</td>
                        <td><small>$booking_ids</small></td>
                        <td>$status</td>
                        <td>$action_btn</td>
                      </tr>";
            }
            
            echo "</tbody></table>";
            
            // Summary
            echo "<div style='background: #f9fafb; padding: 20px; border-radius: 5px; margin-top: 20px;'>";
            echo "<h3>📈 Summary</h3>";
            echo "<p><strong>Total Technicians:</strong> $total</p>";
            echo "<p style='color: green;'><strong>Correct Counters:</strong> $correct</p>";
            echo "<p style='color: red;'><strong>Incorrect Counters:</strong> $incorrect</p>";
            
            if ($incorrect > 0) {
                echo "<div class='alert alert-danger'>";
                echo "<strong>⚠ Action Required:</strong> $incorrect technician(s) have incorrect booking counts.<br>";
                echo "<a href='?action=fix_all' class='btn btn-danger' onclick='return confirm(\"Fix all incorrect booking counts?\")'>Fix All Incorrect Counts</a>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-success'>";
                echo "<strong>✓ All Good!</strong> All technician booking counts are correct.";
                echo "</div>";
            }
            echo "</div>";
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>🔧 Quick Actions</h3>
        <a href="admin-manage-technician.php" class="btn btn-success">Manage Technicians</a>
        <a href="admin-dashboard.php" class="btn">Dashboard</a>
        <a href="run-booking-limit-fix.php" class="btn">Run Full System Fix</a>
        <a href="?" class="btn">Refresh</a>
        
        <hr style="margin: 30px 0;">
        
        <h3>📖 How It Works</h3>
        <ul>
            <li><strong>Counter:</strong> The value stored in <code>t_current_bookings</code> field</li>
            <li><strong>Actual:</strong> Real count of active bookings from database</li>
            <li><strong>Status:</strong> Green = Match, Red = Mismatch</li>
            <li><strong>Fix:</strong> Recalculates the counter based on actual active bookings</li>
        </ul>
        
        <div class="alert alert-info">
            <strong>💡 Tip:</strong> If you see mismatches, it means the counter wasn't updated properly during booking assignment/completion. 
            Click "Fix Now" to correct individual technicians, or "Fix All" to correct everyone at once.
        </div>
    </div>
</body>
</html>
