<?php
// Ensure no output before this point - nav.php must be included after all header() calls
?>
<nav class="navbar navbar-expand navbar-dark static-top" style="background: linear-gradient(90deg, #6B7FDB 0%, #8B7FC7 100%); padding: 0.3rem 1rem; min-height: 50px;">

     <a class="navbar-brand mr-1 d-flex align-items-center" href="admin-dashboard.php" style="gap: 6px; padding: 4px 10px;">
         <img src="../vendor/EZlogonew.png" alt="Electrozot Logo" class="navbar-logo" style="height: 45px; width: auto; transition: transform 0.3s ease; object-fit: contain;">
         <div class="d-none d-md-flex flex-column">
             <span style="font-size: 1.3rem; line-height: 1.1; font-weight: 700; color: #ffffff; text-shadow: 1px 1px 3px rgba(0,0,0,0.2);">Electrozot</span>
             <small class="navbar-tagline" style="font-size: 0.65rem; font-weight: 600; font-style: italic; line-height: 1; color: #f0f0f0; letter-spacing: 0.3px; opacity: 0.95;">We Make Perfect</small>
         </div>
     </a>

     <button class="btn btn-link btn-sm order-1 order-sm-0" id="sidebarToggle" href="#" style="padding: 4px 8px; color: #ffffff;">
         <i class="fas fa-bars" style="font-size: 16px;"></i>
     </button>

     <!-- Quick Booking Button - Left Side -->
     <div class="d-none d-md-block" style="margin-left: 80px;">
         <a href="admin-quick-booking.php" class="btn shadow-sm quick-booking-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; padding: 10px 30px; border-radius: 25px; font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
             <i class="fas fa-phone-alt" style="font-size: 16px;"></i>
             <span>Quick Booking</span>
         </a>
     </div>
     
     <!-- Search - Right Side -->
     <div class="d-none d-md-flex" style="align-items: center; margin-left: auto; margin-right: 20px;">
         
         <!-- Global Search Feature -->
         <div class="search-container" style="position: relative;">
             <i class="fas fa-search search-icon" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #6B7FDB; font-size: 14px; pointer-events: none; transition: all 0.3s ease; z-index: 1;"></i>
             <input type="text" 
                    id="globalSearch" 
                    class="global-search-input"
                    placeholder="Search features... (Ctrl+K)" 
                    style="background: #ffffff; 
                           border: 2px solid #e2e8f0; 
                           padding: 11px 40px 11px 40px; 
                           border-radius: 25px; 
                           width: 280px; 
                           font-size: 14px; 
                           font-weight: 500;
                           color: #2d3748;
                           transition: all 0.3s ease;
                           box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                           outline: none;"
                    autocomplete="off">
             <i class="fas fa-keyboard search-kbd" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none; transition: all 0.3s ease;"></i>
             
             <!-- Search Results Dropdown -->
             <div id="searchResults" style="
                 position: absolute;
                 top: 100%;
                 left: 0;
                 right: 0;
                 background: white;
                 border-radius: 15px;
                 box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                 margin-top: 10px;
                 max-height: 400px;
                 overflow-y: auto;
                 display: none;
                 z-index: 9999;
             "></div>
         </div>
     </div>
     
     <!-- Navbar -->
     <ul class="navbar-nav ml-auto" style="align-items: center;">
         <!-- Live Today's Revenue Display -->
         <li class="nav-item no-arrow mx-2">
             <?php
             // Get today's revenue for live display
             $mysqli->query("CREATE TABLE IF NOT EXISTS tms_payment_collection (
                 pc_id INT AUTO_INCREMENT PRIMARY KEY,
                 pc_booking_id INT NOT NULL,
                 pc_amount DECIMAL(10,2) NOT NULL,
                 pc_method ENUM('QR','TechQR','Cash') NOT NULL,
                 pc_collected_by INT NOT NULL,
                 pc_collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                 pc_status ENUM('Collected','Verified','Pending') DEFAULT 'Collected',
                 INDEX(pc_booking_id)
             )");
             
             $nav_today_revenue_query = "SELECT SUM(IFNULL(pc.pc_amount, 0)) as revenue
                                    FROM tms_payment_collection pc
                                    INNER JOIN tms_service_booking sb ON pc.pc_booking_id = sb.sb_id
                                    WHERE sb.sb_status = 'Completed'
                                    AND DATE(pc.pc_collected_at) = CURDATE()";
             $nav_today_revenue_result = $mysqli->query($nav_today_revenue_query);
             $nav_today_revenue = 0;
             if($nav_today_revenue_result) {
                 $nav_today_revenue = $nav_today_revenue_result->fetch_object()->revenue;
             }
             ?>
             <div id="liveRevenueDisplay" style="
                 background: transparent;
                 color: #ffffff;
                 padding: 6px 10px;
                 border-radius: 15px;
                 display: flex;
                 flex-direction: column;
                 align-items: center;
                 gap: 1px;
                 cursor: pointer;
                 border: none;
                 min-width: 100px;
                 transition: all 0.3s ease;
             " onclick="updateLiveRevenue()" title="Click to refresh revenue">
                 <div style="font-size: 8px; font-weight: 600; letter-spacing: 0.3px; text-transform: uppercase; color: #f0f0f0; opacity: 0.9;">
                     Today Revenue
                 </div>
                 <div style="display: flex; align-items: center; gap: 4px; font-weight: 900; font-size: 16px; color: #ffffff;">
                     <i class="fas fa-rupee-sign" style="font-size: 13px;"></i>
                     <span id="revenueAmount"><?php echo number_format($nav_today_revenue, 0); ?></span>
                     <i class="fas fa-sync-alt" id="revenueRefreshIcon" style="font-size: 9px; opacity: 0.7;"></i>
                 </div>
             </div>
         </li>
         
         <!-- Stats Button -->
         <li class="nav-item no-arrow mx-1">
             <a class="nav-link" href="admin-stats.php" id="statsButton" 
                style="position: relative; padding: 6px 10px;">
                 <i class="fas fa-chart-line fa-fw" style="font-size: 16px; color: #ffffff; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'"></i>
             </a>
         </li>
         
         <!-- Notification Bell -->
         <li class="nav-item dropdown no-arrow mx-1">
             <?php
             // Get total pending bookings count
             $pending_notif_query = "SELECT COUNT(*) as pending_count 
                                    FROM tms_service_booking 
                                    WHERE sb_status = 'Pending'";
             $pending_result = $mysqli->query($pending_notif_query);
             $pending_count = 0;
             if($pending_result) {
                 $pending_count = $pending_result->fetch_object()->pending_count;
             }
             ?>
             <a class="nav-link" href="admin-notifications.php" id="notificationBell" style="position: relative; padding: 6px 10px;" title="<?php echo $pending_count; ?> Pending Bookings">
                 <i class="fas fa-bell fa-fw" style="font-size: 16px; color: #ffffff; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.15) rotate(15deg)'" onmouseout="this.style.transform='scale(1) rotate(0deg)'"></i>
                 <?php if($pending_count > 0): ?>
                     <span class="badge badge-danger" id="notificationBadge" style="
                         position: absolute;
                         top: 2px;
                         right: 2px;
                         background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                         color: white;
                         font-size: 9px;
                         font-weight: 700;
                         padding: 2px 5px;
                         border-radius: 10px;
                         min-width: 18px;
                         text-align: center;
                         box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5);
                         border: none;
                     "><?php echo $pending_count > 99 ? '99+' : $pending_count; ?></span>
                 <?php endif; ?>
             </a>
         </li>

         <li class="nav-item dropdown no-arrow">
             <a style="display: flex; align-items: center; gap: 6px; padding: 4px 10px;" class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <?php if(isset($_SESSION['a_photo']) && !empty($_SESSION['a_photo'])): ?>
                     <img src="../vendor/img/<?php echo htmlspecialchars($_SESSION['a_photo']); ?>" 
                          class="rounded-circle" 
                          style="width: 28px; height: 28px; object-fit: cover; border: 2px solid #ffffff;"
                          alt="Admin Photo">
                 <?php else: ?>
                     <i class="fas fa-user-circle fa-fw" style="font-size: 20px; color: #ffffff;"></i>
                 <?php endif; ?>
                 <span class="d-none d-md-inline" style="font-size: 14px; font-weight: 600; margin: 0; color: #ffffff;">
                     <?php 
                     if(isset($_SESSION['a_name'])) {
                         echo htmlspecialchars($_SESSION['a_name']);
                     } else {
                         echo 'Admin';
                     }
                     ?>
                 </span>
             </a>
             <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" style="border-radius: 10px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.15); margin-top: 8px;">
                 <a class="dropdown-item" href="admin-profile.php" style="padding: 8px 15px; font-size: 14px;"><i class="fas fa-user" style="width: 20px;"></i> Profile</a>
                 <a class="dropdown-item" href="admin-change-password.php" style="padding: 8px 15px; font-size: 14px;"><i class="fas fa-key" style="width: 20px;"></i> Change Password</a>
                 <div class="dropdown-divider" style="margin: 5px 0;"></div>
                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal" style="padding: 8px 15px; font-size: 14px; color: #dc3545;"><i class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout</a>
             </div>
         </li>
     </ul>
 </nav>

 <style>
 /* Custom Tooltip Styling for Revenue */
 .tooltip {
     z-index: 9999 !important;
 }
 
 .tooltip-inner {
     background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
     color: white !important;
     padding: 12px 20px !important;
     border-radius: 10px !important;
     box-shadow: 0 8px 25px rgba(0,0,0,0.5) !important;
     font-size: 15px !important;
     font-weight: 700 !important;
     min-width: 200px !important;
     text-align: center !important;
 }
 
 .tooltip.show {
     opacity: 1 !important;
 }
 
 .tooltip.bs-tooltip-bottom .arrow::before {
     border-bottom-color: #1e293b !important;
 }
 
 .tooltip.bs-tooltip-top .arrow::before {
     border-top-color: #1e293b !important;
 }
 
 /* Logo hover effect */
 .navbar-logo:hover {
     transform: scale(1.05) rotate(2deg);
 }
 
 /* Slim Navbar Styling - Eye-Friendly Purple Theme */
 nav.navbar {
     background: linear-gradient(90deg, #6B7FDB 0%, #8B7FC7 100%) !important;
     box-shadow: 0 2px 8px rgba(0,0,0,0.15);
     position: sticky;
     top: 0;
     z-index: 1000;
 }
 
 .navbar-brand {
     transition: all 0.3s ease;
 }
 
 .navbar-brand:hover {
     transform: translateY(-2px);
     filter: brightness(1.1);
 }
 
 .navbar-brand:hover .logo-container {
     box-shadow: 0 4px 15px rgba(255,255,255,0.3);
     transform: scale(1.08);
 }
 
 /* Sidebar Styling */
 .sidebar {
     background: linear-gradient(180deg, #f7fafc 0%, #edf2f7 100%) !important;
     box-shadow: 2px 0 10px rgba(0,0,0,0.05);
 }
 
 .sidebar .nav-link {
     color: #2d3748 !important;
     font-weight: 600;
     padding: 12px 20px;
     margin: 5px 10px;
     border-radius: 10px;
     transition: all 0.3s ease;
 }
 
 .sidebar .nav-link:hover {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
     color: white !important;
     transform: translateX(5px);
     box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
 }
 
 .sidebar .nav-link i {
     color: #667eea;
     margin-right: 10px;
     transition: all 0.3s ease;
 }
 
 .sidebar .nav-link:hover i {
     color: white !important;
 }
 
 .sidebar .nav-item.active .nav-link {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
     color: white !important;
     box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
 }
 
 .sidebar .nav-item.active .nav-link i {
     color: white !important;
 }
 
 .sidebar .dropdown-menu {
     background: white;
     border: none;
     box-shadow: 0 4px 15px rgba(0,0,0,0.1);
     border-radius: 10px;
     margin-left: 10px;
 }
 
 .sidebar .dropdown-item {
     color: #4a5568;
     padding: 10px 20px;
     transition: all 0.3s ease;
     border-radius: 8px;
     margin: 3px 5px;
 }
 
 .sidebar .dropdown-item:hover {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
     color: white !important;
     transform: translateX(5px);
 }
 
 .sidebar .dropdown-item i {
     color: #667eea;
     margin-right: 8px;
     width: 20px;
     text-align: center;
 }
 
 .sidebar .dropdown-item:hover i {
     color: white;
 }
 
 .sidebar .dropdown-toggle::after {
     color: #667eea;
 }
 
 .sidebar .nav-link:hover .dropdown-toggle::after {
     color: white;
 }
 
 /* Logo animations - Subtle */
 .logo-container {
     transition: all 0.3s ease;
 }
 
 .logo-container img {
     transition: all 0.3s ease;
 }
 
 /* Glow effect on hover */
 .navbar-brand:hover .logo-container img {
     filter: drop-shadow(0 0 4px rgba(255,255,255,0.4));
     transform: scale(1.05);
 }
 
 /* Quick Booking Button Styling - Green with Click Effect */
 .quick-booking-btn {
     position: relative;
     overflow: hidden;
     background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
     box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4) !important;
     transition: all 0.3s ease !important;
 }
 
 .quick-booking-btn:hover {
     background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
     transform: translateY(-2px) scale(1.02);
     box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6) !important;
     color: #ffffff !important;
 }
 
 .quick-booking-btn:active {
     transform: translateY(1px) scale(0.98) !important;
     box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
     transition: all 0.1s ease !important;
 }
 
 /* Ripple effect on click */
 .quick-booking-btn::before {
     content: '';
     position: absolute;
     top: 50%;
     left: 50%;
     width: 0;
     height: 0;
     border-radius: 50%;
     background: rgba(255, 255, 255, 0.5);
     transform: translate(-50%, -50%);
     transition: width 0.6s, height 0.6s;
 }
 
 .quick-booking-btn:active::before {
     width: 300px;
     height: 300px;
     transition: width 0s, height 0s;
 }
 
 /* Pulse animation for attention */
 @keyframes quickBookingPulse {
     0%, 100% {
         box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
     }
     50% {
         box-shadow: 0 4px 20px rgba(16, 185, 129, 0.7);
     }
 }
 
 .quick-booking-btn {
     animation: quickBookingPulse 2s ease-in-out infinite;
 }
 
 .quick-booking-btn:hover {
     animation: none;
 }
 
 /* Icon animation on hover */
 .quick-booking-btn:hover i {
     animation: phoneRing 0.5s ease-in-out;
 }
 
 @keyframes phoneRing {
     0%, 100% { transform: rotate(0deg); }
     10%, 30%, 50%, 70%, 90% { transform: rotate(-10deg); }
     20%, 40%, 60%, 80% { transform: rotate(10deg); }
 }
 
 .btn-success {
     background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
     border: none !important;
     box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3) !important;
 }
 
 .btn-success:hover {
     background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
     transform: translateY(-1px);
     box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5) !important;
 }
 
 .btn-success:active {
     transform: translateY(0);
 }
 
 /* Dropdown Menu Styling */
 .dropdown-menu {
     animation: slideDown 0.3s ease;
 }
 
 @keyframes slideDown {
     from {
         opacity: 0;
         transform: translateY(-10px);
     }
     to {
         opacity: 1;
         transform: translateY(0);
     }
 }
 
 .dropdown-item {
     transition: all 0.2s ease;
 }
 
 .dropdown-item:hover {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
     color: white !important;
     padding-left: 20px;
 }
 
 .dropdown-item:hover i {
     color: white !important;
 }
 
 /* Responsive adjustments */
 @media (max-width: 768px) {
     nav.navbar {
         padding: 0.25rem 0.75rem !important;
     }
     
     .navbar-brand {
         padding: 2px 6px !important;
         gap: 6px !important;
     }
     
     .logo-container {
         width: 32px !important;
         height: 32px !important;
         padding: 3px !important;
     }
 }
 
 @media (max-width: 576px) {
     .navbar-brand span {
         display: none !important;
     }
 }
 
 /* Notification Bell Hover */
 #notificationBell {
     transition: all 0.3s ease;
 }
 
 #notificationBell:hover {
     background: rgba(59, 130, 246, 0.1);
     border-radius: 50%;
     box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
 }
 
 /* Notification Badge Animation */
 #notificationBadge {
     animation: badgePulse 2s ease-in-out infinite;
 }
 
 @keyframes badgePulse {
     0%, 100% {
         transform: scale(1);
         box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5);
     }
     50% {
         transform: scale(1.1);
         box-shadow: 0 3px 10px rgba(239, 68, 68, 0.8);
     }
 }
 
 /* User Dropdown Hover */
 #userDropdown {
     transition: all 0.3s ease;
     border-radius: 25px;
 }
 
 #userDropdown:hover {
     background: rgba(255, 255, 255, 0.15);
     box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2);
 }
 
 /* Stats Button Hover */
 #statsButton {
     transition: all 0.3s ease;
 }
 
 #statsButton:hover {
     background: rgba(255, 255, 255, 0.15);
     border-radius: 50%;
     box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2);
 }
 
 /* Smooth transitions for all nav items */
 .nav-link {
     transition: all 0.3s ease;
 }
 
 /* Simple Clean Search Styling */
 .global-search-input::placeholder {
     color: #94a3b8;
     font-weight: 500;
 }
 
 .global-search-input:hover {
     border-color: #cbd5e1 !important;
     box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
 }
 
 .global-search-input:focus {
     width: 350px !important;
     background: #ffffff !important;
     border-color: #667eea !important;
     box-shadow: 0 4px 16px rgba(102, 126, 234, 0.25), 
                 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
     color: #2d3748 !important;
 }
 
 .search-container:hover .search-icon {
     color: #667eea;
 }
 
 .global-search-input:focus ~ .search-icon {
     color: #667eea !important;
 }
 
 .global-search-input:focus ~ .search-kbd {
     opacity: 0;
 }
 
 /* Live Revenue Display Animation */
 @keyframes revenueUpdate {
     0%, 100% { transform: scale(1); }
     50% { transform: scale(1.05); }
 }
 
 .revenue-updating {
     animation: revenueUpdate 0.5s ease;
 }
 
 @keyframes spin {
     from { transform: rotate(0deg); }
     to { transform: rotate(360deg); }
 }
 
 .spinning {
     animation: spin 0.6s linear;
 }
 
 /* Revenue Display Hover Effect */
 #liveRevenueDisplay:hover {
     background: rgba(59, 130, 246, 0.08) !important;
     transform: scale(1.02);
 }
 </style>

