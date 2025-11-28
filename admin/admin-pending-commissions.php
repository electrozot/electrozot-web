<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get today's date
$today = date('Y-m-d');
$commission_rate = 0.20;

// Get only technicians with pending commission payments for today
$query = "SELECT 
            t.t_id,
            t.t_name, 
            t.t_ez_id, 
            t.t_phone, 
            t.t_category,
            COUNT(sb.sb_id) as today_jobs,
            COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as today_revenue
          FROM tms_technician t
          INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
          WHERE DATE(sb.sb_completed_at) = ? 
            AND sb.sb_status = 'Completed'
          GROUP BY t.t_id
          HAVING today_revenue > 0
          ORDER BY today_revenue DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();

$pending_techs = [];
$total_pending_commission = 0;
$total_pending_count = 0;

while($row = $result->fetch_object()) {
    // Calculate commission
    $row->today_commission = round($row->today_revenue * $commission_rate, 2);
    
    // Get paid amount
    $paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_today
                   FROM tms_commission_payments 
                   WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
    $stmt_paid = $mysqli->prepare($paid_query);
    $stmt_paid->bind_param('is', $row->t_id, $today);
    $stmt_paid->execute();
    $paid_result = $stmt_paid->get_result();
    $paid_data = $paid_result->fetch_object();
    $stmt_paid->close();
    
    $row->today_paid = $paid_data->paid_today;
    $row->today_pending = $row->today_commission - $row->today_paid;
    
    // Only add if there's pending payment
    if($row->today_pending > 0) {
        $pending_techs[] = $row;
        $total_pending_commission += $row->today_pending;
        $total_pending_count++;
    }
}

// Export to CSV functionality
if(isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pending_commissions_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['#', 'Technician Name', 'EZ ID', 'Phone', 'Jobs', 'Revenue', 'Commission (20%)', 'Paid', 'Pending']);
    
    $rank = 1;
    foreach($pending_techs as $tech) {
        fputcsv($output, [
            $rank++,
            $tech->t_name,
            $tech->t_ez_id,
            $tech->t_phone,
            $tech->today_jobs,
            $tech->today_revenue,
            $tech->today_commission,
            $tech->today_paid,
            $tech->today_pending
        ]);
    }
    
    fclose($output);
    exit();
}
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
                    <li class="breadcrumb-item active">Pending Commissions</li>
                </ol>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="font-weight-bold text-danger mb-1">
                            <i class="fas fa-exclamation-triangle"></i> Pending Commission Payments
                        </h3>
                        <p class="text-muted mb-0">Date: <strong><?php echo date('d M Y'); ?></strong></p>
                    </div>
                    <div>
                        <a href="?export=csv" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export CSV
                        </a>
                        <a href="admin-record-commission-payment.php" class="btn btn-primary">
                            <i class="fas fa-money-bill-wave"></i> Record Payment
                        </a>
                    </div>
                </div>

                <!-- Alert Summary -->
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h5><i class="fas fa-exclamation-circle"></i> <strong><?php echo $total_pending_count; ?></strong> technicians have pending payments today!</h5>
                    <p class="mb-0">Total Pending Commission: <strong>₹<?php echo number_format($total_pending_commission, 0); ?></strong></p>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Technicians with Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_pending_count; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pending Amount</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($total_pending_commission, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Pending per Tech</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ₹<?php echo $total_pending_count > 0 ? number_format($total_pending_commission / $total_pending_count, 0) : 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Technicians Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-warning">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-list"></i> Technicians with Pending Commission (Today)
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if(count($pending_techs) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="pendingTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Technician</th>
                                            <th>EZ ID</th>
                                            <th>Phone</th>
                                            <th class="text-center">Jobs</th>
                                            <th class="text-right">Revenue</th>
                                            <th class="text-right">Commission (20%)</th>
                                            <th class="text-right">Paid</th>
                                            <th class="text-right">Pending</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rank = 1;
                                        foreach($pending_techs as $tech): 
                                        ?>
                                            <tr style="background-color: #fff3cd;">
                                                <td><?php echo $rank; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($tech->t_name); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($tech->t_category ?? 'N/A'); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo htmlspecialchars($tech->t_ez_id ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="tel:<?php echo $tech->t_phone; ?>" class="text-primary">
                                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($tech->t_phone); ?>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success badge-pill">
                                                        <?php echo $tech->today_jobs; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-success">
                                                        ₹<?php echo number_format($tech->today_revenue, 0); ?>
                                                    </strong>
                                                </td>
                                                <td class="text-right">
                                                    <span class="text-danger">
                                                        ₹<?php echo number_format($tech->today_commission, 0); ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="text-info">
                                                        ₹<?php echo number_format($tech->today_paid, 0); ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> 
                                                        ₹<?php echo number_format($tech->today_pending, 0); ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <a href="admin-record-commission-payment.php?tech_id=<?php echo $tech->t_id; ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-money-bill"></i> Record
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php 
                                            $rank++;
                                        endforeach; 
                                        ?>
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="8" class="text-right">TOTAL PENDING:</td>
                                            <td class="text-right text-danger">
                                                <strong>₹<?php echo number_format($total_pending_commission, 0); ?></strong>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                                <h4 class="text-success">All Clear!</h4>
                                <p class="text-muted">No pending commission payments for today.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> This page shows only technicians who have pending commission payments. Use the "Export CSV" button to download the list for your records.
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
