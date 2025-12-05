<?php
/**
 * AUTOMATED DAILY COMMISSION REPORT
 * Run this script at end of day (e.g., 11:00 PM) via cron job
 * 
 * Setup Cron Job (Linux/Mac):
 * 0 23 * * * php /path/to/admin/cron-daily-commission-report.php
 * 
 * Setup Task Scheduler (Windows):
 * Run daily at 11:00 PM
 */

include('vendor/inc/config.php');

$today = date('Y-m-d');
$commission_rate = 0.20;

// Get technicians with pending commission
$query = "SELECT 
            t.t_id,
            t.t_name, 
            t.t_ez_id, 
            t.t_phone, 
            t.t_email,
            COUNT(sb.sb_id) as today_jobs,
            COALESCE(SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)), 0) as today_revenue
          FROM tms_technician t
          INNER JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
          WHERE DATE(sb.sb_completed_at) = ? 
            AND sb.sb_status = 'Completed'
          GROUP BY t.t_id
          HAVING today_revenue > 0";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();

$pending_techs = [];
$total_pending = 0;
$report_lines = [];

$report_lines[] = "=================================================";
$report_lines[] = "ELECTROZOT DAILY COMMISSION REPORT";
$report_lines[] = "Date: " . date('d M Y');
$report_lines[] = "Generated: " . date('d M Y h:i A');
$report_lines[] = "=================================================\n";

while($row = $result->fetch_object()) {
    $commission = round($row->today_revenue * $commission_rate, 2);
    
    // Get paid amount
    $paid_query = "SELECT COALESCE(SUM(cp_amount), 0) as paid_today
                   FROM tms_commission_payments 
                   WHERE cp_technician_id = ? AND DATE(cp_date) = ?";
    $stmt_paid = $mysqli->prepare($paid_query);
    $stmt_paid->bind_param('is', $row->t_id, $today);
    $stmt_paid->execute();
    $paid_result = $stmt_paid->get_result();
    $paid_data = $paid_result->fetch_object();
    $stmt_paid->close();
    
    $pending = $commission - $paid_data->paid_today;
    
    if($pending > 0) {
        $pending_techs[] = [
            'id' => $row->t_id,
            'name' => $row->t_name,
            'ez_id' => $row->t_ez_id,
            'phone' => $row->t_phone,
            'email' => $row->t_email,
            'jobs' => $row->today_jobs,
            'revenue' => $row->today_revenue,
            'commission' => $commission,
            'paid' => $paid_data->paid_today,
            'pending' => $pending
        ];
        $total_pending += $pending;
    }
}

// Generate report
if(count($pending_techs) > 0) {
    $report_lines[] = "⚠️  ALERT: " . count($pending_techs) . " TECHNICIANS HAVE PENDING PAYMENTS\n";
    $report_lines[] = "Total Pending Commission: ₹" . number_format($total_pending, 2) . "\n";
    $report_lines[] = "-------------------------------------------------";
    $report_lines[] = sprintf("%-5s %-25s %-12s %-15s %-12s", "#", "Name", "EZ ID", "Phone", "Pending");
    $report_lines[] = "-------------------------------------------------";
    
    $rank = 1;
    foreach($pending_techs as $tech) {
        $report_lines[] = sprintf(
            "%-5s %-25s %-12s %-15s ₹%-12s",
            $rank++,
            substr($tech['name'], 0, 24),
            $tech['ez_id'] ?? 'N/A',
            $tech['phone'],
            number_format($tech['pending'], 0)
        );
    }
    
    $report_lines[] = "-------------------------------------------------";
    $report_lines[] = "TOTAL PENDING: ₹" . number_format($total_pending, 2);
    $report_lines[] = "=================================================\n";
    
    // Save report to file
    $report_dir = 'reports/commission/';
    if(!is_dir($report_dir)) {
        mkdir($report_dir, 0777, true);
    }
    
    $report_file = $report_dir . 'pending_commission_' . $today . '.txt';
    file_put_contents($report_file, implode("\n", $report_lines));
    
    // Log to database
    $log_query = "INSERT INTO tms_system_logs (log_type, log_message, log_data, log_date) 
                  VALUES ('commission_report', ?, ?, NOW())";
    $stmt_log = $mysqli->prepare($log_query);
    $log_message = count($pending_techs) . " technicians have pending commission payments totaling ₹" . number_format($total_pending, 2);
    $log_data = json_encode(['pending_count' => count($pending_techs), 'total_pending' => $total_pending, 'date' => $today]);
    $stmt_log->bind_param('ss', $log_message, $log_data);
    $stmt_log->execute();
    $stmt_log->close();
    
    // Create admin notification
    $notif_query = "INSERT INTO tms_admin_notifications (notification_type, notification_title, notification_message, notification_data, created_at) 
                    VALUES ('commission_pending', ?, ?, ?, NOW())";
    $stmt_notif = $mysqli->prepare($notif_query);
    $notif_title = "Pending Commission Payments - " . date('d M Y');
    $notif_message = count($pending_techs) . " technicians have pending payments totaling ₹" . number_format($total_pending, 2);
    $notif_data = json_encode(['pending_count' => count($pending_techs), 'total_pending' => $total_pending]);
    $stmt_notif->bind_param('sss', $notif_title, $notif_message, $notif_data);
    $stmt_notif->execute();
    $stmt_notif->close();
    
    echo "✓ Report generated: $report_file\n";
    echo "✓ " . count($pending_techs) . " technicians with pending payments\n";
    echo "✓ Total pending: ₹" . number_format($total_pending, 2) . "\n";
    
    // Optional: Send WhatsApp/SMS reminders to technicians
    // Uncomment and configure your SMS/WhatsApp API
    /*
    foreach($pending_techs as $tech) {
        $message = "Dear {$tech['name']}, your pending commission payment to Electrozot is ₹{$tech['pending']}. Please pay today. Thank you!";
        // send_whatsapp($tech['phone'], $message);
        // send_sms($tech['phone'], $message);
    }
    */
    
} else {
    $report_lines[] = "✓ All technicians have paid their commission for today!";
    $report_lines[] = "=================================================";
    
    echo "✓ All clear! No pending payments.\n";
}

$mysqli->close();
?>
