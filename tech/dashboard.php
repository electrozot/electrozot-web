<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "Dashboard";

// Get technician stats
$stats_query = "SELECT COUNT(*) as total_bookings FROM tms_service_booking WHERE sb_technician_id = ?";
$stmt = $mysqli->prepare($stats_query);
if($stmt) {
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $stats_result = $stmt->get_result();
    $stats = $stats_result->fetch_object();
    $total_bookings = $stats->total_bookings;
} else {
    $total_bookings = 0;
}

// Get pending bookings
$pending_query = "SELECT COUNT(*) as pending FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Pending'";
$stmt2 = $mysqli->prepare($pending_query);
if($stmt2) {
    $stmt2->bind_param('i', $t_id);
    $stmt2->execute();
    $pending_result = $stmt2->get_result();
    $pending_data = $pending_result->fetch_object();
    $pending_bookings = $pending_data->pending;
} else {
    $pending_bookings = 0;
}

// Get in progress bookings
$progress_query = "SELECT COUNT(*) as progress FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'In Progress'";
$stmt_progress = $mysqli->prepare($progress_query);
if($stmt_progress) {
    $stmt_progress->bind_param('i', $t_id);
    $stmt_progress->execute();
    $progress_result = $stmt_progress->get_result();
    $progress_data = $progress_result->fetch_object();
    $progress_bookings = $progress_data->progress;
} else {
    $progress_bookings = 0;
}

// Get completed bookings
$completed_query = "SELECT COUNT(*) as completed FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Completed'";
$stmt3 = $mysqli->prepare($completed_query);
if($stmt3) {
    $stmt3->bind_param('i', $t_id);
    $stmt3->execute();
    $completed_result = $stmt3->get_result();
    $completed_data = $completed_result->fetch_object();
    $completed_bookings = $completed_data->completed;
} else {
    $completed_bookings = 0;
}

// Get recent bookings
$recent_query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name 
                 FROM tms_service_booking sb
                 LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                 LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                 WHERE sb.sb_technician_id = ?
                 ORDER BY sb.sb_booking_date DESC, sb.sb_booking_time DESC
                 LIMIT 5";
