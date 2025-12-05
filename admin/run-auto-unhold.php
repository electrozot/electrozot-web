<?php
/**
 * Manual Trigger for Auto Unhold Expired Bookings
 * 
 * This page allows admins to manually trigger the auto-unhold process
 * or can be called via web-based cron services
 * 
 * Access: http://localhost/electrozot/admin/run-auto-unhold.php
 * Or with secret key: ?key=YOUR_SECRET_KEY
 */

session_start();
include('vendor/inc/config.php');

// Security: Check if admin is logged in OR valid secret key provided
$secret_key = "electrozot_auto_unhold_2024"; // Change this to something secure
$is_authorized = false;

if(isset($_SESSION['a_id'])) {
    // Admin is logged in
    $is_authorized = true;
    $triggered_by = "Admin (ID: " . $_SESSION['a_id'] . ")";
} elseif(isset($_GET['key']) && $_GET['key'] === $secret_key) {
    // Valid secret key provided (for cron services)
    $is_authorized = true;
    $triggered_by = "Cron Service";
} else {
    die("Unauthorized access. Please login as admin or provide valid key.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Unhold Expired Bookings</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .container {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #00ff00;
        }
        h1 {
            color: #00ff00;
            text-align: center;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 10px;
        }
        .log-entry {
            padding: 5px;
            margin: 2px 0;
            border-left: 3px solid #00ff00;
            padding-left: 10px;
        }
        .success {
            color: #00ff00;
        }
        .error {
            color: #ff0000;
        }
        .warning {
            color: #ffaa00;
        }
        .info {
            color: #00aaff;
        }
        .summary {
            background: #1a1a1a;
            padding: 15px;
            margin-top: 20px;
            border: 2px solid #00ff00;
            border-radius: 5px;
        }
        .timestamp {
            color: #888;
            font-size: 12px;
        }
        .btn {
            background: #00ff00;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #00cc00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⏰ Auto Unhold Expired Bookings</h1>
        <div class="log-entry info">
            <strong>Triggered by:</strong> <?php echo htmlspecialchars($triggered_by); ?><br>
            <strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
        </div>
        
        <hr style="border-color: #00ff00;">
        
        <?php
        // Execute the auto-unhold process
        $current_time = date('Y-m-d H:i:s');
        
        echo "<div class='log-entry info'>[INFO] Searching for expired hold bookings...</div>";
        
        // Find expired holds
        $query = "SELECT sb.sb_id, sb.sb_hold_reason, sb.sb_hold_end_date,
                  sb.sb_technician_id, sb.sb_user_id,
                  t.t_name, u.u_fname, u.u_lname, s.s_name
                  FROM tms_service_booking sb
                  LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                  WHERE sb.sb_is_on_hold = 1 
                  AND sb.sb_hold_end_date IS NOT NULL
                  AND sb.sb_hold_end_date <= ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $expired_count = $result->num_rows;
        
        if($expired_count == 0) {
            echo "<div class='log-entry success'>[SUCCESS] No expired holds found. All bookings are up to date!</div>";
        } else {
            echo "<div class='log-entry warning'>[FOUND] $expired_count expired hold booking(s) to process</div>";
            
            $unholded_count = 0;
            $failed_count = 0;
            
            while($booking = $result->fetch_object()) {
                echo "<div class='log-entry info'>[PROCESSING] Booking #" . $booking->sb_id . " - " . htmlspecialchars($booking->s_name) . "</div>";
                
                try {
                    // Update booking
                    $update_query = "UPDATE tms_service_booking 
                                   SET sb_is_on_hold = 0,
                                       sb_hold_reason = NULL,
                                       sb_hold_start_date = NULL,
                                       sb_hold_end_date = NULL,
                                       sb_was_on_hold = 1,
                                       sb_is_high_priority = 1,
                                       sb_priority_reason = 'Auto-unholded after hold period expired - requires immediate attention',
                                       sb_status = 'In Progress'
                                   WHERE sb_id = ?";
                    
                    $stmt_update = $mysqli->prepare($update_query);
                    $stmt_update->bind_param('i', $booking->sb_id);
                    
                    if($stmt_update->execute()) {
                        echo "<div class='log-entry success'>  ✓ Booking #" . $booking->sb_id . " unholded successfully</div>";
                        $unholded_count++;
                        
                        // Notify technician
                        if(!empty($booking->sb_technician_id)) {
                            try {
                                $notif_title = "🔥 URGENT - Booking #" . $booking->sb_id . " Auto-Unholded";
                                $notif_message = "Hold period expired for Booking #" . $booking->sb_id . ". This booking is now HIGH PRIORITY and requires IMMEDIATE attention.";
                                
                                $insert_notif = "INSERT INTO tms_technician_notifications 
                                                (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                                                VALUES (?, ?, 'booking_auto_unholded', ?, ?)";
                                $stmt_notif = $mysqli->prepare($insert_notif);
                                $stmt_notif->bind_param('iiss', $booking->sb_technician_id, $booking->sb_id, $notif_title, $notif_message);
                                $stmt_notif->execute();
                                
                                echo "<div class='log-entry success'>  ✓ Technician notification sent to " . htmlspecialchars($booking->t_name) . "</div>";
                            } catch(Exception $e) {
                                echo "<div class='log-entry warning'>  ⚠ Technician notification failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                            }
                        }
                        
                        // Notify admin
                        try {
                            $admin_notif_title = "Booking #" . $booking->sb_id . " Auto-Unholded (Hold Expired)";
                            $admin_notif_message = "Hold period expired for Booking #" . $booking->sb_id . ". Booking automatically unholded and marked as HIGH PRIORITY.";
                            
                            $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                                                   (an_booking_id, an_type, an_title, an_message) 
                                                   VALUES (?, 'booking_auto_unholded', ?, ?)";
                            $stmt_admin = $mysqli->prepare($insert_admin_notif);
                            $stmt_admin->bind_param('iss', $booking->sb_id, $admin_notif_title, $admin_notif_message);
                            $stmt_admin->execute();
                            
                            echo "<div class='log-entry success'>  ✓ Admin notification sent</div>";
                        } catch(Exception $e) {
                            echo "<div class='log-entry warning'>  ⚠ Admin notification failed</div>";
                        }
                        
                    } else {
                        echo "<div class='log-entry error'>  ✗ Failed to unhold Booking #" . $booking->sb_id . "</div>";
                        $failed_count++;
                    }
                    
                } catch(Exception $e) {
                    echo "<div class='log-entry error'>  ✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                    $failed_count++;
                }
            }
            
            // Summary
            echo "<div class='summary'>";
            echo "<h3 style='color: #00ff00; margin-top: 0;'>📊 Summary</h3>";
            echo "<div class='log-entry info'>Total expired holds found: <strong>$expired_count</strong></div>";
            echo "<div class='log-entry success'>Successfully unholded: <strong>$unholded_count</strong></div>";
            if($failed_count > 0) {
                echo "<div class='log-entry error'>Failed: <strong>$failed_count</strong></div>";
            }
            echo "<div class='log-entry info'>Completion time: <strong>" . date('Y-m-d H:i:s') . "</strong></div>";
            echo "</div>";
        }
        ?>
        
        <hr style="border-color: #00ff00;">
        
        <div style="text-align: center;">
            <button class="btn" onclick="location.reload()">🔄 Run Again</button>
            <button class="btn" onclick="window.location.href='admin-dashboard.php'">← Back to Dashboard</button>
        </div>
        
        <div class="log-entry info" style="margin-top: 20px; font-size: 12px;">
            <strong>Note:</strong> This process runs automatically every hour via cron job.<br>
            <strong>Cron URL:</strong> <?php echo $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']); ?>/run-auto-unhold.php?key=<?php echo $secret_key; ?>
        </div>
    </div>
</body>
</html>
