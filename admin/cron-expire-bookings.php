<?php
/**
 * CRON JOB: Auto-expire old pending bookings
 * Run every hour: 0 * * * * php /path/to/admin/cron-expire-bookings.php
 */

require_once('vendor/inc/config.php');
require_once('BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

// Expire old bookings
$result = $bookingSystem->autoExpireBookings();

// Log result
$log_message = date('Y-m-d H:i:s') . " - Expired {$result['expired_count']} bookings\n";
file_put_contents('logs/cron-expire.log', $log_message, FILE_APPEND);

echo $log_message;
?>
