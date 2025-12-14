<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Error reporting disabled in production

include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
if (!$user_stmt) {
    die("Database error: " . $mysqli->error);
}
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();

if (!$user) {
    die("User not found. Please login again.");
}

// Get ALL bookings sorted: On Hold → New/In Progress → Completed
$bookings_query = "SELECT sb.*, s.s_name, s.s_category, s.s_duration, t.t_name,
                   CASE 
                       WHEN sb.sb_is_on_hold = 1 THEN 1
                       WHEN sb.sb_status IN ('Pending', 'Approved', 'In Progress') THEN 2
                       WHEN sb.sb_status = 'Completed' THEN 3
                       ELSE 4
                   END as sort_order
                   FROM tms_service_booking sb 
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id 
                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                   WHERE sb.sb_user_id = ? 
                   ORDER BY sort_order ASC, sb.sb_created_at DESC";
$bookings_stmt = $mysqli->prepare($bookings_query);
$bookings_stmt->bind_param('i', $aid);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Check for cancel success/error
$cancel_success = isset($_GET['cancelled']) && $_GET['cancelled'] == 1;
$cancel_error = isset($_GET['error']);
$error_message = '';
if($cancel_error) {
    if(isset($_SESSION['cancel_error'])) {
        $error_message = $_SESSION['cancel_error'];
        unset($_SESSION['cancel_error']);
    } else {
        $error_message = 'Failed to cancel booking. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#000000">
    <title>My Bookings - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="vendor/inc/navbar-styles.css?v=<?php echo time(); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-top: 75px;
            padding-bottom: 55px;
        }
        

        
        .content {
            padding: 15px;
        }
        
        .booking-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #ffe8f0 0%, #ffd6e8 100%);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-id {
            color: #d13abd;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge {
            background: rgba(209, 58, 189, 0.15);
            color: #d13abd;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(209, 58, 189, 0.3);
        }
        
        .card-body {
            padding: 15px;
        }
        
        .service-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .service-title i {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .info-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ffe8f0 0%, #ffd6e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d13abd;
            margin-right: 12px;
            font-size: 14px;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-top: 2px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 15px;
            background: #f9fafb;
        }
        
        .btn {
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .btn-track {
            background: linear-gradient(135deg, #f48fb1 0%, #ec6ead 80%, #d13abd 100%);
            color: white;
        }
        
        .btn-cancel {
            background: white;
            color: #ef4444;
            border: 2px solid #ef4444;
        }
        
        .btn:active {
            transform: scale(0.95);
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
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3);
        }
        
        .btn-book i {
            margin-right: 8px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        }


        
        .content {
            padding: 15px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 18px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.12);
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(209, 58, 189, 0.2);
        }
        
        .booking-card.status-pending {
            border-left-color: #ffc107;
        }
        
        .booking-card.status-approved {
            border-left-color: #17a2b8;
        }
        
        .booking-card.status-completed {
            border-left-color: #28a745;
        }
        
        .booking-card.status-cancelled {
            border-left-color: #dc3545;
        }
        
        .booking-card.on-hold {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border-left-color: #ffa502;
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 10px;
        }
        
        .booking-id {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }
        
        .booking-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-on-hold {
            background: #ffa502;
            color: white;
        }
        
        .booking-service {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .booking-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .booking-detail {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .booking-detail i {
            color: #d13abd;
            width: 14px;
        }
        
        .booking-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #d13abd 0%, #ec6ead 20%, #f48fb1 50%, #f59e9e 80%, #f9a8a8 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(209, 58, 189, 0.3);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .no-bookings i {
            font-size: 48px;
            color: #d13abd;
            margin-bottom: 15px;
        }
        
        .no-bookings h3 {
            font-size: 20px;
            margin-bottom: 8px;
            color: #333;
        }
        
        .no-bookings p {
            margin-bottom: 20px;
        }
        
        .hold-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 12px;
            color: #856404;
        }
        
        .hold-info i {
            color: #ffa502;
            margin-right: 5px;
        }


    </style>
