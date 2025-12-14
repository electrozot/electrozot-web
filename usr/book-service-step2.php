<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

$category = isset($_GET['category']) ? $_GET['category'] : '';
if (empty($category)) {
    header("Location: book-service-step1.php");
    exit();
}

// Define subcategories (works without SQL file)
$subcategories_map = [
    'Basic Electrical Work' => [
        ['name' => 'Wiring & Fixtures', 'icon' => 'fa-lightbulb'],
        ['name' => 'Safety & Power', 'icon' => 'fa-shield-alt']
    ],
    'Electronic Repair' => [
        ['name' => 'Major Appliances', 'icon' => 'fa-blender'],
        ['name' => 'Other Gadgets', 'icon' => 'fa-tv']
    ],
    'Installation & Setup' => [
        ['name' => 'Appliance Setup', 'icon' => 'fa-plug'],
        ['name' => 'Tech & Security', 'icon' => 'fa-video']
    ],
    'Servicing & Maintenance' => [
        ['name' => 'Routine Care', 'icon' => 'fa-broom']
    ],
    'Plumbing Work' => [
        ['name' => 'Fixtures & Taps', 'icon' => 'fa-tint']
    ]
];

// Check what category we received
$subcategories = [];
if (isset($subcategories_map[$category])) {
    $subcategories = $subcategories_map[$category];
} else {
    // Try to find a match (case-insensitive)
    foreach ($subcategories_map as $key => $value) {
        if (strcasecmp($key, $category) == 0) {
            $subcategories = $value;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">
    <title>Select Type - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="vendor/inc/navbar-styles.css?v=<?php echo time(); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 75px;
            padding-bottom: 70px;
            min-height: 100vh;
        }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #d13abd 0%, #ec6ead 20%, #f48fb1 50%, #f59e9e 80%, #f9a8a8 100%);
            color: white;
            padding: 10px 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.3);
            z-index: 1000;
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
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
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
        
        .category-badge {
            background: white;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            font-size: 14px;
            color: #667eea;
            font-weight: 600;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .subcategory-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 10px rgba(209, 58, 189, 0.08);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border: 1px solid rgba(236, 110, 173, 0.1);
        }
        
        .subcategory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.2);
            border-color: rgba(236, 110, 173, 0.3);
        }
        
        .subcategory-card:active {
            transform: scale(0.98);
        }
        
        .subcategory-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            margin-right: 15px;
        }
        
        .subcategory-info {
            flex: 1;
        }
        
        .subcategory-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .subcategory-arrow {
            color: #ccc;
            font-size: 20px;
        }
        

        
        @media (min-width: 768px) {
            body {
                max-width: 1200px;
                margin: 0 auto;
                box-shadow: 0 0 40px rgba(0,0,0,0.15);
            }
            
            .header {
                border-radius: 0;
            }
            
            .content {
                padding: 30px 50px;
            }
            
            .subcategory-card {
                padding: 30px;
                margin-bottom: 20px;
            }
            
            .subcategory-icon {
                width: 65px;
                height: 65px;
                font-size: 28px;
            }
            
            .subcategory-name {
                font-size: 20px;
            }
        }
        
        @media (min-width: 1024px) {
            body {
                max-width: 1400px;
            }
            
            .content {
                padding: 40px 80px;
            }
            
            .subcategory-card {
                padding: 35px;
            }
        }
        
        @media (min-width: 1440px) {
            body {
                max-width: 1600px;
            }
            
            .content {
                padding: 50px 100px;
            }
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/navbar.php'); ?>

    <div class="step-indicator">
        <div class="steps">
            <div class="step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Category</div>
            </div>
            <div class="step active">
                <div class="step-circle">2</div>
                <div class="step-label">Type</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Service</div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="category-badge">
            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($category); ?>
        </div>
        

        
        <div class="section-title">Choose service type</div>
        
        <?php if (!empty($subcategories)): ?>
            <?php foreach ($subcategories as $sub): ?>
            <a href="book-service-step3.php?category=<?php echo urlencode($category); ?>&subcategory=<?php echo urlencode($sub['name']); ?>" class="subcategory-card">
                <div class="subcategory-icon">
                    <i class="fas <?php echo $sub['icon']; ?>"></i>
                </div>
                <div class="subcategory-info">
                    <div class="subcategory-name"><?php echo htmlspecialchars($sub['name']); ?></div>
                </div>
                <i class="fas fa-chevron-right subcategory-arrow"></i>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #999;">
                <i class="fas fa-exclamation-circle" style="font-size: 50px; margin-bottom: 15px;"></i>
                <p>No types found for this category.</p>
                <a href="book-service-step1.php" style="color: #667eea; text-decoration: none; margin-top: 10px; display: inline-block;">Go Back</a>
            </div>
        <?php endif; ?>
    </div>



    <?php include('vendor/inc/bottom-nav.php'); ?>
</body>
</html>
