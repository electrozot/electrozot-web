<?php
  // Include PWA session fix for production compatibility
  include('pwa-session-fix.php');
  include('../admin/vendor/inc/config.php');
  
  // Configure PWA-compatible session
  configure_pwa_session();
  session_start();

  // Check if technician is already logged in - redirect to last page or dashboard
  if(isset($_SESSION['t_id']) && strlen($_SESSION['t_id']) > 0) {
      // Technician is already logged in, redirect to last visited page or dashboard
      $redirect_page = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : 'dashboard.php';
      header("location: $redirect_page");
      exit;
  }

  if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    
    header('Location: index.php');
    exit();
  }

  $t_phone = isset($_POST['t_phone']) ? trim($_POST['t_phone']) : '';
  $t_pwd   = isset($_POST['t_pwd']) ? trim($_POST['t_pwd']) : '';

  if($t_phone === '' || $t_pwd === ''){
    $_SESSION['tech_err'] = 'Please provide Mobile Number and Password.';
    header('Location: index.php');
    exit();
  }

  // Add phone column if it doesn't exist
  $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(15) DEFAULT NULL");
  
  // Find technician by phone number or ID number (for backward compatibility)
  $ret = "SELECT * FROM tms_technician WHERE t_phone = ? OR t_id_no = ? LIMIT 1";
  $stmt = $mysqli->prepare($ret);
  if(!$stmt){
    $_SESSION['tech_err'] = 'Database error: ' . $mysqli->error;
    header('Location: index.php');
    exit();
  }
  $stmt->bind_param('ss', $t_phone, $t_phone);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_object();

  if(!$row){
    $_SESSION['tech_err'] = 'Mobile number not registered. Please check your number or contact Admin.';
    header('Location: index.php');
    exit();
  }

  // Check if account is locked
  if($row->t_status === 'Locked') {
    // Check if block period has expired
    if($row->t_blocked_until && strtotime($row->t_blocked_until) > time()) {
      $blocked_until = date('M d, Y h:i A', strtotime($row->t_blocked_until));
      $_SESSION['tech_err'] = '🔒 Your account is temporarily locked until ' . $blocked_until . '. Reason: ' . ($row->t_block_reason ?? 'Admin action') . '. Please contact admin for assistance.';
      header('Location: index.php');
      exit();
    } else {
      // Block period expired, auto-unlock
      $mysqli->query("UPDATE tms_technician SET t_status = 'Available', t_blocked_until = NULL, t_block_reason = NULL WHERE t_id = {$row->t_id}");
    }
  }

  // Prefer technician password if column exists and is set; fallback to ID match
  $usePwd = isset($row->t_pwd);
  if($usePwd){
    if($row->t_pwd === ''){
      $_SESSION['tech_err'] = 'Password not set for your account. Please contact Admin to set your password.';
      header('Location: index.php');
      exit();
    }
    
    // Check if password is hashed (starts with $2y$ for bcrypt)
    $is_hashed = (strpos($row->t_pwd, '$2y$') === 0 || strpos($row->t_pwd, '$2a$') === 0 || strpos($row->t_pwd, '$2b$') === 0);
    
    if($is_hashed) {
      // Use password_verify for hashed passwords (guest technicians)
      if(!password_verify($t_pwd, $row->t_pwd)){
        $_SESSION['tech_err'] = 'Incorrect password. Please try again or contact Admin if you forgot your password.';
        header('Location: index.php');
        exit();
      }
    } else {
      // Plain text comparison for admin-created technicians
      if($t_pwd !== $row->t_pwd){
        $_SESSION['tech_err'] = 'Incorrect password. Please try again or contact Admin if you forgot your password.';
        header('Location: index.php');
        exit();
      }
    }
  } else {
    // Legacy fallback: password equals Technician ID
    if($t_pwd !== $row->t_id_no){
      $_SESSION['tech_err'] = 'Incorrect password. Please try again or contact Admin if you forgot your password.';
      header('Location: index.php');
      exit();
    }
  }

  // Success: open session
  $_SESSION['t_id'] = $row->t_id;
  $_SESSION['t_name'] = $row->t_name;
  $_SESSION['t_id_no'] = $row->t_id_no;
  $_SESSION['login_time'] = time();
  
  // Regenerate session ID for security
  session_regenerate_id(true);
  
  // Detect if production PWA environment
  $is_production_pwa = $_SERVER['HTTP_HOST'] !== 'localhost';
  
  // Set permanent cookie for production PWA or when remember me is checked
  $remember_me = isset($_POST['remember_me']) ? true : false;
  if($remember_me || $is_production_pwa) {
      // Cookie will last 10 years (effectively permanent until logout)
      $cookie_lifetime = time() + 315360000; // 10 years
      $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                  $_SERVER['SERVER_PORT'] == 443 ||
                  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                  $is_production_pwa; // Assume production uses HTTPS
      
      setcookie(session_name(), session_id(), $cookie_lifetime, '/', '', $is_https, true);
  }
  
  // Redirect to last visited page or dashboard
  $redirect_page = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : 'dashboard.php';
  header("Location: $redirect_page");
  exit();
?>