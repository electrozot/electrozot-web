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
    
    // Generate unique filenames
    $timestamp = time();
    $image_filename = 'ID_Card_' . $phone . '_' . $timestamp . '.png';
    $pdf_filename = 'ID_Card_' . $phone . '_' . $timestamp . '.pdf';
    $image_filepath = $upload_dir . $image_filename;
    $pdf_filepath = $upload_dir . $pdf_filename;
    
    $image_saved = false;
    $pdf_saved = false;
    
    // Handle base64 image data
    if(isset($_POST['image_data']) && !empty($_POST['image_data'])) {
        $image_data = $_POST['image_data'];
        
        // Remove data:image/png;base64, prefix if present
        if(strpos($image_data, 'data:image') !== false) {
            $image_data = explode(',', $image_data)[1];
        }
        
        // Decode base64 and save image
        $decoded_image = base64_decode($image_data);
        
        if($decoded_image !== false && file_put_contents($image_filepath, $decoded_image)) {
            $image_saved = true;
        }
    }
    
    // Handle base64 PDF data
    if(isset($_POST['pdf_data']) && !empty($_POST['pdf_data'])) {
        $pdf_data = $_POST['pdf_data'];
        
        // Remove data:application/pdf;base64, prefix if present
        if(strpos($pdf_data, 'data:application') !== false) {
            $pdf_data = explode(',', $pdf_data)[1];
        }
        
        // Decode base64 and save PDF
        $decoded_pdf = base64_decode($pdf_data);
        
        if($decoded_pdf !== false && file_put_contents($pdf_filepath, $decoded_pdf)) {
            $pdf_saved = true;
        }
    }
    
    if($image_saved || $pdf_saved) {
        // Get full URLs
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $image_url = $protocol . '://' . $host . '/uploads/id_cards/' . $image_filename;
        $pdf_url = $protocol . '://' . $host . '/uploads/id_cards/' . $pdf_filename;
        
        // AUTO-SAVE: Store ID card record in database
        $admin_id = $_SESSION['a_id'];
        
        // Get technician ID from phone number
        $tech_query = "SELECT t_id FROM tms_technician WHERE t_phone = ?";
        $tech_stmt = $mysqli->prepare($tech_query);
        $tech_stmt->bind_param('s', $phone);
        $tech_stmt->execute();
        $tech_result = $tech_stmt->get_result();
        $technician_id = 0;
        
        if($tech_result->num_rows > 0) {
            $tech_row = $tech_result->fetch_assoc();
            $technician_id = $tech_row['t_id'];
            
            // Save to generated ID cards table
            $save_query = "INSERT INTO tms_generated_id_cards 
                          (technician_id, technician_name, technician_phone, image_path, pdf_path, generated_by_admin_id) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $save_stmt = $mysqli->prepare($save_query);
            $image_path = $image_saved ? 'uploads/id_cards/' . $image_filename : NULL;
            $pdf_path = $pdf_saved ? 'uploads/id_cards/' . $pdf_filename : NULL;
            $save_stmt->bind_param('issssi', $technician_id, $name, $phone, $image_path, $pdf_path, $admin_id);
            $save_stmt->execute();
            
            // Update technician table with latest ID card info
            $update_tech = "UPDATE tms_technician 
                           SET t_id_card_generated = 1, 
                               t_id_card_path = ?, 
                               t_id_card_generated_at = NOW() 
                           WHERE t_id = ?";
            $update_stmt = $mysqli->prepare($update_tech);
            $update_stmt->bind_param('si', $pdf_path, $technician_id);
            $update_stmt->execute();
        }
        
        // Format phone number for WhatsApp (add country code)
        $whatsapp_number = '91' . $phone; // India country code
        
        // Create WhatsApp message with both links
        $message = "Hi " . $name . "! 👋\n\n";
        $message .= "Welcome to Electrozot! ⚡\n\n";
        $message .= "Mohit Choudhary welcomes you to the Electrozot Team! 🎉\n\n";
        $message .= "📋 Your Technician ID Card is attached with this message.\n\n";
        
        if($image_saved) {
            $message .= "🖼️ Image: " . $image_url . "\n\n";
        }
        
        if($pdf_saved) {
            $message .= "📄 PDF: " . $pdf_url . "\n\n";
        }
        
        $message .= "Please download and save your ID card. Keep it with you during service visits.\n\n";
        $message .= "We're excited to have you on board! 💪\n\n";
        $message .= "Best regards,\n";
        $message .= "Mohit Choudhary\n";
        $message .= "Electrozot Management\n\n";
        $message .= "📞 Contact: 7559606925\n";
        $message .= "🌐 Website: www.electrozot.in";
        
        // WhatsApp Web URL
        $whatsapp_url = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($message);
        
        // Try to send via WhatsApp Business API if configured
        $api_sent = false;
        $api_message = '';
        
        // Check if WhatsApp Business API credentials are configured
        $whatsapp_api_token = ''; // Add your WhatsApp Business API token here
        $whatsapp_phone_id = ''; // Add your WhatsApp Business Phone Number ID here
        
        if(!empty($whatsapp_api_token) && !empty($whatsapp_phone_id) && $pdf_saved) {
            // Send PDF via WhatsApp Business API
            $api_url = "https://graph.facebook.com/v18.0/{$whatsapp_phone_id}/messages";
            
            $api_data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $whatsapp_number,
                'type' => 'document',
                'document' => [
                    'link' => $pdf_url,
                    'caption' => "Hi {$name}! Welcome to Electrozot! Here's your Technician ID Card. - Mohit Choudhary",
                    'filename' => $pdf_filename
                ]
            ];
            
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $whatsapp_api_token,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if($http_code == 200) {
                $api_sent = true;
                $api_message = 'PDF sent via WhatsApp Business API';
            } else {
                $api_message = 'WhatsApp API failed, using web link instead';
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'ID Card files saved successfully',
            'whatsapp_url' => $whatsapp_url,
            'image_url' => $image_url,
            'pdf_url' => $pdf_url,
            'api_sent' => $api_sent,
            'api_message' => $api_message
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save ID card files']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
