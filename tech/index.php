<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Technician Login</title>
  <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
  <style>
    :root {
      --g1: #00c853; /* green */
      --g2: #00b0ff; /* blue */
      --card-bg: #ffffff;
    }
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, var(--g1), var(--g2));
      display: flex;
      flex-direction: column;
    }
    .navbar {
      background: rgba(255,255,255,0.15);
      backdrop-filter: saturate(180%) blur(8px);
    }
    .navbar .navbar-brand { color: #fff; font-weight: 600; }
    .login-card {
      max-width: 440px;
      margin: 9vh auto;
      border: none;
      border-radius: 14px;
      box-shadow: 0 10px 24px rgba(0,0,0,0.15);
      overflow: hidden;
    }
    .card-header {
      background: linear-gradient(135deg, rgba(0,200,83,0.95), rgba(0,176,255,0.95));
      color: #fff;
      font-weight: 600;
      padding: 14px 18px;
    }
    .card-body { background: var(--card-bg); padding: 18px; }
    .form-control { border-radius: 10px; }
    .btn-gradient {
      color: #fff;
      background: linear-gradient(135deg, var(--g1), var(--g2));
      border: none;
      border-radius: 10px;
      box-shadow: 0 6px 14px rgba(0,176,255,0.25);
    }
    .btn-gradient:hover { opacity: 0.95; }
    .helper { color: #6c757d; font-size: 0.85rem; }
  </style>
  </head>
<body>
  <nav class="navbar navbar-expand navbar-dark">
    <a class="navbar-brand" href="../index.php">Electrozot</a>
  </nav>
  <div class="card login-card">
    <div class="card-header">Technician Login</div>
    <div class="card-body">
      <?php if(isset($_SESSION['tech_err'])): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $_SESSION['tech_err']; unset($_SESSION['tech_err']); ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="process-login.php">
        <div class="form-group">
          <label for="t_id_no">Technician ID</label>
          <input type="text" name="t_id_no" id="t_id_no" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="t_pwd">Password</label>
          <input type="password" name="t_pwd" id="t_pwd" class="form-control" required>
          <small class="helper">If you don’t have a password yet, ask Admin.</small>
        </div>
        <button type="submit" class="btn btn-gradient btn-block">Login</button>
      </form>
    </div>
  </div>
  <script src="../admin/vendor/jquery/jquery.min.js"></script>
  <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>