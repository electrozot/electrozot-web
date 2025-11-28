<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$tech_id = isset($_GET['tech_id']) ? intval($_GET['tech_id']) : 0;

if($tech_id <= 0) {
    header("location: admin-stats-technicians.php");
    exit();
}

// Get technician details
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt_tech = $mysqli->prepare($tech_query);
$stmt_tech->bind_param('i', $tech_id);
$stmt_tech->execute();
$tech_result = $stmt_tech->get_result();
$technician = $tech_result->fetch_object();
$stmt_tech->close();

if(!$technician) {
    header("location: admin-stats-technicians.php");
    exit();
}

// Get current month dates
$month_start = date('Y-m-01');
$month_end = date('Y-m-d');
$today = date('Y-m-d');
$commission_rate = 0.20;

// Get monthly bookings
$bookings_query = "SELECT 
                    sb.*,
                    s.s_name,
                    u.u_fname,
                    u.u_lname,
                    u.u_phone
                   FROM tms_service_booking sb
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   WHERE sb.sb_technician_id = ?
                   AND DATE(sb.sb_booking_date) BETWEEN ? AND ?
                   ORDER BY sb.sb_booking_date DESC, sb.sb_booking_time DESC";

$stmt_bookings = $mysqli->prepare($bookings_query);
$stmt_bookings->bind_param('iss', $tech_id, $month_start, $month_end);
$stmt_bookings->execute();
$bookings_result = $stmt_bookings->get_result();

// Calculate statistics
$total_bookings = 0;
$completed_bookings = 0;
$pending_bookings = 0;
$cancelled_bookings = 0;
$total_revenue = 0;
$today_revenue = 0;
$today_jobs = 0;

$bookings = [];
while($booking = $bookings_result->fetch_object()) {
    $bookings[] = $booking;
    $total_bookings++;
    
    if($booking->sb_status == 'Completed') {
        $completed_bookings++;
        $revenue = $booking->sb_bill_amount ?? $booking->sb_final_price ?? $booking->sb_tech_decided_price ?? $booking->sb_total_price ?? 0;
        $total_revenue += $revenue;
        
        if(date('Y-m-d', strtotime($booking->sb_completed_at)) == $today) {
            $today_revenue += $revenue;
            $today_jobs++;
        }
    } elseif($booking->sb_status == 'Pending' || $booking->sb_status == 'Approved' || $booking->sb_status == 'In Progress') {
        $pending_bookings++;
    } elseif($booking->sb_status == 'Cancelled' || $booking->sb_status == 'Rejected') {
        $cancelled_bookings++;
    }
}

$total_commission = round($total_revenue * $commission_rate, 2);
$today_commission = round($today_revenue * $commission_rate, 2);

// Get commission payments
$payments_query = "SELECT * FROM tms_commission_payments 
                   WHERE cp_technician_id = ? 
                   AND DATE(cp_date) BETWEEN ? AND ?
                   ORDER BY cp_date DESC";
$stmt_payments = $mysqli->prepare($payments_query);
$stmt_payments->bind_param('iss', $tech_id, $month_start, $month_end);
$stmt_payments->execute();
$payments_result = $stmt_payments->get_result();

$total_paid = 0;
$payments = [];
while($payment = $payments_result->fetch_object()) {
    $payments[] = $payment;
    $total_paid += $payment->cp_amount;
}

$pending_commission = $total_commission - $total_paid;
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
                    <li class="breadcrumb-item"><a href="admin-stats-technicians.php">Technicians Revenue</a></li>
                    <li class="breadcrumb-item active">Monthly Details</li>
                </ol>

                <!-- Technician Info -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($technician->t_name); ?> - Monthly Report
                            </h6>
                            <a href="admin-stats-technicians.php" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>EZ ID:</strong> <?php echo htmlspecialchars($technician->t_ez_id ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($technician->t_phone); ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($technician->t_category ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Period:</strong> <?php echo date('d M', strtotime($month_start)); ?> - <?php echo date('d M Y', strtotime($month_end)); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bookings</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_bookings; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Jobs</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completed_bookings; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Jobs</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_bookings; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cancelled</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $cancelled_bookings; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Today Jobs</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $today_jobs; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($today_revenue, 0); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Monthly Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($total_revenue, 0); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Commission (20%)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($total_commission, 0); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Paid</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($total_paid, 0); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($pending_commission, 0); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Bookings Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list"></i> Monthly Bookings (<?php echo date('F Y'); ?>)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Address</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($bookings) > 0): ?>
                                        <?php foreach($bookings as $booking): 
                                            $amount = $booking->sb_bill_amount ?? $booking->sb_final_price ?? $booking->sb_tech_decided_price ?? $booking->sb_total_price ?? 0;
                                        ?>
                                            <tr>
                                                <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                                <td><?php echo date('d M Y', strtotime($booking->sb_booking_date)); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($booking->u_phone); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking->s_name ?? $booking->sb_custom_service ?? 'N/A'); ?></td>
                                                <td><small><?php echo htmlspecialchars(substr($booking->sb_address, 0, 40)) . '...'; ?></small></td>
                                                <td class="text-center">
                                                    <?php
                                                    $badge_class = 'secondary';
                                                    if($booking->sb_status == 'Completed') $badge_class = 'success';
                                                    elseif($booking->sb_status == 'In Progress') $badge_class = 'info';
                                                    elseif($booking->sb_status == 'Pending') $badge_class = 'warning';
                                                    elseif($booking->sb_status == 'Cancelled' || $booking->sb_status == 'Rejected') $badge_class = 'danger';
                                                    ?>
                                                    <span class="badge badge-<?php echo $badge_class; ?>">
                                                        <?php echo $booking->sb_status; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-<?php echo $booking->sb_status == 'Completed' ? 'success' : 'muted'; ?>">
                                                        ₹<?php echo number_format($amount, 0); ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No bookings this month</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Commission Payments -->
                <?php if(count($payments) > 0): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-money-bill-wave"></i> Commission Payments This Month
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Notes</th>
                                        <th>Recorded At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payments as $payment): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($payment->cp_date)); ?></td>
                                            <td><strong class="text-success">₹<?php echo number_format($payment->cp_amount, 0); ?></strong></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($payment->cp_payment_method); ?></span></td>
                                            <td><?php echo htmlspecialchars($payment->cp_notes ?? '-'); ?></td>
                                            <td><small class="text-muted"><?php echo date('d M Y h:i A', strtotime($payment->cp_recorded_at)); ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td>TOTAL PAID:</td>
                                        <td colspan="4"><strong class="text-success">₹<?php echo number_format($total_paid, 0); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
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
