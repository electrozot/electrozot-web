<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>My Orders - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            padding-bottom: 70px;
            min-height: 100vh;
        }
        
        .top-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 20px 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
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
        
        .header-title {
            text-align: center;
            flex: 1;
        }
        
        .header-title h1 {
            font-size: 20px;
            font-weight: 700;
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
        
        .filter-tabs {
            background: white;
            padding: 15px;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-tabs::-webkit-scrollbar {
            display: none;
        }
        
        .filter-tab {
            padding: 8px 20px;
            border-radius: 20px;
            border: 2px solid #e9ecef;
            background: white;
            color: #666;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s;
        }
        
        .filter-tab.active {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-color: #6366f1;
        }
        
        .orders-container {
            padding: 15px;
        }
        
        .order-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.2);
        }
        
        .order-header {
            padding: 15px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e9ecef;
        }
        
        .order-id {
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }
        
        .order-id i {
            color: #6366f1;
            margin-right: 5px;
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-progress { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .order-body {
            padding: 15px;
        }
        
        .service-info {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
            flex-shrink: 0;
        }
        
        .service-details {
            flex: 1;
        }
        
        .service-name {
            font-size: 17px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .service-category {
            font-size: 13px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .booking-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }
        
        .info-item i {
            width: 20px;
            text-align: center;
            color: #6366f1;
        }
        
        .technician-box {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            padding: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .tech-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 700;
        }
        
        .tech-info {
            flex: 1;
        }
        
        .tech-name {
            font-size: 14px;
            font-weight: 700;
            color: #333;
        }
        
        .tech-phone {
            font-size: 12px;
            color: #666;
        }
        
        .no-tech-alert {
            background: #fff3cd;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .order-footer {
            padding: 15px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #e9ecef;
        }
        
        .order-price {
            font-size: 24px;
            font-weight: 900;
            color: #10b981;
        }
        
        .view-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .order-card:hover .view-btn {
            transform: translateX(3px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-icon {
            font-size: 80px;
            color: #e9ecef;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .empty-text {
            font-size: 15px;
            color: #999;
            margin-bottom: 25px;
        }
        
        .book-now-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .book-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0 8px;
            z-index: 1000;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: #999;
            transition: all 0.3s;
            padding: 5px;
        }
        
        .nav-item.active { color: #667eea; }
        
        .nav-item i {
            font-size: 24px;
            display: block;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 600;
        }
        
        @media (min-width: 768px) {
            .orders-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 25px;
            }
            
            .order-card {
                margin-bottom: 20px;
            }
            
            .service-icon {
                width: 70px;
                height: 70px;
                font-size: 30px;
            }
            
            .service-name {
                font-size: 19px;
            }
            
            .booking-info {
                grid-template-columns: repeat(4, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="top-header">
        <div class="header-content">
            <div class="brand-section">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </div>
            <a href="user-dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="filter-tabs">
        <div class="filter-tab active" data-filter="all">All Orders</div>
        <div class="filter-tab" data-filter="Pending">Pending</div>
        <div class="filter-tab" data-filter="Confirmed">Confirmed</div>
        <div class="filter-tab" data-filter="In Progress">In Progress</div>
        <div class="filter-tab" data-filter="Completed">Completed</div>
        <div class="filter-tab" data-filter="Cancelled">Cancelled</div>
    </div>

    <div class="orders-container">
        <?php
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $bookings_query = "SELECT 
                            sb.*,
                            s.s_name, s.s_category, s.s_price,
                            t.t_name, t.t_phone
                          FROM tms_service_booking sb
                          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                          WHERE sb.sb_user_id = ?
                          ORDER BY sb.sb_id DESC";
        
        $bookings_stmt = $mysqli->prepare($bookings_query);
        
        if (!$bookings_stmt) {
            echo '<div style="background: #fee; padding: 20px; margin: 20px; border-radius: 10px;">';
            echo '<strong>Query Error:</strong> ' . $mysqli->error;
            echo '</div>';
        } else {
            $bookings_stmt->bind_param('i', $aid);
            $bookings_stmt->execute();
            $bookings_result = $bookings_stmt->get_result();
        }
        
        if($bookings_result && $bookings_result->num_rows > 0):
            while($booking = $bookings_result->fetch_object()):
                $status_class = '';
                $status_icon = '';
                switch($booking->sb_status) {
                    case 'Pending':
                        $status_class = 'status-pending';
                        $status_icon = 'clock';
                        break;
                    case 'Confirmed':
                        $status_class = 'status-confirmed';
                        $status_icon = 'check-circle';
                        break;
                    case 'In Progress':
                        $status_class = 'status-progress';
                        $status_icon = 'spinner';
                        break;
                    case 'Completed':
                        $status_class = 'status-completed';
                        $status_icon = 'check-double';
                        break;
                    case 'Cancelled':
                        $status_class = 'status-cancelled';
                        $status_icon = 'times-circle';
                        break;
                    default:
                        $status_class = 'status-pending';
                        $status_icon = 'question';
                }
        ?>
            <a href="user-booking-details.php?booking_id=<?php echo $booking->sb_id; ?>" class="order-card" data-status="<?php echo $booking->sb_status; ?>">
                <div class="order-header">
                    <div class="order-id">
                        <i class="fas fa-receipt"></i> Order #<?php echo $booking->sb_id; ?>
                    </div>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <i class="fas fa-<?php echo $status_icon; ?>"></i>
                        <?php echo $booking->sb_status; ?>
                    </span>
                </div>
                
                <div class="order-body">
                    <div class="service-info">
                        <div class="service-icon">
                            <i class="fas fa-wrench"></i>
                        </div>
                        <div class="service-details">
                            <div class="service-name"><?php echo htmlspecialchars($booking->s_name); ?></div>
                            <div class="service-category">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($booking->s_category); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-info">
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></span>
                        </div>
                    </div>
                    
                    <?php if($booking->t_name): ?>
                        <div class="technician-box">
                            <div class="tech-avatar">
                                <?php echo strtoupper(substr($booking->t_name, 0, 1)); ?>
                            </div>
                            <div class="tech-info">
                                <div class="tech-name"><?php echo htmlspecialchars($booking->t_name); ?></div>
                                <?php if($booking->t_phone): ?>
                                    <div class="tech-phone">
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking->t_phone); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-tech-alert">
                            <i class="fas fa-info-circle"></i>
                            <span>Technician will be assigned soon</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="order-footer">
                    <div class="order-price">₹<?php echo number_format($booking->sb_total_price, 2); ?></div>
                    <div class="view-btn">
                        View Details
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        <?php 
            endwhile;
        else:
        ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="empty-title">No Orders Yet</div>
                <div class="empty-text">You haven't placed any orders yet.<br>Start booking our services now!</div>
                <a href="book-service-step1.php" class="book-now-btn">
                    <i class="fas fa-plus-circle"></i>
                    Book a Service
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service-step1.php" class="nav-item">
            <i class="fas fa-calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="user-view-booking.php" class="nav-item active">
            <i class="fas fa-list-alt"></i>
            <span>Orders</span>
        </a>
        <a href="user-view-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>

    <script>
        // Filter functionality
        const filterTabs = document.querySelectorAll('.filter-tab');
        const orderCards = document.querySelectorAll('.order-card');
        
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                filterTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const filter = tab.getAttribute('data-filter');
                
                // Filter orders
                orderCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-status') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
