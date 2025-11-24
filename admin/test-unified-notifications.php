<?php
/**
 * Test Unified Notification System
 * Creates test notifications to verify the system works
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$test_result = '';

// Handle test actions
if (isset($_POST['test_action'])) {
    $action = $_POST['test_action'];
    
    switch ($action) {
        case 'create_test_booking':
            // Create a test booking
            $test_query = "INSERT INTO tms_service_booking 
                          (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, 
                           sb_address, sb_phone, sb_status, sb_created_at)
                          VALUES (1, 1, CURDATE(), '10:00:00', 'Test Address', '1234567890', 
                                  'Pending', NOW())";
            if ($mysqli->query($test_query)) {
                $test_result = "✅ Test booking created! Notification should appear in 3 seconds.";
            } else {
                $test_result = "❌ Error: " . $mysqli->error;
            }
            break;
            
        case 'test_sound':
            $test_result = "🔊 Sound test triggered! Check if you hear the notification sound.";
            break;
    }
}

// Get current notification stats
$stats_query = "SELECT 
                (SELECT COUNT(*) FROM tms_service_booking WHERE sb_status = 'Pending') as pending,
                (SELECT COUNT(*) FROM tms_service_booking WHERE sb_status IN ('Rejected', 'Rejected by Technician')) as rejected,
                (SELECT COUNT(*) FROM tms_service_booking WHERE sb_status = 'Completed') as completed,
                (SELECT COUNT(*) FROM tms_service_booking WHERE sb_status = 'Cancelled') as cancelled";
$stats = $mysqli->query($stats_query)->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Unified Notifications</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 30px; }
        .test-card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 10px; }
        .stat-number { font-size: 36px; font-weight: bold; }
        .stat-label { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    
    <div class="container">
        <div class="test-card">
            <h2><i class="fas fa-vial"></i> Test Unified Notification System</h2>
            <p class="text-muted">Test the real-time notification system with sound alerts</p>
            
            <?php if ($test_result): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <?php echo $test_result; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-chart-bar"></i> Current Booking Statistics</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="stat-number"><?php echo $stats['pending']; ?></div>
                        <div class="stat-label">Pending Bookings</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <div class="stat-number"><?php echo $stats['rejected']; ?></div>
                        <div class="stat-label">Rejected Bookings</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="stat-number"><?php echo $stats['completed']; ?></div>
                        <div class="stat-label">Completed Bookings</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                        <div class="stat-number"><?php echo $stats['cancelled']; ?></div>
                        <div class="stat-label">Cancelled Bookings</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-flask"></i> Test Actions</h4>
            
            <form method="POST" style="margin-bottom: 20px;">
                <input type="hidden" name="test_action" value="create_test_booking">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle"></i> Create Test Booking
                </button>
                <p class="text-muted mt-2">Creates a new pending booking. You should see a notification popup and hear a sound within 3 seconds.</p>
            </form>
            
            <button onclick="testSound()" class="btn btn-success btn-lg">
                <i class="fas fa-volume-up"></i> Test Sound Only
            </button>
            <p class="text-muted mt-2">Plays the notification sound without creating a booking.</p>
            
            <hr>
            
            <button onclick="testBrowserNotification()" class="btn btn-info btn-lg">
                <i class="fas fa-bell"></i> Test Browser Notification
            </button>
            <p class="text-muted mt-2">Shows a browser notification (requires permission).</p>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-check-circle"></i> Verification Checklist</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-bell"></i> Notification bell icon visible in top navigation
                </li>
                <li class="list-group-item">
                    <i class="fas fa-volume-up"></i> Sound file exists at: <code>vendor/sounds/arived.mp3</code>
                    <?php if (file_exists('vendor/sounds/arived.mp3')): ?>
                        <span class="badge badge-success">✓ Found</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ Missing</span>
                    <?php endif; ?>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-code"></i> Unified system included in navigation
                </li>
                <li class="list-group-item">
                    <i class="fas fa-sync"></i> Real-time checking every 3 seconds
                </li>
            </ul>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-info-circle"></i> How It Works</h4>
            <ol>
                <li><strong>Automatic Checking:</strong> System checks for new notifications every 3 seconds</li>
                <li><strong>Sound Alert:</strong> Plays <code>arived.mp3</code> when new notification arrives</li>
                <li><strong>Popup Notification:</strong> Shows animated popup in top-right corner</li>
                <li><strong>Browser Notification:</strong> Shows system notification (if permission granted)</li>
                <li><strong>Badge Counter:</strong> Shows unread count on bell icon</li>
            </ol>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-link"></i> Quick Links</h4>
            <a href="admin-dashboard.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="admin-manage-service-booking.php" class="btn btn-info">
                <i class="fas fa-list"></i> All Bookings
            </a>
            <a href="cleanup-old-notifications.php" class="btn btn-warning">
                <i class="fas fa-broom"></i> Cleanup Old Systems
            </a>
            <a href="api-unified-notifications.php?last_check=0" class="btn btn-secondary" target="_blank">
                <i class="fas fa-code"></i> View API Response
            </a>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function testSound() {
        const sound = document.getElementById('unifiedNotificationSound');
        if (sound) {
            sound.currentTime = 0;
            sound.volume = 0.7;
            sound.play().then(() => {
                alert('✅ Sound played successfully!');
            }).catch(e => {
                alert('❌ Sound play failed: ' + e.message + '\n\nTry clicking on the page first to enable sound.');
            });
        } else {
            alert('❌ Sound element not found! Make sure unified-notification-system.php is included.');
        }
    }
    
    function testBrowserNotification() {
        if (!('Notification' in window)) {
            alert('❌ Browser notifications not supported');
            return;
        }
        
        if (Notification.permission === 'granted') {
            new Notification('🧪 Test Notification', {
                body: 'This is a test notification from ElectroZot admin panel',
                icon: 'vendor/img/logo.png',
                requireInteraction: false
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification('🧪 Test Notification', {
                        body: 'Permission granted! Notifications will now work.',
                        icon: 'vendor/img/logo.png'
                    });
                }
            });
        } else {
            alert('❌ Notification permission denied. Please enable in browser settings.');
        }
    }
    
    // Auto-refresh stats every 10 seconds
    setInterval(() => {
        location.reload();
    }, 10000);
    </script>
</body>
</html>
