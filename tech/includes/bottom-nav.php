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
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
        <?php if($new_count > 0): ?>
        <span class="badge"><?php echo $new_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="completed-bookings.php" class="nav-item <?php echo ($current_page == 'completed-bookings.php') ? 'active' : ''; ?>">
        <i class="fas fa-check-circle"></i>
        <span>Completed</span>
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
/* Bottom Navigation Bar - Matching User Dashboard Style */
.bottom-nav {
    position: fixed;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 8px);
    max-width: 600px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 3px 20px rgba(102, 126, 234, 0.35), 0 1px 5px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
    padding: 2px 6px;
    z-index: 1000;
    border-radius: 20px;
}

.nav-item {
    flex: 1;
    text-align: center;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.75);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 4px 4px;
    position: relative;
    border-radius: 15px;
    overflow: hidden;
}

.nav-item::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.nav-item:active::before {
    width: 100px;
    height: 100px;
}

.nav-item:hover {
    color: white;
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.nav-item.active { 
    color: white;
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2), inset 0 1px 3px rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.nav-item i {
    font-size: 18px;
    display: block;
    margin-bottom: 1px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.nav-item:hover i {
    transform: scale(1.2) rotate(5deg);
}

.nav-item.active i {
    animation: bounceRotate 0.6s ease;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

@keyframes bounceRotate {
    0% { transform: scale(1) rotate(0deg); }
    25% { transform: scale(1.2) rotate(-10deg); }
    50% { transform: scale(1.3) rotate(10deg); }
    75% { transform: scale(1.2) rotate(-5deg); }
    100% { transform: scale(1.1) rotate(0deg); }
}

.nav-item span:not(.badge) {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.3px;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.nav-item:hover span:not(.badge) {
    transform: scale(1.05);
    letter-spacing: 0.5px;
}

.nav-item.active span:not(.badge) {
    font-weight: 900;
    transform: scale(1.1);
}

.nav-item .badge {
    position: absolute;
    top: 0;
    right: 4px;
    background: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%);
    color: white;
    border-radius: 12px;
    padding: 3px 6px;
    font-size: 10px;
    font-weight: 900;
    min-width: 18px;
    text-align: center;
    animation: pulseBounce 2s infinite;
    box-shadow: 0 3px 10px rgba(255, 71, 87, 0.5), 0 0 0 3px rgba(255, 71, 87, 0.2);
    border: 2px solid white;
    z-index: 2;
}

@keyframes pulseBounce {
    0%, 100% { 
        transform: scale(1) translateY(0); 
        box-shadow: 0 3px 10px rgba(255, 71, 87, 0.5), 0 0 0 3px rgba(255, 71, 87, 0.2);
    }
    25% { 
        transform: scale(1.15) translateY(-2px); 
        box-shadow: 0 5px 15px rgba(255, 71, 87, 0.7), 0 0 0 5px rgba(255, 71, 87, 0.3);
    }
    50% { 
        transform: scale(1.2) translateY(-3px); 
        box-shadow: 0 6px 20px rgba(255, 71, 87, 0.8), 0 0 0 6px rgba(255, 71, 87, 0.4);
    }
    75% { 
        transform: scale(1.15) translateY(-2px); 
        box-shadow: 0 5px 15px rgba(255, 71, 87, 0.7), 0 0 0 5px rgba(255, 71, 87, 0.3);
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
