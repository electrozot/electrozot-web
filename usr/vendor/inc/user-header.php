<?php
// Get user info if not already loaded
if (!isset($user)) {
    $aid = $_SESSION['u_id'];
    $query = "SELECT * FROM tms_user WHERE u_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $aid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_object();
}
?>
<div class="top-header">
    <div class="header-content">
        <div class="brand-section">
            <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
            <div class="brand-text">
                <h2>Electrozot</h2>
                <p>We make perfect</p>
            </div>
        </div>
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
