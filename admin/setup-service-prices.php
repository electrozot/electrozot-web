<?php
/**
 * Service Prices Feature Setup Script
 * Run this once to set up the service prices management feature
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$setup_log = [];

// Step 1: Add admin price column
$setup_log[] = "Step 1: Adding s_admin_price column to tms_service table...";
$query1 = "ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_admin_price DECIMAL(10,2) DEFAULT NULL COMMENT 'Admin-set fixed price in Indian Rupees'";
if($mysqli->query($query1)) {
    $setup_log[] = "✓ Column added successfully";
} else {
    $setup_log[] = "✗ Error: " . $mysqli->error;
}

// Step 2: Check existing services
$setup_log[] = "\nStep 2: Checking existing services...";
$count_query = "SELECT COUNT(*) as total FROM tms_service";
$result = $mysqli->query($count_query);
$count = $result->fetch_object()->total;
$setup_log[] = "✓ Found $count services in the system";

// Step 3: Check services with prices
$priced_query = "SELECT COUNT(*) as priced FROM tms_service WHERE s_admin_price IS NOT NULL";
$result2 = $mysqli->query($priced_query);
$priced = $result2->fetch_object()->priced;
$setup_log[] = "✓ $priced services already have admin prices set";

// Step 4: Verify table structure
$setup_log[] = "\nStep 3: Verifying table structure...";
$verify_query = "SHOW COLUMNS FROM tms_service LIKE 's_admin_price'";
$verify_result = $mysqli->query($verify_query);
if($verify_result->num_rows > 0) {
    $setup_log[] = "✓ s_admin_price column exists and is ready to use";
} else {
    $setup_log[] = "✗ Column verification failed";
}

// Step 5: Sample services by category
$setup_log[] = "\nStep 4: Services by category:";
$category_query = "SELECT s_category, COUNT(*) as count FROM tms_service GROUP BY s_category ORDER BY s_category";
$category_result = $mysqli->query($category_query);
while($cat = $category_result->fetch_object()) {
    $setup_log[] = "  - {$cat->s_category}: {$cat->count} services";
}

$setup_log[] = "\n" . str_repeat("=", 60);
$setup_log[] = "SETUP COMPLETE!";
$setup_log[] = str_repeat("=", 60);
$setup_log[] = "\nNext Steps:";
$setup_log[] = "1. Go to Admin Dashboard → Services → Service Prices";
$setup_log[] = "2. Set prices for services you want to control";
$setup_log[] = "3. Leave prices empty for services where technicians should set the price";
$setup_log[] = "4. Technicians can view prices in their dashboard";
$setup_log[] = "\nAll prices should be in Indian Rupees (₹)";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Prices Setup</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .setup-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .setup-header h1 {
            color: #667eea;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .setup-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .setup-log {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.8;
            max-height: 500px;
            overflow-y: auto;
            margin-bottom: 30px;
        }
        .setup-log div {
            margin: 5px 0;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .info {
            color: #17a2b8;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1><i class="fas fa-rupee-sign"></i> Service Prices Setup</h1>
            <p>Setting up the service prices management feature</p>
        </div>

        <div class="setup-log">
            <?php foreach($setup_log as $log): ?>
                <?php
                $class = '';
                if(strpos($log, '✓') !== false) $class = 'success';
                elseif(strpos($log, '✗') !== false) $class = 'error';
                elseif(strpos($log, 'Step') !== false) $class = 'info';
                ?>
                <div class="<?php echo $class; ?>"><?php echo htmlspecialchars($log); ?></div>
            <?php endforeach; ?>
        </div>

        <div class="text-center">
            <a href="admin-service-prices.php" class="btn btn-custom">
                <i class="fas fa-arrow-right"></i> Go to Service Prices
            </a>
            <a href="admin-dashboard.php" class="btn btn-secondary" style="border-radius: 50px; padding: 15px 40px; font-weight: 700;">
                <i class="fas fa-home"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
