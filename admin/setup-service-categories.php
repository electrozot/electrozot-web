<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-cog"></i> Setting up Service Categories System</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        echo "<h5>Step 1: Creating service_categories table...</h5>";
                        
                        $query1 = "CREATE TABLE IF NOT EXISTS tms_service_categories (
                            sc_id INT AUTO_INCREMENT PRIMARY KEY,
                            sc_name VARCHAR(100) NOT NULL,
                            sc_description TEXT,
                            sc_status ENUM('Active', 'Inactive') DEFAULT 'Active',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )";
                        
                        if($mysqli->query($query1)) {
                            echo "<p class='text-success'><i class='fas fa-check'></i> Service categories table created successfully</p>";
                        } else {
                            echo "<p class='text-danger'><i class='fas fa-times'></i> Error: " . $mysqli->error . "</p>";
                        }
                        
                        echo "<h5>Step 2: Adding columns to services table...</h5>";
                        
                        $check_column = $mysqli->query("SHOW COLUMNS FROM tms_service LIKE 's_category_id'");
                        if($check_column->num_rows == 0) {
                            $query2 = "ALTER TABLE tms_service ADD COLUMN s_category_id INT DEFAULT NULL AFTER s_id";
                            if($mysqli->query($query2)) {
                                echo "<p class='text-success'><i class='fas fa-check'></i> Category ID column added</p>";
                            } else {
                                echo "<p class='text-danger'><i class='fas fa-times'></i> Error: " . $mysqli->error . "</p>";
                            }
                        } else {
                            echo "<p class='text-info'><i class='fas fa-info'></i> Category ID column already exists</p>";
                        }
                        
                        $check_column2 = $mysqli->query("SHOW COLUMNS FROM tms_service LIKE 's_type'");
                        if($check_column2->num_rows == 0) {
                            $query3 = "ALTER TABLE tms_service ADD COLUMN s_type ENUM('Installation', 'Repair', 'Servicing', 'Maintenance', 'Other') DEFAULT 'Installation' AFTER s_category_id";
                            if($mysqli->query($query3)) {
                                echo "<p class='text-success'><i class='fas fa-check'></i> Service type column added</p>";
                            } else {
                                echo "<p class='text-danger'><i class='fas fa-times'></i> Error: " . $mysqli->error . "</p>";
                            }
                        } else {
                            echo "<p class='text-info'><i class='fas fa-info'></i> Service type column already exists</p>";
                        }
                        
                        echo "<h5>Step 3: Inserting default categories...</h5>";
                        
                        $categories = [
                            ['AC', 'Air Conditioning services'],
                            ['TV', 'Television services'],
                            ['Fan', 'Fan services'],
                            ['Heater', 'Heater services'],
                            ['Cooler', 'Cooler services'],
                            ['Refrigerator', 'Refrigerator services'],
                            ['Washing Machine', 'Washing Machine services'],
                            ['Water Filter', 'Water Filter services'],
                            ['Water Tank', 'Water Tank services'],
                            ['Dish', 'Dish services'],
                            ['WiFi', 'WiFi services'],
                            ['Water Geyser', 'Water Geyser services'],
                            ['Lights', 'Lights services'],
                            ['Electric Chimney', 'Electric Chimney services'],
                            ['Camera', 'Camera services'],
                            ['Plumbing', 'Plumbing services'],
                            ['Induction Cooktop', 'Induction Cooktop services'],
                            ['Switch Sockets', 'Switch and Sockets'],
                            ['Electrical Work', 'Basic electrical work'],
                            ['Hand Tools', 'Hand tools and drill machine'],
                            ['Music System', 'Music system services']
                        ];
                        
                        $inserted = 0;
                        foreach($categories as $cat) {
                            $check = $mysqli->query("SELECT * FROM tms_service_categories WHERE sc_name = '{$cat[0]}'");
                            if($check->num_rows == 0) {
                                $stmt = $mysqli->prepare("INSERT INTO tms_service_categories (sc_name, sc_description) VALUES (?, ?)");
                                $stmt->bind_param('ss', $cat[0], $cat[1]);
                                if($stmt->execute()) {
                                    $inserted++;
                                }
                            }
                        }
                        
                        echo "<p class='text-success'><i class='fas fa-check'></i> Inserted $inserted new categories</p>";
                        
                        echo "<hr>";
                        echo "<div class='alert alert-success'>";
                        echo "<h4><i class='fas fa-check-circle'></i> Setup Complete!</h4>";
                        echo "<p>The service categories system has been set up successfully.</p>";
                        echo "</div>";
                        
                        echo "<div class='btn-group' role='group'>";
                        echo "<a href='admin-manage-service-categories.php' class='btn btn-primary'><i class='fas fa-list'></i> Manage Categories</a>";
                        echo "<a href='admin-add-service.php' class='btn btn-success'><i class='fas fa-plus'></i> Add Service</a>";
                        echo "<a href='admin-add-technician.php' class='btn btn-info'><i class='fas fa-user-plus'></i> Add Technician</a>";
                        echo "</div>";
                        ?>
                    </div>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
</body>
</html>
