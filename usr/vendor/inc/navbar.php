<?php
// Get user info for navbar if not already loaded
if (!isset($user) || !$user) {
    $user_query = "SELECT u_fname, u_lname FROM tms_user WHERE u_id = ?";
    $user_stmt = $mysqli->prepare($user_query);
    $user_stmt->bind_param('i', $_SESSION['u_id']);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_object();
}
?>
<div class="top-header">
    <div class="header-content">
        <a href="../index.php" class="brand-section" style="text-decoration: none; color: white;">
            <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
            <div class="brand-text">
                <h2>Electrozot</h2>
                <p>We make perfect</p>
            </div>
        </a>
        <div class="user-section">
            <div class="user-name"><?php echo htmlspecialchars($user->u_fname); ?></div>
            <div class="header-icons">
                <a href="user-view-profile.php" class="header-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</div>