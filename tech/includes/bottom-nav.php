<?php
// Get current page with better detection
$current_page = '';
if (isset($_SERVER['PHP_SCRIPT_NAME'])) {
    $current_page = basename($_SERVER['PHP_SCRIPT_NAME']);
} elseif (isset($_SERVER['SCRIPT_NAME'])) {
    $current_page = basename($_SERVER['SCRIPT_NAME']);
} elseif (isset($_SERVER['REQUEST_URI'])) {
    $current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}

// Debug: Add this temporarily to see what page is detected
// echo "<!-- Current page detected: " . $current_page . " -->";

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

<!-- Real-time Lock Status Checker -->
<script>
// Check account lock status every 3 seconds
(function() {
    function checkLockStatus() {
        fetch('api-check-lock-status.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.is_locked) {
                    // Account is locked - redirect to lock page
                    window.location.href = 'account-locked.php';
                }
            })
            .catch(error => {
                console.error('Lock status check failed:', error);
            });
    }
    
    // Check immediately on page load
    checkLockStatus();
    
    // Then check every 3 seconds
    setInterval(checkLockStatus, 3000);
})();

// Optimize navigation transitions and ensure active states
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const currentPath = window.location.pathname;
    const currentFile = currentPath.split('/').pop();
    
    // Ensure active state is properly applied
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && !href.startsWith('tel:') && !href.startsWith('http') && !item.target) {
            const linkFile = href.split('/').pop();
            
            // Check if this nav item should be active
            if (linkFile === currentFile || 
                (linkFile === 'dashboard.php' && (currentFile === 'index.php' || currentFile.includes('dashboard'))) ||
                (linkFile === 'completed-bookings.php' && currentFile.includes('completed')) ||
                (linkFile === 'my-profile.php' && (currentFile.includes('profile') || currentFile === 'change-password.php'))) {
                
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        }
        
        // Prevent transition hang during navigation
        item.addEventListener('click', function(e) {
            // Only apply to internal links (not tel: or external)
            if (this.href && !this.href.startsWith('tel:') && !this.target) {
                // Add quick visual feedback
                this.style.opacity = '0.7';
                this.style.transform = 'scale(0.95)';
                
                // Reset after short delay
                setTimeout(() => {
                    this.style.opacity = '';
                    this.style.transform = '';
                }, 100);
            }
        });
    });
});
</script>

<!-- Bottom Navigation Bar -->
<div class="bottom-nav">
    <a href="dashboard.php" class="nav-item <?php 
        echo ($current_page == 'dashboard.php' || 
              $current_page == 'index.php' || 
              strpos($current_page, 'dashboard') !== false) ? 'active' : ''; 
    ?>">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
        <?php if($new_count > 0): ?>
        <span class="badge"><?php echo $new_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="completed-bookings.php" class="nav-item <?php 
        echo ($current_page == 'completed-bookings.php' || 
              strpos($current_page, 'completed') !== false) ? 'active' : ''; 
    ?>">
        <i class="fas fa-check-circle"></i>
        <span>Completed</span>
    </a>
    <a href="../index.php" class="nav-item" target="_blank">
        <i class="fas fa-chart-line"></i>
        <span>Main Site</span>
    </a>
    <a href="my-profile.php" class="nav-item <?php 
        echo ($current_page == 'my-profile.php' || 
              $current_page == 'change-password.php' ||
              strpos($current_page, 'profile') !== false) ? 'active' : ''; 
    ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="tel:7559606925" class="nav-item">
        <i class="fas fa-phone-alt"></i>
        <span>Call Admin</span>
    </a>
</div>

<style>
/* Bottom Navigation Bar - Wide & Low Design */
.bottom-nav {
    position: fixed;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%) translateZ(0);
    width: calc(100% - 16px);
    max-width: 480px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 35%, #f093fb 70%, #f5576c 100%);
    box-shadow: 0 3px 20px rgba(102, 126, 234, 0.35), 0 1px 5px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
    padding: 3px 4px;
    z-index: 1000;
    border-radius: 15px;
    will-change: transform;
    backface-visibility: hidden;
    height: 44px;
}

/* Remove any underlines or borders from all nav elements */
.bottom-nav * {
    text-decoration: none !important;
    border: none !important;
    outline: none !important;
}

.nav-item {
    flex: 1;
    text-align: center;
    text-decoration: none !important;
    color: rgba(255, 255, 255, 0.75);
    transition: all 0.15s ease;
    padding: 3px 2px;
    position: relative;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: none;
    outline: none;
}

.nav-item:hover {
    color: white !important;
    background: rgba(255, 255, 255, 0.15);
    text-decoration: none !important;
}

.nav-item.active { 
    color: white !important;
    background: rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    text-decoration: none !important;
    font-weight: 700 !important;
}

.nav-item.active:hover {
    color: white !important;
    background: rgba(255, 255, 255, 0.4) !important;
    text-decoration: none !important;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3) !important;
}

.nav-item i {
    font-size: 14px;
    display: block;
    margin-bottom: 1px;
    text-decoration: none !important;
    border: none;
    outline: none;
}

.nav-item.active i {
    transform: scale(1.1) !important;
    filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.3)) !important;
}

.nav-item.active span:not(.badge) {
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

.nav-item span:not(.badge) {
    font-size: 7px;
    font-weight: 600;
    letter-spacing: 0.1px;
    line-height: 1;
    text-decoration: none !important;
    border: none;
    outline: none;
}

.nav-item .badge {
    position: absolute;
    top: -2px;
    right: 6px;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #856404;
    border-radius: 8px;
    padding: 1px 4px;
    font-size: 7px;
    font-weight: 900;
    min-width: 12px;
    text-align: center;
    border: 1px solid white;
    box-shadow: 0 1px 4px rgba(255, 215, 0, 0.5);
    animation: simplePulse 2s ease-in-out infinite;
}

@keyframes simplePulse {
    0%, 100% { 
        transform: scale(1);
    }
    50% { 
        transform: scale(1.1);
    }
}

/* Add padding to body to prevent content from being hidden behind bottom nav */
body {
    padding-bottom: 55px;
}

/* Tablet & Desktop Responsive */
@media (min-width: 768px) {
    .bottom-nav {
        max-width: 520px;
        bottom: 10px;
        padding: 4px 6px;
        height: 48px;
    }
    
    .nav-item {
        padding: 4px 3px;
    }
    
    .nav-item i {
        font-size: 16px;
        margin-bottom: 2px;
    }
    
    .nav-item span:not(.badge) {
        font-size: 8px;
    }
    
    body {
        padding-bottom: 60px;
    }
}
</style>
