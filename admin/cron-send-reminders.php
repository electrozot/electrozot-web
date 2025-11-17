<?php
/**
 * CRON JOB: Send booking reminders
 * Run every 15 minutes: */15 * * * * php /path/to/admin/cron-send-reminders.php
 */

require_once('vendor/inc/config.php');
require_once('BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

// Get pending reminders
$reminders = $bookingSystem->getPendingReminders();

$sent_count = 0;

foreach ($reminders as $reminder) {
    $booking_id = $reminder['br_booking_id'];
    $reminder_type = $reminder['br_reminder_type'];
    
    // Get user and technician details
    $stmt = $conn->prepare("
        SELECT u.u_email, u.u_phone, u.u_fname,
               t.t_email as tech_email, t.t_phone as tech_phone, t.t_name as tech_name,
               sb.sb_booking_date, sb.sb_booking_time, s.s_name
        FROM tms_service_booking sb
        LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
        LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
        WHERE sb.sb_id = ?
    ");
    $stmt->execute([$booking_id]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$details) continue;
    
    // Prepare reminder message
    $time_text = ($reminder_type == '24_hours') ? '24 hours' : '2 hours';
    $message = "Reminder: Your booking #{$booking_id} for {$details['s_name']} is in {$time_text}. ";
    $message .= "Date: {$details['sb_booking_date']} at {$details['sb_booking_time']}";
    
    // Send to user (implement your SMS/Email sending here)
    if ($reminder['br_recipient_type'] == 'user' || $reminder['br_recipient_type'] == 'both') {
        // Example: sendSMS($details['u_phone'], $message);
        // Example: sendEmail($details['u_email'], 'Booking Reminder', $message);
    }
    
    // Send to technician
    if ($reminder['br_recipient_type'] == 'technician' || $reminder['br_recipient_type'] == 'both') {
        // Example: sendSMS($details['tech_phone'], $message);
        // Example: sendEmail($details['tech_email'], 'Booking Reminder', $message);
    }
    
    // Mark as sent
    $bookingSystem->markReminderSent($reminder['br_id']);
    $sent_count++;
}

// Log result
$log_message = date('Y-m-d H:i:s') . " - Sent {$sent_count} reminders\n";
file_put_contents('logs/cron-reminders.log', $log_message, FILE_APPEND);

echo $log_message;
?>
