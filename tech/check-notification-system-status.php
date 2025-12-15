<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];

// Check if sound file exists
$sound_file = '../admin/vendor/sounds/arived.mp3';
$sound_exists = file_exists($sound_file);
$sound_size = $sound_exists ? filesize($sound_file) : 0;

// Check notification system files
$notification_files = [
    'includes/notification-system.php' => file_exists('includes/notification-system.php'),
    'includes/notification-system-debug.php' => file_exists('includes/notification-system-debug.php'),
    'check-technician-notifications.php' => file_exists('check-technician-notifications.php')
];

// Test database connection and get recent notifications
$recent_notifications = 0;
try {
    $query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_updated_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_notifications = $result->fetch_assoc()['count'];
} catch(Exception $e) {
    $recent_notifications = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'technician_id' => $t_id,
    'technician_name' => $t_name,
    'sound_system' => [
        'sound_file_exists' => $sound_exists,
        'sound_file_path' => $sound_file,
        'sound_file_size' => $sound_size . ' bytes',
        'sound_file_accessible' => $sound_exists && is_readable($sound_file)
    ],
    'notification_files' => $notification_files,
    'database' => [
        'connection' => $mysqli ? 'Connected' : 'Failed',
        'recent_notifications_count' => $recent_notifications
    ],
    'system_status' => [
        'all_files_present' => array_reduce($notification_files, function($carry, $item) { return $carry && $item; }, true),
        'sound_system_ready' => $sound_exists && is_readable($sound_file),
        'overall_status' => 'Ready'
    ],
    'recommendations' => [
        'sound_file' => $sound_exists ? 'Sound file is present and accessible' : 'Sound file is missing or not accessible',
        'notification_system' => 'Currently using DEBUG version - switch to production version for better performance',
        'testing' => 'Use test-notification-sound.php to test the sound system'
    ]
]);
?>