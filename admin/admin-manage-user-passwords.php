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

// Add created_at column if it doesn't exist
$mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS is_deleted TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL");

// Check if registration_type column exists and update it
$check_col = $mysqli->query("SHOW COLUMNS FROM tms_user LIKE 'registration_type'");
if($check_col->num_rows > 0) {
    // Modify existing column to include 'guest'
    $mysqli->query("ALTER TABLE tms_user MODIFY COLUMN registration_type ENUM('admin', 'self', 'guest') DEFAULT 'admin'");
} else {
    // Create new column with all three types
    $mysqli->query("ALTER TABLE tms_user ADD COLUMN registration_type ENUM('admin', 'self', 'guest') DEFAULT 'admin'");
}

// Update user password
if(isset($_POST['update_user_password'])) {
    $u_id = $_POST['u_id'];
    $new_password = $_POST['new_password'];
    
    // Check if user is a guest
    $check_query = "SELECT registration_type FROM tms_user WHERE u_id=?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $u_id);
    $check_stmt->execute();
    $check_res = $check_stmt->get_result();
    $user_data = $check_res->fetch_object();
    
    // If guest user, convert to self-registered when password is assigned
    if($user_data && $user_data->registration_type == 'guest') {
        $query = "UPDATE tms_user SET u_pwd=?, registration_type='self' WHERE u_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('si', $new_password, $u_id);
        
        if($stmt->execute()) {
            $succ = "Password assigned successfully! Guest user converted to Self Registered user.";
        } else {
            $err = "Failed to update password";
        }
    } else {
        // Regular password update
        $query = "UPDATE tms_user SET u_pwd=? WHERE u_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('si', $new_password, $u_id);
        
        if($stmt->execute()) {
            $succ = "User password updated successfully";
        } else {
            $err = "Failed to update password";
        }
    }
}

// Move single user to recycle bin
if(isset($_GET['delete'])) {
    $u_id = intval($_GET['delete']);
    
    // Get user data
    $get_query = "SELECT * FROM tms_user WHERE u_id=?";
    $get_stmt = $mysqli->prepare($get_query);
    $get_stmt->bind_param('i', $u_id);
    $get_stmt->execute();
    $user_data = $get_stmt->get_result()->fetch_assoc();
    
    if($user_data) {
        // Insert into deleted_items table
        $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by) VALUES (?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $item_type = 'user';
        $item_data = json_encode($user_data);
        $insert_stmt->bind_param('sisi', $item_type, $u_id, $item_data, $aid);
        
        if($insert_stmt->execute()) {
            // Delete from main table
            $delete_query = "DELETE FROM tms_user WHERE u_id=?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $u_id);
            
            if($delete_stmt->execute()) {
                $_SESSION['succ'] = "User moved to recycle bin successfully";
                header("Location: admin-manage-user-passwords.php");
                exit();
            } else {
                $err = "Failed to delete user from main table: " . $delete_stmt->error;
            }
        } else {
            $err = "Failed to move user to recycle bin: " . $insert_stmt->error;
        }
    } else {
        $err = "User not found";
    }
}

// Check for session success message
if(isset($_SESSION['succ'])) {
    $succ = $_SESSION['succ'];
    unset($_SESSION['succ']);
}

