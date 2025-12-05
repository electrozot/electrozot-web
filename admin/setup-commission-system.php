<?php
/**
 * SETUP COMMISSION LOCK SYSTEM
 * Run this file ONCE to setup the complete commission system
 * Access: admin/setup-commission-system.php
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$setup_log = [];

// Step 1: Add columns to tms_technician table
$setup_log[] = "Step 1: Adding columns to tms_technician table...";
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS lock_reason TEXT");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL");
$setup_log[] = "✓ Technician table columns added";

// Step 2: Create commission_payments table
$setup_log[] = "Step 2: Creating tms_commission_payments table...";
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
    INDEX idx_date (cp_date),
    FOREIGN KEY (cp_technician_id) REFERENCES tms_technician(t_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if($mysqli->query($create_table)) {
    $setup_log[] = "✓ Commission payments table created";
} else {
    $setup_log[] = "✗ Error creating table: " . $mysqli->error;
}

// Step 3: Create system_logs table if not exists
$setup_log[] = "Step 3: Creating tms_system_logs table...";
$create_logs = "CREATE TABLE IF NOT EXISTS tms_system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    log_type VARCHAR(50) NOT NULL,
    log_message TEXT NOT NULL,
    log_data TEXT,
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (log_type),
    INDEX idx_date (log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if($mysqli->query($create_logs)) {
    $setup_log[] = "✓ System logs table created";
} else {
    $setup_log[] = "✗ Error creating logs table: " . $mysqli->error;
}

// Step 4: Test the system
$setup_log[] = "Step 4: Testing system...";
$test_query = "SELECT COUNT(*) as count FROM tms_technician WHERE account_locked IS NOT NULL";
$result = $mysqli->query($test_query);
if($result) {
    $setup_log[] = "✓ System is ready to use";
} else {
    $setup_log[] = "✗ System test failed";
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
                <h2 class="mb-4">Commission System Setup</h2>
                
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle"></i> Setup Complete</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5><i class="fas fa-info-circle"></i> Setup Log:</h5>
                            <?php foreach($setup_log as $log): ?>
                                <p class="mb-1"><?php echo $log; ?></p>
                            <?php endforeach; ?>
                        </div>
                        
                        <h5 class="mt-4">Next Steps:</h5>
                        <ol>
                            <li><strong>Setup Cron Job (Windows Task Scheduler):</strong>
                                <ul>
                                    <li>Open Task Scheduler</li>
                                    <li>Create Basic Task: "Lock Unpaid Technicians"</li>
                                    <li>Trigger: Daily at 7:00 AM</li>
                                    <li>Action: Start a program</li>
                                    <li>Program: <code>C:\xampp\php\php.exe</code> (adjust path)</li>
                                    <li>Arguments: <code><?php echo realpath('cron-lock-unpaid-technicians.php'); ?></code></li>
                                </ul>
                            </li>
                            <li><strong>Test the system:</strong>
                                <ul>
                                    <li><a href="test-commission-lock-system.php" class="btn btn-sm btn-info">Run Test Page</a></li>
                                    <li><a href="admin-pending-commissions.php" class="btn btn-sm btn-warning">View Pending Commissions</a></li>
                                </ul>
                            </li>
                        </ol>
                        
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-lightbulb"></i> How It Works:</h6>
                            <ol>
                                <li>Every day at 7:00 AM, system checks yesterday's completed jobs</li>
                                <li>Calculates 20% commission for each technician</li>
                                <li>If payment not recorded, locks the account</li>
                                <li>Locked technicians cannot receive new bookings</li>
                                <li>When technician pays, admin records payment and account unlocks automatically</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('vendor/inc/footer.php');?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
