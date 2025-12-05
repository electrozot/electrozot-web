<?php
/**
 * Cron Job: Auto-delete bills older than 30 days
 * Run this daily via cron: 0 2 * * * /usr/bin/php /path/to/admin/cron-delete-old-bills.php
 */

// Include database config
require_once('vendor/inc/config.php');

// Delete bills older than 30 days
$delete_query = "DELETE FROM tms_bills 
                 WHERE bill_generated_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";

$mysqli->query($delete_query);

// Silent execution - no output
exit(0);
?>
