<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get a deleted user to test
$query = "SELECT * FROM tms_deleted_items WHERE di_item_type = 'user' LIMIT 1";
$result = $mysqli->query($query);
$deleted_user = $result->fetch_object();

$test_results = [];

if($deleted_user) {
    $data = json_decode($deleted_user->di_item_data, true);
    
    $test_results[] = "✅ Found deleted user: " . $data['u_fname'] . " " . $data['u_lname'];
    $test_results[] = "📋 User Data:";
    foreach($data as $key => $value) {
        $test_results[] = "  - $key: " . (is_null($value) ? 'NULL' : (empty($value) ? '(empty)' : $value));
    }
    
    // Check if user already exists
    $check_query = "SELECT * FROM tms_user WHERE u_id = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $data['u_id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        $test_results[] = "⚠️ User already exists in database (ID: " . $data['u_id'] . ")";
    } else {
        $test_results[] = "✅ User does not exist, can be restored";
    }
    
    // Test the restore query
    $test_results[] = "";
    $test_results[] = "🔧 Testing Restore Query:";
    
    $restore_query = "INSERT INTO tms_user (u_id, u_fname, u_lname, u_phone, u_addr, u_category, u_email, u_pwd, t_tech_category, t_tech_id, t_booking_date, t_booking_status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $restore_stmt = $mysqli->prepare($restore_query);
    
    if(!$restore_stmt) {
        $test_results[] = "❌ Prepare failed: " . $mysqli->error;
    } else {
        $test_results[] = "✅ Query prepared successfully";
        
        // Handle missing fields with defaults
        $t_tech_category = isset($data['t_tech_category']) ? $data['t_tech_category'] : '';
        $t_tech_id = isset($data['t_tech_id']) ? $data['t_tech_id'] : '';
        $t_booking_date = isset($data['t_booking_date']) ? $data['t_booking_date'] : '';
        $t_booking_status = isset($data['t_booking_status']) ? $data['t_booking_status'] : '';
        
        $test_results[] = "📝 Using values:";
        $test_results[] = "  - u_id: " . $data['u_id'];
        $test_results[] = "  - u_fname: " . $data['u_fname'];
        $test_results[] = "  - u_lname: " . $data['u_lname'];
        $test_results[] = "  - u_phone: " . $data['u_phone'];
        $test_results[] = "  - u_addr: " . $data['u_addr'];
        $test_results[] = "  - u_category: " . $data['u_category'];
        $test_results[] = "  - u_email: " . $data['u_email'];
        $test_results[] = "  - u_pwd: " . (isset($data['u_pwd']) ? '***' : 'NULL');
        $test_results[] = "  - t_tech_category: " . ($t_tech_category ? $t_tech_category : '(empty)');
        $test_results[] = "  - t_tech_id: " . ($t_tech_id ? $t_tech_id : '(empty)');
        $test_results[] = "  - t_booking_date: " . ($t_booking_date ? $t_booking_date : '(empty)');
        $test_results[] = "  - t_booking_status: " . ($t_booking_status ? $t_booking_status : '(empty)');
    }
} else {
    $test_results[] = "❌ No deleted users found in recycle bin";
    $test_results[] = "💡 Try deleting a user first from admin-manage-user.php";
}

// Check table structure
$test_results[] = "";
$test_results[] = "📊 User Table Structure:";
$structure = $mysqli->query("DESCRIBE tms_user");
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
                    <li class="breadcrumb-item active">Test User Restore</li>
                </ol>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-bug"></i> User Restore Debug
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Debug Information:</strong> This page helps diagnose user restore issues.
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
                            <a href="admin-manage-user.php" class="btn btn-warning">
                                <i class="fas fa-users"></i> Manage Users
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
