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
    <title>Track Order - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <?php include('vendor/inc/user-header-styles.php'); ?>
    <style>
        .content {
            padding: 15px;
        }
        
        .status-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            text-align: center;
        }
        
        .order-number {
            font-size: 13px;
            color: #999;
            margin-bottom: 8px;
        }
        
        .service-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .status-text {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .status-desc {
            font-size: 13px;
            color: #666;
        }
        
        .timeline-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
        }
        
        .timeline-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .timeline-title i {
            margin-right: 8px;
            color: #6366f1;
        }
        
        .timeline {
            position: relative;
            padding-left: 45px;
        }
        
        .timeline-step {
            position: relative;
            padding-bottom: 30px;
        }
        
        .timeline-step:last-child {
            padding-bottom: 0;
        }
        
        .timeline-step::before {
            content: '';
            position: absolute;
            left: -28px;
            top: 35px;
            width: 3px;
            height: calc(100% - 20px);
            background: #e5e7eb;
        }
        
        .timeline-step.completed::before {
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        }
        
        .timeline-step.active::before {
            background: #e5e7eb;
        }
        
        /* Hide line only for the last step */
        .timeline-step:last-child::before {
            display: none;
        }
        
        /* Show line for active step if it's not the last one */
        .timeline-step.active:not(:last-child)::before {
            display: block;
            background: #e5e7eb;
        }
        
        .step-icon {
            position: absolute;
            left: -40px;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #9ca3af;
            z-index: 2;
        }
        
        .timeline-step.completed .step-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .timeline-step.active .step-icon {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            animation: pulse 2s infinite;
        }
        
        .step-content {
            background: #f9fafb;
            padding: 12px;
            border-radius: 12px;
        }
        
        .timeline-step.active .step-content {
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            border: 2px solid #6366f1;
        }
        
        .step-title {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }
        
        .timeline-step.active .step-title {
            color: #6366f1;
        }
        
        .step-desc {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .step-time {
            font-size: 11px;
            color: #999;
        }
        
        .step-time i {
            margin-right: 4px;
        }
        
        .info-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
        }
        
        .info-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .info-title i {
            margin-right: 8px;
            color: #6366f1;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .info-item {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            padding: 12px;
            border-radius: 12px;
        }
        
        .info-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        
        .empty-state {
            background: white;
            border-radius: 20px;
            padding: 50px 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            margin-top: 50px;
        }
        
        .empty-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #6366f1;
        }
        
        .empty-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .empty-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .booking-selector {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
        }
        
        .selector-title {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .selector-title i {
            margin-right: 8px;
            color: #6366f1;
        }
        
        .booking-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .booking-select:focus {
            outline: none;
            border-color: #6366f1;
            background: white;
        }
        
        .btn-book {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 15px 35px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .btn-book i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/user-header.php'); ?>

    <div class="content">
        <?php
        // Get all user bookings for dropdown
        $all_bookings_query = "SELECT sb.sb_id, sb.sb_booking_date, sb.sb_status, s.s_name 
                               FROM tms_service_booking sb 
                               LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                               WHERE sb.sb_user_id = ? 
                               ORDER BY sb.sb_created_at DESC";
        $all_bookings_stmt = $mysqli->prepare($all_bookings_query);
        $all_bookings_stmt->bind_param('i', $aid);
        $all_bookings_stmt->execute();
        $all_bookings_result = $all_bookings_stmt->get_result();
        $has_bookings = $all_bookings_result->num_rows > 0;
        
        // Get booking to track - either from URL parameter or latest booking
        $booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
        
        if($booking_id > 0) {
            // Get specific booking
            $booking_query = "SELECT sb.*, s.s_name, s.s_category 
                             FROM tms_service_booking sb 
                             LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                             WHERE sb.sb_id = ? AND sb.sb_user_id = ?";
            $booking_stmt = $mysqli->prepare($booking_query);
            $booking_stmt->bind_param('ii', $booking_id, $aid);
        } else {
            // Get latest booking
            $booking_query = "SELECT sb.*, s.s_name, s.s_category 
                             FROM tms_service_booking sb 
                             LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                             WHERE sb.sb_user_id = ? 
                             ORDER BY sb.sb_created_at DESC 
                             LIMIT 1";
            $booking_stmt = $mysqli->prepare($booking_query);
            $booking_stmt->bind_param('i', $aid);
        }
        
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        $booking = $booking_result->fetch_object();
        
        if ($booking) {
            // Show booking selector if user has multiple bookings
            if($has_bookings && $all_bookings_result->num_rows > 1) {
                mysqli_data_seek($all_bookings_result, 0); // Reset pointer
        ?>
        <div class="booking-selector">
            <div class="selector-title">
                <i class="fas fa-list"></i> Select Booking to Track
            </div>
            <select class="booking-select" onchange="window.location.href='user-track-booking.php?booking_id=' + this.value">
                <?php while($b = $all_bookings_result->fetch_object()): ?>
                <option value="<?php echo $b->sb_id; ?>" <?php echo ($b->sb_id == $booking->sb_id) ? 'selected' : ''; ?>>
                    #<?php echo str_pad($b->sb_id, 5, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($b->s_name); ?> (<?php echo date('d M Y', strtotime($b->sb_booking_date)); ?>) - <?php echo $b->sb_status; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php
            }
        ?>
        
        <?php
            $service_name = $booking->s_name ?? 'Service';
            $status = $booking->sb_status ?? 'Pending';
            $has_technician = !empty($booking->sb_technician_id);
            
            // If technician is assigned (Approved status), show as "In Progress" to customer
            $display_status = $status;
            if($status == 'Approved' && $has_technician) {
                $display_status = 'In Progress';
            }
            
            // Determine status display
            $status_icon_bg = '';
            $status_icon = '';
            $status_color = '';
            $status_message = '';
            
            switch($display_status) {
                case 'Pending':
                    $status_icon_bg = 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);';
                    $status_icon = 'clock';
                    $status_color = '#f59e0b';
                    $status_message = 'Waiting for technician assignment';
                    break;
                case 'Approved':
                    $status_icon_bg = 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);';
                    $status_icon = 'check-circle';
                    $status_color = '#3b82f6';
                    $status_message = 'Booking confirmed - Technician assigned';
                    break;
                case 'Confirmed':
                    $status_icon_bg = 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);';
                    $status_icon = 'check-circle';
                    $status_color = '#3b82f6';
                    $status_message = 'Booking confirmed successfully';
                    break;
                case 'In Progress':
                    $status_icon_bg = 'background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);';
                    $status_icon = 'tools';
                    $status_color = '#8b5cf6';
                    $status_message = 'Technician is working on your service';
                    break;
                case 'Completed':
                    $status_icon_bg = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%);';
                    $status_icon = 'check-double';
                    $status_color = '#10b981';
                    $status_message = 'Service completed successfully';
                    break;
                case 'Cancelled':
                    $status_icon_bg = 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);';
                    $status_icon = 'times-circle';
                    $status_color = '#ef4444';
                    $status_message = 'Booking cancelled';
                    break;
                case 'Rejected':
                case 'Rejected by Technician':
                case 'Not Done':
                    $status_icon_bg = 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);';
                    $status_icon = 'exclamation-triangle';
                    $status_color = '#ef4444';
                    $status_message = 'Service could not be completed - Contact support';
                    break;
                default:
                    $status_icon_bg = 'background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);';
                    $status_icon = 'info-circle';
                    $status_color = '#6b7280';
                    $status_message = 'Status: ' . $status;
            }
            
            // Timeline steps
            $step_pending = true;
            $step_confirmed = in_array($display_status, ['Approved', 'Confirmed', 'In Progress', 'Completed']) || $has_technician;
            $step_progress = in_array($display_status, ['In Progress', 'Completed']);
            $step_completed = ($display_status == 'Completed');
        ?>
        
        <!-- Status Card -->
        <div class="status-card">
            <div class="order-number">Order #<?php echo str_pad($booking->sb_id, 5, '0', STR_PAD_LEFT); ?></div>
            <div class="service-name"><?php echo htmlspecialchars($service_name); ?></div>
            <div class="status-icon" style="<?php echo $status_icon_bg; ?>">
                <i class="fas fa-<?php echo $status_icon; ?>"></i>
            </div>
            <div class="status-text" style="color: <?php echo $status_color; ?>;"><?php echo $display_status; ?></div>
            <div class="status-desc"><?php echo $status_message; ?></div>
        </div>
        
        <!-- Timeline Card -->
        <div class="timeline-card">
            <div class="timeline-title">
                <i class="fas fa-route"></i> Order Progress
            </div>
            
            <div class="timeline">
                <!-- Step 1 -->
                <div class="timeline-step <?php echo $step_pending ? 'completed' : ''; ?>">
                    <div class="step-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="step-content">
                        <div class="step-title">Order Placed</div>
                        <div class="step-desc">Your booking has been received</div>
                        <div class="step-time">
                            <i class="fas fa-clock"></i> <?php echo date('d M, h:i A', strtotime($booking->sb_booking_date . ' ' . $booking->sb_booking_time)); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="timeline-step <?php echo $step_confirmed ? ($step_progress ? 'completed' : 'active') : ''; ?>">
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="step-content">
                        <div class="step-title">Order Confirmed</div>
                        <div class="step-desc">
                            <?php echo $step_confirmed ? 'Booking confirmed' : 'Waiting for confirmation'; ?>
                        </div>
                        <?php if ($step_confirmed): ?>
                        <div class="step-time">
                            <i class="fas fa-check"></i> Confirmed
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="timeline-step <?php echo $step_progress ? ($step_completed ? 'completed' : 'active') : ''; ?>">
                    <div class="step-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="step-content">
                        <div class="step-title">Service In Progress</div>
                        <div class="step-desc">
                            <?php echo $step_progress ? 'Technician working' : 'Not started yet'; ?>
                        </div>
                        <?php if ($step_progress): ?>
                        <div class="step-time">
                            <i class="fas fa-spinner fa-spin"></i> In Progress
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="timeline-step <?php echo $step_completed ? 'completed' : ''; ?>">
                    <div class="step-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="step-content">
                        <div class="step-title">Service Completed</div>
                        <div class="step-desc">
                            <?php echo $step_completed ? 'Service finished' : 'Pending completion'; ?>
                        </div>
                        <?php if ($step_completed): ?>
                        <div class="step-time">
                            <i class="fas fa-check-double"></i> Completed
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="info-card">
            <div class="info-title">
                <i class="fas fa-info-circle"></i> Booking Details
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Customer</div>
                    <div class="info-value"><?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value"><?php echo htmlspecialchars($user->u_phone); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Date</div>
                    <div class="info-value"><?php echo date('d M Y', strtotime($booking->sb_booking_date)); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value" style="color: <?php echo $status_color; ?>;"><?php echo $status; ?></div>
                </div>
            </div>
        </div>
        
        <?php } else { ?>
        
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="empty-title">No Active Orders</div>
            <div class="empty-text">You don't have any orders to track.<br>Book a service to get started!</div>
            <a href="book-service-step1.php" class="btn-book">
                <i class="fas fa-plus-circle"></i> Book Service
            </a>
        </div>
        
        <?php } ?>
    </div>

    <?php include('vendor/inc/user-footer.php'); ?>
</body>
</html>
