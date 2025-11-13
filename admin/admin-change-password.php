<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get admin details
$query = "SELECT * FROM tms_admin WHERE a_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $aid);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_object();

// Change password
if(isset($_POST['change_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if($current_password != $admin->a_pwd) {
        $error = "Current password is incorrect!";
    } elseif($new_password != $confirm_password) {
        $error = "New passwords do not match!";
    } elseif(strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        $hashed_password = md5($new_password);
        $update_query = "UPDATE tms_admin SET a_pwd = ? WHERE a_id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('si', $hashed_password, $aid);
        
        if($update_stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password: " . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="admin-profile.php">Profile</a>
                    </li>
                    <li class="breadcrumb-item active">Change Password</li>
                </ol>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-warning text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-key"></i> Change Password
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <strong>Security Tips:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use at least 6 characters</li>
                                        <li>Mix uppercase and lowercase letters</li>
                                        <li>Include numbers and special characters</li>
                                        <li>Don't use common words or personal information</li>
                                    </ul>
                                </div>

                                <form method="POST" id="passwordForm">
                                    <div class="form-group">
                                        <label for="current_password">
                                            <i class="fas fa-lock"></i> Current Password <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label for="new_password">
                                            <i class="fas fa-key"></i> New Password <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="new_password" 
                                               name="new_password" 
                                               minlength="6"
                                               required>
                                        <small class="form-text text-muted">Minimum 6 characters</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password">
                                            <i class="fas fa-check"></i> Confirm New Password <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               minlength="6"
                                               required>
                                        <small id="passwordMatch" class="form-text"></small>
                                    </div>

                                    <hr>

                                    <div class="form-group mb-0">
                                        <button type="submit" name="change_password" class="btn btn-warning btn-lg btn-block">
                                            <i class="fas fa-save"></i> Change Password
                                        </button>
                                        <a href="admin-profile.php" class="btn btn-secondary btn-lg btn-block">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Info -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-info text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-user"></i> Account Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($admin->a_name); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($admin->a_email); ?></p>
                                <p class="mb-0"><strong>Admin ID:</strong> #<?php echo $admin->a_id; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <script>
        // Password match validation
        $('#confirm_password').on('keyup', function() {
            var newPassword = $('#new_password').val();
            var confirmPassword = $(this).val();
            
            if(confirmPassword.length > 0) {
                if(newPassword === confirmPassword) {
                    $('#passwordMatch').html('<span class="text-success"><i class="fas fa-check"></i> Passwords match</span>');
                } else {
                    $('#passwordMatch').html('<span class="text-danger"><i class="fas fa-times"></i> Passwords do not match</span>');
                }
            } else {
                $('#passwordMatch').html('');
            }
        });

        // Form validation
        $('#passwordForm').on('submit', function(e) {
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();
            
            if(newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>
</body>
</html>
