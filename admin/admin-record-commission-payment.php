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

if(isset($_POST['record_payment'])) {
    $tech_id = $_POST['tech_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'];
    $admin_id = $_SESSION['a_id'];
    
    $query = "INSERT INTO tms_commission_payments (cp_technician_id, cp_amount, cp_date, cp_payment_method, cp_notes, cp_recorded_by) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('idsssi', $tech_id, $amount, $payment_date, $payment_method, $notes, $admin_id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Commission payment recorded successfully!";
    } else {
        $_SESSION['error'] = "Failed to record payment.";
    }
    $stmt->close();
    header("location: admin-record-commission-payment.php");
    exit();
}

// Get all technicians
$techs_query = "SELECT t_id, t_name, t_ez_id FROM tms_technician ORDER BY t_name";
$techs_result = $mysqli->query($techs_query);

// Get recent payments
$payments_query = "SELECT cp.*, t.t_name, t.t_ez_id 
                   FROM tms_commission_payments cp
                   JOIN tms_technician t ON cp.cp_technician_id = t.t_id
                   ORDER BY cp.cp_date DESC, cp.cp_recorded_at DESC
                   LIMIT 50";
$payments_result = $mysqli->query($payments_query);
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
                    <li class="breadcrumb-item active">Record Commission Payment</li>
                </ol>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-money-bill-wave"></i> Record Commission Payment
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label>Technician *</label>
                                        <select name="tech_id" class="form-control" required>
                                            <option value="">Select Technician</option>
                                            <?php while($tech = $techs_result->fetch_object()): ?>
                                                <option value="<?php echo $tech->t_id; ?>">
                                                    <?php echo htmlspecialchars($tech->t_name); ?> (<?php echo htmlspecialchars($tech->t_ez_id); ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount (₹) *</label>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Payment Date *</label>
                                        <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select name="payment_method" class="form-control">
                                            <option value="Cash">Cash</option>
                                            <option value="UPI">UPI</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button type="submit" name="record_payment" class="btn btn-primary btn-block">
                                        <i class="fas fa-save"></i> Record Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-history"></i> Recent Commission Payments
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                    <table class="table table-bordered table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Technician</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($payments_result && $payments_result->num_rows > 0): ?>
                                                <?php while($payment = $payments_result->fetch_object()): ?>
                                                    <tr>
                                                        <td><?php echo date('d M Y', strtotime($payment->cp_date)); ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($payment->t_name); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($payment->t_ez_id); ?></small>
                                                        </td>
                                                        <td><strong class="text-success">₹<?php echo number_format($payment->cp_amount, 0); ?></strong></td>
                                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($payment->cp_payment_method); ?></span></td>
                                                        <td><small><?php echo htmlspecialchars($payment->cp_notes ?? '-'); ?></small></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No payments recorded yet</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
