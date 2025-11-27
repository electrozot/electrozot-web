<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$tech_id = $_SESSION['t_id'];

// Get completed bookings stats
$total_completed_query = "SELECT COUNT(*) as count FROM tms_service_booking 
                          WHERE sb_technician_id = ? AND sb_status = 'Completed'";
$stmt = $mysqli->prepare($total_completed_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$total_completed = $stmt->get_result()->fetch_object()->count;

// Get this month's earnings
$month_earnings_query = "SELECT SUM(sb_total_price) as total FROM tms_service_booking 
                         WHERE sb_technician_id = ? 
                         AND sb_status = 'Completed'
                         AND MONTH(sb_updated_at) = MONTH(CURRENT_DATE())
                         AND YEAR(sb_updated_at) = YEAR(CURRENT_DATE())";
$stmt = $mysqli->prepare($month_earnings_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$month_earnings = $stmt->get_result()->fetch_object()->total ?? 0;

// Get all completed bookings
$bookings_query = "SELECT sb.*, 
                          CONCAT(u.u_fname, ' ', u.u_lname) as customer_name,
                          u.u_phone as customer_phone,
                          s.s_name as service_name
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_technician_id = ? AND sb.sb_status = 'Completed'
                   ORDER BY sb.sb_updated_at DESC";
$stmt = $mysqli->prepare($bookings_query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Bookings - Electrozot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
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
        }

        /* Header */
        .header-nav {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 8px 20px;
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
            height: 70px;
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
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
        }
        .stats-card h3 {
            font-size: 2rem;
            font-weight: 900;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.95;
            font-weight: 600;
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
            <h2 style="margin: 0; color: #10b981; font-weight: 900;">
                <i class="fas fa-check-circle"></i> Completed Bookings
            </h2>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="stats-card">
                    <i class="fas fa-clipboard-check" style="font-size: 2rem; opacity: 0.3; float: right;"></i>
                    <h3><?php echo $total_completed; ?></h3>
                    <p>Total Completed Bookings</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #00c853 0%, #00F260 100%);">
                    <i class="fas fa-rupee-sign" style="font-size: 2rem; opacity: 0.3; float: right;"></i>
                    <h3>₹<?php echo number_format($month_earnings, 2); ?></h3>
                    <p>This Month's Earnings</p>
                </div>
            </div>
        </div>

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
                            ₹<?php echo number_format($booking->sb_total_price, 2); ?>
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
