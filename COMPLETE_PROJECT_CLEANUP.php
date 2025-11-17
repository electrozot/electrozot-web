<?php
/**
 * COMPLETE PROJECT CLEANUP
 * Moves all unnecessary files to backup folder
 * Run once to clean up the project
 */

$backup_dir = 'PROJECT_BACKUP_' . date('Y-m-d_H-i-s');
mkdir($backup_dir, 0755, true);

$stats = [
    'moved' => 0,
    'not_found' => 0,
    'errors' => 0
];

// Files to move
$files_to_cleanup = [
    // Documentation files (keep only README.md)
    'ADMIN_ALL_BOOKINGS_BUGS_FIXED.md',
    'ADMIN_COMPLETION_DETAILS_SUMMARY.md',
    'ADMIN_DASHBOARD_SIMPLIFICATION.md',
    'ADMIN_MENU_COMPARISON.txt',
    'ADMIN_MENU_FINAL.txt',
    'ADMIN_QUICK_REFERENCE.md',
    'ADMIN_SIMPLIFICATION_SUMMARY.txt',
    'APPLY_TO_OTHER_PAGES.md',
    'AUTOMATIC_TECHNICIAN_STATUS_SYSTEM.md',
    'BEFORE_AFTER_COMPARISON.md',
    'BOOKING_DELETE_REDIRECT_FIX.md',
    'BOOKING_FORM_COMPARISON.txt',
    'CHANGES_SUMMARY.txt',
    'CLEANUP_SUMMARY.md',
    'COMPACT_COMPARISON.md',
    'COMPACT_MOBILE_DESIGN.md',
    'COMPLETE_BOOKING_FIXED.md',
    'COMPLETE_BOOKING_QUICK_FIX.md',
    'COMPLETE_BOOKING_SYSTEM.md',
    'COMPLETE_BOOKING_TROUBLESHOOTING.md',
    'COMPLETE_BUTTON_FIX_SUMMARY.md',
    'COMPLETE_IMPLEMENTATION_SUMMARY.txt',
    'COMPLETE_SERVICE_STRUCTURE_GUIDE.md',
    'COMPREHENSIVE_TEST_CONDITIONS.md',
    'CREATE_PWA_ICONS.md',
    'CUSTOM_NOTIFICATION_SOUND.md',
    'FEEDBACK_PUBLISH_IMPROVEMENT.md',
    'FEEDBACK_SETUP_INSTRUCTIONS.md',
    'FINAL_CHANGES_SUMMARY.txt',
    'FINAL_MOBILE_SUMMARY.md',
    'FINAL_UPDATE_SUMMARY.md',
    'HIERARCHICAL_SERVICE_SYSTEM_GUIDE.md',
    'HIERARCHICAL_SERVICES_QUICK_START.md',
    'HOME_SLIDER_SETUP.md',
    'HOW_TO_ADD_SERVICES.md',
    'IMPLEMENTATION_COMPLETE.txt',
    'IMPLEMENTATION_SUMMARY_HIERARCHICAL_SERVICES.md',
    'IMPLEMENTATION_SUMMARY.md',
    'INSTALL_SERVICES_NOW.md',
    'MOBILE_DASHBOARD_IMPROVEMENTS.md',
    'MOBILE_FEATURES_QUICK_GUIDE.md',
    'MOBILE_LOGIN_SYSTEM.md',
    'MOBILE_REDESIGN_SUMMARY.md',
    'NOTIFICATION_DEBUG_GUIDE.md',
    'NOTIFICATION_FIX_APPLIED.md',
    'NOTIFICATION_MARQUEE_SYSTEM.md',
    'NOTIFICATION_TROUBLESHOOTING.md',
    'OLD_FILES_CLEANUP_GUIDE.md',
    'ORGANIZED_BOOKING_FLOW.md',
    'PASSWORD_MANAGEMENT_RESTORED.md',
    'PRICE_DISPLAY_UPDATE.md',
    'PROFESSIONAL_COLORS.md',
    'PROPER_BOOKING_STRUCTURE.md',
    'PWA_QUICK_START.md',
    'PWA_SETUP_GUIDE.md',
    'PWA_TROUBLESHOOTING.md',
    'QUICK_REFERENCE.md',
    'QUICK_START_MOBILE.md',
    'QUICK_VISUAL_GUIDE.txt',
    'REALTIME_NOTIFICATIONS.md',
    'SERVICE_COMPLETION_DISPLAY.md',
    'SERVICE_DATABASE_SETUP_GUIDE.md',
    'SERVICE_ICONS_AND_BUTTON.md',
    'SETUP_NEW_SERVICES.md',
    'SETUP_ONE_BOOKING_RULE.md',
    'SIMPLIFIED_BOOKING_FORM.md',
    'SIMPLIFIED_BOOKING_UPDATE.md',
    'START_HERE_HIERARCHICAL_SERVICES.md',
    'START_HERE_ONE_BOOKING_SYSTEM.md',
    'SUBCATEGORY_STRUCTURE_VISUAL.md',
    'SYSTEM_FIX_GUIDE.md',
    'SYSTEM_LOGS_AUTO_CLEANUP.md',
    'TECH_LOGIN_THEME_ALIGNED.md',
    'TECHNICIAN_ENGAGEMENT_FLOW.md',
    'TECHNICIAN_FORM_UPDATE.md',
    'TECHNICIAN_IMPROVEMENTS.md',
    'TECHNICIAN_LOGIN_REDESIGN.md',
    'TECHNICIAN_NOTIFICATION_ACCESS.md',
    'TECHNICIAN_NOTIFICATIONS_GUIDE.md',
    'TECHNICIAN_NOTIFICATIONS.md',
    'TECHNICIAN_ONE_BOOKING_RULE.md',
    'TECHNICIAN_STATUS_EXPLAINED.md',
    'TESTING_REPORT.md',
    'UNIVERSAL_NOTIFICATIONS.md',
    'USER_TYPES_SYSTEM.md',
    'VISUAL_FLOW_DIAGRAM.md',
    'VISUAL_IMPROVEMENTS.md',
    'WHAT_TO_DO_NOW.md',
    
    // Test/Setup files
    'cleanup-old-files.php',
    'compact-mobile-preview.html',
    'create-placeholder-icons.html',
    'COMPLETE_FIX_NOW.php',
    'fix-all-logics.php',
    'fix-free-status.sql',
    'mobile-dashboard-preview.html',
    'RESTORE_AND_FIX.php',
    'setup-hierarchical-services.php',
    'test-database.php',
    '.DS_Store',
    
    // Old admin files
    'admin/admin-add-booking-usr.php',
    'admin/admin-add-booking.php',
    'admin/admin-add-feedback.php',
    'admin/admin-edit-feedback.php',
    'admin/admin-edit-slider.php',
    'admin/admin-manage-booking.php',
    'admin/admin-manage-service-booking.php',
    'admin/admin-manage-slider.php',
    'admin/admin-view-booking.php',
    'admin/admin-view-feedback.php',
    'admin/admin-view-service-booking.php',
    'admin/admin-view-service.php',
    'admin/admin-view-technician.php',
    'admin/admin-view-user.php',
    
    // Admin test files
    'admin/add-sample-data.php',
    'admin/check-database-health.php',
    'admin/fix-database.php',
    'admin/fix-missing-columns.php',
    'admin/fix-technician-status.php',
    'admin/setup-feedback-photo.php',
    'admin/setup-recycle-bin.php',
    'admin/setup-service-categories.php',
    'admin/setup-technician-services.php',
    'admin/setup-unique-constraints.php',
    'admin/test-admin-panel.php',
    'admin/test-available-technicians.php',
    'admin/test-booking-restore.php',
    'admin/test-service-restore.php',
    'admin/test-technician-availability.php',
    'admin/test-technician-engagement.php',
    'admin/test-user-restore.php',
    'admin/TESTING_SUMMARY.md',
    'admin/UNIQUE_CONSTRAINTS_INFO.md',
    'admin/update-admin-name.php',
    
    // Tech test files
    'tech/check-system.php',
    'tech/COMPLETE_BUTTON_TROUBLESHOOTING.md',
    'tech/complete-booking-simple.php',
    'tech/complete-test-simple.php',
    'tech/dashboard-old-backup.php',
    'tech/debug-complete.php',
    'tech/profile-new.php',
    'tech/search-booking.php',
    'tech/test-complete-button.php',
    'tech/test-complete-form.php',
    'tech/test-complete.php',
    'tech/test-db.php',
];

