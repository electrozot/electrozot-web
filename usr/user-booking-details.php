<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Get booking details
$query = "SELECT 
            sb.*,
            s.s_name, s.s_category, s.s_price, s.s_description, s.s_duration,
            t.t_name, t.t_phone, t.t_email, t.t_id_no, t.t_specialization,
            u.u_fname, u.u_lname, u.u_phone as user_phone, u.u_email as user_email
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          WHERE sb.sb_id = ? AND sb.sb_user_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $aid);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if(!$booking) {
    header("Location: user-view-booking.php");
    exit();
}
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
                    <li class="breadcrumb-item">
                        <a href="user-view-booking.php">My Bookings</a>
                    </li>
                    <li class="breadcrumb-item active">Booking #<?php echo $booking->sb_id; ?></li>
                </ol>

                <!-- Booking Header -->
                <div class="card shadow-lg mb-4" style="border: none; border-radius: 15px;">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4 class="m-0 text-white font-weight-bold">
                                    <i class="fas fa-receipt"></i> Booking #<?php echo $booking->sb_id; ?>
                                </h4>
                                <small class="text-white">Created: <?php echo date('M d, Y h:i A', strtotime($booking->sb_created_at)); ?></small>
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
                                <span class="badge badge-<?php echo $status_color; ?> p-3" style="font-size: 1.2rem;">
                                    <i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo $booking->sb_status; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Service Details -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg h-100" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-wrench"></i> Service Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <h5 class="font-weight-bold text-primary"><?php echo $booking->s_name; ?></h5>
                                <p class="mb-3">
                                    <span class="badge badge-secondary"><?php echo $booking->s_category; ?></span>
                                </p>
                                
                                <div class="detail-row">
                                    <i class="fas fa-rupee-sign text-success"></i>
                                    <strong>Price:</strong> ₹<?php echo number_format($booking->sb_total_price, 2); ?>
                                </div>
                                
                                <div class="detail-row">
                                    <i class="fas fa-clock text-warning"></i>
                                    <strong>Duration:</strong> <?php echo $booking->s_duration; ?>
                                </div>
                                
                                <?php if($booking->s_description): ?>
                                    <hr>
                                    <p class="text-muted small mb-0">
                                        <strong>Description:</strong><br>
                                        <?php echo $booking->s_description; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Schedule -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg h-100" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-info text-white" style="border-radius: 15px 15px 0 0;">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-calendar-alt"></i> Schedule & Location
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="detail-row">
                                    <i class="fas fa-calendar text-primary"></i>
                                    <strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($booking->sb_booking_date)); ?>
                                </div>
                                
                                <div class="detail-row">
                                    <i class="fas fa-clock text-warning"></i>
                                    <strong>Time:</strong> <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                                </div>
                                
                                <hr>
                                
                                <div class="detail-row">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <strong>Address:</strong>
                                </div>
                                <p class="ml-4 text-muted"><?php echo $booking->sb_address; ?></p>
                                
                                <div class="detail-row">
                                    <i class="fas fa-phone text-success"></i>
                                    <strong>Contact:</strong> <?php echo $booking->sb_phone; ?>
                                </div>
                                
                                <?php if($booking->sb_description): ?>
                                    <hr>
                                    <div class="detail-row">
                                        <i class="fas fa-comment text-info"></i>
                                        <strong>Notes:</strong>
                                    </div>
                                    <p class="ml-4 text-muted small"><?php echo $booking->sb_description; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Technician Details -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg h-100" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-user-cog"></i> Technician Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if($booking->t_name): ?>
                                    <h5 class="font-weight-bold text-success"><?php echo $booking->t_name; ?></h5>
                                    
                                    <div class="detail-row">
                                        <i class="fas fa-id-card text-primary"></i>
                                        <strong>ID:</strong> <?php echo $booking->t_id_no; ?>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <i class="fas fa-star text-warning"></i>
                                        <strong>Specialization:</strong> <?php echo $booking->t_specialization; ?>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="detail-row">
                                        <i class="fas fa-phone text-success"></i>
                                        <strong>Phone:</strong> 
                                        <a href="tel:<?php echo $booking->t_phone; ?>"><?php echo $booking->t_phone; ?></a>
                                    </div>
                                    
                                    <?php if($booking->t_email): ?>
                                        <div class="detail-row">
                                            <i class="fas fa-envelope text-info"></i>
                                            <strong>Email:</strong> 
                                            <a href="mailto:<?php echo $booking->t_email; ?>"><?php echo $booking->t_email; ?></a>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>Technician Not Assigned Yet</strong>
                                        <p class="mb-0 mt-2 small">We'll assign a technician soon and notify you.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg h-100" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-warning text-white" style="border-radius: 15px 15px 0 0;">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-user"></i> Customer Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <h5 class="font-weight-bold"><?php echo $booking->u_fname . ' ' . $booking->u_lname; ?></h5>
                                
                                <div class="detail-row">
                                    <i class="fas fa-phone text-success"></i>
                                    <strong>Phone:</strong> <?php echo $booking->user_phone; ?>
                                </div>
                                
                                <div class="detail-row">
                                    <i class="fas fa-envelope text-info"></i>
                                    <strong>Email:</strong> <?php echo $booking->user_email; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Completion Images (Only for Completed Bookings) -->
                <?php if($booking->sb_status == 'Completed' && (!empty($booking->sb_completion_img) || !empty($booking->sb_bill_img) || !empty($booking->sb_service_image) || !empty($booking->sb_bill_image))): ?>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card shadow-lg" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-check-circle"></i> Service Completion Documents
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Service Completion Image -->
                                    <?php if(!empty($booking->sb_completion_img) || !empty($booking->sb_service_image)): ?>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="font-weight-bold text-success">
                                                <i class="fas fa-camera"></i> Service Completion Image
                                            </h6>
                                            <?php 
                                            $completion_img = !empty($booking->sb_completion_img) ? $booking->sb_completion_img : $booking->sb_service_image;
                                            ?>
                                            <div class="text-center">
                                                <a href="../vendor/img/completions/<?php echo $completion_img; ?>" target="_blank">
                                                    <img src="../vendor/img/completions/<?php echo $completion_img; ?>" 
                                                         alt="Service Completion" 
                                                         class="img-fluid rounded shadow" 
                                                         style="max-height: 300px; cursor: pointer; border: 3px solid #28a745;">
                                                </a>
                                                <div class="mt-2">
                                                    <a href="../vendor/img/completions/<?php echo $completion_img; ?>" 
                                                       target="_blank" 
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-eye"></i> View Full Size
                                                    </a>
                                                    <a href="../vendor/img/completions/<?php echo $completion_img; ?>" 
                                                       download 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Bill Image -->
                                    <?php if(!empty($booking->sb_bill_img) || !empty($booking->sb_bill_image)): ?>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="font-weight-bold text-info">
                                                <i class="fas fa-file-invoice-dollar"></i> Service Bill
                                            </h6>
                                            <?php 
                                            $bill_img = !empty($booking->sb_bill_img) ? $booking->sb_bill_img : $booking->sb_bill_image;
                                            ?>
                                            <div class="text-center">
                                                <a href="../vendor/img/bills/<?php echo $bill_img; ?>" target="_blank">
                                                    <img src="../vendor/img/bills/<?php echo $bill_img; ?>" 
                                                         alt="Service Bill" 
                                                         class="img-fluid rounded shadow" 
                                                         style="max-height: 300px; cursor: pointer; border: 3px solid #17a2b8;">
                                                </a>
                                                <div class="mt-2">
                                                    <a href="../vendor/img/bills/<?php echo $bill_img; ?>" 
                                                       target="_blank" 
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View Full Size
                                                    </a>
                                                    <a href="../vendor/img/bills/<?php echo $bill_img; ?>" 
                                                       download 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if(!empty($booking->sb_final_price) || !empty($booking->sb_charged_price)): ?>
                                    <hr>
                                    <div class="alert alert-success mb-0">
                                        <h5 class="font-weight-bold mb-0">
                                            <i class="fas fa-rupee-sign"></i> Final Amount Charged: 
                                            ₹<?php echo number_format(!empty($booking->sb_final_price) ? $booking->sb_final_price : $booking->sb_charged_price, 2); ?>
                                        </h5>
                                    </div>
                                <?php endif; ?>

                                <?php if(!empty($booking->sb_completion_notes)): ?>
                                    <hr>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-comment"></i> Technician Notes:</strong>
                                        <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($booking->sb_completion_notes)); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-lg" style="border: none; border-radius: 15px;">
                            <div class="card-body text-center p-4">
                                <a href="user-track-booking.php" class="btn btn-primary btn-lg mx-2">
                                    <i class="fas fa-map-marker-alt"></i> Track Order
                                </a>
                                <a href="user-view-booking.php" class="btn btn-info btn-lg mx-2">
                                    <i class="fas fa-list"></i> All Bookings
                                </a>
                                <?php if($booking->sb_status == 'Pending' && empty($booking->sb_technician_id)): ?>
                                    <a href="user-cancel-service-booking.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-danger btn-lg mx-2">
                                        <i class="fas fa-times-circle"></i> Cancel Booking
                                    </a>
                                <?php elseif($booking->sb_status == 'Pending' && !empty($booking->sb_technician_id)): ?>
                                    <button class="btn btn-secondary btn-lg mx-2" disabled title="Cannot cancel - Technician assigned">
                                        <i class="fas fa-ban"></i> Cannot Cancel
                                    </button>
                                    <div class="alert alert-info mt-3 mx-2">
                                        <i class="fas fa-info-circle"></i> <strong>Technician Assigned:</strong> You cannot cancel this booking as a technician has already been assigned. Please contact support if needed.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
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
        .detail-row {
            padding: 10px 0;
            font-size: 16px;
        }
        
        .detail-row i {
            width: 25px;
            margin-right: 10px;
        }
        
        .detail-row strong {
            margin-right: 10px;
        }
    </style>
</body>
</html>
