<?php
/**
 * Auto Unhold Expired Bookings - Cron Job
 * 
 * This script automatically unholds bookings when their hold period expires.
 * After the hold_end_date passes, the booking is automatically resumed and
 * marked as high priority.
 * 
 * Setup: Run this script every hour via cron job
 * Cron: 0 * * * * php /path/to/admin/cron-auto-unhold-expired.php
 */

// Allow script to run from command line or web
if(php_sapi_name() != 'cli') {
    // If accessed via web, start session
    session_start();
}

// Include database config
require_once(__DIR__ . '/vendor/inc/config.php');

// Log file
$log_file = __DIR__ . '/logs/auto-unhold.log';
$log_dir = dirname($log_file);
if(!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function log_message($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    echo $log_entry; // Also output to console
}

log_message("=== Auto Unhold Expired Bookings - Started ===");

try {
    // Find all bookings that are on hold and past their end date
    $current_time = date('Y-m-d H:i:s');
    
    $query = "SELECT sb.sb_id, sb.sb_hold_reason, sb.sb_hold_end_date,
              sb.sb_technician_id, sb.sb_user_id,
              t.t_name, t.t_phone, t.t_email,
              u.u_fname, u.u_lname, u.u_phone as u_phone, u.u_email as u_email,
              s.s_name
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
    log_message("Found $expired_count expired hold booking(s)");
    
    if($expired_count == 0) {
        log_message("No expired holds to process. Exiting.");
        log_message("=== Auto Unhold Expired Bookings - Completed ===\n");
        exit(0);
    }
    
    $unholded_count = 0;
    $failed_count = 0;
    
    while($booking = $result->fetch_object()) {
        log_message("Processing Booking #" . $booking->sb_id);
        
        try {
            // Update booking - remove hold and mark as high priority
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
                log_message("✓ Booking #" . $booking->sb_id . " unholded successfully");
                $unholded_count++;
                
                // Notify technician if assigned
                if(!empty($booking->sb_technician_id)) {
                    try {
                        $notif_title = "🔥 URGENT - Booking #" . $booking->sb_id . " Auto-Unholded";
                        $notif_message = "Hold period expired for Booking #" . $booking->sb_id . " (" . $booking->s_name . "). This booking is now HIGH PRIORITY and requires IMMEDIATE attention. Please contact customer " . $booking->u_fname . " " . $booking->u_lname . " at " . $booking->u_phone . " as soon as possible.";
                        
                        $insert_notif = "INSERT INTO tms_technician_notifications 
                                        (tn_technician_id, tn_booking_id, tn_type, tn_title, tn_message) 
                                        VALUES (?, ?, 'booking_auto_unholded', ?, ?)";
                        $stmt_notif = $mysqli->prepare($insert_notif);
                        $stmt_notif->bind_param('iiss', $booking->sb_technician_id, $booking->sb_id, $notif_title, $notif_message);
                        $stmt_notif->execute();
                        
                        log_message("  → Technician notification sent to " . $booking->t_name);
                    } catch(Exception $e) {
                        log_message("  ⚠ Failed to send technician notification: " . $e->getMessage());
                    }
                }
                
                // Notify customer
                try {
                    // You can add SMS/Email notification here if needed
                    log_message("  → Customer notification: " . $booking->u_fname . " " . $booking->u_lname);
                } catch(Exception $e) {
                    log_message("  ⚠ Failed to send customer notification: " . $e->getMessage());
                }
                
                // Notify admin
                try {
                    $admin_notif_title = "Booking #" . $booking->sb_id . " Auto-Unholded (Hold Expired)";
                    $admin_notif_message = "Hold period expired for Booking #" . $booking->sb_id . ". Booking has been automatically unholded and marked as HIGH PRIORITY. Customer: " . $booking->u_fname . " " . $booking->u_lname . ", Technician: " . ($booking->t_name ?? 'Not assigned');
                    
                    $insert_admin_notif = "INSERT INTO tms_admin_notifications 
                                           (an_booking_id, an_type, an_title, an_message) 
                                           VALUES (?, 'booking_auto_unholded', ?, ?)";
                    $stmt_admin = $mysqli->prepare($insert_admin_notif);
                    $stmt_admin->bind_param('iss', $booking->sb_id, $admin_notif_title, $admin_notif_message);
                    $stmt_admin->execute();
                    
                    log_message("  → Admin notification sent");
                } catch(Exception $e) {
                    log_message("  ⚠ Failed to send admin notification: " . $e->getMessage());
                }
                
                // Log to system logs
                try {
                    $log_query = "INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                                 VALUES (?, ?, ?, ?, ?)";
                    $log_email = "system_cron";
                    $log_ip = "127.0.0.1";
                    $log_details = "Auto-unholded Booking #" . $booking->sb_id . " (Hold expired)";
                    $user_type = "system";
                    $stmt_log = $mysqli->prepare($log_query);
                    $stmt_log->bind_param('sssss', $log_email, $log_ip, $log_details, $log_details, $user_type);
                    $stmt_log->execute();
                } catch(Exception $e) {
                    // Logging failed, continue anyway
                }
                
            } else {
                log_message("✗ Failed to unhold Booking #" . $booking->sb_id . ": " . $mysqli->error);
                $failed_count++;
            }
            
        } catch(Exception $e) {
            log_message("✗ Error processing Booking #" . $booking->sb_id . ": " . $e->getMessage());
            $failed_count++;
        }
    }
    
    log_message("\n--- Summary ---");
    log_message("Total expired holds found: $expired_count");
    log_message("Successfully unholded: $unholded_count");
    log_message("Failed: $failed_count");
    log_message("=== Auto Unhold Expired Bookings - Completed ===\n");
    
    // Return success code
    exit(0);
    
} catch(Exception $e) {
    log_message("FATAL ERROR: " . $e->getMessage());
    log_message("=== Auto Unhold Expired Bookings - Failed ===\n");
    exit(1);
}
?>
