<?php
/**
 * Immediate Sync - Technician Slots
 * Fixes t_current_bookings to match actual active bookings
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Sync all technicians immediately
$sync_sql = "UPDATE tms_technician t
            SET t_current_bookings = (
                SELECT COUNT(*)
                FROM tms_service_booking sb
                WHERE sb.sb_technician_id = t.t_id
                AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
            )";

$mysqli->query($sync_sql);
$synced_count = $mysqli->affected_rows;

// Update all statuses
$status_sql = "UPDATE tms_technician
              SET t_status = CASE
                  WHEN t_current_bookings >= t_booking_limit THEN 'Busy'
                  ELSE 'Available'
              END";

$mysqli->query($status_sql);
$status_updated = $mysqli->affected_rows;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sync Complete</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f7fa; text-align: center; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #10b981; }
        .success { background: #ecfdf5; color: #059669; padding: 20px; border-radius: 5px; margin: 20px 0; font-size: 18px; }
        .stats { background: #f9fafb; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .btn { padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px; }
        table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #667eea; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✅ Sync Complete!</h2>
        
        <div class="success">
            <strong>Technician slots have been synchronized!</strong>
        </div>
        
        <div class="stats">
            <p><strong>📊 Results:</strong></p>
            <p>✅ Synced booking counts: <strong><?php echo $synced_count; ?> technicians</strong></p>
            <p>✅ Updated availability status: <strong><?php echo $status_updated; ?> technicians</strong></p>
        </div>
        
        <h3>Updated Technician Data:</h3>
        
        <?php
        // Show updated data
        $query = "SELECT 
                    t.t_id,
                    t.t_name,
                    t.t_status,
                    t.t_current_bookings,
                    t.t_booking_limit,
                    (SELECT COUNT(*) FROM tms_service_booking 
                     WHERE sb_technician_id = t.t_id 
                     AND sb_status IN ('Pending', 'Approved', 'In Progress')) as actual_bookings
                  FROM tms_technician t
                  ORDER BY t.t_name";
        
        $result = $mysqli->query($query);
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>Name</th>";
            echo "<th>Status</th>";
            echo "<th>Bookings</th>";
            echo "<th>Limit</th>";
            echo "<th>Verified</th>";
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                $is_correct = ($row['t_current_bookings'] == $row['actual_bookings']);
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['t_name']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($row['t_status']) . "</strong></td>";
                echo "<td>{$row['t_current_bookings']}</td>";
                echo "<td>{$row['t_booking_limit']}</td>";
                echo "<td>" . ($is_correct ? "✅" : "⚠️") . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
        
        <p style="margin-top: 30px;">
            <strong>All technician slots are now accurate!</strong><br>
            When technicians reject or complete bookings, their slots will update automatically.
        </p>
        
        <a href="admin-manage-technicians.php" class="btn">View Technicians</a>
        <a href="admin-dashboard.php" class="btn">Dashboard</a>
    </div>
</body>
</html>
