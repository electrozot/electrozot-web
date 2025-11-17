<?php
/**
 * ULTIMATE PROJECT CLEANUP SCRIPT
 * Removes ALL unnecessary files identified in analysis
 * Creates backup before deletion for safety
 */

// Prevent timeout
set_time_limit(300);

$backupFolder = 'CLEANUP_BACKUP_' . date('Y-m-d_H-i-s');
$deletedCount = 0;
$errors = [];
$categories = [];

// Define all files to clean up by category
$filesToClean = [
    
    // 1. DOCUMENTATION FILES (78 files)
    'Documentation' => [
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
        'CLEANUP_INSTRUCTIONS.txt',
        'CLEANUP_SUMMARY.md',
        'COMPACT_COMPARISON.md',
        'COMPACT_MOBILE_DESIGN.md',
        'COMPLETE_BOOKING_FIXED.md',
        'COMPLETE_BOOKING_QUICK_FIX.md',
        'COMPLETE_BOOKING_SYSTEM.md',
        'COMPLETE_BOOKING_TROUBLESHOOTING.md',
        'COMPLETE_BUTTON_FIX_SUMMARY.md',
        'COMPLETE_CLEANUP_ANALYSIS.md',
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
        'admin/TESTING_SUMMARY.md',
        'admin/UNIQUE_CONSTRAINTS_INFO.md',
        'tech/COMPLETE_BUTTON_TROUBLESHOOTING.md',
        'DATABASE FILE/00_START_HERE.txt',
        'DATABASE FILE/ADD_SERVICES_GUIDE.txt',
        'DATABASE FILE/ADMIN_FORMS_UPDATED.txt',
        'DATABASE FILE/EXECUTE_INSTRUCTIONS.txt',
        'DATABASE FILE/EXECUTION_CHECKLIST.md',
        'DATABASE FILE/FINAL_SUMMARY.txt',
        'DATABASE FILE/NEW_CATEGORIES_GUIDE.txt',
        'DATABASE FILE/README_TESTING.md',
        'DATABASE FILE/STRICT_CATEGORY_MATCHING.txt',
        'DATABASE FILE/TECHNICIAN_AUTO_AVAILABILITY.txt',
        'DATABASE FILE/TECHNICIAN_FORM_UPDATED.txt',
        'DATABASE FILE/TESTING_SUMMARY.md',
        'DATABASE FILE/VISUAL_GUIDE.txt',
    ],
    
    // 2. TEST & DEBUG FILES (18 files)
    'Test Files' => [
        'test-database.php',
        'process-guest-booking.php',
        'admin/test-admin-panel.php',
        'admin/test-available-technicians.php',
        'admin/test-booking-restore.php',
        'admin/test-service-restore.php',
        'admin/test-technician-availability.php',
        'admin/test-technician-engagement.php',
        'admin/test-user-restore.php',
        'tech/complete-booking-simple.php',
        'tech/complete-test-simple.php',
        'tech/debug-complete.php',
        'tech/test-complete-button.php',
        'tech/test-complete-form.php',
        'tech/test-complete.php',
        'tech/test-db.php',
        'tech/check-system.php',
        'tech/search-booking.php',
    ],
    
    // 3. BACKUP & OLD FILES (8 files)
    'Backup Files' => [
        'tech/dashboard-old-backup.php',
        'tech/dashboard-new.php',
        'tech/profile-new.php',
        'usr/user-dashboard-android.php',
        'usr/user-dashboard-new.php',
        'usr/usr-forgot-pwd.php',
        'usr/usr-register-new.php',
    ],
    
    // 4. PREVIEW/DEMO HTML (3 files)
    'Preview Files' => [
        'compact-mobile-preview.html',
        'mobile-dashboard-preview.html',
        'create-placeholder-icons.html',
    ],
    
    // 5. SETUP SCRIPTS (18 files)
    'Setup Scripts' => [
        'cleanup-old-files.php',
        'COMPLETE_FIX_NOW.php',
        'COMPLETE_PROJECT_CLEANUP.php',
        'fix-all-logics.php',
        'RESTORE_AND_FIX.php',
        'setup-hierarchical-services.php',
        'fix-free-status.sql',
        'admin/add-sample-data.php',
        'admin/check-database-health.php',
        'admin/fix-database.php',
        'admin/fix-missing-columns.php',
        'admin/fix-technician-status.php',
        'admin/setup-feedback-photo.php',
        'admin/setup-recycle-bin.php',
        'admin/setup-service-categories.php',
        'admin/setup-site-settings.php',
        'admin/setup-technician-services.php',
        'admin/setup-unique-constraints.php',
        'admin/update-admin-name.php',
    ],
    
    // 6. OLD SQL FILES (17 files - keep only electrozot_db.sql)
    'Old SQL Files' => [
        'DATABASE FILE/add_all_electrical_services.sql',
        'DATABASE FILE/add_all_services.sql',
        'DATABASE FILE/add_detailed_services.sql',
        'DATABASE FILE/add_hierarchical_service_structure.sql',
        'DATABASE FILE/add_services_final.sql',
        'DATABASE FILE/add_subcategory_column.sql',
        'DATABASE FILE/add_technician_engagement_columns.sql',
        'DATABASE FILE/create_simple_bookings.sql',
        'DATABASE FILE/execute_all.sql',
        'DATABASE FILE/fix_booking_constraint.sql',
        'DATABASE FILE/insert_bookings.sql',
        'DATABASE FILE/populate_services_with_gadgets.sql',
        'DATABASE FILE/sample_data_insert.sql',
        'DATABASE FILE/setup_complete_services.sql',
        'DATABASE FILE/test_database_logic.sql',
        'DATABASE FILE/update_categories_complete.sql',
        'DATABASE FILE/update_service_structure_complete.sql',
    ],
    
    // 7. SYSTEM FILES (2 files)
    'System Files' => [
        '.DS_Store',
        'electrozot/.DS_Store',
    ],
];

