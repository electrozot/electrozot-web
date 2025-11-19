<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

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

// Handle bulk restore
if(isset($_POST['bulk_restore'])) {
    if(isset($_POST['selected_items']) && is_array($_POST['selected_items'])) {
        $restored_count = 0;
        foreach($_POST['selected_items'] as $di_id) {
            // Get deleted item
            $query = "SELECT * FROM tms_deleted_items WHERE di_id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('i', $di_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_object();
            
            if($item) {
                $data = json_decode($item->di_item_data, true);
                $restored = false;
                
                // Restore based on type (same logic as single restore)
                switch($item->di_item_type) {
                    case 'technician':
                        $restore_query = "INSERT INTO tms_technician (t_id, t_name, t_id_no, t_category, t_experience, t_specialization, t_pic, t_status, t_pwd, t_phone, t_email, t_addr, t_service_pincode, t_ez_id, t_pincode) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $restore_stmt = $mysqli->prepare($restore_query);
                        $t_email = isset($data['t_email']) ? $data['t_email'] : '';
                        $t_addr = isset($data['t_addr']) ? $data['t_addr'] : '';
                        $t_service_pincode = isset($data['t_service_pincode']) ? $data['t_service_pincode'] : '';
                        $t_ez_id = isset($data['t_ez_id']) ? $data['t_ez_id'] : '';
                        $t_pincode = isset($data['t_pincode']) ? $data['t_pincode'] : '';
                        $restore_stmt->bind_param('issssssssssssss', 
                            $data['t_id'], $data['t_name'], $data['t_id_no'], $data['t_category'], 
                            $data['t_experience'], $data['t_specialization'], $data['t_pic'], 
                            $data['t_status'], $data['t_pwd'], $data['t_phone'], $t_email, 
                            $t_addr, $t_service_pincode, $t_ez_id, $t_pincode);
                        $restored = $restore_stmt->execute();
                        break;
                        
                    case 'user':
                        $restore_query = "INSERT INTO tms_user (u_id, u_fname, u_lname, u_phone, u_addr, u_category, u_email, u_pwd, u_area, u_pincode, registration_type, created_at) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $restore_stmt = $mysqli->prepare($restore_query);
                        $u_area = isset($data['u_area']) ? $data['u_area'] : '';
                        $u_pincode = isset($data['u_pincode']) ? $data['u_pincode'] : '';
                        $registration_type = isset($data['registration_type']) ? $data['registration_type'] : 'admin';
                        $created_at = isset($data['created_at']) ? $data['created_at'] : date('Y-m-d H:i:s');
                        $restore_stmt->bind_param('isssssssssss', 
                            $data['u_id'], $data['u_fname'], $data['u_lname'], $data['u_phone'], 
                            $data['u_addr'], $data['u_category'], $data['u_email'], $data['u_pwd'], 
                            $u_area, $u_pincode, $registration_type, $created_at);
                        $restored = $restore_stmt->execute();
                        break;
                        
                    case 'service':
                        $restore_query = "INSERT INTO tms_service (s_id, s_name, s_description, s_category, s_price, s_duration, s_status) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $restore_stmt = $mysqli->prepare($restore_query);
                        $s_duration = isset($data['s_duration']) ? $data['s_duration'] : '1-2 hours';
                        $s_status = isset($data['s_status']) ? $data['s_status'] : 'Active';
                        $restore_stmt->bind_param('isssdss', 
                            $data['s_id'], $data['s_name'], $data['s_description'], 
                            $data['s_category'], $data['s_price'], $s_duration, $s_status);
                        $restored = $restore_stmt->execute();
                        break;
                        
                    case 'booking':
                        $restore_query = "INSERT INTO tms_service_booking (sb_id, sb_user_id, sb_service_id, sb_technician_id, sb_booking_date, sb_booking_time, sb_address, sb_phone, sb_description, sb_status, sb_total_price) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $restore_stmt = $mysqli->prepare($restore_query);
                        $sb_technician_id = isset($data['sb_technician_id']) ? $data['sb_technician_id'] : null;
                        $sb_address = isset($data['sb_address']) ? $data['sb_address'] : '';
                        $sb_phone = isset($data['sb_phone']) ? $data['sb_phone'] : '';
                        $sb_description = isset($data['sb_description']) ? $data['sb_description'] : '';
                        $sb_total_price = isset($data['sb_total_price']) ? $data['sb_total_price'] : 0.00;
                        
                        // 11 parameters: i,i,i,i,s,s,s,s,s,s,d
                        $restore_stmt->bind_param('iiiissssssd', 
                            $data['sb_id'], $data['sb_user_id'], $data['sb_service_id'], 
                            $sb_technician_id, $data['sb_booking_date'], $data['sb_booking_time'], 
                            $sb_address, $sb_phone, $sb_description, $data['sb_status'], $sb_total_price);
                        
                        $restored = $restore_stmt->execute();
                        break;
                }
                
                if($restored) {
                    // Remove from recycle bin
                    $delete_query = "DELETE FROM tms_deleted_items WHERE di_id = ?";
                    $delete_stmt = $mysqli->prepare($delete_query);
                    $delete_stmt->bind_param('i', $di_id);
                    $delete_stmt->execute();
                    $restored_count++;
                }
            }
        }
        $_SESSION['success'] = "$restored_count item(s) restored successfully!";
    } else {
        $_SESSION['error'] = "No items selected";
    }
    header("Location: admin-recycle-bin.php" . (isset($_GET['type']) ? "?type=" . $_GET['type'] : ""));
    exit();
}

