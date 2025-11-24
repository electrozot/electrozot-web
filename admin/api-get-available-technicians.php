<?php
/**
 * API: Get available technicians for assignment
 * Enhanced to match by specific service skills
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');
require_once('vendor/inc/booking-limit-helper.php');

$service_id = $_GET['service_id'] ?? null;
$service_category = $_GET['category'] ?? null;

// If service_id is provided, match by specific service skills
if ($service_id) {
    $technicians = getAvailableTechniciansForService($mysqli, $service_id);
    $match_type = 'service_skill';
} else {
    // Otherwise, match by category only
    $technicians = getAvailableTechniciansWithCapacity($mysqli, $service_category);
    $match_type = 'category';
}

echo json_encode([
    'success' => true,
    'technicians' => $technicians,
    'match_type' => $match_type,
    'filters' => [
        'service_id' => $service_id,
        'category' => $service_category
    ]
]);
?>
