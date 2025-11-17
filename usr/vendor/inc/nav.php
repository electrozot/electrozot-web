<?php
// Get logged-in user's name
$nav_user_id = $_SESSION['u_id'];
$nav_user_query = "SELECT u_fname, u_lname FROM tms_user WHERE u_id = ?";
$nav_user_stmt = $mysqli->prepare($nav_user_query);
$nav_user_stmt->bind_param('i', $nav_user_id);
$nav_user_stmt->execute();
$nav_user_result = $nav_user_stmt->get_result();
$nav_user = $nav_user_result->fetch_object();
$user_display_name = $nav_user ? $nav_user->u_fname . ' ' . $nav_user->u_lname : 'User';
?>
<nav class="navbar navbar-expand static-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">

    <a class="navbar-brand mr-1" href="user-dashboard.php" style="display: flex; align-items: center; gap: 12px; padding: 8px 15px; background: rgba(255,255,255,0.15); border-radius: 12px; backdrop-filter: blur(10px); border: 2px solid rgba(255,255,255,0.2);">
        <div class="logo-container" style="background: white; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); padding: 5px;">
            <img src="../vendor/EZlogonew.png" alt="Electrozot Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div style="display: flex; flex-direction: column; line-height: 1.3;">
            <span style="font-size: 18px; font-weight: 900; color: white; text-shadow: 2px 2px 6px rgba(0,0,0,0.4); letter-spacing: 0.5px;">
                <span style="color: #ffd700;">Electrozot</span> User
            </span>
            <span style="font-size: 11px; color: rgba(255,255,255,0.95); font-weight: 600; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">
                <i class="fas fa-user-shield" style="font-size: 10px;"></i> <?php echo htmlspecialchars($user_display_name); ?>
            </span>
        </div>
    </a>

    <button class="btn btn-link btn-sm order-1 order-sm-0" id="sidebarToggle" href="#" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <!-- <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
        <div class="input-group-append">
          <button class="btn btn-primary" type="button">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div> -->
    </form>

    <!-- Navbar -->
    <ul class="navbar-nav ml-auto ml-md-0">


        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white;">
                <i class="fas fa-user-circle fa-fw" style="font-size: 1.5rem;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-lg" aria-labelledby="userDropdown" style="border-radius: 10px; border: none;">
                <a class="dropdown-item" href="user-view-profile.php"><i class="fas fa-user"></i> My Profile</a>
                <a class="dropdown-item" href="user-track-booking.php"><i class="fas fa-map-marker-alt"></i> Track Orders</a>
                <a class="dropdown-item" href="user-change-pwd.php"><i class="fas fa-key"></i> Change Password</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </li>
    </ul>



</nav>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-sign-out-alt"></i> Ready to Leave?</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer" style="border: none;">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" href="user-logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<style>
/* Light Sidebar Styling */
.sidebar {
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%) !important;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar .nav-link {
    color: #495057 !important;
    font-weight: 600;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    transform: translateX(5px);
    border-radius: 10px;
    margin: 0 10px;
}

.sidebar .nav-link i {
    color: #667eea;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover i {
    color: white !important;
}

.sidebar .nav-item.active .nav-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-radius: 10px;
    margin: 0 10px;
}

.sidebar .nav-item.active .nav-link i {
    color: white !important;
}

.sidebar .dropdown-menu {
    background: white;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.sidebar .dropdown-item {
    color: #495057;
    transition: all 0.3s ease;
}

.sidebar .dropdown-item:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
}

.sidebar .dropdown-item i {
    color: #667eea;
    margin-right: 8px;
}

.sidebar .dropdown-item:hover i {
    color: white;
}

.sidebar .dropdown-header {
    color: #667eea;
    font-weight: 700;
}
</style>