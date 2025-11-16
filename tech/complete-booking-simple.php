<?php
/**
 * Simple Complete Booking Page - NO CAMERA, NO FANCY FEATURES
 * Use this if the main complete-booking.php is not working
 */
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $sb_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die('Booking not found or not assigned to you');
}

$booking = $result->fetch_object();

// Handle form submission
if(isset($_POST['mark_done'])){
    $error = '';
    $success = '';
    
    // Validate
    if(!isset($_FILES['service_image']) || $_FILES['service_image']['error'] != 0){
        $error = 'Please upload service image';
    }
    elseif(!isset($_FILES['bill_image']) || $_FILES['bill_image']['error'] != 0){
        $error = 'Please upload bill image';
    }
    elseif(!isset($_POST['amount_charged']) || floatval($_POST['amount_charged']) <= 0){
        $error = 'Please enter valid amount';
    }
    else {
        // Create upload directories
        $service_dir = "../uploads/service_images/";
        $bill_dir = "../uploads/bill_images/";
        
        if(!file_exists($service_dir)) mkdir($service_dir, 0777, true);
        if(!file_exists($bill_dir)) mkdir($bill_dir, 0777, true);
        
        // Upload service image
        $service_image = time() . '_service_' . basename($_FILES['service_image']['name']);
        $service_path = $service_dir . $service_image;
        
        // Upload bill image
        $bill_image = time() . '_bill_' . basename($_FILES['bill_image']['name']);
        $bill_path = $bill_dir . $bill_image;
        
        if(move_uploaded_file($_FILES['service_image']['tmp_name'], $service_path) &&
           move_uploaded_file($_FILES['bill_image']['tmp_name'], $bill_path)) {
            
            $amount = floatval($_POST['amount_charged']);
            
            // Update booking
            $update_query = "UPDATE tms_service_booking 
                           SET sb_status='Completed', 
                               sb_service_image=?, 
                               sb_bill_image=?, 
                               sb_amount_charged=?,
                               sb_completed_at=NOW()
                           WHERE sb_id=? AND sb_technician_id=?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param('ssdii', $service_image, $bill_image, $amount, $sb_id, $t_id);
            
            if($update_stmt->execute() && $update_stmt->affected_rows > 0) {
                // Free up technician
                $free_query = "UPDATE tms_technician 
                              SET t_status = 'Available', 
                                  t_is_available = 1, 
                                  t_current_booking_id = NULL 
                              WHERE t_id = ?";
                $free_stmt = $mysqli->prepare($free_query);
                $free_stmt->bind_param('i', $t_id);
                $free_stmt->execute();
                
                $success = 'Booking completed successfully!';
                header('Location: dashboard.php?success=completed');
                exit();
            } else {
                $error = 'Failed to update booking: ' . $mysqli->error;
            }
        } else {
            $error = 'Failed to upload images';
        }
    }
}

// Handle rejection
if(isset($_POST['mark_not_done'])){
    $reason = trim($_POST['reason']);
    
    if(empty($reason)){
        $error = 'Please enter reason';
    } else {
        $update_query = "UPDATE tms_service_booking 
                       SET sb_status='Not Done', 
                           sb_not_done_reason=?,
                           sb_not_done_at=NOW()
                       WHERE sb_id=? AND sb_technician_id=?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('sii', $reason, $sb_id, $t_id);
        
        if($update_stmt->execute() && $update_stmt->affected_rows > 0) {
            // Free up technician
            $free_query = "UPDATE tms_technician 
                          SET t_status = 'Available', 
                              t_is_available = 1, 
                              t_current_booking_id = NULL 
                          WHERE t_id = ?";
            $free_stmt = $mysqli->prepare($free_query);
            $free_stmt->bind_param('i', $t_id);
            $free_stmt->execute();
            
            header('Location: dashboard.php?success=not_done');
            exit();
        } else {
            $error = 'Failed to update booking';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Booking - Simple Version</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: Arial, sans-serif;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h2 {
            color: #667eea;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        input[type="file"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        input[type="file"] {
            padding: 10px;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #fee;
            border: 2px solid #f88;
            color: #c33;
        }
        .alert-success {
            background: #efe;
            border: 2px solid #8f8;
            color: #3c3;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .preview {
            max-width: 200px;
            margin-top: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-clipboard-check"></i> Complete Booking (Simple Version)</h2>
            
            <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <div class="info-box">
                <p><strong>Booking ID:</strong> #<?php echo $sb_id; ?></p>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></p>
                <p><strong>Service:</strong> <?php echo htmlspecialchars($booking->s_name); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking->u_phone); ?></p>
            </div>
            
            <h3>Mark as Done</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="fas fa-camera"></i> Service Image *</label>
                    <input type="file" name="service_image" accept="image/*" required>
                    <small style="color: #666;">Take a photo of the completed work</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-file-invoice"></i> Bill Image *</label>
                    <input type="file" name="bill_image" accept="image/*" required>
                    <small style="color: #666;">Take a photo of the bill/invoice</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-rupee-sign"></i> Amount Charged *</label>
                    <input type="number" name="amount_charged" step="0.01" min="0" placeholder="Enter amount" required>
                </div>
                
                <button type="submit" name="mark_done" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Complete Service
                </button>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h3>Or Mark as Not Done</h3>
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-comment-alt"></i> Reason *</label>
                    <textarea name="reason" placeholder="Why couldn't you complete the service?" required></textarea>
                </div>
                
                <button type="submit" name="mark_not_done" class="btn btn-danger">
                    <i class="fas fa-times-circle"></i> Mark as Not Done
                </button>
            </form>
            
            <a href="dashboard.php" class="btn btn-secondary" style="display: block; text-align: center; text-decoration: none; margin-top: 20px;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="card" style="background: #fff3cd; border: 2px solid #ffc107;">
            <p style="margin: 0;"><strong>Note:</strong> This is the simplified version without camera features. If you prefer the camera version, use the regular complete booking page.</p>
        </div>
    </div>
</body>
</html>
