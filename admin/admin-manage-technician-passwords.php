<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid=$_SESSION['a_id'];

// Create deleted_items table if not exists
try {
    $create_table = "CREATE TABLE IF NOT EXISTS tms_deleted_items (
        di_id INT AUTO_INCREMENT PRIMARY KEY,
        di_item_type VARCHAR(50) NOT NULL,
        di_item_id INT NOT NULL,
        di_item_data TEXT NOT NULL,
        di_deleted_by INT NOT NULL,
        di_deleted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        di_reason TEXT,
        INDEX(di_item_type),
        INDEX(di_deleted_date)
    )";
    $mysqli->query($create_table);
} catch(Exception $e) {}

// Ensure pincode and EZ ID columns exist
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_pincode VARCHAR(255)");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_ez_id VARCHAR(20) DEFAULT NULL");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS is_deleted TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL");

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

// Update technician pincode
if(isset($_POST['update_tech_pincode'])) {
    $t_id = $_POST['t_id'];
    $t_pincode = $_POST['t_pincode'];
    
    $query = "UPDATE tms_technician SET t_pincode=? WHERE t_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('si', $t_pincode, $t_id);
    
    if($stmt->execute()) {
        $succ = "Technician work area pincode updated successfully";
    } else {
        $err = "Failed to update pincode";
    }
}

