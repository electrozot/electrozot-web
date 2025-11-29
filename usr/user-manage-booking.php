<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    <title>My Bookings - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-top: 75px;
            padding-bottom: 55px;
        }
        
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            color: white;
            padding: 10px 15px;
            box-shadow: 0 4px 20px rgba(209, 58, 189, 0.3);
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-left: 0;
            margin-left: -5px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .logo {
            height: 55px;
            width: auto;
        }
        
        .brand-text h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 13px;
            opacity: 0.85;
            margin: 3px 0 0 0;
            font-style: italic;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }
        
        .header-icons {
            display: flex;
            gap: 6px;
        }
        
        .header-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 18px;
            flex-shrink: 0;
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
        
        .bottom-nav {
            position: fixed;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 16px);
            max-width: 450px;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            box-shadow: 0 3px 20px rgba(209, 58, 189, 0.35), 0 1px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 4px 6px;
            z-index: 1000;
            border-radius: 20px;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 4px 2px;
            position: relative;
            border-radius: 12px;
        }
        
        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }
        
        .nav-item.active { 
            color: white;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
        }
        
        .nav-item i {
            font-size: 16px;
            display: block;
            margin-bottom: 1px;
        }
        
        .nav-item.active i {
            animation: bounce 0.4s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        
        .nav-item span {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }
        
        @media (min-width: 768px) {
            .bottom-nav {
                max-width: 400px;
                bottom: 10px;
                padding: 5px 8px;
            }
            
            .nav-item {
                padding: 5px 4px;
            }
            
            .nav-item i {
                font-size: 18px;
                margin-bottom: 2px;
            }
            
            .nav-item span {
                font-size: 9px;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="header-content">
            <a href="../index.php" class="brand-section" style="text-decoration: none; color: white;">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </a>
            <div class="user-section">
                <div class="header-icons">
                    <a href="user-view-profile.php" class="header-icon">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

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
            
            <div class="action-buttons" style="grid-template-columns: <?php echo ($status != 'Cancelled' && $status != 'Completed' && empty($booking->sb_technician_id)) ? '1fr 1fr 1fr' : '1fr 1fr'; ?>;">
                <a href="user-view-booking-details.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="user-track-booking.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track">
                    <i class="fas fa-map-marker-alt"></i> Track
                </a>
                <?php if ($status != 'Cancelled' && $status != 'Completed' && empty($booking->sb_technician_id)): ?>
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

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service-step1.php" class="nav-item">
            <i class="fas fa-calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item active">
            <i class="fas fa-list-alt"></i>
            <span>Orders</span>
        </a>
        <a href="user-view-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <a href="../index.php" class="nav-item">
            <i class="fas fa-store"></i>
            <span>Main</span>
        </a>
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
</body>
</html>
