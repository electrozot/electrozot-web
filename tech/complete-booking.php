<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Redirect if no booking ID provided
if($sb_id == 0){
    $_SESSION['error'] = "No booking ID provided.";
    header('Location: dashboard.php');
    exit();
}

// Ensure required columns exist
$columns_to_add = [
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completion_image VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_attachment VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_amount DECIMAL(10,2) DEFAULT NULL",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completed_at TIMESTAMP NULL DEFAULT NULL",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_not_done_reason TEXT DEFAULT NULL",
    "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_not_done_at TIMESTAMP NULL DEFAULT NULL"
];

foreach($columns_to_add as $sql) {
    try { $mysqli->query($sql); } catch(Exception $e) {}
}

// Get booking details with service price
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, s.s_name, s.s_category, s.s_price
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $sb_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    $_SESSION['error'] = "Booking not found or not assigned to you. (Booking ID: $sb_id, Technician ID: $t_id)";
    header('Location: dashboard.php');
    exit();
}

$booking = $result->fetch_object();

// Check if admin has set a fixed price
$admin_price_set = ($booking->s_price !== null && $booking->s_price > 0);
$display_price = $admin_price_set ? $booking->s_price : $booking->sb_total_price;

// Check if already completed or not done
if($booking->sb_status == 'Completed' || $booking->sb_status == 'Not Done'){
    $_SESSION['error'] = "This booking is already " . strtolower($booking->sb_status) . ".";
    header('Location: dashboard.php');
    exit();
}

$success = '';
$error = '';

// Debug mode - show info if debug parameter is present
$debug_mode = isset($_GET['debug']);

// Handle Mark as Done
if(isset($_POST['mark_done'])){
    $bill_amount = isset($_POST['bill_amount']) ? floatval($_POST['bill_amount']) : 0;
    
    // Validate inputs (skip validation if admin has set fixed price)
    if($bill_amount <= 0 && !$admin_price_set){
        $error = 'Please enter a valid bill amount greater than 0';
    }
    elseif(!isset($_FILES['service_image']) || $_FILES['service_image']['error'] == 4){
        $error = 'Please upload service completion image';
    }
    elseif(!isset($_FILES['bill_image']) || $_FILES['bill_image']['error'] == 4){
        $error = 'Please upload bill/receipt image';
    }
    else {
        // Check for upload errors
        if($_FILES['service_image']['error'] != 0){
            $error = 'Service image upload error (Code: ' . $_FILES['service_image']['error'] . ')';
        }
        elseif($_FILES['bill_image']['error'] != 0){
            $error = 'Bill image upload error (Code: ' . $_FILES['bill_image']['error'] . ')';
        }
        else {
            // Process uploads
            $upload_dir_service = "../uploads/service_images/";
            $upload_dir_bill = "../uploads/bill_images/";
            
            // Create directories if they don't exist
            if(!file_exists($upload_dir_service)) mkdir($upload_dir_service, 0777, true);
            if(!file_exists($upload_dir_bill)) mkdir($upload_dir_bill, 0777, true);
            
            // Generate unique filenames
            $service_ext = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
            $bill_ext = pathinfo($_FILES['bill_image']['name'], PATHINFO_EXTENSION);
            
            $service_filename = 'service_' . $sb_id . '_' . time() . '.' . $service_ext;
            $bill_filename = 'bill_' . $sb_id . '_' . time() . '.' . $bill_ext;
            
            $service_path = $upload_dir_service . $service_filename;
            $bill_path = $upload_dir_bill . $bill_filename;
            
            // Move uploaded files
            if(move_uploaded_file($_FILES['service_image']['tmp_name'], $service_path) && 
               move_uploaded_file($_FILES['bill_image']['tmp_name'], $bill_path)){
                
                // Update booking status to Completed
                $update_query = "UPDATE tms_service_booking 
                                SET sb_status = 'Completed',
                                    sb_completion_image = ?,
                                    sb_bill_attachment = ?,
                                    sb_bill_amount = ?,
                                    sb_completed_at = NOW(),
                                    sb_updated_at = NOW()
                                WHERE sb_id = ? AND sb_technician_id = ?";
                
                $update_stmt = $mysqli->prepare($update_query);
                $service_db_path = 'uploads/service_images/' . $service_filename;
                $bill_db_path = 'uploads/bill_images/' . $bill_filename;
                
                $update_stmt->bind_param('ssdii', $service_db_path, $bill_db_path, $bill_amount, $sb_id, $t_id);
                
                if($update_stmt->execute() && $update_stmt->affected_rows > 0){
                    // CRITICAL: Free up technician for next booking
                    // Update all status fields to ensure technician is fully available
                    $free_tech = "UPDATE tms_technician 
                                 SET t_status = 'Available', 
                                     t_is_available = 1, 
                                     t_current_booking_id = NULL 
                                 WHERE t_id = ?";
                    $free_stmt = $mysqli->prepare($free_tech);
                    $free_stmt->bind_param('i', $t_id);
                    $free_stmt->execute();
                    
                    $_SESSION['success'] = "Service completed successfully!";
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Failed to update booking status. Please try again.';
                }
            } else {
                $error = 'Failed to save uploaded files. Please try again.';
            }
        }
    }
}

