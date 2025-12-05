<?php
/**
 * Setup Rejection Tracking System
 * Creates necessary database table and verifies the feature is working
 */

session_start();
include('vendor/inc/config.php');

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { background: white; border-collapse: collapse; width: 100%; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
    td { padding: 10px; border-bottom: 1px solid #ddd; }
    h2 { color: #333; }
</style>";

echo "<h2>🔧 Rejection Tracking System Setup</h2>";

// Create rejection tracking table
$create_table = "CREATE TABLE IF NOT EXISTS tms_technician_rejections (
    tr_id INT AUTO_INCREMENT PRIMARY KEY,
    tr_technician_id INT NOT NULL,
    tr_booking_id INT NOT NULL,
    tr_reason TEXT,
    tr_rejected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tr_admin_notified TINYINT(1) DEFAULT 0,
    tr_admin_action VARCHAR(50) NULL,
    tr_admin_action_at TIMESTAMP NULL,
    tr_admin_notes TEXT NULL,
    INDEX(tr_technician_id),
    INDEX(tr_booking_id),
    INDEX(tr_rejected_at),
    INDEX(tr_admin_notified),
    FOREIGN KEY (tr_technician_id) REFERENCES tms_technician(t_id) ON DELETE CASCADE,
    FOREIGN KEY (tr_booking_id) REFERENCES tms_service_booking(sb_id) ON DELETE CASCADE
)";

if($mysqli->query($create_table)) {
    echo "<div class='success'>✅ Rejection tracking table created/verified successfully</div>";
} else {
    echo "<div class='error'>❌ Error creating table: " . $mysqli->error . "</div>";
}

// Add columns to technician table if they don't exist
$alter_queries = [
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_blocked_until TIMESTAMP NULL",
    "ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_block_reason TEXT NULL"
];

foreach($alter_queries as $query) {
    $mysqli->query($query);
}

echo "<div class='success'>✅ Technician table columns verified</div>";

// Check if rejection tracking is working
$check_query = "SELECT 
    t.t_id,
    t.t_name,
    COUNT(tr.tr_id) as rejection_count,
    MAX(tr.tr_rejected_at) as last_rejection
FROM tms_technician t
LEFT JOIN tms_technician_rejections tr ON t.t_id = tr.tr_technician_id
    AND tr.tr_rejected_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY t.t_id, t.t_name
HAVING rejection_count > 0
ORDER BY rejection_count DESC
LIMIT 10";

$result = $mysqli->query($check_query);

if($result && $result->num_rows > 0) {
    echo "<h3>📊 Recent Rejections (Last 7 Days)</h3>";
    echo "<table>";
    echo "<tr><th>Technician ID</th><th>Name</th><th>Rejections</th><th>Last Rejection</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        $alert_class = $row['rejection_count'] >= 3 ? 'style="background: #fff3cd;"' : '';
        echo "<tr $alert_class>";
        echo "<td>" . $row['t_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['t_name']) . "</td>";
        echo "<td><strong>" . $row['rejection_count'] . "</strong>" . ($row['rejection_count'] >= 3 ? ' ⚠️' : '') . "</td>";
        echo "<td>" . date('M d, Y h:i A', strtotime($row['last_rejection'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if($result->num_rows > 0) {
        echo "<div class='info'><strong>ℹ️ Note:</strong> Technicians with 3+ rejections will trigger an alert popup on admin dashboard.</div>";
    }
} else {
    echo "<div class='info'>ℹ️ No rejections recorded in the last 7 days</div>";
}

// Check if modal is included in dashboard
$dashboard_file = file_get_contents('admin-dashboard.php');
if(strpos($dashboard_file, 'widget-rejection-alert-modal.php') !== false) {
    echo "<div class='success'>✅ Rejection alert modal is included in admin dashboard</div>";
} else {
    echo "<div class='error'>❌ Rejection alert modal is NOT included in admin dashboard</div>";
    echo "<div class='info'>Add this line before closing &lt;/body&gt; tag in admin-dashboard.php:<br>";
    echo "<code>&lt;?php include('widget-rejection-alert-modal.php'); ?&gt;</code></div>";
}

// Feature summary
echo "<hr>";
echo "<h3>📋 Feature Summary</h3>";
echo "<div class='info'>";
echo "<strong>How it works:</strong><br>";
echo "1. When a technician rejects or marks a booking as 'Not Done', it's tracked in the database<br>";
echo "2. Admin dashboard checks every 5 minutes for technicians with 3+ rejections in last 7 days<br>";
echo "3. A popup modal appears showing the flagged technicians<br>";
echo "4. Admin can take action: Lock Account, Block Bookings, or No Action<br>";
echo "5. All actions are logged with admin notes<br><br>";

echo "<strong>Threshold:</strong> 3 rejections in 7 days<br>";
echo "<strong>Check Interval:</strong> Every 5 minutes (300 seconds)<br>";
echo "<strong>Initial Check:</strong> 2 seconds after page load<br><br>";

echo "<strong>Admin Actions Available:</strong><br>";
echo "• <strong>Lock Account (2 Days):</strong> Technician cannot login or receive bookings<br>";
echo "• <strong>Block Bookings (2 Days):</strong> Technician can login but won't receive new bookings<br>";
echo "• <strong>No Action:</strong> Mark as reviewed without taking action<br>";
echo "</div>";

// Test the API
echo "<hr>";
echo "<h3>🧪 Test API Endpoints</h3>";
echo "<div class='info'>";
echo "<strong>Check Rejection Threshold:</strong><br>";
echo "<a href='api-check-rejection-threshold.php' target='_blank' class='btn btn-primary'>Test API</a><br><br>";

echo "<strong>Files Involved:</strong><br>";
echo "• <code>admin/widget-rejection-alert-modal.php</code> - Modal UI<br>";
echo "• <code>admin/api-check-rejection-threshold.php</code> - Check for alerts<br>";
echo "• <code>admin/api-take-rejection-action.php</code> - Process admin actions<br>";
echo "• <code>tech/api-reject-booking.php</code> - Records rejections<br>";
echo "</div>";

$mysqli->close();
?>
