<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Technician Login - Electrozot</title>
  <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #667eea;
      --secondary: #764ba2;
      --accent: #ffd700;
      --dark: #2d3748;
      --light: #f7fafc;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow-x: hidden;
    }
    
    /* Decorative Background Elements */
    body::before {
      content: '';
      position: absolute;
      top: -100px;
      right: -100px;
      width: 400px;
      height: 400px;
      background: rgba(255, 215, 0, 0.08);
      border-radius: 50%;
      z-index: 0;
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
      z-index: 0;
    }

    /* Floating Animation */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    .navbar {
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(20px);
      box-shadow: 0 4px 30px rgba(0,0,0,0.1);
      padding: 15px 0;
      position: relative;
      z-index: 10;
    }
    
    .navbar-brand { 
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 0 20px;
    }
    
    .logo-container {
      width: 55px;
      height: 55px;
      background: white;
      border-radius: 15px;
      padding: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }

    .logo-container:hover {
      transform: scale(1.05) rotate(5deg);
    }
    
    .logo-container img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .brand-text {
      color: white;
      font-size: 1.5rem;
      font-weight: 800;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .login-container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      position: relative;
      z-index: 1;
    }
    
    .login-card {
      max-width: 500px;
      width: 100%;
      background: white;
      border-radius: 25px;
      box-shadow: 0 25px 80px rgba(0,0,0,0.25);
      overflow: hidden;
      animation: slideUp 0.6s ease-out;
      border: 3px solid var(--accent);
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .card-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 40px 30px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .card-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 20%, transparent 20%);
      background-size: 30px 30px;
      animation: headerPattern 15s linear infinite;
    }

    @keyframes headerPattern {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .card-header-content {
      position: relative;
      z-index: 1;
    }

    .header-icon {
      width: 80px;
      height: 80px;
      background: rgba(255,255,255,0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      backdrop-filter: blur(10px);
      border: 3px solid rgba(255,255,255,0.3);
    }

    .header-icon i {
      font-size: 2.5rem;
      color: white;
    }
    
    .card-header h3 {
      margin: 0 0 10px 0;
      font-size: 2rem;
      font-weight: 800;
      letter-spacing: -0.5px;
    }

    .card-header p {
      margin: 0;
      font-size: 1rem;
      opacity: 0.95;
      font-weight: 500;
    }
    
    .card-body { 
      padding: 40px 35px;
    }

    .alert {
      border-radius: 15px;
      border: none;
      padding: 15px 20px;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 12px;
      animation: shake 0.5s ease;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }
    
    .alert-danger {
      background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
    }

    .alert i {
      font-size: 1.3rem;
    }
    
    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 10px;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .form-group label i {
      color: var(--primary);
      margin-right: 8px;
      font-size: 1rem;
    }

    .input-wrapper {
      position: relative;
      width: 100%;
    }
    
    .form-control { 
      width: 100%;
      border-radius: 15px;
      border: 2px solid #e2e8f0;
      padding: 15px 20px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f7fafc;
      font-weight: 500;
      color: var(--dark);
    }

    .form-control::placeholder {
      color: #a0aec0;
      font-weight: 400;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
      transform: translateY(-2px);
    }

    .password-input {
      padding-right: 55px;
    }

    .toggle-password {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #a0aec0;
      font-size: 1.2rem;
      transition: all 0.3s ease;
      z-index: 2;
    }

    .toggle-password:hover {
      color: var(--primary);
      transform: translateY(-50%) scale(1.1);
    }
    
    .helper { 
      color: #718096;
      font-size: 0.85rem;
      margin-top: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 500;
    }

    .helper i {
      font-size: 0.8rem;
      color: var(--primary);
    }

    .security-notice {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
      padding: 18px 20px;
      border-radius: 15px;
      margin-bottom: 25px;
      border-left: 4px solid var(--primary);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .security-notice i {
      font-size: 1.5rem;
      color: var(--primary);
      flex-shrink: 0;
    }

    .security-notice p {
      margin: 0;
      font-size: 0.9rem;
      color: var(--dark);
      font-weight: 600;
      line-height: 1.5;
    }
    
    .btn-gradient {
      width: 100%;
      color: white;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 15px;
      padding: 16px 32px;
      font-size: 1.1rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      transition: all 0.3s ease;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    
    .btn-gradient:hover { 
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .btn-gradient:active {
      transform: translateY(-1px);
    }

    .btn-gradient i {
      font-size: 1.2rem;
    }
    
    .register-link {
      text-align: center;
      margin-top: 20px;
      padding: 20px;
      background: linear-gradient(135deg, rgba(5, 117, 230, 0.1) 0%, rgba(0, 242, 96, 0.1) 100%);
      border-radius: 15px;
      border: 2px solid rgba(5, 117, 230, 0.2);
    }

    .register-link p {
      margin: 0 0 12px 0;
      color: #2d3748;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .register-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, #0575E6 0%, #00F260 100%);
      color: white;
      padding: 12px 30px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 800;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(5, 117, 230, 0.3);
    }

    .register-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(5, 117, 230, 0.4);
      color: white;
      text-decoration: none;
    }

    .register-btn i {
      font-size: 1.1rem;
    }

    .back-link {
      text-align: center;
      margin-top: 25px;
      padding-top: 25px;
      border-top: 2px solid #e2e8f0;
    }
    
    .back-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .back-link a:hover {
      color: var(--secondary);
      gap: 12px;
    }

    .back-link a i {
      transition: transform 0.3s ease;
    }

    .back-link a:hover i {
      transform: translateX(-5px);
    }
    
    @media (max-width: 576px) {
      .navbar-brand {
        padding: 0 15px;
      }

      .logo-container {
        width: 50px;
        height: 50px;
      }

      .brand-text {
        font-size: 1.3rem;
      }
      
      .card-header {
        padding: 35px 25px;
      }

      .header-icon {
        width: 70px;
        height: 70px;
      }

      .header-icon i {
        font-size: 2rem;
      }
      
      .card-header h3 {
        font-size: 1.6rem;
      }

      .card-header p {
        font-size: 0.9rem;
      }
      
      .card-body {
        padding: 30px 25px;
      }

      .form-control {
        padding: 13px 18px;
        font-size: 0.95rem;
      }

      .btn-gradient {
        padding: 14px 28px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <a class="navbar-brand" href="../index.php">
      <div class="logo-container">
        <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
      </div>
      <span class="brand-text">Electrozot</span>
    </a>
  </nav>
  
  <!-- Floating Background Icons -->
  <div style="position: absolute; top: 15%; left: 8%; z-index: 0; opacity: 0.12; animation: float 6s ease-in-out infinite;">
    <i class="fas fa-bolt" style="font-size: 3rem; color: #ffd700;"></i>
  </div>
  <div style="position: absolute; top: 60%; right: 10%; z-index: 0; opacity: 0.1; animation: float 8s ease-in-out infinite;">
    <i class="fas fa-tools" style="font-size: 2.5rem; color: white;"></i>
  </div>
  <div style="position: absolute; bottom: 20%; left: 12%; z-index: 0; opacity: 0.08; animation: rotate 20s linear infinite;">
    <i class="fas fa-cog" style="font-size: 2rem; color: #ffd700;"></i>
  </div>
  <div style="position: absolute; top: 25%; right: 25%; z-index: 0; opacity: 0.08; animation: float 7s ease-in-out infinite;">
    <i class="fas fa-wrench" style="font-size: 2.2rem; color: white;"></i>
  </div>
  
  <div class="login-container">
    <div class="login-card">
      <div class="card-header">
        <div class="card-header-content">
          <div class="header-icon">
            <i class="fas fa-tools"></i>
          </div>
          <h3>Technician Login</h3>
          <p>Access your technician dashboard</p>
        </div>
      </div>
      
      <div class="card-body">
        <?php if(isset($_SESSION['registration_success'])): ?>
          <div class="alert alert-success" role="alert" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $_SESSION['registration_success']; unset($_SESSION['registration_success']); ?></span>
          </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['tech_err'])): ?>
          <div class="alert alert-danger" role="alert" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $_SESSION['tech_err']; unset($_SESSION['tech_err']); ?></span>
          </div>
        <?php endif; ?>
        
        <form method="POST" action="process-login.php" id="loginForm">
          <div class="form-group">
            <label for="t_phone">
              <i class="fas fa-mobile-alt"></i>
              Mobile Number
            </label>
            <div class="input-wrapper">
              <input 
                type="tel" 
                name="t_phone" 
                id="t_phone" 
                class="form-control" 
                placeholder="Enter 10-digit mobile number" 
                required 
                autofocus 
                pattern="[0-9]{10}" 
                maxlength="10"
              >
            </div>
            <small class="helper">
              <i class="fas fa-info-circle"></i>
              <span>Enter the mobile number registered with your account</span>
            </small>
          </div>
          
          <div class="form-group">
            <label for="t_pwd">
              <i class="fas fa-lock"></i>
              Password
            </label>
            <div class="input-wrapper">
              <input 
                type="password" 
                name="t_pwd" 
                id="t_pwd" 
                class="form-control password-input" 
                placeholder="Enter your password" 
                required
              >
              <i 
                class="fas fa-eye toggle-password" 
                id="toggleTechPassword" 
                onclick="togglePasswordVisibility('t_pwd', 'toggleTechPassword')"
              ></i>
            </div>
            <small class="helper">
              <i class="fas fa-info-circle"></i>
              <span>Forgot password? Contact Admin for password reset</span>
            </small>
          </div>
          
          <div class="security-notice">
            <i class="fas fa-shield-alt"></i>
            <p>Your account is secure. Never share your password with anyone.</p>
          </div>
          
          <button type="submit" class="btn-gradient">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login to Dashboard</span>
          </button>
        </form>
        
        <div class="register-link">
          <p>New Technician?</p>
          <a href="register.php" class="register-btn">
            <i class="fas fa-user-plus"></i>
            <span>Register as Technician</span>
          </a>
        </div>

        <div class="back-link">
          <a href="../index.php">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Home</span>
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <script src="../admin/vendor/jquery/jquery.min.js"></script>
  <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  
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
  
  // Hide error message when user starts typing
  document.addEventListener('DOMContentLoaded', function() {
      const errorAlert = document.getElementById('errorAlert');
      const phoneInput = document.getElementById('t_phone');
      const passwordInput = document.getElementById('t_pwd');
      
      if (errorAlert) {
          // Hide error when user types in mobile number field
          phoneInput.addEventListener('input', function() {
              errorAlert.style.transition = 'opacity 0.3s ease, max-height 0.3s ease';
              errorAlert.style.opacity = '0';
              errorAlert.style.maxHeight = '0';
              errorAlert.style.overflow = 'hidden';
              errorAlert.style.marginBottom = '0';
              errorAlert.style.padding = '0';
              setTimeout(function() {
                  errorAlert.style.display = 'none';
              }, 300);
          });
          
          // Hide error when user types in password field
          passwordInput.addEventListener('input', function() {
              errorAlert.style.transition = 'opacity 0.3s ease, max-height 0.3s ease';
              errorAlert.style.opacity = '0';
              errorAlert.style.maxHeight = '0';
              errorAlert.style.overflow = 'hidden';
              errorAlert.style.marginBottom = '0';
              errorAlert.style.padding = '0';
              setTimeout(function() {
                  errorAlert.style.display = 'none';
              }, 300);
          });
      }
  });
  </script>
</body>
</html>
