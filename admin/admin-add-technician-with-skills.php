<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// All 43 services organized by category
$all_services = [
    'Basic Electrical Work - Wiring & Fixtures' => [
        'Home Wiring - New Installation',
        'Home Wiring - Repair',
        'Switch/Socket - Installation',
        'Switch/Socket - Replacement',
        'Light Fixture - Installation',
        'Festive Lighting - Setup'
    ],
    'Basic Electrical Work - Safety & Power' => [
        'Circuit Breaker - Repair',
        'Inverter - Installation',
        'UPS - Installation',
        'Voltage Stabilizer - Installation',
        'Grounding System - Installation',
        'Electrical Fault - Repair'
    ],
    'Electronic Repair - Major Appliances' => [
        'AC (Split) - Repair',
        'AC (Window) - Repair',
        'Refrigerator - Repair',
        'Refrigerator - Gas Charging',
        'Washing Machine - Repair',
        'Microwave Oven - Repair',
        'Geyser/Water Heater - Repair'
    ],
    'Electronic Repair - Other Gadgets' => [
        'Ceiling Fan - Repair',
        'Table Fan - Repair',
        'LED TV - Repair',
        'Smart TV - Repair',
        'Electric Iron - Repair',
        'Induction Cooktop - Repair',
        'Air Cooler - Repair',
        'Water Filter/Purifier - Repair'
    ],
    'Installation & Setup - Appliance Setup' => [
        'TV/DTH - Installation',
        'Electric Chimney - Installation',
        'Ceiling Fan - Installation',
        'Washing Machine - Installation',
        'Air Cooler - Installation',
        'Water Filter/Purifier - Installation',
        'Geyser/Water Heater - Installation'
    ],
    'Installation & Setup - Tech & Security' => [
        'CCTV Camera - Installation (Single)',
        'CCTV Camera - Installation (4 Cameras)',
        'Wi-Fi Router - Setup',
        'Smart Switch - Installation',
        'Smart Light - Installation'
    ],
    'Servicing & Maintenance - Routine Care' => [
        'AC - Wet Servicing',
        'AC - Dry Servicing',
        'Washing Machine - Cleaning',
        'Geyser - Descaling',
        'Water Filter - Cartridge Replacement',
        'Water Tank - Cleaning (Manual)',
        'Water Tank - Cleaning (Motorized)'
    ],
    'Plumbing Work - Fixtures & Taps' => [
        'Tap/Faucet - Installation',
        'Tap/Faucet - Repair',
        'Shower - Installation',
        'Shower - Repair',
        'Washbasin - Installation',
        'Kitchen Sink - Installation',
        'Toilet/Commode - Installation',
        'Flush Tank - Installation'
    ]
];

// Handle form submission
if (isset($_POST['add_technician'])) {
    $t_name = $_POST['t_name'];
    $t_id_no = $_POST['t_id_no'];
    $t_phone = $_POST['t_phone'];
    $t_email = $_POST['t_email'];
    $t_experience = $_POST['t_experience'];
    $t_category = $_POST['t_category'];
    $t_status = $_POST['t_status'];
    $t_booking_limit = intval($_POST['t_booking_limit']);
    
    // Get selected skills
    $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];
    $t_skills = implode(',', $selected_skills);
    
    $query = "INSERT INTO tms_technician (t_name, t_id_no, t_phone, t_email, t_experience, t_category, t_status, t_booking_limit, t_current_bookings, t_skills) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssssssss", $t_name, $t_id_no, $t_phone, $t_email, $t_experience, $t_category, $t_status, $t_booking_limit, $t_skills);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Technician added successfully with " . count($selected_skills) . " skills!";
        header("Location: admin-manage-technician.php");
        exit;
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Technician with Skills</title>
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
        .skill-count { background: #667eea; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 600; position: fixed; bottom: 20px; right: 20px; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-user-plus"></i> Add New Technician</h2>
                    <p class="mb-0">Fill details and select skills</p>
                </div>
                <a href="admin-manage-technician.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <form method="POST">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="t_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Technician ID *</label>
                            <input type="text" name="t_id_no" class="form-control" placeholder="e.g., TECH001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="t_phone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="t_email" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Experience (Years)</label>
                            <input type="number" name="t_experience" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Primary Category</label>
                            <select name="t_category" class="form-select">
                                <option value="Electrical">Electrical</option>
                                <option value="Electronic Repair">Electronic Repair</option>
                                <option value="Installation">Installation</option>
                                <option value="Plumbing">Plumbing</option>
                                <option value="Multi-Skilled">Multi-Skilled</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Booking Limit</label>
                            <input type="number" name="t_booking_limit" class="form-control" min="1" value="5">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Status</label>
                            <select name="t_status" class="form-select">
                                <option value="Available">Available</option>
                                <option value="Busy">Busy</option>
                                <option value="On Leave">On Leave</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="skills-section">
                <h4><i class="fas fa-tools"></i> Select Skills (Tick all services this technician can handle)</h4>
                <p class="text-muted">Select at least one skill. Technician will only be shown for bookings matching these skills.</p>
                
                <?php foreach ($all_services as $category => $services): ?>
                    <div class="category-header">
                        <?php echo htmlspecialchars($category); ?> (<?php echo count($services); ?> services)
                        <button type="button" class="select-all-btn" onclick="selectCategory('<?php echo htmlspecialchars($category); ?>')">
                            Select All
                        </button>
                    </div>
                    <div class="row">
                        <?php foreach ($services as $service): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="skill-checkbox">
                                    <input type="checkbox" name="skills[]" value="<?php echo htmlspecialchars($service); ?>" 
                                           id="skill_<?php echo md5($service); ?>" class="form-check-input" 
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
                <button type="submit" name="add_technician" class="btn btn-success btn-lg px-5">
                    <i class="fas fa-save"></i> Add Technician
                </button>
            </div>
        </form>

        <div class="skill-count" id="skillCount">
            <i class="fas fa-check-double"></i> 0 Skills Selected
        </div>
    </div>

    <script>
        function updateCount() {
            const checked = document.querySelectorAll('input[name="skills[]"]:checked').length;
            document.getElementById('skillCount').innerHTML = '<i class="fas fa-check-double"></i> ' + checked + ' Skills Selected';
        }

        function selectCategory(category) {
            const categoryDiv = event.target.closest('.category-header').nextElementSibling;
            const checkboxes = categoryDiv.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            updateCount();
        }

        // Select all button
        document.addEventListener('DOMContentLoaded', function() {
            updateCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
