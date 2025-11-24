<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

$technician_id = isset($_GET['t_id']) ? intval($_GET['t_id']) : 0;

if ($technician_id == 0) {
    header("Location: admin-manage-technician.php");
    exit;
}

// Get technician details
$query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $technician_id);
$stmt->execute();
$technician = $stmt->get_result()->fetch_assoc();

if (!$technician) {
    $_SESSION['error'] = "Technician not found";
    header("Location: admin-manage-technician.php");
    exit;
}

// All 43 services
$all_services = [
    'Basic Electrical Work - Wiring & Fixtures' => [
        'Home Wiring - New Installation', 'Home Wiring - Repair', 'Switch/Socket - Installation',
        'Switch/Socket - Replacement', 'Light Fixture - Installation', 'Festive Lighting - Setup'
    ],
    'Basic Electrical Work - Safety & Power' => [
        'Circuit Breaker - Repair', 'Inverter - Installation', 'UPS - Installation',
        'Voltage Stabilizer - Installation', 'Grounding System - Installation', 'Electrical Fault - Repair'
    ],
    'Electronic Repair - Major Appliances' => [
        'AC (Split) - Repair', 'AC (Window) - Repair', 'Refrigerator - Repair',
        'Refrigerator - Gas Charging', 'Washing Machine - Repair', 'Microwave Oven - Repair', 'Geyser/Water Heater - Repair'
    ],
    'Electronic Repair - Other Gadgets' => [
        'Ceiling Fan - Repair', 'Table Fan - Repair', 'LED TV - Repair', 'Smart TV - Repair',
        'Electric Iron - Repair', 'Induction Cooktop - Repair', 'Air Cooler - Repair', 'Water Filter/Purifier - Repair'
    ],
    'Installation & Setup - Appliance Setup' => [
        'TV/DTH - Installation', 'Electric Chimney - Installation', 'Ceiling Fan - Installation',
        'Washing Machine - Installation', 'Air Cooler - Installation', 'Water Filter/Purifier - Installation', 'Geyser/Water Heater - Installation'
    ],
    'Installation & Setup - Tech & Security' => [
        'CCTV Camera - Installation (Single)', 'CCTV Camera - Installation (4 Cameras)',
        'Wi-Fi Router - Setup', 'Smart Switch - Installation', 'Smart Light - Installation'
    ],
    'Servicing & Maintenance - Routine Care' => [
        'AC - Wet Servicing', 'AC - Dry Servicing', 'Washing Machine - Cleaning',
        'Geyser - Descaling', 'Water Filter - Cartridge Replacement', 'Water Tank - Cleaning (Manual)', 'Water Tank - Cleaning (Motorized)'
    ],
    'Plumbing Work - Fixtures & Taps' => [
        'Tap/Faucet - Installation', 'Tap/Faucet - Repair', 'Shower - Installation', 'Shower - Repair',
        'Washbasin - Installation', 'Kitchen Sink - Installation', 'Toilet/Commode - Installation', 'Flush Tank - Installation'
    ]
];

// Get current skills
$current_skills = !empty($technician['t_skills']) ? explode(',', $technician['t_skills']) : [];

// Handle form submission
if (isset($_POST['update_skills'])) {
    $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];
    $t_skills = implode(',', $selected_skills);
    
    $update_query = "UPDATE tms_technician SET t_skills = ? WHERE t_id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("si", $t_skills, $technician_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Skills updated successfully! Now has " . count($selected_skills) . " skills.";
        header("Location: admin-edit-technician-skills.php?t_id=" . $technician_id);
        exit;
    } else {
        $_SESSION['error'] = "Error updating skills";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Skills - <?php echo htmlspecialchars($technician['t_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; }
        .skills-section { background: white; padding: 25px; border-radius: 10px; margin-top: 20px; }
        .category-header { background: #667eea; color: white; padding: 10px 15px; border-radius: 8px; margin-top: 15px; margin-bottom: 10px; font-weight: 600; }
        .skill-checkbox { margin: 8px 0; }
        .skill-checkbox label { cursor: pointer; padding: 8px 12px; border-radius: 5px; transition: all 0.2s; display: inline-block; width: 100%; }
        .skill-checkbox input:checked + label { background: #e0e7ff; color: #667eea; font-weight: 600; }
        .select-all-btn { background: #10b981; color: white; border: none; padding: 5px 15px; border-radius: 5px; font-size: 12px; margin-left: 10px; }
        .skill-count { background: #667eea; color: white; padding: 10px 20px; border-radius: 20px; font-size: 16px; font-weight: 600; position: fixed; bottom: 20px; right: 20px; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .info-card { background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-edit"></i> Edit Technician Skills</h2>
                    <h4><?php echo htmlspecialchars($technician['t_name']); ?></h4>
                    <p class="mb-0">ID: <?php echo htmlspecialchars($technician['t_id_no']); ?> | Status: <?php echo $technician['t_status']; ?></p>
                </div>
                <a href="admin-manage-technician.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="info-card">
            <h6><i class="fas fa-info-circle"></i> Current Status</h6>
            <div class="row">
                <div class="col-md-4">
                    <strong>Total Skills:</strong> <?php echo count($current_skills); ?>
                </div>
                <div class="col-md-4">
                    <strong>Current Bookings:</strong> <?php echo $technician['t_current_bookings']; ?> / <?php echo $technician['t_booking_limit']; ?>
                </div>
                <div class="col-md-4">
                    <strong>Available Slots:</strong> <?php echo ($technician['t_booking_limit'] - $technician['t_current_bookings']); ?>
                </div>
            </div>
        </div>

        <form method="POST">
            <div class="skills-section">
                <h4><i class="fas fa-tools"></i> Select Skills</h4>
                <p class="text-muted">Tick all services this technician can handle. Only bookings matching these skills will show this technician.</p>
                
                <?php foreach ($all_services as $category => $services): ?>
                    <div class="category-header">
                        <?php echo htmlspecialchars($category); ?> (<?php echo count($services); ?> services)
                        <button type="button" class="select-all-btn" onclick="selectCategory(this)">
                            Select All
                        </button>
                    </div>
                    <div class="row">
                        <?php foreach ($services as $service): 
                            $is_checked = in_array($service, $current_skills);
                        ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="skill-checkbox">
                                    <input type="checkbox" name="skills[]" value="<?php echo htmlspecialchars($service); ?>" 
                                           id="skill_<?php echo md5($service); ?>" class="form-check-input" 
                                           <?php echo $is_checked ? 'checked' : ''; ?>
                                           onchange="updateCount()">
                                    <label for="skill_<?php echo md5($service); ?>" class="form-check-label">
                                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($service); ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4 mb-5">
                <button type="submit" name="update_skills" class="btn btn-success btn-lg px-5">
                    <i class="fas fa-save"></i> Update Skills
                </button>
            </div>
        </form>

        <div class="skill-count" id="skillCount">
            <i class="fas fa-check-double"></i> <?php echo count($current_skills); ?> Skills Selected
        </div>
    </div>

    <script>
        function updateCount() {
            const checked = document.querySelectorAll('input[name="skills[]"]:checked').length;
            document.getElementById('skillCount').innerHTML = '<i class="fas fa-check-double"></i> ' + checked + ' Skills Selected';
        }

        function selectCategory(btn) {
            const categoryDiv = btn.closest('.category-header').nextElementSibling;
            const checkboxes = categoryDiv.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            updateCount();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
