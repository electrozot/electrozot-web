<?php
/**
 * Mobile Notification System Fix
 * This script will clean up conflicting notification systems and ensure mobile notifications work properly
 */

session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];

// Step 1: Check current system status
$issues_found = [];
$fixes_applied = [];

// Check for conflicting notification files
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

$existing_conflicts = [];
foreach ($conflicting_files as $file) {
    if (file_exists($file)) {
        $existing_conflicts[] = $file;
    }
}

if (count($existing_conflicts) > 0) {
    $issues_found[] = "Found " . count($existing_conflicts) . " conflicting notification system files";
}

// Check dashboard.php for multiple includes
$dashboard_content = file_get_contents('dashboard.php');
$dashboard_includes = [];
foreach ($conflicting_files as $file) {
    if (strpos($dashboard_content, $file) !== false) {
        $dashboard_includes[] = $file;
    }
}

if (count($dashboard_includes) > 0) {
    $issues_found[] = "Dashboard includes " . count($dashboard_includes) . " conflicting notification systems";
}

// Check sound file
$sound_file = '../admin/vendor/sounds/arived.mp3';
if (!file_exists($sound_file)) {
    $issues_found[] = "Sound file missing: " . $sound_file;
}

// Check service worker
if (!file_exists('service-worker.js')) {
    $issues_found[] = "Service worker missing: service-worker.js";
}