// Move technician to recycle bin
if(isset($_GET['delete'])) {
    $t_id = $_GET['delete'];
    
    // Get technician data
    $get_query = "SELECT * FROM tms_technician WHERE t_id=?";
    $get_stmt = $mysqli->prepare($get_query);
    $get_stmt->bind_param('i', $t_id);
    $get_stmt->execute();
    $tech_data = $get_stmt->get_result()->fetch_assoc();
    
    if($tech_data) {
        // Insert into deleted_items table
        $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by) VALUES (?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $item_type = 'technician';
        $item_data = json_encode($tech_data);
        $insert_stmt->bind_param('sisi', $item_type, $t_id, $item_data, $aid);
        
        if($insert_stmt->execute()) {
            // Delete from main table
            $delete_query = "DELETE FROM tms_technician WHERE t_id=?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $t_id);
            $delete_stmt->execute();
            
            $succ = "Technician moved to recycle bin successfully";
        } else {
            $err = "Failed to move technician to recycle bin";
        }
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
                
                <!-- Statistics Cards -->
                <div class="row mb-3">
                    <?php
                    $total_techs = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician")->fetch_object()->count;
                    $active_techs = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_pwd IS NOT NULL AND t_pwd != ''")->fetch_object()->count;
                    $with_pincode = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_pincode IS NOT NULL AND t_pincode != ''")->fetch_object()->count;
                    ?>
                    <div class="col-md-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Technicians</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_techs; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">With Password</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $active_techs; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-key fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">With Work Area</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $with_pincode; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-key"></i> Technician Management
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label><i class="fas fa-filter"></i> Filter by Category:</label>
                                <select class="form-control" id="filterCategory" onchange="filterTable()">
                                    <option value="all">All Categories</option>
                                    <?php
                                    $categories = $mysqli->query("SELECT DISTINCT t_category FROM tms_technician WHERE t_category IS NOT NULL ORDER BY t_category");
                                    while($cat = $categories->fetch_object()) {
                                        echo '<option value="'.$cat->t_category.'">'.$cat->t_category.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label><i class="fas fa-map-pin"></i> Filter by Pincode:</label>
                                <select class="form-control" id="filterPincode" onchange="filterTable()">
                                    <option value="all">All Areas</option>
                                    <option value="with">With Pincode</option>
                                    <option value="without">Without Pincode</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label><i class="fas fa-search"></i> Search:</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by name, EZ ID, email..." onkeyup="searchTable()">
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="techTable" style="font-size: 0.9rem;">
                                <style>
                                    #techTable td {
                                        vertical-align: middle;
                                        padding: 8px 10px;
                                    }
                                    #techTable th {
                                        vertical-align: middle;
                                        padding: 10px;
                                        font-size: 0.85rem;
                                        white-space: nowrap;
                                    }
                                </style>
                                <thead>
                                    <tr>
                                        <th width="40" style="text-align: center;">#</th>
                                        <th width="140">Name</th>
                                        <th width="100">EZ ID</th>
                                        <th width="100">ID Number</th>
                                        <th width="180">Email</th>
                                        <th width="120">Category</th>
                                        <th width="120">Work Area (Pincode)</th>
                                        <th width="140">Password</th>
                                        <th width="150" style="text-align: center;">Actions</th>
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
                                        $has_pincode = !empty($row->t_pincode) ? 'with' : 'without';
                                    ?>
                                    <tr data-category="<?php echo $row->t_category; ?>" data-pincode="<?php echo $has_pincode; ?>">
                                        <td style="text-align: center;"><?php echo $cnt; ?></td>
                                        <td style="white-space: nowrap;"><?php echo $row->t_name; ?></td>
                                        <td style="text-align: center;">
                                            <?php if(!empty($row->t_ez_id)) { ?>
                                                <span class="badge badge-dark" style="font-size: 0.85rem;">
                                                    <?php echo $row->t_ez_id; ?>
                                                </span>
                                            <?php } else { ?>
                                                <span class="text-muted" style="font-size: 0.75rem;">Not Set</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $row->t_id_no; ?></td>
                                        <td style="font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis;" title="<?php echo $row->t_email; ?>">
                                            <?php echo $row->t_email; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary" style="font-size: 0.8rem;">
                                                <?php echo $row->t_category; ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if(!empty($row->t_pincode)) { ?>
                                                <span class="badge badge-success" style="font-size: 0.85rem;">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo $row->t_pincode; ?>
                                                </span>
                                            <?php } else { ?>
                                                <span class="text-muted" style="font-size: 0.75rem;">Not Set</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                                <input type="password" class="form-control form-control-sm" id="pwd_<?php echo $row->t_id; ?>" value="<?php echo $row->t_pwd; ?>" readonly style="font-size: 0.8rem; padding: 4px 8px;">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword(<?php echo $row->t_id; ?>)" style="padding: 4px 8px;">
                                                        <i class="fas fa-eye" id="icon_<?php echo $row->t_id; ?>" style="font-size: 0.8rem;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#changePasswordModal<?php echo $row->t_id; ?>" style="padding: 4px 8px; font-size: 0.8rem;" title="Change Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#changePincodeModal<?php echo $row->t_id; ?>" style="padding: 4px 8px; font-size: 0.8rem;" title="Set Work Area">
                                                <i class="fas fa-map-pin"></i>
                                            </button>
                                            <a href="admin-manage-technician-passwords.php?delete=<?php echo $row->t_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Move this technician to recycle bin?')" style="padding: 4px 8px; font-size: 0.8rem;" title="Move to Recycle Bin">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
                                    
                                    <!-- Change Pincode Modal -->
                                    <div class="modal fade" id="changePincodeModal<?php echo $row->t_id; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title"><i class="fas fa-map-pin"></i> Set Work Area for <?php echo $row->t_name; ?></h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="t_id" value="<?php echo $row->t_id; ?>">
                                                        <div class="form-group">
                                                            <label>Work Area Pincodes <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="t_pincode" rows="4" placeholder="Enter pincodes separated by commas (e.g., 110001, 110002, 110003)" required><?php echo $row->t_pincode; ?></textarea>
                                                            <small class="form-text text-muted">
                                                                <i class="fas fa-info-circle"></i> Enter multiple pincodes separated by commas where this technician can work
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="update_tech_pincode" class="btn btn-info">
                                                            <i class="fas fa-save"></i> Update Work Area
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
    <script src="js/sb-admin.min.js"></script>
    
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
    
    // Search function
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('techTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            
            if (filter === '') {
                row.style.display = '';
                continue;
            }
            
            let found = false;
            // Search in name (col 1), EZ ID (col 2), ID Number (col 3), email (col 4)
            for (let j = 1; j <= 4; j++) {
                if (cells[j]) {
                    const text = cells[j].textContent || cells[j].innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    }
    
    // Filter function
    function filterTable() {
        const categoryFilter = document.getElementById('filterCategory').value;
        const pincodeFilter = document.getElementById('filterPincode').value;
        const table = document.getElementById('techTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const category = row.getAttribute('data-category');
            const pincode = row.getAttribute('data-pincode');
            
            let showCategory = true;
            let showPincode = true;
            
            // Category filter
            if (categoryFilter !== 'all') {
                showCategory = (category === categoryFilter);
            }
            
            // Pincode filter
            if (pincodeFilter !== 'all') {
                showPincode = (pincode === pincodeFilter);
            }
            
            if (showCategory && showPincode) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
    </script>
</body>
</html>
