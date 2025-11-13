<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get a deleted service to test
$query = "SELECT * FROM tms_deleted_items WHERE di_item_type = 'service' LIMIT 1";
$result = $mysqli->query($query);
$deleted_service = $result->fetch_object();

$test_results = [];

if($deleted_service) {
    $data = json_decode($deleted_service->di_item_data, true);
    
    $test_results[] = "✅ Found deleted service: " . $data['s_name'];
    $test_results[] = "📋 Service Data:";
    foreach($data as $key => $value) {
        $test_results[] = "  - $key: " . (is_null($value) ? 'NULL' : $value);
    }
    
    // Check if service already exists
    $check_query = "SELECT * FROM tms_service WHERE s_id = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $data['s_id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        $test_results[] = "⚠️ Service already exists in database (ID: " . $data['s_id'] . ")";
    } else {
        $test_results[] = "✅ Service does not exist, can be restored";
    }
    
    // Test the restore query
    $test_results[] = "";
    $test_results[] = "🔧 Testing Restore Query:";
    
    $restore_query = "INSERT INTO tms_service (s_id, s_name, s_description, s_category, s_price, s_duration, s_status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $restore_stmt = $mysqli->prepare($restore_query);
    
    if(!$restore_stmt) {
        $test_results[] = "❌ Prepare failed: " . $mysqli->error;
    } else {
        $test_results[] = "✅ Query prepared successfully";
        
        // Handle missing fields with defaults
        $s_duration = isset($data['s_duration']) ? $data['s_duration'] : '1-2 hours';
        $s_status = isset($data['s_status']) ? $data['s_status'] : 'Active';
        
        $test_results[] = "📝 Using values:";
        $test_results[] = "  - s_id: " . $data['s_id'];
        $test_results[] = "  - s_name: " . $data['s_name'];
        $test_results[] = "  - s_description: " . (isset($data['s_description']) ? substr($data['s_description'], 0, 50) . '...' : 'NULL');
        $test_results[] = "  - s_category: " . $data['s_category'];
        $test_results[] = "  - s_price: " . $data['s_price'];
        $test_results[] = "  - s_duration: " . $s_duration;
        $test_results[] = "  - s_status: " . $s_status;
    }
} else {
    $test_results[] = "❌ No deleted services found in recycle bin";
    $test_results[] = "💡 Try deleting a service first from admin-manage-service.php";
}

// Check table structure
$test_results[] = "";
$test_results[] = "📊 Service Table Structure:";
$structure = $mysqli->query("DESCRIBE tms_service");
while($col = $structure->fetch_assoc()) {
    $test_results[] = "  - " . $col['Field'] . " (" . $col['Type'] . ") " . ($col['Null'] == 'NO' ? 'NOT NULL' : 'NULL');
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Test Service Restore</li>
                </ol>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bug"></i> Service Restore Debug
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Debug Information:</strong> This page helps diagnose service restore issues.
                        </div>
                        
                        <pre style="background: #f8f9fa; padding: 20px; border-radius: 5px; border: 1px solid #dee2e6;"><?php 
                            foreach($test_results as $line) {
                                echo htmlspecialchars($line) . "\n";
                            }
                        ?></pre>
                        
                        <div class="mt-4">
                            <a href="admin-recycle-bin.php" class="btn btn-primary">
                                <i class="fas fa-recycle"></i> Go to Recycle Bin
                            </a>
                            <a href="admin-manage-service.php" class="btn btn-info">
                                <i class="fas fa-cogs"></i> Manage Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
