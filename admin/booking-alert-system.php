<?php
/**
 * Fresh Booking Alert System
 * Triggers popup + sound when bookings needing action reach 12 or 25
 * Counts: Pending, Rejected, Not Done, and Unassigned bookings
 * 
 * IMPORTANT: This file should be included BEFORE any HTML output or nav.php
 */

// Handle alert dismissal using AJAX instead of POST redirect
if(isset($_POST['dismiss_booking_alert'])) {
    $dismissed_level = intval($_POST['alert_level']);
    $_SESSION['booking_alert_dismissed_' . $dismissed_level] = time();
    
    // Return JSON response instead of redirect
    if(!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    }
}

// Count all bookings that need admin action
$alert_query = "SELECT COUNT(*) as total_count 
                FROM tms_service_booking 
                WHERE (
                    sb_status = 'Pending' 
                    OR sb_status = 'Rejected' 
                    OR sb_status = 'Not Done'
                    OR (sb_technician_id IS NULL AND sb_status NOT IN ('Completed', 'Cancelled'))
                )
                AND sb_status NOT IN ('Completed', 'Cancelled')";

$alert_result = $mysqli->query($alert_query);
$bookings_needing_action = 0;

if($alert_result) {
    $row = $alert_result->fetch_object();
    $bookings_needing_action = $row->total_count;
}

// Determine alert level
$alert_level = 0; // 0 = no alert, 12 = warning, 25 = critical

if($bookings_needing_action >= 25) {
    $alert_level = 25;
} elseif($bookings_needing_action >= 12) {
    $alert_level = 12;
}

// Check if we should show alert
$show_alert = false;
$alert_session_key = 'booking_alert_dismissed_' . $alert_level;

if($alert_level > 0 && !isset($_SESSION[$alert_session_key])) {
    $show_alert = true;
}

// Auto-reset dismissed flags when count drops to 5 or below
if($bookings_needing_action <= 5) {
    unset($_SESSION['booking_alert_dismissed_12']);
    unset($_SESSION['booking_alert_dismissed_25']);
}

// Get breakdown for display
$breakdown_query = "SELECT 
                    SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN sb_status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN sb_status = 'Not Done' THEN 1 ELSE 0 END) as not_done,
                    SUM(CASE WHEN sb_technician_id IS NULL AND sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Not Done', 'Pending') THEN 1 ELSE 0 END) as unassigned
                   FROM tms_service_booking 
                   WHERE (
                       sb_status = 'Pending' 
                       OR sb_status = 'Rejected' 
                       OR sb_status = 'Not Done'
                       OR (sb_technician_id IS NULL AND sb_status NOT IN ('Completed', 'Cancelled'))
                   )
                   AND sb_status NOT IN ('Completed', 'Cancelled')";

$breakdown_result = $mysqli->query($breakdown_query);
$breakdown = $breakdown_result ? $breakdown_result->fetch_object() : null;
?>