$stmt_recent = $mysqli->prepare($recent_query);
$stmt_recent->bind_param('i', $t_id);
$stmt_recent->execute();
$recent_result = $stmt_recent->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container-fluid main-content">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="welcome-content">
                        <h1 class="welcome-title">
                            <i class="fas fa-hand-sparkles"></i>
                            Welcome Back, <?php echo htmlspecialchars($t_name); ?>!
                        </h1>
                        <p class="welcome-subtitle">
                            Here's your dashboard overview for today. Manage your bookings and track your performance.
                        </p>
                        <div class="welcome-meta">
                            <span><i class="fas fa-calendar"></i> <?php echo date('l, F d, Y'); ?></span>
                            <span class="ml-3"><i class="fas fa-id-badge"></i> ID: <?php echo htmlspecialchars($t_id_no); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="welcome-illustration">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="new-bookings.php" class="quick-access-card card-red">
                    <div class="qac-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="qac-content">
                        <h3>New Bookings</h3>
                        <p>View & accept new service requests</p>
                        <div class="qac-badge"><?php echo $pending_bookings; ?> Pending</div>
                    </div>
                    <div class="qac-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="my-bookings.php" class="quick-access-card card-yellow">
                    <div class="qac-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="qac-content">
                        <h3>All Bookings</h3>
                        <p>Manage all your assigned tasks</p>
                        <div class="qac-badge"><?php echo $total_bookings; ?> Total</div>
                    </div>
                    <div class="qac-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="completed-bookings.php" class="quick-access-card card-green">
                    <div class="qac-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="qac-content">
                        <h3>Completed</h3>
                        <p>View your completed services</p>
                        <div class="qac-badge"><?php echo $completed_bookings; ?> Done</div>
                    </div>
                    <div class="qac-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="profile.php" class="quick-access-card card-pink">
                    <div class="qac-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="qac-content">
                        <h3>My Profile</h3>
                        <p>Update your information</p>
                        <div class="qac-badge">Edit Profile</div>
                    </div>
                    <div class="qac-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card-modern stats-red">
                    <div class="stats-icon-modern">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stats-details">
                        <h2><?php echo $total_bookings; ?></h2>
                        <p>Total Bookings</p>
                        <div class="stats-trend">
                            <i class="fas fa-chart-line"></i> All time
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card-modern stats-yellow">
                    <div class="stats-icon-modern">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-details">
                        <h2><?php echo $pending_bookings; ?></h2>
                        <p>Pending Tasks</p>
                        <div class="stats-trend">
                            <i class="fas fa-exclamation-circle"></i> Needs attention
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card-modern stats-pink">
                    <div class="stats-icon-modern">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="stats-details">
                        <h2><?php echo $progress_bookings; ?></h2>
                        <p>In Progress</p>
                        <div class="stats-trend">
                            <i class="fas fa-sync"></i> Active now
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card-modern stats-green">
                    <div class="stats-icon-modern">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-details">
                        <h2><?php echo $completed_bookings; ?></h2>
                        <p>Completed</p>
                        <div class="stats-trend">
                            <i class="fas fa-trophy"></i> <?php echo $total_bookings > 0 ? round(($completed_bookings/$total_bookings)*100) : 0; ?>% Success
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings & Performance -->
        <div class="row">
            <!-- Recent Bookings -->
            <div class="col-lg-9 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5>
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </h5>
                        <a href="my-bookings.php" class="btn btn-sm btn-primary-custom">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <?php if($recent_result->num_rows > 0): ?>
                        <div class="bookings-list-modern">
                            <?php while($booking = $recent_result->fetch_object()): 
                                $status_class = '';
                                $status_icon = '';
                                $status_bg = '';
                                if($booking->sb_status == 'Pending') {
                                    $status_class = 'status-pending';
                                    $status_icon = 'fa-clock';
                                    $status_bg = '#ffa502';
                                } elseif($booking->sb_status == 'In Progress') {
                                    $status_class = 'status-progress';
                                    $status_icon = 'fa-spinner';
                                    $status_bg = '#00b4db';
                                } elseif($booking->sb_status == 'Completed') {
                                    $status_class = 'status-completed';
                                    $status_icon = 'fa-check-circle';
                                    $status_bg = '#38ef7d';
                                } else {
                                    $status_class = 'status-cancelled';
                                    $status_icon = 'fa-times-circle';
                                    $status_bg = '#ff4757';
                                }
                            ?>
                            <div class="booking-item-modern">
                                <div class="booking-left">
                                    <div class="booking-icon-modern" style="background: linear-gradient(135deg, <?php echo $status_bg; ?>, <?php echo $status_bg; ?>dd);">
                                        <i class="fas fa-wrench"></i>
                                    </div>
                                    <div class="booking-info">
                                        <h6><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></h6>
                                        <p class="service-name"><i class="fas fa-tools"></i> <?php echo htmlspecialchars($booking->s_name); ?></p>
                                        <p class="booking-time"><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?> <span class="mx-2">•</span> <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></p>
                                    </div>
                                </div>
                                <div class="booking-right">
                                    <span class="status-badge-modern <?php echo $status_class; ?>">
                                        <i class="fas <?php echo $status_icon; ?>"></i>
                                        <?php echo $booking->sb_status; ?>
                                    </span>
                                    <a href="booking-details.php?id=<?php echo $booking->sb_id; ?>" class="btn-view-modern">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h5>No Recent Bookings</h5>
                            <p>You don't have any bookings yet. New assignments will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Performance & Quick Links -->
            <div class="col-lg-3 mb-4">
                <!-- Performance Card -->
                <div class="card-custom performance-card-modern">
                    <div class="performance-header">
                        <div class="performance-icon-modern">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h5>Performance</h5>
                    </div>
                    
                    <div class="performance-stats">
                        <div class="perf-circle">
                            <svg viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#gradient)" stroke-width="8" 
                                        stroke-dasharray="<?php echo $total_bookings > 0 ? (($completed_bookings/$total_bookings)*283) : 0; ?> 283" 
                                        stroke-linecap="round" transform="rotate(-90 50 50)"/>
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#11998e;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#38ef7d;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="perf-percentage">
                                <h2><?php echo $total_bookings > 0 ? round(($completed_bookings/$total_bookings)*100) : 0; ?>%</h2>
                                <p>Success Rate</p>
                            </div>
                        </div>
                        
                        <div class="perf-details">
                            <div class="perf-detail-item">
                                <span class="perf-label">Completed</span>
                                <span class="perf-value"><?php echo $completed_bookings; ?></span>
                            </div>
                            <div class="perf-detail-item">
                                <span class="perf-label">Total Tasks</span>
                                <span class="perf-value"><?php echo $total_bookings; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card-custom mt-3 quick-links-card">
                    <h6 class="quick-links-title">
                        <i class="fas fa-link"></i> Quick Links
                    </h6>
                    <div class="quick-links-list">
                        <a href="change-password.php" class="quick-link-item">
                            <i class="fas fa-key"></i>
                            <span>Change Password</span>
                        </a>
                        <a href="profile.php" class="quick-link-item">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                        <a href="logout.php" class="quick-link-item text-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #ff4757 0%, #ffa502 50%, #ff6b9d 100%);
            border-radius: 30px;
            padding: 50px;
            margin-bottom: 30px;
            box-shadow: 0 15px 50px rgba(255, 71, 87, 0.4);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 215, 0, 0.15);
            border-radius: 50%;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: white;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .welcome-title i {
            color: #ffd700;
            margin-right: 15px;
            animation: wave 2s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(20deg); }
            75% { transform: rotate(-20deg); }
        }

        .welcome-subtitle {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.95);
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .welcome-meta {
            color: rgba(255,255,255,0.95);
            font-size: 1rem;
            font-weight: 600;
        }

        .welcome-meta i {
            color: #ffd700;
            margin-right: 8px;
        }

        .welcome-illustration {
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            backdrop-filter: blur(10px);
            border: 5px solid rgba(255,255,255,0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .welcome-illustration i {
            font-size: 5rem;
            color: #ffd700;
        }

        /* Quick Access Cards */
        .quick-access-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .quick-access-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            transition: width 0.3s ease;
        }

        .quick-access-card.card-red::before {
            background: linear-gradient(180deg, #ff4757, #ff6348);
        }

        .quick-access-card.card-yellow::before {
            background: linear-gradient(180deg, #ffa502, #ff6348);
        }

        .quick-access-card.card-green::before {
            background: linear-gradient(180deg, #11998e, #38ef7d);
        }

        .quick-access-card.card-pink::before {
            background: linear-gradient(180deg, #ff6b9d, #ff4757);
        }

        .quick-access-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            text-decoration: none;
        }

        .quick-access-card:hover::before {
            width: 100%;
        }

        .qac-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            flex-shrink: 0;
            margin-right: 20px;
            position: relative;
            z-index: 2;
        }

        .card-red .qac-icon {
            background: linear-gradient(135deg, #ff4757, #ff6348);
        }

        .card-yellow .qac-icon {
            background: linear-gradient(135deg, #ffa502, #ff6348);
        }

        .card-green .qac-icon {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .card-pink .qac-icon {
            background: linear-gradient(135deg, #ff6b9d, #ff4757);
        }

        .qac-content {
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .qac-content h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }

        .quick-access-card:hover .qac-content h3 {
            color: white;
        }

        .qac-content p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .quick-access-card:hover .qac-content p {
            color: rgba(255,255,255,0.9);
        }

        .qac-badge {
            display: inline-block;
            padding: 5px 15px;
            background: #f8f9fa;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #2d3748;
            transition: all 0.3s ease;
        }

        .quick-access-card:hover .qac-badge {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        .qac-arrow {
            font-size: 1.5rem;
            color: #e2e8f0;
            margin-left: 15px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .quick-access-card:hover .qac-arrow {
            color: white;
            transform: translateX(5px);
        }

        /* Modern Stats Cards */
        .stats-card-modern {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
            height: 100%;
        }

        .stats-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.1;
            transition: all 0.3s ease;
        }

        .stats-card-modern.stats-red::before {
            background: linear-gradient(135deg, #ff4757, #ff6348);
        }

        .stats-card-modern.stats-yellow::before {
            background: linear-gradient(135deg, #ffa502, #ff6348);
        }

        .stats-card-modern.stats-pink::before {
            background: linear-gradient(135deg, #ff6b9d, #ff4757);
        }

        .stats-card-modern.stats-green::before {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .stats-card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .stats-card-modern:hover::before {
            width: 150px;
            height: 150px;
            opacity: 0.15;
        }

        .stats-icon-modern {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            flex-shrink: 0;
            position: relative;
            z-index: 2;
        }

        .stats-red .stats-icon-modern {
            background: linear-gradient(135deg, #ff4757, #ff6348);
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);
        }

        .stats-yellow .stats-icon-modern {
            background: linear-gradient(135deg, #ffa502, #ff6348);
            box-shadow: 0 8px 20px rgba(255, 165, 2, 0.3);
        }

        .stats-pink .stats-icon-modern {
            background: linear-gradient(135deg, #ff6b9d, #ff4757);
            box-shadow: 0 8px 20px rgba(255, 107, 157, 0.3);
        }

        .stats-green .stats-icon-modern {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            box-shadow: 0 8px 20px rgba(56, 239, 125, 0.3);
        }

        .stats-details {
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .stats-details h2 {
            font-size: 3rem;
            font-weight: 900;
            color: #2d3748;
            margin-bottom: 5px;
            line-height: 1;
        }

        .stats-details p {
            font-size: 1rem;
            color: #6c757d;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stats-trend {
            font-size: 0.85rem;
            color: #a0aec0;
            font-weight: 600;
        }

        .stats-trend i {
            margin-right: 5px;
        }

        /* Card Header */
        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #f0f0f0;
        }

        .card-header-custom h5 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #2d3748;
            margin: 0;
        }

        .card-header-custom i {
            color: #ff4757;
            margin-right: 10px;
        }

        /* Modern Bookings List */
        .bookings-list-modern {
            max-height: 600px;
            overflow-y: auto;
        }

        .bookings-list-modern::-webkit-scrollbar {
            width: 8px;
        }

        .bookings-list-modern::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .bookings-list-modern::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #ff4757, #ffa502);
            border-radius: 10px;
        }

        .booking-item-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 15px;
            background: white;
            border: 2px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .booking-item-modern:hover {
            border-color: #ff4757;
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(255, 71, 87, 0.15);
        }

        .booking-left {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .booking-icon-modern {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .booking-icon-modern i {
            font-size: 1.8rem;
            color: white;
        }

        .booking-info {
            flex: 1;
        }

        .booking-info h6 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .booking-info .service-name {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .booking-info .booking-time {
            font-size: 0.85rem;
            color: #a0aec0;
            margin: 0;
        }

        .booking-info i {
            margin-right: 5px;
        }

        .booking-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-badge-modern {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .status-pending {
            background: linear-gradient(135deg, #ffa502, #ff6348);
            color: white;
        }

        .status-progress {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: white;
        }

        .status-completed {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #ff4757, #ff6b9d);
            color: white;
        }

        .btn-view-modern {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff4757, #ffa502);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
        }

        .btn-view-modern:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 71, 87, 0.4);
            color: white;
        }

        /* Performance Card Modern */
        .performance-card-modern {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .performance-card-modern::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .performance-header {
            position: relative;
            z-index: 2;
            margin-bottom: 25px;
        }

        .performance-icon-modern {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255,255,255,0.3);
        }

        .performance-icon-modern i {
            font-size: 2rem;
            color: #ffd700;
        }

        .performance-header h5 {
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            margin: 0;
        }

        .performance-stats {
            position: relative;
            z-index: 2;
        }

        .perf-circle {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 25px;
        }

        .perf-circle svg {
            transform: rotate(0deg);
        }

        .perf-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .perf-percentage h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: white;
            margin: 0;
            line-height: 1;
        }

        .perf-percentage p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.9);
            margin: 5px 0 0 0;
            font-weight: 600;
        }

        .perf-details {
            background: rgba(255,255,255,0.15);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .perf-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .perf-detail-item:last-child {
            border-bottom: none;
        }

        .perf-label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.9);
            font-weight: 600;
        }

        .perf-value {
            font-size: 1.5rem;
            font-weight: 900;
            color: white;
        }

        /* Quick Links Card */
        .quick-links-card {
            background: white;
        }

        .quick-links-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
        }

        .quick-links-title i {
            color: #ff4757;
            margin-right: 8px;
        }

        .quick-links-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quick-link-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8f9fa;
            text-decoration: none;
            color: #2d3748;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .quick-link-item:hover {
            background: linear-gradient(135deg, #ff4757, #ffa502);
            color: white;
            transform: translateX(5px);
            text-decoration: none;
        }

        .quick-link-item.text-danger:hover {
            background: linear-gradient(135deg, #ff4757, #ff6b9d);
            color: white !important;
        }

        .quick-link-item i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i {
            font-size: 5rem;
            color: #e2e8f0;
            margin-bottom: 25px;
        }

        .empty-state h5 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #6c757d;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #a0aec0;
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 1199px) {
            .welcome-banner {
                padding: 40px;
            }

            .welcome-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 991px) {
            .welcome-title {
                font-size: 1.8rem;
            }

            .welcome-subtitle {
                font-size: 1rem;
            }

            .welcome-illustration {
                width: 140px;
                height: 140px;
                margin-top: 20px;
            }

            .welcome-illustration i {
                font-size: 3.5rem;
            }

            .quick-access-card {
                margin-bottom: 15px;
            }

            .stats-details h2 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .welcome-banner {
                padding: 30px;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .welcome-subtitle {
                font-size: 0.95rem;
            }

            .welcome-illustration {
                width: 100px;
                height: 100px;
            }

            .welcome-illustration i {
                font-size: 2.5rem;
            }

            .quick-access-card {
                padding: 20px;
            }

            .qac-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .qac-content h3 {
                font-size: 1.1rem;
            }

            .stats-card-modern {
                padding: 20px;
            }

            .stats-icon-modern {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }

            .stats-details h2 {
                font-size: 2rem;
            }

            .booking-item-modern {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .booking-left {
                flex-direction: column;
                width: 100%;
                margin-bottom: 15px;
            }

            .booking-icon-modern {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .booking-right {
                flex-direction: column;
                width: 100%;
            }

            .status-badge-modern {
                width: 100%;
                text-align: center;
            }

            .btn-view-modern {
                margin-top: 10px;
            }
        }

        @media (max-width: 576px) {
            .welcome-banner {
                padding: 25px;
            }

            .welcome-title {
                font-size: 1.3rem;
            }

            .qac-content h3 {
                font-size: 1rem;
            }

            .qac-content p {
                font-size: 0.85rem;
            }
        }
    </style>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
