<?php
/**
 * Technician Notification Check Endpoint
 * Returns new booking notifications for the logged-in technician
 */

// Suppress all output
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

session_start();

// Check if technician is logged in
if (!isset($_SESSION['t_id'])) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in',
        'notification_count' => 0,
        'has_notifications' => false,
        'notifications' => []
    ]);
    exit;
}

include('../admin/vendor/inc/config.php');

// Clear any output
ob_end_clean();
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$t_id = $_SESSION['t_id'];

try {
    // Ensure required tables exist
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL DEFAULT NULL");
    
    $create_tracking = "CREATE TABLE IF NOT EXISTS tms_technician_notification_tracking (
        tnt_id INT AUTO_INCREMENT PRIMARY KEY,
        tnt_technician_id INT NOT NULL,
        tnt_booking_id INT NOT NULL,
        tnt_action_type VARCHAR(50) NOT NULL,
        tnt_booking_status VARCHAR(50) NOT NULL,
        tnt_shown_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_tech_booking_action (tnt_technician_id, tnt_booking_id, tnt_action_type, tnt_booking_status),
        INDEX idx_technician (tnt_technician_id),
        INDEX idx_booking (tnt_booking_id),
        INDEX idx_shown_at (tnt_shown_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $mysqli->query($create_tracking);
    
    // Clean up old records
    $mysqli->query("DELETE FROM tms_technician_notification_tracking WHERE tnt_shown_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    
    // Get bookings updated in last 10 minutes
    $query = "SELECT 
                sb.sb_id, sb.sb_status, sb.sb_updated_at, sb.sb_created_at, sb.sb_assigned_at,
                sb.sb_service_deadline_date, sb.sb_service_deadline_time,
                sb.sb_is_on_hold, sb.sb_hold_reason, sb.sb_is_high_priority,
                COALESCE(u.u_fname, 'Guest') as u_fname,
                COALESCE(u.u_lname, '') as u_lname,
                COALESCE(u.u_phone, 'N/A') as u_phone,
                COALESCE(u.u_addr, 'N/A') as u_addr,
                COALESCE(s.s_name, 'Unknown Service') as s_name
              FROM tms_service_booking sb
              LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
              LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
              WHERE sb.sb_technician_id = ?
              AND sb.sb_updated_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)
              AND sb.sb_status NOT IN ('Cancelled', 'Completed')
              ORDER BY sb.sb_updated_at DESC
              LIMIT 20";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    $notifications_to_mark = [];
    
    while ($booking = $result->fetch_assoc()) {
        $action = '';
        $message = '';
        
        // Determine action type
        if ($booking['sb_is_on_hold'] == 1) {
            $action = 'hold';
            $message = '⏸️ Booking placed on hold';
            if ($booking['sb_hold_reason']) {
                $message .= ' - Reason: ' . $booking['sb_hold_reason'];
            }
        } else {
            // Check if booking was previously on hold (now resumed)
            $check_was_hold = $mysqli->prepare("SELECT COUNT(*) as count FROM tms_technician_notification_tracking WHERE tnt_booking_id = ? AND tnt_action_type = 'hold'");
            $check_was_hold->bind_param('i', $booking['sb_id']);
            $check_was_hold->execute();
            $was_hold_result = $check_was_hold->get_result();
            $was_hold_count = $was_hold_result->fetch_assoc()['count'];
            
            if ($was_hold_count > 0 && $booking['sb_is_on_hold'] == 0) {
                // Booking was on hold and now resumed
                $action = 'resumed';
                $message = '▶️ Booking resumed - Work can continue';
            }
        }
        
        if (!$action && $booking['sb_assigned_at'] && strtotime($booking['sb_assigned_at']) > strtotime('-5 minutes')) {
            $action = 'assigned';
            $message = '✨ New booking assigned to you';
            
            // Check for reassignment
            $check_prev = $mysqli->prepare("SELECT COUNT(*) as count FROM tms_technician_notification_tracking WHERE tnt_booking_id = ? AND tnt_action_type = 'assigned'");
            $check_prev->bind_param('i', $booking['sb_id']);
            $check_prev->execute();
            $prev_result = $check_prev->get_result();
            $prev_count = $prev_result->fetch_assoc()['count'];
            
            if ($prev_count > 0) {
                $action = 'reassigned';
                $message = '🔄 Booking reassigned to you';
            }
        } else if ($booking['sb_is_high_priority'] == 1) {
            $action = 'high_priority';
            $message = '🔥 High priority booking assigned';
        } else {
            switch ($booking['sb_status']) {
                case 'Pending':
                    $action = 'pending';
                    $message = '⏳ Booking awaiting your action';
                    break;
                case 'Approved':
                    $action = 'approved';
                    $message = '✅ Booking approved by admin';
                    break;
                case 'In Progress':
                    $action = 'in_progress';
                    $message = '🔧 Booking marked as in progress';
                    break;
                case 'Rejected':
                    $action = 'rejected';
                    $message = '❌ Booking was rejected';
                    break;
                default:
                    $action = 'updated';
                    $message = '📝 Booking updated by admin';
            }
        }
        
        // Check if already shown
        $check_shown = $mysqli->prepare("SELECT tnt_id FROM tms_technician_notification_tracking WHERE tnt_technician_id = ? AND tnt_booking_id = ? AND tnt_action_type = ? AND tnt_booking_status = ?");
        $check_shown->bind_param('iiss', $t_id, $booking['sb_id'], $action, $booking['sb_status']);
        $check_shown->execute();
        $shown_result = $check_shown->get_result();
        
        if ($shown_result->num_rows == 0) {
            $notifications[] = [
                'id' => $booking['sb_id'],
                'customer' => trim($booking['u_fname'] . ' ' . $booking['u_lname']),
                'phone' => $booking['u_phone'],
                'address' => $booking['u_addr'],
                'service' => $booking['s_name'],
                'status' => $booking['sb_status'],
                'deadline_date' => $booking['sb_service_deadline_date'],
                'deadline_time' => $booking['sb_service_deadline_time'],
                'message' => $message,
                'action' => $action,
                'updated_at' => $booking['sb_updated_at'],
                'assigned_at' => $booking['sb_assigned_at'],
                'is_on_hold' => $booking['sb_is_on_hold'],
                'is_high_priority' => $booking['sb_is_high_priority']
            ];
            
            $notifications_to_mark[] = [
                'booking_id' => $booking['sb_id'],
                'action' => $action,
                'status' => $booking['sb_status']
            ];
        }
    }
    
    // Mark as shown BEFORE returning
    if (!empty($notifications_to_mark)) {
        foreach ($notifications_to_mark as $notif) {
            $mark_stmt = $mysqli->prepare("INSERT IGNORE INTO tms_technician_notification_tracking (tnt_technician_id, tnt_booking_id, tnt_action_type, tnt_booking_status) VALUES (?, ?, ?, ?)");
            if ($mark_stmt) {
                $mark_stmt->bind_param('iiss', $t_id, $notif['booking_id'], $notif['action'], $notif['status']);
                $mark_stmt->execute();
                $mark_stmt->close();
            }
        }
    }
    
    // Also check technician_notifications table for hold request responses
    $notif_query = "SELECT 
                      tn_id, tn_booking_id, tn_type, tn_title, tn_message, tn_created_at
                    FROM tms_technician_notifications
                    WHERE tn_technician_id = ?
                    AND tn_is_read = 0
                    AND tn_created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)
                    ORDER BY tn_created_at DESC";
    
    $notif_stmt = $mysqli->prepare($notif_query);
    $notif_stmt->bind_param('i', $t_id);
    $notif_stmt->execute();
    $notif_result = $notif_stmt->get_result();
    
    while ($notif = $notif_result->fetch_assoc()) {
        // Get booking details
        $booking_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name
                         FROM tms_service_booking sb
                         LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                         LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                         WHERE sb.sb_id = ?";
        $booking_stmt = $mysqli->prepare($booking_query);
        $booking_stmt->bind_param('i', $notif['tn_booking_id']);
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        
        if ($booking_result->num_rows > 0) {
            $booking = $booking_result->fetch_assoc();
            
            $action = $notif['tn_type'];
            $message = $notif['tn_message'];
            
            // Add to notifications array
            $notifications[] = [
                'id' => $notif['tn_booking_id'],
                'customer' => trim($booking['u_fname'] . ' ' . $booking['u_lname']),
                'phone' => $booking['u_phone'],
                'address' => $booking['u_addr'] ?? 'N/A',
                'service' => $booking['s_name'],
                'status' => $booking['sb_status'],
                'deadline_date' => $booking['sb_service_deadline_date'] ?? null,
                'deadline_time' => $booking['sb_service_deadline_time'] ?? null,
                'message' => $message,
                'action' => $action,
                'updated_at' => $notif['tn_created_at'],
                'assigned_at' => null,
                'is_on_hold' => $booking['sb_is_on_hold'] ?? 0,
                'is_high_priority' => $booking['sb_is_high_priority'] ?? 0
            ];
            
            // Mark as read
            $mark_read = "UPDATE tms_technician_notifications SET tn_is_read = 1 WHERE tn_id = ?";
            $mark_stmt = $mysqli->prepare($mark_read);
            $mark_stmt->bind_param('i', $notif['tn_id']);
            $mark_stmt->execute();
        }
    }
    
    // Get total count
    $count_query = "SELECT COUNT(DISTINCT sb.sb_id) as count FROM tms_service_booking sb LEFT JOIN tms_cancelled_bookings cb ON sb.sb_id = cb.cb_booking_id AND cb.cb_technician_id = ? WHERE sb.sb_technician_id = ? AND sb.sb_status NOT IN ('Cancelled', 'Completed') AND cb.cb_id IS NULL";
    $count_stmt = $mysqli->prepare($count_query);
    $count_stmt->bind_param('ii', $t_id, $t_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_data = $count_result->fetch_assoc();
    $total_count = $count_data['count'];
    
    $response = [
        'success' => true,
        'notification_count' => $total_count,
        'has_notifications' => count($notifications) > 0,
        'notifications' => $notifications,
        'new_count' => count($notifications),
        'current_time' => date('Y-m-d H:i:s'),
        'technician_id' => $t_id
    ];
    
    ob_clean();
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'notification_count' => 0,
        'has_notifications' => false,
        'notifications' => []
    ]);
}

ob_end_flush();
exit;
?>