<!-- Unified Notification System - Works on ALL admin pages -->
<?php include('unified-notification-system.php'); ?>

<script>
// Live Revenue Update - Auto refresh every 30 seconds
function updateLiveRevenue() {
    const revenueAmount = document.getElementById('revenueAmount');
    const refreshIcon = document.getElementById('revenueRefreshIcon');
    const revenueDisplay = document.getElementById('liveRevenueDisplay');
    
    if (!revenueAmount) return;
    
    // Add spinning animation to refresh icon
    refreshIcon.classList.add('spinning');
    
    // Fetch today's revenue
    fetch('get-live-revenue.php')
        .then(response => response.json())
        .then(data => {
            if (data.revenue !== undefined) {
                // Format the number with commas
                const formattedRevenue = new Intl.NumberFormat('en-IN').format(data.revenue);
                
                // Update with animation
                revenueDisplay.classList.add('revenue-updating');
                revenueAmount.textContent = formattedRevenue;
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    revenueDisplay.classList.remove('revenue-updating');
                }, 500);
            }
        })
        .catch(error => console.log('Revenue update error:', error))
        .finally(() => {
            // Remove spinning animation
            setTimeout(() => {
                refreshIcon.classList.remove('spinning');
            }, 600);
        });
}

// Update revenue every 30 seconds
setInterval(updateLiveRevenue, 30000);

