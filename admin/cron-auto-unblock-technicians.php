<?php
/**
 * Cron Job: Auto-unblock technicians whose block period has expired
 * Run this every hour or via system cron
 */

require_once('vendor/inc/config.php');
require_once('vendor/inc/rejection-helper.php');

$unblocked_count = autoUnblockTechnicians($mysqli);

if ($unblocked_count > 0) {
    // Log the action
    $log_message = "Auto-unblocked $unblocked_count technician(s)";
    $mysqli->query("INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                   VALUES ('$log_message', 'SYSTEM', 'AUTO', 'CRON', 'system')");
    
    echo "✓ Unblocked $unblocked_count technician(s)\n";
} else {
    echo "No technicians to unblock\n";
}
?>