// Bulk delete users
if(isset($_POST['bulk_delete'])) {
    if(isset($_POST['selected_users']) && is_array($_POST['selected_users'])) {
        $deleted_count = 0;
        foreach($_POST['selected_users'] as $u_id) {
            // Get user data
            $get_query = "SELECT * FROM tms_user WHERE u_id=?";
            $get_stmt = $mysqli->prepare($get_query);
            $get_stmt->bind_param('i', $u_id);
            $get_stmt->execute();
            $user_data = $get_stmt->get_result()->fetch_assoc();
            
            if($user_data) {
                // Insert into deleted_items table
                $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by) VALUES (?, ?, ?, ?)";
                $insert_stmt = $mysqli->prepare($insert_query);
                $item_type = 'user';
                $item_data = json_encode($user_data);
                $insert_stmt->bind_param('sisi', $item_type, $u_id, $item_data, $aid);
                
                if($insert_stmt->execute()) {
                    // Delete from main table
                    $delete_query = "DELETE FROM tms_user WHERE u_id=?";
                    $delete_stmt = $mysqli->prepare($delete_query);
                    $delete_stmt->bind_param('i', $u_id);
                    $delete_stmt->execute();
                    $deleted_count++;
                }
            }
        }
        $_SESSION['succ'] = "$deleted_count user(s) moved to recycle bin successfully";
        header("Location: admin-manage-user-passwords.php");
        exit();
    } else {
        $err = "No users selected";
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
                    <li class="breadcrumb-item"><a href="#">Users</a></li>
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
                    $total_users = $mysqli->query("SELECT COUNT(*) as count FROM tms_user")->fetch_object()->count;
                    $admin_users = $mysqli->query("SELECT COUNT(*) as count FROM tms_user WHERE registration_type='admin'")->fetch_object()->count;
                    $self_users = $mysqli->query("SELECT COUNT(*) as count FROM tms_user WHERE registration_type='self'")->fetch_object()->count;
                    $guest_users = $mysqli->query("SELECT COUNT(*) as count FROM tms_user WHERE registration_type='guest' OR registration_type IS NULL")->fetch_object()->count;
                    ?>
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Admin Created</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $admin_users; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Self Registered</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $self_users; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Guest Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $guest_users; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-key"></i> User Passwords Management
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()" id="bulkDeleteBtn" style="display: none;">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label><i class="fas fa-filter"></i> Filter by Registration Type:</label>
                                <select class="form-control" id="filterType" onchange="filterTable()">
                                    <option value="all">All Users</option>
                                    <option value="admin">Admin Created</option>
                                    <option value="self">Self Registered</option>
                                    <option value="guest">Guest Users</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label><i class="fas fa-calendar"></i> Filter by Date:</label>
                                <select class="form-control" id="filterDate" onchange="filterTable()">
                                    <option value="all">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label><i class="fas fa-search"></i> Search:</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, phone..." onkeyup="searchTable()">
                            </div>
                        </div>
                        
                        <form method="POST" id="bulkDeleteForm">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="usersTable" style="font-size: 0.9rem;">
                                    <style>
                                        #usersTable td {
                                            vertical-align: middle;
                                            padding: 8px 10px;
                                        }
                                        #usersTable th {
                                            vertical-align: middle;
                                            padding: 10px;
                                            font-size: 0.85rem;
                                            white-space: nowrap;
                                        }
                                        #usersTable .badge {
                                            font-size: 0.75rem;
                                            padding: 5px 8px;
                                            white-space: nowrap;
                                        }
                                        #usersTable small {
                                            font-size: 0.7rem;
                                        }
                                    </style>
                                    <thead>
                                        <tr>
                                            <th width="30" style="text-align: center;">
                                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                            </th>
                                            <th width="40" style="text-align: center;">#</th>
                                            <th width="140">Name</th>
                                            <th width="180">Email</th>
                                            <th width="110">Phone</th>
                                            <th width="120">Area</th>
                                            <th width="80">Pincode</th>
                                            <th width="140">Registration</th>
                                            <th width="130">Created Date</th>
                                            <th width="140">Password</th>
                                            <th width="120" style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = "SELECT * FROM tms_user ORDER BY created_at DESC";
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                        $cnt = 1;
                                        while($row = $res->fetch_object()) {
                                            $reg_type = isset($row->registration_type) ? $row->registration_type : 'admin';
                                            $created_date = isset($row->created_at) ? date('Y-m-d H:i', strtotime($row->created_at)) : 'N/A';
                                        ?>
                                        <tr data-type="<?php echo $reg_type; ?>" data-date="<?php echo $created_date; ?>">
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="selected_users[]" value="<?php echo $row->u_id; ?>" class="user-checkbox" onchange="updateBulkDeleteBtn()">
                                            </td>
                                            <td style="text-align: center;"><?php echo $cnt; ?></td>
                                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo $row->u_fname . ' ' . $row->u_lname; ?>">
                                                <?php echo $row->u_fname . ' ' . $row->u_lname; ?>
                                            </td>
                                            <td style="font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis;" title="<?php echo $row->u_email; ?>">
                                                <?php echo $row->u_email; ?>
                                            </td>
                                            <td style="white-space: nowrap;"><?php echo $row->u_phone; ?></td>
                                            <td style="font-size: 0.85rem;">
                                                <?php 
                                                $area = isset($row->u_area) && !empty($row->u_area) ? $row->u_area : '<span class="text-muted" style="font-size: 0.75rem;">N/A</span>';
                                                echo $area;
                                                ?>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <?php 
                                                $pincode = isset($row->u_pincode) && !empty($row->u_pincode) ? $row->u_pincode : '<span class="text-muted" style="font-size: 0.75rem;">N/A</span>';
                                                echo $pincode;
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if($reg_type == 'self') { 
                                                    echo '<span class="badge badge-success">';
                                                    echo '<i class="fas fa-user-plus"></i> Self';
                                                    echo '</span>';
                                                } elseif($reg_type == 'guest') { 
                                                    echo '<span class="badge badge-warning">';
                                                    echo '<i class="fas fa-user-clock"></i> Guest';
                                                    echo '</span>';
                                                } else { 
                                                    echo '<span class="badge badge-info">';
                                                    echo '<i class="fas fa-user-shield"></i> Admin';
                                                    echo '</span>';
                                                } 
                                                ?>
                                            </td>
                                            <td style="font-size: 0.8rem; white-space: nowrap;"><?php echo date('d/m/y H:i', strtotime($created_date)); ?></td>
                                            <td>
                                                <div class="input-group input-group-sm" style="max-width: 140px;">
                                                    <input type="password" class="form-control form-control-sm" id="pwd_<?php echo $row->u_id; ?>" value="<?php echo $row->u_pwd; ?>" readonly style="font-size: 0.8rem; padding: 4px 8px;">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword(<?php echo $row->u_id; ?>)" style="padding: 4px 8px;">
                                                            <i class="fas fa-eye" id="icon_<?php echo $row->u_id; ?>" style="font-size: 0.8rem;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#changePasswordModal<?php echo $row->u_id; ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <a href="admin-manage-user-passwords.php?delete=<?php echo $row->u_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Move this user to recycle bin?')" style="padding: 4px 8px; font-size: 0.8rem;">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    
                                    <!-- Change Password Modal -->
                                    <div class="modal fade" id="changePasswordModal<?php echo $row->u_id; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title"><i class="fas fa-key"></i> Change Password for <?php echo $row->u_fname . ' ' . $row->u_lname; ?></h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="u_id" value="<?php echo $row->u_id; ?>">
                                                        <div class="form-group">
                                                            <label>New Password <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control" name="new_password" id="newPwd_<?php echo $row->u_id; ?>" required>
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleNewPassword(<?php echo $row->u_id; ?>)">
                                                                        <i class="fas fa-eye" id="newIcon_<?php echo $row->u_id; ?>"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="update_user_password" class="btn btn-warning">
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
                        </form>
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
    function togglePassword(userId) {
        const input = document.getElementById('pwd_' + userId);
        const icon = document.getElementById('icon_' + userId);
        
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
    
    function toggleNewPassword(userId) {
        const input = document.getElementById('newPwd_' + userId);
        const icon = document.getElementById('newIcon_' + userId);
        
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
    
    // Select all checkboxes
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => {
            if(cb.closest('tr').style.display !== 'none') {
                cb.checked = selectAll.checked;
            }
        });
        updateBulkDeleteBtn();
    }
    
    // Update bulk delete button visibility
    function updateBulkDeleteBtn() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        if(checkboxes.length > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
            bulkDeleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Selected (' + checkboxes.length + ')';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }
    
    // Bulk delete function
    function bulkDelete() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        if(checkboxes.length === 0) {
            alert('Please select users to delete');
            return;
        }
        
        if(confirm('Are you sure you want to move ' + checkboxes.length + ' user(s) to recycle bin?')) {
            const form = document.getElementById('bulkDeleteForm');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'bulk_delete';
            input.value = '1';
            form.appendChild(input);
            form.submit();
        }
    }
    
    // Search function
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('usersTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');
        
        // First apply filters
        const typeFilter = document.getElementById('filterType').value;
        const dateFilter = document.getElementById('filterDate').value;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            
            // Check if row passes filter criteria
            const type = row.getAttribute('data-type');
            let passesFilter = true;
            
            if (typeFilter !== 'all') {
                passesFilter = (type === typeFilter);
            }
            
            if (!passesFilter) {
                row.style.display = 'none';
                continue;
            }
            
            // Now check search
            if (filter === '') {
                row.style.display = '';
                continue;
            }
            
            let found = false;
            // Search in name (col 2), email (col 3), phone (col 4)
            for (let j = 2; j <= 4; j++) {
                if (cells[j]) {
                    const text = cells[j].textContent || cells[j].innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            row.style.display = found ? '' : 'none';
            
            // Uncheck hidden rows
            if (!found) {
                const checkbox = row.querySelector('.user-checkbox');
                if(checkbox) checkbox.checked = false;
            }
        }
        
        document.getElementById('selectAll').checked = false;
        updateBulkDeleteBtn();
    }
    
    // Filter function
    function filterTable() {
        const typeFilter = document.getElementById('filterType').value;
        const dateFilter = document.getElementById('filterDate').value;
        const table = document.getElementById('usersTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let visibleCount = 0;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const type = row.getAttribute('data-type');
            const dateStr = row.getAttribute('data-date');
            
            let showType = true;
            let showDate = true;
            
            // Type filter
            if (typeFilter !== 'all') {
                showType = (type === typeFilter);
            }
            
            // Date filter
            if (dateFilter !== 'all' && dateStr && dateStr !== 'N/A') {
                const rowDate = new Date(dateStr);
                rowDate.setHours(0, 0, 0, 0);
                
                if (dateFilter === 'today') {
                    showDate = (rowDate.getTime() === today.getTime());
                } else if (dateFilter === 'week') {
                    const weekAgo = new Date(today);
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    showDate = (rowDate >= weekAgo);
                } else if (dateFilter === 'month') {
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(monthAgo.getMonth() - 1);
                    showDate = (rowDate >= monthAgo);
                }
            }
            
            if (showType && showDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                // Uncheck hidden rows
                const checkbox = row.querySelector('.user-checkbox');
                if(checkbox) checkbox.checked = false;
            }
        }
        
        // Update select all checkbox
        document.getElementById('selectAll').checked = false;
        updateBulkDeleteBtn();
        
        // Show message if no results
        console.log('Visible rows: ' + visibleCount);
    }
    </script>
</body>
</html>
