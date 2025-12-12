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

// Load services from database based on subcategory
// Check if s_subcategory column exists, if not use s_category
$check_column = $mysqli->query("SHOW COLUMNS FROM tms_service LIKE 's_subcategory'");
$has_subcategory_column = ($check_column->num_rows > 0);

$services = [];

if ($has_subcategory_column) {
    // Use subcategory column if it exists
    $query = "SELECT s_id, s_name, s_description, s_price, s_duration, s_category 
              FROM tms_service 
              WHERE s_subcategory = ? AND s_status = 'Active' 
              ORDER BY s_name ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $subcategory);
} else {
    // Fallback: Load all services from the main category
    $query = "SELECT s_id, s_name, s_description, s_price, s_duration, s_category 
              FROM tms_service 
              WHERE s_category = ? AND s_status = 'Active' 
              ORDER BY s_name ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $services[] = [
        'id' => $row['s_id'],
        'name' => $row['s_name'],
        'desc' => $row['s_description'],
        'price' => $row['s_price'],
        'duration' => $row['s_duration']
    ];
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">
    <title>Select Service - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 75px;
            padding-bottom: 80px;
            min-height: 100vh;
        }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 10px 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.3);
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-left: 0;
            margin-left: -5px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .logo {
            height: 29px;
            width: auto;
        }
        
        .brand-text h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 13px;
            opacity: 0.85;
            margin: 3px 0 0 0;
            font-style: italic;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }
        
        .header-icons {
            display: flex;
            gap: 6px;
        }
        
        .header-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 14px;
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
            padding-bottom: 25px;
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
            color: #d13abd;
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
            box-shadow: 0 2px 10px rgba(209, 58, 189, 0.08);
            text-decoration: none;
            display: block;
            transition: all 0.3s;
            border: 1px solid rgba(236, 110, 173, 0.1);
            position: relative;
        }
        
        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.2);
            border-color: rgba(236, 110, 173, 0.3);
        }
        
        .service-card:last-child {
            margin-bottom: 20px;
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
            margin-bottom: 45px;
        }
        
        .duration-badge {
            background: #f0f0f0;
            color: #666;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .book-btn {
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            position: absolute;
            bottom: 15px;
            right: 15px;
            white-space: nowrap;
            z-index: 10;
            pointer-events: auto;
            box-shadow: 0 2px 8px rgba(209, 58, 189, 0.3);
            transition: all 0.3s ease;
        }
        
        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(209, 58, 189, 0.4);
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 16px);
            max-width: 450px;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            box-shadow: 0 3px 20px rgba(209, 58, 189, 0.35), 0 1px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 4px 6px;
            z-index: 1000;
            border-radius: 20px;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 4px 2px;
            position: relative;
            border-radius: 12px;
        }
        
        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }
        
        .nav-item.active { 
            color: white;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
        }
        
        .nav-item i {
            font-size: 16px;
            display: block;
            margin-bottom: 1px;
        }
        
        .nav-item.active i {
            animation: bounce 0.4s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        
        .nav-item span {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }
        
        @media (min-width: 768px) {
            .bottom-nav {
                max-width: 400px;
                bottom: 10px;
                padding: 5px 8px;
            }
            
            .nav-item {
                padding: 5px 4px;
            }
            
            .nav-item i {
                font-size: 18px;
                margin-bottom: 2px;
            }
            
            .nav-item span {
                font-size: 9px;
            }
        }
        
        .nav-item i {
            font-size: 20px;
            display: block;
            margin-bottom: 3px;
        }
        
        .nav-item span {
            font-size: 10px;
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
                height: 29px;
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
            <a href="../index.php" class="brand-section" style="text-decoration: none; color: white;">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </a>
            <div class="user-section">
                <div class="header-icons">
                    <a href="user-view-profile.php" class="header-icon">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
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
            <a href="confirm-booking.php?service_id=<?php echo $service['id']; ?>&service_name=<?php echo urlencode($service['name']); ?>&duration=<?php echo urlencode($service['duration']); ?>&category=<?php echo urlencode($category); ?>&subcategory=<?php echo urlencode($subcategory); ?>" class="service-card">
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
                <i class="fas fa-exclamation-circle" style="font-size: 50px; margin-bottom: 15px; color: #f59e0b;"></i>
                <p style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px;">No Services Available</p>
                <p style="font-size: 14px; margin-bottom: 20px;">No services have been added to this category yet.</p>
                <p style="font-size: 13px; color: #666; margin-bottom: 25px;">
                    <i class="fas fa-info-circle"></i> Admin needs to add services for <strong><?php echo htmlspecialchars($subcategory); ?></strong>
                </p>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="book-service-step2.php?category=<?php echo urlencode($category); ?>" 
                       style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
                       <i class="fas fa-arrow-left"></i> Choose Different Type
                    </a>
                    <a href="book-custom-service.php" 
                       style="background: white; color: #6366f1; border: 2px solid #6366f1; padding: 12px 25px; border-radius: 25px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
                       <i class="fas fa-plus-circle"></i> Request Custom Service
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service-step1.php" class="nav-item active">
            <i class="fas fa-calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item">
            <i class="fas fa-list-alt"></i>
            <span>Orders</span>
        </a>
        <a href="user-view-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <a href="../index.php" class="nav-item">
            <i class="fas fa-store"></i>
            <span>Main</span>
        </a>
    </div>
</body>
</html>
