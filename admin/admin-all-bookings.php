<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get session messages
$delete_success = isset($_SESSION['delete_success']) ? $_SESSION['delete_success'] : '';
$delete_error = isset($_SESSION['delete_error']) ? $_SESSION['delete_error'] : '';
unset($_SESSION['delete_success']);
unset($_SESSION['delete_error']);

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
$tech_filter = isset($_GET['technician']) ? $_GET['technician'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where_conditions = [];
$params = [];
$types = '';

if($status_filter != 'all') {
    $where_conditions[] = "sb.sb_status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if($tech_filter == 'assigned') {
    $where_conditions[] = "sb.sb_technician_id IS NOT NULL";
} elseif($tech_filter == 'unassigned') {
    $where_conditions[] = "sb.sb_technician_id IS NULL";
}

if($date_filter != 'all') {
    switch($date_filter) {
        case 'today':
            $where_conditions[] = "DATE(sb.sb_booking_date) = CURDATE()";
            break;
        case 'week':
            $where_conditions[] = "YEARWEEK(sb.sb_booking_date) = YEARWEEK(NOW())";
            break;
        case 'month':
            $where_conditions[] = "MONTH(sb.sb_booking_date) = MONTH(NOW()) AND YEAR(sb.sb_booking_date) = YEAR(NOW())";
            break;
    }
}

if(!empty($search)) {
    $where_conditions[] = "(u.u_fname LIKE ? OR u.u_lname LIKE ? OR u.u_phone LIKE ? OR s.s_name LIKE ? OR t.t_name LIKE ? OR sb.sb_id LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssssss';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get all bookings with details
$query = "SELECT 
            sb.*,
            u.u_fname, u.u_lname, u.u_phone, u.u_email, u.u_addr,
            s.s_name, s.s_category, s.s_price, 
            COALESCE(s.s_description, '') as s_description,
            t.t_name, t.t_id_no, 
            COALESCE(t.t_phone, '') as t_phone, 
            COALESCE(t.t_specialization, '') as t_specialization
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          $where_clause
          ORDER BY sb.sb_id DESC";

$stmt = $mysqli->prepare($query);
if(!$stmt) {
    die("Query preparation failed: " . $mysqli->error);
}
if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
if(!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}
$result = $stmt->get_result();

// Get statistics
$stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN sb_status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN sb_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN sb_status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN sb_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(sb_total_price) as total_revenue
                FROM tms_service_booking";
$stats_result = $mysqli->query($stats_query);
$stats = $stats_result->fetch_object();
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
                <!-- Success/Error Messages -->
                <?php if(!empty($delete_success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $delete_success; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                <?php if(!empty($delete_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $delete_error; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">All Bookings</li>
                </ol>

                <!-- Compact Statistics Cards - Single Line -->
                <div class="d-flex flex-wrap mb-2" style="gap: 8px;">
                    <!-- Total -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #4e73df;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #4e73df; font-weight: 600;">TOTAL</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #333;"><?php echo $stats->total; ?></div>
                        </div>
                    </div>
                    
                    <!-- Pending -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #f6c23e;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #f6c23e; font-weight: 600;">PENDING</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #333;"><?php echo $stats->pending; ?></div>
                        </div>
                    </div>
                    
                    <!-- Confirmed -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #36b9cc;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #36b9cc; font-weight: 600;">CONFIRMED</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #333;"><?php echo $stats->confirmed; ?></div>
                        </div>
                    </div>
                    
                    <!-- In Progress -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #4e73df;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #4e73df; font-weight: 600;">PROGRESS</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #333;"><?php echo $stats->in_progress; ?></div>
                        </div>
                    </div>
                    
                    <!-- Completed -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #1cc88a;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #1cc88a; font-weight: 600;">COMPLETED</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #333;"><?php echo $stats->completed; ?></div>
                        </div>
                    </div>
                    
                    <!-- Cancelled -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #e74a3b;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #e74a3b; font-weight: 600;">CANCELLED</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #333;"><?php echo $stats->cancelled; ?></div>
                        </div>
                    </div>
                    
                    <!-- Unassigned (Yellow) -->
                    <?php
                    $unassigned_count = $mysqli->query("SELECT COUNT(*) as total FROM tms_service_booking WHERE sb_technician_id IS NULL AND sb_status NOT IN ('Rejected', 'Cancelled', 'Completed')")->fetch_object()->total;
                    ?>
                    <div class="card shadow-sm" style="flex: 1; min-width: 120px; border-left: 3px solid #ffc107; background-color: #fff9e6;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #f6a000; font-weight: 600;">UNASSIGNED</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #f6a000;"><?php echo $unassigned_count; ?></div>
                        </div>
                    </div>
                    
                    <!-- Revenue -->
                    <div class="card shadow-sm" style="flex: 1; min-width: 140px; border-left: 3px solid #1cc88a;">
                        <div class="card-body p-2 text-center">
                            <div style="font-size: 0.65rem; color: #1cc88a; font-weight: 600;">REVENUE</div>
                            <div style="font-size: 1.3rem; font-weight: 700; color: #1cc88a;">₹<?php echo number_format($stats->total_revenue, 0); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Simple Filters -->
                <div class="card mb-2 shadow-sm">
                    <div class="card-body py-2 px-3">
                        <form method="GET" action="" class="form-inline" id="filterForm">
                            <small class="mr-2 text-muted"><i class="fas fa-filter" style="font-size: 0.7rem;"></i></small>
                            
                            <select name="status" class="form-control form-control-sm mr-2 mb-1" style="width: 120px; font-size: 0.8rem;" onchange="this.form.submit();">
                                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>📋 All Status</option>
                                <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                <option value="Approved" <?php echo $status_filter == 'Approved' ? 'selected' : ''; ?>>✓ Approved</option>
                                <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>✓ Confirmed</option>
                                <option value="In Progress" <?php echo $status_filter == 'In Progress' ? 'selected' : ''; ?>>🔧 In Progress</option>
                                <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>✅ Completed</option>
                                <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                                <option value="Rejected" <?php echo $status_filter == 'Rejected' ? 'selected' : ''; ?>>🚫 Rejected</option>
                            </select>
                            
                            <select name="technician" class="form-control form-control-sm mr-2 mb-1" style="width: 110px; font-size: 0.8rem;" onchange="this.form.submit();">
                                <option value="all" <?php echo $tech_filter == 'all' ? 'selected' : ''; ?>>👷 All Tech</option>
                                <option value="assigned" <?php echo $tech_filter == 'assigned' ? 'selected' : ''; ?>>✓ Assigned</option>
                                <option value="unassigned" <?php echo $tech_filter == 'unassigned' ? 'selected' : ''; ?>>⚠ Unassigned</option>
                            </select>
                            
                            <select name="date" class="form-control form-control-sm mr-2 mb-1" style="width: 110px; font-size: 0.8rem;" onchange="this.form.submit();">
                                <option value="all" <?php echo $date_filter == 'all' ? 'selected' : ''; ?>>📅 All Time</option>
                                <option value="today" <?php echo $date_filter == 'today' ? 'selected' : ''; ?>>📅 Today</option>
                                <option value="week" <?php echo $date_filter == 'week' ? 'selected' : ''; ?>>📅 Week</option>
                                <option value="month" <?php echo $date_filter == 'month' ? 'selected' : ''; ?>>📅 Month</option>
                            </select>
                            
                            <input type="text" name="search" class="form-control form-control-sm mr-2 mb-1" 
                                   placeholder="🔍 Search..." value="<?php echo htmlspecialchars($search); ?>" style="width: 200px; font-size: 0.8rem;">
                            
                            <button type="submit" class="btn btn-primary btn-sm mr-2 mb-1" style="font-size: 0.75rem; padding: 2px 8px;">
                                <i class="fas fa-search" style="font-size: 0.7rem;"></i> Go
                            </button>
                            
                            <?php if($status_filter != 'all' || $date_filter != 'all' || $tech_filter != 'all' || !empty($search)): ?>
                                <a href="admin-all-bookings.php" class="btn btn-outline-secondary btn-sm mb-1" style="font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="fas fa-times" style="font-size: 0.7rem;"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-list"></i> All Bookings - Complete Details
                            <span class="badge badge-light ml-2"><?php echo $result->num_rows; ?> Results</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover" id="dataTable" style="font-size: 0.85rem;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th style="width: 150px;">Customer</th>
                                            <th style="width: 130px;">Service</th>
                                            <th style="width: 130px;">Technician</th>
                                            <th style="width: 110px;">Date/Time</th>
                                            <th style="width: 150px;">Location</th>
                                            <th style="width: 70px;">Price</th>
                                            <th style="width: 80px;">Status</th>
                                            <th style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = $result->fetch_object()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                            <td>
                                                <strong style="font-size: 0.9rem;">
                                                    <?php 
                                                    if(!empty($booking->u_fname)) {
                                                        echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname);
                                                    } else {
                                                        echo '<span class="text-muted">Guest Customer</span>';
                                                    }
                                                    ?>
                                                </strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i> <?php echo !empty($booking->u_phone) ? $booking->u_phone : (!empty($booking->sb_phone) ? $booking->sb_phone : 'N/A'); ?>
                                                    <?php if(!empty($booking->u_email)): ?>
                                                    <br><i class="fas fa-envelope"></i> <?php echo substr($booking->u_email, 0, 20); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong><?php echo !empty($booking->s_name) ? htmlspecialchars($booking->s_name) : '<span class="text-muted">Service Deleted</span>'; ?></strong><br>
                                                <?php if(!empty($booking->s_category)): ?>
                                                <span class="badge badge-secondary badge-sm"><?php echo htmlspecialchars($booking->s_category); ?></span>
                                                <?php endif; ?>
                                                <?php if(!empty($booking->sb_description)): ?>
                                                <br><small class="text-muted" title="<?php echo htmlspecialchars($booking->sb_description); ?>">
                                                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars(substr($booking->sb_description, 0, 20)); ?>...
                                                </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($booking->t_name)): ?>
                                                    <strong><?php echo htmlspecialchars($booking->t_name); ?></strong><br>
                                                    <small class="text-muted">
                                                        <?php if(!empty($booking->t_id_no)): ?>
                                                        ID: <?php echo htmlspecialchars($booking->t_id_no); ?>
                                                        <?php endif; ?>
                                                        <?php if(!empty($booking->t_phone)): ?>
                                                        <br><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking->t_phone); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">⚠ Unassigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($booking->sb_booking_date)): ?>
                                                <i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($booking->sb_booking_date)); ?><br>
                                                <?php endif; ?>
                                                <?php if(!empty($booking->sb_booking_time)): ?>
                                                <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($booking->sb_address)): ?>
                                                <small title="<?php echo htmlspecialchars($booking->sb_address); ?>">
                                                    <?php echo htmlspecialchars(substr($booking->sb_address, 0, 30)); ?><?php echo strlen($booking->sb_address) > 30 ? '...' : ''; ?>
                                                </small>
                                                <?php else: ?>
                                                <small class="text-muted">No address</small>
                                                <?php endif; ?>
                                                <?php if(!empty($booking->sb_pincode)): ?>
                                                <br><span class="badge badge-info badge-sm"><?php echo htmlspecialchars($booking->sb_pincode); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong class="text-success">₹<?php echo isset($booking->sb_total_price) ? number_format($booking->sb_total_price, 0) : '0'; ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch($booking->sb_status) {
                                                    case 'Pending': $status_class = 'warning'; break;
                                                    case 'Confirmed': $status_class = 'info'; break;
                                                    case 'Approved': $status_class = 'info'; break;
                                                    case 'In Progress': $status_class = 'primary'; break;
                                                    case 'Completed': $status_class = 'success'; break;
                                                    case 'Cancelled': $status_class = 'danger'; break;
                                                    case 'Rejected': $status_class = 'danger'; break;
                                                    default: $status_class = 'secondary';
                                                }
                                                ?>
                                                <span class="badge badge-<?php echo $status_class; ?>"><?php echo $booking->sb_status; ?></span>
                                                <?php if($booking->sb_status == 'Rejected' && !empty($booking->sb_rejection_reason)): ?>
                                                <br><small class="text-danger" title="<?php echo htmlspecialchars($booking->sb_rejection_reason); ?>">
                                                    <i class="fas fa-exclamation-circle"></i> Reason
                                                </small>
                                                <?php endif; ?>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(<?php echo $booking->sb_id; ?>)" title="View Full Details" style="padding: 1px 4px; cursor: pointer;">
                                                    <i class="fas fa-eye" style="font-size: 0.65rem;"></i>
                                                </button>
                                                <?php if($booking->sb_status == 'Pending' || $booking->sb_status == 'Approved'): ?>
                                                    <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-sm btn-outline-success" title="Assign Technician" style="padding: 1px 4px;">
                                                        <i class="fas fa-user-plus" style="font-size: 0.65rem;"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if($booking->sb_status != 'Cancelled' && $booking->sb_status != 'Completed'): ?>
                                                    <a href="admin-cancel-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-sm btn-outline-warning" onclick="return confirm('Cancel this booking? Technician will be freed up.');" title="Cancel Booking" style="padding: 1px 4px;">
                                                        <i class="fas fa-ban" style="font-size: 0.65rem;"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="admin-delete-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this booking permanently? This cannot be undone!');" title="Delete Permanently" style="padding: 1px 4px;">
                                                    <i class="fas fa-trash" style="font-size: 0.65rem;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h4>No Bookings Found</h4>
                                <p class="text-muted">Try adjusting your filters or search criteria</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Booking Details</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 25
            });
        });

        function viewDetails(bookingId) {
            $('#detailsModal').modal('show');
            $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading...</p></div>');
            
            $.ajax({
                url: 'ajax-get-booking-details.php',
                method: 'GET',
                data: { sb_id: bookingId },
                success: function(response) {
                    $('#modalContent').html(response);
                },
                error: function() {
                    $('#modalContent').html('<div class="alert alert-danger">Failed to load booking details</div>');
                }
            });
        }
    </script>
</body>
</html>
