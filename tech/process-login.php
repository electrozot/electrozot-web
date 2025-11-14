<?php
  session_start();
  include('../admin/vendor/inc/config.php');

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
    $_SESSION['tech_err'] = 'Technician not found with this mobile number.';
    header('Location: index.php');
    exit();
  }

  // Prefer technician password if column exists and is set; fallback to ID match
  $usePwd = isset($row->t_pwd);
  if($usePwd){
    if($row->t_pwd === ''){
      $_SESSION['tech_err'] = 'Password not set. Please contact Admin.';
      header('Location: index.php');
      exit();
    }
    if($t_pwd !== $row->t_pwd){
      $_SESSION['tech_err'] = 'Invalid password.';
      header('Location: index.php');
      exit();
    }
  } else {
    // Legacy fallback: password equals Technician ID
    if($t_pwd !== $row->t_id_no){
      $_SESSION['tech_err'] = 'Invalid password.';
      header('Location: index.php');
      exit();
    }
  }

  // Success: open session
  $_SESSION['t_id'] = $row->t_id;
  $_SESSION['t_name'] = $row->t_name;
  $_SESSION['t_id_no'] = $row->t_id_no;
  header('Location: dashboard.php');
  exit();
?>