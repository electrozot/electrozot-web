<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];

// Check for notification system files
$notification_files = [
    'includes/notification-system.php' => file_exists('includes/notification-system.php'),
    'includes/notification-system-debug.php' => file_exists('includes/notification-system-debug.php'),
    'includes/notification-system-mobile-persistent.php' => file_exists('includes/notification-system-mobile-persistent.php'),
    'includes/notification-system-mobile-enhanced.php' => file_exists('includes/notification-system-mobile-enhanced.php'),
    'includes/push-notification-setup.php' => file_exists('includes/push-notification-setup.php'),
    'includes/unified-notification-system.php' => file_exists('includes/unified-notification-system.php')
];

// Check sound file
$sound_file_exists = file_exists('../admin/vendor/sounds/arived.mp3');
$sound_file_size = $sound_file_exists ? filesize('../admin/vendor/sounds/arived.mp3') : 0;

// Check service worker
$service_worker_exists = file_exists('service-worker.js');

// Check dashboard includes
$dashboard_content = file_get_contents('dashboard.php');
$includes_in_dashboard = [];
foreach ($notification_files as $file => $exists) {
    if (strpos($dashboard_content, $file) !== false) {
        $includes_in_dashboard[] = $file;
    }
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'technician_id' => $t_id,
    'notification_files' => $notification_files,
    'includes_in_dashboard' => $includes_in_dashboard,
    'sound_system' => [
        'file_exists' => $sound_file_exists,
        'file_size' => $sound_file_size,
        'file_path' => '../admin/vendor/sounds/arived.mp3'
    ],
    'service_worker' => [
        'exists' => $service_worker_exists
    ],
    'conflicts_detected' => count($includes_in_dashboard) > 1,
    'recommendations' => [
        'active_system' => count($includes_in_dashboard) === 1 ? $includes_in_dashboard[0] : 'Multiple systems detected',
        'should_use' => 'includes/unified-notification-system.php',
        'conflicts' => count($includes_in_dashboard) > 1 ? 'Remove other notification systems from dashboard.php' : 'No conflicts detected'
    ]
]);
?>