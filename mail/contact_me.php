<?php
// Include database connection
require_once('../admin/vendor/inc/config.php');

// Create table if not exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_contact_messages (
    cm_id INT AUTO_INCREMENT PRIMARY KEY,
    cm_name VARCHAR(200) NOT NULL,
    cm_email VARCHAR(200) NOT NULL,
    cm_phone VARCHAR(20) NOT NULL,
    cm_message TEXT NOT NULL,
    cm_status VARCHAR(20) DEFAULT 'Unread',
    cm_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Check for empty fields
if(empty($_POST['name']) ||
   empty($_POST['email']) ||
   empty($_POST['phone']) ||
   empty($_POST['message']) ||
   !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
{
    echo "No arguments Provided!";
    exit;
}

$name = strip_tags(htmlspecialchars($_POST['name']));
$email_address = strip_tags(htmlspecialchars($_POST['email']));
$phone = strip_tags(htmlspecialchars($_POST['phone']));
$message = strip_tags(htmlspecialchars($_POST['message']));

// Save to database
$stmt = $mysqli->prepare("INSERT INTO tms_contact_messages (cm_name, cm_email, cm_phone, cm_message) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $name, $email_address, $phone, $message);

if($stmt->execute()) {
    echo "success";
} else {
    echo "Database error: " . $mysqli->error;
}

$stmt->close();
$mysqli->close();
?>
 