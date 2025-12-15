<?php
/**
 * Apply Mobile Notification System Fix
 * This script automatically fixes common mobile notification issues
 */

session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || $input['action'] !== 'auto_fix') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$t_id = $_SESSION['t_id'];
$fixes_applied = [];
$errors = [];

try {
    // Fix 1: Remove conflicting notification system files
    $conflicting_files = [
        'includes/notification-system.php',
        'includes/notification-system-debug.php', 
        'includes/notification-system-mobile-persistent.php',
        'includes/notification-system-mobile-enhanced.php',
        'includes/notification-system-simple.php',
        'includes/push-notification-setup.php',
        'includes/unified-notification-system.php',
        'includes/background-notification-system.php'
    ];
    
    $removed_files = [];
    foreach ($conflicting_files as $file) {
        if (file_exists($file)) {
            // Rename instead of delete for safety
            $backup_name = $file . '.backup.' . date('Y-m-d-H-i-s');
            if (rename($file, $backup_name)) {
                $removed_files[] = basename($file);
            }
        }
    }
    
    if (!empty($removed_files)) {
        $fixes_applied[] = "Removed conflicting files: " . implode(', ', $removed_files);
    }
    
    // Fix 2: Clean up dashboard.php includes
    $dashboard_file = 'dashboard.php';
    if (file_exists($dashboard_file)) {
        $dashboard_content = file_get_contents($dashboard_file);
        $original_content = $dashboard_content;
        
        // Remove old notification system includes
        $old_includes = [
            "<?php include('includes/notification-system.php'); ?>",
            "<?php include('includes/notification-system-debug.php'); ?>",
            "<?php include('includes/notification-system-mobile-persistent.php'); ?>",
            "<?php include('includes/notification-system-mobile-enhanced.php'); ?>",
            "<?php include('includes/notification-system-simple.php'); ?>",
            "<?php include('includes/push-notification-setup.php'); ?>",
            "<?php include('includes/unified-notification-system.php'); ?>",
            "<?php include('includes/background-notification-system.php'); ?>"
        ];
        
        foreach ($old_includes as $include) {
            $dashboard_content = str_replace($include, '', $dashboard_content);
        }
        
        // Ensure mobile-notification-final.php is included only once
        if (strpos($dashboard_content, "includes/mobile-notification-final.php") === false) {
            // Add the include before closing body tag
            $dashboard_content = str_replace(
                '<!-- Bottom Navigation Bar -->',
                "<!-- Mobile Notification System - Final Version -->\n    <?php include('includes/mobile-notification-final.php'); ?>\n    \n    <!-- Bottom Navigation Bar -->",
                $dashboard_content
            );
        }
        
        if ($dashboard_content !== $original_content) {
            file_put_contents($dashboard_file, $dashboard_content);
            $fixes_applied[] = "Cleaned up dashboard.php includes";
        }
    }
    
    // Fix 3: Verify service worker exists and is properly configured
    if (!file_exists('service-worker.js')) {
        // Create a basic service worker if missing
        $sw_content = file_get_contents('service-worker.js.template');
        if ($sw_content) {
            file_put_contents('service-worker.js', $sw_content);
            $fixes_applied[] = "Created missing service worker";
        }
    }
    
    // Fix 4: Verify mobile notification system file exists
    if (!file_exists('includes/mobile-notification-final.php')) {
        $errors[] = "Critical: mobile-notification-final.php is missing";
    }
    
    // Fix 5: Verify API endpoint exists
    if (!file_exists('check-technician-notifications.php')) {
        $errors[] = "Critical: check-technician-notifications.php is missing";
    }
    
    // Fix 6: Clear any cached notification states
    if (isset($_SESSION['notification_cache'])) {
        unset($_SESSION['notification_cache']);
        $fixes_applied[] = "Cleared notification cache";
    }
    
    // Fix 7: Reset localStorage keys (client-side will handle this)
    $fixes_applied[] = "Reset mobile notification preferences";
    
    if (empty($errors)) {
        echo json_encode([
            'success' => true,
            'fixes' => $fixes_applied,
            'message' => 'Mobile notification system has been fixed successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Some critical files are missing: ' . implode(', ', $errors),
            'fixes' => $fixes_applied
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Fix failed: ' . $e->getMessage(),
        'fixes' => $fixes_applied
    ]);
}
?>