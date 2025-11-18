<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "My Profile";

// Get technician details
$query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();
$tech = $result->fetch_object();

// Handle profile update
if(isset($_POST['update_profile'])){
    $t_name = $_POST['t_name'];
    $t_phone = $_POST['t_phone'];
    $t_email = $_POST['t_email'];
    $t_addr = $_POST['t_addr'];
    
    $update_query = "UPDATE tms_technician SET t_name=?, t_phone=?, t_email=?, t_addr=? WHERE t_id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('ssssi', $t_name, $t_phone, $t_email, $t_addr, $t_id);
    
    if($update_stmt->execute()){
        $_SESSION['t_name'] = $t_name;
        $success = "Profile updated successfully!";
        // Refresh data
        $stmt->execute();
        $result = $stmt->get_result();
        $tech = $result->fetch_object();
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <div class="page-header">
            <h2>
                <i class="fas fa-user-circle" style="color: var(--primary);"></i>
                My Profile
            </h2>
            <p>View and update your profile information</p>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-4">
                <!-- ID Card -->
                <div class="id-card">
                    <div class="id-card-header">
                        <h3>ELECTROZOT</h3>
                        <p>Technician ID Card</p>
                    </div>
                    <div class="id-card-body">
                        <div class="id-photo">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="id-info">
                            <div class="id-field">
                                <label>Name</label>
                                <p><?php echo htmlspecialchars($tech->t_name); ?></p>
                            </div>
                            <div class="id-field">
                                <label>ID Number</label>
                                <p class="id-number"><?php echo htmlspecialchars($tech->t_id_no); ?></p>
                            </div>
                            <div class="id-field">
                                <label>Phone</label>
                                <p><?php echo htmlspecialchars($tech->t_phone ?? 'N/A'); ?></p>
                            </div>
                            <div class="id-field">
                                <label>Aadhaar Number</label>
                                <p><?php echo htmlspecialchars($tech->t_aadhar ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="id-card-footer">
                        <a href="tel:+919876543210" class="call-admin-btn">
                            <i class="fas fa-phone-alt"></i>
                            Call Admin Support
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card-custom">
                    <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 25px;">
                        <i class="fas fa-edit" style="color: var(--primary);"></i>
                        Edit Profile Information
                    </h5>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <input type="text" name="t_name" class="form-control" value="<?php echo htmlspecialchars($tech->t_name); ?>" required style="border-radius: 10px; padding: 12px;">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #2d3748;">
                                        <i class="fas fa-phone"></i> Phone Number
                                    </label>
                                    <input type="text" name="t_phone" class="form-control" value="<?php echo htmlspecialchars($tech->t_phone); ?>" required style="border-radius: 10px; padding: 12px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #2d3748;">
                                        <i class="fas fa-envelope"></i> Email Address
                                    </label>
                                    <input type="email" name="t_email" class="form-control" value="<?php echo htmlspecialchars($tech->t_email); ?>" required style="border-radius: 10px; padding: 12px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-map-marker-alt"></i> Address
                            </label>
                            <textarea name="t_addr" class="form-control" rows="3" required style="border-radius: 10px; padding: 12px;"><?php echo htmlspecialchars($tech->t_addr); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-briefcase"></i> Category
                            </label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($tech->t_category); ?>" readonly style="border-radius: 10px; padding: 12px; background: #f8f9fa;">
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary-custom">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <a href="change-password.php" class="btn btn-success-custom ml-2">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
