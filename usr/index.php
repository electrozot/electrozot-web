<!--Server Side Scripting Language to inject login code-->
<?php
    session_start();
    include('vendor/inc/config.php');//get configuration file
    if(isset($_POST['user_login']))
    {
      $u_phone=$_POST['u_phone']; // Changed to phone
      $u_pwd=($_POST['u_pwd']);//
     
      $stmt=$mysqli->prepare("SELECT u_phone, u_pwd, u_id FROM tms_user WHERE u_phone=? and u_pwd=? ");//sql to log in user using phone
      $stmt->bind_param('ss',$u_phone,$u_pwd);//bind fetched parameters
      $stmt->execute();//execute bind
      $stmt -> bind_result($u_phone,$u_pwd,$u_id);//bind result
      $rs=$stmt->fetch();
      $_SESSION['u_id']=$u_id;//assaign session to user id
      //$uip=$_SERVER['REMOTE_ADDR'];
      //$ldate=date('d/m/Y h:i:s', time());
      if($rs)
      {//if its sucessfull
        header("location:user-dashboard.php");
      }

      else
      {
      #echo "<script>alert('Access Denied Please Check Your Credentials');</script>";
      $error = "Mobile Number & Password Not Match";
      }
  }
?>

<!--End Server Side Script Injection-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Client Login - Electrozot</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 20px 10px;
            width: 100%;
        }
        
        /* Animated Background Elements */
        body::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        /* Logo Section */
        .logo-section {
            position: fixed;
            top: 20px;
            left: 30px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            animation: slideInLeft 0.6s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .logo-section img {
            height: 40px;
            width: auto;
        }
        
        .logo-section .brand-name {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            padding: 0 15px;
            margin: 80px auto 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .logo-circle {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .logo-circle i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-header h2 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }
        
        .login-body {
            padding: 40px 35px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .form-group label i {
            color: #667eea;
            margin-right: 8px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 45px 15px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #667eea;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .toggle-password:hover {
            color: #764ba2;
            transform: translateY(-50%) scale(1.1);
        }
        
        .helper-text {
            font-size: 0.85rem;
            color: #718096;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }
        
        .btn-login span {
            position: relative;
            z-index: 1;
        }
        
        .links-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        
        .links-section a {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin: 8px 15px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .links-section a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #667eea;
            transition: width 0.3s ease;
        }
        
        .links-section a:hover {
            color: #764ba2;
        }
        
        .links-section a:hover::after {
            width: 100%;
        }
        
        .links-section a i {
            margin-right: 5px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .logo-section {
                top: 15px;
                left: 15px;
                padding: 10px 15px;
            }
            
            .logo-section img {
                height: 35px;
            }
            
            .logo-section .brand-name {
                font-size: 1.1rem;
            }
            
            .login-container {
                margin: 70px auto 20px;
                padding: 15px;
            }
            
            .login-body {
                padding: 30px 25px;
            }
            
            .login-header {
                padding: 35px 25px;
            }
            
            .login-header h2 {
                font-size: 1.5rem;
            }
            
            .logo-circle {
                width: 70px;
                height: 70px;
            }
            
            .logo-circle i {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px 5px;
            }
            
            .logo-section {
                top: 10px;
                left: 10px;
                right: auto;
                padding: 8px 12px;
                gap: 8px;
                max-width: calc(100% - 20px);
            }
            
            .logo-section img {
                height: 30px;
            }
            
            .logo-section .brand-name {
                font-size: 1rem;
            }
            
            .login-container {
                margin: 60px auto 10px;
                padding: 0 10px;
                max-width: 100%;
            }
            
            .login-card {
                border-radius: 20px;
                margin: 0 5px;
            }
            
            .login-body {
                padding: 25px 15px;
            }
            
            .login-header {
                padding: 30px 15px;
            }
            
            .login-header h2 {
                font-size: 1.3rem;
            }
            
            .login-header p {
                font-size: 0.9rem;
            }
            
            .logo-circle {
                width: 60px;
                height: 60px;
                margin-bottom: 15px;
            }
            
            .logo-circle i {
                font-size: 1.8rem;
            }
            
            .form-control {
                padding: 12px 40px 12px 12px;
                font-size: 0.95rem;
            }
            
            .btn-login {
                padding: 13px;
                font-size: 1rem;
            }
            
            .links-section a {
                display: block;
                margin: 10px 0;
            }
        }
        
        @media (max-width: 360px) {
            body {
                padding: 10px 3px;
            }
            
            .login-container {
                padding: 0 5px;
            }
            
            .login-card {
                margin: 0 2px;
            }
            
            .login-body {
                padding: 20px 12px;
            }
            
            .login-header {
                padding: 25px 12px;
            }
        }
        
        @media (min-height: 900px) {
            body {
                align-items: center;
            }
            
            .login-container {
                margin: auto;
            }
        }
    </style>
</head>

<body>
    <!-- Logo Section -->
    <div class="logo-section">
        <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
        <span class="brand-name">Electrozot</span>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-circle">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2>Welcome Back!</h2>
                <p>Login to access your account</p>
            </div>
            
            <div class="login-body">
                <?php if(isset($error)) {?>
                <script>
                setTimeout(function() {
                    swal("Failed!", "<?php echo $error;?>!", "error");
                }, 100);
                </script>
                <?php } ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-mobile-alt"></i> Mobile Number
                        </label>
                        <div class="input-wrapper">
                            <input type="tel" name="u_phone" id="inputPhone" class="form-control" required autofocus pattern="[0-9]{10}" maxlength="10" placeholder="Enter 10-digit mobile number">
                        </div>
                        <div class="helper-text">
                            <i class="fas fa-info-circle"></i>
                            <span>Enter your registered mobile number</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-wrapper">
                            <input type="password" name="u_pwd" id="inputPassword" class="form-control" required placeholder="Enter your password">
                            <i class="fas fa-eye toggle-password" id="toggleUserPassword" onclick="togglePasswordVisibility('inputPassword', 'toggleUserPassword')"></i>
                        </div>
                    </div>
                    
                    <button type="submit" name="user_login" class="btn-login">
                        <span><i class="fas fa-sign-in-alt"></i> Login to Dashboard</span>
                    </button>
                </form>
                
                <div class="links-section">
                    <a href="usr-register.php">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                    <a href="usr-forgot-password.php">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                    <br>
                    <a href="../index.php">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!--INject Sweet alert js-->
    <script src="vendor/js/swal.js"></script>
    
    <script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    </script>

</body>

</html>
