<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get a deleted booking to test
$query = "SELECT * FROM tms_deleted_items WHERE di_item_type = 'booking' LIMIT 1";
$result = $mysqli->query($query);
$deleted_booking = $result->fetch_object();

$test_results = [];

if($deleted_booking) {
    $data = json_decode($deleted_booking->di_item_data, true);
    
    $test_results[] = "✅ Found deleted booking: #" . $data['sb_id'];
    $test_results[] = "📋 Booking Data:";
    foreach($data as $key => $value) {
        $test_results[] = "  - $key: " . (is_null($value) ? 'NULL' : (empty($value) ? '(empty)' : $value));
    }
    
    // Check if booking already exists
    $check_query = "SELECT * FROM tms_service_booking WHERE sb_id = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $data['sb_id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        $test_results[] = "⚠️ Booking already exists in database (ID: " . $data['sb_id'] . ")";
    } else {
        $test_results[] = "✅ Booking does not exist, can be restored";
    }
    
    // Test the restore query
    $test_results[] = "";
    $test_results[] = "🔧 Testing Restore Query:";
    
    $restore_query = "INSERT INTO tms_service_booking (sb_id, sb_user_id, sb_service_id, sb_technician_id, sb_booking_date, sb_booking_time, sb_address, sb_phone, sb_description, sb_status, sb_total_price) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $restore_stmt = $mysqli->prepare($restore_query);
    
    if(!$restore_stmt) {
        $test_results[] = "❌ Prepare failed: " . $mysqli->error;
    } else {
        $test_results[] = "✅ Query prepared successfully";
        
        // Handle missing fields with defaults
        $sb_technician_id = isset($data['sb_technician_id']) ? $data['sb_technician_id'] : null;
        $sb_address = isset($data['sb_address']) ? $data['sb_address'] : '';
        $sb_phone = isset($data['sb_phone']) ? $data['sb_phone'] : '';
        $sb_description = isset($data['sb_description']) ? $data['sb_description'] : '';
        $sb_total_price = isset($data['sb_total_price']) ? $data['sb_total_price'] : 0.00;
        
        $test_results[] = "📝 Using values:";
        $test_results[] = "  - sb_id: " . $data['sb_id'];
        $test_results[] = "  - sb_user_id: " . $data['sb_user_id'];
        $test_results[] = "  - sb_service_id: " . $data['sb_service_id'];
        $test_results[] = "  - sb_technician_id: " . ($sb_technician_id ? $sb_technician_id : 'NULL');
        $test_results[] = "  - sb_booking_date: " . $data['sb_booking_date'];
        $test_results[] = "  - sb_booking_time: " . $data['sb_booking_time'];
        $test_results[] = "  - sb_address: " . ($sb_address ? substr($sb_address, 0, 50) . '...' : '(empty)');
        $test_results[] = "  - sb_phone: " . ($sb_phone ? $sb_phone : '(empty)');
        $test_results[] = "  - sb_description: " . ($sb_description ? substr($sb_description, 0, 50) . '...' : '(empty)');
        $test_results[] = "  - sb_status: " . $data['sb_status'];
        $test_results[] = "  - sb_total_price: " . $sb_total_price;
    }
} else {
    $test_results[] = "❌ No deleted bookings found in recycle bin";
    $test_results[] = "💡 Try deleting a booking first from admin-manage-service-booking.php";
}

// Check table structure
$test_results[] = "";
$test_results[] = "📊 Service Booking Table Structure:";
$structure = $mysqli->query("DESCRIBE tms_service_booking");
while($col = $structure->fetch_assoc()) {
    $test_results[] = "  - " . $col['Field'] . " (" . $col['Type'] . ") " . ($col['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . " " . ($col['Default'] ? "DEFAULT: " . $col['Default'] : '');
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
                    <li class="breadcrumb-item active">Test Booking Restore</li>
                </ol>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-bug"></i> Booking Restore Debug
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Debug Information:</strong> This page helps diagnose booking restore issues.
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
                            <a href="admin-manage-service-booking.php" class="btn btn-info">
                                <i class="fas fa-calendar"></i> Manage Bookings
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
