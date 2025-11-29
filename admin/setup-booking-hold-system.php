<?php
/**
 * Booking Hold System Setup
 * Creates necessary database tables and columns
 */

include('vendor/inc/config.php');

echo "<h2>Setting up Booking Hold System...</h2>";

try {
    // Create booking hold requests table
    $create_hold_table = "CREATE TABLE IF NOT EXISTS tms_booking_hold_requests (
        bhr_id INT AUTO_INCREMENT PRIMARY KEY,
        bhr_booking_id INT NOT NULL,
        bhr_technician_id INT NOT NULL,
        bhr_reason TEXT NOT NULL,
        bhr_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        bhr_requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        bhr_responded_at TIMESTAMP NULL,
        bhr_customer_response TEXT NULL,
        INDEX(bhr_booking_id),
        INDEX(bhr_technician_id),
        INDEX(bhr_status)
    )";
    
    if($mysqli->query($create_hold_table)) {
        echo "✅ Booking hold requests table created<br>";
    }
    
    // Add hold-related columns to service_booking table
    $alter_queries = [
        "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_on_hold TINYINT(1) DEFAULT 0",
        "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_reason TEXT NULL",
        "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_start_date TIMESTAMP NULL",
        "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_end_date TIMESTAMP NULL",
        "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_high_priority TINYINT(1) DEFAULT 0",
        "ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_priority_reason VARCHAR(255) NULL"
    ];
    
    foreach($alter_queries as $query) {
        try {
            $mysqli->query($query);
            echo "✅ Column added/verified<br>";
        } catch(Exception $e) {
            // Column might already exist
        }
    }
    
    // Create customer notifications table if not exists
    $create_customer_notif = "CREATE TABLE IF NOT EXISTS tms_customer_notifications (
        cn_id INT AUTO_INCREMENT PRIMARY KEY,
        cn_user_id INT NOT NULL,
        cn_booking_id INT NOT NULL,
        cn_type VARCHAR(50) NOT NULL,
        cn_title VARCHAR(255) NOT NULL,
        cn_message TEXT NOT NULL,
        cn_is_read TINYINT(1) DEFAULT 0,
        cn_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        cn_action_required TINYINT(1) DEFAULT 0,
        cn_action_url VARCHAR(255) NULL,
        INDEX(cn_user_id),
        INDEX(cn_booking_id),
        INDEX(cn_is_read)
    )";
    
    if($mysqli->query($create_customer_notif)) {
        echo "✅ Customer notifications table created<br>";
    }
    
    // Create technician notifications table if not exists
    $create_tech_notif = "CREATE TABLE IF NOT EXISTS tms_technician_notifications (
        tn_id INT AUTO_INCREMENT PRIMARY KEY,
        tn_technician_id INT NOT NULL,
        tn_booking_id INT NOT NULL,
        tn_type VARCHAR(50) NOT NULL,
        tn_title VARCHAR(255) NOT NULL,
        tn_message TEXT NOT NULL,
        tn_is_read TINYINT(1) DEFAULT 0,
        tn_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(tn_technician_id),
        INDEX(tn_booking_id),
        INDEX(tn_is_read)
    )";
    
    if($mysqli->query($create_tech_notif)) {
        echo "✅ Technician notifications table created<br>";
    }
    
    echo "<br><h3>✅ Booking Hold System Setup Complete!</h3>";
    echo "<p><a href='admin-dashboard.php'>Go to Dashboard</a></p>";
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
