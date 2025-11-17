<?php
/**
 * API: User rates technician after service completion
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['u_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once('../admin/vendor/inc/config.php');
require_once('../admin/BookingSystem.php');

$bookingSystem = new BookingSystem($conn);

$booking_id = $_POST['booking_id'] ?? null;
$technician_id = $_POST['technician_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$review = $_POST['review'] ?? '';
$punctuality = $_POST['punctuality'] ?? null;
$professionalism = $_POST['professionalism'] ?? null;
$quality = $_POST['quality'] ?? null;
$user_id = $_SESSION['u_id'];

if (!$booking_id || !$technician_id || !$rating) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate rating (1-5)
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit;
}

$result = $bookingSystem->addTechnicianRating(
    $booking_id, 
    $technician_id, 
    $user_id, 
    $rating, 
    $review,
    $punctuality,
    $professionalism,
    $quality
);

echo json_encode($result);
?>
