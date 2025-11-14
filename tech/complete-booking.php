<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];

// Ensure required columns exist
try {
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_image VARCHAR(200) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_image VARCHAR(200) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completion_date DATETIME DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejection_reason TEXT DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_charged_price DECIMAL(10,2) DEFAULT NULL");
} catch(Exception $e) {}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success = '';
$error = '';

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name 
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if(!$booking) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if(isset($_POST['mark_completed'])) {
    $bill_image = '';
    $service_image = '';
    $charged_price = isset($_POST['charged_price']) ? floatval($_POST['charged_price']) : 0;
    
    // Validate charged price
    if($charged_price <= 0) {
        $error = "Please enter a valid charged price.";
    } else {
        // Upload bill image
        if(isset($_FILES['bill_image']) && $_FILES['bill_image']['error'] == 0) {
            $bill_ext = pathinfo($_FILES['bill_image']['name'], PATHINFO_EXTENSION);
            $bill_image = 'bill_' . $booking_id . '_' . time() . '.' . $bill_ext;
            move_uploaded_file($_FILES['bill_image']['tmp_name'], '../vendor/img/' . $bill_image);
        }
        
        // Upload service image
        if(isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $service_ext = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
            $service_image = 'service_' . $booking_id . '_' . time() . '.' . $service_ext;
            move_uploaded_file($_FILES['service_image']['tmp_name'], '../vendor/img/' . $service_image);
        }
        
        if(!empty($bill_image) && !empty($service_image)) {
            $update_query = "UPDATE tms_service_booking 
                            SET sb_status = 'Completed', 
                                sb_bill_image = ?, 
                                sb_service_image = ?,
                                sb_charged_price = ?,
                                sb_completion_date = NOW()
                            WHERE sb_id = ? AND sb_technician_id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param('ssdii', $bill_image, $service_image, $charged_price, $booking_id, $t_id);
            
            if($update_stmt->execute()) {
                // Set technician status back to Available
                $tech_update = "UPDATE tms_technician SET t_status = 'Available' WHERE t_id = ?";
                $tech_stmt = $mysqli->prepare($tech_update);
                $tech_stmt->bind_param('i', $t_id);
                $tech_stmt->execute();
                
                $success = "Booking marked as completed successfully with charged price ₹" . number_format($charged_price, 2);
                // Refresh booking data
                $stmt->execute();
                $result = $stmt->get_result();
                $booking = $result->fetch_object();
            } else {
                $error = "Failed to update booking.";
            }
        } else {
            $error = "Please upload both bill and service images.";
        }
    }
}

