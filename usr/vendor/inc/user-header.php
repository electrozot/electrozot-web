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
        <a href="../index.php" class="brand-section" style="text-decoration: none; color: white;">
            <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
            <div class="brand-text">
                <h2>Electrozot</h2>
                <p>We make perfect</p>
            </div>
        </a>
        <div class="user-section">
            <div class="header-icons">
                <a href="user-view-profile.php" class="header-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</div>