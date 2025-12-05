<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$commission_rate = 0.20;

// Create tables
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_commission_payments (
    cp_id INT AUTO_INCREMENT PRIMARY KEY,
    cp_technician_id INT NOT NULL,
    cp_amount DECIMAL(10,2) NOT NULL,
    cp_date DATE NOT NULL,
    cp_payment_method VARCHAR(50) DEFAULT 'Cash',
    cp_notes TEXT,
    cp_recorded_by INT,
    cp_recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tech_date (cp_technician_id, cp_date)
) ENGINE=InnoDB");

$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS lock_reason TEXT");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL");

// Handle actions
if(isset($_POST['record_payment'])) {
    $tech_id = $_POST['tech_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'];
    
    $stmt = $mysqli->prepare("INSERT INTO tms_commission_payments (cp_technician_id, cp_amount, cp_date, cp_payment_method, cp_notes, cp_recorded_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('idsssi', $tech_id, $amount, $payment_date, $payment_method, $notes, $_SESSION['a_id']);
    $stmt->execute();
    
    $mysqli->query("UPDATE tms_technician SET account_locked = 0, lock_reason = NULL, locked_at = NULL WHERE t_id = $tech_id");
    $_SESSION['success'] = "Payment recorded & account unlocked!";
    header("location: admin-commission-complete.php");
    exit();
}

if(isset($_POST['manual_lock'])) {
    $tech_id = $_POST['lock_tech_id'];
    $amount = $_POST['lock_amount'];
    $lock_date = $_POST['lock_date'];
    
    $lock_reason = "Unpaid charges for " . date('d M Y', strtotime($lock_date)) . ". Amount: ₹" . $amount;
    $mysqli->query("UPDATE tms_technician SET account_locked = 1, lock_reason = '$lock_reason', locked_at = NOW() WHERE t_id = $tech_id");
    $_SESSION['success'] = "Account locked!";
    header("location: admin-commission-complete.php");
    exit();
}

if(isset($_GET['unlock'])) {
    $mysqli->query("UPDATE tms_technician SET account_locked = 0, lock_reason = NULL, locked_at = NULL WHERE t_id = " . intval($_GET['unlock']));
    $_SESSION['success'] = "Account unlocked!";
    header("location: admin-commission-complete.php");
    exit();
}

// Filters
$filter = $_GET['filter'] ?? 'today';
$status_filter = $_GET['status'] ?? 'all'; // all, pending, paid, active
$search = $_GET['search'] ?? '';
$month = $_GET['month'] ?? date('Y-m');
$year = $_GET['year'] ?? date('Y');

switch($filter) {
    case 'today': $start_date = $end_date = date('Y-m-d'); break;
    case 'yesterday': $start_date = $end_date = date('Y-m-d', strtotime('-1 day')); break;
    case 'thismonth': $start_date = date('Y-m-01'); $end_date = date('Y-m-d'); break;
    case 'monthly': $start_date = $month . '-01'; $end_date = date('Y-m-t', strtotime($start_date)); break;
    case 'yearly': $start_date = $year . '-01-01'; $end_date = $year . '-12-31'; break;
    default: $start_date = $end_date = date('Y-m-d');
}

// Get data
$query = "SELECT t.t_id, t.t_name, t.t_ez_id, t.t_phone, t.t_category, t.t_status, t.account_locked,
          COUNT(DISTINCT sb.sb_id) as jobs,
          COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, 0)), 0) as revenue
          FROM tms_technician t
          LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
            AND DATE(sb.sb_completed_at) BETWEEN '$start_date' AND '$end_date'
            AND sb.sb_status = 'Completed'
          WHERE 1=1";
if($search) $query .= " AND (t.t_name LIKE '%$search%' OR t.t_ez_id LIKE '%$search%' OR t.t_phone LIKE '%$search%')";
$query .= " GROUP BY t.t_id ORDER BY revenue DESC";

$result = $mysqli->query($query);
$technicians = [];
$totals = ['revenue' => 0, 'commission' => 0, 'paid' => 0, 'pending' => 0, 'jobs' => 0];
$counts = ['total' => 0, 'pending' => 0, 'paid' => 0, 'active' => 0];

while($row = $result->fetch_object()) {
    $row->commission = round($row->revenue * $commission_rate, 0);
    
    $paid_query = $mysqli->query("SELECT COALESCE(SUM(cp_amount), 0) as paid FROM tms_commission_payments WHERE cp_technician_id = {$row->t_id} AND DATE(cp_date) BETWEEN '$start_date' AND '$end_date'");
    $row->paid = $paid_query->fetch_object()->paid;
    $row->pending = $row->commission - $row->paid;
    $row->payment_status = ($row->commission > 0 && $row->pending <= 0) ? 'Paid' : (($row->pending > 0) ? 'Not Paid' : 'No Jobs');
    
    $totals['revenue'] += $row->revenue;
    $totals['commission'] += $row->commission;
    $totals['paid'] += $row->paid;
    $totals['pending'] += $row->pending;
    $totals['jobs'] += $row->jobs;
    
    $counts['total']++;
    if($row->pending > 0) $counts['pending']++;
    if($row->payment_status == 'Paid') $counts['paid']++;
    if($row->t_status == 'Available') $counts['active']++;
    
    // Apply status filter
    if($status_filter == 'pending' && $row->pending <= 0) continue;
    if($status_filter == 'paid' && $row->payment_status != 'Paid') continue;
    if($status_filter == 'active' && $row->t_status != 'Available') continue;
    
    $technicians[] = $row;
}

