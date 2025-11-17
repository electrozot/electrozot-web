<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Ensure columns exist
$mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_subcategory VARCHAR(200) NULL");
$mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_gadget_name VARCHAR(200) NULL");
$mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS is_popular TINYINT(1) DEFAULT 0");

// Complete service list with all 43 services
$services = [
    // BASIC ELECTRICAL WORK - Wiring & Fixtures
    [
        'name' => 'Home Wiring Service',
        'description' => 'Complete home wiring installation and repair services including new wiring, rewiring, and electrical fault fixing.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Wiring & Fixtures',
        'gadget' => 'Home Wiring (New installation and repair)',
        'price' => 500.00,
        'duration' => '2-4 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Switch & Socket Installation',
        'description' => 'Professional installation and replacement of electrical switches and sockets of all types.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Wiring & Fixtures',
        'gadget' => 'Switch/Socket Installation and Replacement',
        'price' => 150.00,
        'duration' => '30 minutes - 1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Light Fixture Installation',
        'description' => 'Installation of tube lights, LED panels, chandeliers, and all types of lighting fixtures.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Wiring & Fixtures',
        'gadget' => 'Light Fixture Installation (Tube lights, LED panels, chandeliers)',
        'price' => 300.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Festive Lighting Setup',
        'description' => 'Professional light decoration and festive lighting setup for homes and events.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Wiring & Fixtures',
        'gadget' => 'Light Decoration/Festive Lighting Setup',
        'price' => 800.00,
        'duration' => '3-5 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    
    // BASIC ELECTRICAL WORK - Safety & Power
    [
        'name' => 'Circuit Breaker Repair',
        'description' => 'Troubleshooting and repair of circuit breakers and main fuse box panels.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Safety & Power',
        'gadget' => 'Circuit Breaker and Fuse Box (Main Panel) troubleshooting and repair',
        'price' => 600.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Inverter & UPS Installation',
        'description' => 'Installation and wiring of inverters, UPS systems, and voltage stabilizers.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Safety & Power',
        'gadget' => 'Inverter, UPS, and Voltage Stabilizer installation/wiring',
        'price' => 700.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Earthing System Installation',
        'description' => 'Professional grounding and earthing system installation for electrical safety.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Safety & Power',
        'gadget' => 'Grounding and Earthing system installation',
        'price' => 1200.00,
        'duration' => '3-4 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'New Electrical Point Installation',
        'description' => 'Installation of new electrical outlets and power points anywhere in your home.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Safety & Power',
        'gadget' => 'New Electrical Outlet/Point installation',
        'price' => 400.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Fan Regulator Repair',
        'description' => 'Repair and replacement of ceiling fan regulators and speed controllers.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Safety & Power',
        'gadget' => 'Ceiling Fan Regulator repair/replacement',
        'price' => 200.00,
        'duration' => '30 minutes',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Electrical Fault Finding',
        'description' => 'Expert electrical fault finding and short-circuit repair services.',
        'category' => 'BASIC ELECTRICAL WORK',
        'subcategory' => 'Safety & Power',
        'gadget' => 'Electrical fault finding and short-circuit repair',
        'price' => 500.00,
        'duration' => '1-3 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    
    // ELECTRONIC REPAIR - Major Appliances
    [
        'name' => 'AC Repair Service',
        'description' => 'Complete air conditioner repair for split, window, and central AC units.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Major Appliances',
        'gadget' => 'Air Conditioner (AC) Repair (Split, Window, Central)',
        'price' => 800.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Refrigerator Repair',
        'description' => 'Refrigerator repair and gas charging services for all brands.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Major Appliances',
        'gadget' => 'Refrigerator Repair and Gas Charging',
        'price' => 700.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Washing Machine Repair',
        'description' => 'Repair services for semi-automatic, fully automatic, front load, and top load washing machines.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Major Appliances',
        'gadget' => 'Washing Machine Repair (Semi/Fully automatic, Front/Top Load)',
        'price' => 600.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Microwave Oven Repair',
        'description' => 'Professional microwave oven repair and troubleshooting services.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Major Appliances',
        'gadget' => 'Microwave Oven Repair',
        'price' => 500.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Geyser Repair',
        'description' => 'Water heater and geyser repair services for all types and brands.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Major Appliances',
        'gadget' => 'Geyser (Water Heater) Repair',
        'price' => 450.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    
    // ELECTRONIC REPAIR - Other Gadgets
    [
        'name' => 'Fan Repair Service',
        'description' => 'Repair services for ceiling fans, table fans, and exhaust fans.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Fan Repair (Ceiling, Table, Exhaust)',
        'price' => 300.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'TV Repair Service',
        'description' => 'Television repair and troubleshooting for LED, LCD, and Smart TVs.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Television (TV) Repair and Troubleshooting',
        'price' => 600.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Electric Iron Repair',
        'description' => 'Repair services for electric irons and press machines.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Electric Iron/Press Repair',
        'price' => 200.00,
        'duration' => '30 minutes - 1 hour',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Music System Repair',
        'description' => 'Repair services for music systems and home theatre systems.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Music System/Home Theatre Repair',
        'price' => 500.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Electric Heater Repair',
        'description' => 'Repair services for room heaters, heating rods, and electric heaters.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Electric Heater Repair (Room Heaters, Rods)',
        'price' => 350.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Induction Cooktop Repair',
        'description' => 'Repair services for induction cooktops and electric stoves.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Induction Cooktop and Electric Stove Repair',
        'price' => 400.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Air Cooler Repair',
        'description' => 'Professional air cooler repair and maintenance services.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Air Cooler Repair',
        'price' => 400.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Power Tools Repair',
        'description' => 'Repair services for power tools including drills, cutters, and grinders.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Power Tools Repair (Drills, Cutters, Grinders, etc.)',
        'price' => 450.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Water Purifier Repair',
        'description' => 'Water filter and purifier repair services for all brands.',
        'category' => 'ELECTRONIC REPAIR',
        'subcategory' => 'Other Gadgets',
        'gadget' => 'Water Filter/Purifier Repair',
        'price' => 500.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    
    // INSTALLATION & SETUP - Appliance Setup
    [
        'name' => 'TV & DTH Installation',
        'description' => 'Professional TV and DTH dish installation and tuning services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'TV/DTH Dish Installation and Tuning',
        'price' => 400.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Electric Chimney Installation',
        'description' => 'Professional electric chimney installation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Electric Chimney Installation',
        'price' => 600.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Fan Installation',
        'description' => 'Installation services for ceiling fans and wall fans.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Ceiling and Wall Fan Installation',
        'price' => 300.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Washing Machine Installation',
        'description' => 'Professional washing machine installation and uninstallation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Washing Machine Installation and Uninstallation',
        'price' => 400.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Air Cooler Installation',
        'description' => 'Professional air cooler installation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Air Cooler Installation',
        'price' => 300.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Water Purifier Installation',
        'description' => 'Professional water filter and purifier installation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Water Filter/Purifier Installation',
        'price' => 500.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Geyser Installation',
        'description' => 'Professional geyser and water heater installation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Geyser/Water Heater Installation',
        'price' => 500.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Light Fixture Setup',
        'description' => 'Professional light fixture installation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Appliance Setup',
        'gadget' => 'Light Fixture Installation',
        'price' => 300.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    
    // INSTALLATION & SETUP - Tech & Security
    [
        'name' => 'CCTV Installation',
        'description' => 'Professional CCTV and security camera installation services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Tech & Security',
        'gadget' => 'CCTV and Security Camera Installation',
        'price' => 1500.00,
        'duration' => '3-4 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'WiFi Router Setup',
        'description' => 'Wi-Fi router and modem setup and troubleshooting services.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Tech & Security',
        'gadget' => 'Wi-Fi Router and Modem Setup/Troubleshooting',
        'price' => 300.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Smart Home Installation',
        'description' => 'Installation of smart home devices including smart switches and smart lights.',
        'category' => 'INSTALLATION & SETUP',
        'subcategory' => 'Tech & Security',
        'gadget' => 'Smart Home Device Installation (Smart switches, smart lights)',
        'price' => 800.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    
    // SERVICING & MAINTENANCE - Routine Care
    [
        'name' => 'AC Servicing',
        'description' => 'AC wet and dry servicing for optimal performance.',
        'category' => 'SERVICING & MAINTENANCE',
        'subcategory' => 'Routine Care',
        'gadget' => 'AC Wet and Dry Servicing',
        'price' => 600.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Washing Machine Maintenance',
        'description' => 'General maintenance and cleaning services for washing machines.',
        'category' => 'SERVICING & MAINTENANCE',
        'subcategory' => 'Routine Care',
        'gadget' => 'Washing Machine General Maintenance and Cleaning',
        'price' => 400.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Geyser Descaling',
        'description' => 'Geyser descaling and service for better efficiency.',
        'category' => 'SERVICING & MAINTENANCE',
        'subcategory' => 'Routine Care',
        'gadget' => 'Geyser Descaling and Service',
        'price' => 400.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Water Filter Service',
        'description' => 'Water filter cartridge replacement and general service.',
        'category' => 'SERVICING & MAINTENANCE',
        'subcategory' => 'Routine Care',
        'gadget' => 'Water Filter Cartridge Replacement and General Service',
        'price' => 350.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Water Tank Cleaning',
        'description' => 'Manual and motorized water tank cleaning services.',
        'category' => 'SERVICING & MAINTENANCE',
        'subcategory' => 'Routine Care',
        'gadget' => 'Water Tank Cleaning (Manual and Motorized)',
        'price' => 800.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    
    // PLUMBING WORK - Fixtures & Taps
    [
        'name' => 'Tap & Faucet Service',
        'description' => 'Tap, faucet, and shower installation and repair services.',
        'category' => 'PLUMBING WORK',
        'subcategory' => 'Fixtures & Taps',
        'gadget' => 'Tap, Faucet, and Shower Installation/Repair',
        'price' => 300.00,
        'duration' => '1 hour',
        'status' => 'Active',
        'popular' => 1
    ],
    [
        'name' => 'Washbasin Installation',
        'description' => 'Washbasin and sink installation and repair services.',
        'category' => 'PLUMBING WORK',
        'subcategory' => 'Fixtures & Taps',
        'gadget' => 'Washbasin and Sink Installation/Repair',
        'price' => 500.00,
        'duration' => '1-2 hours',
        'status' => 'Active',
        'popular' => 0
    ],
    [
        'name' => 'Toilet Installation',
        'description' => 'Toilet, commode, and flush tank installation services.',
        'category' => 'PLUMBING WORK',
        'subcategory' => 'Fixtures & Taps',
        'gadget' => 'Toilet, Commode, and Flush Tank Installation',
        'price' => 800.00,
        'duration' => '2-3 hours',
        'status' => 'Active',
        'popular' => 0
    ]
];

$inserted = 0;
$skipped = 0;
$errors = [];

foreach($services as $service) {
    // Check if service already exists
    $check = $mysqli->prepare("SELECT s_id FROM tms_service WHERE s_gadget_name = ?");
    $check->bind_param('s', $service['gadget']);
    $check->execute();
    $check->store_result();
    
    if($check->num_rows > 0) {
        $skipped++;
        $check->close();
        continue;
    }
    $check->close();
    
    // Insert service
    $query = "INSERT INTO tms_service (s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status, is_popular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    
    if($stmt) {
        $stmt->bind_param('sssssdssi', 
            $service['name'],
            $service['description'],
            $service['category'],
            $service['subcategory'],
            $service['gadget'],
            $service['price'],
            $service['duration'],
            $service['status'],
            $service['popular']
        );
        
        if($stmt->execute()) {
            $inserted++;
        } else {
            $errors[] = "Failed to insert: " . $service['name'];
        }
        $stmt->close();
    } else {
        $errors[] = "Failed to prepare statement for: " . $service['name'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Populate Services</li>
                </ol>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-database"></i> Service Population Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Service Population Complete!</h5>
                            <hr>
                            <p class="mb-2"><strong>Total Services Processed:</strong> <?php echo count($services); ?></p>
                            <p class="mb-2"><strong>Successfully Inserted:</strong> <span class="badge badge-success"><?php echo $inserted; ?></span></p>
                            <p class="mb-2"><strong>Skipped (Already Exists):</strong> <span class="badge badge-warning"><?php echo $skipped; ?></span></p>
                            <?php if(count($errors) > 0): ?>
                            <p class="mb-2"><strong>Errors:</strong> <span class="badge badge-danger"><?php echo count($errors); ?></span></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if(count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <h6>Errors Encountered:</h6>
                            <ul>
                                <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <h6>Service Categories Added:</h6>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-bolt text-primary"></i> BASIC ELECTRICAL WORK</span>
                                    <span class="badge badge-primary badge-pill">10 services</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-wrench text-success"></i> ELECTRONIC REPAIR</span>
                                    <span class="badge badge-success badge-pill">14 services</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-cog text-info"></i> INSTALLATION & SETUP</span>
                                    <span class="badge badge-info badge-pill">11 services</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-tools text-warning"></i> SERVICING & MAINTENANCE</span>
                                    <span class="badge badge-warning badge-pill">5 services</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-faucet text-danger"></i> PLUMBING WORK</span>
                                    <span class="badge badge-danger badge-pill">3 services</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="admin-manage-service.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> View All Services
                            </a>
                            <a href="admin-dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
</body>
</html>
