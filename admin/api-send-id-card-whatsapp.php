<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = isset($_POST['phone']) ? preg_replace('/\D/', '', $_POST['phone']) : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    
    if(empty($phone) || strlen($phone) !== 10) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/id_cards/';
    if(!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = 'ID_Card_' . $phone . '_' . time() . '.png';
    $filepath = $upload_dir . $filename;
    
    // Handle base64 image data
    if(isset($_POST['image_data']) && !empty($_POST['image_data'])) {
        $image_data = $_POST['image_data'];
        
        // Remove data:image/png;base64, prefix if present
        if(strpos($image_data, 'data:image') !== false) {
            $image_data = explode(',', $image_data)[1];
        }
        
        // Decode base64 and save
        $decoded_image = base64_decode($image_data);
        
        if($decoded_image !== false && file_put_contents($filepath, $decoded_image)) {
            // Get full URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $file_url = $protocol . '://' . $host . '/uploads/id_cards/' . $filename;
            
            // Format phone number for WhatsApp (add country code)
            $whatsapp_number = '91' . $phone; // India country code
            
            // Create WhatsApp message with image
            $message = "Hi " . $name . "! 👋\n\n";
            $message .= "Welcome to Electrozot! ⚡\n\n";
            $message .= "Mohit Choudhary welcomes you to the Electrozot Team! 🎉\n\n";
            $message .= "📋 Your Technician ID Card:\n";
            $message .= $file_url . "\n\n";
            $message .= "Please download and save your ID card. Keep it with you during service visits.\n\n";
            $message .= "We're excited to have you on board! 💪\n\n";
            $message .= "Best regards,\n";
            $message .= "Mohit Choudhary\n";
            $message .= "Electrozot Management\n\n";
            $message .= "📞 Contact: 7559606925\n";
            $message .= "🌐 Website: www.electrozot.com";
            
            // WhatsApp Web URL with image
            $whatsapp_url = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($message);
            
            echo json_encode([
                'success' => true,
                'message' => 'ID Card saved successfully',
                'whatsapp_url' => $whatsapp_url,
                'file_url' => $file_url
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save ID card image']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No image data received']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
