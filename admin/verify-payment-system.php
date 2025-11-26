<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$results = [];

// 1. Check payment_collection table
$check1 = $mysqli->query("SHOW TABLES LIKE 'tms_payment_collection'");
$results['payment_collection_table'] = $check1->num_rows > 0 ? '✅ EXISTS' : '❌ MISSING';

// 2. Check payment_settings table
$check2 = $mysqli->query("SHOW TABLES LIKE 'tms_payment_settings'");
$results['payment_settings_table'] = $check2->num_rows > 0 ? '✅ EXISTS' : '❌ MISSING';

// 3. Check t_payment_qr column
$check3 = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_payment_qr'");
$results['t_payment_qr_column'] = $check3->num_rows > 0 ? '✅ EXISTS' : '❌ MISSING';

// 4. Check if payment settings has data
$check4 = $mysqli->query("SELECT COUNT(*) as count FROM tms_payment_settings");
$count4 = $check4 ? $check4->fetch_object()->count : 0;
$results['payment_settings_data'] = $count4 > 0 ? '✅ HAS DATA' : '⚠️ EMPTY (Will auto-create)';

// 5. Check files exist
$results['collect_payment_file'] = file_exists('collect-payment.php') ? '❌ WRONG LOCATION' : (file_exists('../tech/collect-payment.php') ? '✅ EXISTS' : '❌ MISSING');
$results['payment_settings_file'] = file_exists('admin-payment-settings.php') ? '✅ EXISTS' : '❌ MISSING';

// 6. Check uploads directory
$results['payment_qr_directory'] = is_dir('../uploads/payment') ? '✅ EXISTS' : '⚠️ WILL BE CREATED';
$results['technician_qr_directory'] = is_dir('../uploads/technician_qr') ? '✅ EXISTS' : '⚠️ WILL BE CREATED';

// 7. Test query for payment collection
try {
    $test_query = $mysqli->query("SELECT * FROM tms_payment_collection LIMIT 1");
    $results['payment_collection_query'] = '✅ WORKING';
} catch(Exception $e) {
    $results['payment_collection_query'] = '❌ ERROR: ' . $e->getMessage();
}

// 8. Test query for payment settings
try {
    $test_query2 = $mysqli->query("SELECT * FROM tms_payment_settings LIMIT 1");
    $results['payment_settings_query'] = '✅ WORKING';
} catch(Exception $e) {
    $results['payment_settings_query'] = '❌ ERROR: ' . $e->getMessage();
}

// 9. Check technician QR column
try {
    $test_query3 = $mysqli->query("SELECT t_id, t_payment_qr FROM tms_technician LIMIT 1");
    $results['technician_qr_query'] = '✅ WORKING';
} catch(Exception $e) {
    $results['technician_qr_query'] = '❌ ERROR: ' . $e->getMessage();
}

// 10. Count technicians with QR codes
$qr_count = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_payment_qr IS NOT NULL AND t_payment_qr != ''");
$qr_count_data = $qr_count ? $qr_count->fetch_object()->count : 0;
$results['technicians_with_qr'] = $qr_count_data . ' technician(s) have personal QR uploaded';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment System Verification</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h2 {
            color: #667eea;
            font-weight: 900;
            margin-bottom: 25px;
        }
        .result-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .result-item strong {
            color: #2c3e50;
        }
        .status-ok {
            color: #28a745;
            font-weight: 700;
        }
        .status-warning {
            color: #ffc107;
            font-weight: 700;
        }
        .status-error {
            color: #dc3545;
            font-weight: 700;
        }
        .back-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-top: 20px;
        }
        .back-btn:hover {
            background: #5568d3;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-check-circle"></i> Payment System Verification</h2>
            
            <?php foreach($results as $key => $value): ?>
                <div class="result-item">
                    <strong><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</strong>
                    <span class="<?php 
                        if(strpos($value, '✅') !== false) echo 'status-ok';
                        elseif(strpos($value, '⚠️') !== false) echo 'status-warning';
                        else echo 'status-error';
                    ?>">
                        <?php echo $value; ?>
                    </span>
                </div>
            <?php endforeach; ?>
            
            <hr>
            
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> System Status:</strong>
                <ul class="mb-0 mt-2">
                    <li>All tables will be created automatically when pages are accessed</li>
                    <li>Directories will be created when files are uploaded</li>
                    <li>Payment collection requires tables to exist first</li>
                </ul>
            </div>
            
            <a href="admin-dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
