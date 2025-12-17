<div class="bottom-nav">
    <a href="user-dashboard.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'user-dashboard.php') ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="book-service-step1.php" class="nav-item <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'book-') === 0) ? 'active' : ''; ?>">
        <i class="fas fa-calendar-plus"></i>
        <span>Book</span>
    </a>
    <a href="user-manage-booking.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'user-manage-booking.php') ? 'active' : ''; ?>">
        <i class="fas fa-list-alt"></i>
        <span>Orders</span>
    </a>
    <a href="user-view-profile.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'user-view-profile.php') ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="../index.php" class="nav-item">
        <i class="fas fa-store"></i>
        <span>Main</span>
    </a>
</div>