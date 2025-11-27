<?php
/**
 * Upgrade Custom Booking System
 * Adds enhanced fields and features for custom/other service bookings
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$success_messages = [];
$error_messages = [];

// Step 1: Add detailed service name column for admin to specify
$add_detailed_name = "ALTER TABLE tms_service_booking 
                      ADD COLUMN IF NOT EXISTS sb_detailed_service_name VARCHAR(500) DEFAULT NULL 
                      COMMENT 'Detailed service name added by admin for custom bookings'";
if($mysqli->query($add_detailed_name)) {
    $success_messages[] = "✅ Added 'sb_detailed_service_name' column for detailed service descriptions";
} else {
    $error_messages[] = "❌ Error adding detailed service name column: " . $mysqli->error;
}

// Step 2: Add extracted keywords column for smart matching
$add_keywords = "ALTER TABLE tms_service_booking 
                 ADD COLUMN IF NOT EXISTS sb_extracted_keywords TEXT DEFAULT NULL 
                 COMMENT 'Keywords extracted from description/image for technician matching'";
if($mysqli->query($add_keywords)) {
    $success_messages[] = "✅ Added 'sb_extracted_keywords' column for smart technician matching";
} else {
    $error_messages[] = "❌ Error adding keywords column: " . $mysqli->error;
}

// Step 3: Add suggested category column
$add_category = "ALTER TABLE tms_service_booking 
                 ADD COLUMN IF NOT EXISTS sb_suggested_category VARCHAR(100) DEFAULT NULL 
                 COMMENT 'Auto-suggested category based on description analysis'";
if($mysqli->query($add_category)) {
    $success_messages[] = "✅ Added 'sb_suggested_category' column for category suggestions";
} else {
    $error_messages[] = "❌ Error adding category column: " . $mysqli->error;
}

// Step 4: Add image analysis result column
$add_image_analysis = "ALTER TABLE tms_service_booking 
                       ADD COLUMN IF NOT EXISTS sb_image_analysis TEXT DEFAULT NULL 
                       COMMENT 'Analysis result from booking image (if uploaded)'";
if($mysqli->query($add_image_analysis)) {
    $success_messages[] = "✅ Added 'sb_image_analysis' column for image-based matching";
} else {
    $error_messages[] = "❌ Error adding image analysis column: " . $mysqli->error;
}

// Step 5: Ensure custom bookings are treated as non-fixed-price
$update_custom_prices = "UPDATE tms_service_booking 
                         SET sb_total_price = 0 
                         WHERE (sb_custom_service IS NOT NULL OR sb_service_id IS NULL) 
                         AND sb_status NOT IN ('Completed', 'Cancelled')
                         AND sb_total_price > 0";
if($mysqli->query($update_custom_prices)) {
    $affected = $mysqli->affected_rows;
    if($affected > 0) {
        $success_messages[] = "✅ Updated {$affected} custom booking(s) to non-fixed-price (₹0)";
    } else {
        $success_messages[] = "✅ All custom bookings already set to non-fixed-price";
    }
} else {
    $error_messages[] = "❌ Error updating custom booking prices: " . $mysqli->error;
}

// Step 6: Create keyword-service mapping table for smart matching
$create_keyword_table = "CREATE TABLE IF NOT EXISTS tms_service_keywords (
    sk_id INT AUTO_INCREMENT PRIMARY KEY,
    sk_service_id INT NOT NULL,
    sk_keyword VARCHAR(100) NOT NULL,
    sk_weight INT DEFAULT 1 COMMENT 'Relevance weight (1-10)',
    sk_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_keyword (sk_keyword),
    INDEX idx_service (sk_service_id),
    FOREIGN KEY (sk_service_id) REFERENCES tms_service(s_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if($mysqli->query($create_keyword_table)) {
    $success_messages[] = "✅ Created 'tms_service_keywords' table for smart matching";
    
    // Populate with common keywords
    $keywords_data = [
        // Electrical keywords
        ['keyword' => 'wiring', 'services' => ['Wiring', 'Electrical'], 'weight' => 10],
        ['keyword' => 'switch', 'services' => ['Switch', 'Electrical'], 'weight' => 9],
        ['keyword' => 'socket', 'services' => ['Socket', 'Electrical'], 'weight' => 9],
        ['keyword' => 'fan', 'services' => ['Fan', 'Ceiling Fan'], 'weight' => 10],
        ['keyword' => 'light', 'services' => ['Light', 'Lighting'], 'weight' => 9],
        ['keyword' => 'bulb', 'services' => ['Light', 'Bulb'], 'weight' => 8],
        ['keyword' => 'tube', 'services' => ['Tube Light', 'Light'], 'weight' => 8],
        ['keyword' => 'mcb', 'services' => ['MCB', 'Electrical'], 'weight' => 10],
        ['keyword' => 'breaker', 'services' => ['Circuit Breaker', 'MCB'], 'weight' => 10],
        ['keyword' => 'inverter', 'services' => ['Inverter', 'UPS'], 'weight' => 10],
        ['keyword' => 'ups', 'services' => ['UPS', 'Inverter'], 'weight' => 10],
        ['keyword' => 'stabilizer', 'services' => ['Stabilizer', 'Voltage'], 'weight' => 10],
        
        // Repair keywords
        ['keyword' => 'repair', 'services' => ['Repair'], 'weight' => 8],
        ['keyword' => 'fix', 'services' => ['Repair'], 'weight' => 7],
        ['keyword' => 'broken', 'services' => ['Repair'], 'weight' => 7],
        ['keyword' => 'not working', 'services' => ['Repair'], 'weight' => 8],
        
        // Installation keywords
        ['keyword' => 'install', 'services' => ['Installation'], 'weight' => 9],
        ['keyword' => 'setup', 'services' => ['Installation', 'Setup'], 'weight' => 8],
        ['keyword' => 'new', 'services' => ['Installation'], 'weight' => 6],
        
        // Appliance keywords
        ['keyword' => 'ac', 'services' => ['AC', 'Air Conditioner'], 'weight' => 10],
        ['keyword' => 'air conditioner', 'services' => ['AC'], 'weight' => 10],
        ['keyword' => 'fridge', 'services' => ['Refrigerator', 'Fridge'], 'weight' => 10],
        ['keyword' => 'refrigerator', 'services' => ['Refrigerator'], 'weight' => 10],
        ['keyword' => 'washing machine', 'services' => ['Washing Machine'], 'weight' => 10],
        ['keyword' => 'geyser', 'services' => ['Geyser', 'Water Heater'], 'weight' => 10],
        ['keyword' => 'water heater', 'services' => ['Geyser', 'Water Heater'], 'weight' => 10],
        
        // Plumbing keywords
        ['keyword' => 'tap', 'services' => ['Plumbing', 'Tap'], 'weight' => 9],
        ['keyword' => 'pipe', 'services' => ['Plumbing', 'Pipe'], 'weight' => 9],
        ['keyword' => 'leak', 'services' => ['Plumbing', 'Leak'], 'weight' => 10],
        ['keyword' => 'water', 'services' => ['Plumbing'], 'weight' => 7],
        ['keyword' => 'drain', 'services' => ['Plumbing', 'Drainage'], 'weight' => 9],
    ];
    
    $insert_count = 0;
    foreach($keywords_data as $kw) {
        foreach($kw['services'] as $service_name) {
            // Find service ID
            $find_service = "SELECT s_id FROM tms_service WHERE s_name LIKE ? LIMIT 1";
            $stmt = $mysqli->prepare($find_service);
            $search_term = "%{$service_name}%";
            $stmt->bind_param('s', $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result && $result->num_rows > 0) {
                $service = $result->fetch_object();
                
                // Check if keyword already exists
                $check_existing = "SELECT sk_id FROM tms_service_keywords WHERE sk_service_id = ? AND sk_keyword = ?";
                $check_stmt = $mysqli->prepare($check_existing);
                $check_stmt->bind_param('is', $service->s_id, $kw['keyword']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if($check_result->num_rows == 0) {
                    // Insert keyword
                    $insert_kw = "INSERT INTO tms_service_keywords (sk_service_id, sk_keyword, sk_weight) VALUES (?, ?, ?)";
                    $insert_stmt = $mysqli->prepare($insert_kw);
                    $insert_stmt->bind_param('isi', $service->s_id, $kw['keyword'], $kw['weight']);
                    if($insert_stmt->execute()) {
                        $insert_count++;
                    }
                }
            }
        }
    }
    
    if($insert_count > 0) {
        $success_messages[] = "✅ Added {$insert_count} keyword mappings for smart technician matching";
    }
} else {
    $error_messages[] = "❌ Error creating keywords table: " . $mysqli->error;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Upgrade Custom Booking System</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 40px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 900px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #2d3748; border-bottom: 4px solid #667eea; padding-bottom: 15px; margin-bottom: 30px; }
        .success-box { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-left: 5px solid #10b981; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .error-box { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-left: 5px solid #ef4444; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .feature-box { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #667eea; }
        .btn-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 25px; font-weight: 600; text-decoration: none; display: inline-block; margin: 10px 5px; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); color: white; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-rocket"></i> Custom Booking System Upgrade</h1>
    
    <?php if(count($success_messages) > 0): ?>
        <div class="success-box">
            <h4 style="color: #065f46; margin-bottom: 15px;"><i class="fas fa-check-circle"></i> Upgrade Successful!</h4>
            <?php foreach($success_messages as $msg): ?>
                <div style="margin: 8px 0;"><?php echo $msg; ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if(count($error_messages) > 0): ?>
        <div class="error-box">
            <h4 style="color: #991b1b; margin-bottom: 15px;"><i class="fas fa-exclamation-circle"></i> Errors Occurred</h4>
            <?php foreach($error_messages as $msg): ?>
                <div style="margin: 8px 0;"><?php echo $msg; ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="feature-box">
        <h3 style="color: #667eea; margin-bottom: 15px;"><i class="fas fa-star"></i> New Features Enabled</h3>
        
        <h5><i class="fas fa-edit"></i> 1. Detailed Service Name Field</h5>
        <p>Admin can now add detailed service descriptions for custom bookings. This helps technicians understand exactly what work is needed.</p>
        
        <h5><i class="fas fa-brain"></i> 2. Smart Technician Matching</h5>
        <p>System analyzes booking descriptions and images to extract keywords, then matches with technicians who have relevant skills.</p>
        
        <h5><i class="fas fa-tags"></i> 3. Keyword-Based Filtering</h5>
        <p>Over 30+ keywords mapped to services (wiring, fan, AC, plumbing, etc.) for intelligent technician suggestions.</p>
        
        <h5><i class="fas fa-rupee-sign"></i> 4. Non-Fixed-Price Treatment</h5>
        <p>All custom bookings are automatically set to ₹0, allowing technicians to set the final price during service completion.</p>
        
        <h5><i class="fas fa-image"></i> 5. Image Analysis Support</h5>
        <p>Ready for future integration with image analysis to extract service details from uploaded photos.</p>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="admin-dashboard.php" class="btn-custom"><i class="fas fa-home"></i> Go to Dashboard</a>
        <a href="admin-all-bookings.php" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="fas fa-list"></i> View Bookings</a>
    </div>
    
    <div style="margin-top: 40px; padding: 20px; background: #fffbeb; border-radius: 10px; border-left: 4px solid #f59e0b;">
        <h5 style="color: #92400e;"><i class="fas fa-info-circle"></i> How It Works</h5>
        <ol style="color: #78350f;">
            <li><strong>Customer books custom service</strong> - Provides description/image</li>
            <li><strong>System analyzes keywords</strong> - Extracts relevant terms (e.g., "fan", "wiring", "repair")</li>
            <li><strong>Admin adds detailed name</strong> - Specifies exact service needed</li>
            <li><strong>Smart filtering shows matching technicians</strong> - Only those with relevant skills</li>
            <li><strong>Technician completes work</strong> - Sets final price based on actual work done</li>
        </ol>
    </div>
</div>
</body>
</html>
