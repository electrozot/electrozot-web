<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$tech_id = $_SESSION['t_id'];

// Get filter and search parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'today';
$custom_date = isset($_GET['custom_date']) ? $_GET['custom_date'] : '';

// Get today's earnings from payment collection
$today_earnings_query = "SELECT SUM(pc.pc_amount) as total 
                         FROM tms_payment_collection pc
                         INNER JOIN tms_service_booking sb ON pc.pc_booking_id = sb.sb_id
                         WHERE sb.sb_technician_id = ? 
                         AND sb.sb_status = 'Completed'
                         AND DATE(pc.pc_collected_at) = CURDATE()";
$stmt = $mysqli->prepare($today_earnings_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$today_earnings = $stmt->get_result()->fetch_object()->total ?? 0;

// Get today's commission to pay Electrozot (20%)
$today = date('Y-m-d');
$commission_rate = 0.20;

$commission_query = "SELECT 
                     COUNT(*) as today_jobs,
                     COALESCE(SUM(COALESCE(sb_bill_amount, sb_final_price, sb_tech_decided_price, sb_total_price, 0)), 0) as today_revenue
                     FROM tms_service_booking
                     WHERE sb_technician_id = ? 
                     AND sb_status = 'Completed' 
                     AND DATE(sb_completed_at) = ?";
$stmt_comm = $mysqli->prepare($commission_query);
$stmt_comm->bind_param('is', $tech_id, $today);
$stmt_comm->execute();
$comm_result = $stmt_comm->get_result();
$comm_data = $comm_result->fetch_object();
$stmt_comm->close();

$today_jobs = $comm_data->today_jobs;
$today_revenue = $comm_data->today_revenue;
$today_commission = round($today_revenue * $commission_rate, 2);

// Build dynamic query based on filter and search
$base_query = "SELECT sb.*, 
                      CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                      u.u_phone as customer_phone,
                      s.s_name as service_name,
                      pc.pc_amount as payment_amount,
                      pc.pc_method as payment_method,
                      pc.pc_collected_at as payment_collected_at,
                      pc.pc_status as payment_status
               FROM tms_service_booking sb
               LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
               LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
               LEFT JOIN tms_payment_collection pc ON sb.sb_id = pc.pc_booking_id
               WHERE sb.sb_technician_id = ? AND sb.sb_status = 'Completed'";

// Add date filter conditions
if($date_filter == 'today') {
    $base_query .= " AND DATE(sb.sb_completed_at) = CURDATE()";
} elseif($date_filter == 'yesterday') {
    $base_query .= " AND DATE(sb.sb_completed_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif($date_filter == 'last7') {
    $base_query .= " AND sb.sb_completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif($date_filter == 'thismonth') {
    $base_query .= " AND MONTH(sb.sb_completed_at) = MONTH(CURDATE()) AND YEAR(sb.sb_completed_at) = YEAR(CURDATE())";
} elseif($date_filter == 'custom' && !empty($custom_date)) {
    $base_query .= " AND DATE(sb.sb_completed_at) = ?";
}

// Add filter conditions
if($filter == 'new') {
    // New: Completed in last 7 days
    $base_query .= " AND sb.sb_completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif($filter == 'pending') {
    // Pending: Payment collected but not verified
    $base_query .= " AND pc.pc_status = 'Collected'";
}

// Add search condition
if(!empty($search)) {
    $base_query .= " AND (sb.sb_id LIKE ? OR CONCAT(u.u_fname, ' ', u.u_lname) LIKE ? OR sb.sb_phone LIKE ? OR s.s_name LIKE ?)";
}

$base_query .= " ORDER BY sb.sb_updated_at DESC";

// Prepare and execute query
$stmt = $mysqli->prepare($base_query);

// Bind parameters based on filters
if($date_filter == 'custom' && !empty($custom_date)) {
    if(!empty($search)) {
        $search_param = "%$search%";
        $stmt->bind_param('isssss', $tech_id, $custom_date, $search_param, $search_param, $search_param, $search_param);
    } else {
        $stmt->bind_param('is', $tech_id, $custom_date);
    }
} else {
    if(!empty($search)) {
        $search_param = "%$search%";
        $stmt->bind_param('issss', $tech_id, $search_param, $search_param, $search_param, $search_param);
    } else {
        $stmt->bind_param('i', $tech_id);
    }
}

$stmt->execute();
$bookings = $stmt->get_result();

// Calculate earnings for selected date filter
$filtered_earnings = 0;
$filtered_jobs = 0;
$filtered_commission = 0;

$earnings_query = "SELECT 
                   COUNT(*) as job_count,
                   COALESCE(SUM(COALESCE(sb_bill_amount, sb_final_price, sb_tech_decided_price, sb_total_price, 0)), 0) as total_revenue
                   FROM tms_service_booking
                   WHERE sb_technician_id = ? AND sb_status = 'Completed'";

if($date_filter == 'today') {
    $earnings_query .= " AND DATE(sb_completed_at) = CURDATE()";
} elseif($date_filter == 'yesterday') {
    $earnings_query .= " AND DATE(sb_completed_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif($date_filter == 'last7') {
    $earnings_query .= " AND sb_completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif($date_filter == 'thismonth') {
    $earnings_query .= " AND MONTH(sb_completed_at) = MONTH(CURDATE()) AND YEAR(sb_completed_at) = YEAR(CURDATE())";
} elseif($date_filter == 'custom' && !empty($custom_date)) {
    $earnings_query .= " AND DATE(sb_completed_at) = ?";
}

$stmt_earn = $mysqli->prepare($earnings_query);
if($date_filter == 'custom' && !empty($custom_date)) {
    $stmt_earn->bind_param('is', $tech_id, $custom_date);
} else {
    $stmt_earn->bind_param('i', $tech_id);
}
$stmt_earn->execute();
$earn_result = $stmt_earn->get_result();
$earn_data = $earn_result->fetch_object();
$stmt_earn->close();

$filtered_jobs = $earn_data->job_count;
$filtered_earnings = $earn_data->total_revenue;
$filtered_commission = round($filtered_earnings * $commission_rate, 2);

// Get date label for display
$date_label = 'All Time';
if($date_filter == 'today') $date_label = "Today's";
elseif($date_filter == 'yesterday') $date_label = "Yesterday's";
elseif($date_filter == 'last7') $date_label = "Last 7 Days";
elseif($date_filter == 'thismonth') $date_label = "This Month's";
elseif($date_filter == 'custom' && !empty($custom_date)) $date_label = date('d M Y', strtotime($custom_date));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Completed Bookings - Electrozot</title>
    
    <!-- PWA Meta Tags for Fullscreen -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Hide browser loading bars in PWA */
        ::-webkit-progress-bar,
        ::-webkit-progress-value {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
        
        /* Hide Android Chrome loading bar */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: transparent !important;
            z-index: 9999;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 80px;
            padding-bottom: 100px;
            position: relative;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        /* Header */
        .header-nav {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 8px 20px;
            padding-top: calc(8px + env(safe-area-inset-top));
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 2px solid rgba(6, 182, 212, 0.3);
            min-height: 70px;
            height: auto;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        
        .logo-image {
            width: 55px;
            height: 55px;
            background: transparent;
            border-radius: 8px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }
        
        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            justify-content: center;
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 900;
            color: white;
            margin: 0;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
            margin: 0;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .notif-icon-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            position: relative;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            text-decoration: none;
        }
        
        .notif-icon-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.4);
            color: white;
        }

        .container {
            padding: 20px;
            padding-bottom: 100px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stats-card {
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            min-height: 80px;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }
        
        .stat-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-content {
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 900;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .stat-label {
            margin: 2px 0 0 0;
            opacity: 0.95;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Commission Widget */
        .commission-widget {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 12px;
            padding: 12px 15px;
            box-shadow: 0 3px 12px rgba(17, 153, 142, 0.25);
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            margin-bottom: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .commission-widget::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        
        .commission-content {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .commission-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }
        
        .commission-details {
            flex: 1;
        }
        
        .commission-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            opacity: 0.9;
            margin-bottom: 3px;
        }
        
        .commission-amount {
            font-size: 1.6rem;
            font-weight: 900;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            line-height: 1;
            margin-bottom: 2px;
        }
        
        .commission-info {
            font-size: 0.7rem;
            opacity: 0.85;
            font-weight: 500;
        }
        
        @media (max-width: 576px) {
            .commission-widget {
                padding: 10px 12px;
            }
            
            .commission-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .commission-amount {
                font-size: 1.4rem;
            }
            
            .commission-label,
            .commission-info {
                font-size: 0.65rem;
            }
        }
        
        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #06b6d4 100%);
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .booking-id {
            font-weight: 800;
            color: #10b981;
            font-size: 1.2rem;
        }
        .price-badge {
            background: linear-gradient(135deg, #00c853 0%, #00F260 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(0, 200, 83, 0.3);
        }
        .customer-info {
            margin: 15px 0;
            line-height: 1.8;
        }
        .customer-info div {
            margin-bottom: 8px;
        }
        .customer-info i {
            color: #10b981;
            width: 25px;
            margin-right: 5px;
        }
        .date-info {
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .date-info i {
            color: #10b981;
            margin-right: 5px;
        }
        
        /* Date Filter Buttons */
        .date-filter-btn {
            padding: 8px 16px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .date-filter-btn:hover {
            text-decoration: none;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            color: #1e293b;
        }
        
        .date-filter-btn.active {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        
        .date-filter-btn i {
            font-size: 0.9rem;
        }
        
        /* Scrollbar for date filters */
        .date-filter-btn::-webkit-scrollbar {
            height: 4px;
        }
        
        .date-filter-btn::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .date-filter-btn::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-nav">
        <a href="dashboard.php" class="logo-section">
            <div class="logo-image">
                <img src="../vendor/EZlogonew.png" alt="EZ">
            </div>
            <div class="brand-info">
                <div class="brand-title">ELECTROZOT</div>
                <div class="brand-subtitle">We Make Perfect</div>
            </div>
        </a>
        <div class="header-actions">
            <a href="notifications.php" class="notif-icon-btn">
                <i class="fas fa-bell"></i>
            </a>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h2 style="margin: 0 0 15px 0; color: #10b981; font-weight: 900; font-size: 1.5rem;">
                <i class="fas fa-check-circle"></i> Completed Bookings
            </h2>
            
            <!-- Search Bar -->
            <form method="GET" action="">
                <div style="position: relative;">
                    <input type="text" 
                           name="search" 
                           id="searchInput"
                           placeholder="Search by ID, customer, phone, service..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           style="width: 100%; padding: 12px 50px 12px 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; outline: none; transition: all 0.3s;">
                    <button type="submit" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 700;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Date Filter Buttons -->
        <div style="background: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="display: flex; gap: 8px; overflow-x: auto; -webkit-overflow-scrolling: touch; padding-bottom: 5px;">
                <a href="?<?php echo !empty($search) ? 'search=' . urlencode($search) : ''; ?>" 
                   class="date-filter-btn <?php echo $date_filter == 'today' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-day"></i> Today
                </a>
                <a href="?date=all<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="date-filter-btn <?php echo $date_filter == 'all' ? 'active' : ''; ?>">
                    <i class="fas fa-infinity"></i> All
                </a>
                <a href="?date=yesterday<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="date-filter-btn <?php echo $date_filter == 'yesterday' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-minus"></i> Yesterday
                </a>
                <a href="?date=last7<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="date-filter-btn <?php echo $date_filter == 'last7' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-week"></i> Last 7 Days
                </a>
                <a href="?date=thismonth<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="date-filter-btn <?php echo $date_filter == 'thismonth' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i> This Month
                </a>
                <button type="button" onclick="document.getElementById('customDatePicker').style.display='block'" 
                        class="date-filter-btn <?php echo $date_filter == 'custom' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i> Custom Date
                </button>
            </div>
            
            <!-- Custom Date Picker -->
            <div id="customDatePicker" style="display: <?php echo $date_filter == 'custom' ? 'block' : 'none'; ?>; margin-top: 15px; padding-top: 15px; border-top: 2px solid #e2e8f0;">
                <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="date" value="custom">
                    <?php if(!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <input type="date" 
                           name="custom_date" 
                           value="<?php echo htmlspecialchars($custom_date); ?>"
                           max="<?php echo date('Y-m-d'); ?>"
                           style="flex: 1; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
                    <button type="submit" style="background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; white-space: nowrap;">
                        <i class="fas fa-check"></i> Apply
                    </button>
                    <button type="button" onclick="document.getElementById('customDatePicker').style.display='none'" style="background: #ef4444; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin-bottom: 20px;">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">₹<?php echo number_format($filtered_earnings, 2); ?></div>
                <div class="stat-label"><?php echo $date_label; ?> Earnings</div>
            </div>
        </div>

        <!-- Commission Widget -->
        <?php if($filtered_commission > 0): ?>
        <div class="commission-widget">
            <div class="commission-content">
                <div class="commission-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="commission-details">
                    <div class="commission-label"><?php echo $date_label; ?> Electrozot Charge</div>
                    <div class="commission-amount">₹<?php echo number_format($filtered_commission, 0); ?></div>
                    <div class="commission-info"><?php echo $filtered_jobs; ?> job<?php echo $filtered_jobs != 1 ? 's' : ''; ?> completed</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bookings List -->
        <div class="bookings-list">
            <?php if($bookings->num_rows > 0): ?>
                <?php while($booking = $bookings->fetch_object()): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div class="booking-id">
                            <i class="fas fa-hashtag"></i> <?php echo $booking->sb_id; ?>
                        </div>
                        <div class="price-badge">
                            ₹<?php echo number_format($booking->payment_amount ?? $booking->sb_total_price ?? 0, 2); ?>
                        </div>
                    </div>
                    
                    <div class="customer-info">
                        <div><i class="fas fa-wrench"></i> <strong><?php echo $booking->service_name; ?></strong></div>
                        <div><i class="fas fa-user"></i> <?php echo $booking->customer_name ?: 'Guest Customer'; ?></div>
                        <div><i class="fas fa-phone"></i> <?php echo $booking->sb_phone; ?></div>
                        <div><i class="fas fa-map-marker-alt"></i> <?php echo $booking->sb_address; ?></div>
                    </div>
                    
                    <div class="date-info">
                        <i class="fas fa-calendar"></i> Booking Date: <?php echo date('d M Y', strtotime($booking->sb_booking_date)); ?>
                        <br>
                        <i class="fas fa-check"></i> Completed: <?php echo date('d M Y h:i A', strtotime($booking->sb_updated_at)); ?>
                        <?php if($booking->payment_amount): ?>
                        <br>
                        <i class="fas fa-credit-card"></i> Payment: 
                        <?php 
                        if($booking->payment_method == 'QR') {
                            echo '<span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 700;">Company QR</span>';
                        } elseif($booking->payment_method == 'TechQR') {
                            echo '<span style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 700;">Tech QR</span>';
                        } elseif($booking->payment_method == 'Cash') {
                            echo '<span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 700;">Cash</span>';
                        }
                        ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="booking-card text-center" style="padding: 60px 20px;">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #cbd5e1; margin: 20px 0; opacity: 0.5;"></i>
                    <h3 style="color: #64748b; font-weight: 700; margin-top: 20px;">No Completed Bookings Yet</h3>
                    <p style="color: #94a3b8;">Your completed bookings will appear here.</p>
                    <a href="dashboard.php" style="display: inline-block; margin-top: 20px; background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 800; box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-home"></i> Go to Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/bottom-nav.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
