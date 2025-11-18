<?php
session_start();
include('../admin/vendor/inc/config.php');

// Ensure required columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_experience VARCHAR(50) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_skills TEXT DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_aadhar VARCHAR(12) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_status VARCHAR(20) DEFAULT 'Active'");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_is_guest TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_registered_at TIMESTAMP NULL DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_registration_ip VARCHAR(45) DEFAULT NULL");
} catch(Exception $e) {}

// Create registration attempts tracking table
try {
    $create_attempts_table = "CREATE TABLE IF NOT EXISTS tms_registration_attempts (
        ra_id INT AUTO_INCREMENT PRIMARY KEY,
        ra_phone VARCHAR(15) NOT NULL,
        ra_email VARCHAR(100) NOT NULL,
        ra_aadhar VARCHAR(12) NOT NULL,
        ra_ip VARCHAR(45) NOT NULL,
        ra_attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(ra_phone),
        INDEX(ra_email),
        INDEX(ra_aadhar),
        INDEX(ra_ip),
        INDEX(ra_attempted_at)
    )";
    $mysqli->query($create_attempts_table);
} catch(Exception $e) {}

// Handle registration submission
if(isset($_POST['register_technician'])){
    $t_name = trim($_POST['t_name']);
    $t_phone = trim($_POST['t_phone']);
    $t_email = trim($_POST['t_email']);
    $t_addr = trim($_POST['t_addr']);
    $t_pwd = $_POST['t_pwd'];
    $t_pwd_confirm = $_POST['t_pwd_confirm'];
    $t_service_pincode = trim($_POST['t_service_pincode']);
    $t_experience = trim($_POST['t_experience']);
    $t_skills = trim($_POST['t_skills']);
    $t_aadhar = trim($_POST['t_aadhar']);
    
    // Get user IP
    $user_ip = $_SERVER['REMOTE_ADDR'];
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    // Validation
    $errors = [];
    
    // Check if already registered as regular EZ Technician (approved)
    $existing_tech_check = "SELECT t_id, t_ez_id, t_name FROM tms_technician 
                           WHERE (t_phone = ? OR t_aadhar = ?) 
                           AND (t_is_guest = 0 OR t_status = 'Available' OR t_status = 'Booked')";
    $existing_stmt = $mysqli->prepare($existing_tech_check);
    $existing_stmt->bind_param('ss', $t_phone, $t_aadhar);
    $existing_stmt->execute();
    $existing_result = $existing_stmt->get_result();
    
    if($existing_result->num_rows > 0){
        $existing_tech = $existing_result->fetch_assoc();
        $errors[] = "You are already registered as an EZ Technician (EZ ID: " . htmlspecialchars($existing_tech['t_ez_id']) . "). Please login with your existing credentials. If you forgot your password, contact admin.";
    }
    
    // Check for duplicate registration attempts (phone, email, or Aadhar)
    $duplicate_check = "SELECT t_id FROM tms_technician WHERE (t_phone = ? OR t_email = ? OR t_aadhar = ?) AND t_is_guest = 1 AND t_status = 'Pending'";
    $dup_stmt = $mysqli->prepare($duplicate_check);
    $dup_stmt->bind_param('sss', $t_phone, $t_email, $t_aadhar);
    $dup_stmt->execute();
    if($dup_stmt->get_result()->num_rows > 0){
        $errors[] = "A registration request with this phone, email, or Aadhaar is already pending approval. Please wait for admin confirmation.";
    }
    
    // Check registration attempts in last 24 hours from this IP
    $ip_check = "SELECT COUNT(*) as attempt_count FROM tms_registration_attempts 
                 WHERE ra_ip = ? AND ra_attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $ip_stmt = $mysqli->prepare($ip_check);
    $ip_stmt->bind_param('s', $user_ip);
    $ip_stmt->execute();
    $ip_result = $ip_stmt->get_result()->fetch_assoc();
    
    if($ip_result['attempt_count'] >= 3){
        $errors[] = "Maximum 3 registration requests allowed per 24 hours. Please try again later.";
    }
    
    // Check if phone/email/Aadhar was used in last 24 hours
    $data_check = "SELECT COUNT(*) as data_count FROM tms_registration_attempts 
                   WHERE (ra_phone = ? OR ra_email = ? OR ra_aadhar = ?) 
                   AND ra_attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $data_stmt = $mysqli->prepare($data_check);
    $data_stmt->bind_param('sss', $t_phone, $t_email, $t_aadhar);
    $data_stmt->execute();
    $data_result = $data_stmt->get_result()->fetch_assoc();
    
    if($data_result['data_count'] > 0){
        $errors[] = "This phone number, email, or Aadhaar was already used for registration in the last 24 hours.";
    }
    
    if(empty($t_name)) $errors[] = "Name is required";
    if(empty($t_phone) || !preg_match('/^[0-9]{10}$/', $t_phone)) $errors[] = "Valid 10-digit phone number is required";
    if(empty($t_email) || !filter_var($t_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if(empty($t_addr)) $errors[] = "Address is required";
    if(empty($t_service_pincode) || !preg_match('/^[0-9]{6}$/', $t_service_pincode)) $errors[] = "Valid 6-digit pincode is required";
    if(empty($t_pwd) || strlen($t_pwd) < 6) $errors[] = "Password must be at least 6 characters";
    if($t_pwd !== $t_pwd_confirm) $errors[] = "Passwords do not match";
    if(empty($t_aadhar) || !preg_match('/^[0-9]{12}$/', $t_aadhar)) $errors[] = "Valid 12-digit Aadhaar number is required";
    
    // Check if phone already exists
    $check_query = "SELECT t_id FROM tms_technician WHERE t_phone = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('s', $t_phone);
    $check_stmt->execute();
    if($check_stmt->get_result()->num_rows > 0){
        $errors[] = "Phone number already registered";
    }
    
    // Check if email already exists
    $check_email_query = "SELECT t_id FROM tms_technician WHERE t_email = ?";
    $check_email_stmt = $mysqli->prepare($check_email_query);
    $check_email_stmt->bind_param('s', $t_email);
    $check_email_stmt->execute();
    if($check_email_stmt->get_result()->num_rows > 0){
        $errors[] = "Email already registered";
    }
    
    // Validate image upload
    if(isset($_FILES['t_pic']) && $_FILES['t_pic']['error'] == 0){
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['t_pic']['name'];
        $filetype = $_FILES['t_pic']['type'];
        $filesize = $_FILES['t_pic']['size'];
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(!in_array($ext, $allowed)){
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed";
        }
        
        if($filesize > 2097152){ // 2MB
            $errors[] = "File size must be less than 2MB";
        }
    } else {
        $errors[] = "Profile photo is required";
    }
    
    if(empty($errors)){
        // Handle image upload
        $t_pic = time() . '_' . $_FILES['t_pic']['name'];
        $upload_path = "../vendor/img/" . $t_pic;
        
        if(!move_uploaded_file($_FILES['t_pic']['tmp_name'], $upload_path)){
            $errors[] = "Failed to upload image";
        }
    }
    
    if(empty($errors)){
        // Generate temporary ID
        $t_id_no = 'GUEST-' . strtoupper(substr(md5(time() . $t_phone), 0, 8));
        
        // Hash password
        $hashed_pwd = password_hash($t_pwd, PASSWORD_DEFAULT);
        
        // Insert as guest technician (pending approval)
        $insert_query = "INSERT INTO tms_technician (t_name, t_phone, t_email, t_addr, t_pwd, t_id_no, t_service_pincode, t_experience, t_skills, t_aadhar, t_pic, t_status, t_is_guest, t_registered_at, t_registration_ip) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 1, NOW(), ?)";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param('ssssssssssss', $t_name, $t_phone, $t_email, $t_addr, $hashed_pwd, $t_id_no, $t_service_pincode, $t_experience, $t_skills, $t_aadhar, $t_pic, $user_ip);
        
        if($stmt->execute()){
            // Log the registration attempt
            $log_attempt = "INSERT INTO tms_registration_attempts (ra_phone, ra_email, ra_aadhar, ra_ip) VALUES (?, ?, ?, ?)";
            $log_stmt = $mysqli->prepare($log_attempt);
            $log_stmt->bind_param('ssss', $t_phone, $t_email, $t_aadhar, $user_ip);
            $log_stmt->execute();
            
            $_SESSION['registration_success'] = "Registration successful! Your application is pending admin approval. You will be notified once approved.";
            header('Location: index.php');
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Technician Registration - Electrozot</title>
  <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #0575E6 0%, #00F260 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 40px 20px;
    }

    .registration-container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    .registration-header {
      background: linear-gradient(135deg, #0575E6 0%, #00F260 100%);
      padding: 40px;
      text-align: center;
      color: white;
    }

    .registration-header i {
      font-size: 3rem;
      margin-bottom: 15px;
    }

    .registration-header h2 {
      font-size: 2rem;
      font-weight: 900;
      margin-bottom: 10px;
    }

    .registration-header p {
      font-size: 1rem;
      opacity: 0.95;
    }

    .registration-body {
      padding: 40px;
    }

    .form-section {
      margin-bottom: 30px;
    }

    .section-title {
      font-size: 1.3rem;
      font-weight: 800;
      color: #0575E6;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 3px solid #00F260;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-group label i {
      color: #0575E6;
    }

    .form-control {
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      padding: 12px 15px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #0575E6;
      box-shadow: 0 0 0 3px rgba(5, 117, 230, 0.1);
      outline: none;
    }

    .btn-register {
      background: linear-gradient(135deg, #0575E6 0%, #00F260 100%);
      color: white;
      padding: 15px 40px;
      border: none;
      border-radius: 50px;
      font-weight: 800;
      font-size: 1.1rem;
      width: 100%;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(5, 117, 230, 0.3);
    }

    .btn-register:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(5, 117, 230, 0.4);
    }

    .alert {
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 20px;
    }

    .back-link {
      text-align: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 2px solid #e2e8f0;
    }

    .back-link a {
      color: #0575E6;
      text-decoration: none;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .back-link a:hover {
      color: #00F260;
    }

    .required {
      color: #ff4757;
    }

    @media (max-width: 768px) {
      .registration-body {
        padding: 20px;
      }

      .registration-header {
        padding: 30px 20px;
      }

      .registration-header h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="registration-container">
    <div class="registration-header">
      <i class="fas fa-user-plus"></i>
      <h2>Technician Registration</h2>
      <p>Join Electrozot as a Professional Technician</p>
    </div>

    <div class="registration-body">
      <?php if(isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
          <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
          <ul style="margin: 10px 0 0 20px;">
            <?php foreach($errors as $error): ?>
              <li><?php echo $error; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="" enctype="multipart/form-data">
        <!-- Personal Information -->
        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-user"></i>
            Personal Information
          </div>

          <div class="form-group">
            <label for="t_name">
              <i class="fas fa-id-card"></i>
              Full Name <span class="required">*</span>
            </label>
            <input type="text" name="t_name" id="t_name" class="form-control" placeholder="Enter your full name" required value="<?php echo isset($_POST['t_name']) ? htmlspecialchars($_POST['t_name']) : ''; ?>">
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="t_phone">
                  <i class="fas fa-mobile-alt"></i>
                  Mobile Number <span class="required">*</span>
                </label>
                <input type="tel" name="t_phone" id="t_phone" class="form-control" placeholder="10-digit mobile number" required pattern="[0-9]{10}" maxlength="10" value="<?php echo isset($_POST['t_phone']) ? htmlspecialchars($_POST['t_phone']) : ''; ?>">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="t_email">
                  <i class="fas fa-envelope"></i>
                  Email Address <span class="required">*</span>
                </label>
                <input type="email" name="t_email" id="t_email" class="form-control" placeholder="your.email@example.com" required value="<?php echo isset($_POST['t_email']) ? htmlspecialchars($_POST['t_email']) : ''; ?>">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="t_aadhar">
              <i class="fas fa-id-card-alt"></i>
              Aadhaar Number <span class="required">*</span>
            </label>
            <input type="text" name="t_aadhar" id="t_aadhar" class="form-control" placeholder="12-digit Aadhaar number" required pattern="[0-9]{12}" maxlength="12" value="<?php echo isset($_POST['t_aadhar']) ? htmlspecialchars($_POST['t_aadhar']) : ''; ?>">
          </div>

          <div class="form-group">
            <label for="t_pic">
              <i class="fas fa-camera"></i>
              Profile Photo <span class="required">*</span>
            </label>
            <input type="file" name="t_pic" id="t_pic" class="form-control" accept="image/*" required>
            <small class="text-muted">Upload a clear photo for your ID card (JPG, PNG, max 2MB)</small>
          </div>

          <div class="form-group">
            <label for="t_addr">
              <i class="fas fa-map-marker-alt"></i>
              Complete Address <span class="required">*</span>
            </label>
            <textarea name="t_addr" id="t_addr" class="form-control" rows="3" placeholder="Enter your complete address" required><?php echo isset($_POST['t_addr']) ? htmlspecialchars($_POST['t_addr']) : ''; ?></textarea>
          </div>

          <div class="form-group">
            <label for="t_service_pincode">
              <i class="fas fa-map-pin"></i>
              Service Pincode <span class="required">*</span>
            </label>
            <input type="text" name="t_service_pincode" id="t_service_pincode" class="form-control" placeholder="6-digit pincode where you provide service" required pattern="[0-9]{6}" maxlength="6" value="<?php echo isset($_POST['t_service_pincode']) ? htmlspecialchars($_POST['t_service_pincode']) : ''; ?>">
          </div>
        </div>

        <!-- Professional Information -->
        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-briefcase"></i>
            Professional Information
          </div>

          <div class="form-group">
            <label for="t_experience">
              <i class="fas fa-clock"></i>
              Years of Experience
            </label>
            <input type="text" name="t_experience" id="t_experience" class="form-control" placeholder="e.g., 5 years" value="<?php echo isset($_POST['t_experience']) ? htmlspecialchars($_POST['t_experience']) : ''; ?>">
          </div>

          <div class="form-group">
            <label for="t_skills">
              <i class="fas fa-tools"></i>
              Skills & Expertise
            </label>
            <textarea name="t_skills" id="t_skills" class="form-control" rows="3" placeholder="List your skills (e.g., Electrical work, Plumbing, AC repair, etc.)"><?php echo isset($_POST['t_skills']) ? htmlspecialchars($_POST['t_skills']) : ''; ?></textarea>
          </div>
        </div>

        <!-- Account Security -->
        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-lock"></i>
            Account Security
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="t_pwd">
                  <i class="fas fa-key"></i>
                  Password <span class="required">*</span>
                </label>
                <input type="password" name="t_pwd" id="t_pwd" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="t_pwd_confirm">
                  <i class="fas fa-check-circle"></i>
                  Confirm Password <span class="required">*</span>
                </label>
                <input type="password" name="t_pwd_confirm" id="t_pwd_confirm" class="form-control" placeholder="Re-enter password" required minlength="6">
              </div>
            </div>
          </div>
        </div>

        <button type="submit" name="register_technician" class="btn-register">
          <i class="fas fa-paper-plane"></i>
          Submit Registration
        </button>
      </form>

      <div class="back-link">
        <a href="index.php">
          <i class="fas fa-arrow-left"></i>
          Back to Login
        </a>
      </div>
    </div>
  </div>

  <script src="../admin/vendor/jquery/jquery.min.js"></script>
  <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
