<?php
/**
 * Link Guest Bookings to User Account
 * 
 * This script automatically links guest bookings to user accounts
 * when a user logs in with the same phone number used for guest booking.
 * 
 * Called automatically on user login.
 */

function linkGuestBookingsToUser($mysqli, $user_id, $phone_number) {
    // Find all guest bookings with this phone number that aren't linked to this user
    $find_query = "SELECT sb.sb_id, sb.sb_user_id, u.registration_type
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   WHERE sb.sb_phone = ?
                   AND (sb.sb_user_id != ? OR u.registration_type = 'guest')";
    
    $stmt = $mysqli->prepare($find_query);
    $stmt->bind_param('si', $phone_number, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $linked_count = 0;
    $booking_ids = [];
    
    while($row = $result->fetch_assoc()) {
        $booking_ids[] = $row['sb_id'];
    }
    
    $stmt->close();
    
    // Link these bookings to the logged-in user
    if(count($booking_ids) > 0) {
        $placeholders = str_repeat('?,', count($booking_ids) - 1) . '?';
        $update_query = "UPDATE tms_service_booking 
                        SET sb_user_id = ? 
                        WHERE sb_id IN ($placeholders)";
        
        $stmt = $mysqli->prepare($update_query);
        
        // Bind parameters
        $types = 'i' . str_repeat('i', count($booking_ids));
        $params = array_merge([$user_id], $booking_ids);
        $stmt->bind_param($types, ...$params);
        
        if($stmt->execute()) {
            $linked_count = $stmt->affected_rows;
        }
        
        $stmt->close();
    }
    
    return $linked_count;
}

/**
 * Link bookings by phone number match
 * More aggressive - links any booking with matching phone
 */
function linkBookingsByPhone($mysqli, $user_id, $phone_number) {
    $update_query = "UPDATE tms_service_booking 
                    SET sb_user_id = ? 
                    WHERE sb_phone = ? 
                    AND (sb_user_id IS NULL OR sb_user_id = 0 OR sb_user_id != ?)";
    
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param('isi', $user_id, $phone_number, $user_id);
    
    if($stmt->execute()) {
        $linked_count = $stmt->affected_rows;
        $stmt->close();
        return $linked_count;
    }
    
    $stmt->close();
    return 0;
}
?>
