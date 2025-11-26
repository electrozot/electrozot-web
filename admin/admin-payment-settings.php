<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Create payment settings table if not exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_payment_settings (
    ps_id INT AUTO_INCREMENT PRIMARY KEY,
    ps_qr_code VARCHAR(255),
    ps_upi_id VARCHAR(100),
    ps_business_name VARCHAR(255),
    ps_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ps_updated_by INT
)");

// Insert default row if empty
$check = $mysqli->query("SELECT COUNT(*) as count FROM tms_payment_settings");
if($check->fetch_object()->count == 0) {
    $mysqli->query("INSERT INTO tms_payment_settings (ps_business_name) VALUES ('Electrozot')");
}

// Handle QR Upload
if(isset($_POST['upload_qr'])) {
    $ps_upi_id = $_POST['ps_upi_id'];
    $ps_business_name = $_POST['ps_business_name'];
    
    if(!empty($_FILES["ps_qr_code"]["name"])) {
        $target_dir = "../uploads/payment/";
        if(!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["ps_qr_code"]["name"], PATHINFO_EXTENSION);
        $new_filename = "payment_qr_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if(move_uploaded_file($_FILES["ps_qr_code"]["tmp_name"], $target_file)) {
            $db_path = "uploads/payment/" . $new_filename;
            
            $query = "UPDATE tms_payment_settings SET ps_qr_code=?, ps_upi_id=?, ps_business_name=?, ps_updated_by=? WHERE ps_id=1";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sssi', $db_path, $ps_upi_id, $ps_business_name, $aid);
            
            if($stmt->execute()) {
                $succ = "Payment QR Code uploaded successfully!";
            } else {
                $err = "Failed to save QR code";
            }
        } else {
            $err = "Failed to upload file";
        }
    } else {
        // Update without changing QR
        $query = "UPDATE tms_payment_settings SET ps_upi_id=?, ps_business_name=?, ps_updated_by=? WHERE ps_id=1";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssi', $ps_upi_id, $ps_business_name, $aid);
        
        if($stmt->execute()) {
            $succ = "Payment settings updated successfully!";
        } else {
            $err = "Failed to update settings";
        }
    }
}

// Get current settings
$settings_query = "SELECT * FROM tms_payment_settings WHERE ps_id=1";
$settings_result = $mysqli->query($settings_query);
$settings = $settings_result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <?php if(isset($succ)) {?>
                <script>
                setTimeout(function() {
                    swal("Success!", "<?php echo $succ;?>", "success");
                }, 100);
                </script>
                <?php } ?>
                <?php if(isset($err)) {?>
                <script>
                setTimeout(function() {
                    swal("Failed!", "<?php echo $err;?>", "error");
                }, 100);
                </script>
                <?php } ?>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1" style="font-weight: 800; color: #2c3e50;">
                            <i class="fas fa-qrcode" style="color: #667eea;"></i> Payment Settings
                        </h2>
                        <p class="text-muted mb-0">Manage payment QR code for technician collections</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0; padding: 20px; border: none;">
                                <h5 class="mb-0" style="color: white; font-weight: 700;">
                                    <i class="fas fa-upload"></i> Upload Payment QR Code
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 30px;">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label><i class="fas fa-building"></i> Business Name</label>
                                        <input type="text" name="ps_business_name" class="form-control" value="<?php echo htmlspecialchars($settings->ps_business_name ?? 'Electrozot'); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><i class="fas fa-mobile-alt"></i> UPI ID (Optional)</label>
                                        <input type="text" name="ps_upi_id" class="form-control" value="<?php echo htmlspecialchars($settings->ps_upi_id ?? ''); ?>" placeholder="example@upi">
                                        <small class="text-muted">For reference only</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><i class="fas fa-qrcode"></i> Payment QR Code Image</label>
                                        <input type="file" name="ps_qr_code" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Upload QR code image (JPG, PNG)</small>
                                    </div>
                                    
                                    <button type="submit" name="upload_qr" class="btn btn-primary btn-block" style="border-radius: 50px; padding: 12px; font-weight: 700;">
                                        <i class="fas fa-save"></i> Save Payment Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                            <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 15px 15px 0 0; padding: 20px; border: none;">
                                <h5 class="mb-0" style="color: white; font-weight: 700;">
                                    <i class="fas fa-eye"></i> Current QR Code
                                </h5>
                            </div>
                            <div class="card-body text-center" style="padding: 30px;">
                                <?php if(!empty($settings->ps_qr_code) && file_exists("../" . $settings->ps_qr_code)): ?>
                                    <img src="../<?php echo $settings->ps_qr_code; ?>" alt="Payment QR" style="max-width: 100%; max-height: 400px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    <div class="mt-3">
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($settings->ps_business_name); ?></strong></p>
                                        <?php if(!empty($settings->ps_upi_id)): ?>
                                        <p class="text-muted mb-0"><small><?php echo htmlspecialchars($settings->ps_upi_id); ?></small></p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> No QR code uploaded yet
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    <script src="vendor/js/swal.js"></script>
</body>
</html>
