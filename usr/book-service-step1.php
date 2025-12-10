<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
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
        
        .step.active .step-label {
            color: #d13abd;
            font-weight: 600;
        }
        
        .content {
            padding: 15px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.1);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border: 1px solid rgba(236, 110, 173, 0.1);
        }
        
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(209, 58, 189, 0.2);
        }
        
        .category-card:active {
            transform: scale(0.98);
        }
        
        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-right: 15px;
        }
        
        .category-info {
            flex: 1;
        }
        
        .category-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        
        .category-desc {
            font-size: 12px;
            color: #999;
        }
        
        .category-arrow {
            color: #ccc;
            font-size: 20px;
        }
        
        .bg-blue { background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 100%); }
        .bg-purple { background: linear-gradient(135deg, #d13abd 0%, #b91c9e 100%); }
        .bg-pink { background: linear-gradient(135deg, #ec6ead 0%, #d13abd 100%); }
        .bg-green { background: linear-gradient(135deg, #f59e9e 0%, #f48fb1 100%); }
        .bg-orange { background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 100%); }
        
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
            
            .category-card {
                padding: 30px;
                margin-bottom: 20px;
            }
            
            .category-icon {
                width: 75px;
                height: 75px;
                font-size: 36px;
            }
            
            .category-name {
                font-size: 20px;
            }
            
            .category-desc {
                font-size: 15px;
            }
        }
        
        @media (min-width: 1024px) {
            body {
                max-width: 1400px;
            }
            
            .content {
                padding: 40px 80px;
            }
            
            .category-card {
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
            <div class="step active">
                <div class="step-circle">1</div>
                <div class="step-label">Category</div>
            </div>
            <div class="step">
                <div class="step-circle">2</div>
                <div class="step-label">Subcategory</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Service</div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="section-title">What service do you need?</div>
        
        <a href="book-service-step2.php?category=<?php echo urlencode('Basic Electrical Work'); ?>" class="category-card">
            <div class="category-icon bg-blue">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="category-info">
                <div class="category-name">Basic Electrical Work</div>
                <div class="category-desc">Wiring, Fixtures, Safety & Power</div>
            </div>
            <i class="fas fa-chevron-right category-arrow"></i>
        </a>

        <a href="book-service-step2.php?category=<?php echo urlencode('Electronic Repair'); ?>" class="category-card">
            <div class="category-icon bg-purple">
                <i class="fas fa-tools"></i>
            </div>
            <div class="category-info">
                <div class="category-name">Electronic Repair</div>
                <div class="category-desc">AC, Fridge, TV, Washing Machine</div>
            </div>
            <i class="fas fa-chevron-right category-arrow"></i>
        </a>

        <a href="book-service-step2.php?category=<?php echo urlencode('Installation & Setup'); ?>" class="category-card">
            <div class="category-icon bg-pink">
                <i class="fas fa-wrench"></i>
            </div>
            <div class="category-info">
                <div class="category-name">Installation & Setup</div>
                <div class="category-desc">TV, CCTV, Appliances, Smart Home</div>
            </div>
            <i class="fas fa-chevron-right category-arrow"></i>
        </a>

        <a href="book-service-step2.php?category=<?php echo urlencode('Servicing & Maintenance'); ?>" class="category-card">
            <div class="category-icon bg-green">
                <i class="fas fa-cog"></i>
            </div>
            <div class="category-info">
                <div class="category-name">Servicing & Maintenance</div>
                <div class="category-desc">AC Service, Cleaning, Tank Cleaning</div>
            </div>
            <i class="fas fa-chevron-right category-arrow"></i>
        </a>

        <a href="book-service-step2.php?category=<?php echo urlencode('Plumbing Work'); ?>" class="category-card">
            <div class="category-icon bg-orange">
                <i class="fas fa-tint"></i>
            </div>
            <div class="category-info">
                <div class="category-name">Plumbing Work</div>
                <div class="category-desc">Taps, Sinks, Toilets, Pipes</div>
            </div>
            <i class="fas fa-chevron-right category-arrow"></i>
        </a>
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
