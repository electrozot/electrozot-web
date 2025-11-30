<?php
// Include this file at the top of every technician dashboard page
// Usage: include('check-account-status.php');

if(!isset($_SESSION['t_id'])) {
    header("location: index.php");
    exit();
}

$tech_id = $_SESSION['t_id'];

// Check if account is locked
$check_query = "SELECT t_status, t_blocked_until, t_block_reason FROM tms_technician WHERE t_id = ?";
$stmt_check = $mysqli->prepare($check_query);
$stmt_check->bind_param('i', $tech_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$tech_status = $result_check->fetch_object();
$stmt_check->close();

// Check if account is locked or blocked
$is_locked = false;
$lock_reason = '';
$locked_until = '';

if($tech_status) {
    if($tech_status->t_status === 'Locked') {
        $is_locked = true;
        $lock_reason = $tech_status->t_block_reason ?? 'Your account has been locked by admin';
        $locked_until = $tech_status->t_blocked_until ? date('M d, Y h:i A', strtotime($tech_status->t_blocked_until)) : '';
        
        // Check if block period expired
        if($tech_status->t_blocked_until && strtotime($tech_status->t_blocked_until) <= time()) {
            // Auto-unlock
            $mysqli->query("UPDATE tms_technician SET t_status = 'Available', t_blocked_until = NULL, t_block_reason = NULL WHERE t_id = $tech_id");
            $is_locked = false;
        }
    }
}

if($is_locked) {
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
                padding: 10px;
            }
            .lock-container {
                max-width: 500px;
                width: 100%;
                padding: 10px;
            }
            .lock-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                overflow: hidden;
            }
            @media (max-width: 576px) {
                .lock-container {
                    padding: 5px;
                    max-width: 100%;
                }
                .lock-card {
                    border-radius: 10px;
                }
                .lock-header {
                    padding: 20px 15px !important;
                }
                .lock-icon {
                    font-size: 50px !important;
                }
                .lock-header h3 {
                    font-size: 1.5rem !important;
                }
                .lock-body {
                    padding: 15px !important;
                }
                .amount-box, .contact-box {
                    padding: 10px !important;
                    margin: 15px 0 !important;
                }
                .alert {
                    padding: 10px !important;
                    font-size: 0.9rem;
                }
                h5, h6 {
                    font-size: 1rem !important;
                }
                p, li {
                    font-size: 0.9rem;
                }
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
                    <p><?php echo htmlspecialchars($lock_reason); ?></p>
                    
                    <?php if($locked_until): ?>
                    <div class="amount-box">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-clock"></i> Lock Duration
                        </h6>
                        <p class="mb-0">Your account will be automatically unlocked on: <strong><?php echo $locked_until; ?></strong></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-box">
                        <h6 class="text-info mb-2">
                            <i class="fas fa-phone"></i> Need Help?
                        </h6>
                        <p class="mb-0">Contact Electrozot Admin for assistance or to resolve this issue.</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="logout.php" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
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