// Handle rejection
if(isset($_POST['mark_rejected'])) {
    $rejection_reason = $_POST['rejection_reason'];
    
    if(!empty($rejection_reason)) {
        $update_query = "UPDATE tms_service_booking 
                        SET sb_status = 'Rejected', 
                            sb_rejection_reason = ?,
                            sb_technician_id = NULL
                        WHERE sb_id = ? AND sb_technician_id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('sii', $rejection_reason, $booking_id, $t_id);
        
        if($update_stmt->execute()) {
            // Set technician status back to Available
            $tech_update = "UPDATE tms_technician SET t_status = 'Available' WHERE t_id = ?";
            $tech_stmt = $mysqli->prepare($tech_update);
            $tech_stmt->bind_param('i', $t_id);
            $tech_stmt->execute();
            
            header('Location: dashboard.php?msg=rejected');
            exit();
        } else {
            $error = "Failed to reject booking.";
        }
    } else {
        $error = "Please provide a rejection reason.";
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
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: #ff4757;
        }
        
        .container-custom {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .card-custom {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-label {
            font-weight: 700;
            color: #666;
            width: 150px;
        }
        
        .info-value {
            color: #333;
            flex: 1;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-complete {
            flex: 1;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-complete:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-reject {
            flex: 1;
            padding: 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-reject:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .upload-section {
            margin: 20px 0;
        }
        
        .upload-box {
            border: 3px dashed #e0e0e0;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .upload-box:hover {
            border-color: #ff4757;
            background: #fff5f7;
        }
        
        .upload-box input[type="file"] {
            display: none;
        }
        
        .upload-label {
            cursor: pointer;
            display: block;
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #ff4757;
            margin-bottom: 10px;
        }
        
        .preview-image {
            max-width: 200px;
            margin-top: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .alert-custom {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #ff4757;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            background: #5a6268;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-bolt"></i> Electrozot
        </div>
    </div>

    <div class="container-custom">
        <a href="dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <?php if($success): ?>
            <div class="alert-custom alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert-custom alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card-custom">
            <div class="card-title">
                <i class="fas fa-clipboard-check"></i> Booking Details
            </div>
            
            <div class="info-row">
                <div class="info-label">Booking ID:</div>
                <div class="info-value"><strong>#<?php echo $booking->sb_id; ?></strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Customer Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">
                    <a href="tel:<?php echo $booking->u_phone; ?>" style="color: #28a745; font-weight: 700;">
                        <i class="fas fa-phone"></i> <?php echo $booking->u_phone; ?>
                    </a>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->u_addr); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Service:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->s_name); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Date & Time:</div>
                <div class="info-value">
                    <?php echo date('M d, Y - h:i A', strtotime($booking->sb_booking_date . ' ' . $booking->sb_booking_time)); ?>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <strong style="color: <?php echo $booking->sb_status == 'Completed' ? '#28a745' : '#ffa502'; ?>">
                        <?php echo $booking->sb_status; ?>
                    </strong>
                </div>
            </div>
        </div>

        <?php if($booking->sb_status != 'Completed' && $booking->sb_status != 'Rejected'): ?>
            <div class="card-custom">
                <div class="card-title">
                    <i class="fas fa-check-double"></i> Mark as Completed
                </div>
                
                <form method="POST" enctype="multipart/form-data" id="completeForm">
                    <div class="upload-section">
                        <h5 style="margin-bottom: 15px; color: #333;">Upload Bill Image</h5>
                        <div class="upload-box">
                            <label for="bill_image" class="upload-label">
                                <div class="upload-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <h5>Click to Upload Bill Image</h5>
                                <p style="color: #999;">JPG, PNG, PDF (Max 5MB)</p>
                            </label>
                            <input type="file" id="bill_image" name="bill_image" accept="image/*,.pdf" required onchange="previewImage(this, 'billPreview')">
                            <img id="billPreview" class="preview-image" style="display: none;">
                        </div>
                    </div>

                    <div class="upload-section">
                        <h5 style="margin-bottom: 15px; color: #333;">Upload Service Image</h5>
                        <div class="upload-box">
                            <label for="service_image" class="upload-label">
                                <div class="upload-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <h5>Click to Upload Service Image</h5>
                                <p style="color: #999;">JPG, PNG (Max 5MB)</p>
                            </label>
                            <input type="file" id="service_image" name="service_image" accept="image/*" required onchange="previewImage(this, 'servicePreview')">
                            <img id="servicePreview" class="preview-image" style="display: none;">
                        </div>
                    </div>

                    <div class="upload-section">
                        <h5 style="margin-bottom: 15px; color: #333;">
                            <i class="fas fa-rupee-sign"></i> Enter Charged Price
                        </h5>
                        <div class="form-group">
                            <label class="form-label">
                                Actual Price Charged to Customer <span style="color: #dc3545;">*</span>
                            </label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #666;">₹</span>
                                <input type="number" 
                                       name="charged_price" 
                                       class="form-control" 
                                       placeholder="Enter amount charged" 
                                       step="0.01" 
                                       min="0" 
                                       required
                                       style="padding-left: 35px; font-size: 1.2rem; font-weight: 700;">
                            </div>
                            <small style="color: #666; display: block; margin-top: 8px;">
                                <i class="fas fa-info-circle"></i> Enter the final amount you charged to the customer
                            </small>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" name="mark_completed" class="btn-complete">
                            <i class="fas fa-check-circle"></i> Mark as Completed
                        </button>
                        <button type="button" class="btn-reject" onclick="openRejectModal()">
                            <i class="fas fa-times-circle"></i> Not Completed
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif($booking->sb_status == 'Completed'): ?>
            <div class="card-custom">
                <div class="card-title">
                    <i class="fas fa-rupee-sign"></i> Charged Price
                </div>
                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 10px; margin-bottom: 20px;">
                    <h2 style="color: white; font-size: 3rem; font-weight: 900; margin: 0;">
                        ₹<?php echo isset($booking->sb_charged_price) ? number_format($booking->sb_charged_price, 2) : '0.00'; ?>
                    </h2>
                    <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-weight: 600;">Amount Charged to Customer</p>
                </div>
            </div>
            
            <div class="card-custom">
                <div class="card-title">
                    <i class="fas fa-images"></i> Uploaded Images
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Bill Image</h5>
                        <?php if(!empty($booking->sb_bill_image)): ?>
                            <img src="../vendor/img/<?php echo $booking->sb_bill_image; ?>" style="max-width: 100%; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5>Service Image</h5>
                        <?php if(!empty($booking->sb_service_image)): ?>
                            <img src="../vendor/img/<?php echo $booking->sb_service_image; ?>" style="max-width: 100%; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i> Reject Booking
            </div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Reason for Rejection:</label>
                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Please provide a reason..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="mark_rejected" class="btn-reject" style="flex: 1;">
                        <i class="fas fa-times-circle"></i> Confirm Rejection
                    </button>
                    <button type="button" onclick="closeRejectModal()" style="flex: 1; background: #6c757d;" class="btn-reject">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openRejectModal() {
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