// Check API endpoint
if (!file_exists('check-technician-notifications.php')) {
    $issues_found[] = "API endpoint missing: check-technician-notifications.php";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Mobile Notifications - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .fix-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .fix-header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .fix-section {
            padding: 30px;
        }
        
        .issue-item {
            background: #fef2f2;
            border: 2px solid #fecaca;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .issue-item i {
            color: #ef4444;
            font-size: 1.5rem;
        }
        
        .fix-item {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .fix-item i {
            color: #10b981;
            font-size: 1.5rem;
        }
        
        .fix-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 15px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin: 20px 0;
        }
        
        .fix-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        
        .test-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .test-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
        }
        
        .status-good {
            color: #10b981;
            font-weight: bold;
        }
        
        .status-bad {
            color: #ef4444;
            font-weight: bold;
        }
        
        .code-block {
            background: #1f2937;
            color: #f9fafb;
            padding: 15px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 14px;
            margin: 15px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="fix-container">
        <div class="fix-header">
            <h1><i class="fas fa-tools"></i> Mobile Notification System Fix</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Diagnose and fix mobile notification issues</p>
        </div>
        
        <div class="fix-section">
            <h3><i class="fas fa-search"></i> System Diagnosis</h3>
            
            <?php if (empty($issues_found)): ?>
                <div class="fix-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>No Issues Found</strong><br>
                        <small>Your mobile notification system appears to be configured correctly.</small>
                    </div>
                </div>
            <?php else: ?>
                <p><strong class="status-bad">Issues Found: <?php echo count($issues_found); ?></strong></p>
                
                <?php foreach ($issues_found as $issue): ?>
                    <div class="issue-item">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Issue:</strong> <?php echo htmlspecialchars($issue); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <h3><i class="fas fa-cog"></i> System Status</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;">
                <div>
                    <strong>Sound File:</strong><br>
                    <span class="<?php echo file_exists($sound_file) ? 'status-good' : 'status-bad'; ?>">
                        <?php echo file_exists($sound_file) ? '✅ Present' : '❌ Missing'; ?>
                    </span>
                </div>
                
                <div>
                    <strong>Service Worker:</strong><br>
                    <span class="<?php echo file_exists('service-worker.js') ? 'status-good' : 'status-bad'; ?>">
                        <?php echo file_exists('service-worker.js') ? '✅ Present' : '❌ Missing'; ?>
                    </span>
                </div>
                
                <div>
                    <strong>API Endpoint:</strong><br>
                    <span class="<?php echo file_exists('check-technician-notifications.php') ? 'status-good' : 'status-bad'; ?>">
                        <?php echo file_exists('check-technician-notifications.php') ? '✅ Present' : '❌ Missing'; ?>
                    </span>
                </div>
                
                <div>
                    <strong>Mobile System:</strong><br>
                    <span class="<?php echo file_exists('includes/mobile-notification-final.php') ? 'status-good' : 'status-bad'; ?>">
                        <?php echo file_exists('includes/mobile-notification-final.php') ? '✅ Present' : '❌ Missing'; ?>
                    </span>
                </div>
            </div>
            
            <?php if (!empty($existing_conflicts)): ?>
                <h3><i class="fas fa-exclamation-triangle"></i> Conflicting Files Found</h3>
                <p>The following files may be causing conflicts with the mobile notification system:</p>
                
                <?php foreach ($existing_conflicts as $file): ?>
                    <div class="issue-item">
                        <i class="fas fa-file-code"></i>
                        <div>
                            <strong><?php echo htmlspecialchars($file); ?></strong><br>
                            <small>This file should be removed or renamed to avoid conflicts.</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <h3><i class="fas fa-wrench"></i> Recommended Actions</h3>
            
            <div class="fix-item">
                <i class="fas fa-mobile-alt"></i>
                <div>
                    <strong>Use Only Mobile-Final System</strong><br>
                    <small>Ensure dashboard.php only includes mobile-notification-final.php</small>
                </div>
            </div>
            
            <div class="fix-item">
                <i class="fas fa-trash-alt"></i>
                <div>
                    <strong>Remove Conflicting Files</strong><br>
                    <small>Delete or rename old notification system files to prevent conflicts</small>
                </div>
            </div>
            
            <div class="fix-item">
                <i class="fas fa-volume-up"></i>
                <div>
                    <strong>Verify Sound File</strong><br>
                    <small>Ensure arived.mp3 is accessible at ../admin/vendor/sounds/arived.mp3</small>
                </div>
            </div>
            
            <div class="fix-item">
                <i class="fas fa-cogs"></i>
                <div>
                    <strong>Test Service Worker</strong><br>
                    <small>Verify service worker is registered for background notifications</small>
                </div>
            </div>
            
            <button class="fix-btn" onclick="applyAutoFix()">
                <i class="fas fa-magic"></i> Apply Automatic Fix
            </button>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="test-mobile-final.php" class="test-btn">
                    <i class="fas fa-flask"></i> Test Mobile System
                </a>
                
                <a href="dashboard.php" class="test-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <h3><i class="fas fa-code"></i> Manual Fix Instructions</h3>
            
            <p><strong>If automatic fix doesn't work, follow these manual steps:</strong></p>
            
            <div class="code-block">
1. Remove conflicting notification files:
   - Delete or rename old notification system files in includes/
   - Keep only mobile-notification-final.php

2. Verify dashboard.php includes:
   - Should only include: includes/mobile-notification-final.php
   - Remove any other notification system includes

3. Check sound file:
   - Verify ../admin/vendor/sounds/arived.mp3 exists
   - File should be accessible from tech directory

4. Test service worker:
   - Open browser dev tools (F12)
   - Go to Application > Service Workers
   - Verify service-worker.js is registered

5. Test on mobile device:
   - Open dashboard on mobile phone
   - Allow notifications when prompted
   - Test with test-mobile-final.php
            </div>
        </div>
    </div>

    <script>
    function applyAutoFix() {
        if (confirm('This will automatically fix common mobile notification issues. Continue?')) {
            // Show loading
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying Fix...';
            btn.disabled = true;
            
            // Apply fixes via AJAX
            fetch('apply-mobile-notification-fix.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'auto_fix',
                    technician_id: <?php echo $t_id; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Auto-fix completed successfully!\n\nFixed:\n' + data.fixes.join('\n'));
                    location.reload();
                } else {
                    alert('❌ Auto-fix failed: ' + data.error);
                }
            })
            .catch(error => {
                alert('❌ Error applying fix: ' + error.message);
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    }
    </script>
</body>
</html>