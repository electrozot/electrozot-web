<?php
/**
 * AUTOMATED TECHNICIAN ACCOUNT LOCKING FOR UNPAID COMMISSION
 * Run this script daily at 7:15 AM via cron job
 * 
 * Setup Cron Job (Linux/Mac):
 * 15 7 * * * php /path/to/admin/cron-lock-unpaid-technicians.php
 * 
 * Setup Task Scheduler (Windows):
 * Run daily at 7:15 AM
 */

include('vendor/inc/config.php');

$yesterday = date('Y-m-d', strtotime('-1 day')); // Check yesterday's commission at 7:15 AM
$commission_rate = 0.20;

// Add account_locked column if not exists
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS lock_reason TEXT");
$mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL");

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
            $lock_reason = "Unpaid charges for " . date('d M Y', strtotime($yesterday)) . ". Amount: ₹" . number_format($commission, 0) . ". Please complete payment and contact EZ Admin.";
            
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
            
            echo "✓ Locked: {$tech->t_name} - Pending: ₹{$pending}\n";
        }
    }
}

// Log the action
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
} else {
    echo "✓ All technicians paid their commission. No accounts locked.\n";
}

$mysqli->close();
?>
