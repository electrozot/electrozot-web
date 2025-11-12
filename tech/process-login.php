<?php
  session_start();
  include('../admin/vendor/inc/config.php');

  if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    
    header('Location: index.php');
    exit();
  }

  $t_id_no = isset($_POST['t_id_no']) ? trim($_POST['t_id_no']) : '';
  $t_pwd   = isset($_POST['t_pwd']) ? trim($_POST['t_pwd']) : '';

  if($t_id_no === '' || $t_pwd === ''){
    $_SESSION['tech_err'] = 'Please provide Technician ID and Password.';
    header('Location: index.php');
    exit();
  }

  // Find technician by ID number
  $ret = "SELECT * FROM tms_technician WHERE t_id_no = ? LIMIT 1";
  $stmt = $mysqli->prepare($ret);
  if(!$stmt){
    $_SESSION['tech_err'] = 'Database error: ' . $mysqli->error;
    header('Location: index.php');
    exit();
  }
  $stmt->bind_param('s', $t_id_no);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_object();

  if(!$row){
    $_SESSION['tech_err'] = 'Technician not found.';
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