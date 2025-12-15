<?php
session_start();
include('../admin/vendor/inc/config.php');
include('../admin/vendor/inc/checklogin.php');
check_login();

$tech_id = $_SESSION['t_id'];

// Get today's date and month
$today = date('Y-m-d');
$month_start = date('Y-m-01');
$month_end = date('Y-m-d');

// Commission rate
$commission_rate = 0.20;

// Get technician revenue and commission
$query = "SELECT 
            COUNT(CASE WHEN DATE(sb_completed_at) = ? THEN 1 END) as today_jobs,
            COALESCE(SUM(CASE WHEN DATE(sb_completed_at) = ? THEN COALESCE(sb_bill_amount, sb_final_price, sb_tech_decided_price, sb_total_price, 0) END), 0) as today_revenue,
            COUNT(CASE WHEN DATE(sb_completed_at) BETWEEN ? AND ? THEN 1 END) as monthly_jobs,
            COALESCE(SUM(CASE WHEN DATE(sb_completed_at) BETWEEN ? AND ? THEN COALESCE(sb_bill_amount, sb_final_price, sb_tech_decided_price, sb_total_price, 0) END), 0) as monthly_revenue
          FROM tms_service_booking
          WHERE sb_technician_id = ? AND sb_status = 'Completed'";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ssssssi', $today, $today, $month_start, $month_end, $month_start, $month_end, $tech_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_object();
$stmt->close();

// Calculate commission
$today_commission = round($data->today_revenue * $commission_rate, 2);
$monthly_commission = round($data->monthly_revenue * $commission_rate, 2);

// Get paid amounts
$paid_query = "SELECT 
                COALESCE(SUM(CASE WHEN DATE(cp_date) = ? THEN cp_amount END), 0) as paid_today,
                COALESCE(SUM(CASE WHEN DATE(cp_date) BETWEEN ? AND ? THEN cp_amount END), 0) as paid_monthly
               FROM tms_commission_payments 
               WHERE cp_technician_id = ?";
$stmt_paid = $mysqli->prepare($paid_query);
$stmt_paid->bind_param('sssi', $today, $month_start, $month_end, $tech_id);
$stmt_paid->execute();
$paid_result = $stmt_paid->get_result();
$paid_data = $paid_result->fetch_object();
$stmt_paid->close();

$today_paid = $paid_data->paid_today;
$monthly_paid = $paid_data->paid_monthly;
$today_pending = $today_commission - $today_paid;
$monthly_pending = $monthly_commission - $monthly_paid;

// Get payment history
$history_query = "SELECT * FROM tms_commission_payments 
                  WHERE cp_technician_id = ? 
                  ORDER BY cp_date DESC, cp_recorded_at DESC 
                  LIMIT 20";
$stmt_history = $mysqli->prepare($history_query);
$stmt_history->bind_param('i', $tech_id);
$stmt_history->execute();
$history_result = $stmt_history->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('../admin/vendor/inc/head.php');?>
<body id="page-top">
    <?php include("../admin/vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("../admin/vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="tech-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Commission Details</li>
                </ol>

                <h3 class="mb-4"><i class="fas fa-hand-holding-usd"></i> My Commission to Electrozot (20%)</h3>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($data->today_revenue, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Today's Commission (20%)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($today_commission, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today Paid</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($today_paid, 0); ?></div>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Today Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($today_pending, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Monthly Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($data->monthly_revenue, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Monthly Commission (20%)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($monthly_commission, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Monthly Paid</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($monthly_paid, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-double fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Monthly Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($monthly_pending, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history"></i> My Payment History
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount Paid</th>
                                        <th>Payment Method</th>
                                        <th>Notes</th>
                                        <th>Recorded At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($history_result && $history_result->num_rows > 0): ?>
                                        <?php while($payment = $history_result->fetch_object()): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($payment->cp_date)); ?></td>
                                                <td><strong class="text-success">₹<?php echo number_format($payment->cp_amount, 0); ?></strong></td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($payment->cp_payment_method); ?></span></td>
                                                <td><?php echo htmlspecialchars($payment->cp_notes ?? '-'); ?></td>
                                                <td><small class="text-muted"><?php echo date('d M Y h:i A', strtotime($payment->cp_recorded_at)); ?></small></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No payment history yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> Electrozot charges 20% commission on all completed jobs. Please ensure daily payment to avoid pending dues.
                </div>
                
            </div>
            <?php include('../admin/vendor/inc/footer.php');?>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../admin/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../admin/js/sb-admin.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
