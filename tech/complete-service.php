<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Complete Service";

$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name
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
    
    // Update booking
    $update_query = "UPDATE tms_service_booking 
                     SET sb_status='Completed', 
                         sb_completion_img=?, 
                         sb_bill_img=?, 
                         sb_final_price=?,
                         sb_completion_notes=?,
                         sb_completed_date=NOW()
                     WHERE sb_id=? AND sb_technician_id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('ssdsii', $service_img, $bill_img, $final_price, $completion_notes, $sb_id, $t_id);
    
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
                        <label>Original Price</label>
                        <p style="font-size: 1.5rem; color: #38ef7d; font-weight: 700;">$<?php echo number_format($booking->sb_total_price, 2); ?></p>
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

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-dollar-sign"></i> Final Price <span style="color: #ff4757;">*</span>
                            </label>
                            <input type="number" name="final_price" class="form-control" step="0.01" min="0" value="<?php echo $booking->sb_total_price; ?>" required style="border-radius: 10px; padding: 12px; font-size: 1.1rem; font-weight: 600;">
                            <small style="color: #6c757d;">Enter the final service price (can be different from original if additional work was done)</small>
                        </div>

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
