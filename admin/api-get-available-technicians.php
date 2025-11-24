<?php
/**
 * API: Get available technicians for assignment
 * Enhanced to match both category and gadget type
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['a_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('vendor/inc/config.php');
require_once('vendor/inc/booking-limit-helper.php');

$service_category = $_GET['category'] ?? null;

$technicians = getAvailableTechniciansWithCapacity($mysqli, $service_category);

echo json_encode([
    'success' => true,
    'technicians' => $technicians,
    'filters' => [
        'category' => $service_category
    ]
]);
?>
