<nav class="navbar navbar-expand-lg navbar-dark sticky-top tech-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-bolt"></i>
            <span>Electrozot</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link nav-btn nav-btn-red <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn nav-btn-yellow <?php echo basename($_SERVER['PHP_SELF']) == 'new-bookings.php' ? 'active' : ''; ?>" href="new-bookings.php">
                        <i class="fas fa-bell"></i>
                        <span>New Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn nav-btn-pink <?php echo basename($_SERVER['PHP_SELF']) == 'my-bookings.php' ? 'active' : ''; ?>" href="my-bookings.php">
                        <i class="fas fa-clipboard-list"></i>
                        <span>All Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn nav-btn-red <?php echo basename($_SERVER['PHP_SELF']) == 'completed-bookings.php' ? 'active' : ''; ?>" href="completed-bookings.php">
                        <i class="fas fa-check-circle"></i>
                        <span>Completed</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn nav-btn-yellow <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-info-nav" href="#" id="userDropdown" data-toggle="dropdown">
                        <div class="user-avatar-nav">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['t_name']); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header">
                            <strong><?php echo htmlspecialchars($_SESSION['t_name']); ?></strong><br>
                            <small>ID: <?php echo htmlspecialchars($_SESSION['t_id_no']); ?></small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="change-password.php">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .tech-navbar {
        background: linear-gradient(135deg, #ff4757 0%, #ffa502 50%, #ff6b9d 100%);
        box-shadow: 0 4px 20px rgba(255, 71, 87, 0.3);
        padding: 10px 0;
    }
    
    .navbar-brand {
        color: white !important;
        font-weight: 800;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        padding: 10px 20px;
        background: rgba(255,255,255,0.15);
        border-radius: 50px;
        backdrop-filter: blur(10px);
    }
    
    .navbar-brand i {
        color: #ffd700;
        margin-right: 10px;
        font-size: 1.8rem;
    }
    
    .navbar-nav {
        gap: 10px;
    }
    
    .nav-btn {
        padding: 12px 25px !important;
        border-radius: 50px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 2px solid transparent;
    }
    
    .nav-btn-red {
        background: rgba(255, 71, 87, 0.2);
        color: white !important;
        border-color: rgba(255, 71, 87, 0.3);
    }
    
    .nav-btn-red:hover, .nav-btn-red.active {
        background: #ff4757;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 71, 87, 0.4);
    }
    
    .nav-btn-yellow {
        background: rgba(255, 165, 2, 0.2);
        color: white !important;
        border-color: rgba(255, 165, 2, 0.3);
    }
    
    .nav-btn-yellow:hover, .nav-btn-yellow.active {
        background: #ffa502;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 165, 2, 0.4);
    }
    
    .nav-btn-pink {
        background: rgba(255, 107, 157, 0.2);
        color: white !important;
        border-color: rgba(255, 107, 157, 0.3);
    }
    
    .nav-btn-pink:hover, .nav-btn-pink.active {
        background: #ff6b9d;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
    }
    
    .user-info-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px !important;
        background: rgba(255,255,255,0.15);
        border-radius: 50px;
        color: white !important;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    
    .user-avatar-nav {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffd700;
        font-size: 1.2rem;
    }
    
    .dropdown-menu {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        padding: 0;
        margin-top: 10px;
    }
    
    .dropdown-header {
        padding: 20px;
        background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .dropdown-item {
        padding: 12px 20px;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
        color: white;
    }
    
    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
        color: white !important;
    }
    
    @media (max-width: 991px) {
        .navbar-nav {
            margin-top: 15px;
        }
        
        .nav-btn {
            margin: 5px 0;
            justify-content: center;
        }
        
        .user-info-nav {
            justify-content: center;
            margin-top: 10px;
        }
    }
</style>
