<?php
// Get current page
$current_page = isset($_SERVER['PHP_SCRIPT_NAME']) ? basename($_SERVER['PHP_SCRIPT_NAME']) : '';

// Get technician ID
$tech_id = isset($_SESSION['t_id']) ? $_SESSION['t_id'] : 0;

// Get new bookings count only
$new_count = 0;
if($tech_id > 0) {
    $new_count_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Pending'";
    $stmt_new = $mysqli->prepare($new_count_query);
    $stmt_new->bind_param('i', $tech_id);
    $stmt_new->execute();
    $new_count_result = $stmt_new->get_result();
    $new_count_data = $new_count_result->fetch_object();
    $new_count = $new_count_data ? $new_count_data->count : 0;
}
?>

<!-- Bottom Navigation Bar -->
<div class="bottom-nav">
    <a href="dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i>
        <span>Dashboard</span>
        <?php if($new_count > 0): ?>
        <span class="badge"><?php echo $new_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="completed-bookings.php" class="nav-item <?php echo ($current_page == 'completed-bookings.php') ? 'active' : ''; ?>">
        <i class="fas fa-check-circle"></i>
        <span>Completed</span>
    </a>
    <a href="../index.php" class="nav-item" target="_blank">
        <i class="fas fa-home"></i>
        <span>Main Site</span>
    </a>
    <a href="my-profile.php" class="nav-item <?php echo ($current_page == 'my-profile.php') ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="tel:7559606925" class="nav-item">
        <i class="fas fa-phone-alt"></i>
        <span>Call Admin</span>
    </a>
</div>

<style>
/* Bottom Navigation Bar - Optimized Performance */
.bottom-nav {
    position: fixed;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%) translateZ(0);
    width: calc(100% - 16px);
    max-width: 600px;
    background: linear-gradient(135deg, #EEBD89 0%, #F5A8B8 35%, #E87BA8 70%, #D13ABD 100%);
    box-shadow: 0 4px 20px rgba(238, 189, 137, 0.4);
    display: flex;
    justify-content: space-around;
    padding: 8px 6px;
    z-index: 1000;
    border-radius: 20px;
    will-change: transform;
    backface-visibility: hidden;
}

.nav-item {
    flex: 1;
    text-align: center;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.9);
    transition: all 0.2s ease;
    padding: 6px 4px;
    position: relative;
    border-radius: 12px;
}

.nav-item:hover {
    color: white;
    background: rgba(255, 255, 255, 0.2);
}

.nav-item.active { 
    color: white;
    background: rgba(255, 255, 255, 0.25);
}

.nav-item i {
    font-size: 22px;
    display: block;
    margin-bottom: 3px;
    transition: all 0.3s ease;
    filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.2));
}

.nav-item:hover i {
    transform: scale(1.15) translateY(-2px);
    filter: drop-shadow(0 4px 6px rgba(255, 255, 255, 0.3));
}

.nav-item.active i {
    transform: scale(1.2);
    filter: drop-shadow(0 3px 8px rgba(255, 255, 255, 0.5));
    animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% { 
        transform: scale(1.2);
    }
    50% { 
        transform: scale(1.25);
    }
}

.nav-item span:not(.badge) {
    font-size: 10px;
    font-weight: 700;
    display: block;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.nav-item .badge {
    position: absolute;
    top: 2px;
    right: 8px;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #856404;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 900;
    min-width: 18px;
    text-align: center;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(255, 215, 0, 0.5);
    animation: simplePulse 2s ease-in-out infinite;
}

@keyframes simplePulse {
    0%, 100% { 
        transform: scale(1);
    }
    50% { 
        transform: scale(1.1);
    }
}

/* Add padding to body to prevent content from being hidden behind bottom nav */
body {
    padding-bottom: 70px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .bottom-nav {
        max-width: 550px;
        bottom: 10px;
    }
    
    .bottom-nav .nav-item i {
        font-size: 20px;
    }
    
    .bottom-nav .nav-item span:not(.badge) {
        font-size: 10px;
    }
}

/* Desktop view - show with better styling */
@media (min-width: 769px) {
    .bottom-nav {
        display: flex;
        padding: 12px 0;
    }
    
    .bottom-nav .nav-item {
        padding: 10px 20px;
        max-width: 150px;
    }
    
    .bottom-nav .nav-item i {
        font-size: 24px;
    }
    
    .bottom-nav .nav-item span:not(.badge) {
        font-size: 12px;
    }
    
    body {
        padding-bottom: 90px;
    }
}
</style>
