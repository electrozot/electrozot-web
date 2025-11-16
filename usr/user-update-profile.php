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

// Get user info
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();

$success_msg = '';
$error_msg = '';

// Handle form submission
if (isset($_POST['update_profile'])) {
    $fname = $_POST['u_fname'];
    $lname = $_POST['u_lname'];
    $phone = $_POST['u_phone'];
    $addr = $_POST['u_addr'];
    
    $update_query = "UPDATE tms_user SET u_fname=?, u_lname=?, u_phone=?, u_addr=? WHERE u_id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('ssssi', $fname, $lname, $phone, $addr, $aid);
    
    if ($update_stmt->execute()) {
        $success_msg = "Profile updated successfully!";
        // Refresh user data
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user = $user_result->fetch_object();
    } else {
        $error_msg = "Failed to update profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Profile - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .top-bar {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin-right: 12px;
        }
        
        .logo {
            height: 35px;
            width: auto;
            margin-right: 12px;
        }
        
        .top-bar-title {
            flex: 1;
            font-size: 18px;
            font-weight: 700;
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
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 40px;
            color: white;
            font-weight: 700;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
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
            color: #6366f1;
            font-size: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            background: linear-gradient(135deg, #f5f7ff 0%, #ffffff 100%);
        }
        
        .form-control:disabled {
            background: #f9fafb;
            color: #9ca3af;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit i {
            margin-right: 8px;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            z-index: 100;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: #999;
            padding: 8px;
            transition: all 0.3s;
        }
        
        .nav-item.active {
            color: #6366f1;
        }
        
        .nav-item i {
            font-size: 20px;
            display: block;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 600;
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
    <div class="top-bar">
        <a href="user-view-profile.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
        <div class="top-bar-title">Edit Profile</div>
        <div style="width: 40px;"></div>
    </div>

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
        
        <div class="profile-avatar">
            <?php 
            $initials = strtoupper(substr($user->u_fname, 0, 1) . substr($user->u_lname, 0, 1));
            echo $initials;
            ?>
        </div>
        
        <div class="form-card">
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user->u_email); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> First Name
                    </label>
                    <input type="text" name="u_fname" class="form-control" value="<?php echo htmlspecialchars($user->u_fname); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Last Name
                    </label>
                    <input type="text" name="u_lname" class="form-control" value="<?php echo htmlspecialchars($user->u_lname); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" name="u_phone" class="form-control" value="<?php echo htmlspecialchars($user->u_phone); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Address
                    </label>
                    <textarea name="u_addr" class="form-control" required><?php echo htmlspecialchars($user->u_addr); ?></textarea>
                </div>
                
                <button type="submit" name="update_profile" class="btn-submit">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item">
            <i class="fas fa-clipboard-list"></i>
            <span>Bookings</span>
        </a>
        <a href="user-track-booking.php" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Track</span>
        </a>
        <a href="user-view-profile.php" class="nav-item active">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
</body>
</html>