// Handle Mark as Not Done
if(isset($_POST['mark_not_done'])){
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    
    if(empty($reason)){
        $error = 'Please provide a reason for not completing the service';
    } else {
        // Update booking status to Not Done and clear technician assignment
        $update_query = "UPDATE tms_service_booking 
                        SET sb_status = 'Not Done',
                            sb_not_done_reason = ?,
                            sb_not_done_at = NOW(),
                            sb_updated_at = NOW(),
                            sb_technician_id = NULL
                        WHERE sb_id = ? AND sb_technician_id = ?";
        
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('sii', $reason, $sb_id, $t_id);
        
        if($update_stmt->execute() && $update_stmt->affected_rows > 0){
            // CRITICAL: Free up technician for next booking (rejection case)
            // Update all status fields to ensure technician is fully available
            $free_tech = "UPDATE tms_technician 
                         SET t_status = 'Available', 
                             t_is_available = 1, 
                             t_current_booking_id = NULL 
                         WHERE t_id = ?";
            $free_stmt = $mysqli->prepare($free_tech);
            $free_stmt->bind_param('i', $t_id);
            $free_stmt->execute();
            
            // Get technician name for notifications
            $tech_query = "SELECT t_name FROM tms_technician WHERE t_id = ?";
            $tech_stmt = $mysqli->prepare($tech_query);
            $tech_stmt->bind_param('i', $t_id);
            $tech_stmt->execute();
            $tech_result = $tech_stmt->get_result();
            $tech_data = $tech_result->fetch_object();
            $tech_name = $tech_data ? $tech_data->t_name : 'Technician';
            
            // Create admin notification table if not exists
            $mysqli->query("CREATE TABLE IF NOT EXISTS tms_admin_notifications (
                an_id INT AUTO_INCREMENT PRIMARY KEY,
                an_type VARCHAR(50) NOT NULL,
                an_title VARCHAR(255) NOT NULL,
                an_message TEXT NOT NULL,
                an_booking_id INT,
                an_technician_id INT,
                an_is_read TINYINT(1) DEFAULT 0,
                an_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_read (an_is_read),
                INDEX idx_booking (an_booking_id)
            )");
            
            // Create admin notification
            $notif_title = "Service Not Done - Needs Reassignment";
            $notif_message = "$tech_name marked Booking #$sb_id as Not Done. Reason: $reason. Please reassign to another technician.";
            $notif_type = "SERVICE_NOT_DONE";
            
            $notif_stmt = $mysqli->prepare("INSERT INTO tms_admin_notifications (an_type, an_title, an_message, an_booking_id, an_technician_id) VALUES (?, ?, ?, ?, ?)");
            $notif_stmt->bind_param('sssii', $notif_type, $notif_title, $notif_message, $sb_id, $t_id);
            $notif_stmt->execute();
            
            // Create user notification table if not exists
            $mysqli->query("CREATE TABLE IF NOT EXISTS tms_user_notifications (
                un_id INT AUTO_INCREMENT PRIMARY KEY,
                un_user_id INT NOT NULL,
                un_booking_id INT,
                un_type VARCHAR(50) NOT NULL,
                un_title VARCHAR(255) NOT NULL,
                un_message TEXT NOT NULL,
                un_is_read TINYINT(1) DEFAULT 0,
                un_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (un_user_id),
                INDEX idx_read (un_is_read)
            )");
            
            // Create user notification
            if ($booking->sb_user_id) {
                $user_notif_title = "Service Status Update";
                $user_notif_message = "Your booking #$sb_id could not be completed. Don't worry, we'll assign another technician to help you soon!";
                $user_notif_type = "SERVICE_NOT_DONE";
                
                $user_notif_stmt = $mysqli->prepare("INSERT INTO tms_user_notifications (un_user_id, un_booking_id, un_type, un_title, un_message) VALUES (?, ?, ?, ?, ?)");
                $user_notif_stmt->bind_param('iisss', $booking->sb_user_id, $sb_id, $user_notif_type, $user_notif_title, $user_notif_message);
                $user_notif_stmt->execute();
            }
            
            $_SESSION['success'] = "Booking marked as not done. Admin has been notified for reassignment.";
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Failed to update booking. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Booking - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }
        
        .card h3 {
            color: #667eea;
            font-weight: 900;
            margin-bottom: 25px;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .booking-info {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 2px solid #e0e7ff;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e0e7ff;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .info-row label {
            font-weight: 700;
            color: #667eea;
            font-size: 0.9rem;
        }
        
        .info-row span {
            font-weight: 700;
            color: #1e293b;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .file-upload {
            border: 3px dashed #cbd5e1;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .file-upload i {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        
        .file-upload p {
            color: #64748b;
            font-weight: 600;
            margin: 0;
        }
        
        .file-upload input[type="file"] {
            display: none;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 15px;
            display: none;
        }
        
        .preview-image.show {
            display: block;
        }
        
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px;
            border-radius: 50px;
            font-weight: 900;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.5);
        }
        
        .btn-not-done {
            width: 100%;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 15px;
            border-radius: 50px;
            font-weight: 900;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        
        .btn-not-done:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.5);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .alert-danger {
            background: #fee;
            border: 2px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background: #efe;
            border: 2px solid #cfc;
            color: #3c3;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if($action == 'done'): ?>
        <!-- Mark as Done Form -->
        <div class="card">
            <h3><i class="fas fa-check-circle"></i> Mark as Done</h3>
            
            <div class="booking-info">
                <div class="info-row">
                    <label>Booking ID:</label>
                    <span>#<?php echo $sb_id; ?></span>
                </div>
                <div class="info-row">
                    <label>Customer:</label>
                    <span><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></span>
                </div>
                <div class="info-row">
                    <label>Service:</label>
                    <span><?php echo htmlspecialchars($booking->s_name); ?></span>
                </div>
                <div class="info-row">
                    <label>Category:</label>
                    <span><?php echo htmlspecialchars($booking->s_category); ?></span>
                </div>
            </div>
            
            <form method="POST" enctype="multipart/form-data" id="doneForm">
                <!-- Service Image -->
                <div class="form-group">
                    <label><i class="fas fa-camera"></i> Service Completion Image *</label>
                    <div class="file-upload" onclick="document.getElementById('service_image').click()">
                        <i class="fas fa-image"></i>
                        <p>Click to upload service photo</p>
                        <input type="file" name="service_image" id="service_image" accept="image/*" required onchange="previewImage(this, 'service_preview')">
                    </div>
                    <img id="service_preview" class="preview-image" alt="Service Preview">
                </div>
                
                <!-- Bill Image -->
                <div class="form-group">
                    <label><i class="fas fa-file-invoice"></i> Bill/Receipt Image *</label>
                    <div class="file-upload" onclick="document.getElementById('bill_image').click()">
                        <i class="fas fa-receipt"></i>
                        <p>Click to upload bill photo</p>
                        <input type="file" name="bill_image" id="bill_image" accept="image/*" required onchange="previewImage(this, 'bill_preview')">
                    </div>
                    <img id="bill_preview" class="preview-image" alt="Bill Preview">
                </div>
                
                <!-- Bill Amount -->
                <?php if(!$admin_price_set): ?>
                <div class="form-group">
                    <label><i class="fas fa-rupee-sign"></i> Bill Amount (₹) *</label>
                    <input type="number" name="bill_amount" class="form-control" placeholder="Enter bill amount based on parts and work" step="0.01" min="0.01" required>
                </div>
                <?php else: ?>
                <input type="hidden" name="bill_amount" value="<?php echo $display_price; ?>">
                <div class="alert alert-success" style="border-left: 4px solid #28a745; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-lock" style="font-size: 1.5rem; color: #28a745;"></i>
                        <div>
                            <strong style="color: #28a745;">Fixed Price: ₹<?php echo number_format($display_price, 2); ?></strong>
                            <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 0.9rem;">This price is set by admin and cannot be changed.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <button type="submit" name="mark_done" class="btn-submit">
                    <i class="fas fa-check-circle"></i> Complete Service
                </button>
            </form>
        </div>
        
        <?php elseif($action == 'not-done'): ?>
        <!-- Mark as Not Done Form -->
        <div class="card">
            <h3><i class="fas fa-times-circle"></i> Mark as Not Done</h3>
            
            <div class="booking-info">
                <div class="info-row">
                    <label>Booking ID:</label>
                    <span>#<?php echo $sb_id; ?></span>
                </div>
                <div class="info-row">
                    <label>Customer:</label>
                    <span><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></span>
                </div>
                <div class="info-row">
                    <label>Service:</label>
                    <span><?php echo htmlspecialchars($booking->s_name); ?></span>
                </div>
            </div>
            
            <form method="POST" id="notDoneForm">
                <div class="form-group">
                    <label><i class="fas fa-comment"></i> Reason for Not Completing *</label>
                    <textarea name="reason" class="form-control" placeholder="Please explain why the service could not be completed..." required></textarea>
                </div>
                
                <button type="submit" name="mark_not_done" class="btn-not-done">
                    <i class="fas fa-times-circle"></i> Mark as Not Done
                </button>
            </form>
        </div>
        
        <?php else: ?>
        <!-- Action Selection -->
        <div class="card">
            <h3><i class="fas fa-clipboard-check"></i> Complete Booking</h3>
            
            <div class="booking-info">
                <div class="info-row">
                    <label>Booking ID:</label>
                    <span>#<?php echo $sb_id; ?></span>
                </div>
                <div class="info-row">
                    <label>Customer:</label>
                    <span><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></span>
                </div>
                <div class="info-row">
                    <label>Service:</label>
                    <span><?php echo htmlspecialchars($booking->s_name); ?></span>
                </div>
            </div>
            
            <p style="text-align: center; color: #64748b; font-weight: 600; margin-bottom: 25px;">
                Choose an action to complete this booking:
            </p>
            
            <div style="display: flex; gap: 15px; flex-direction: column;">
                <a href="?id=<?php echo $sb_id; ?>&action=done" class="btn-submit" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-check-circle"></i> Mark as Done
                </a>
                <a href="?id=<?php echo $sb_id; ?>&action=not-done" class="btn-not-done" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-times-circle"></i> Mark as Not Done
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
