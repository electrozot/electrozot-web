<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Create commission_payments table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS tms_commission_payments (
    cp_id INT AUTO_INCREMENT PRIMARY KEY,
    cp_technician_id INT NOT NULL,
    cp_amount DECIMAL(10,2) NOT NULL,
    cp_date DATE NOT NULL,
    cp_payment_method VARCHAR(50) DEFAULT 'Cash',
    cp_notes TEXT,
    cp_recorded_by INT,
    cp_recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cp_technician_id) REFERENCES tms_technician(t_id) ON DELETE CASCADE
)";
$mysqli->query($create_table);

// Get today's date
$today = date('Y-m-d');

// Get current month start and end
$month_start = date('Y-m-01');
$month_end = date('Y-m-d');

// Get all technicians with today's revenue only
$query = "SELECT 
            t.t_id,
            t.t_name, 
            t.t_ez_id, 
            t.t_phone, 
            t.t_category,
            COUNT(CASE WHEN DATE(sb.sb_completed_at) = ? THEN 1 END) as today_jobs,
            COALESCE(SUM(CASE WHEN DATE(sb.sb_completed_at) = ? THEN COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0) END), 0) as today_revenue
          FROM tms_technician t
          LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
            AND sb.sb_status = 'Completed'
          GROUP BY t.t_id
          ORDER BY today_revenue DESC, t.t_name ASC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ss', $today, $today);
$stmt->execute();
$result = $stmt->get_result();

// Commission rate (20%)
$commission_rate = 0.20;

// Calculate total revenue and commission
$total_today_revenue = 0;
$total_today_jobs = 0;
$total_today_commission = 0;
$techs_data = [];

