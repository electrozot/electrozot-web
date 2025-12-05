<?php
/**
 * MANUAL TEST - Lock Technician for Unpaid Commission
 * Use this to test the locking system immediately without waiting for cron
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$commission_rate = 0.20;

// Ensure columns exist
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS lock_reason TEXT");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL");

// Handle manual lock action
if(isset($_POST['lock_technician'])) {
    $tech_id = intval($_POST['tech_id']);
    $test_date = $_POST['test_date'];
    
    // Get technician's completed jobs for that date
    $job_query = "SELECT 
                    t.t_id,
                    t.t_name,
                    COUNT(sb.sb_id) as jobs,
                    COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as revenue
                  FROM tms_technician t
                  INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
                  WHERE t.t_id = ?
                    AND DATE(sb.sb_completed_at) = ? 
                    AND sb.sb_status = 'Completed'
                  GROUP BY t.t_id";
    
    $stmt = $mysqli->prepare($job_query);
    $stmt->bind_param('is', $tech_id, $test_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $tech = $result->fetch_object();
        $commission = round($tech->revenue * $commission_rate, 0);
        
        // Check if payment was made
        $paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_amount
                       FROM tms_commission_payments 
                       WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
        $stmt_paid = $mysqli->prepare($paid_query);
        $stmt_paid->bind_param('is', $tech_id, $test_date);
        $stmt_paid->execute();
        $paid_result = $stmt_paid->get_result();
        $paid_data = $paid_result->fetch_object();
        $stmt_paid->close();
        
        $pending = $commission - $paid_data->paid_amount;
        
        if($pending > 0) {
            // Lock the account - ONLY show commission amount, NOT revenue
            $lock_reason = "Unpaid Electrozot charges for " . date('d M Y', strtotime($test_date)) . ". Amount Due: ₹" . number_format($commission, 0) . ". Please complete payment and contact admin to unlock your account.";
            
            $lock_query = "UPDATE tms_technician 
                          SET account_locked = 1, 
                              lock_reason = ?, 
                              locked_at = NOW() 
                          WHERE t_id = ?";
            $stmt_lock = $mysqli->prepare($lock_query);
            $stmt_lock->bind_param('si', $lock_reason, $tech_id);
            
            if($stmt_lock->execute()) {
                $success = "✅ Successfully locked {$tech->t_name}! Commission Due: ₹{$commission}";
            } else {
                $error = "❌ Failed to lock technician: " . $mysqli->error;
            }
            $stmt_lock->close();
        } else {
            $error = "⚠️ Cannot lock {$tech->t_name} - Commission already paid (₹{$paid_data->paid_amount})";
        }
    } else {
        $error = "⚠️ No completed jobs found for this technician on {$test_date}";
    }
}

// Handle unlock action
if(isset($_POST['unlock_technician'])) {
    $tech_id = intval($_POST['tech_id']);
    
    $unlock_query = "UPDATE tms_technician 
                    SET account_locked = 0, 
                        lock_reason = NULL, 
                        locked_at = NULL 
                    WHERE t_id = ?";
    $stmt_unlock = $mysqli->prepare($unlock_query);
    $stmt_unlock->bind_param('i', $tech_id);
    
    if($stmt_unlock->execute()) {
        $success = "✅ Technician unlocked successfully!";
    } else {
        $error = "❌ Failed to unlock: " . $mysqli->error;
    }
    $stmt_unlock->close();
}

// Get technicians with completed jobs in last 7 days
$recent_query = "SELECT 
                    t.t_id,
                    t.t_name,
                    t.t_phone,
                    t.account_locked,
                    t.lock_reason,
                    DATE(sb.sb_completed_at) as job_date,
                    COUNT(sb.sb_id) as jobs,
                    COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as revenue
                  FROM tms_technician t
                  INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
                  WHERE DATE(sb.sb_completed_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    AND sb.sb_status = 'Completed'
                  GROUP BY t.t_id, DATE(sb.sb_completed_at)
                  ORDER BY job_date DESC, t.t_name ASC";
$recent_result = $mysqli->query($recent_query);
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
                <h2 class="mb-4"><i class="fas fa-vial"></i> Manual Test - Lock Technician</h2>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Instructions -->
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> How to Test</h5>
                    <ol class="mb-0">
                        <li>Find a technician who completed jobs recently (shown below)</li>
                        <li>Click "🔒 Lock Now" to immediately lock their account</li>
                        <li>Try to assign them a booking - it should FAIL</li>
                        <li>Click "🔓 Unlock" to restore their account</li>
                        <li>Try assigning again - it should WORK</li>
                    </ol>
                </div>

                <!-- Currently Locked Technicians -->
                <?php
                $locked_query = "SELECT t_id, t_name, t_phone, lock_reason, locked_at 
                                FROM tms_technician 
                                WHERE account_locked = 1 
                                ORDER BY locked_at DESC";
                $locked_result = $mysqli->query($locked_query);
                
                if($locked_result->num_rows > 0):
                ?>
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-lock"></i> Currently Locked Technicians (<?php echo $locked_result->num_rows; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Technician</th>
                                        <th>Reason</th>
                                        <th>Locked At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($locked = $locked_result->fetch_object()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($locked->t_name); ?></strong><br>
                                            <small><?php echo $locked->t_phone; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($locked->lock_reason); ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($locked->locked_at)); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="tech_id" value="<?php echo $locked->t_id; ?>">
                                                <button type="submit" name="unlock_technician" class="btn btn-success btn-sm" onclick="return confirm('Unlock this technician?');">
                                                    <i class="fas fa-unlock"></i> Unlock
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Technicians with Recent Jobs -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Technicians with Completed Jobs (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <?php if($recent_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Technician</th>
                                            <th>Date</th>
                                            <th>Jobs</th>
                                            <th>Revenue</th>
                                            <th>Commission (20%)</th>
                                            <th>Paid</th>
                                            <th>Pending</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($tech = $recent_result->fetch_object()): 
                                            $commission = round($tech->revenue * $commission_rate, 0);
                                            
                                            // Check payment
                                            $paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_amount
                                                           FROM tms_commission_payments 
                                                           WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
                                            $stmt_paid = $mysqli->prepare($paid_query);
                                            $stmt_paid->bind_param('is', $tech->t_id, $tech->job_date);
                                            $stmt_paid->execute();
                                            $paid_result = $stmt_paid->get_result();
                                            $paid_data = $paid_result->fetch_object();
                                            $stmt_paid->close();
                                            
                                            $pending = $commission - $paid_data->paid_amount;
                                            $can_lock = $pending > 0 && $tech->account_locked == 0;
                                        ?>
                                        <tr class="<?php echo $tech->account_locked == 1 ? 'table-danger' : ''; ?>">
                                            <td>
                                                <strong><?php echo htmlspecialchars($tech->t_name); ?></strong><br>
                                                <small><?php echo $tech->t_phone; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($tech->job_date)); ?></td>
                                            <td><?php echo $tech->jobs; ?></td>
                                            <td>₹<?php echo number_format($tech->revenue, 0); ?></td>
                                            <td><strong>₹<?php echo number_format($commission, 0); ?></strong></td>
                                            <td>₹<?php echo number_format($paid_data->paid_amount, 0); ?></td>
                                            <td>
                                                <?php if($pending > 0): ?>
                                                    <strong class="text-danger">₹<?php echo number_format($pending, 0); ?></strong>
                                                <?php else: ?>
                                                    <span class="text-success">₹0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($tech->account_locked == 1): ?>
                                                    <span class="badge badge-danger">🔒 LOCKED</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">🔓 UNLOCKED</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($can_lock): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="tech_id" value="<?php echo $tech->t_id; ?>">
                                                        <input type="hidden" name="test_date" value="<?php echo $tech->job_date; ?>">
                                                        <button type="submit" name="lock_technician" class="btn btn-danger btn-sm" onclick="return confirm('Lock <?php echo htmlspecialchars($tech->t_name); ?> for ₹<?php echo $pending; ?> unpaid commission?');">
                                                            <i class="fas fa-lock"></i> Lock Now
                                                        </button>
                                                    </form>
                                                <?php elseif($tech->account_locked == 1): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="tech_id" value="<?php echo $tech->t_id; ?>">
                                                        <button type="submit" name="unlock_technician" class="btn btn-success btn-sm">
                                                            <i class="fas fa-unlock"></i> Unlock
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Already Paid</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No completed jobs found in the last 7 days</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Test Instructions -->
                <div class="card mt-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Testing Steps</h5>
                    </div>
                    <div class="card-body">
                        <h6>Step 1: Lock a Technician</h6>
                        <p>Click "🔒 Lock Now" button for any technician with pending commission above</p>
                        
                        <h6>Step 2: Verify Lock Works</h6>
                        <ul>
                            <li>Go to <a href="admin-assign-technician.php" target="_blank">Assign Technician</a> page</li>
                            <li>Try to assign the locked technician to a booking</li>
                            <li>You should see an error: "This technician's account is LOCKED"</li>
                        </ul>
                        
                        <h6>Step 3: Check Available List</h6>
                        <ul>
                            <li>The locked technician should NOT appear in available technicians dropdown</li>
                            <li>They are automatically filtered out</li>
                        </ul>
                        
                        <h6>Step 4: Unlock and Test Again</h6>
                        <ul>
                            <li>Click "🔓 Unlock" button</li>
                            <li>Try assigning again - it should work now</li>
                        </ul>
                        
                        <h6>Step 5: Test Auto-Unlock on Payment</h6>
                        <ul>
                            <li>Lock a technician again</li>
                            <li>Go to <a href="admin-quick-record-payment.php" target="_blank">Record Payment</a></li>
                            <li>Record their commission payment</li>
                            <li>Account should automatically unlock</li>
                        </ul>
                    </div>
                </div>

            </div>
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
