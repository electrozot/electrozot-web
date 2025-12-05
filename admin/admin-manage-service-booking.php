<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid=$_SESSION['a_id'];

// Get filter
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get stats
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN sb_status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN sb_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN sb_status = 'Completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN sb_status IN ('Cancelled', 'Rejected', 'Not Done', 'Rejected by Technician') THEN 1 ELSE 0 END) as rejected
    FROM tms_service_booking";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_object();

// Build query
$sql = "SELECT sb.*, 
        COALESCE(u.u_fname, 'Guest') as u_fname,
        COALESCE(u.u_lname, 'Customer') as u_lname,
        s.s_name,
        t.t_name as tech_name,
        CASE 
            WHEN sb.sb_status IN ('Cancelled', 'Rejected', 'Not Done', 'Rejected by Technician') THEN 1
            WHEN sb.sb_status = 'Pending' THEN 2
            WHEN sb.sb_status = 'In Progress' THEN 3
            WHEN sb.sb_status = 'Approved' THEN 4
            ELSE 5
        END as priority_order
        FROM tms_service_booking sb
        LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
        LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
        WHERE 1=1";

// Hide completed bookings by default (unless specifically filtered)
if($status == 'all') {
    $sql .= " AND sb.sb_status != 'Completed'";
}

// Apply status filter
if($status != 'all') {
    if($status == 'pending') {
        $sql .= " AND sb.sb_status = 'Pending'";
    } elseif($status == 'approved') {
        $sql .= " AND sb.sb_status = 'Approved'";
    } elseif($status == 'in_progress') {
        $sql .= " AND sb.sb_status = 'In Progress'";
    } elseif($status == 'completed') {
        $sql .= " AND sb.sb_status = 'Completed'";
    } elseif($status == 'rejected') {
        $sql .= " AND sb.sb_status IN ('Cancelled', 'Rejected', 'Not Done', 'Rejected by Technician')";
    }
}

// Apply search
if($search != '') {
    $search_term = '%' . $search . '%';
    $sql .= " AND (CONCAT(u.u_fname, ' ', u.u_lname) LIKE '$search_term' 
              OR s.s_name LIKE '$search_term' 
              OR t.t_name LIKE '$search_term')";
}

// Order by priority: Rejected > Pending > In Progress > Approved
$sql .= " ORDER BY priority_order ASC, sb.sb_created_at DESC";
$result = $mysqli->query($sql);
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
                
                <!-- Page Title -->
                <h2 class="mb-4"><i class="fas fa-calendar-check"></i> Service Bookings</h2>
                
                <!-- Stats Cards - Priority Order -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="card text-center" style="border-left: 5px solid #dc3545;">
                            <div class="card-body p-3">
                                <h2 class="mb-0" style="color: #dc3545; font-weight: 800;"><?php echo $stats->rejected; ?></h2>
                                <small class="text-muted font-weight-bold">REJECTED</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="card text-center" style="border-left: 5px solid #ffc107;">
                            <div class="card-body p-3">
                                <h2 class="mb-0" style="color: #ffc107; font-weight: 800;"><?php echo $stats->pending; ?></h2>
                                <small class="text-muted font-weight-bold">PENDING</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="card text-center" style="border-left: 5px solid #007bff;">
                            <div class="card-body p-3">
                                <h2 class="mb-0" style="color: #007bff; font-weight: 800;"><?php echo $stats->in_progress; ?></h2>
                                <small class="text-muted font-weight-bold">IN PROGRESS</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="card text-center" style="border-left: 5px solid #17a2b8;">
                            <div class="card-body p-3">
                                <h2 class="mb-0" style="color: #17a2b8; font-weight: 800;"><?php echo $stats->approved; ?></h2>
                                <small class="text-muted font-weight-bold">APPROVED</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters and Table -->
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <!-- Status Filter Buttons - Priority Order -->
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="?status=all" class="btn <?php echo $status=='all' ? 'btn-dark' : 'btn-outline-dark'; ?>">Active</a>
                                    <a href="?status=rejected" class="btn <?php echo $status=='rejected' ? 'btn-danger' : 'btn-outline-danger'; ?>">Rejected</a>
                                    <a href="?status=pending" class="btn <?php echo $status=='pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">Pending</a>
                                    <a href="?status=in_progress" class="btn <?php echo $status=='in_progress' ? 'btn-primary' : 'btn-outline-primary'; ?>">In Progress</a>
                                    <a href="?status=approved" class="btn <?php echo $status=='approved' ? 'btn-info' : 'btn-outline-info'; ?>">Approved</a>
                                    <a href="?status=completed" class="btn <?php echo $status=='completed' ? 'btn-success' : 'btn-outline-success'; ?>">Completed</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Search -->
                                <form method="get" class="form-inline float-right">
                                    <input type="hidden" name="status" value="<?php echo $status; ?>">
                                    <input type="text" name="search" class="form-control form-control-sm mr-1" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                                    <?php if($search): ?>
                                    <a href="?status=<?php echo $status; ?>" class="btn btn-sm btn-link">Clear</a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Date & Time</th>
                                        <th>Technician</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                            <p class="text-muted">No bookings found</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php while($row = $result->fetch_object()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $row->sb_id; ?></strong></td>
                                        <td><?php echo htmlspecialchars($row->u_fname . ' ' . $row->u_lname); ?></td>
                                        <td><?php echo htmlspecialchars($row->s_name); ?></td>
                                        <td>
                                            <div><?php echo date('M d, Y', strtotime($row->sb_booking_date)); ?></div>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime($row->sb_booking_time)); ?></small>
                                        </td>
                                        <td>
                                            <?php if($row->tech_name): ?>
                                                <span class="badge badge-info"><?php echo htmlspecialchars($row->tech_name); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Not Assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_class = 'secondary';
                                            $status_icon = '';
                                            if($row->sb_status == 'Pending') { 
                                                $status_class = 'warning'; 
                                                $status_icon = '⏳';
                                            } elseif($row->sb_status == 'Approved') { 
                                                $status_class = 'info'; 
                                                $status_icon = '✓';
                                            } elseif($row->sb_status == 'In Progress') { 
                                                $status_class = 'primary'; 
                                                $status_icon = '🔄';
                                            } elseif($row->sb_status == 'Completed') { 
                                                $status_class = 'success'; 
                                                $status_icon = '✓';
                                            } else { 
                                                $status_class = 'danger'; 
                                                $status_icon = '✗';
                                            }
                                            ?>
                                            <span class="badge badge-<?php echo $status_class; ?>">
                                                <?php echo $status_icon . ' ' . strtoupper($row->sb_status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="admin-view-service-booking.php?sb_id=<?php echo $row->sb_id; ?>" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($row->sb_status != 'Cancelled' && $row->sb_status != 'Completed'): ?>
                                            <a href="admin-assign-technician.php?sb_id=<?php echo $row->sb_id; ?>" class="btn btn-sm btn-success" title="Assign">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
</body>
</html>
