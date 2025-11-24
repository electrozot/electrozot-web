<?php
/**
 * Unified Notifications API - Simplified Version
 * Returns booking notifications for admin
 */

// Suppress errors to prevent breaking JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

session_start();

// Check authentication
if (!isset($_SESSION['a_id'])) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');

// Clear buffer and set headers
ob_clean();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    $notifications = [];
    $currentTimestamp = time();
    
    // Get last check timestamp from request (to filter new notifications only)
    $lastCheck = isset($_GET['last_check']) ? intval($_GET['last_check']) : ($currentTimestamp - 60);
    
    // Initialize session array to track shown notifications
    if (!isset($_SESSION['shown_notifications'])) {
        $_SESSION['shown_notifications'] = [];
    }
    
    // Clean up old shown notifications (older than 10 minutes to prevent memory buildup)
    // Reduced from 1 hour to 10 minutes for better memory management
    $tenMinutesAgo = $currentTimestamp - 600;
    $_SESSION['shown_notifications'] = array_filter($_SESSION['shown_notifications'], function($timestamp) use ($tenMinutesAgo) {
        return $timestamp > $tenMinutesAgo;
    });

    // Get NEW pending bookings (created in last 30 seconds and not shown yet)
    // Reduced from 2 minutes to 30 seconds to prevent repeated notifications
    $query = "SELECT 
                sb.sb_id,
                sb.sb_status,
                COALESCE(sb.sb_created_at, NOW()) as sb_created_at,
                COALESCE(CONCAT(u.u_fname, ' ', u.u_lname), 'Guest') as customer_name,
                COALESCE(s.s_name, 'Service') as service_name
              FROM tms_service_booking sb
              LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
              LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
              WHERE sb.sb_status = 'Pending'
              AND sb.sb_created_at IS NOT NULL
              AND sb.sb_created_at >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
              ORDER BY sb.sb_id DESC
              LIMIT 10";

    $result = $mysqli->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $notifId = 'booking_' . $row['sb_id'];
            
            // Only add if not already shown
            if (!isset($_SESSION['shown_notifications'][$notifId])) {
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'NEW_BOOKING',
                    'booking_id' => $row['sb_id'],
                    'message' => 'New Booking #' . $row['sb_id'],
                    'details' => $row['customer_name'] . ' - ' . $row['service_name'],
                    'timestamp' => $currentTimestamp
                ];
                
                // Mark as shown
                $_SESSION['shown_notifications'][$notifId] = $currentTimestamp;
            }
        }
    }

    // Get NEW rejected bookings (updated in last 30 seconds and not shown yet)
    // Reduced from 2 minutes to 30 seconds to prevent repeated notifications
    // Note: Checking for both 'Not Completed' and 'Not Done' (different rejection methods)
    $query2 = "SELECT 
                sb.sb_id,
                sb.sb_status,
                COALESCE(sb.sb_updated_at, NOW()) as sb_updated_at,
                COALESCE(s.s_name, 'Service') as service_name,
                COALESCE(t.t_name, 'Technician') as tech_name
               FROM tms_service_booking sb
               LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
               LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
               WHERE sb.sb_status IN ('Rejected', 'Rejected by Technician', 'Not Completed', 'Not Done')
               AND sb.sb_updated_at IS NOT NULL
               AND sb.sb_updated_at >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
               ORDER BY sb.sb_id DESC
               LIMIT 5";

    $result2 = $mysqli->query($query2);
    if ($result2) {
        while ($row = $result2->fetch_assoc()) {
            $notifId = 'rejected_' . $row['sb_id'];
            
            // Only add if not already shown
            if (!isset($_SESSION['shown_notifications'][$notifId])) {
                // Handle different rejection statuses
                if ($row['sb_status'] == 'Not Completed' || $row['sb_status'] == 'Not Done') {
                    $message = 'Booking #' . $row['sb_id'] . ' - Technician Cannot Complete';
                } else {
                    $message = 'Booking #' . $row['sb_id'] . ' Rejected';
                }
                
                $details = $row['service_name'];
                if (isset($row['tech_name']) && $row['tech_name'] != 'Technician' && !empty($row['tech_name'])) {
                    $details .= ' | Technician: ' . $row['tech_name'];
                }
                
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'BOOKING_REJECTED',
                    'booking_id' => $row['sb_id'],
                    'message' => $message,
                    'details' => $details,
                    'timestamp' => $currentTimestamp
                ];
                
                // Mark as shown
                $_SESSION['shown_notifications'][$notifId] = $currentTimestamp;
            }
        }
    }

    // Get NEW completed bookings (updated in last 30 seconds and not shown yet)
    $query3 = "SELECT 
                sb.sb_id,
                sb.sb_status,
                COALESCE(sb.sb_updated_at, NOW()) as sb_updated_at,
                COALESCE(s.s_name, 'Service') as service_name,
                COALESCE(t.t_name, 'Technician') as tech_name,
                COALESCE(CONCAT(u.u_fname, ' ', u.u_lname), 'Customer') as customer_name
               FROM tms_service_booking sb
               LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
               LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
               LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
               WHERE sb.sb_status = 'Completed'
               AND sb.sb_updated_at IS NOT NULL
               AND sb.sb_updated_at >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
               ORDER BY sb.sb_id DESC
               LIMIT 5";

    $result3 = $mysqli->query($query3);
    if ($result3) {
        while ($row = $result3->fetch_assoc()) {
            $notifId = 'completed_' . $row['sb_id'];
            
            // Only add if not already shown
            if (!isset($_SESSION['shown_notifications'][$notifId])) {
                $message = 'Booking #' . $row['sb_id'] . ' - Service Completed Successfully';
                
                $details = $row['service_name'];
                if (isset($row['tech_name']) && !empty($row['tech_name'])) {
                    $details .= ' | Technician: ' . $row['tech_name'];
                }
                if (isset($row['customer_name']) && !empty($row['customer_name'])) {
                    $details .= ' | Customer: ' . $row['customer_name'];
                }
                
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'BOOKING_COMPLETED',
                    'booking_id' => $row['sb_id'],
                    'message' => $message,
                    'details' => $details,
                    'timestamp' => $currentTimestamp
                ];
                
                // Mark as shown
                $_SESSION['shown_notifications'][$notifId] = $currentTimestamp;
            }
        }
    }

    // Get unread count (includes both 'Not Completed' and 'Not Done')
    $count_query = "SELECT COUNT(*) as count 
                    FROM tms_service_booking 
                    WHERE sb_status IN ('Pending', 'Rejected', 'Rejected by Technician', 'Not Completed', 'Not Done')";
    $count_result = $mysqli->query($count_query);
    $unread_count = 0;
    if ($count_result) {
        $count_row = $count_result->fetch_object();
        $unread_count = $count_row ? $count_row->count : 0;
    }

    // Return success response
    // Only return notifications array if there are new ones
    echo json_encode([
        'success' => true,
        'notifications' => $notifications, // Will be empty array if no new notifications
        'unread_count' => $unread_count,
        'current_timestamp' => $currentTimestamp,
        'new_count' => count($notifications),
        'debug' => [
            'last_check' => $lastCheck,
            'session_tracked' => count($_SESSION['shown_notifications'])
        ]
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Error',
        'notifications' => [],
        'unread_count' => 0,
        'current_timestamp' => time(),
        'new_count' => 0
    ]);
}

ob_end_flush();
?>
