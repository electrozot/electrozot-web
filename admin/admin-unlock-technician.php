<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Handle unlock request
if(isset($_POST['unlock_account'])) {
    $tech_id = $_POST['tech_id'];
    $lock_type = isset($_POST['lock_type']) ? $_POST['lock_type'] : 'commission';
    
    // Unlock both types of locks
    $unlock_query = "UPDATE tms_technician 
                     SET account_locked = 0, 
                         lock_reason = NULL, 
                         locked_at = NULL,
                         t_status = CASE 
                             WHEN t_status = 'Locked' THEN 'Available'
                             ELSE t_status
                         END,
                         t_blocked_until = NULL,
                         t_block_reason = NULL
                     WHERE t_id = ?";
    $stmt_unlock = $mysqli->prepare($unlock_query);
    $stmt_unlock->bind_param('i', $tech_id);
    
    if($stmt_unlock->execute()) {
        // Log the unlock action
        $log_query = "INSERT INTO tms_syslogs (u_email, u_ip, u_city, u_country, user_type) 
                      VALUES (?, ?, 'Admin Panel', 'Unlock Action', 'admin')";
        $log_stmt = $mysqli->prepare($log_query);
        $admin_email = $_SESSION['a_email'] ?? 'admin';
        $admin_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $log_message = "Unlocked technician ID: $tech_id (Type: $lock_type)";
        $log_stmt->bind_param('ss', $log_message, $admin_ip);
        $log_stmt->execute();
        
        $_SESSION['success'] = "Technician account unlocked successfully!";
    } else {
        $_SESSION['error'] = "Failed to unlock account.";
    }
    $stmt_unlock->close();
    
    header("location: admin-unlock-technician.php");
    exit();
}

// Get all locked technicians (all types: commission lock, rejection lock, temporary block)
$locked_query = "SELECT *, 
                 CASE 
                     WHEN account_locked = 1 THEN 'commission'
                     WHEN t_status = 'Locked' THEN 'rejection'
                     WHEN t_blocked_until IS NOT NULL AND t_blocked_until > NOW() THEN 'temporary'
                     ELSE 'other'
                 END as lock_type,
                 CASE 
                     WHEN account_locked = 1 THEN lock_reason
                     WHEN t_status = 'Locked' THEN t_block_reason
                     WHEN t_blocked_until IS NOT NULL AND t_blocked_until > NOW() THEN CONCAT(t_block_reason, ' (Until: ', DATE_FORMAT(t_blocked_until, '%b %d, %Y %h:%i %p'), ')')
                     ELSE 'Account locked'
                 END as display_reason,
                 CASE 
                     WHEN account_locked = 1 THEN locked_at
                     WHEN t_status = 'Locked' THEN NOW()
                     ELSE NOW()
                 END as display_locked_at
                 FROM tms_technician 
                 WHERE account_locked = 1 
                    OR t_status = 'Locked'
                    OR (t_blocked_until IS NOT NULL AND t_blocked_until > NOW())
                 ORDER BY 
                     CASE 
                         WHEN account_locked = 1 THEN locked_at
                         WHEN t_status = 'Locked' THEN NOW()
                         ELSE NOW()
                     END DESC";
$locked_result = $mysqli->query($locked_query);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Locked Technicians</li>
                </ol>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <h3 class="mb-3">
                    <i class="fas fa-lock text-danger"></i> Locked Technician Accounts
                </h3>

                <div class="card shadow">
                    <div class="card-header py-3 bg-danger">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-user-lock"></i> Technicians with Locked Accounts
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if($locked_result && $locked_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Technician</th>
                                            <th>EZ ID</th>
                                            <th>Phone</th>
                                            <th>Lock Type</th>
                                            <th>Lock Reason</th>
                                            <th>Locked At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Get highlighted technician ID from URL
                                        $highlight_id = isset($_GET['highlight']) ? intval($_GET['highlight']) : 0;
                                        
                                        while($tech = $locked_result->fetch_object()): 
                                            // Check if this is the highlighted technician
                                            $is_highlighted = ($highlight_id > 0 && $tech->t_id == $highlight_id);
                                            $row_class = $is_highlighted ? 'table-warning' : '';
                                            $row_id = $is_highlighted ? 'id="highlighted-tech"' : '';
                                        ?>
                                            <tr class="<?php echo $row_class; ?>" <?php echo $row_id; ?>>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($tech->t_name); ?></strong>
                                                    <?php if($is_highlighted): ?>
                                                        <span class="badge badge-warning ml-2">
                                                            <i class="fas fa-arrow-left"></i> This Technician
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($tech->t_ez_id ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($tech->t_phone); ?></td>
                                                <td>
                                                    <?php 
                                                    $lock_type = $tech->lock_type ?? 'other';
                                                    if($lock_type == 'commission'): 
                                                    ?>
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-money-bill-wave"></i> Unpaid Commission
                                                        </span>
                                                    <?php elseif($lock_type == 'rejection'): ?>
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-times-circle"></i> Excessive Rejections
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-lock"></i> Other
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($tech->display_reason ?? $tech->lock_reason ?? $tech->t_block_reason ?? 'Account locked'); ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $locked_time = $tech->display_locked_at ?? $tech->locked_at ?? null;
                                                    if($locked_time): 
                                                        echo date('d M Y h:i A', strtotime($locked_time));
                                                    else:
                                                        echo 'N/A';
                                                    endif;
                                                    
                                                    // Show blocked until date if applicable
                                                    if(!empty($tech->t_blocked_until) && strtotime($tech->t_blocked_until) > time()):
                                                        echo '<br><small class="text-danger">Until: ' . date('d M Y h:i A', strtotime($tech->t_blocked_until)) . '</small>';
                                                    endif;
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="tech_id" value="<?php echo $tech->t_id; ?>">
                                                        <input type="hidden" name="lock_type" value="<?php echo $lock_type; ?>">
                                                        <button type="submit" name="unlock_account" 
                                                                class="btn btn-sm btn-success <?php echo $is_highlighted ? 'btn-pulse' : ''; ?>" 
                                                                onclick="return confirm('Are you sure you want to unlock this technician? <?php echo ($lock_type == 'commission') ? 'Make sure commission has been paid.' : 'This will allow them to receive bookings again.'; ?>')">
                                                            <i class="fas fa-unlock"></i> Unlock
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                                <h4 class="text-success">All Clear!</h4>
                                <p class="text-muted">No locked technician accounts.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            <?php include('vendor/inc/footer.php');?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
    
    <style>
    /* Highlight animation for the specific technician */
    #highlighted-tech {
        animation: highlightPulse 2s ease-in-out infinite;
        border-left: 5px solid #ffc107 !important;
    }
    
    @keyframes highlightPulse {
        0%, 100% { background-color: #fff3cd; }
        50% { background-color: #ffe69c; }
    }
    
    /* Pulse animation for unlock button */
    .btn-pulse {
        animation: btnPulse 1.5s ease-in-out infinite;
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    
    @keyframes btnPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }
    </style>
    
    <script>
    $(document).ready(function() {
        // Scroll to highlighted technician if exists
        var highlightedTech = $('#highlighted-tech');
        if(highlightedTech.length) {
            $('html, body').animate({
                scrollTop: highlightedTech.offset().top - 100
            }, 1000);
            
            // Show a toast notification
            <?php if($highlight_id > 0): ?>
            setTimeout(function() {
                var techName = highlightedTech.find('strong').first().text();
                alert('👇 Scroll down to unlock: ' + techName);
            }, 500);
            <?php endif; ?>
        }
    });
    </script>
</body>
</html>
