<?php
/**
 * API: Update Technician Status
 * Endpoint to manually trigger technician status updates
 * Can be called via AJAX or cron job
 */

session_start();
require_once('vendor/inc/config.php');

// Check authentication (admin only)
if (!isset($_SESSION['a_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

header('Content-Type: application/json');

try {
    // Include the auto-update script
    include('auto-update-technician-status.php');
    
    // Get updated technician stats
    $stats_query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN t_status = 'Available' THEN 1 ELSE 0 END) as available,
                        SUM(CASE WHEN t_status = 'Booked' THEN 1 ELSE 0 END) as booked,
                        SUM(CASE WHEN t_status = 'Pending' THEN 1 ELSE 0 END) as pending
                    FROM tms_technician";
    
    $stats_result = $mysqli->query($stats_query);
    $stats = $stats_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'Technician statuses updated successfully',
        'stats' => $stats,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating technician statuses',
        'error' => $e->getMessage()
    ]);
}
?>