$all_techs = $mysqli->query("SELECT t_id, t_name, t_ez_id FROM tms_technician ORDER BY t_name");
?>
<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php');?>
<body id="page-top">
<?php include("vendor/inc/nav.php");?>
<div id="wrapper">
<?php include("vendor/inc/sidebar.php");?>
<div id="content-wrapper">
<div class="container-fluid">

<h3 class="mb-3"><i class="fas fa-money-bill-wave"></i> Commission Management</h3>

<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible">
    <button class="close" data-dismiss="alert">&times;</button>
    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="card shadow mb-3">
    <div class="card-body">
        <form method="GET" class="form-inline">
            <!-- Date Filters -->
            <div class="btn-group mr-3">
                <a href="?filter=today&status=<?php echo $status_filter; ?>" class="btn btn-sm btn-<?php echo $filter=='today'?'primary':'outline-primary'; ?>">
                    <i class="fas fa-calendar-day"></i> Today
                </a>
                <a href="?filter=yesterday&status=<?php echo $status_filter; ?>" class="btn btn-sm btn-<?php echo $filter=='yesterday'?'primary':'outline-primary'; ?>">
                    <i class="fas fa-calendar-minus"></i> Yesterday
                </a>
                <a href="?filter=thismonth&status=<?php echo $status_filter; ?>" class="btn btn-sm btn-<?php echo $filter=='thismonth'?'primary':'outline-primary'; ?>">
                    <i class="fas fa-calendar-alt"></i> This Month
                </a>
            </div>
            
            <!-- Status Filters -->
            <div class="btn-group mr-3">
                <a href="?filter=<?php echo $filter; ?>&status=all" class="btn btn-sm btn-<?php echo $status_filter=='all'?'info':'outline-info'; ?>">
                    <i class="fas fa-list"></i> All
                </a>
                <a href="?filter=<?php echo $filter; ?>&status=pending" class="btn btn-sm btn-<?php echo $status_filter=='pending'?'warning':'outline-warning'; ?>">
                    <i class="fas fa-exclamation-triangle"></i> Pending
                </a>
                <a href="?filter=<?php echo $filter; ?>&status=paid" class="btn btn-sm btn-<?php echo $status_filter=='paid'?'success':'outline-success'; ?>">
                    <i class="fas fa-check-circle"></i> Paid
                </a>
                <a href="?filter=<?php echo $filter; ?>&status=active" class="btn btn-sm btn-<?php echo $status_filter=='active'?'primary':'outline-primary'; ?>">
                    <i class="fas fa-user-check"></i> Active
                </a>
            </div>
            
            <input type="month" name="month" class="form-control form-control-sm mr-2" value="<?php echo $month; ?>" onchange="window.location.href='?filter=monthly&month='+this.value+'&status=<?php echo $status_filter; ?>'">
            <select name="year" class="form-control form-control-sm mr-2" onchange="window.location.href='?filter=yearly&year='+this.value+'&status=<?php echo $status_filter; ?>'">
                <?php for($y=date('Y'); $y>=2020; $y--): ?>
                <option value="<?php echo $y; ?>" <?php echo $year==$y?'selected':''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Search..." value="<?php echo $search; ?>">
            <input type="hidden" name="filter" value="<?php echo $filter; ?>">
            <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            <a href="admin-commission-complete.php" class="btn btn-sm btn-secondary ml-2"><i class="fas fa-redo"></i></a>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-3">
    <div class="col-md-2">
        <div class="card border-left-info shadow py-2">
            <div class="card-body p-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Technicians</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo $counts['total']; ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-primary shadow py-2">
            <div class="card-body p-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                <div class="h5 mb-0 font-weight-bold">₹<?php echo number_format($totals['revenue'], 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-warning shadow py-2">
            <div class="card-body p-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">EZ Commission (20%)</div>
                <div class="h5 mb-0 font-weight-bold">₹<?php echo number_format($totals['commission'], 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-success shadow py-2">
            <div class="card-body p-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                <div class="h5 mb-0 font-weight-bold">₹<?php echo number_format($totals['paid'], 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-danger shadow py-2">
            <div class="card-body p-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pending</div>
                <div class="h5 mb-0 font-weight-bold">₹<?php echo number_format($totals['pending'], 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-primary shadow py-2">
            <div class="card-body p-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jobs</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo $totals['jobs']; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card shadow">
    <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users"></i> Technician Revenue & Payment Status
            <span class="badge badge-primary"><?php echo ucfirst($filter); ?></span>
            <?php if($filter=='monthly') echo '<span class="badge badge-info">'.date('F Y', strtotime($month)).'</span>'; ?>
            <?php if($filter=='yearly') echo '<span class="badge badge-info">'.$year.'</span>'; ?>
            <?php if($status_filter!='all') echo '<span class="badge badge-warning">'.ucfirst($status_filter).'</span>'; ?>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="width: 150px;">Technician</th>
                        <th style="width: 80px;">EZ ID</th>
                        <th style="width: 100px;">Phone</th>
                        <th style="width: 60px;">Jobs</th>
                        <th style="width: 100px;">Revenue</th>
                        <th style="width: 100px;">Commission</th>
                        <th style="width: 100px;">Paid</th>
                        <th style="width: 100px;">Pending</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i=1; 
                    foreach($technicians as $t): 
                        $class = $t->account_locked ? 'table-danger' : ($t->pending > 0 ? 'table-warning' : '');
                    ?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo $i++; ?></td>
                        <td><strong><?php echo $t->t_name; ?></strong><br><small class="text-muted"><?php echo $t->t_category; ?></small></td>
                        <td><?php echo $t->t_ez_id; ?></td>
                        <td><?php echo $t->t_phone; ?></td>
                        <td class="text-center"><span class="badge badge-info"><?php echo $t->jobs; ?></span></td>
                        <td class="text-success"><strong>₹<?php echo number_format($t->revenue, 0); ?></strong></td>
                        <td class="text-warning"><strong>₹<?php echo number_format($t->commission, 0); ?></strong></td>
                        <td class="text-success">₹<?php echo number_format($t->paid, 0); ?></td>
                        <td class="text-danger"><strong>₹<?php echo number_format($t->pending, 0); ?></strong></td>
                        <td class="text-center">
                            <?php if($t->account_locked): ?>
                                <span class="badge badge-danger"><i class="fas fa-lock"></i> Locked</span>
                            <?php elseif($t->pending > 0): ?>
                                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Not Paid</span>
                            <?php elseif($t->payment_status == 'Paid'): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Paid</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($t->account_locked): ?>
                                <a href="?unlock=<?php echo $t->t_id; ?>" class="btn btn-sm btn-success" title="Unlock" onclick="return confirm('Unlock?')">
                                    <i class="fas fa-unlock"></i>
                                </a>
                            <?php elseif($t->pending > 0): ?>
                                <button class="btn btn-sm btn-primary" onclick="pay(<?php echo $t->t_id; ?>,'<?php echo $t->t_name; ?>',<?php echo $t->pending; ?>)" title="Record Payment">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="lock(<?php echo $t->t_id; ?>,'<?php echo $t->t_name; ?>',<?php echo $t->pending; ?>)" title="Lock">
                                    <i class="fas fa-lock"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-right">TOTAL:</td>
                        <td class="text-center"><?php echo $totals['jobs']; ?></td>
                        <td class="text-success">₹<?php echo number_format($totals['revenue'], 0); ?></td>
                        <td class="text-warning">₹<?php echo number_format($totals['commission'], 0); ?></td>
                        <td class="text-success">₹<?php echo number_format($totals['paid'], 0); ?></td>
                        <td class="text-danger">₹<?php echo number_format($totals['pending'], 0); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

</div>
<?php include('vendor/inc/footer.php');?>
</div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="payModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle"></i> Record Payment</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Technician</label>
                        <select name="tech_id" id="pay_tech" class="form-control" required>
                            <option value="">Select</option>
                            <?php while($t = $all_techs->fetch_object()): ?>
                            <option value="<?php echo $t->t_id; ?>"><?php echo $t->t_name; ?> (<?php echo $t->t_ez_id; ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (₹)</label>
                        <input type="number" name="amount" id="pay_amt" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Method</label>
                        <select name="payment_method" class="form-control">
                            <option>Cash</option>
                            <option>UPI</option>
                            <option>Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="record_payment" class="btn btn-success"><i class="fas fa-save"></i> Record & Unlock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lock Modal -->
<div class="modal fade" id="lockModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-lock"></i> Lock Account</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Technician cannot receive bookings!</div>
                    <div class="form-group">
                        <label>Technician</label>
                        <select name="lock_tech_id" id="lock_tech" class="form-control" required>
                            <option value="">Select</option>
                            <?php $all_techs->data_seek(0); while($t = $all_techs->fetch_object()): ?>
                            <option value="<?php echo $t->t_id; ?>"><?php echo $t->t_name; ?> (<?php echo $t->t_ez_id; ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount Due (₹)</label>
                        <input type="number" name="lock_amount" id="lock_amt" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="lock_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="manual_lock" class="btn btn-danger"><i class="fas fa-lock"></i> Lock Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
function pay(id, name, amt) {
    $('#pay_tech').val(id);
    $('#pay_amt').val(amt);
    $('#payModal').modal('show');
}
function lock(id, name, amt) {
    $('#lock_tech').val(id);
    $('#lock_amt').val(amt);
    $('#lockModal').modal('show');
}
</script>
</body>
</html>
