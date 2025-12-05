<?php
/**
 * AUTOMATED TECHNICIAN ACCOUNT LOCKING FOR UNPAID COMMISSION
 * Run this script daily at 7:00 AM via cron job/task scheduler
 * 
 * Setup Task Scheduler (Windows):
 * 1. Open Task Scheduler
 * 2. Create Basic Task: "Lock Unpaid Technicians"
 * 3. Trigger: Daily at 7:00 AM
 * 4. Action: Start a program
 * 5. Program: C:\xampp\php\php.exe (adjust your PHP path)
 * 6. Arguments: Full path to this file
 */

// Error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cron-errors.log');

// Log start time
file_put_contents(__DIR__ . '/cron-last-run.log', date('Y-m-d H:i:s') . " - Cron job started\n", FILE_APPEND);

include('vendor/inc/config.php');

// Check if database connection is successful
if(!$mysqli || $mysqli->connect_error) {
    $error = "Database connection failed: " . ($mysqli ? $mysqli->connect_error : 'mysqli not initialized');
    file_put_contents(__DIR__ . '/cron-errors.log', date('Y-m-d H:i:s') . " - " . $error . "\n", FILE_APPEND);
    die($error);
}

$yesterday = date('Y-m-d', strtotime('-1 day')); // Check yesterday's commission at 7:00 AM
$commission_rate = 0.20;

// Ensure required columns exist
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS lock_reason TEXT");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL");

// Ensure commission_payments table exists
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

// Ensure system_logs table exists
$create_logs = "CREATE TABLE IF NOT EXISTS tms_system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    log_type VARCHAR(50) NOT NULL,
    log_message TEXT NOT NULL,
    log_data TEXT,
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (log_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$mysqli->query($create_logs);

file_put_contents(__DIR__ . '/cron-last-run.log', date('Y-m-d H:i:s') . " - Tables checked/created\n", FILE_APPEND);

// Get all technicians who worked yesterday
$query = "SELECT 
            t.t_id,
            t.t_name,
            t.t_phone,
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
$locked_techs = [];

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
        
        // If payment not done, lock the account
        if($pending > 0) {
            // ONLY show commission amount to technician, NOT revenue
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
            
            echo "✓ Locked: {$tech->t_name} - Commission Due: ₹{$pending}\n";
        }
    }
}

// Log the action
$log_message = "";
if($locked_count > 0) {
    $log_query = "INSERT INTO tms_system_logs (log_type, log_message, log_data, log_date) 
                  VALUES ('account_lock', ?, ?, NOW())";
    $stmt_log = $mysqli->prepare($log_query);
    $log_message = "{$locked_count} technician accounts locked for unpaid commission";
    $log_data = json_encode(['locked_count' => $locked_count, 'date' => $yesterday, 'technicians' => $locked_techs]);
    $stmt_log->bind_param('ss', $log_message, $log_data);
    $stmt_log->execute();
    $stmt_log->close();
    
    echo "\n✓ Total locked: {$locked_count} technicians\n";
    $log_message = "SUCCESS: Locked {$locked_count} technicians for date {$yesterday}";
} else {
    echo "✓ All technicians paid their commission. No accounts locked.\n";
    $log_message = "SUCCESS: All technicians paid commission for date {$yesterday}";
}

// Write to log file
file_put_contents(__DIR__ . '/cron-last-run.log', date('Y-m-d H:i:s') . " - " . $log_message . "\n", FILE_APPEND);

$mysqli->close();

echo "\n✓ Cron job completed successfully at " . date('Y-m-d H:i:s') . "\n";
?>