// Handle bulk permanent delete
if(isset($_POST['bulk_permanent_delete'])) {
    if(isset($_POST['selected_items']) && is_array($_POST['selected_items'])) {
        $deleted_count = 0;
        foreach($_POST['selected_items'] as $di_id) {
            $delete_query = "DELETE FROM tms_deleted_items WHERE di_id = ?";
            $stmt = $mysqli->prepare($delete_query);
            $stmt->bind_param('i', $di_id);
            if($stmt->execute()) {
                $deleted_count++;
            }
        }
        $_SESSION['success'] = "$deleted_count item(s) permanently deleted!";
    } else {
        $_SESSION['error'] = "No items selected";
    }
    header("Location: admin-recycle-bin.php" . (isset($_GET['type']) ? "?type=" . $_GET['type'] : ""));
    exit();
}

// Handle restore
if(isset($_POST['restore'])) {
    $di_id = $_POST['di_id'];
    
    // Get deleted item
    $query = "SELECT * FROM tms_deleted_items WHERE di_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $di_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_object();
    
    if($item) {
        $data = json_decode($item->di_item_data, true);
        $restored = false;
        
        // Restore based on type
        switch($item->di_item_type) {
            case 'technician':
                $restore_query = "INSERT INTO tms_technician (t_id, t_name, t_id_no, t_category, t_experience, t_specialization, t_pic, t_status, t_pwd, t_phone, t_email, t_addr, t_service_pincode, t_ez_id, t_pincode) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $restore_stmt = $mysqli->prepare($restore_query);
                $t_email = isset($data['t_email']) ? $data['t_email'] : '';
                $t_addr = isset($data['t_addr']) ? $data['t_addr'] : '';
                $t_service_pincode = isset($data['t_service_pincode']) ? $data['t_service_pincode'] : '';
                $t_ez_id = isset($data['t_ez_id']) ? $data['t_ez_id'] : '';
                $t_pincode = isset($data['t_pincode']) ? $data['t_pincode'] : '';
                $restore_stmt->bind_param('issssssssssssss', 
                    $data['t_id'], $data['t_name'], $data['t_id_no'], $data['t_category'], 
                    $data['t_experience'], $data['t_specialization'], $data['t_pic'], 
                    $data['t_status'], $data['t_pwd'], $data['t_phone'], $t_email, 
                    $t_addr, $t_service_pincode, $t_ez_id, $t_pincode);
                $restored = $restore_stmt->execute();
                break;
                
            case 'booking':
                $restore_query = "INSERT INTO tms_service_booking (sb_id, sb_user_id, sb_service_id, sb_technician_id, sb_booking_date, sb_booking_time, sb_address, sb_phone, sb_description, sb_status, sb_total_price) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $restore_stmt = $mysqli->prepare($restore_query);
                
                // Handle missing fields with defaults
                $sb_technician_id = isset($data['sb_technician_id']) ? $data['sb_technician_id'] : null;
                $sb_address = isset($data['sb_address']) ? $data['sb_address'] : '';
                $sb_phone = isset($data['sb_phone']) ? $data['sb_phone'] : '';
                $sb_description = isset($data['sb_description']) ? $data['sb_description'] : '';
                $sb_total_price = isset($data['sb_total_price']) ? $data['sb_total_price'] : 0.00;
                
                // 11 parameters: i,i,i,i,s,s,s,s,s,s,d
                $restore_stmt->bind_param('iiiissssssd', 
                    $data['sb_id'], 
                    $data['sb_user_id'], 
                    $data['sb_service_id'], 
                    $sb_technician_id,
                    $data['sb_booking_date'], 
                    $data['sb_booking_time'], 
                    $sb_address,
                    $sb_phone,
                    $sb_description,
                    $data['sb_status'],
                    $sb_total_price
                );
                $restored = $restore_stmt->execute();
                break;
                
            case 'user':
                $restore_query = "INSERT INTO tms_user (u_id, u_fname, u_lname, u_phone, u_addr, u_category, u_email, u_pwd, t_tech_category, t_tech_id, t_booking_date, t_booking_status) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $restore_stmt = $mysqli->prepare($restore_query);
                
                // Handle missing fields with defaults
                $t_tech_category = isset($data['t_tech_category']) ? $data['t_tech_category'] : '';
                $t_tech_id = isset($data['t_tech_id']) ? $data['t_tech_id'] : '';
                $t_booking_date = isset($data['t_booking_date']) ? $data['t_booking_date'] : '';
                $t_booking_status = isset($data['t_booking_status']) ? $data['t_booking_status'] : '';
                
                $restore_stmt->bind_param('isssssssssss', 
                    $data['u_id'], 
                    $data['u_fname'], 
                    $data['u_lname'], 
                    $data['u_phone'], 
                    $data['u_addr'], 
                    $data['u_category'], 
                    $data['u_email'], 
                    $data['u_pwd'], 
                    $t_tech_category,
                    $t_tech_id,
                    $t_booking_date,
                    $t_booking_status
                );
                $restored = $restore_stmt->execute();
                break;
                
            case 'service':
                $restore_query = "INSERT INTO tms_service (s_id, s_name, s_description, s_category, s_price, s_duration, s_status) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?)";
                $restore_stmt = $mysqli->prepare($restore_query);
                
                // Handle missing fields with defaults
                $s_duration = isset($data['s_duration']) ? $data['s_duration'] : '1-2 hours';
                $s_status = isset($data['s_status']) ? $data['s_status'] : 'Active';
                
                $restore_stmt->bind_param('isssdss', 
                    $data['s_id'], 
                    $data['s_name'], 
                    $data['s_description'], 
                    $data['s_category'], 
                    $data['s_price'],
                    $s_duration,
                    $s_status
                );
                $restored = $restore_stmt->execute();
                break;
        }
        
        if($restored) {
            // Remove from recycle bin
            $delete_query = "DELETE FROM tms_deleted_items WHERE di_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $di_id);
            $delete_stmt->execute();
            
            $_SESSION['success'] = "Item restored successfully!";
        } else {
            $err_msg = "Failed to restore item. Error: " . ($restore_stmt ? $restore_stmt->error : "Unknown error");
            if(strpos($err_msg, 'Duplicate entry') !== false) {
                $err_msg = "Failed to restore item. It already exists in the database.";
            }
            $_SESSION['error'] = $err_msg;
        }
    }
    header("Location: admin-recycle-bin.php" . (isset($_GET['type']) ? "?type=" . $_GET['type'] : ""));
    exit();
}

