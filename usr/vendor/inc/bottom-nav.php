<?php
// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="bottom-nav">
    <a href="user-dashboard.php" class="nav-item <?php echo ($current_page == 'user-dashboard.php') ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="book-service-step1.php" class="nav-item <?php echo (strpos($current_page, 'book-service') !== false || strpos($current_page, 'book-custom') !== false) ? 'active' : ''; ?>">
        <i class="fas fa-calendar-plus"></i>
        <span>Book</span>
    </a>
    <a href="user-manage-booking.php" class="nav-item <?php echo (strpos($current_page, 'user-manage-booking') !== false || strpos($current_page, 'user-track-booking') !== false || strpos($current_page, 'user-view-booking') !== false) ? 'active' : ''; ?>">
        <i class="fas fa-list-alt"></i>
        <span>Orders</span>
    </a>
    <a href="user-view-profile.php" class="nav-item <?php echo (strpos($current_page, 'user-view-profile') !== false || strpos($current_page, 'user-update-profile') !== false || strpos($current_page, 'user-change-pwd') !== false) ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="../index.php" class="nav-item">
        <i class="fas fa-store"></i>
        <span>Main</span>
    </a>
</div>