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
                    if($booking->sb_status == 'Pending') {
                        $status_class = 'badge-pending';
                    } elseif($booking->sb_status == 'In Progress') {
                        $status_class = 'badge-progress';
                    } elseif($booking->sb_status == 'Completed') {
                        $status_class = 'badge-completed';
                    }
                ?>
                <div class="col-md-6 mb-3">
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">#<?php echo $booking->sb_id; ?></span>
                            <span class="badge-status <?php echo $status_class; ?>"><?php echo $booking->sb_status; ?></span>
                        </div>
                        <div class="order-card-body">
                            <div class="order-item">
                                <div class="order-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="order-content">
                                    <label>CUSTOMER NAME</label>
                                    <p><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></p>
                                </div>
                            </div>
                            
                            <div class="order-item">
                                <div class="order-icon">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <div class="order-content">
                                    <label>PINCODE</label>
                                    <p><?php echo htmlspecialchars($booking->u_pincode ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            
                            <div class="order-item">
                                <div class="order-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="order-content">
                                    <label>ADDRESS</label>
                                    <p><?php echo htmlspecialchars($booking->sb_address); ?></p>
                                </div>
                            </div>
                            
                            <div class="order-item">
                                <div class="order-icon">
                                    <i class="fas fa-wrench"></i>
                                </div>
                                <div class="order-content">
                                    <label>SERVICE</label>
                                    <p><?php echo htmlspecialchars($booking->s_name); ?></p>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="tel:<?php echo $booking->u_phone; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-phone"></i> Call
                                </a>
                                <a href="booking-details.php?id=<?php echo $booking->sb_id; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> View Details
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
                <a href="dashboard.php" class="btn btn-primary-custom mt-3">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .order-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .order-card-body {
            padding: 20px;
        }

        .order-field {
            margin-bottom: 15px;
        }

        .order-field label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        .order-field p {
            font-size: 0.9rem;
            color: #2d3748;
            margin: 0;
            line-height: 1.4;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .order-actions .btn {
            flex: 1;
            font-size: 0.85rem;
            padding: 8px 12px;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 4px 10px;
        }

        .badge-progress {
            background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
        }
    </style>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
