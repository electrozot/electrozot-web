<!--Server Side Scripting Language to inject login code-->
<?php
    session_start();
    include('vendor/inc/config.php');//get configuration file
    if(isset($_POST['admin_login']))
    {
      $a_email=$_POST['a_email'];
      $a_pwd=($_POST['a_pwd']);//
      $a_pwd= md5($a_pwd);//
      $stmt=$mysqli->prepare("SELECT a_email, a_pwd, a_id, a_name, a_photo FROM tms_admin WHERE a_email=? and a_pwd=? ");//sql to log in user
      $stmt->bind_param('ss',$a_email,$a_pwd);//bind fetched parameters
      $stmt->execute();//execute bind
      $stmt -> bind_result($a_email,$a_pwd,$a_id,$a_name,$a_photo);//bind result
      $rs=$stmt->fetch();
      $_SESSION['a_id']=$a_id;//assaign session to admin id
      $_SESSION['a_name']=$a_name;//assign session to admin name
      $_SESSION['a_photo']=$a_photo;//assign session to admin photo
      if($rs)
      {//if its sucessfull
        header("location:admin-dashboard.php");
      }
      else
      {
      $error = "Admin User Name & Password Not Match";
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Technician Booking System - Admin Login">
    <title>Admin Login - Electrozot</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
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
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        /* Animated Background Circles */
        body::before {
            content: '';
            position: fixed;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: fixed;
            bottom: -150px;
            left: -150px;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
            z-index: 0;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
            }
            50% {
                transform: translateY(-20px) translateX(20px);
            }
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease;
        }
        
        .logo-box {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: logoFloat 3s ease-in-out infinite;
        }
        
        .logo-box img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }
        
        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .welcome-text {
            color: white;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 5px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        }
        
        .welcome-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
            font-weight: 500;
        }
        
        .login-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-header {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            padding: 25px;
            text-align: center;
            color: white;
        }
        
        .card-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .card-header i {
            font-size: 1.8rem;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .alert-error i {
            font-size: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .form-label i {
            color: #48bb78;
            margin-right: 8px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 18px 14px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #48bb78;
            box-shadow: 0 0 0 4px rgba(72, 187, 120, 0.1);
            transform: translateY(-2px);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus + .input-icon {
            color: #48bb78;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #48bb78;
        }
        
        .checkbox-wrapper label {
            color: #4a5568;
            font-size: 0.95rem;
            cursor: pointer;
            user-select: none;
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .links-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
        }
        
        .link-item {
            display: block;
            color: #4299e1;
            text-decoration: none;
            font-weight: 600;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        
        .link-item:hover {
            color: #2b6cb0;
            text-decoration: none;
            transform: translateX(5px);
        }
        
        .link-item i {
            margin-right: 5px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 576px) {
            body {
                padding: 30px 15px;
            }
            
            .logo-box {
                width: 80px;
                height: 80px;
            }
            
            .welcome-text {
                font-size: 1.6rem;
            }
            
            .welcome-subtitle {
                font-size: 0.9rem;
            }
            
            .card-body {
                padding: 25px;
            }
            
            .form-input {
                padding: 12px 15px 12px 40px;
                font-size: 0.95rem;
            }
            
            .btn-login {
                padding: 13px;
                font-size: 1rem;
            }
        }
        
        /* Ensure scrollability on small screens */
        @media (max-height: 700px) {
            body {
                display: block;
                padding: 30px 20px;
            }
            
            .login-container {
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo-box">
                <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
            </div>
            <h1 class="welcome-text">Welcome Back</h1>
            <p class="welcome-subtitle">Admin Control Panel</p>
        </div>
        
        <!-- Login Card -->
        <div class="login-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-shield-alt"></i>
                    Admin Login
                </h3>
            </div>
            
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-wrapper">
                            <input type="email" name="a_email" class="form-input" placeholder="Enter your email" required autofocus>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-wrapper">
                            <input type="password" name="a_pwd" class="form-input" placeholder="Enter your password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" value="remember-me">
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" name="admin_login" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Login to Dashboard
                    </button>
                </form>
                
                <div class="links-section">
                    <a href="../index.php" class="link-item">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                    <a href="admin-reset-pwd.php" class="link-item">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/swal.js"></script>
    
    <?php if(isset($error)): ?>
    <script>
        setTimeout(function() {
            swal("Failed!", "<?php echo $error;?>", "error");
        }, 100);
    </script>
    <?php endif; ?>
</body>
</html>