function moveFile($file, $backup_dir, &$stats) {
    if (file_exists($file)) {
        $filename = basename($file);
        $subdir = dirname($file);
        
        if ($subdir != '.') {
            $dest_dir = $backup_dir . '/' . $subdir;
            if (!file_exists($dest_dir)) {
                mkdir($dest_dir, 0755, true);
            }
            $destination = $dest_dir . '/' . $filename;
        } else {
            $destination = $backup_dir . '/' . $filename;
        }
        
        if (rename($file, $destination)) {
            $stats['moved']++;
            return true;
        } else {
            $stats['errors']++;
            return false;
        }
    } else {
        $stats['not_found']++;
        return null;
    }
}

echo "<!DOCTYPE html><html><head><title>Complete Project Cleanup</title>";
echo "<style>
body { font-family: Arial; max-width: 900px; margin: 30px auto; padding: 20px; }
.success { color: green; background: #d4edda; padding: 8px; margin: 5px 0; border-radius: 4px; font-size: 0.9rem; }
.info { color: blue; background: #d1ecf1; padding: 8px; margin: 5px 0; border-radius: 4px; font-size: 0.9rem; }
.warning { color: orange; background: #fff3cd; padding: 8px; margin: 5px 0; border-radius: 4px; font-size: 0.9rem; }
h1 { color: #333; }
.summary { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
</style></head><body>";

echo "<h1>🧹 Complete Project Cleanup</h1>";
echo "<p>Moving unnecessary files to backup...</p><hr>";

foreach ($files_to_cleanup as $file) {
    $result = moveFile($file, $backup_dir, $stats);
    if ($result === true) {
        echo "<div class='success'>✓ Moved: $file</div>";
    } elseif ($result === false) {
        echo "<div class='warning'>✗ Failed: $file</div>";
    }
}

echo "<hr><div class='summary'>";
echo "<h2>📊 Cleanup Summary</h2>";
echo "<p><strong>Files moved:</strong> {$stats['moved']}</p>";
echo "<p><strong>Files not found:</strong> {$stats['not_found']}</p>";
echo "<p><strong>Errors:</strong> {$stats['errors']}</p>";
echo "<p><strong>Backup location:</strong> $backup_dir</p>";
echo "</div>";

if ($stats['moved'] > 0) {
    echo "<div class='success'><h3>✅ Cleanup Complete!</h3>";
    echo "<p>Moved {$stats['moved']} files to backup folder.</p></div>";
}

echo "<div class='info'><h3>⚠️ Next Steps:</h3>";
echo "<ol><li>Test your entire system</li>";
echo "<li>Check admin panel, user panel, tech panel</li>";
echo "<li>If everything works, delete backup folder</li>";
echo "<li>If something breaks, restore from backup</li></ol></div>";

echo "<p><a href='admin/admin-dashboard.php' style='display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>Go to Admin Dashboard</a></p>";
echo "</body></html>";
?>
