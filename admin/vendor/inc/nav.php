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

     <!-- Quick Booking Button - Larger -->
     <div class="mx-auto d-none d-md-block">
         <a href="admin-quick-booking.php" class="btn shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%); color: #6B7FDB; border: none; padding: 10px 30px; border-radius: 25px; font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
             <i class="fas fa-phone-alt" style="font-size: 16px;"></i>
             <span>Quick Booking</span>
         </a>
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
 
 /* Quick Booking Button Styling - Compact */
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