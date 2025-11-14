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
    :root {
      --primary: #667eea;
      --secondary: #764ba2;
      --accent: #ffd700;
      --card-bg: #ffffff;
    }
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
      background: rgba(255, 215, 0, 0.1);
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
    
    .navbar {
      background: rgba(255,255,255,0.15);
      backdrop-filter: saturate(180%) blur(10px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: relative;
      z-index: 10;
    }
    
    .navbar .navbar-brand { 
      display: flex;
      align-items: center;
      padding: 5px;
    }
    
    .logo-container {
      width: 50px;
      height: 50px;
      background: white;
      border-radius: 10px;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .logo-container img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    
    .login-container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      z-index: 1;
    }
    
    .login-card {
      max-width: 480px;
      width: 100%;
      border: none;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      overflow: hidden;
      border: 3px solid var(--accent);
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: #fff;
      font-weight: 700;
      padding: 30px;
      text-align: center;
      border-bottom: 4px solid var(--accent);
    }
    
    .card-header h3 {
      margin: 0;
      font-size: 1.8rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .card-header i {
      color: var(--accent);
      margin-right: 12px;
      font-size: 2rem;
    }
    
    .card-body { 
      background: var(--card-bg); 
      padding: 40px; 
    }
    
    .form-group label {
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 10px;
      font-size: 1rem;
    }
    
    .form-group label i {
      color: var(--primary);
      margin-right: 8px;
    }
    
    .form-control { 
      border-radius: 12px;
      border: 2px solid #e2e8f0;
      padding: 12px 16px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
      transform: translateY(-2px);
    }
    
    .btn-gradient {
      color: #fff;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      border: none;
      border-radius: 50px;
      padding: 14px 32px;
      font-size: 1.1rem;
      font-weight: 700;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
      transition: all 0.3s ease;
    }
    
    .btn-gradient:hover { 
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
    }
    
    .helper { 
      color: #6c757d; 
      font-size: 0.9rem; 
      margin-top: 8px;
      display: block;
    }
    
    .alert {
      border-radius: 12px;
      border: none;
      padding: 15px 20px;
    }
    
    .alert-danger {
      background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
      color: white;
    }
    
    .back-link {
      text-align: center;
      margin-top: 20px;
    }
    
    .back-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .back-link a:hover {
      color: var(--secondary);
      text-decoration: underline;
    }
    
    @media (max-width: 576px) {
      .logo-container {
        width: 45px;
        height: 45px;
      }
      
      .card-header h3 {
        font-size: 1.4rem;
      }
      
      .card-body {
        padding: 25px;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand navbar-dark">
    <a class="navbar-brand" href="../index.php">
      <div class="logo-container">
        <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
      </div>
    </a>
  </nav>
  
  <div class="login-container">
    <div class="card login-card">
      <div class="card-header">
        <h3>
          <i class="fas fa-tools"></i>
          Technician Login
        </h3>
        <p style="margin: 10px 0 0 0; font-size: 0.95rem; opacity: 0.9;">Access your technician dashboard</p>
      </div>
      <div class="card-body">
        <?php if(isset($_SESSION['tech_err'])): ?>
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['tech_err']; unset($_SESSION['tech_err']); ?>
          </div>
        <?php endif; ?>
        
        <form method="POST" action="process-login.php">
          <div class="form-group">
            <label for="t_phone">
              <i class="fas fa-mobile-alt"></i>
              Mobile Number
            </label>
            <input type="tel" name="t_phone" id="t_phone" class="form-control" placeholder="Enter your 10-digit mobile number" required autofocus pattern="[0-9]{10}" maxlength="10">
            <small class="helper">
              <i class="fas fa-info-circle"></i>
              Enter the mobile number registered with your account
            </small>
          </div>
          
          <div class="form-group">
            <label for="t_pwd">
              <i class="fas fa-lock"></i>
              Password
            </label>
            <div style="position: relative;">
              <input type="password" name="t_pwd" id="t_pwd" class="form-control" placeholder="Enter your password" required style="padding-right: 45px;">
              <i class="fas fa-eye toggle-password" id="toggleTechPassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #667eea; font-size: 1.2rem;" onclick="togglePasswordVisibility('t_pwd', 'toggleTechPassword')"></i>
            </div>
            <small class="helper">
              <i class="fas fa-info-circle"></i>
              If you don't have a password yet, please contact Admin.
            </small>
          </div>
          
          <button type="submit" class="btn btn-gradient btn-block">
            <i class="fas fa-sign-in-alt"></i> Login to Dashboard
          </button>
        </form>
        
        <div class="back-link">
          <a href="../index.php">
            <i class="fas fa-arrow-left"></i> Back to Home
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
  </script>
</body>
</html>
