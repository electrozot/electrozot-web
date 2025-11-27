<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
include('../admin/vendor/inc/image-visibility-helper.php');
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
    header("Location: user-manage-booking.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Booking Details - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <?php include('vendor/inc/user-header-styles.php'); ?>
    <style>
        body {
            padding-top: 0;
        }
        
        .content {
            padding: 15px;
        }

        
        .status-header {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
            text-align: center;
        }
        
        .booking-number {
            font-size: 13px;
            color: #999;
            margin-bottom: 8px;
        }
        
        .service-name {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 700;
            color: white;
        }
        
        .section-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            flex: 0 0 120px;
            font-size: 13px;
            color: #666;
            font-weight: 600;
        }
        
        .info-value {
            flex: 1;
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }
        
        .image-gallery {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .image-item {
            position: relative;
        }
        
        .image-label {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
        }
        
        .image-wrapper {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .image-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
        }
        
        .btn-download {
            background: white;
            color: #6366f1;
            border: 2px solid #6366f1;
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-track {
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
        }
        
        .btn-orders {
            background: white;
            color: #6366f1;
            border: 2px solid #6366f1;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #3b82f6;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/user-header.php'); ?>

    <div class="content">
        <!-- Status Header -->
        <div class="status-header">
            <div class="booking-number">
                <i class="fas fa-receipt"></i> Booking #<?php echo str_pad($booking->sb_id, 5, '0', STR_PAD_LEFT); ?>
            </div>
            <div class="service-name"><?php echo htmlspecialchars($booking->s_name); ?></div>
            <?php
            $badge_style = '';
            if($booking->sb_status == 'Completed') {
                $badge_style = 'background: #10b981;';
            } elseif($booking->sb_status == 'In Progress') {
                $badge_style = 'background: #8b5cf6;';
            } elseif($booking->sb_status == 'Pending') {
                $badge_style = 'background: #f59e0b;';
            } elseif(in_array($booking->sb_status, ['Cancelled', 'Rejected', 'Not Done'])) {
                $badge_style = 'background: #ef4444;';
            } else {
                $badge_style = 'background: #6366f1;';
            }
            ?>
            <div class="status-badge" style="<?php echo $badge_style; ?>">
                <?php echo $booking->sb_status; ?>
            </div>
        </div>

        <!-- Service Details -->
        <div class="section-card">
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
                <?php 
                // Check if images should be visible (31 days for customers)
                $show_images = $booking->sb_status == 'Completed' && 
                               !empty($booking->sb_completed_date) && 
                               isImageVisible($booking->sb_completed_date, 'customer') &&
                               (!empty($booking->sb_completion_img) || !empty($booking->sb_bill_img) || !empty($booking->sb_service_image) || !empty($booking->sb_bill_image));
                ?>
                <?php if($show_images): ?>
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
                                    <?php 
                                    $completion_img = !empty($booking->sb_completion_img) ? $booking->sb_completion_img : (!empty($booking->sb_service_image) ? $booking->sb_service_image : '');
                                    if(!empty($completion_img) && shouldDisplayImage($completion_img, $booking->sb_completed_date, 'customer')): 
                                    ?>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="font-weight-bold text-success">
                                                <i class="fas fa-camera"></i> Service Completion Image
                                            </h6>
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
                                    <?php 
                                    $bill_img = !empty($booking->sb_bill_img) ? $booking->sb_bill_img : (!empty($booking->sb_bill_image) ? $booking->sb_bill_image : '');
                                    if(!empty($bill_img) && shouldDisplayImage($bill_img, $booking->sb_completed_date, 'customer')): 
                                    ?>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="font-weight-bold text-info">
                                                <i class="fas fa-file-invoice-dollar"></i> Service Bill
                                            </h6>
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
                                <a href="user-manage-booking.php" class="btn btn-info btn-lg mx-2">
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
