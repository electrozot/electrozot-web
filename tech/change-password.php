<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];

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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .change-password-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header-section {
            background: linear-gradient(135deg, #8ff5e2ff 0%, #eb6df2ff 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .header-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
        }
        
        .header-icon i {
            font-size: 2.5rem;
        }
        
        .header-section h2 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .header-section p {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .form-section {
            padding: 30px;
        }
        
        .alert-box {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        .alert-box i {
            font-size: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .form-label i {
            color: #ff4757;
            margin-right: 5px;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #ff4757;
            box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.1);
        }
        
        .form-hint {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
            display: block;
        }
        
        .form-hint i {
            color: #ffa502;
        }
        
        .security-tips {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border-left: 4px solid #ffd700;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .security-tips strong {
            color: #333;
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .security-tips strong i {
            color: #ffd700;
            margin-right: 5px;
        }
        
        .security-tips ul {
            margin: 0;
            padding-left: 20px;
            color: #555;
            font-size: 0.9rem;
        }
        
        .security-tips li {
            margin-bottom: 5px;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }
        
        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #e275f8ff 0%, #7cf1f3ff 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.4);
        }
        
        .btn-back {
            flex: 1;
            background: #6c757d;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        
        /* Mobile Responsive */
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .change-password-container {
                border-radius: 15px;
            }
            
            .header-section {
                padding: 25px 20px;
            }
            
            .header-icon {
                width: 70px;
                height: 70px;
            }
            
            .header-icon i {
                font-size: 2rem;
            }
            
            .header-section h2 {
                font-size: 1.5rem;
            }
            
            .header-section p {
                font-size: 0.9rem;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .form-input {
                padding: 12px 15px;
                font-size: 0.95rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn-submit,
            .btn-back {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="change-password-container">
        <div class="header-section">
            <div class="header-icon">
                <i class="fas fa-key"></i>
            </div>
            <h2>Change Password</h2>
            <p>Update your account security</p>
        </div>
        
        <div class="form-section">
            <?php if(isset($success)): ?>
                <div class="alert-box alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert-box alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Current Password
                    </label>
                    <input type="password" name="current_pwd" class="form-input" required placeholder="Enter your current password">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i> New Password
                    </label>
                    <input type="password" name="new_pwd" class="form-input" required placeholder="Enter new password" minlength="6">
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i> Minimum 6 characters required
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-check-circle"></i> Confirm New Password
                    </label>
                    <input type="password" name="confirm_pwd" class="form-input" required placeholder="Re-enter new password" minlength="6">
                </div>

                <div class="security-tips">
                    <strong><i class="fas fa-shield-alt"></i> Security Tips:</strong>
                    <ul>
                        <li>Use letters, numbers, and symbols</li>
                        <li>Don't share your password</li>
                        <li>Change it regularly</li>
                    </ul>
                </div>

                <div class="button-group">
                    <button type="submit" name="change_password" class="btn-submit">
                        <i class="fas fa-save"></i> Change Password
                    </button>
                    <a href="my-profile.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
