<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<h2>Setting up Technician Services System...</h2>";

// Create technician_services junction table
$query1 = "CREATE TABLE IF NOT EXISTS tms_technician_services (
    ts_id INT AUTO_INCREMENT PRIMARY KEY,
    t_id INT NOT NULL,
    sc_id INT NOT NULL,
    service_type ENUM('Installation', 'Repair', 'Servicing', 'Maintenance', 'Other') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (t_id) REFERENCES tms_technician(t_id) ON DELETE CASCADE,
    FOREIGN KEY (sc_id) REFERENCES tms_service_categories(sc_id) ON DELETE CASCADE,
    UNIQUE KEY unique_tech_service (t_id, sc_id, service_type)
)";

if($mysqli->query($query1)) {
    echo "<p style='color: green;'>✓ Technician services table created successfully</p>";
} else {
    echo "<p style='color: red;'>Error: " . $mysqli->error . "</p>";
}

echo "<br><br><h3>Setup Complete!</h3>";
echo "<p><a href='admin-add-technician.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Add Technician</a></p>";
echo "<p><a href='admin-manage-technician.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Manage Technicians</a></p>";
?>
