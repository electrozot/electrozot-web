<?php
/**
 * Quick Fix for Abhi's Slots
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Find Abhi's technician ID
$find_abhi = "SELECT t_id, t_name, t_current_bookings, t_booking_limit, t_status 
              FROM tms_technician 
              WHERE t_name LIKE '%Abhi%' OR t_name LIKE '%abhi%'
              LIMIT 1";

$result = $mysqli->query($find_abhi);
$abhi = $result->fetch_assoc();

if (!$abhi) {
    die("Abhi not found");
}

$abhi_id = $abhi['t_id'];

// Get Abhi's actual active bookings
$count_query = "SELECT 
                  COUNT(*) as active_count,
                  GROUP_CONCAT(CONCAT('#', sb_id, ' - ', sb_status) SEPARATOR ', ') as booking_list
                FROM tms_service_booking 
                WHERE sb_technician_id = ? 
                AND sb_status IN ('Pending', 'Approved', 'In Progress')";

$stmt = $mysqli->prepare($count_query);
$stmt->bind_param('i', $abhi_id);
$stmt->execute();
$count_result = $stmt->get_result();
$count_data = $count_result->fetch_assoc();

$actual_count = $count_data['active_count'];
$booking_list = $count_data['booking_list'];

// Update Abhi's data
$update_sql = "UPDATE tms_technician 
              SET t_current_bookings = ?,
                  t_status = CASE 
                      WHEN ? >= t_booking_limit THEN 'Busy'
                      ELSE 'Available'
                  END
              WHERE t_id = ?";

$update_stmt = $mysqli->prepare($update_sql);
$update_stmt->bind_param('iii', $actual_count, $actual_count, $abhi_id);
$update_stmt->execute();

// Get updated data
$after_query = "SELECT t_name, t_current_bookings, t_booking_limit, t_status FROM tms_technician WHERE t_id = ?";
$after_stmt = $mysqli->prepare($after_query);
$after_stmt->bind_param('i', $abhi_id);
$after_stmt->execute();
$after_result = $after_stmt->get_result();
$after_data = $after_result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fixed Abhi's Slots</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f7fa; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; }
        h2 { color: #10b981; }
        .box { background: #f9fafb; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }
        .success { border-left-color: #10b981; background: #ecfdf5; }
        .error { border-left-color: #ef4444; background: #fef2f2; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #667eea; color: white; }
        .btn { padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✅ Fixed <?php echo htmlspecialchars($abhi['t_name']); ?>'s Slots!</h2>
        
        <div class="box">
            <h3>📊 Before Fix:</h3>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><?php echo htmlspecialchars($abhi['t_name']); ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><strong><?php echo $abhi['t_status']; ?></strong></td>
                </tr>
                <tr>
                    <td>Recorded Bookings</td>
                    <td><strong style="color: #ef4444;"><?php echo $abhi['t_current_bookings']; ?></strong></td>
                </tr>
                <tr>
                    <td>Actual Active Bookings</td>
                    <td><strong style="color: #10b981;"><?php echo $actual_count; ?></strong></td>
                </tr>
                <tr>
                    <td>Booking Limit</td>
                    <td><?php echo $abhi['t_booking_limit']; ?></td>
                </tr>
            </table>
            
            <?php if ($booking_list): ?>
                <p><strong>Active Bookings:</strong> <?php echo $booking_list; ?></p>
            <?php else: ?>
                <p><strong>Active Bookings:</strong> None</p>
            <?php endif; ?>
        </div>
        
        <div class="box success">
            <h3>✅ After Fix:</h3>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><?php echo htmlspecialchars($after_data['t_name']); ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><strong style="color: #10b981;"><?php echo $after_data['t_status']; ?></strong></td>
                </tr>
                <tr>
                    <td>Current Bookings</td>
                    <td><strong style="color: #10b981;"><?php echo $after_data['t_current_bookings']; ?></strong></td>
                </tr>
                <tr>
                    <td>Booking Limit</td>
                    <td><?php echo $after_data['t_booking_limit']; ?></td>
                </tr>
                <tr>
                    <td>Available Slots</td>
                    <td><strong style="color: #10b981;"><?php echo ($after_data['t_booking_limit'] - $after_data['t_current_bookings']); ?></strong></td>
                </tr>
            </table>
        </div>
        
        <div class="box">
            <h3>🔧 What Was Fixed:</h3>
            <ul>
                <li>✅ Updated <code>t_current_bookings</code> from <strong><?php echo $abhi['t_current_bookings']; ?></strong> to <strong><?php echo $actual_count; ?></strong></li>
                <li>✅ Updated <code>t_status</code> from <strong><?php echo $abhi['t_status']; ?></strong> to <strong><?php echo $after_data['t_status']; ?></strong></li>
                <li>✅ Capacity now shows: <strong><?php echo $after_data['t_current_bookings']; ?>/<?php echo $after_data['t_booking_limit']; ?></strong></li>
            </ul>
        </div>
        
        <div class="box">
            <h3>🎯 Next Steps:</h3>
            <ol>
                <li>Go back to the technician management page</li>
                <li>Refresh the page</li>
                <li>Abhi should now show correct capacity</li>
                <li>When Abhi rejects/completes bookings, slots will update automatically</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="admin-manage-technicians.php" class="btn">📋 View Technicians</a>
            <a href="sync-technician-slots-now.php" class="btn">🔄 Sync All Technicians</a>
            <a href="admin-dashboard.php" class="btn">🏠 Dashboard</a>
        </div>
    </div>
</body>
</html>
