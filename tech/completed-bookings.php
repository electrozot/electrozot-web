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
    <title>Completed Bookings - Technician Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            padding: 20px;
            padding-bottom: 100px;
        }
        .header {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .booking-id {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1rem;
        }
        .price-badge {
            background: #2ecc71;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .customer-info {
            margin: 10px 0;
        }
        .customer-info i {
            color: #667eea;
            width: 20px;
        }
        .date-info {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; color: #667eea;">
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
                <div class="stats-card" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
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
                <div class="booking-card text-center">
                    <i class="fas fa-inbox" style="font-size: 3rem; color: #bdc3c7; margin: 20px 0;"></i>
                    <p style="color: #7f8c8d;">No completed bookings yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/bottom-nav.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
