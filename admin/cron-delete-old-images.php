<?php
/**
 * Automatic Image Deletion Cron Job
 * 
 * This script should be run daily via cron job
 * Deletes service completion and bill images after:
 * - 40 days from completion date (complete deletion)
 * 
 * Setup cron job (Linux):
 * 0 2 * * * /usr/bin/php /path/to/admin/cron-delete-old-images.php
 * 
 * Or run via URL (secure with token):
 * https://yourdomain.com/admin/cron-delete-old-images.php?token=YOUR_SECRET_TOKEN
 */

// Security token (change this to a random string)
define('CRON_TOKEN', 'electrozot_secure_cron_2024');

// Check if running from command line or via URL with token
$is_cli = php_sapi_name() === 'cli';
$is_authorized = $is_cli || (isset($_GET['token']) && $_GET['token'] === CRON_TOKEN);

if (!$is_authorized) {
    die('Unauthorized access');
}

// Include database config
require_once('vendor/inc/config.php');

// Log file
$log_file = __DIR__ . '/logs/image-deletion.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("=== Image Deletion Cron Job Started ===");

// Calculate cutoff date (40 days ago)
$cutoff_date = date('Y-m-d H:i:s', strtotime('-40 days'));

writeLog("Cutoff date: $cutoff_date (40 days ago)");

// Get all completed bookings older than 40 days with images
$query = "SELECT sb_id, sb_completion_img, sb_bill_img, sb_completed_date 
          FROM tms_service_booking 
          WHERE sb_status = 'Completed' 
          AND sb_completed_date IS NOT NULL
          AND sb_completed_date < ?
          AND (sb_completion_img IS NOT NULL OR sb_bill_img IS NOT NULL)";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $cutoff_date);
$stmt->execute();
$result = $stmt->get_result();

$deleted_count = 0;
$error_count = 0;

while ($booking = $result->fetch_object()) {
    $booking_id = $booking->sb_id;
    $completion_img = $booking->sb_completion_img;
    $bill_img = $booking->sb_bill_img;
    
    writeLog("Processing booking #$booking_id (completed: {$booking->sb_completed_date})");
    
    // Delete completion image
    if (!empty($completion_img)) {
        $completion_path = __DIR__ . '/../vendor/img/completions/' . $completion_img;
        if (file_exists($completion_path)) {
            if (unlink($completion_path)) {
                writeLog("  ✓ Deleted completion image: $completion_img");
                $deleted_count++;
            } else {
                writeLog("  ✗ Failed to delete completion image: $completion_img");
                $error_count++;
            }
        } else {
            writeLog("  - Completion image not found: $completion_img");
        }
    }
    
    // Delete bill image
    if (!empty($bill_img)) {
        $bill_path = __DIR__ . '/../vendor/img/bills/' . $bill_img;
        if (file_exists($bill_path)) {
            if (unlink($bill_path)) {
                writeLog("  ✓ Deleted bill image: $bill_img");
                $deleted_count++;
            } else {
                writeLog("  ✗ Failed to delete bill image: $bill_img");
                $error_count++;
            }
        } else {
            writeLog("  - Bill image not found: $bill_img");
        }
    }
    
    // Update database to clear image references
    $update_query = "UPDATE tms_service_booking 
                     SET sb_completion_img = NULL, sb_bill_img = NULL 
                     WHERE sb_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('i', $booking_id);
    
    if ($update_stmt->execute()) {
        writeLog("  ✓ Cleared image references in database for booking #$booking_id");
    } else {
        writeLog("  ✗ Failed to update database for booking #$booking_id");
        $error_count++;
    }
}

writeLog("=== Image Deletion Cron Job Completed ===");
writeLog("Total images deleted: $deleted_count");
writeLog("Errors encountered: $error_count");
writeLog("");

// Output summary (for CLI or web)
echo "Image Deletion Cron Job Completed\n";
echo "Total images deleted: $deleted_count\n";
echo "Errors encountered: $error_count\n";
echo "Check log file for details: $log_file\n";
?>
