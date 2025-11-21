<!--Server Side Scripting To inject Login-->
<?php
  //session_start();
  include('vendor/inc/config.php');
  //include('vendor/inc/checklogin.php');
  //check_login();
  //$aid=$_SESSION['a_id'];
  // Ensure registration_type column exists
  $mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS registration_type ENUM('admin', 'self', 'guest') DEFAULT 'admin'");
  
  // Ensure area and pincode columns exist
  $mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS u_area VARCHAR(100)");
  $mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS u_pincode VARCHAR(10)");
  
  //Add USer
  if(isset($_POST['add_user']))
    {
            $u_fname=$_POST['u_fname'];
            $u_lname = $_POST['u_lname'];
            $u_phone=$_POST['u_phone'];
            
            // Validate phone number is exactly 10 digits
            if(!preg_match('/^[0-9]{10}$/', $u_phone)) {
                $err = "Phone number must be exactly 10 digits";
            } else {
            
            $u_addr=$_POST['u_addr'];
            $u_area=$_POST['u_area'];
            $u_pincode=$_POST['u_pincode'];
            $u_email=$_POST['u_email'];
            $u_pwd=$_POST['u_pwd'];
            $u_category=$_POST['u_category'];
            $registration_type = 'self'; // Mark as self-registered
            
            // Check if mobile number already exists
            $check_phone = $mysqli->prepare("SELECT u_id FROM tms_user WHERE u_phone = ?");
            $check_phone->bind_param('s', $u_phone);
            $check_phone->execute();
            $check_phone->store_result();
            
            if($check_phone->num_rows > 0) {
                $err = "This mobile number is already registered. Please use a different number or login.";
            } else {
                $query="INSERT into `tms_user` (u_fname, u_lname, u_phone, u_addr, u_area, u_pincode, u_category, u_email, u_pwd, registration_type) values(?,?,?,?,?,?,?,?,?,?)";
                $stmt = $mysqli->prepare($query);
                $rc=$stmt->bind_param('ssssssssss', $u_fname,  $u_lname, $u_phone, $u_addr, $u_area, $u_pincode, $u_category, $u_email, $u_pwd, $registration_type);
                $stmt->execute();
                
                if($stmt)
                {
                    $succ = "Account Created Successfully! Please Log In";
                }
                else 
                {
                    $err = "Registration Failed. Please Try Again Later";
                }
            }
            $check_phone->close();
            }
            } // Close phone validation
?>

