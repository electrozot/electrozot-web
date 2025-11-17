<?php
/**
 * API: Get available technicians for assignment
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

$technicians = $bookingSystem->getAvailableTechnicians($service_category);

echo json_encode([
    'success' => true,
    'technicians' => $technicians
]);
?>