</head>
<body>
    <?php include('vendor/inc/navbar.php'); ?>

    <div class="content">
        <?php if ($cancel_success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Booking cancelled successfully!
        </div>
        <?php endif; ?>
        
        <?php if ($cancel_error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
        
        <?php
        if ($bookings_result->num_rows > 0) {
            while ($booking = $bookings_result->fetch_object()) {
                $service_name = $booking->s_name ?? 'Service';
                $status = $booking->sb_status ?? 'Pending';
                $has_technician = !empty($booking->sb_technician_id);
                
                // If technician is assigned (Approved status), show as "In Progress" to customer
                $display_status = $status;
                if($status == 'Approved' && $has_technician) {
                    $display_status = 'In Progress';
                }
                
                // Determine badge styling
                $badge_style = '';
                if($display_status == 'Completed') {
                    $badge_style = 'background: #10b981; color: white;';
                } elseif($display_status == 'In Progress') {
                    $badge_style = 'background: #8b5cf6; color: white;';
                } elseif($display_status == 'Pending') {
                    $badge_style = 'background: #f59e0b; color: white;';
                } elseif(in_array($display_status, ['Cancelled', 'Rejected', 'Not Done'])) {
                    $badge_style = 'background: #ef4444; color: white;';
                }
        ?>
        
        <div class="booking-card">
            <div class="card-header">
                <div class="booking-id">
                    <i class="fas fa-receipt"></i> Booking #<?php echo str_pad($booking->sb_id, 5, '0', STR_PAD_LEFT); ?>
                </div>
                <div class="status-badge" style="<?php echo $badge_style; ?>">
                    <?php echo $display_status; ?>
                </div>
            </div>
            
            <div class="card-body">
                <div class="service-title">
                    <i class="fas fa-tools"></i>
                    <span><?php echo htmlspecialchars($service_name); ?></span>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Customer</div>
                        <div class="info-value"><?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Contact</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking->sb_phone); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Booking Date</div>
                        <div class="info-value"><?php echo date('d M Y', strtotime($booking->sb_booking_date)); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Time</div>
                        <div class="info-value"><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></div>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Pincode</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking->sb_pincode); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($booking->sb_address)): ?>
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking->sb_address); ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons" style="grid-template-columns: <?php 
                if($status == 'Completed') {
                    echo '1fr 1fr 1fr 1fr'; // View, Track, Bill, Feedback
                } elseif($status != 'Cancelled' && $status != 'Completed' && empty($booking->sb_technician_id)) {
                    echo '1fr 1fr 1fr'; // View, Track, Cancel
                } else {
                    echo '1fr 1fr'; // View, Track
                }
            ?>;">
                <a href="user-view-booking-details.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="user-track-booking.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track">
                    <i class="fas fa-map-marker-alt"></i> Track
                </a>
                <?php if ($status == 'Completed'): ?>
                <a href="user-view-bill.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-file-invoice"></i> Bill
                </a>
                <a href="user-give-feedback.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-star"></i> Feedback
                </a>
                <?php elseif ($status != 'Cancelled' && empty($booking->sb_technician_id)): ?>
                <a href="user-delete-booking.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-cancel" onclick="return confirm('Cancel this booking?');">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php } } else { ?>
        
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="empty-title">No Bookings Yet</div>
            <div class="empty-text">You haven't made any bookings yet.<br>Book your first service now!</div>
            <a href="book-service-step1.php" class="btn-book">
                <i class="fas fa-plus-circle"></i> Book Service
            </a>
        </div>
        
        <?php } ?>
    </div>

    <script>
        // Auto-refresh bookings list every 15 seconds to show status updates
        let refreshInterval;
        let isPageVisible = true;
        
        // Detect if page is visible
        document.addEventListener('visibilitychange', function() {
            isPageVisible = !document.hidden;
        });
        
        // Function to check for booking updates
        function checkForBookingUpdates() {
            if (!isPageVisible) return;
            
            fetch('get-all-bookings-status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.has_changes) {
                        // Show notification
                        showUpdateNotification('Booking status updated!');
                        
                        // Reload page after 2 seconds
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.log('Auto-refresh error:', error);
                });
        }
        
        // Show notification
        function showUpdateNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                left: 50%;
                transform: translateX(-50%);
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 15px 25px;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
                z-index: 9999;
                font-weight: 600;
                font-size: 14px;
                animation: slideDown 0.3s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            `;
            notification.innerHTML = `
                <i class="fas fa-sync-alt fa-spin"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        // Start auto-refresh every 15 seconds
        <?php if ($bookings_result->num_rows > 0): ?>
        refreshInterval = setInterval(checkForBookingUpdates, 15000);
        <?php endif; ?>
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideDown {
                from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
                to { opacity: 1; transform: translateX(-50%) translateY(0); }
            }
            @keyframes slideUp {
                from { opacity: 1; transform: translateX(-50%) translateY(0); }
                to { opacity: 0; transform: translateX(-50%) translateY(-20px); }
            }
        `;
        document.head.appendChild(style);
    </script>
    
    <script>
    // Live booking list updates
    let lastBookingCount = document.querySelectorAll('.booking-card').length;
    
    function checkForBookingUpdates() {
        fetch('api-dashboard-stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const currentCount = document.querySelectorAll('.booking-card').length;
                    
                    // If booking count changed, reload to show updates
                    if (data.stats.total !== lastBookingCount && lastBookingCount > 0) {
                        window.location.reload();
                    }
                    lastBookingCount = data.stats.total;
                }
            })
            .catch(error => console.error('Error checking booking updates:', error));
    }
    
    // Check every 10 seconds
    setInterval(checkForBookingUpdates, 10000);
    </script>

    <?php include('vendor/inc/bottom-nav.php'); ?>
</body>
</html>
