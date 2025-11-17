<?php
// Get technician stats for navbar
$t_id = $_SESSION['t_id'];

// Ensure required columns exist in tms_technician table
try {
    // Check if t_phone column exists
    $colCheck = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tms_technician' AND COLUMN_NAME = 't_phone'");
    if($colCheck) {
        $hasPhone = $colCheck->fetch_object();
        if(!$hasPhone || intval($hasPhone->c) === 0) {
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_phone VARCHAR(20) DEFAULT ''");
        }
    }
    
    // Check if t_email column exists
    $colCheck2 = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tms_technician' AND COLUMN_NAME = 't_email'");
    if($colCheck2) {
        $hasEmail = $colCheck2->fetch_object();
        if(!$hasEmail || intval($hasEmail->c) === 0) {
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_email VARCHAR(100) DEFAULT ''");
        }
    }
    
    // Check if t_addr column exists
    $colCheck3 = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tms_technician' AND COLUMN_NAME = 't_addr'");
    if($colCheck3) {
        $hasAddr = $colCheck3->fetch_object();
        if(!$hasAddr || intval($hasAddr->c) === 0) {
            $mysqli->query("ALTER TABLE tms_technician ADD COLUMN t_addr TEXT DEFAULT ''");
        }
    }
} catch(Exception $e) { 
    // Ignore errors
}

// Get technician details including pincode
$tech_query = "SELECT t_phone, t_email, t_addr FROM tms_technician WHERE t_id = ?";
$stmt_tech = $mysqli->prepare($tech_query);
$t_phone = '';
$t_pincode = '';
if($stmt_tech) {
    $stmt_tech->bind_param('i', $t_id);
    $stmt_tech->execute();
    $tech_result = $stmt_tech->get_result();
    $tech_data = $tech_result->fetch_object();
    if($tech_data) {
        $t_phone = isset($tech_data->t_phone) ? $tech_data->t_phone : '';
        // Extract pincode from address (assuming it's a 6-digit number)
        if(!empty($tech_data->t_addr)) {
            preg_match('/\b\d{6}\b/', $tech_data->t_addr, $matches);
            if(!empty($matches)) {
                $t_pincode = $matches[0];
            }
        }
    }
}

// Get pending bookings count
$pending_query = "SELECT COUNT(*) as pending FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Pending'";
$stmt_pending = $mysqli->prepare($pending_query);
$nav_pending = 0;
if($stmt_pending) {
    $stmt_pending->bind_param('i', $t_id);
    $stmt_pending->execute();
    $pending_result = $stmt_pending->get_result();
    $pending_data = $pending_result->fetch_object();
    $nav_pending = $pending_data->pending;
}

// Get in progress bookings count
$progress_query = "SELECT COUNT(*) as progress FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'In Progress'";
$stmt_prog = $mysqli->prepare($progress_query);
$nav_progress = 0;
if($stmt_prog) {
    $stmt_prog->bind_param('i', $t_id);
    $stmt_prog->execute();
    $prog_result = $stmt_prog->get_result();
    $prog_data = $prog_result->fetch_object();
    $nav_progress = $prog_data->progress;
}

// Get total bookings count
$total_query = "SELECT COUNT(*) as total FROM tms_service_booking WHERE sb_technician_id = ?";
$stmt_total = $mysqli->prepare($total_query);
$nav_total = 0;
if($stmt_total) {
    $stmt_total->bind_param('i', $t_id);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();
    $total_data = $total_result->fetch_object();
    $nav_total = $total_data->total;
}

