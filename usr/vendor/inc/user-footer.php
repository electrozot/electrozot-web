<?php
// Determine active page
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'user-dashboard.php');
$is_book = (strpos($current_page, 'book-service') !== false || $current_page == 'book-custom-service.php');
$is_orders = (strpos($current_page, 'manage-booking') !== false || strpos($current_page, 'track-booking') !== false || strpos($current_page, 'booking-details') !== false);
$is_profile = (strpos($current_page, 'profile') !== false || strpos($current_page, 'view-profile') !== false);
?>
<div class="bottom-nav">
    <a href="user-dashboard.php" class="nav-item <?php echo $is_home ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="book-service-step1.php" class="nav-item <?php echo $is_book ? 'active' : ''; ?>">
        <i class="fas fa-calendar-plus"></i>
        <span>Book</span>
    </a>
    <a href="user-manage-booking.php" class="nav-item <?php echo $is_orders ? 'active' : ''; ?>">
        <i class="fas fa-list-alt"></i>
        <span>Orders</span>
    </a>
    <a href="user-view-profile.php" class="nav-item <?php echo $is_profile ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
</div>
