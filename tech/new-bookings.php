<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$page_title = "New Bookings";

// Get new/pending bookings (not completed or cancelled)
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, s.s_name, s.s_price, s.s_category
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_technician_id = ? 
          AND sb.sb_status NOT IN ('Completed', 'Cancelled')
          ORDER BY sb.sb_booking_date ASC, sb.sb_booking_time ASC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <div class="page-header" style="background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%); color: white; border-left: none;">
            <h2 style="color: white;">
                <i class="fas fa-bell"></i>
                New & Pending Bookings
            </h2>
            <p style="color: rgba(255,255,255,0.95);">Bookings that need your attention</p>
        </div>

        <?php if($result->num_rows > 0): ?>
            <div class="row">
                <?php while($booking = $result->fetch_object()): 
                    $status_class = '';
                    $status_color = '';
                    if($booking->sb_status == 'Pending') {
                        $status_class = 'badge-pending';
                        $status_color = '#ffa502';
                    } elseif($booking->sb_status == 'In Progress') {
                        $status_class = 'badge-progress';
                        $status_color = '#00b4db';
                    }
                ?>
                <div class="col-lg-6 mb-4">
                    <div class="booking-card-new">
                        <div class="booking-card-header" style="background: linear-gradient(135deg, <?php echo $status_color; ?> 0%, <?php echo $status_color; ?>dd 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="booking-id">#<?php echo $booking->sb_id; ?></span>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $booking->sb_status; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="booking-card-body">
                            <div class="customer-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="customer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking->u_email); ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="service-info mb-3">
                                <div class="info-row">
                                    <i class="fas fa-tools"></i>
                                    <span><strong>Service:</strong> <?php echo htmlspecialchars($booking->s_name); ?></span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-tag"></i>
                                    <span><strong>Category:</strong> <?php echo htmlspecialchars($booking->s_category); ?></span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span><strong>Price:</strong> $<?php echo number_format($booking->sb_total_price, 2); ?></span>
                                </div>
                            </div>

                            <div class="booking-schedule mb-3">
                                <div class="schedule-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('l, F d, Y', strtotime($booking->sb_booking_date)); ?></span>
                                </div>
                                <div class="schedule-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></span>
                                </div>
                            </div>

                            <div class="booking-address mb-3">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($booking->sb_address); ?></span>
                            </div>

                            <div class="booking-actions">
                                <a href="tel:<?php echo $booking->u_phone; ?>" class="btn btn-success-custom btn-sm">
                                    <i class="fas fa-phone"></i> Call Customer
                                </a>
                                <a href="complete-service.php?id=<?php echo $booking->sb_id; ?>" class="btn btn-primary-custom btn-sm">
                                    <i class="fas fa-check-circle"></i> Mark as Done
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card-custom text-center" style="padding: 80px 30px;">
                <i class="fas fa-check-circle" style="font-size: 5rem; color: #38ef7d; margin-bottom: 20px;"></i>
                <h3 style="color: #2d3748; font-weight: 700;">All Caught Up!</h3>
                <p style="color: #6c757d; font-size: 1.1rem;">You don't have any pending bookings at the moment.</p>
                <a href="my-bookings.php" class="btn btn-primary-custom mt-3">
                    <i class="fas fa-clipboard-list"></i> View All Bookings
                </a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .booking-card-new {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .booking-card-new:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .booking-card-header {
            padding: 20px;
            color: white;
        }

        .booking-id {
            font-size: 1.2rem;
            font-weight: 800;
        }

        .booking-card-body {
            padding: 25px;
        }

        .customer-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.5rem;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            color: #4a5568;
        }

        .info-row i {
            color: #ffa502;
            width: 20px;
        }

        .booking-schedule {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-around;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .schedule-item i {
            color: #ff4757;
        }

        .booking-address {
            display: flex;
            align-items: start;
            gap: 10px;
            padding: 15px;
            background: #fff5f0;
            border-radius: 10px;
            border-left: 4px solid #ffa502;
        }

        .booking-address i {
            color: #ff4757;
            margin-top: 3px;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .booking-actions .btn {
            flex: 1;
            min-width: 150px;
        }

        .badge-progress {
            background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
        }
    </style>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
