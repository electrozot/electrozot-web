<?php
// Include this file at the top of every technician dashboard page
// Usage: include('check-account-status.php');

if(!isset($_SESSION['t_id'])) {
    header("location: index.php");
    exit();
}

$tech_id = $_SESSION['t_id'];

// Check if account is locked
$check_query = "SELECT account_locked, lock_reason, locked_at FROM tms_technician WHERE t_id = ?";
$stmt_check = $mysqli->prepare($check_query);
$stmt_check->bind_param('i', $tech_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$tech_status = $result_check->fetch_object();
$stmt_check->close();

if($tech_status && $tech_status->account_locked == 1) {
    // Account is locked - show blocked page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Account Locked - Electrozot</title>
        <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../admin/vendor/fontawesome-free/css/all.min.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .lock-container {
                max-width: 500px;
                width: 100%;
                padding: 20px;
            }
            .lock-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                overflow: hidden;
            }
            .lock-header {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                padding: 30px;
                text-align: center;
            }
            .lock-icon {
                font-size: 80px;
                color: white;
                animation: shake 0.5s infinite;
            }
            @keyframes shake {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(-10deg); }
                75% { transform: rotate(10deg); }
            }
            .lock-body {
                padding: 30px;
            }
            .amount-box {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
            }
            .contact-box {
                background: #d1ecf1;
                border-left: 4px solid #17a2b8;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="lock-container">
            <div class="lock-card">
                <div class="lock-header">
                    <i class="fas fa-lock lock-icon"></i>
                    <h3 class="text-white mt-3 mb-0">Account Locked</h3>
                </div>
                <div class="lock-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Your account has been temporarily locked</strong>
                    </div>
                    
                    <h5 class="text-danger mb-3">
                        <i class="fas fa-info-circle"></i> Reason:
                    </h5>
                    <p><?php echo htmlspecialchars($tech_status->lock_reason); ?></p>
                    
                    <div class="amount-box">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-rupee-sign"></i> Payment Required
                        </h6>
                        <p class="mb-0">Please complete your charges payment to Electrozot.</p>
                    </div>
                    
                    <div class="contact-box">
                        <h6 class="text-info mb-2">
                            <i class="fas fa-phone"></i> Next Steps:
                        </h6>
                        <ol class="mb-0 pl-3">
                            <li>Complete your charges payment</li>
                            <li>Call EZ Admin to confirm payment</li>
                            <li>Admin will unlock your account</li>
                        </ol>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted mb-2">
                            <small>Locked on: <?php echo date('d M Y h:i A', strtotime($tech_status->locked_at)); ?></small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
