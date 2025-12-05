<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Handle unlock request
if(isset($_POST['unlock_account'])) {
    $tech_id = $_POST['tech_id'];
    
    $unlock_query = "UPDATE tms_technician 
                     SET account_locked = 0, 
                         lock_reason = NULL, 
                         locked_at = NULL 
                     WHERE t_id = ?";
    $stmt_unlock = $mysqli->prepare($unlock_query);
    $stmt_unlock->bind_param('i', $tech_id);
    
    if($stmt_unlock->execute()) {
        $_SESSION['success'] = "Technician account unlocked successfully!";
    } else {
        $_SESSION['error'] = "Failed to unlock account.";
    }
    $stmt_unlock->close();
    
    header("location: admin-unlock-technician.php");
    exit();
}

// Get all locked technicians
$locked_query = "SELECT * FROM tms_technician WHERE account_locked = 1 ORDER BY locked_at DESC";
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
                                            <th>Lock Reason</th>
                                            <th>Locked At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($tech = $locked_result->fetch_object()): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($tech->t_name); ?></strong></td>
                                                <td><?php echo htmlspecialchars($tech->t_ez_id ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($tech->t_phone); ?></td>
                                                <td><?php echo htmlspecialchars($tech->lock_reason); ?></td>
                                                <td><?php echo date('d M Y h:i A', strtotime($tech->locked_at)); ?></td>
                                                <td class="text-center">
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="tech_id" value="<?php echo $tech->t_id; ?>">
                                                        <button type="submit" name="unlock_account" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('Are you sure the technician has paid? This will unlock their account.')">
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
</body>
</html>
