<?php
// Widget to show on admin dashboard - Include this in admin-dashboard.php
$today = date('Y-m-d');
$commission_rate = 0.20;

// Count technicians with pending commission
$pending_query = "SELECT COUNT(DISTINCT t.t_id) as pending_count,
                         COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as total_revenue
                  FROM tms_technician t
                  INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
                  WHERE DATE(sb.sb_completed_at) = ? 
                    AND sb.sb_status = 'Completed'";

$stmt_pending = $mysqli->prepare($pending_query);
$stmt_pending->bind_param('s', $today);
$stmt_pending->execute();
$pending_result = $stmt_pending->get_result();
$pending_data = $pending_result->fetch_object();
$stmt_pending->close();

$total_commission = round($pending_data->total_revenue * $commission_rate, 2);

// Get total paid today
$paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as total_paid
               FROM tms_commission_payments 
               WHERE DATE(cp_date) = ?";
$stmt_paid = $mysqli->prepare($paid_query);
$stmt_paid->bind_param('s', $today);
$stmt_paid->execute();
$paid_result = $stmt_paid->get_result();
$paid_data = $paid_result->fetch_object();
$stmt_paid->close();

$total_pending = $total_commission - $paid_data->total_paid;
$pending_count = $pending_data->pending_count;

// Only show if there are pending payments
if($total_pending > 0 && $pending_count > 0):
?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h5 class="alert-heading">
        <i class="fas fa-exclamation-triangle"></i> Pending Commission Payments Alert!
    </h5>
    <p class="mb-2">
        <strong><?php echo $pending_count; ?></strong> technicians have pending commission payments today.
        <br>Total Pending Amount: <strong class="text-danger">₹<?php echo number_format($total_pending, 0); ?></strong>
    </p>
    <hr>
    <div class="mb-0">
        <a href="admin-pending-commissions.php" class="btn btn-warning btn-sm">
            <i class="fas fa-list"></i> View Pending List
        </a>
        <a href="admin-record-commission-payment.php" class="btn btn-primary btn-sm">
            <i class="fas fa-money-bill-wave"></i> Record Payment
        </a>
        <a href="admin-pending-commissions.php?export=csv" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> Export CSV
        </a>
    </div>
</div>
<?php endif; ?>
