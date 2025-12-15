<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

$success_msg = '';
$error_msg = '';

// Handle form submission
if (isset($_POST['change_password'])) {
    $current_pwd = $_POST['current_pwd'];
    $new_pwd = $_POST['new_pwd'];
    $confirm_pwd = $_POST['confirm_pwd'];
    
    // Get current password from database
    $query = "SELECT u_pwd FROM tms_user WHERE u_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $aid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_object();
    
    // Verify current password
    if ($current_pwd != $user->u_pwd) {
        $error_msg = "Current password is incorrect!";
    } elseif ($new_pwd != $confirm_pwd) {
        $error_msg = "New passwords do not match!";
    } elseif (strlen($new_pwd) < 6) {
        $error_msg = "Password must be at least 6 characters long!";
    } else {
        // Update password
        $update_query = "UPDATE tms_user SET u_pwd = ? WHERE u_id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('si', $new_pwd, $aid);
        
        if ($update_stmt->execute()) {
            $success_msg = "Password changed successfully!";
        } else {
            $error_msg = "Failed to change password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#000000">
    <title>Change Password - Electrozot</title>
    
    <!-- Favicon -->
    <?php include('vendor/inc/favicon.php'); ?>
    
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="vendor/inc/navbar-styles.css?v=<?php echo time(); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-top: 75px;
            padding-bottom: 55px;
        }
        

        
        .content {
            padding: 20px 15px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        .security-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 45px;
            color: white;
            box-shadow: 0 8px 25px rgba(209, 58, 189, 0.3);
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-label i {
            margin-right: 8px;
            color: #d13abd;
            font-size: 16px;
        }
        
        .password-input-wrapper {
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            padding-right: 45px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #d13abd;
            background: linear-gradient(135deg, #fff5f7 0%, #ffffff 100%);
        }
        
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
        }
        
        .toggle-password:hover {
            color: #d13abd;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 12px;
        }
        
        .strength-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }
        
        .strength-weak { width: 33%; background: #ef4444; }
        .strength-medium { width: 66%; background: #f59e0b; }
        .strength-strong { width: 100%; background: #10b981; }
        
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(209, 58, 189, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit i {
            margin-right: 8px;
        }
        
        .security-tips {
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .tips-title {
            font-size: 13px;
            font-weight: 700;
            color: #d13abd;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .tips-title i {
            margin-right: 8px;
        }
        
        .tips-list {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
            padding-left: 20px;
        }
        

        
        @media (min-width: 768px) {
            .content {
                padding: 30px;
                max-width: 700px;
            }
            
            .form-card {
                padding: 35px;
            }
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/navbar.php'); ?>

    <div class="content">
        <?php if ($success_msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_msg; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_msg; ?>
        </div>
        <?php endif; ?>
        
        <div class="security-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <div class="form-card">
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Current Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" name="current_pwd" id="current_pwd" class="form-control" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('current_pwd')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i> New Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" name="new_pwd" id="new_pwd" class="form-control" required onkeyup="checkPasswordStrength()">
                        <button type="button" class="toggle-password" onclick="togglePassword('new_pwd')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-check-circle"></i> Confirm New Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" name="confirm_pwd" id="confirm_pwd" class="form-control" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_pwd')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" name="change_password" class="btn-submit">
                    <i class="fas fa-shield-alt"></i> Change Password
                </button>
            </form>
            
            <div class="security-tips">
                <div class="tips-title">
                    <i class="fas fa-info-circle"></i> Password Tips
                </div>
                <ul class="tips-list">
                    <li>Use at least 6 characters</li>
                    <li>Mix uppercase and lowercase letters</li>
                    <li>Include numbers and special characters</li>
                    <li>Don't use common words or personal info</li>
                </ul>
            </div>
        </div>
    </div>



    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        function checkPasswordStrength() {
            const password = document.getElementById('new_pwd').value;
            const strengthFill = document.getElementById('strength-fill');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthFill.className = 'strength-fill';
            if (strength <= 2) {
                strengthFill.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthFill.classList.add('strength-medium');
            } else {
                strengthFill.classList.add('strength-strong');
            }
        }
    </script>

    <?php include('vendor/inc/bottom-nav.php'); ?>
</body>
</html>
