<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid=$_SESSION['a_id'];

// Update technician password
if(isset($_POST['update_tech_password'])) {
    $t_id = $_POST['t_id'];
    $new_password = $_POST['new_password'];
    
    $query = "UPDATE tms_technician SET t_pwd=? WHERE t_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('si', $new_password, $t_id);
    
    if($stmt->execute()) {
        $succ = "Technician password updated successfully";
    } else {
        $err = "Failed to update password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Technicians</a></li>
                    <li class="breadcrumb-item active">Manage Passwords</li>
                </ol>
                
                <?php if(isset($succ)) { ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $succ; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php } ?>
                <?php if(isset($err)) { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php } ?>
                
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-key"></i> Technician Passwords Management
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>ID Number</th>
                                        <th>Category</th>
                                        <th>Current Password</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM tms_technician ORDER BY t_name ASC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while($row = $res->fetch_object()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td><?php echo $row->t_name; ?></td>
                                        <td><?php echo $row->t_id_no; ?></td>
                                        <td><?php echo $row->t_category; ?></td>
                                        <td>
                                            <div class="input-group input-group-sm" style="max-width: 250px;">
                                                <input type="password" class="form-control" id="pwd_<?php echo $row->t_id; ?>" value="<?php echo $row->t_pwd; ?>" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(<?php echo $row->t_id; ?>)">
                                                        <i class="fas fa-eye" id="icon_<?php echo $row->t_id; ?>"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#changePasswordModal<?php echo $row->t_id; ?>">
                                                <i class="fas fa-key"></i> Change Password
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Change Password Modal -->
                                    <div class="modal fade" id="changePasswordModal<?php echo $row->t_id; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title"><i class="fas fa-key"></i> Change Password for <?php echo $row->t_name; ?></h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="t_id" value="<?php echo $row->t_id; ?>">
                                                        <div class="form-group">
                                                            <label>New Password <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control" name="new_password" id="newPwd_<?php echo $row->t_id; ?>" required>
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleNewPassword(<?php echo $row->t_id; ?>)">
                                                                        <i class="fas fa-eye" id="newIcon_<?php echo $row->t_id; ?>"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="update_tech_password" class="btn btn-warning">
                                                            <i class="fas fa-save"></i> Update Password
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/sb-admin.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    
    <script>
    function togglePassword(techId) {
        const input = document.getElementById('pwd_' + techId);
        const icon = document.getElementById('icon_' + techId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    function toggleNewPassword(techId) {
        const input = document.getElementById('newPwd_' + techId);
        const icon = document.getElementById('newIcon_' + techId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