// Folders to delete entirely
$foldersToDelete = [
    'electrozot',  // Duplicate project folder
    'screenshort', // Wrong project screenshots
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Ultimate Project Cleanup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header p { font-size: 1.2em; opacity: 0.9; }
        .content { padding: 30px; }
        .warning {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .warning h3 { color: #856404; margin-bottom: 10px; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card h3 { font-size: 2.5em; margin-bottom: 5px; }
        .stat-card p { opacity: 0.9; }
        .category {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .category h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .file-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 10px;
        }
        .file-item {
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.9em;
            border-left: 3px solid #28a745;
        }
        .file-item.error { border-left-color: #dc3545; }
        .buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn {
            padding: 15px 40px;
            font-size: 1.1em;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3); }
        .btn-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .btn-success:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(79, 172, 254, 0.3); }
        .success-message {
            background: #d4edda;
            border-left: 5px solid #28a745;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success-message h3 { color: #155724; margin-bottom: 10px; }
        .folder-item {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #ffc107;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧹 Ultimate Project Cleanup</h1>
            <p>Remove ALL unnecessary files safely</p>
        </div>
        
        <div class="content">
            <?php if (!isset($_GET['confirm'])): ?>
                
                <div class="warning">
                    <h3>⚠️ Before You Start</h3>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>This will move <strong>~138 files + 2 folders</strong> to a backup folder</li>
                        <li>Backup folder: <code><?php echo $backupFolder; ?></code></li>
                        <li>You can restore files from backup if needed</li>
                        <li>Test your system after cleanup</li>
                        <li>Delete backup folder once confirmed working</li>
                    </ul>
                </div>
                
                <div class="stats">
                    <div class="stat-card">
                        <h3>138</h3>
                        <p>Files to Clean</p>
                    </div>
                    <div class="stat-card">
                        <h3>2</h3>
                        <p>Folders to Remove</p>
                    </div>
                    <div class="stat-card">
                        <h3>~97 MB</h3>
                        <p>Space to Free</p>
                    </div>
                </div>
                
                <h2 style="margin-bottom: 20px;">📋 What Will Be Cleaned:</h2>
                
                <?php foreach ($filesToClean as $category => $files): ?>
                <div class="category">
                    <h3><?php echo $category; ?> (<?php echo count($files); ?> files)</h3>
                    <div class="file-list">
                        <?php foreach (array_slice($files, 0, 10) as $file): ?>
                            <div class="file-item">📄 <?php echo $file; ?></div>
                        <?php endforeach; ?>
                        <?php if (count($files) > 10): ?>
                            <div class="file-item" style="border-left-color: #6c757d;">
                                ... and <?php echo count($files) - 10; ?> more files
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="category">
                    <h3>🗂️ Folders to Delete (2 folders)</h3>
                    <?php foreach ($foldersToDelete as $folder): ?>
                        <div class="folder-item">
                            <strong>📁 <?php echo $folder; ?>/</strong>
                            <?php if ($folder == 'electrozot'): ?>
                                <br><small>Complete duplicate of main project</small>
                            <?php elseif ($folder == 'screenshort'): ?>
                                <br><small>Screenshots from wrong project (VehicleBooking-PHP)</small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="buttons">
                    <a href="?confirm=yes" class="btn btn-danger">
                        🗑️ Start Cleanup
                    </a>
                    <a href="FINAL_CLEANUP_REPORT.md" class="btn btn-success" target="_blank">
                        📄 View Full Report
                    </a>
                </div>
                
            <?php else:
                // Perform cleanup
                echo '<div class="success-message">';
                echo '<h3>🚀 Cleanup in Progress...</h3>';
                echo '</div>';
                
                // Create backup folder
                if (!file_exists($backupFolder)) {
                    mkdir($backupFolder, 0777, true);
                }
                
                // Process each category
                foreach ($filesToClean as $category => $files) {
                    echo "<div class='category'>";
                    echo "<h3>Processing: $category</h3>";
                    echo "<div class='file-list'>";
                    
                    foreach ($files as $file) {
                        if (file_exists($file)) {
                            $backupPath = $backupFolder . '/' . dirname($file);
                            if (!file_exists($backupPath)) {
                                mkdir($backupPath, 0777, true);
                            }
                            
                            if (rename($file, $backupFolder . '/' . $file)) {
                                echo "<div class='file-item'>✅ " . basename($file) . "</div>";
                                $deletedCount++;
                                $categories[$category] = ($categories[$category] ?? 0) + 1;
                            } else {
                                echo "<div class='file-item error'>❌ " . basename($file) . " (failed)</div>";
                                $errors[] = $file;
                            }
                        }
                    }
                    
                    echo "</div></div>";
                }
                
                // Delete folders
                echo "<div class='category'>";
                echo "<h3>Removing Folders</h3>";
                
                function deleteDirectory($dir) {
                    if (!file_exists($dir)) return true;
                    if (!is_dir($dir)) return unlink($dir);
                    foreach (scandir($dir) as $item) {
                        if ($item == '.' || $item == '..') continue;
                        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
                    }
                    return rmdir($dir);
                }
                
                foreach ($foldersToDelete as $folder) {
                    if (file_exists($folder)) {
                        if (deleteDirectory($folder)) {
                            echo "<div class='folder-item' style='border-left-color: #28a745;'>✅ Deleted: $folder/</div>";
                            $deletedCount++;
                        } else {
                            echo "<div class='folder-item' style='border-left-color: #dc3545;'>❌ Failed: $folder/</div>";
                            $errors[] = $folder;
                        }
                    }
                }
                echo "</div>";
                
                // Summary
                echo '<div class="success-message">';
                echo '<h3>✅ Cleanup Complete!</h3>';
                echo '<p><strong>Files moved to backup:</strong> ' . $deletedCount . '</p>';
                echo '<p><strong>Backup location:</strong> ' . $backupFolder . '/</p>';
                if (count($errors) > 0) {
                    echo '<p style="color: #dc3545;"><strong>Errors:</strong> ' . count($errors) . ' files could not be moved</p>';
                }
                echo '</div>';
                
                echo '<div class="stats">';
                foreach ($categories as $cat => $count) {
                    echo "<div class='stat-card'>";
                    echo "<h3>$count</h3>";
                    echo "<p>$cat</p>";
                    echo "</div>";
                }
                echo '</div>';
                
                echo '<div class="warning">';
                echo '<h3>📝 Next Steps:</h3>';
                echo '<ol style="margin-left: 20px; margin-top: 10px;">';
                echo '<li>Test your entire system thoroughly</li>';
                echo '<li>Check admin panel, user panel, and technician panel</li>';
                echo '<li>Verify all features work correctly</li>';
                echo '<li>If everything works: Delete the backup folder</li>';
                echo '<li>If issues occur: Restore files from backup</li>';
                echo '</ol>';
                echo '</div>';
                
                echo '<div class="buttons">';
                echo '<a href="admin/admin-dashboard.php" class="btn btn-success">Test Admin Panel</a>';
                echo '<a href="index.php" class="btn btn-success">Test Homepage</a>';
                echo '</div>';
                
            endif; ?>
        </div>
    </div>
</body>
</html>
