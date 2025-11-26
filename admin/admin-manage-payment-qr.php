<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Create payment settings table if not exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_payment_settings (
    ps_id INT AUTO_INCREMENT PRIMARY KEY,
    ps_qr_image VARCHAR(255),
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
    
    if(!empty($_FILES["ps_qr_image"]["name"])) {
        $target_dir = "../uploads/payment/";
        if(!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES["ps_qr_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "payment_qr_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if(move_uploaded_file($_FILES["ps_qr_image"]["tmp_name"], $target_file)) {
            $query = "UPDATE tms_payment_settings SET ps_qr_image=?, ps_upi_id=?, ps_business_name=?, ps_updated_by=? WHERE ps_id=1";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sssi', $new_filename, $ps_upi_id, $ps_business_name, $aid);
            
            if($stmt->execute()) {
                $succ = "Payment QR Code updated successfully!";
            } else {
                $err = "Failed to update QR code.";
            }
        } else {
            $err = "Failed to upload QR image.";
        }
    } else {
        // Update without image
        $query = "UPDATE tms_payment_settings SET ps_upi_id=?, ps_business_name=?, ps_updated_by=? WHERE ps_id=1";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssi', $ps_upi_id, $ps_business_name, $aid);
        
        if($stmt->execute()) {
            $succ = "Payment details updated successfully!";
        } else {
            $err = "Failed to update details.";
        }
    }
}

// Get current settings
$settings_query = "SELECT * FROM tms_payment_settings WHERE ps_id=1";
$settings = $mysqli->query($settings_query)->fetch_object();
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
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Settings</a></li>
                    <li class="breadcrumb-item active">Payment QR Management</li>
                </ol>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <h5 class="mb-0"><i class="fas fa-qrcode"></i> Payment QR Code Management</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label><i class="fas fa-building"></i> Business Name</label>
                                        <input type="text" name="ps_business_name" class="form-control" value="<?php echo htmlspecialchars($settings->ps_business_name ?? 'Electrozot'); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><i class="fas fa-mobile-alt"></i> UPI ID</label>
                                        <input type="text" name="ps_upi_id" class="form-control" placeholder="example@upi" value="<?php echo htmlspecialchars($settings->ps_upi_id ?? ''); ?>">
                                        <small class="text-muted">Optional: Your UPI ID for reference</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><i class="fas fa-image"></i> Upload Payment QR Code</label>
                                        <input type="file" name="ps_qr_image" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Upload your business payment QR code image</small>
                                    </div>
                                    
                                    <button type="submit" name="upload_qr" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Payment QR
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-eye"></i> Current QR Code</h6>
                            </div>
                            <div class="card-body text-center">
                                <?php if(!empty($settings->ps_qr_image) && file_exists("../uploads/payment/" . $settings->ps_qr_image)): ?>
                                    <img src="../uploads/payment/<?php echo $settings->ps_qr_image; ?>" class="img-fluid" style="max-width: 300px; border: 2px solid #ddd; border-radius: 10px;">
                                    <p class="mt-3"><strong><?php echo htmlspecialchars($settings->ps_business_name); ?></strong></p>
                                    <?php if($settings->ps_upi_id): ?>
                                        <p class="text-muted"><?php echo htmlspecialchars($settings->ps_upi_id); ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> No QR code uploaded yet
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card shadow mt-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> How It Works</h6>
                            </div>
                            <div class="card-body">
                                <ol class="small">
                                    <li>Upload your business payment QR code</li>
                                    <li>Technicians will show this QR to customers</li>
                                    <li>Customers scan and pay</li>
                                    <li>Technician confirms payment received</li>
                                    <li>Service can then be marked complete</li>
                                </ol>
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