// Get completed bookings count
$completed_query = "SELECT COUNT(*) as completed FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Completed'";
$stmt_comp = $mysqli->prepare($completed_query);
$nav_completed = 0;
if($stmt_comp) {
    $stmt_comp->bind_param('i', $t_id);
    $stmt_comp->execute();
    $comp_result = $stmt_comp->get_result();
    $comp_data = $comp_result->fetch_object();
    $nav_completed = $comp_data->completed;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top tech-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-bolt"></i>
            <span>Electrozot</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link nav-btn nav-btn-red <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>

            <!-- Search Bar -->
            <form class="form-inline my-2 my-lg-0 mx-3 search-form" action="search-booking.php" method="GET">
                <div class="search-wrapper">
                    <input class="form-control search-input" type="search" name="phone" placeholder="Search by mobile number..." aria-label="Search" required>
                    <button class="btn search-btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav ml-auto nav-right-group">
                <li class="nav-item">
                    <a class="nav-link nav-btn-compact nav-btn-purple notification-btn <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>" href="notifications.php">
                        <i class="fas fa-bell"></i>
                        <span class="btn-label">Notifications</span>
                        <?php 
                        $total_notifications = $nav_pending + $nav_progress;
                        if($total_notifications > 0): 
                        ?>
                            <span class="nav-badge-compact notification-badge"><?php echo $total_notifications; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn-compact nav-btn-yellow <?php echo basename($_SERVER['PHP_SELF']) == 'new-bookings.php' ? 'active' : ''; ?>" href="new-bookings.php">
                        <i class="fas fa-calendar-plus"></i>
                        <span class="btn-label">New</span>
                        <?php if($nav_pending > 0): ?>
                            <span class="nav-badge-compact"><?php echo $nav_pending; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn-compact nav-btn-pink <?php echo basename($_SERVER['PHP_SELF']) == 'my-bookings.php' ? 'active' : ''; ?>" href="my-bookings.php">
                        <i class="fas fa-clock"></i>
                        <span class="btn-label">Pending</span>
                        <?php if($nav_progress > 0): ?>
                            <span class="nav-badge-compact"><?php echo $nav_progress; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn-compact nav-btn-green <?php echo basename($_SERVER['PHP_SELF']) == 'completed-bookings.php' ? 'active' : ''; ?>" href="completed-bookings.php">
                        <i class="fas fa-check-circle"></i>
                        <span class="btn-label">Completed</span>
                        <?php if($nav_completed > 0): ?>
                            <span class="nav-badge-compact"><?php echo $nav_completed; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item nav-divider"></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-info-nav" href="#" id="userDropdown" data-toggle="dropdown">
                        <div class="user-avatar-nav">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details-nav">
                            <span class="user-name">
                                <?php echo htmlspecialchars($_SESSION['t_name']); ?>
                                <?php if(!empty($t_pincode)): ?>
                                    <span class="user-pincode">PIN: <?php echo htmlspecialchars($t_pincode); ?></span>
                                <?php endif; ?>
                            </span>
                            <?php if(!empty($t_phone)): ?>
                                <span class="user-phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($t_phone); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header">
                            <strong><?php echo htmlspecialchars($_SESSION['t_name']); ?></strong>
                            <?php if(!empty($t_pincode)): ?>
                                <span class="badge" style="background: #ffd700; color: #ff4757; font-weight: 700; margin-left: 8px;">PIN: <?php echo htmlspecialchars($t_pincode); ?></span>
                            <?php endif; ?>
                            <br>
                            <small>ID: <?php echo htmlspecialchars($_SESSION['t_id_no']); ?></small>
                            <?php if(!empty($t_phone)): ?>
                                <small class="d-block mt-1"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($t_phone); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="notifications.php">
                            <i class="fas fa-bell"></i> Notifications
                            <?php if($nav_pending + $nav_progress > 0): ?>
                                <span class="badge badge-warning ml-2"><?php echo $nav_pending + $nav_progress; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="change-password.php">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Quick Actions Bar -->
<?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php'): ?>
<div class="dashboard-quick-bar">
    <div class="container-fluid">
        <div class="quick-bar-content">
            <a href="notifications.php" class="quick-bar-btn qb-purple notification-quick-btn">
                <div class="qb-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="qb-info">
                    <span class="qb-count"><?php echo $nav_total; ?></span>
                    <span class="qb-label">All Notifications</span>
                </div>
                <?php if($nav_pending + $nav_progress > 0): ?>
                    <span class="quick-badge"><?php echo $nav_pending + $nav_progress; ?></span>
                <?php endif; ?>
            </a>

            <a href="new-bookings.php" class="quick-bar-btn qb-red">
                <div class="qb-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="qb-info">
                    <span class="qb-count"><?php echo $nav_pending; ?></span>
                    <span class="qb-label">New Bookings</span>
                </div>
            </a>

            <a href="dashboard.php?filter=pending" class="quick-bar-btn qb-blue">
                <div class="qb-icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="qb-info">
                    <span class="qb-count"><?php echo $nav_progress; ?></span>
                    <span class="qb-label">In Progress</span>
                </div>
            </a>

            <a href="completed-bookings.php" class="quick-bar-btn qb-green">
                <div class="qb-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="qb-info">
                    <span class="qb-count"><?php echo $nav_completed; ?></span>
                    <span class="qb-label">Completed</span>
                </div>
            </a>

            <a href="profile.php" class="quick-bar-btn qb-purple">
                <div class="qb-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="qb-info">
                    <span class="qb-count"><i class="fas fa-arrow-right"></i></span>
                    <span class="qb-label">My Profile</span>
                </div>
            </a>

            <a href="change-password.php" class="quick-bar-btn qb-pink">
                <div class="qb-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="qb-info">
                    <span class="qb-count"><i class="fas fa-arrow-right"></i></span>
                    <span class="qb-label">Security</span>
                </div>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .tech-navbar {
        background: linear-gradient(135deg, #ff4757 0%, #ffa502 50%, #ff6b9d 100%);
        box-shadow: 0 4px 20px rgba(255, 71, 87, 0.3);
        padding: 10px 0;
    }
    
    .navbar-brand {
        color: white !important;
        font-weight: 800;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        padding: 10px 20px;
        background: rgba(255,255,255,0.15);
        border-radius: 50px;
        backdrop-filter: blur(10px);
    }
    
    .navbar-brand i {
        color: #ffd700;
        margin-right: 10px;
        font-size: 1.8rem;
    }
    
    .navbar-nav {
        gap: 10px;
    }

    /* Search Bar */
    .search-form {
        margin: 0 15px;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input {
        width: 280px;
        padding: 10px 45px 10px 20px;
        border-radius: 50px;
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.15);
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .search-input::placeholder {
        color: rgba(255,255,255,0.7);
    }

    .search-input:focus {
        outline: none;
        background: rgba(255,255,255,0.25);
        border-color: white;
        box-shadow: 0 4px 15px rgba(255,255,255,0.2);
        width: 320px;
    }

    .search-btn {
        position: absolute;
        right: 5px;
        background: white;
        color: #ff4757;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .search-btn:hover {
        background: #ffd700;
        color: #ff4757;
        transform: scale(1.1);
    }

    .nav-right-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-divider {
        width: 2px;
        height: 40px;
        background: rgba(255,255,255,0.3);
        margin: 0 10px;
    }
    
    .nav-btn {
        padding: 12px 25px !important;
        border-radius: 50px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 2px solid transparent;
    }
    
    .nav-btn-red {
        background: rgba(255, 71, 87, 0.2);
        color: white !important;
        border-color: rgba(255, 71, 87, 0.3);
    }
    
    .nav-btn-red:hover, .nav-btn-red.active {
        background: #ff4757;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 71, 87, 0.4);
    }
    
    .nav-btn-yellow {
        background: rgba(255, 165, 2, 0.2);
        color: white !important;
        border-color: rgba(255, 165, 2, 0.3);
    }
    
    .nav-btn-yellow:hover, .nav-btn-yellow.active {
        background: #ffa502;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 165, 2, 0.4);
    }
    
    .nav-btn-pink {
        background: rgba(255, 107, 157, 0.2);
        color: white !important;
        border-color: rgba(255, 107, 157, 0.3);
    }
    
    .nav-btn-pink:hover, .nav-btn-pink.active {
        background: #ff6b9d;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
    }

    .nav-btn-green {
        background: rgba(17, 153, 142, 0.2);
        color: white !important;
        border-color: rgba(17, 153, 142, 0.3);
    }
    
    .nav-btn-green:hover, .nav-btn-green.active {
        background: #11998e;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
    }

    .nav-btn-purple {
        background: rgba(102, 126, 234, 0.2);
        color: white !important;
        border-color: rgba(102, 126, 234, 0.3);
    }
    
    .nav-btn-purple:hover, .nav-btn-purple.active {
        background: #667eea;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* Enhanced Notification Button */
    .notification-btn {
        position: relative;
        animation: bellShake 3s ease-in-out infinite;
    }

    .notification-btn .fa-bell {
        font-size: 1.2rem;
    }

    .notification-badge {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%) !important;
        color: #ff4757 !important;
        font-weight: 900 !important;
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.5) !important;
        animation: pulse 2s ease-in-out infinite !important;
    }

    @keyframes bellShake {
        0%, 90%, 100% { transform: rotate(0deg); }
        92%, 96% { transform: rotate(-10deg); }
        94%, 98% { transform: rotate(10deg); }
    }

    /* Quick Bar Notification Button */
    .notification-quick-btn {
        position: relative;
    }

    .notification-quick-btn .qb-icon {
        animation: bellRing 2s ease-in-out infinite;
    }

    @keyframes bellRing {
        0%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(-15deg); }
        20%, 40% { transform: rotate(15deg); }
        50% { transform: rotate(0deg); }
    }

    .quick-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        color: #ff4757;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 900;
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.5);
        animation: pulse 2s ease-in-out infinite;
        z-index: 10;
    }

    .nav-badge {
        background: white;
        color: #ff4757;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 900;
        margin-left: 5px;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Compact Navigation Buttons */
    .nav-btn-compact {
        padding: 10px 20px !important;
        border-radius: 50px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 2px solid transparent;
        position: relative;
    }

    .nav-btn-compact i {
        font-size: 1.1rem;
    }

    .nav-btn-compact .btn-label {
        font-size: 0.9rem;
        font-weight: 700;
    }

    .nav-badge-compact {
        background: white;
        color: #ff4757;
        padding: 2px 8px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 900;
        margin-left: 3px;
        animation: pulse 2s ease-in-out infinite;
        position: absolute;
        top: -5px;
        right: -5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .user-info-nav {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 20px !important;
        background: rgba(255,255,255,0.15);
        border-radius: 50px;
        color: white !important;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .user-info-nav:hover {
        background: rgba(255,255,255,0.25);
    }
    
    .user-avatar-nav {
        width: 45px;
        height: 45px;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffd700;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .user-details-nav {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.3;
    }

    .user-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .user-pincode {
        background: #ffd700;
        color: #ff4757;
        padding: 2px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        animation: glow 2s ease-in-out infinite;
    }

    @keyframes glow {
        0%, 100% { box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3); }
        50% { box-shadow: 0 2px 15px rgba(255, 215, 0, 0.6); }
    }

    .user-phone {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.85);
        font-weight: 600;
    }

    .user-phone i {
        font-size: 0.7rem;
        margin-right: 3px;
    }
    
    .dropdown-menu {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        padding: 0;
        margin-top: 10px;
    }
    
    .dropdown-header {
        padding: 20px;
        background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .dropdown-item {
        padding: 12px 20px;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
        color: white;
    }
    
    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
        color: white !important;
    }
    
    /* Dashboard Quick Bar */
    .dashboard-quick-bar {
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 20px 0;
        margin-bottom: 0;
        border-bottom: 3px solid #f0f0f0;
    }

    .quick-bar-content {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding: 5px;
    }

    .quick-bar-content::-webkit-scrollbar {
        height: 6px;
    }

    .quick-bar-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .quick-bar-content::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #ff4757, #ffa502);
        border-radius: 10px;
    }

    .quick-bar-btn {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 30px;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        border: 3px solid transparent;
    }

    .quick-bar-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.1;
        transition: opacity 0.3s ease;
    }

    .quick-bar-btn:hover {
        transform: translateY(-5px) scale(1.05);
        text-decoration: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .quick-bar-btn:hover::before {
        opacity: 0.2;
    }

    .qb-red {
        background: linear-gradient(135deg, rgba(255, 71, 87, 0.1), rgba(255, 99, 72, 0.1));
        border-color: rgba(255, 71, 87, 0.3);
    }

    .qb-red::before {
        background: linear-gradient(135deg, #ff4757, #ff6348);
    }

    .qb-red:hover {
        border-color: #ff4757;
    }

    .qb-yellow {
        background: linear-gradient(135deg, rgba(255, 165, 2, 0.1), rgba(255, 99, 72, 0.1));
        border-color: rgba(255, 165, 2, 0.3);
    }

    .qb-yellow::before {
        background: linear-gradient(135deg, #ffa502, #ff6348);
    }

    .qb-yellow:hover {
        border-color: #ffa502;
    }

    .qb-blue {
        background: linear-gradient(135deg, rgba(0, 180, 219, 0.1), rgba(0, 131, 176, 0.1));
        border-color: rgba(0, 180, 219, 0.3);
    }

    .qb-blue::before {
        background: linear-gradient(135deg, #00b4db, #0083b0);
    }

    .qb-blue:hover {
        border-color: #00b4db;
    }

    .qb-green {
        background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1));
        border-color: rgba(17, 153, 142, 0.3);
    }

    .qb-green::before {
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }

    .qb-green:hover {
        border-color: #11998e;
    }

    .qb-purple {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        border-color: rgba(102, 126, 234, 0.3);
    }

    .qb-purple::before {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .qb-purple:hover {
        border-color: #667eea;
    }

    .qb-pink {
        background: linear-gradient(135deg, rgba(255, 107, 157, 0.1), rgba(255, 71, 87, 0.1));
        border-color: rgba(255, 107, 157, 0.3);
    }

    .qb-pink::before {
        background: linear-gradient(135deg, #ff6b9d, #ff4757);
    }

    .qb-pink:hover {
        border-color: #ff6b9d;
    }

    .qb-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
    }

    .qb-red .qb-icon {
        background: linear-gradient(135deg, #ff4757, #ff6348);
        box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
    }

    .qb-yellow .qb-icon {
        background: linear-gradient(135deg, #ffa502, #ff6348);
        box-shadow: 0 5px 15px rgba(255, 165, 2, 0.3);
    }

    .qb-blue .qb-icon {
        background: linear-gradient(135deg, #00b4db, #0083b0);
        box-shadow: 0 5px 15px rgba(0, 180, 219, 0.3);
    }

    .qb-green .qb-icon {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
    }

    .qb-purple .qb-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .qb-pink .qb-icon {
        background: linear-gradient(135deg, #ff6b9d, #ff4757);
        box-shadow: 0 5px 15px rgba(255, 107, 157, 0.3);
    }

    .qb-info {
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }

    .qb-count {
        font-size: 2rem;
        font-weight: 900;
        color: #2d3748;
        line-height: 1;
        margin-bottom: 5px;
    }

    .qb-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 700;
        white-space: nowrap;
    }

    @media (max-width: 991px) {
        .navbar-nav {
            margin-top: 15px;
        }

        .search-form {
            width: 100%;
            margin: 15px 0;
        }

        .search-wrapper {
            width: 100%;
        }

        .search-input {
            width: 100%;
        }

        .search-input:focus {
            width: 100%;
        }

        .nav-right-group {
            flex-direction: column;
            width: 100%;
        }

        .nav-divider {
            width: 100%;
            height: 2px;
            margin: 10px 0;
        }
        
        .nav-btn {
            margin: 5px 0;
            justify-content: center;
            width: 100%;
        }

        .nav-btn-compact {
            width: 100%;
            justify-content: center;
            margin: 5px 0;
        }
        
        .user-info-nav {
            justify-content: center;
            margin-top: 10px;
            width: 100%;
        }

        .dashboard-quick-bar {
            padding: 15px 0;
        }

        .quick-bar-btn {
            padding: 15px 20px;
        }

        .qb-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }

        .qb-count {
            font-size: 1.5rem;
        }

        .qb-label {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .quick-bar-btn {
            padding: 12px 15px;
            gap: 10px;
        }

        .qb-icon {
            width: 45px;
            height: 45px;
            font-size: 1.3rem;
        }

        .qb-count {
            font-size: 1.3rem;
        }

        .qb-label {
            font-size: 0.75rem;
        }
    }
</style>
