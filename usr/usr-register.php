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
                    // Redirect to login page after successful registration
                    header("Location: index.php?registered=success");
                    exit;
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
    <meta name="theme-color" content="#000000">

    <title>Create Account - Electrozot</title>
    
    <!-- Favicon -->
    <?php include('vendor/inc/favicon.php'); ?>
    
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { overflow-x: hidden; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #FFE4F0 0%, #FFC9E0 50%, #FFB3D9 100%);
            background-attachment: fixed;
            min-height: 100vh;
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

        .register-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 550px;
            padding: 0 15px;
            margin: 100px auto 40px;
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
            background: linear-gradient(135deg, #d13abd 0%, #ec6ead 20%, #f48fb1 50%, #f59e9e 80%, #f9a8a8 100%);
            padding: 25px 30px;
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
            width: 65px;
            height: 65px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 15px;
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
            font-size: 2rem;
            background: linear-gradient(135deg, #ec6ead 0%, #d13abd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .register-header h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        .register-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 8px 0 0;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }
        .register-body { 
            padding: 30px 35px;
            background: linear-gradient(180deg, #FFFEF0 0%, #FFF9E0 100%);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        .form-group label i {
            color: #ec6ead;
            margin-right: 8px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        .form-control:focus {
            outline: none;
            border-color: #ec6ead;
            background: white;
            box-shadow: 0 0 0 4px rgba(236, 110, 173, 0.1);
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
            padding: 12px 20px;
            background: linear-gradient(135deg, #d13abd 0%, #ec6ead 20%, #f48fb1 50%, #f59e9e 80%, #f9a8a8 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
            white-space: nowrap;
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
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
        }
        .btn-link {
            display: inline-block;
            padding: 10px 16px;
            background: white;
            border: 2px solid #ec6ead;
            border-radius: 10px;
            color: #ec6ead !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.15s ease;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(236, 110, 173, 0.15);
            cursor: pointer;
            text-align: center;
            white-space: nowrap;
        }
        .btn-link:hover {
            background: linear-gradient(135deg, #ec6ead 0%, #d13abd 100%);
            color: white !important;
            border-color: #d13abd;
            box-shadow: 0 4px 12px rgba(236, 110, 173, 0.35);
        }
        .btn-link:active {
            background: linear-gradient(135deg, #ff1493 0%, #ff69b4 100%) !important;
            color: white !important;
            border-color: #ff1493;
            transform: scale(0.96);
            box-shadow: 0 2px 8px rgba(255, 20, 147, 0.5);
            transition: all 0.05s ease;
        }
        .btn-link:visited {
            color: #ec6ead !important;
            text-decoration: none;
        }
        .btn-link:visited:hover {
            color: white !important;
        }
        .btn-link:visited:active {
            color: white !important;
        }
        .btn-link i {
            margin-right: 6px;
        }
        .btn-link-home {
            background: white;
            border-color: #667eea;
            color: #667eea !important;
        }
        .btn-link-home:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-color: #764ba2;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.35);
        }
        .btn-link-home:active {
            background: linear-gradient(135deg, #4169e1 0%, #6a5acd 100%) !important;
            color: white !important;
            border-color: #4169e1;
            transform: scale(0.96);
            box-shadow: 0 2px 8px rgba(65, 105, 225, 0.5);
            transition: all 0.05s ease;
        }
        .btn-link-home:visited {
            color: #667eea !important;
        }
        .btn-link-home:visited:hover {
            color: white !important;
        }
        .btn-link-home:visited:active {
            color: white !important;
        }
        @media (max-width: 768px) {
            .register-container { margin: 70px auto 20px; padding: 15px; }
            .register-body { padding: 30px 25px; }
            .register-header { padding: 35px 25px; }
            .register-header h2 { font-size: 1.5rem; }
            .logo-circle { width: 70px; height: 70px; }
            .logo-circle i { font-size: 2rem; }
        }
        @media (max-width: 480px) {
            body { padding: 10px 5px; }
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
            .btn-link { display: block; margin: 10px auto; max-width: 280px; }
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
                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="flex: 1;">
                            <label style="display: block; color: #4a5568; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;"><i class="fas fa-user" style="color: #ec6ead; margin-right: 8px;"></i> First Name</label>
                            <input type="text" required class="form-control" name="u_fname" placeholder="First name">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; color: #4a5568; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;"><i class="fas fa-user" style="color: #ec6ead; margin-right: 8px;"></i> Last Name</label>
                            <input type="text" required class="form-control" name="u_lname" placeholder="Last name">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="flex: 1;">
                            <label style="display: block; color: #4a5568; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;"><i class="fas fa-phone" style="color: #ec6ead; margin-right: 8px;"></i> Mobile Number</label>
                            <div style="position: relative;">
                                <input type="tel" required class="form-control" id="u_phone" name="u_phone" placeholder="10-digit mobile" pattern="[0-9]{10}" maxlength="10" title="Enter exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10); checkPhoneAvailability(this.value);">
                                <span id="phone-status" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 1.2rem;"></span>
                            </div>
                            <small id="phone-message" style="display: block; margin-top: 5px; font-size: 0.85rem;"></small>
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; color: #4a5568; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;"><i class="fas fa-envelope" style="color: #ec6ead; margin-right: 8px;"></i> Email</label>
                            <input type="email" required class="form-control" name="u_email" placeholder="Email address">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <input type="text" required class="form-control" name="u_addr" placeholder="Enter your complete address">
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="flex: 1;">
                            <label style="display: block; color: #4a5568; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;"><i class="fas fa-map-signs" style="color: #ec6ead; margin-right: 8px;"></i> Area</label>
                            <input type="text" required class="form-control" name="u_area" placeholder="Area or locality">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; color: #4a5568; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;"><i class="fas fa-map-pin" style="color: #ec6ead; margin-right: 8px;"></i> Pincode</label>
                            <input type="text" required class="form-control" name="u_pincode" placeholder="6-digit pincode" pattern="[0-9]{6}" maxlength="6">
                        </div>
                    </div>
                    
                    <div class="form-group" style="display:none">
                        <input type="text" class="form-control" value="User" name="u_category">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div style="position: relative;">
                            <input type="password" required class="form-control" name="u_pwd" id="registerPassword" placeholder="Create a strong password" minlength="6">
                            <i class="fas fa-eye" id="toggleRegisterPassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #ec6ead;"></i>
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
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                        <a href="index.php" class="btn-link" style="flex: 1; min-width: 130px;"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="usr-forgot-password.php" class="btn-link" style="flex: 1; min-width: 130px;"><i class="fas fa-key"></i> Reset Password</a>
                    </div>
                    <a href="../index.php" class="btn-link btn-link-home" style="display: block; margin-top: 10px;"><i class="fas fa-home"></i> Home</a>
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
        
        // Real-time phone number availability check
        let phoneCheckTimeout;
        function checkPhoneAvailability(phone) {
            const statusIcon = document.getElementById('phone-status');
            const messageText = document.getElementById('phone-message');
            const submitBtn = document.querySelector('button[name="add_user"]');
            
            // Clear previous timeout
            clearTimeout(phoneCheckTimeout);
            
            // Reset if empty
            if(phone.length === 0) {
                statusIcon.innerHTML = '';
                messageText.innerHTML = '';
                messageText.style.color = '';
                return;
            }
            
            // Show checking status
            if(phone.length === 10) {
                statusIcon.innerHTML = '<i class="fas fa-spinner fa-spin" style="color: #ffc107;"></i>';
                messageText.innerHTML = 'Checking availability...';
                messageText.style.color = '#ffc107';
                
                // Delay the check to avoid too many requests
                phoneCheckTimeout = setTimeout(function() {
                    // Make AJAX request
                    fetch('check-phone-availability.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'phone=' + encodeURIComponent(phone)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.available) {
                            statusIcon.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i>';
                            messageText.innerHTML = '✓ ' + data.message;
                            messageText.style.color = '#28a745';
                            submitBtn.disabled = false;
                        } else {
                            statusIcon.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i>';
                            messageText.innerHTML = '✗ ' + data.message;
                            messageText.style.color = '#dc3545';
                            submitBtn.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        statusIcon.innerHTML = '';
                        messageText.innerHTML = '';
                    });
                }, 500); // Wait 500ms after user stops typing
            } else {
                statusIcon.innerHTML = '';
                messageText.innerHTML = 'Enter 10-digit mobile number';
                messageText.style.color = '#718096';
            }
        }
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