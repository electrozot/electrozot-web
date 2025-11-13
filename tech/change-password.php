<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Change Password";

// Handle password change
if(isset($_POST['change_password'])){
    $current_pwd = $_POST['current_pwd'];
    $new_pwd = $_POST['new_pwd'];
    $confirm_pwd = $_POST['confirm_pwd'];
    
    // Get current password
    $query = "SELECT t_pwd FROM tms_technician WHERE t_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_object();
    
    // Validate current password
    if($tech->t_pwd != $current_pwd){
        $error = "Current password is incorrect!";
    }
    // Check if new passwords match
    elseif($new_pwd != $confirm_pwd){
        $error = "New passwords do not match!";
    }
    // Check password length
    elseif(strlen($new_pwd) < 6){
        $error = "Password must be at least 6 characters long!";
    }
    else {
        // Update password
        $update_query = "UPDATE tms_technician SET t_pwd=? WHERE t_id=?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('si', $new_pwd, $t_id);
        
        if($update_stmt->execute()){
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password. Please try again.";
        }
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
                <i class="fas fa-key" style="color: var(--primary);"></i>
                Change Password
            </h2>
            <p>Update your account password for security</p>
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

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-custom">
                    <div class="text-center mb-4">
                        <div style="width: 100px; height: 100px; margin: 0 auto 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);">
                            <i class="fas fa-lock" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                        <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748;">
                            Update Your Password
                        </h5>
                        <p style="color: #6c757d;">Enter your current password and choose a new one</p>
                    </div>

                    <form method="POST">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <input type="password" name="current_pwd" class="form-control" required placeholder="Enter your current password" style="border-radius: 10px; padding: 12px;">
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-key"></i> New Password
                            </label>
                            <input type="password" name="new_pwd" class="form-control" required placeholder="Enter new password (min 6 characters)" minlength="6" style="border-radius: 10px; padding: 12px;">
                            <small style="color: #6c757d; display: block; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> Password must be at least 6 characters long
                            </small>
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-check-circle"></i> Confirm New Password
                            </label>
                            <input type="password" name="confirm_pwd" class="form-control" required placeholder="Re-enter new password" minlength="6" style="border-radius: 10px; padding: 12px;">
                        </div>

                        <div class="alert-custom" style="background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%); border-left: 5px solid #ffd700;">
                            <i class="fas fa-shield-alt" style="color: #ffd700;"></i>
                            <strong>Security Tips:</strong>
                            <ul style="margin: 10px 0 0 20px; color: #4a5568;">
                                <li>Use a strong password with letters, numbers, and symbols</li>
                                <li>Don't share your password with anyone</li>
                                <li>Change your password regularly</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="change_password" class="btn btn-primary-custom">
                                <i class="fas fa-save"></i> Change Password
                            </button>
                            <a href="profile.php" class="btn btn-secondary ml-2" style="border-radius: 50px; padding: 12px 30px;">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
