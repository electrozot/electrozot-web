<?php
/**
 * Get All Bookings Status API
 * Checks if any booking status has changed for auto-refresh
 */
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if user is logged in
if(!isset($_SESSION['u_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

try {
    include('vendor/inc/config.php');
    
    if(!isset($mysqli) || $mysqli->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    $user_id = $_SESSION['u_id'];

    // Get current booking statuses with more details
    $query = "SELECT sb.sb_id, sb.sb_status, sb.sb_booking_date, sb.sb_updated_at,
                     s.s_name, t.t_name as technician_name
              FROM tms_service_booking sb
              LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
              LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
              WHERE sb.sb_user_id = ? 
              ORDER BY sb.sb_created_at DESC";

    $stmt = $mysqli->prepare($query);
    if(!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookings = [];
    $active_count = 0;
    $completed_count = 0;

    while($row = $result->fetch_assoc()) {
        $bookings[] = [
            'id' => $row['sb_id'],
            'status' => $row['sb_status'],
            'date' => $row['sb_booking_date'],
            'service' => $row['s_name'],
            'technician' => $row['technician_name'],
            'updated_at' => $row['sb_updated_at']
        ];
        
        // Count active vs completed
        if($row['sb_status'] == 'Completed' || $row['sb_status'] == 'Cancelled') {
            $completed_count++;
        } else {
            $active_count++;
        }
    }

    // Store current state in session to compare
    $current_state = json_encode($bookings);
    $has_changes = false;
    $changed_bookings = [];

    if(isset($_SESSION['last_booking_state'])) {
        $old_state = json_decode($_SESSION['last_booking_state'], true);
        $new_state = $bookings;
        
        // Check for changes
        if(is_array($old_state) && is_array($new_state)) {
            foreach($new_state as $new_booking) {
                foreach($old_state as $old_booking) {
                    if($new_booking['id'] == $old_booking['id']) {
                        if($new_booking['status'] != $old_booking['status']) {
                            $has_changes = true;
                            $changed_bookings[] = [
                                'id' => $new_booking['id'],
                                'old_status' => $old_booking['status'],
                                'new_status' => $new_booking['status']
                            ];
                        }
                        break;
                    }
                }
            }
        }
    }

    $_SESSION['last_booking_state'] = $current_state;

    echo json_encode([
        'success' => true,
        'has_changes' => $has_changes,
        'bookings' => $bookings,
        'changed_bookings' => $changed_bookings,
        'active_count' => $active_count,
        'completed_count' => $completed_count,
        'timestamp' => time()
    ]);

    $stmt->close();
    $mysqli->close();
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch booking status',
        'message' => $e->getMessage()
    ]);
}
?>
