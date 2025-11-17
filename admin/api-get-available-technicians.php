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
require_once('BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

$service_category = $_GET['category'] ?? null;
$service_gadget_type = $_GET['gadget_type'] ?? null;

$technicians = $bookingSystem->getAvailableTechnicians($service_category, $service_gadget_type);

echo json_encode([
    'success' => true,
    'technicians' => $technicians,
    'filters' => [
        'category' => $service_category,
        'gadget_type' => $service_gadget_type
    ]
]);
?>
