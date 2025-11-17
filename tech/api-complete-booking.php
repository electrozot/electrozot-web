<?php
/**
 * API: Technician completes booking
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['t_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');
require_once('../admin/BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

$booking_id = $_POST['booking_id'] ?? null;
$notes = $_POST['notes'] ?? '';
$technician_id = $_SESSION['t_id'];

// Handle image upload if provided
$image = '';
if (isset($_FILES['completion_image']) && $_FILES['completion_image']['error'] == 0) {
    $upload_dir = '../uploads/completion_images/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES['completion_image']['name'], PATHINFO_EXTENSION);
    $image = 'completion_' . $booking_id . '_' . time() . '.' . $file_extension;
    move_uploaded_file($_FILES['completion_image']['tmp_name'], $upload_dir . $image);
}

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

$result = $bookingSystem->completeBooking($booking_id, $technician_id, $notes, $image);
echo json_encode($result);
?>
