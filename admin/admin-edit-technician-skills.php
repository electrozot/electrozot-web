<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$t_id = isset($_GET['t_id']) ? intval($_GET['t_id']) : 0;

// Create table if not exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_technician_skills (
    ts_id INT AUTO_INCREMENT PRIMARY KEY,
    ts_technician_id INT NOT NULL,
    ts_service_id INT NOT NULL,
    ts_added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tech_service (ts_technician_id, ts_service_id),
    INDEX idx_technician (ts_technician_id),
    INDEX idx_service (ts_service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Update skills
if(isset($_POST['update_skills'])) {
    $selected_services = isset($_POST['services']) ? $_POST['services'] : [];
    
    // Delete all existing skills for this technician
    $mysqli->query("DELETE FROM tms_technician_skills WHERE ts_technician_id = $t_id");
    
    // Insert new skills
    $count = 0;
    foreach($selected_services as $service_id) {
        $service_id = intval($service_id);
        $stmt = $mysqli->prepare("INSERT INTO tms_technician_skills (ts_technician_id, ts_service_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $t_id, $service_id);
        if($stmt->execute()) $count++;
    }
    
    $succ = "$count service skills updated successfully!";
}

// Get technician details
$tech = $mysqli->query("SELECT * FROM tms_technician WHERE t_id = $t_id")->fetch_object();
if(!$tech) {
    die("Technician not found");
}

// Get current skills
$current_skills = [];
$skills_result = $mysqli->query("SELECT ts_service_id FROM tms_technician_skills WHERE ts_technician_id = $t_id");
while($row = $skills_result->fetch_object()) {
    $current_skills[] = $row->ts_service_id;
}

// Get all services
$services = $mysqli->query("SELECT * FROM tms_service WHERE s_status='Active' ORDER BY s_category, s_name");
?>
<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
<?php include("vendor/inc/nav.php"); ?>
<div id="wrapper">
<?php include('vendor/inc/sidebar.php'); ?>
<div id="content-wrapper">
<div class="container-fluid">

<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="admin-manage-technician.php">Technicians</a></li>
    <li class="breadcrumb-item active">Edit Skills</li>
</ol>

<?php if(isset($succ)): ?>
<div class="alert alert-success"><?php echo $succ; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-tools"></i> Edit Skills for <?php echo htmlspecialchars($tech->t_name); ?>
        <a href="admin-manage-technician.php" class="btn btn-sm btn-secondary float-right">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Instructions:</strong> Check all services that this technician can perform. 
            When assigning bookings, only technicians with matching skills will be shown.
        </div>
        
        <form method="POST">
            <div class="row">
                <?php
                $current_category = '';
                while($service = $services->fetch_object()):
                    if($current_category != $service->s_category):
                        if($current_category != '') echo '</div></div>';
                        $current_category = $service->s_category;
                        echo '<div class="col-md-6 mb-4">';
                        echo '<h5 class="border-bottom pb-2"><i class="fas fa-wrench"></i> ' . htmlspecialchars($current_category) . '</h5>';
                        echo '<div class="pl-3">';
                    endif;
                    
                    $checked = in_array($service->s_id, $current_skills) ? 'checked' : '';
                ?>
                <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input" id="service<?php echo $service->s_id; ?>" 
                           name="services[]" value="<?php echo $service->s_id; ?>" <?php echo $checked; ?>>
                    <label class="custom-control-label" for="service<?php echo $service->s_id; ?>">
                        <?php echo htmlspecialchars($service->s_name); ?>
                        <small class="text-muted">(₹<?php echo number_format($service->s_price, 0); ?>)</small>
                    </label>
                </div>
                <?php endwhile; ?>
                </div></div>
            </div>
            
            <hr>
            <button type="submit" name="update_skills" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Update Skills
            </button>
            <a href="admin-manage-technician.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-times"></i> Cancel
            </a>
        </form>
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