while($row = $result->fetch_object()) {
    // Calculate commission for today only
    $row->today_commission = round($row->today_revenue * $commission_rate, 2);
    
    // Get paid amount for today
    $paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_today
                   FROM tms_commission_payments 
                   WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
    $stmt_paid = $mysqli->prepare($paid_query);
    $stmt_paid->bind_param('is', $row->t_id, $today);
    $stmt_paid->execute();
    $paid_result = $stmt_paid->get_result();
    $paid_data = $paid_result->fetch_object();
    
    $row->today_paid = $paid_data->paid_today;
    $row->today_pending = $row->today_commission - $row->today_paid;
    $row->payment_status = ($row->today_commission > 0 && $row->today_pending <= 0) ? 'Paid' : (($row->today_pending > 0) ? 'Not Paid' : 'No Jobs');
    
    $stmt_paid->close();
    
    $techs_data[] = $row;
    $total_today_revenue += $row->today_revenue;
    $total_today_jobs += $row->today_jobs;
    $total_today_commission += $row->today_commission;
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
                    <li class="breadcrumb-item active">Technicians Daily Revenue</li>
                </ol>
                
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible py-2 mb-2">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <small><i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></small>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible py-2 mb-2">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <small><i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></small>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h5 class="font-weight-bold text-success mb-0">
                            <i class="fas fa-users"></i> Technicians Daily Revenue
                        </h5>
                        <small class="text-muted">Date: <strong><?php echo date('d M Y'); ?></strong></small>
                    </div>
                </div>

                <!-- Quick Filter Buttons -->
                <div class="mb-2">
                    <button class="btn btn-xs btn-outline-success" onclick="filterTable('all')">
                        <i class="fas fa-list"></i> All
                    </button>
                    <button class="btn btn-xs btn-outline-warning" onclick="filterTable('pending')">
                        <i class="fas fa-exclamation-triangle"></i> Pending
                    </button>
                    <button class="btn btn-xs btn-outline-success" onclick="filterTable('paid')">
                        <i class="fas fa-check-circle"></i> Paid
                    </button>
                    <button class="btn btn-xs btn-outline-info" onclick="filterTable('active')">
                        <i class="fas fa-briefcase"></i> Active
                    </button>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-1">
                            <div class="card-body p-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">TOTAL TECHNICIANS</div>
                                        <div class="h6 mb-0 font-weight-bold"><?php echo count($techs_data); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-1">
                            <div class="card-body p-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">TODAY'S TOTAL REVENUE</div>
                                        <div class="h6 mb-0 font-weight-bold">₹<?php echo number_format($total_today_revenue, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-rupee-sign fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-danger shadow h-100 py-1">
                            <div class="card-body p-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">EZ COMMISSION (20%)</div>
                                        <div class="h6 mb-0 font-weight-bold">₹<?php echo number_format($total_today_commission, 0); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hand-holding-usd fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-1">
                            <div class="card-body p-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">TODAY'S TOTAL JOBS</div>
                                        <div class="h6 mb-0 font-weight-bold"><?php echo $total_today_jobs; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-briefcase fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Technicians Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list"></i> Today's Technician Revenue & Payment Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th style="width: 150px;">Technician</th>
                                        <th class="text-center" style="width: 50px;">View</th>
                                        <th style="width: 60px;">EZ ID</th>
                                        <th style="width: 85px;">Phone</th>
                                        <th class="text-center" style="width: 60px;">Jobs</th>
                                        <th class="text-right" style="width: 100px;">Revenue</th>
                                        <th class="text-right" style="width: 100px;">Commission</th>
                                        <th class="text-center" style="width: 110px;">Status</th>
                                        <th class="text-center" style="width: 80px;">Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if(count($techs_data) > 0):
                                        $rank = 1;
                                        foreach($techs_data as $tech): 
                                    ?>
                                        <tr class="tech-row" 
                                            data-pending="<?php echo $tech->payment_status == 'Not Paid' ? 'yes' : 'no'; ?>" 
                                            data-paid="<?php echo $tech->payment_status == 'Paid' ? 'yes' : 'no'; ?>"
                                            data-active="<?php echo $tech->today_jobs > 0 ? 'yes' : 'no'; ?>"
                                            style="<?php echo $tech->payment_status == 'Not Paid' ? 'background-color: #fff3cd;' : ''; ?>">
                                            <td><?php echo $rank; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($tech->t_name); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($tech->t_category ?? 'N/A'); ?></small>
                                            </td>
                                            <td class="text-center">
                                                <a href="admin-technician-monthly-details.php?tech_id=<?php echo $tech->t_id; ?>" 
                                                   class="btn btn-xs btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                            <td><small><?php echo htmlspecialchars($tech->t_ez_id ?? 'N/A'); ?></small></td>
                                            <td><small><?php echo htmlspecialchars($tech->t_phone); ?></small></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?php echo $tech->today_jobs > 0 ? 'success' : 'secondary'; ?> badge-pill">
                                                    <?php echo $tech->today_jobs; ?>
                                                </span>
                                            </td>
                                            <td class="text-right">₹<?php echo number_format($tech->today_revenue, 0); ?></td>
                                            <td class="text-right text-danger">₹<?php echo number_format($tech->today_commission, 0); ?></td>
                                            <td class="text-center">
                                                <?php if($tech->payment_status == 'Paid'): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> PAID
                                                    </span>
                                                <?php elseif($tech->payment_status == 'Not Paid'): ?>
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times"></i> NOT PAID
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($tech->today_pending > 0): ?>
                                                    <button type="button" 
                                                            class="btn btn-xs btn-danger" 
                                                            onclick="recordPayment(<?php echo $tech->t_id; ?>, '<?php echo htmlspecialchars($tech->t_name); ?>', <?php echo $tech->today_commission; ?>)"
                                                            title="Payment Pending - Click to Record">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php elseif($tech->today_commission > 0): ?>
                                                    <button type="button" class="btn btn-xs btn-success" disabled title="Payment Received">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        $rank++;
                                        endforeach;
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No technicians found</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="5" class="text-right">TOTAL:</td>
                                        <td class="text-center"><?php echo $total_today_jobs; ?></td>
                                        <td class="text-right text-success">₹<?php echo number_format($total_today_revenue, 0); ?></td>
                                        <td class="text-right text-danger">₹<?php echo number_format($total_today_commission, 0); ?></td>
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
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title mb-0">
                        <i class="fas fa-money-bill-wave"></i> Confirm Payment
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="admin-quick-record-payment.php">
                    <div class="modal-body py-2">
                        <input type="hidden" name="tech_id" id="modal_tech_id">
                        <input type="hidden" name="redirect" value="admin-stats-technicians.php">
                        <input type="hidden" name="amount" id="modal_amount">
                        
                        <div class="form-group mb-2">
                            <label class="mb-1"><small><strong>Technician:</strong></small></label>
                            <p class="mb-0" id="modal_tech_name"></p>
                        </div>
                        
                        <div class="form-group mb-2">
                            <label class="mb-1"><small><strong>Commission (20%):</strong></small></label>
                            <p class="mb-0 text-danger"><strong>₹<span id="modal_amount_display"></span></strong></p>
                        </div>
                        
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Payment Date *</small></label>
                            <input type="date" name="payment_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Payment Method</small></label>
                            <select name="payment_method" class="form-control form-control-sm">
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Notes</small></label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="record_payment" class="btn btn-sm btn-success">
                            <i class="fas fa-check"></i> Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
    
    <script>
    function filterTable(filter) {
        const rows = document.querySelectorAll('.tech-row');
        
        rows.forEach(row => {
            if(filter === 'all') {
                row.style.display = '';
            } else if(filter === 'pending') {
                row.style.display = row.dataset.pending === 'yes' ? '' : 'none';
            } else if(filter === 'paid') {
                row.style.display = row.dataset.paid === 'yes' ? '' : 'none';
            } else if(filter === 'active') {
                row.style.display = row.dataset.active === 'yes' ? '' : 'none';
            }
        });
    }
    
    function recordPayment(techId, techName, pendingAmount) {
        $('#modal_tech_id').val(techId);
        $('#modal_tech_name').text(techName);
        // Round to whole number
        var roundedAmount = Math.round(pendingAmount);
        $('#modal_amount').val(roundedAmount);
        $('#modal_amount_display').text(roundedAmount);
        $('#paymentModal').modal('show');
    }
    </script>
</body>
</html>
