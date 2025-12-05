<?php
/**
 * COMMISSION STATUS WIDGET
 * Include this in admin dashboard to show commission overview
 * Usage: include('widget-commission-status.php');
 */

// Get today's pending commissions
$today = date('Y-m-d');
$commission_rate = 0.20;

$query = "SELECT 
            COUNT(DISTINCT t.t_id) as tech_count,
            COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as total_revenue
          FROM tms_technician t
          INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
          WHERE DATE(sb.sb_completed_at) = ? 
            AND sb.sb_status = 'Completed'";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();
$today_data = $result->fetch_object();
$stmt->close();

$today_commission = round($today_data->total_revenue * $commission_rate, 0);

// Get paid amount today
$paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_today
               FROM tms_commission_payments 
               WHERE DATE(cp_date) = ?";
$stmt_paid = $mysqli->prepare($paid_query);
$stmt_paid->bind_param('s', $today);
$stmt_paid->execute();
$paid_result = $stmt_paid->get_result();
$paid_data = $paid_result->fetch_object();
$stmt_paid->close();

$pending_today = $today_commission - $paid_data->paid_today;

// Get locked technicians count
$locked_query = "SELECT COUNT(*) as locked_count FROM tms_technician WHERE account_locked = 1";
$locked_result = $mysqli->query($locked_query);
$locked_data = $locked_result->fetch_object();
$locked_count = $locked_data->locked_count;

// Get last cron run time
$last_run = "Not run yet";
$log_file = __DIR__ . '/cron-last-run.log';
if(file_exists($log_file)) {
    $log_lines = file($log_file);
    if(count($log_lines) > 0) {
        $last_line = trim(end($log_lines));
        if(preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $last_line, $matches)) {
            $last_run = date('d M Y h:i A', strtotime($matches[1]));
        }
    }
}
?>

<div class="row mb-4">
    <!-- Today's Commission -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Today's Commission Due
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ₹<?php echo number_format($today_commission, 0); ?>
                        </div>
                        <small class="text-muted"><?php echo $today_data->tech_count; ?> technicians</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php echo $pending_today > 0 ? 'warning' : 'success'; ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?php echo $pending_today > 0 ? 'warning' : 'success'; ?> text-uppercase mb-1">
                            Pending Today
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ₹<?php echo number_format($pending_today, 0); ?>
                        </div>
                        <small class="text-muted">Paid: ₹<?php echo number_format($paid_data->paid_today, 0); ?></small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-<?php echo $pending_today > 0 ? 'exclamation-triangle' : 'check-circle'; ?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Locked Accounts -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php echo $locked_count > 0 ? 'danger' : 'success'; ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?php echo $locked_count > 0 ? 'danger' : 'success'; ?> text-uppercase mb-1">
                            Locked Accounts
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $locked_count; ?>
                        </div>
                        <?php if($locked_count > 0): ?>
                            <a href="admin-unlock-technician.php" class="small text-danger">View locked →</a>
                        <?php else: ?>
                            <small class="text-success">All clear!</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-lock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Auto-Lock Run -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Last Auto-Lock
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800" style="font-size: 0.9rem;">
                            <?php echo $last_run; ?>
                        </div>
                        <a href="run-commission-lock-manually.php" class="small text-info">Run manually →</a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($locked_count > 0): ?>
<!-- Alert for locked accounts -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-lock"></i> <?php echo $locked_count; ?> Technician Account(s) Locked!</strong>
    <br>These technicians cannot receive new bookings until they pay their commission.
    <a href="admin-unlock-technician.php" class="alert-link">View and unlock →</a>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<?php if($pending_today > 0): ?>
<!-- Alert for pending payments -->
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-exclamation-triangle"></i> ₹<?php echo number_format($pending_today, 0); ?> Commission Pending Today!</strong>
    <br>Remind technicians to pay before 7 AM tomorrow to avoid account lock.
    <a href="admin-pending-commissions.php" class="alert-link">View details →</a>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>
