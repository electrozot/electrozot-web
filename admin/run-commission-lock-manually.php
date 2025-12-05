<?php
/**
 * MANUAL COMMISSION LOCK EXECUTION
 * Use this page to manually run the commission lock system
 * Useful for testing or if cron job fails
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$output = [];
$yesterday = date('Y-m-d', strtotime('-1 day'));
$commission_rate = 0.20;

// Ensure tables exist
$output[] = "Checking database tables...";
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS lock_reason TEXT");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL");

$create_table = "CREATE TABLE IF NOT EXISTS tms_commission_payments (
    cp_id INT AUTO_INCREMENT PRIMARY KEY,
    cp_technician_id INT NOT NULL,
    cp_amount DECIMAL(10,2) NOT NULL,
    cp_date DATE NOT NULL,
    cp_payment_method VARCHAR(50) DEFAULT 'Cash',
    cp_notes TEXT,
    cp_recorded_by INT,
    cp_recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tech_date (cp_technician_id, cp_date),
    FOREIGN KEY (cp_technician_id) REFERENCES tms_technician(t_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$mysqli->query($create_table);

$create_logs = "CREATE TABLE IF NOT EXISTS tms_system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    log_type VARCHAR(50) NOT NULL,
    log_message TEXT NOT NULL,
    log_data TEXT,
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (log_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$mysqli->query($create_logs);

$output[] = "✓ Database tables ready";

// Get all technicians who worked yesterday
$output[] = "Checking technicians who worked on: " . date('d M Y', strtotime($yesterday));

$query = "SELECT 
            t.t_id,
            t.t_name,
            t.t_phone,
            t.account_locked,
            COUNT(sb.sb_id) as yesterday_jobs,
            COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as yesterday_revenue
          FROM tms_technician t
          INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
          WHERE DATE(sb.sb_completed_at) = ? 
            AND sb.sb_status = 'Completed'
          GROUP BY t.t_id";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $yesterday);
$stmt->execute();
$result = $stmt->get_result();

$locked_count = 0;
$already_locked = 0;
$paid_count = 0;
$locked_techs = [];
$tech_details = [];

while($tech = $result->fetch_object()) {
    // Calculate commission
    $commission = round($tech->yesterday_revenue * $commission_rate, 0);
    
    if($commission > 0) {
        // Check if payment was made
        $paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_amount
                       FROM tms_commission_payments 
                       WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
        $stmt_paid = $mysqli->prepare($paid_query);
        $stmt_paid->bind_param('is', $tech->t_id, $yesterday);
        $stmt_paid->execute();
        $paid_result = $stmt_paid->get_result();
        $paid_data = $paid_result->fetch_object();
        $stmt_paid->close();
        
        $pending = $commission - $paid_data->paid_amount;
        
        $tech_details[] = [
            'name' => $tech->t_name,
            'jobs' => $tech->yesterday_jobs,
            'revenue' => $tech->yesterday_revenue,
            'commission' => $commission,
            'paid' => $paid_data->paid_amount,
            'pending' => $pending,
            'was_locked' => $tech->account_locked,
            'action' => ''
        ];
        
        // If payment not done, lock the account
        if($pending > 0) {
            if($tech->account_locked == 1) {
                $already_locked++;
                $tech_details[count($tech_details)-1]['action'] = 'Already Locked';
            } else {
                $lock_reason = "Unpaid Electrozot charges for " . date('d M Y', strtotime($yesterday)) . ". Amount Due: ₹" . number_format($commission, 0) . ". Please complete payment and contact Electrozot Admin to unlock your account.";
                
                $lock_query = "UPDATE tms_technician 
                              SET account_locked = 1, 
                                  lock_reason = ?, 
                                  locked_at = NOW() 
                              WHERE t_id = ?";
                $stmt_lock = $mysqli->prepare($lock_query);
                $stmt_lock->bind_param('si', $lock_reason, $tech->t_id);
                $stmt_lock->execute();
                $stmt_lock->close();
                
                $locked_count++;
                $locked_techs[] = [
                    'name' => $tech->t_name,
                    'phone' => $tech->t_phone,
                    'commission' => $commission,
                    'pending' => $pending
                ];
                
                $tech_details[count($tech_details)-1]['action'] = '🔒 LOCKED NOW';
            }
        } else {
            $paid_count++;
            $tech_details[count($tech_details)-1]['action'] = '✓ Paid';
        }
    }
}

// Log the action
if($locked_count > 0) {
    $log_query = "INSERT INTO tms_system_logs (log_type, log_message, log_data, log_date) 
                  VALUES ('account_lock', ?, ?, NOW())";
    $stmt_log = $mysqli->prepare($log_query);
    $log_message = "{$locked_count} technician accounts locked for unpaid commission (Manual Run)";
    $log_data = json_encode(['locked_count' => $locked_count, 'date' => $yesterday, 'technicians' => $locked_techs, 'run_by' => $_SESSION['a_email']]);
    $stmt_log->bind_param('ss', $log_message, $log_data);
    $stmt_log->execute();
    $stmt_log->close();
}

$output[] = "✓ Processing complete";
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
                    <li class="breadcrumb-item active">Manual Commission Lock</li>
                </ol>

                <h3 class="mb-4">
                    <i class="fas fa-play-circle"></i> Manual Commission Lock Execution
                </h3>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Newly Locked</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $locked_count; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Already Locked</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $already_locked; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $paid_count; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Checked</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($tech_details); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Execution Log -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Execution Log</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Date Checked:</strong> <?php echo date('d M Y', strtotime($yesterday)); ?> (Yesterday)<br>
                            <strong>Execution Time:</strong> <?php echo date('d M Y h:i:s A'); ?><br>
                            <strong>Commission Rate:</strong> 20%
                        </div>
                        <?php foreach($output as $line): ?>
                            <p class="mb-1"><?php echo $line; ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Technician Details -->
                <?php if(count($tech_details) > 0): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Technician Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Technician</th>
                                        <th>Jobs</th>
                                        <th>Revenue</th>
                                        <th>Commission (20%)</th>
                                        <th>Paid</th>
                                        <th>Pending</th>
                                        <th>Action Taken</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    foreach($tech_details as $detail): 
                                        $row_class = '';
                                        if($detail['action'] == '🔒 LOCKED NOW') {
                                            $row_class = 'table-danger';
                                        } elseif($detail['action'] == 'Already Locked') {
                                            $row_class = 'table-warning';
                                        } elseif($detail['action'] == '✓ Paid') {
                                            $row_class = 'table-success';
                                        }
                                    ?>
                                        <tr class="<?php echo $row_class; ?>">
                                            <td><?php echo $i++; ?></td>
                                            <td><strong><?php echo htmlspecialchars($detail['name']); ?></strong></td>
                                            <td><?php echo $detail['jobs']; ?></td>
                                            <td>₹<?php echo number_format($detail['revenue'], 0); ?></td>
                                            <td><strong>₹<?php echo number_format($detail['commission'], 0); ?></strong></td>
                                            <td>₹<?php echo number_format($detail['paid'], 0); ?></td>
                                            <td><strong class="text-danger">₹<?php echo number_format($detail['pending'], 0); ?></strong></td>
                                            <td><strong><?php echo $detail['action']; ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No technicians completed jobs on <?php echo date('d M Y', strtotime($yesterday)); ?>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="card shadow">
                    <div class="card-body">
                        <h5>Next Steps:</h5>
                        <a href="admin-unlock-technician.php" class="btn btn-success">
                            <i class="fas fa-unlock"></i> View Locked Technicians
                        </a>
                        <a href="admin-pending-commissions.php" class="btn btn-warning">
                            <i class="fas fa-money-bill-wave"></i> View Pending Commissions
                        </a>
                        <a href="admin-record-commission-payment.php" class="btn btn-primary">
                            <i class="fas fa-cash-register"></i> Record Payment
                        </a>
                        <a href="run-commission-lock-manually.php" class="btn btn-info">
                            <i class="fas fa-sync"></i> Run Again
                        </a>
                    </div>
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
