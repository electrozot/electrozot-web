<?php
// Get current page
$current_page = isset($_SERVER['PHP_SCRIPT_NAME']) ? basename($_SERVER['PHP_SCRIPT_NAME']) : '';

// Get technician ID
$tech_id = isset($_SESSION['t_id']) ? $_SESSION['t_id'] : 0;

// Get new bookings count
$new_count = 0;
$completed_count = 0;
if($tech_id > 0) {
    $new_count_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Pending'";
    $stmt_new = $mysqli->prepare($new_count_query);
    $stmt_new->bind_param('i', $tech_id);
    $stmt_new->execute();
    $new_count_result = $stmt_new->get_result();
    $new_count_data = $new_count_result->fetch_object();
    $new_count = $new_count_data ? $new_count_data->count : 0;
    
    // Get completed bookings count
    $completed_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Completed'";
    $stmt_completed = $mysqli->prepare($completed_query);
    $stmt_completed->bind_param('i', $tech_id);
    $stmt_completed->execute();
    $completed_result = $stmt_completed->get_result();
    $completed_data = $completed_result->fetch_object();
    $completed_count = $completed_data ? $completed_data->count : 0;
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
        <?php if($completed_count > 0): ?>
        <span class="badge" style="background: #2ecc71;"><?php echo $completed_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="my-profile.php" class="nav-item <?php echo ($current_page == 'my-profile.php') ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="logout.php" class="nav-item">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</div>

<style>
/* Bottom Navigation Bar */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 8px 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
    z-index: 1000;
    border-top: 3px solid rgba(255,255,255,0.2);
}

.bottom-nav .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    flex: 1;
    max-width: 120px;
}

.bottom-nav .nav-item i {
    font-size: 22px;
    margin-bottom: 4px;
    transition: transform 0.3s ease;
}

.bottom-nav .nav-item span:not(.badge) {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bottom-nav .nav-item:hover {
    color: white;
    background: rgba(255,255,255,0.1);
}

.bottom-nav .nav-item:hover i {
    transform: scale(1.2) translateY(-2px);
}

.bottom-nav .nav-item.active {
    color: white;
    background: rgba(255,255,255,0.2);
}

.bottom-nav .nav-item.active i {
    transform: scale(1.1);
}

.bottom-nav .nav-item .badge {
    position: absolute;
    top: 2px;
    right: 8px;
    background: #ff4757;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 18px;
    text-align: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Add padding to body to prevent content from being hidden behind bottom nav */
body {
    padding-bottom: 80px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .bottom-nav .nav-item {
        padding: 6px 8px;
        min-width: 50px;
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
