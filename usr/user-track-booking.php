<?php
ob_start();
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

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
    <meta name="theme-color" content="#000000">
    <title>Track Order - Electrozot</title>
    
    <!-- Favicon -->
    <?php include('vendor/inc/favicon.php'); ?>
    
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="vendor/inc/navbar-styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            padding-top: 75px;
            padding-bottom: 70px;
        }
        
        .content {
            padding: 15px;
            padding-bottom: 25px;
        }
        
        .status-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
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
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
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
            color: #d13abd;
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
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 100%);
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
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
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
            color: #d13abd;
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
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
            margin-top: 50px;
        }
        
        .empty-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffe8f0 0%, #ffd6e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #d13abd;
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
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
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
            color: #d13abd;
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
            border-color: #d13abd;
            background: white;
        }
        
        .btn-book {
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
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
        
        .technician-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .tech-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .tech-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            margin-right: 15px;
            background: white;
        }
        
        .tech-info {
            flex: 1;
        }
        
        .tech-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .tech-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .tech-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .tech-contact {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .tech-contact-btn {
            flex: 1;
            background: rgba(255, 255, 255, 0.2);
            color: white;
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
        
        .tech-contact-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        .tech-contact-btn i {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/navbar.php'); ?>

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
            // Get specific booking with technician info
            $booking_query = "SELECT sb.*, s.s_name, s.s_category, t.t_name, t.t_pic, t.t_phone 
                             FROM tms_service_booking sb 
                             LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                             LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id 
                             WHERE sb.sb_id = ? AND sb.sb_user_id = ?";
            $booking_stmt = $mysqli->prepare($booking_query);
            $booking_stmt->bind_param('ii', $booking_id, $aid);
        } else {
            // Get latest booking with technician info
            $booking_query = "SELECT sb.*, s.s_name, s.s_category, t.t_name, t.t_pic, t.t_phone 
                             FROM tms_service_booking sb 
                             LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                             LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id 
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
        
        <!-- Technician Card (Show only if technician is assigned) -->
        <?php if ($has_technician && !empty($booking->t_name)): ?>
        <div class="technician-card">
            <div class="tech-header">
                <?php 
                $tech_photo = !empty($booking->t_pic) ? '../admin/assets/img/technicians/' . $booking->t_pic : '../admin/assets/img/default-avatar.png';
                ?>
                <img src="<?php echo htmlspecialchars($tech_photo); ?>" alt="Technician" class="tech-photo" onerror="this.src='../admin/assets/img/default-avatar.png'">
                <div class="tech-info">
                    <div class="tech-label">Your Technician</div>
                    <div class="tech-name"><?php echo htmlspecialchars($booking->t_name); ?></div>
                    <span class="tech-badge"><i class="fas fa-tools"></i> Assigned</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- On Hold Alert - COMPACT -->
        <?php if(isset($booking->sb_is_on_hold) && $booking->sb_is_on_hold == 1): ?>
        <div class="info-card" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%); border: 3px solid #ffc107; animation: pulse 2s infinite;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #856404; font-size: 16px; margin-bottom: 5px;">
                        <i class="fas fa-pause-circle"></i> ⚠️ On Hold
                    </div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 3px;">
                        <?php echo htmlspecialchars($booking->sb_hold_reason); ?>
                    </div>
                    <div style="color: #666; font-size: 12px;">
                        <i class="fas fa-clock"></i> Until: <?php echo date('M d', strtotime($booking->sb_hold_end_date)); ?>
                    </div>
                </div>
                <button onclick="unholdBooking(<?php echo $booking->sb_id; ?>)" 
                   style="background: linear-gradient(135deg, #00c853 0%, #00F260 100%); color: white; padding: 12px 20px; border-radius: 10px; border: none; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 3px 10px rgba(0, 200, 83, 0.3); white-space: nowrap;">
                    <i class="fas fa-play-circle"></i> Resume
                </button>
            </div>
        </div>
        <?php endif; ?>
        
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
        
        <!-- Quick Actions -->
        <div class="info-card">
            <div class="info-title">
                <i class="fas fa-bolt"></i> Quick Actions
            </div>
            
            <div style="display: grid; grid-template-columns: <?php echo ($booking->sb_status == 'Completed') ? '1fr 1fr 1fr' : '1fr 1fr'; ?>; gap: 10px;">
                <a href="user-view-booking-details.php?booking_id=<?php echo $booking->sb_id; ?>" style="background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%); color: white; padding: 12px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-eye" style="margin-right: 6px;"></i> View Full Details
                </a>
                <?php if($booking->sb_status == 'Completed'): ?>
                <a href="user-view-bill.php?booking_id=<?php echo $booking->sb_id; ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-invoice" style="margin-right: 6px;"></i> View Bill
                </a>
                <?php endif; ?>
                <a href="user-manage-booking.php" style="background: white; color: #d13abd; border: 2px solid #d13abd; padding: 12px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-list" style="margin-right: 6px;"></i> All Orders
                </a>
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
    
    <script>
    // Unhold booking function - Single click
    function unholdBooking(bookingId) {
        if(confirm('Resume this booking?\n\n✓ Booking will be marked as HIGH PRIORITY\n✓ Technician will be notified immediately\n✓ Service will continue\n\nClick OK to resume now.')) {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resuming...';
            btn.disabled = true;
            
            fetch('api-unhold-booking.php?id=' + bookingId, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('✅ Booking resumed successfully!\n\nYour booking is now HIGH PRIORITY.\nTechnician has been notified.');
                    location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to resume booking'));
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                alert('❌ Error: Could not resume booking. Please try again.');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        }
    }
    
    // Live update functionality
    <?php if ($booking): ?>
    const bookingId = <?php echo $booking->sb_id; ?>;
    let lastStatus = '<?php echo $status; ?>';
    let lastTechnicianId = '<?php echo $booking->sb_technician_id ?? ''; ?>';
    
    function updateBookingData() {
        fetch('api-live-booking-data.php?booking_id=' + bookingId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const booking = data.data;
                    
                    // Check if status changed
                    if (booking.sb_status !== lastStatus || booking.sb_technician_id !== lastTechnicianId) {
                        // Reload page to show updated data with smooth transition
                        window.location.reload();
                    }
                }
            })
            .catch(error => console.error('Error fetching booking data:', error));
    }
    
    // Update every 5 seconds
    setInterval(updateBookingData, 5000);
    <?php endif; ?>
    </script>

    <?php include('vendor/inc/bottom-nav.php'); ?>
</body>
</html>
