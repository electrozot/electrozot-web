<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Complete Service";

$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ensure price tracking columns exist
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_price_set_by_tech TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_tech_decided_price DECIMAL(10,2) DEFAULT NULL");
$mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_final_price DECIMAL(10,2) DEFAULT NULL");

// Get booking details with service price
$query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name, s.s_price
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $sb_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header('Location: new-bookings.php');
    exit();
}

$booking = $result->fetch_object();

// Determine the price to use
// Admin price is set ONLY if s_price exists AND is greater than 0
$admin_price_set = (!empty($booking->s_price) && $booking->s_price > 0);
$display_price = $admin_price_set ? $booking->s_price : ($booking->sb_total_price > 0 ? $booking->sb_total_price : 0);

// Handle form submission
if(isset($_POST['complete_service'])){
    $final_price = $_POST['final_price'];
    $completion_notes = $_POST['completion_notes'];
    
    // Handle service image upload
    $service_img = '';
    if(isset($_FILES['service_img']) && $_FILES['service_img']['error'] == 0){
        $target_dir = "../vendor/img/completions/";
        $file_extension = pathinfo($_FILES["service_img"]["name"], PATHINFO_EXTENSION);
        $service_img = "sb" . $sb_id . "_service_" . time() . "." . $file_extension;
        $target_file = $target_dir . $service_img;
        move_uploaded_file($_FILES["service_img"]["tmp_name"], $target_file);
    }
    
    // Handle bill image upload
    $bill_img = '';
    if(isset($_FILES['bill_img']) && $_FILES['bill_img']['error'] == 0){
        $target_dir = "../vendor/img/bills/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_extension = pathinfo($_FILES["bill_img"]["name"], PATHINFO_EXTENSION);
        $bill_img = "sb" . $sb_id . "_bill_" . time() . "." . $file_extension;
        $target_file = $target_dir . $bill_img;
        move_uploaded_file($_FILES["bill_img"]["tmp_name"], $target_file);
    }
    
    // Determine if technician set the price (when admin didn't set one)
    $price_set_by_tech = 0;
    $tech_decided_price = null;
    
    if(!$admin_price_set) {
        // No admin price, so technician is setting the price
        $price_set_by_tech = 1;
        $tech_decided_price = $final_price;
    } elseif($final_price != $display_price) {
        // Admin price exists but technician changed it (shouldn't happen with locked field, but just in case)
        $price_set_by_tech = 1;
        $tech_decided_price = $final_price;
    }
    
    // Update booking with price tracking
    $update_query = "UPDATE tms_service_booking 
                     SET sb_status='Completed', 
                         sb_completion_img=?, 
                         sb_bill_img=?, 
                         sb_final_price=?,
                         sb_tech_decided_price=?,
                         sb_price_set_by_tech=?,
                         sb_completion_notes=?,
                         sb_completed_date=NOW()
                     WHERE sb_id=? AND sb_technician_id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('ssddiisii', $service_img, $bill_img, $final_price, $tech_decided_price, $price_set_by_tech, $completion_notes, $sb_id, $t_id);
    
    if($update_stmt->execute()){
        // Set technician status back to Available
        $tech_update = "UPDATE tms_technician SET t_status = 'Available' WHERE t_id = ?";
        $tech_stmt = $mysqli->prepare($tech_update);
        $tech_stmt->bind_param('i', $t_id);
        $tech_stmt->execute();
        
        $_SESSION['success_msg'] = "Service marked as completed successfully!";
        header('Location: completed-bookings.php');
        exit();
    } else {
        $error = "Failed to complete service. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <div class="page-header" style="background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%); color: white; border-left: none;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 style="color: white;">
                        <i class="fas fa-check-circle"></i>
                        Complete Service
                    </h2>
                    <p style="color: rgba(255,255,255,0.95);">Upload service completion details</p>
                </div>
                <a href="new-bookings.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; border-radius: 50px; padding: 10px 25px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Booking Info -->
            <div class="col-md-4 mb-4">
                <div class="card-custom">
                    <h5 style="font-size: 1.2rem; font-weight: 700; color: #2d3748; margin-bottom: 20px; border-bottom: 3px solid #38ef7d; padding-bottom: 15px;">
                        <i class="fas fa-info-circle" style="color: #38ef7d;"></i>
                        Booking Information
                    </h5>
                    
                    <div class="info-item mb-3">
                        <label>Booking ID</label>
                        <p>#<?php echo $sb_id; ?></p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label>Customer</label>
                        <p><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label>Service</label>
                        <p><?php echo htmlspecialchars($booking->s_name); ?></p>
                    </div>
                    
                    <div class="info-item">
                        <label>Service Price</label>
                        <?php if($admin_price_set): ?>
                        <p style="font-size: 1.5rem; color: #28a745; font-weight: 700;">₹<?php echo number_format($display_price, 2); ?></p>
                        <small class="badge badge-success"><i class="fas fa-lock"></i> Fixed by Admin</small>
                        <small style="display: block; margin-top: 5px; color: #28a745; font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> This price cannot be changed
                        </small>
                        <?php else: ?>
                        <p style="font-size: 1.5rem; color: #ffc107; font-weight: 700;">₹<?php echo number_format($display_price, 2); ?></p>
                        <small class="badge badge-warning"><i class="fas fa-edit"></i> No Fixed Price</small>
                        <small style="display: block; margin-top: 5px; color: #856404; font-size: 0.85rem;">
                            <i class="fas fa-exclamation-triangle"></i> You must set the final price below
                        </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Completion Form -->
            <div class="col-md-8 mb-4">
                <div class="card-custom">
                    <h5 style="font-size: 1.2rem; font-weight: 700; color: #2d3748; margin-bottom: 20px; border-bottom: 3px solid #38ef7d; padding-bottom: 15px;">
                        <i class="fas fa-upload" style="color: #38ef7d;"></i>
                        Service Completion Details
                    </h5>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-camera"></i> Service Completion Image <span style="color: #ff4757;">*</span>
                            </label>
                            <div class="custom-file-upload">
                                <input type="file" name="service_img" id="service_img" accept="image/*" required onchange="previewImage(this, 'service-preview')">
                                <label for="service_img" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Click to upload service image</span>
                                </label>
                                <div id="service-preview" class="image-preview"></div>
                            </div>
                            <small style="color: #6c757d;">Upload a photo of the completed work (JPG, PNG - Max 5MB)</small>
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-file-invoice"></i> Bill/Receipt Image <span style="color: #ff4757;">*</span>
                            </label>
                            <div class="custom-file-upload">
                                <input type="file" name="bill_img" id="bill_img" accept="image/*" required onchange="previewImage(this, 'bill-preview')">
                                <label for="bill_img" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Click to upload bill/receipt</span>
                                </label>
                                <div id="bill-preview" class="image-preview"></div>
                            </div>
                            <small style="color: #6c757d;">Upload the service bill or receipt (JPG, PNG - Max 5MB)</small>
                        </div>

                        <?php if(!$admin_price_set): ?>
                        <!-- Admin has NOT set a price - Technician must enter -->
                        <div class="alert" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.05) 100%); border-left: 5px solid #ffc107; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ffc107;"></i>
                                <div>
                                    <strong style="color: #856404; font-size: 1.1rem;">⚠️ No Fixed Price Set by Admin</strong>
                                    <p style="margin: 8px 0 0 0; color: #856404; font-size: 0.95rem;">
                                        You need to enter the final service price based on parts used and work completed.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label style="font-weight: 700; color: #2d3748; font-size: 1.1rem;">
                                <i class="fas fa-rupee-sign"></i> Enter Final Service Price (₹) <span style="color: #ff4757;">*</span>
                            </label>
                            <input type="number" name="final_price" class="form-control" step="0.01" min="0" value="<?php echo $display_price; ?>" required style="border-radius: 10px; padding: 15px; font-size: 1.3rem; font-weight: 700; border: 3px solid #ffc107; background: #fffbf0;">
                            <small style="color: #856404; font-weight: 600;">
                                <i class="fas fa-info-circle"></i> Calculate based on: Parts cost + Labor charges + Any additional work
                            </small>
                        </div>
                        <?php else: ?>
                        <!-- Admin HAS set a fixed price - Cannot be changed -->
                        <input type="hidden" name="final_price" value="<?php echo $display_price; ?>">
                        <div class="alert" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.05) 100%); border-left: 5px solid #28a745; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <i class="fas fa-lock" style="font-size: 2rem; color: #28a745;"></i>
                                <div>
                                    <strong style="color: #28a745; font-size: 1.3rem;">✅ Fixed Price: ₹<?php echo number_format($display_price, 2); ?></strong>
                                    <p style="margin: 8px 0 0 0; color: #155724; font-size: 0.95rem; font-weight: 600;">
                                        <i class="fas fa-check-circle"></i> This price is set by admin and cannot be modified.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-comment"></i> Completion Notes
                            </label>
                            <textarea name="completion_notes" class="form-control" rows="4" placeholder="Add any notes about the service completion..." style="border-radius: 10px; padding: 12px;"></textarea>
                        </div>

                        <div class="alert-custom" style="background: linear-gradient(135deg, rgba(56, 239, 125, 0.1) 0%, rgba(17, 153, 142, 0.1) 100%); border-left: 5px solid #38ef7d;">
                            <i class="fas fa-info-circle" style="color: #38ef7d;"></i>
                            <strong>Note:</strong> Once you submit, this booking will be marked as completed and sent to admin for review.
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="complete_service" class="btn btn-success-custom" style="padding: 15px 50px; font-size: 1.1rem;">
                                <i class="fas fa-check-circle"></i> Complete Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-item label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .info-item p {
            font-size: 1.1rem;
            color: #2d3748;
            font-weight: 600;
            margin: 0;
        }

        .custom-file-upload {
            margin-bottom: 10px;
        }

        .custom-file-upload input[type="file"] {
            display: none;
        }

        .file-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 3px dashed #cbd5e0;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-label:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            border-color: #38ef7d;
        }

        .file-label i {
            font-size: 3rem;
            color: #38ef7d;
            margin-bottom: 10px;
        }

        .file-label span {
            font-weight: 600;
            color: #4a5568;
        }

        .image-preview {
            margin-top: 15px;
            display: none;
        }

        .image-preview img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