// Handle permanent delete
if(isset($_POST['permanent_delete'])) {
    $di_id = $_POST['di_id'];
    
    $delete_query = "DELETE FROM tms_deleted_items WHERE di_id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param('i', $di_id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Item permanently deleted!";
    } else {
        $_SESSION['error'] = "Failed to delete item.";
    }
    header("Location: admin-recycle-bin.php" . (isset($_GET['type']) ? "?type=" . $_GET['type'] : ""));
    exit();
}

// Handle empty recycle bin
if(isset($_POST['empty_bin'])) {
    $empty_query = "DELETE FROM tms_deleted_items";
    if($mysqli->query($empty_query)) {
        $_SESSION['success'] = "Recycle bin emptied successfully!";
    } else {
        $_SESSION['error'] = "Failed to empty recycle bin.";
    }
    header("Location: admin-recycle-bin.php");
    exit();
}

// Get session messages
if(isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Get filter
$filter = isset($_GET['type']) ? $_GET['type'] : 'all';

// Build query
$where = "";
if($filter != 'all') {
    $where = "WHERE di_item_type = '$filter'";
}

// Get deleted items
$items_query = "SELECT * FROM tms_deleted_items $where ORDER BY di_deleted_date DESC";
$items_result = $mysqli->query($items_query);

// Get counts
$counts_query = "SELECT 
                 COUNT(*) as total,
                 SUM(CASE WHEN di_item_type = 'technician' THEN 1 ELSE 0 END) as technicians,
                 SUM(CASE WHEN di_item_type = 'booking' THEN 1 ELSE 0 END) as bookings,
                 SUM(CASE WHEN di_item_type = 'user' THEN 1 ELSE 0 END) as users,
                 SUM(CASE WHEN di_item_type = 'service' THEN 1 ELSE 0 END) as services
                 FROM tms_deleted_items";
$counts_result = $mysqli->query($counts_query);
$counts = $counts_result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Recycle Bin</li>
                </ol>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $counts->total; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-trash fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Technicians</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $counts->technicians; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bookings</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $counts->bookings; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $counts->users; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="card mb-3">
                    <div class="card-header bg-danger text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-recycle"></i> Recycle Bin
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success" id="bulkRestoreBtn" style="display: none;" onclick="bulkRestore()">
                                    <i class="fas fa-undo"></i> Restore Selected
                                </button>
                                <button class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display: none;" onclick="bulkDelete()">
                                    <i class="fas fa-trash-alt"></i> Delete Selected
                                </button>
                                <?php if($counts->total > 0): ?>
                                    <button class="btn btn-sm btn-warning" onclick="emptyBin()">
                                        <i class="fas fa-trash-alt"></i> Empty All
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="btn-group mb-3" role="group">
                            <a href="?type=all" class="btn btn-<?php echo $filter == 'all' ? 'primary' : 'outline-primary'; ?>">
                                All (<?php echo $counts->total; ?>)
                            </a>
                            <a href="?type=technician" class="btn btn-<?php echo $filter == 'technician' ? 'success' : 'outline-success'; ?>">
                                Technicians (<?php echo $counts->technicians; ?>)
                            </a>
                            <a href="?type=booking" class="btn btn-<?php echo $filter == 'booking' ? 'info' : 'outline-info'; ?>">
                                Bookings (<?php echo $counts->bookings; ?>)
                            </a>
                            <a href="?type=user" class="btn btn-<?php echo $filter == 'user' ? 'warning' : 'outline-warning'; ?>">
                                Users (<?php echo $counts->users; ?>)
                            </a>
                            <a href="?type=service" class="btn btn-<?php echo $filter == 'service' ? 'secondary' : 'outline-secondary'; ?>">
                                Services (<?php echo $counts->services; ?>)
                            </a>
                        </div>

                        <?php if($items_result && $items_result->num_rows > 0): ?>
                            <form method="POST" id="bulkActionForm">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30">
                                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                                </th>
                                                <th>Type</th>
                                                <th>Item Details</th>
                                                <th>Deleted Date</th>
                                                <th>Reason</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php while($item = $items_result->fetch_object()): 
                                            $data = json_decode($item->di_item_data, true);
                                            $type_badge = '';
                                            $details = '';
                                            
                                            switch($item->di_item_type) {
                                                case 'technician':
                                                    $type_badge = '<span class="badge badge-success">Technician</span>';
                                                    $details = $data['t_name'] . ' (ID: ' . $data['t_id_no'] . ')';
                                                    break;
                                                case 'booking':
                                                    $type_badge = '<span class="badge badge-info">Booking</span>';
                                                    $details = 'Booking #' . $data['sb_id'] . ' - ' . $data['sb_status'];
                                                    break;
                                                case 'user':
                                                    $type_badge = '<span class="badge badge-warning">User</span>';
                                                    $details = $data['u_fname'] . ' ' . $data['u_lname'] . ' (' . $data['u_email'] . ')';
                                                    break;
                                                case 'service':
                                                    $type_badge = '<span class="badge badge-secondary">Service</span>';
                                                    $details = $data['s_name'] . ' - ₹' . $data['s_price'];
                                                    break;
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_items[]" value="<?php echo $item->di_id; ?>" class="item-checkbox" onchange="updateBulkButtons()">
                                            </td>
                                            <td><?php echo $type_badge; ?></td>
                                            <td><?php echo $details; ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($item->di_deleted_date)); ?></td>
                                            <td><?php echo htmlspecialchars($item->di_reason ? $item->di_reason : '-'); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="di_id" value="<?php echo $item->di_id; ?>">
                                                    <button type="submit" name="restore" class="btn btn-success btn-sm" 
                                                            onclick="return confirm('Restore this item?')">
                                                        <i class="fas fa-undo"></i> Restore
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="di_id" value="<?php echo $item->di_id; ?>">
                                                    <button type="submit" name="permanent_delete" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Permanently delete this item? This cannot be undone!')">
                                                        <i class="fas fa-trash-alt"></i> Delete Forever
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-trash fa-4x text-muted mb-3"></i>
                                <h4>Recycle Bin is Empty</h4>
                                <p class="text-muted">Deleted items will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <script>
        function emptyBin() {
            if(confirm('Are you sure you want to empty the recycle bin? This will permanently delete all items and cannot be undone!')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="empty_bin" value="1">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkButtons();
        }
        
        // Update bulk action buttons visibility
        function updateBulkButtons() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            
            if(checkboxes.length > 0) {
                bulkRestoreBtn.style.display = 'inline-block';
                bulkDeleteBtn.style.display = 'inline-block';
                bulkRestoreBtn.innerHTML = '<i class="fas fa-undo"></i> Restore Selected (' + checkboxes.length + ')';
                bulkDeleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Delete Selected (' + checkboxes.length + ')';
            } else {
                bulkRestoreBtn.style.display = 'none';
                bulkDeleteBtn.style.display = 'none';
            }
            
            // Update select all checkbox
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            document.getElementById('selectAll').checked = (checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0);
        }
        
        // Bulk restore function
        function bulkRestore() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            if(checkboxes.length === 0) {
                alert('Please select items to restore');
                return;
            }
            
            if(confirm('Are you sure you want to restore ' + checkboxes.length + ' item(s)?')) {
                const form = document.getElementById('bulkActionForm');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bulk_restore';
                input.value = '1';
                form.appendChild(input);
                form.submit();
            }
        }
        
        // Bulk delete function
        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            if(checkboxes.length === 0) {
                alert('Please select items to delete');
                return;
            }
            
            if(confirm('Are you sure you want to permanently delete ' + checkboxes.length + ' item(s)? This cannot be undone!')) {
                const form = document.getElementById('bulkActionForm');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bulk_permanent_delete';
                input.value = '1';
                form.appendChild(input);
                form.submit();
            }
        }
    </script>
</body>
</html>