<!--End Server Side Scriptiong-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Tranport Management System, Saccos, Matwana Culture">
    <meta name="author" content="MartDevelopers ">

    <title>Create Account - Electrozot</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { overflow-x: hidden; }
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
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .logo-section img { height: 55px; width: auto; }
        .logo-section .brand-name {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 550px;
            padding: 0 15px;
            margin: 80px auto 20px;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .register-header::before {
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
        .register-header h2 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        .register-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }
        .register-body { padding: 40px 35px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .form-group label i {
            color: #667eea;
            margin-right: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
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
        .helper-text {
            font-size: 0.85rem;
            color: #718096;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-register {
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
        .btn-register::before {
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
        .btn-register:hover::before {
            width: 300px;
            height: 300px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }
        .btn-register span {
            position: relative;
            z-index: 1;
        }
        .links-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
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
        @media (max-width: 768px) {
            .logo-section { top: 15px; left: 15px; padding: 10px 15px; }
            .logo-section img { height: 45px; }
            .logo-section .brand-name { font-size: 1.3rem; }
            .register-container { margin: 70px auto 20px; padding: 15px; }
            .register-body { padding: 30px 25px; }
            .register-header { padding: 35px 25px; }
            .register-header h2 { font-size: 1.5rem; }
            .logo-circle { width: 70px; height: 70px; }
            .logo-circle i { font-size: 2rem; }
        }
        @media (max-width: 480px) {
            body { padding: 10px 5px; }
            .logo-section { top: 10px; left: 10px; padding: 8px 12px; gap: 8px; max-width: calc(100% - 20px); }
            .logo-section img { height: 30px; }
            .logo-section .brand-name { font-size: 1rem; }
            .register-container { margin: 60px auto 10px; padding: 0 10px; max-width: 100%; }
            .register-card { border-radius: 20px; margin: 0 5px; }
            .register-body { padding: 25px 15px; }
            .register-header { padding: 30px 15px; }
            .register-header h2 { font-size: 1.3rem; }
            .register-header p { font-size: 0.9rem; }
            .logo-circle { width: 60px; height: 60px; margin-bottom: 15px; }
            .logo-circle i { font-size: 1.8rem; }
            .form-control { padding: 12px; font-size: 0.95rem; }
            .btn-register { padding: 13px; font-size: 1rem; }
            .links-section a { display: block; margin: 10px 0; }
        }
        @media (max-width: 360px) {
            body { padding: 10px 3px; }
            .register-container { padding: 0 5px; }
            .register-card { margin: 0 2px; }
            .register-body { padding: 20px 12px; }
            .register-header { padding: 25px 12px; }
        }
    </style>
</head>

<body>
    <?php if(isset($succ)) {?>
    <!--This code for injecting an alert-->
    <script>
    setTimeout(function() {
            swal("Success!", "<?php echo $succ;?>!", "success");
        },
        100);
    </script>

    <?php } ?>
    <?php if(isset($err)) {?>
    <!--This code for injecting an alert-->
    <script>
    setTimeout(function() {
            swal("Failed!", "<?php echo $err;?>!", "Failed");
        },
        100);
    </script>
    <?php } ?>
    <div class="logo-section">
        <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
        <span class="brand-name">Electrozot</span>
    </div>

    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-circle">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Create Your Account</h2>
                <p>Join Electrozot for professional electrical services</p>
            </div>
            <div class="register-body">
                <form method="post">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> First Name</label>
                        <input type="text" required class="form-control" name="u_fname" placeholder="Enter your first name">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" required class="form-control" name="u_lname" placeholder="Enter your last name">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" required class="form-control" name="u_phone" placeholder="10-digit mobile number" pattern="[0-9]{10}" maxlength="10" title="Enter exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                        <div class="helper-text">
                            <i class="fas fa-info-circle"></i> Enter exactly 10 digits
                            <span>10-digit mobile number for login</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <input type="text" required class="form-control" name="u_addr" placeholder="Enter your complete address">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-signs"></i> Area / Locality</label>
                        <input type="text" required class="form-control" name="u_area" placeholder="Enter your area or locality">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-pin"></i> Pincode</label>
                        <input type="text" required class="form-control" name="u_pincode" placeholder="Enter 6-digit pincode" pattern="[0-9]{6}" maxlength="6">
                        <div class="helper-text">
                            <i class="fas fa-info-circle"></i>
                            <span>6-digit postal pincode</span>
                        </div>
                    </div>
                    
                    <div class="form-group" style="display:none">
                        <input type="text" class="form-control" value="User" name="u_category">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" required class="form-control" name="u_email" placeholder="Enter your email address">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div style="position: relative;">
                            <input type="password" required class="form-control" name="u_pwd" id="registerPassword" placeholder="Create a strong password" minlength="6">
                            <i class="fas fa-eye" id="toggleRegisterPassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #667eea;"></i>
                        </div>
                        <div class="helper-text">
                            <i class="fas fa-shield-alt"></i>
                            <span>Minimum 6 characters required</span>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_user" class="btn-register">
                        <span><i class="fas fa-user-plus"></i> Create Account</span>
                    </button>
                </form>
                
                <div class="links-section">
                    <a href="index.php"><i class="fas fa-sign-in-alt"></i> Already have an account? Login</a>
                    <a href="usr-forgot-password.php"><i class="fas fa-key"></i> Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('toggleRegisterPassword');
        const password = document.getElementById('registerPassword');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!--INject Sweet alert js-->
    <script src="vendor/js/swal.js"></script>

</body>

</html>