// Also update when page becomes visible again
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        updateLiveRevenue();
    }
});
</script>

 <!-- Stats Modal -->
 <div class="modal fade" id="statsModal" tabindex="-1" role="dialog" aria-labelledby="statsModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
         <div class="modal-content" style="border: none; border-radius: 15px; overflow: hidden;">
             <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 20px 30px;">
                 <h5 class="modal-title" id="statsModalLabel" style="color: white; font-weight: 700; font-size: 1.5rem;">
                     <i class="fas fa-chart-line"></i> Business Statistics
                 </h5>
                 <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                     <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
                 </button>
             </div>
             <div class="modal-body" style="padding: 30px; background: #f8f9fa;">
                 <div id="statsContent">
                     <div class="text-center py-5">
                         <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: #667eea;"></i>
                         <p class="mt-3" style="color: #718096;">Loading statistics...</p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <script>
 // Load stats when modal is opened
 $('#statsModal').on('show.bs.modal', function (e) {
     loadStats();
 });
 
 function loadStats() {
     $.ajax({
         url: 'get-stats-data.php',
         method: 'GET',
         success: function(response) {
             $('#statsContent').html(response);
         },
         error: function() {
             $('#statsContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Failed to load statistics</div>');
         }
     });
 }
 

 </script>

 <!-- Logout Modal-->
 <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                 <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">×</span>
                 </button>
             </div>
             <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
             <div class="modal-footer">
                 <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                 <a class="btn btn-danger" href="admin-logout.php">Logout</a>
             </div>
         </div>
     </div>
 </div>

 <!-- Include Pending Bookings Alert -->
 <?php include('pending-alert.php'); ?>
 
 <!-- Quick Booking Button Click Effect -->
 <script>
 // Add click effect to Quick Booking button
 document.addEventListener('DOMContentLoaded', function() {
     const quickBookingBtn = document.querySelector('.quick-booking-btn');
     
     if (quickBookingBtn) {
         quickBookingBtn.addEventListener('click', function(e) {
             // Create ripple effect
             const ripple = document.createElement('span');
             const rect = this.getBoundingClientRect();
             const size = Math.max(rect.width, rect.height);
             const x = e.clientX - rect.left - size / 2;
             const y = e.clientY - rect.top - size / 2;
             
             ripple.style.width = ripple.style.height = size + 'px';
             ripple.style.left = x + 'px';
             ripple.style.top = y + 'px';
             ripple.style.position = 'absolute';
             ripple.style.borderRadius = '50%';
             ripple.style.background = 'rgba(255, 255, 255, 0.6)';
             ripple.style.transform = 'scale(0)';
             ripple.style.animation = 'ripple 0.6s ease-out';
             ripple.style.pointerEvents = 'none';
             
             this.appendChild(ripple);
             
             setTimeout(() => {
                 ripple.remove();
             }, 600);
         });
     }
 });
 
 // Add ripple animation
 const style = document.createElement('style');
 style.textContent = `
     @keyframes ripple {
         to {
             transform: scale(4);
             opacity: 0;
         }
     }
 `;
 document.head.appendChild(style);
 </script>
 
 <!-- Global Search Functionality -->
 <script>
 // Feature search data - COMPLETE LIST OF ALL ADMIN PAGES
 const searchFeatures = [
     // Dashboard
     { name: 'Dashboard', url: 'admin-dashboard.php', category: 'Dashboard', icon: 'fa-tachometer-alt', keywords: 'dashboard home overview stats main' },
     
     // Bookings - All Pages
     { name: 'Quick Booking', url: 'admin-quick-booking.php', category: 'Bookings', icon: 'fa-phone-alt', keywords: 'quick booking call phone new create fast' },
     { name: 'All Bookings', url: 'admin-all-bookings.php', category: 'Bookings', icon: 'fa-list', keywords: 'all bookings list view manage complete' },
     { name: 'Add Booking', url: 'admin-add-booking.php', category: 'Bookings', icon: 'fa-plus-circle', keywords: 'add new booking create' },
     { name: 'Add Booking for User', url: 'admin-add-booking-usr.php', category: 'Bookings', icon: 'fa-user-plus', keywords: 'add booking user customer' },
     { name: 'Manage Service Bookings', url: 'admin-manage-service-booking.php', category: 'Bookings', icon: 'fa-calendar-check', keywords: 'manage service bookings assign view' },
     { name: 'Manage Bookings', url: 'admin-manage-booking.php', category: 'Bookings', icon: 'fa-tasks', keywords: 'manage bookings all list' },
     { name: 'Assign Technician', url: 'admin-assign-technician.php', category: 'Bookings', icon: 'fa-user-cog', keywords: 'assign technician booking allocate' },
     { name: 'Approve Booking', url: 'admin-approve-booking.php', category: 'Bookings', icon: 'fa-check', keywords: 'approve booking accept confirm' },
     { name: 'View Booking', url: 'admin-view-booking.php', category: 'Bookings', icon: 'fa-eye', keywords: 'view booking details see' },
     { name: 'View Service Booking', url: 'admin-view-service-booking.php', category: 'Bookings', icon: 'fa-eye', keywords: 'view service booking details' },
     { name: 'Edit Custom Booking', url: 'admin-edit-custom-booking.php', category: 'Bookings', icon: 'fa-edit', keywords: 'edit custom booking modify' },
     { name: 'Unassigned Bookings', url: 'admin-all-bookings.php?technician=unassigned', category: 'Bookings', icon: 'fa-exclamation-triangle', keywords: 'unassigned pending bookings no technician' },
     { name: 'Rejected Bookings', url: 'admin-rejected-bookings.php', category: 'Bookings', icon: 'fa-times-circle', keywords: 'rejected not done cancelled bookings declined' },
     { name: 'Completed Bookings', url: 'admin-completed-bookings.php', category: 'Bookings', icon: 'fa-check-circle', keywords: 'completed finished done bookings success' },
     { name: 'Hold Bookings', url: 'admin-manage-hold-bookings.php', category: 'Bookings', icon: 'fa-pause-circle', keywords: 'hold bookings on hold waiting paused' },
     { name: 'Cancel Booking', url: 'admin-cancel-service-booking.php', category: 'Bookings', icon: 'fa-ban', keywords: 'cancel booking delete remove' },
     { name: 'Delete Booking', url: 'admin-delete-booking.php', category: 'Bookings', icon: 'fa-trash', keywords: 'delete booking remove' },
     { name: 'Delete Service Booking', url: 'admin-delete-service-booking.php', category: 'Bookings', icon: 'fa-trash-alt', keywords: 'delete service booking remove' },
     
     // Technicians - All Pages
     { name: 'Add Technician', url: 'admin-add-technician.php', category: 'Technicians', icon: 'fa-user-plus', keywords: 'add new technician register create worker' },
     { name: 'Add Technician with Skills', url: 'admin-add-technician-with-skills.php', category: 'Technicians', icon: 'fa-user-cog', keywords: 'add technician skills create' },
     { name: 'Manage Technicians', url: 'admin-manage-technician.php', category: 'Technicians', icon: 'fa-users-cog', keywords: 'manage all technicians list view edit workers' },
     { name: 'Technician List', url: 'admin-technician-list.php', category: 'Technicians', icon: 'fa-list-ul', keywords: 'technician list all view' },
     { name: 'View Technician', url: 'admin-view-technician.php', category: 'Technicians', icon: 'fa-eye', keywords: 'view technician details profile' },
     { name: 'Manage Single Technician', url: 'admin-manage-single-technician.php', category: 'Technicians', icon: 'fa-user-edit', keywords: 'manage single technician edit update' },
     { name: 'Unlock Technicians', url: 'admin-unlock-technician.php', category: 'Technicians', icon: 'fa-unlock', keywords: 'unlock locked technicians commission rejection unblock' },
     { name: 'Guest Technicians', url: 'admin-guest-technicians.php', category: 'Technicians', icon: 'fa-user-clock', keywords: 'guest pending technicians approve new registration' },
     { name: 'Technician Passwords', url: 'admin-manage-technician-passwords.php', category: 'Technicians', icon: 'fa-key', keywords: 'technician passwords reset change security' },
     { name: 'Manage Technician Skills', url: 'admin-manage-technician-skills.php', category: 'Technicians', icon: 'fa-tools', keywords: 'manage technician skills edit update abilities' },
     { name: 'Edit Technician Skills', url: 'admin-edit-technician-skills.php', category: 'Technicians', icon: 'fa-edit', keywords: 'edit technician skills modify' },
     { name: 'Generate ID Card', url: 'admin-generate-id-card.php', category: 'Technicians', icon: 'fa-id-card', keywords: 'generate id card technician identity badge' },
     { name: 'Technician Monthly Details', url: 'admin-technician-monthly-details.php', category: 'Technicians', icon: 'fa-calendar-alt', keywords: 'technician monthly details report performance' },
     
     // Services - All Pages
     { name: 'Add Service', url: 'admin-add-service.php', category: 'Services', icon: 'fa-plus-square', keywords: 'add new service create offering' },
     { name: 'Manage Services', url: 'admin-manage-service.php', category: 'Services', icon: 'fa-wrench', keywords: 'manage services list edit view all offerings' },
     { name: 'Manage Single Service', url: 'admin-manage-single-service.php', category: 'Services', icon: 'fa-cog', keywords: 'manage single service edit update' },
     { name: 'View Service', url: 'admin-view-service.php', category: 'Services', icon: 'fa-eye', keywords: 'view service details see' },
     { name: 'Service Prices', url: 'admin-service-prices.php', category: 'Services', icon: 'fa-rupee-sign', keywords: 'service prices rates cost pricing money' },
     
     // Customers/Users - All Pages
     { name: 'Add Customer', url: 'admin-add-user.php', category: 'Customers', icon: 'fa-user-plus', keywords: 'add new customer user register client' },
     { name: 'Manage Customers', url: 'admin-manage-user.php', category: 'Customers', icon: 'fa-users', keywords: 'manage customers users list view clients' },
     { name: 'Manage Single User', url: 'admin-manage-single-usr.php', category: 'Customers', icon: 'fa-user-edit', keywords: 'manage single user customer edit' },
     { name: 'View User', url: 'admin-view-user.php', category: 'Customers', icon: 'fa-eye', keywords: 'view user customer details profile' },
     { name: 'Customer Passwords', url: 'admin-manage-user-passwords.php', category: 'Customers', icon: 'fa-key', keywords: 'customer user passwords reset change' },
     
     // Feedbacks - All Pages
     { name: 'Add Feedback', url: 'admin-add-feedback.php', category: 'Feedbacks', icon: 'fa-comment-medical', keywords: 'add feedback review rating create' },
     { name: 'Manage Feedbacks', url: 'admin-manage-feedback.php', category: 'Feedbacks', icon: 'fa-comments', keywords: 'manage feedbacks reviews ratings list' },
     { name: 'View Feedback', url: 'admin-view-feedback.php', category: 'Feedbacks', icon: 'fa-eye', keywords: 'view feedback review details' },
     { name: 'Edit Feedback', url: 'admin-edit-feedback.php', category: 'Feedbacks', icon: 'fa-edit', keywords: 'edit feedback review modify' },
     { name: 'Approve Feedback', url: 'admin-approve-feedback.php', category: 'Feedbacks', icon: 'fa-check-circle', keywords: 'approve feedback review accept' },
     { name: 'Publish Feedback', url: 'admin-publish-feedback.php', category: 'Feedbacks', icon: 'fa-thumbs-up', keywords: 'publish feedback review approve show' },
     
     // Messages & Notifications
     { name: 'Contact Messages', url: 'admin-contact-messages.php', category: 'Messages', icon: 'fa-envelope', keywords: 'contact messages inbox email inquiries' },
     { name: 'Notifications', url: 'admin-notifications.php', category: 'Notifications', icon: 'fa-bell', keywords: 'notifications alerts updates news' },
     
     // Statistics & Reports - All Pages
     { name: 'Statistics', url: 'admin-stats.php', category: 'Reports', icon: 'fa-chart-line', keywords: 'statistics stats reports analytics data' },
     { name: 'Service Statistics', url: 'admin-stats-services.php', category: 'Reports', icon: 'fa-chart-bar', keywords: 'service statistics reports data analytics' },
     { name: 'Technician Statistics', url: 'admin-stats-technicians.php', category: 'Reports', icon: 'fa-chart-pie', keywords: 'technician statistics performance reports data' },
     { name: 'Pending Commissions', url: 'admin-pending-commissions.php', category: 'Reports', icon: 'fa-money-bill-wave', keywords: 'pending commissions payments dues money unpaid' },
     { name: 'Record Commission Payment', url: 'admin-record-commission-payment.php', category: 'Reports', icon: 'fa-hand-holding-usd', keywords: 'record commission payment pay' },
     { name: 'Quick Record Payment', url: 'admin-quick-record-payment.php', category: 'Reports', icon: 'fa-dollar-sign', keywords: 'quick record payment fast commission' },
     
     // Settings - All Pages
     { name: 'Site Settings', url: 'admin-site-settings.php', category: 'Settings', icon: 'fa-cogs', keywords: 'site settings contact info configuration' },
     { name: 'Payment Settings', url: 'admin-payment-settings.php', category: 'Settings', icon: 'fa-qrcode', keywords: 'payment qr settings upi configuration' },
     { name: 'Manage Payment QR', url: 'admin-manage-payment-qr.php', category: 'Settings', icon: 'fa-qrcode', keywords: 'manage payment qr code upi' },
     { name: 'Home Slider', url: 'admin-home-slider.php', category: 'Settings', icon: 'fa-images', keywords: 'home slider banner images carousel' },
     { name: 'Manage Slider', url: 'admin-manage-slider.php', category: 'Settings', icon: 'fa-sliders-h', keywords: 'manage slider banner images' },
     { name: 'Edit Slider', url: 'admin-edit-slider.php', category: 'Settings', icon: 'fa-edit', keywords: 'edit slider banner modify' },
     { name: 'Gallery', url: 'admin-manage-gallery.php', category: 'Settings', icon: 'fa-photo-video', keywords: 'gallery photos images pictures media' },
     { name: 'System Logs', url: 'admin-view-syslogs.php', category: 'Settings', icon: 'fa-file-alt', keywords: 'system logs history audit trail activity' },
     { name: 'Recycle Bin', url: 'admin-recycle-bin.php', category: 'Settings', icon: 'fa-trash-restore', keywords: 'recycle bin deleted trash restore recover' },
     
     // Admin Management
     { name: 'Add Admin', url: 'admin-add-admin.php', category: 'Admin', icon: 'fa-user-shield', keywords: 'add admin administrator new user' },
     
     // Profile & Account
     { name: 'My Profile', url: 'admin-profile.php', category: 'Profile', icon: 'fa-user', keywords: 'profile account settings my info' },
     { name: 'Change Password', url: 'admin-change-password.php', category: 'Profile', icon: 'fa-key', keywords: 'change password security update' },
     { name: 'Reset Password', url: 'admin-reset-pwd.php', category: 'Profile', icon: 'fa-undo', keywords: 'reset password forgot recovery' },
     { name: 'Logout', url: 'admin-logout.php', category: 'Profile', icon: 'fa-sign-out-alt', keywords: 'logout signout exit leave' }
 ];
 
 // Search functionality
 const searchInput = document.getElementById('globalSearch');
 const searchResults = document.getElementById('searchResults');
 const searchKbd = document.querySelector('.search-kbd');
 
 if (searchInput && searchResults) {
     // Handle focus/blur
     searchInput.addEventListener('blur', function() {
         if (this.value === '') {
             this.style.width = '280px';
             this.style.background = '#ffffff';
             this.style.borderColor = '#e2e8f0';
             this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
         }
     });
     
     searchInput.addEventListener('input', function() {
         const query = this.value.toLowerCase().trim();
         
         if (query.length < 2) {
             searchResults.style.display = 'none';
             return;
         }
         
         // Filter features
         const matches = searchFeatures.filter(feature => {
             return feature.name.toLowerCase().includes(query) ||
                    feature.category.toLowerCase().includes(query) ||
                    feature.keywords.toLowerCase().includes(query);
         });
         
         if (matches.length === 0) {
             searchResults.innerHTML = `
                 <div style="padding: 20px; text-align: center; color: #718096;">
                     <i class="fas fa-search" style="font-size: 2rem; opacity: 0.3;"></i>
                     <p style="margin-top: 10px;">No results found for "${query}"</p>
                 </div>
             `;
             searchResults.style.display = 'block';
             return;
         }
         
         // Group by category
         const grouped = {};
         matches.forEach(feature => {
             if (!grouped[feature.category]) {
                 grouped[feature.category] = [];
             }
             grouped[feature.category].push(feature);
         });
         
         // Build HTML
         let html = '';
         Object.keys(grouped).forEach(category => {
             html += `
                 <div style="padding: 10px 15px; background: #f7fafc; border-bottom: 1px solid #e2e8f0; font-weight: 700; font-size: 12px; color: #667eea; text-transform: uppercase; letter-spacing: 0.5px;">
                     ${category}
                 </div>
             `;
             
             grouped[category].forEach(feature => {
                 html += `
                     <a href="${feature.url}" style="
                         display: flex;
                         align-items: center;
                         gap: 12px;
                         padding: 12px 15px;
                         text-decoration: none;
                         color: #2d3748;
                         border-bottom: 1px solid #f0f0f0;
                         transition: all 0.2s ease;
                     " onmouseover="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.style.color='white';" onmouseout="this.style.background='white'; this.style.color='#2d3748';">
                         <i class="fas ${feature.icon}" style="width: 20px; text-align: center; font-size: 14px;"></i>
                         <span style="font-weight: 600; font-size: 14px;">${feature.name}</span>
                         <i class="fas fa-arrow-right" style="margin-left: auto; font-size: 12px; opacity: 0.5;"></i>
                     </a>
                 `;
             });
         });
         
         searchResults.innerHTML = html;
         searchResults.style.display = 'block';
     });
     
     // Close search results when clicking outside
     document.addEventListener('click', function(e) {
         if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
             searchResults.style.display = 'none';
         }
     });
     
     // Navigate with keyboard
     searchInput.addEventListener('keydown', function(e) {
         if (e.key === 'Escape') {
             searchResults.style.display = 'none';
             searchInput.blur();
         }
         
         if (e.key === 'Enter') {
             const firstLink = searchResults.querySelector('a');
             if (firstLink) {
                 window.location.href = firstLink.href;
             }
         }
     });
     
     // Keyboard shortcut: Ctrl+K or Cmd+K to focus search
     document.addEventListener('keydown', function(e) {
         if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
             e.preventDefault();
             searchInput.focus();
             searchInput.select();
         }
     });
 }
 </script>


<!-- Booking Alert System - Shows on ALL admin pages -->
<?php 
// Include alert system logic
if(file_exists('../../booking-alert-system.php')) {
    include('../../booking-alert-system.php');
} elseif(file_exists('../booking-alert-system.php')) {
    include('../booking-alert-system.php');
} elseif(file_exists('booking-alert-system.php')) {
    include('booking-alert-system.php');
}

// Include alert modal display
if(file_exists('../../booking-alert-modal.php')) {
    include('../../booking-alert-modal.php');
} elseif(file_exists('../booking-alert-modal.php')) {
    include('../booking-alert-modal.php');
} elseif(file_exists('booking-alert-modal.php')) {
    include('booking-alert-modal.php');
}
?>
