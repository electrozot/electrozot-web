<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

$category = isset($_GET['category']) ? $_GET['category'] : '';
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';

if (empty($category) || empty($subcategory)) {
    header("Location: book-service-step1.php");
    exit();
}

// Define services manually (works without SQL file)
$services_data = [
    'Basic Electrical Work' => [
        'Wiring & Fixtures' => [
            ['name' => 'Home Wiring - New Installation', 'desc' => 'Complete new home wiring installation', 'price' => 2500, 'duration' => '1-2 days'],
            ['name' => 'Home Wiring - Repair', 'desc' => 'Repair existing home wiring', 'price' => 800, 'duration' => '2-4 hours'],
            ['name' => 'Switch/Socket - Installation', 'desc' => 'Install new switches and sockets', 'price' => 150, 'duration' => '30 mins'],
            ['name' => 'Switch/Socket - Replacement', 'desc' => 'Replace old switches and sockets', 'price' => 120, 'duration' => '30 mins'],
            ['name' => 'Light Fixture - Installation', 'desc' => 'Install tube lights, LED panels, chandeliers', 'price' => 300, 'duration' => '1 hour'],
            ['name' => 'Festive Lighting - Setup', 'desc' => 'Decorative lighting installation', 'price' => 600, 'duration' => '2-4 hours']
        ],
        'Safety & Power' => [
            ['name' => 'Circuit Breaker - Repair', 'desc' => 'Fix circuit breaker issues', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Inverter - Installation', 'desc' => 'Complete inverter installation', 'price' => 800, 'duration' => '3-4 hours'],
            ['name' => 'UPS - Installation', 'desc' => 'UPS system installation', 'price' => 600, 'duration' => '2-3 hours'],
            ['name' => 'Voltage Stabilizer - Installation', 'desc' => 'Install voltage stabilizer', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Grounding System - Installation', 'desc' => 'Earthing system setup', 'price' => 1000, 'duration' => '4-6 hours'],
            ['name' => 'Electrical Fault - Repair', 'desc' => 'Find and fix electrical faults', 'price' => 500, 'duration' => '1-2 hours']
        ]
    ],
    'Electronic Repair' => [
        'Major Appliances' => [
            ['name' => 'AC (Split) - Repair', 'desc' => 'Split AC repair service', 'price' => 800, 'duration' => '2-3 hours'],
            ['name' => 'AC (Window) - Repair', 'desc' => 'Window AC repair', 'price' => 600, 'duration' => '2-3 hours'],
            ['name' => 'Refrigerator - Repair', 'desc' => 'Fridge repair and troubleshooting', 'price' => 700, 'duration' => '2-3 hours'],
            ['name' => 'Refrigerator - Gas Charging', 'desc' => 'Gas refill service', 'price' => 1200, 'duration' => '2-3 hours'],
            ['name' => 'Washing Machine - Repair', 'desc' => 'All types washing machine repair', 'price' => 700, 'duration' => '2-3 hours'],
            ['name' => 'Microwave Oven - Repair', 'desc' => 'Microwave repair service', 'price' => 600, 'duration' => '1-2 hours'],
            ['name' => 'Geyser/Water Heater - Repair', 'desc' => 'Geyser repair service', 'price' => 500, 'duration' => '1-2 hours']
        ],
        'Other Gadgets' => [
            ['name' => 'Ceiling Fan - Repair', 'desc' => 'Ceiling fan repair', 'price' => 300, 'duration' => '1 hour'],
            ['name' => 'Table Fan - Repair', 'desc' => 'Table fan repair', 'price' => 200, 'duration' => '1 hour'],
            ['name' => 'LED TV - Repair', 'desc' => 'LED TV repair service', 'price' => 800, 'duration' => '2-3 hours'],
            ['name' => 'Smart TV - Repair', 'desc' => 'Smart TV repair', 'price' => 1000, 'duration' => '2-4 hours'],
            ['name' => 'Electric Iron - Repair', 'desc' => 'Iron repair service', 'price' => 200, 'duration' => '1 hour'],
            ['name' => 'Induction Cooktop - Repair', 'desc' => 'Induction repair', 'price' => 500, 'duration' => '1-2 hours'],
            ['name' => 'Air Cooler - Repair', 'desc' => 'Air cooler repair', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Water Filter/Purifier - Repair', 'desc' => 'Filter repair service', 'price' => 500, 'duration' => '1-2 hours']
        ]
    ],
    'Installation & Setup' => [
        'Appliance Setup' => [
            ['name' => 'TV/DTH - Installation', 'desc' => 'TV and DTH dish installation', 'price' => 600, 'duration' => '2-3 hours'],
            ['name' => 'Electric Chimney - Installation', 'desc' => 'Kitchen chimney installation', 'price' => 800, 'duration' => '2-3 hours'],
            ['name' => 'Ceiling Fan - Installation', 'desc' => 'New ceiling fan installation', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Washing Machine - Installation', 'desc' => 'Washing machine setup', 'price' => 500, 'duration' => '1-2 hours'],
            ['name' => 'Air Cooler - Installation', 'desc' => 'Air cooler installation', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Water Filter/Purifier - Installation', 'desc' => 'Filter installation', 'price' => 700, 'duration' => '2-3 hours'],
            ['name' => 'Geyser/Water Heater - Installation', 'desc' => 'Geyser installation', 'price' => 700, 'duration' => '2-3 hours']
        ],
        'Tech & Security' => [
            ['name' => 'CCTV Camera - Installation (Single)', 'desc' => 'Single camera installation', 'price' => 1200, 'duration' => '2-3 hours'],
            ['name' => 'CCTV Camera - Installation (4 Cameras)', 'desc' => 'Complete 4 camera system', 'price' => 4000, 'duration' => '1 day'],
            ['name' => 'Wi-Fi Router - Setup', 'desc' => 'Router installation and configuration', 'price' => 300, 'duration' => '1 hour'],
            ['name' => 'Smart Switch - Installation', 'desc' => 'Smart home switch installation', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Smart Light - Installation', 'desc' => 'Smart lighting system', 'price' => 500, 'duration' => '1-2 hours']
        ]
    ],
    'Servicing & Maintenance' => [
        'Routine Care' => [
            ['name' => 'AC - Wet Servicing', 'desc' => 'Complete AC wet servicing with deep cleaning', 'price' => 800, 'duration' => '2-3 hours'],
            ['name' => 'AC - Dry Servicing', 'desc' => 'AC dry servicing and filter cleaning', 'price' => 500, 'duration' => '1-2 hours'],
            ['name' => 'Washing Machine - Cleaning', 'desc' => 'Deep cleaning of washing machine', 'price' => 400, 'duration' => '1-2 hours'],
            ['name' => 'Geyser - Descaling', 'desc' => 'Geyser descaling and cleaning', 'price' => 600, 'duration' => '2-3 hours'],
            ['name' => 'Water Filter - Cartridge Replacement', 'desc' => 'Replace filter cartridge', 'price' => 400, 'duration' => '1 hour'],
            ['name' => 'Water Tank - Cleaning (Manual)', 'desc' => 'Manual tank cleaning', 'price' => 800, 'duration' => '3-4 hours'],
            ['name' => 'Water Tank - Cleaning (Motorized)', 'desc' => 'Motorized tank cleaning', 'price' => 1200, 'duration' => '2-3 hours']
        ]
    ],
    'Plumbing Work' => [
        'Fixtures & Taps' => [
            ['name' => 'Tap/Faucet - Installation', 'desc' => 'New tap installation', 'price' => 300, 'duration' => '1 hour'],
            ['name' => 'Tap/Faucet - Repair', 'desc' => 'Tap repair service', 'price' => 200, 'duration' => '1 hour'],
            ['name' => 'Shower - Installation', 'desc' => 'Shower installation', 'price' => 500, 'duration' => '1-2 hours'],
            ['name' => 'Shower - Repair', 'desc' => 'Shower repair', 'price' => 300, 'duration' => '1 hour'],
            ['name' => 'Washbasin - Installation', 'desc' => 'Washbasin installation', 'price' => 800, 'duration' => '2-3 hours'],
            ['name' => 'Kitchen Sink - Installation', 'desc' => 'Kitchen sink installation', 'price' => 700, 'duration' => '2-3 hours'],
            ['name' => 'Toilet/Commode - Installation', 'desc' => 'Complete toilet installation', 'price' => 1200, 'duration' => '3-4 hours'],
            ['name' => 'Flush Tank - Installation', 'desc' => 'Flush tank installation', 'price' => 600, 'duration' => '1-2 hours']
        ]
    ]
];

$services = [];
if (isset($services_data[$category][$subcategory])) {
    $services = $services_data[$category][$subcategory];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Service - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            padding-bottom: 20px;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 20px 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }
        
        .header-content {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 15px;
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .page-title {
            margin-left: auto;
        }
        
        .logo {
            height: 45px;
            width: auto;
        }
        
        .brand-text h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 11px;
            opacity: 0.85;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        
        .back-btn {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
            flex-shrink: 0;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.35);
            transform: scale(1.05);
        }
        
        .page-title {
            font-size: 16px;
            font-weight: 600;
            text-align: right;
        }
        
        .step-indicator {
            background: white;
            padding: 15px;
            margin: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .step {
            flex: 1;
            text-align: center;
        }
        
        .step-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px;
            font-weight: 700;
        }
        
        .step.active .step-circle {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }
        
        .step.completed .step-circle {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .step-label {
            font-size: 11px;
            color: #666;
        }
        
        .content {
            padding: 15px;
        }
        
        .breadcrumb {
            background: white;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            font-size: 13px;
            color: #666;
        }
        
        .breadcrumb strong {
            color: #667eea;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }
        
        .service-card:active {
            transform: scale(0.98);
        }
        
        .service-name {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .service-desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .service-meta {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        
        .duration-badge {
            background: #f0f0f0;
            color: #666;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .book-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-top: 10px;
            font-weight: 600;
        }
        
        /* Responsive for PC/Tablet */
        @media (min-width: 768px) {
            body {
                max-width: 1200px;
                margin: 0 auto;
                box-shadow: 0 0 40px rgba(0,0,0,0.15);
            }
            
            .header {
                border-radius: 0;
            }
            
            .header-content {
                padding: 0 50px;
            }
            
            .logo {
                height: 50px;
            }
            
            .brand-text h2 {
                font-size: 20px;
            }
            
            .brand-text p {
                font-size: 12px;
            }
            
            .step-indicator {
                margin: 20px 50px;
            }
            
            .content {
                padding: 30px 50px;
            }
            
            .services-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
            
            .service-card {
                padding: 25px;
                margin-bottom: 0;
            }
            
            .service-name {
                font-size: 18px;
            }
            
            .service-desc {
                font-size: 14px;
            }
        }
        
        @media (min-width: 1024px) {
            body {
                max-width: 1400px;
            }
            
            .content {
                padding: 40px 80px;
            }
            
            .services-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            
            .service-card {
                padding: 30px;
            }
        }
        
        @media (min-width: 1440px) {
            body {
                max-width: 1600px;
            }
            
            .content {
                padding: 50px 100px;
            }
            
            .services-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 35px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="brand-section">
                <a href="book-service-step2.php?category=<?php echo urlencode($category); ?>" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </div>
            <div class="page-title">Select Service</div>
        </div>
    </div>

    <div class="step-indicator">
        <div class="steps">
            <div class="step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Category</div>
            </div>
            <div class="step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Subcategory</div>
            </div>
            <div class="step active">
                <div class="step-circle">3</div>
                <div class="step-label">Service</div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="breadcrumb">
            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($category); ?> 
            <i class="fas fa-chevron-right" style="font-size: 10px; margin: 0 5px;"></i> 
            <strong><?php echo htmlspecialchars($subcategory); ?></strong>
        </div>
        
        <div class="section-title">Choose Your Service</div>
        
        <?php if (!empty($services)): ?>
            <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <a href="confirm-booking.php?service_name=<?php echo urlencode($service['name']); ?>&price=<?php echo $service['price']; ?>&duration=<?php echo urlencode($service['duration']); ?>&category=<?php echo urlencode($category); ?>&subcategory=<?php echo urlencode($subcategory); ?>" class="service-card">
                <div class="service-name">
                    <i class="fas fa-check-circle" style="color: #43e97b;"></i>
                    <?php echo htmlspecialchars($service['name']); ?>
                </div>
                <div class="service-desc">
                    <?php echo htmlspecialchars($service['desc']); ?>
                </div>
                <div class="service-meta">
                    <span class="duration-badge">
                        <i class="far fa-clock"></i> <?php echo htmlspecialchars($service['duration']); ?>
                    </span>
                </div>
                <div class="book-btn">
                    <i class="fas fa-calendar-check"></i> Book This Service
                </div>
            </a>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #999;">
                <i class="fas fa-exclamation-circle" style="font-size: 50px; margin-bottom: 15px;"></i>
                <p>No services found in this category.</p>
                <p style="font-size: 12px; margin-top: 10px;">Please run the SQL file to add services.</p>
                <a href="book-service-step2.php?category=<?php echo urlencode($category); ?>" 
                   style="color: #667eea; text-decoration: none; margin-top: 15px; display: inline-block;">
                   <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
