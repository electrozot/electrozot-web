<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user bookings with tracking info
$query = "SELECT 
            sb.*,
            s.s_name, s.s_category, s.s_price,
            t.t_name, t.t_phone, t.t_id_no
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          WHERE sb.sb_user_id = ?
          ORDER BY sb.sb_created_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $aid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="user-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Track Orders</li>
                </ol>

                <div class="card shadow-lg mb-4" style="border: none; border-radius: 15px;">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-map-marker-alt"></i> Track Your Orders
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if($result->num_rows > 0): ?>
                            <?php while($booking = $result->fetch_object()): ?>
                                <div class="card mb-4 shadow" style="border: none; border-radius: 15px; overflow: hidden;">
                                    <!-- Booking Header -->
                                    <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-receipt"></i> Booking #<?php echo $booking->sb_id; ?>
                                                </h5>
                                                <small><?php echo $booking->s_name; ?> - <?php echo $booking->s_category; ?></small>
                                            </div>
                                            <div class="col-md-6 text-md-right">
                                                <?php
                                                $status_color = '';
                                                $status_icon = '';
                                                switch($booking->sb_status) {
                                                    case 'Pending':
                                                        $status_color = 'warning';
                                                        $status_icon = 'clock';
                                                        break;
                                                    case 'Confirmed':
                                                        $status_color = 'info';
                                                        $status_icon = 'check-circle';
                                                        break;
                                                    case 'In Progress':
                                                        $status_color = 'primary';
                                                        $status_icon = 'spinner';
                                                        break;
                                                    case 'Completed':
                                                        $status_color = 'success';
                                                        $status_icon = 'check-double';
                                                        break;
                                                    case 'Cancelled':
                                                        $status_color = 'danger';
                                                        $status_icon = 'times-circle';
                                                        break;
                                                    default:
                                                        $status_color = 'secondary';
                                                        $status_icon = 'question';
                                                }
                                                ?>
                                                <span class="badge badge-<?php echo $status_color; ?> p-3" style="font-size: 1rem;">
                                                    <i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo $booking->sb_status; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <!-- Tracking Timeline -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6 class="font-weight-bold mb-3">
                                                    <i class="fas fa-route"></i> Order Progress
                                                </h6>
                                                <div class="tracking-timeline">
                                                    <!-- Pending -->
                                                    <div class="tracking-step <?php echo in_array($booking->sb_status, ['Pending', 'Confirmed', 'In Progress', 'Completed']) ? 'completed' : ''; ?>">
                                                        <div class="tracking-icon">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </div>
                                                        <div class="tracking-content">
                                                            <h6>Order Placed</h6>
                                                            <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($booking->sb_created_at)); ?></small>
                                                        </div>
                                                    </div>

                                                    <!-- Confirmed -->
                                                    <div class="tracking-step <?php echo in_array($booking->sb_status, ['Confirmed', 'In Progress', 'Completed']) ? 'completed' : ''; ?>">
                                                        <div class="tracking-icon">
                                                            <i class="fas fa-check-circle"></i>
                                                        </div>
                                                        <div class="tracking-content">
                                                            <h6>Order Confirmed</h6>
                                                            <small class="text-muted">
                                                                <?php echo $booking->sb_status == 'Pending' ? 'Waiting for confirmation' : 'Confirmed'; ?>
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- In Progress -->
                                                    <div class="tracking-step <?php echo in_array($booking->sb_status, ['In Progress', 'Completed']) ? 'completed' : ''; ?>">
                                                        <div class="tracking-icon">
                                                            <i class="fas fa-tools"></i>
                                                        </div>
                                                        <div class="tracking-content">
                                                            <h6>Service In Progress</h6>
                                                            <small class="text-muted">
                                                                <?php echo $booking->sb_status == 'In Progress' ? 'Technician is working' : 'Not started yet'; ?>
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- Completed -->
                                                    <div class="tracking-step <?php echo $booking->sb_status == 'Completed' ? 'completed' : ''; ?>">
                                                        <div class="tracking-icon">
                                                            <i class="fas fa-check-double"></i>
                                                        </div>
                                                        <div class="tracking-content">
                                                            <h6>Service Completed</h6>
                                                            <small class="text-muted">
                                                                <?php echo $booking->sb_status == 'Completed' ? 'Service finished' : 'Pending completion'; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Booking Details -->
                                            <div class="col-md-4">
                                                <div class="card" style="background: #f8f9fa; border: none;">
                                                    <div class="card-body">
                                                        <h6 class="font-weight-bold mb-3">
                                                            <i class="fas fa-info-circle"></i> Details
                                                        </h6>
                                                        <p class="mb-2">
                                                            <strong>Date:</strong><br>
                                                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?>
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Time:</strong><br>
                                                            <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Price:</strong><br>
                                                            <i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($booking->sb_total_price, 2); ?>
                                                        </p>
                                                        <?php if($booking->t_name): ?>
                                                            <hr>
                                                            <p class="mb-2">
                                                                <strong>Technician:</strong><br>
                                                                <i class="fas fa-user-cog"></i> <?php echo $booking->t_name; ?>
                                                            </p>
                                                            <p class="mb-0">
                                                                <strong>Contact:</strong><br>
                                                                <i class="fas fa-phone"></i> <?php echo $booking->t_phone; ?>
                                                            </p>
                                                        <?php else: ?>
                                                            <div class="alert alert-warning mt-2 mb-0">
                                                                <small><i class="fas fa-exclamation-triangle"></i> Technician not assigned yet</small>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h4>No Orders Yet</h4>
                                <p class="text-muted">You haven't placed any orders yet.</p>
                                <a href="usr-book-service.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus-circle"></i> Book a Service
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <style>
        /* Tracking Timeline Styles */
        .tracking-timeline {
            position: relative;
            padding-left: 50px;
        }

        .tracking-step {
            position: relative;
            padding-bottom: 30px;
        }

        .tracking-step:last-child {
            padding-bottom: 0;
        }

        .tracking-step::before {
            content: '';
            position: absolute;
            left: -35px;
            top: 30px;
            width: 2px;
            height: calc(100% - 10px);
            background: #e9ecef;
        }

        .tracking-step.completed::before {
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }

        .tracking-step:last-child::before {
            display: none;
        }

        .tracking-icon {
            position: absolute;
            left: -50px;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .tracking-step.completed .tracking-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .tracking-content h6 {
            margin-bottom: 5px;
            font-weight: 700;
            color: #495057;
        }

        .tracking-step.completed .tracking-content h6 {
            color: #667eea;
        }
    </style>
</body>
</html>
