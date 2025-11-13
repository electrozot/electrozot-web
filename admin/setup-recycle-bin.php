<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Create the deleted_items table
$create_table = "CREATE TABLE IF NOT EXISTS tms_deleted_items (
    di_id INT AUTO_INCREMENT PRIMARY KEY,
    di_item_type VARCHAR(50) NOT NULL,
    di_item_id INT NOT NULL,
    di_item_data TEXT NOT NULL,
    di_deleted_by INT NOT NULL,
    di_deleted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    di_reason TEXT,
    INDEX(di_item_type),
    INDEX(di_deleted_date)
)";

if($mysqli->query($create_table)) {
    // Check if table exists
    $check = $mysqli->query("SHOW TABLES LIKE 'tms_deleted_items'");
    if($check->num_rows > 0) {
        $success = "✅ Recycle bin table created successfully!";
        
        // Get table info
        $info = $mysqli->query("DESCRIBE tms_deleted_items");
        $columns = [];
        while($row = $info->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    } else {
        $error = "❌ Table creation failed!";
    }
} else {
    $error = "❌ Error: " . $mysqli->error;
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
                    <li class="breadcrumb-item active">Setup Recycle Bin</li>
                </ol>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-recycle"></i> Recycle Bin Setup
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success">
                                <h4><?php echo $success; ?></h4>
                                <p>The recycle bin system is now ready to use!</p>
                                
                                <h5 class="mt-4">Table Structure:</h5>
                                <ul>
                                    <?php foreach($columns as $col): ?>
                                        <li><code><?php echo $col; ?></code></li>
                                    <?php endforeach; ?>
                                </ul>
                                
                                <hr>
                                <h5>Next Steps:</h5>
                                <ol>
                                    <li>Go to <a href="admin-recycle-bin.php">Recycle Bin</a> to view deleted items</li>
                                    <li>Delete any booking, technician, user, or service</li>
                                    <li>Check the recycle bin to see the deleted items</li>
                                    <li>Restore items if needed</li>
                                </ol>
                                
                                <div class="mt-4">
                                    <a href="admin-recycle-bin.php" class="btn btn-primary">
                                        <i class="fas fa-recycle"></i> Go to Recycle Bin
                                    </a>
                                    <a href="admin-manage-service-booking.php" class="btn btn-info">
                                        <i class="fas fa-calendar"></i> Manage Bookings
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <h4><?php echo $error; ?></h4>
                                <p>Please check your database connection and try again.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>
</html>
