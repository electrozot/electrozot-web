<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
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
    $where_conditions[] = "(u.u_fname LIKE ? OR u.u_lname LIKE ? OR sb.sb_phone LIKE ? OR s.s_name LIKE ? OR t.t_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sssss';
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
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">All Bookings</li>
                </ol>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->total; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->pending; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Confirmed</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->confirmed; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">In Progress</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->in_progress; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->completed; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-double fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cancelled</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->cancelled; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Card -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-left-success shadow">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                                        <div class="h3 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($stats->total_revenue, 2); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-rupee-sign fa-3x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-filter"></i> Filters & Search
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Status</option>
                                        <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="In Progress" <?php echo $status_filter == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Date Range</label>
                                    <select name="date" class="form-control">
                                        <option value="all" <?php echo $date_filter == 'all' ? 'selected' : ''; ?>>All Time</option>
                                        <option value="today" <?php echo $date_filter == 'today' ? 'selected' : ''; ?>>Today</option>
                                        <option value="week" <?php echo $date_filter == 'week' ? 'selected' : ''; ?>>This Week</option>
                                        <option value="month" <?php echo $date_filter == 'month' ? 'selected' : ''; ?>>This Month</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Customer, Service, Technician..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php if($status_filter != 'all' || $date_filter != 'all' || !empty($search)): ?>
                            <div class="mt-2">
                                <a href="admin-all-bookings.php" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list"></i> All Bookings
                            <span class="badge badge-primary ml-2"><?php echo $result->num_rows; ?> Results</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Technician</th>
                                            <th>Date & Time</th>
                                            <th>Address</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = $result->fetch_object()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                            <td>
                                                <strong>
                                                    <?php 
                                                    if(!empty($booking->u_fname)) {
                                                        echo $booking->u_fname . ' ' . $booking->u_lname;
                                                    } else {
                                                        echo 'Customer';
                                                    }
                                                    ?>
                                                </strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i> <?php echo $booking->sb_phone; ?><br>
                                                    <?php if(!empty($booking->u_email)): ?>
                                                    <i class="fas fa-envelope"></i> <?php echo $booking->u_email; ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong><?php echo $booking->s_name; ?></strong><br>
                                                <small class="text-muted">
                                                    <span class="badge badge-secondary"><?php echo $booking->s_category; ?></span>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if($booking->t_name): ?>
                                                    <strong><?php echo $booking->t_name; ?></strong><br>
                                                    <small class="text-muted">
                                                        ID: <?php echo $booking->t_id_no; ?><br>
                                                        <i class="fas fa-phone"></i> <?php echo $booking->t_phone; ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Not Assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?><br>
                                                <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                                            </td>
                                            <td>
                                                <small><?php echo isset($booking->sb_address) ? substr($booking->sb_address, 0, 50) : 'N/A'; ?><?php echo isset($booking->sb_address) && strlen($booking->sb_address) > 50 ? '...' : ''; ?></small><br>
                                                <small class="text-muted"><i class="fas fa-phone"></i> <?php echo isset($booking->sb_phone) ? $booking->sb_phone : 'N/A'; ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-success">₹<?php echo isset($booking->sb_total_price) ? number_format($booking->sb_total_price, 2) : '0.00'; ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch($booking->sb_status) {
                                                    case 'Pending': $status_class = 'warning'; break;
                                                    case 'Confirmed': $status_class = 'info'; break;
                                                    case 'In Progress': $status_class = 'primary'; break;
                                                    case 'Completed': $status_class = 'success'; break;
                                                    case 'Cancelled': $status_class = 'danger'; break;
                                                    default: $status_class = 'secondary';
                                                }
                                                ?>
                                                <span class="badge badge-<?php echo $status_class; ?>"><?php echo $booking->sb_status; ?></span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewDetails(<?php echo $booking->sb_id; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if($booking->sb_status == 'Pending'): ?>
                                                    <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-sm btn-success">
                                                        <i class="fas fa-user-plus"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="admin-delete-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this booking?')">
                                                    <i class="fas fa-trash"></i>
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
