 <nav class="navbar navbar-expand navbar-dark static-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 0.3rem 1rem; min-height: 50px;">

     <a class="navbar-brand mr-1" href="admin-dashboard.php" style="display: flex; align-items: center; gap: 8px; padding: 4px 10px;">
         <div class="logo-container" style="background: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 4px;">
             <img src="../vendor/EZlogonew.png" alt="Electrozot Logo" style="width: 100%; height: 100%; object-fit: contain;">
         </div>
         <span style="font-size: 16px; font-weight: 700; color: white; display: none; display: md-block;">Electrozot</span>
     </a>

     <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#" style="padding: 4px 8px;">
         <i class="fas fa-bars" style="font-size: 16px;"></i>
     </button>

     <!-- Quick Booking Button - Compact -->
     <div class="mx-auto d-none d-md-block">
         <a href="admin-quick-booking.php" class="btn btn-success btn-sm shadow-sm" style="padding: 6px 18px; border-radius: 20px; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 6px; transition: all 0.3s ease;">
             <i class="fas fa-phone-alt" style="font-size: 12px;"></i>
             <span>Quick Booking</span>
         </a>
     </div>
     
     <!-- Navbar -->
     <ul class="navbar-nav ml-auto" style="align-items: center;">
         <!-- Notification Bell -->
         <li class="nav-item dropdown no-arrow mx-1">
             <a class="nav-link" href="admin-notifications.php" id="notificationBell" style="position: relative; padding: 6px 10px;" title="View All Notifications">
                 <i class="fas fa-bell fa-fw" style="font-size: 16px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.15) rotate(15deg)'" onmouseout="this.style.transform='scale(1) rotate(0deg)'"></i>
             </a>
         </li>

         <li class="nav-item dropdown no-arrow">
             <a style="display: flex; align-items: center; gap: 6px; padding: 4px 10px;" class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <?php if(isset($_SESSION['a_photo']) && !empty($_SESSION['a_photo'])): ?>
                     <img src="../vendor/img/<?php echo htmlspecialchars($_SESSION['a_photo']); ?>" 
                          class="rounded-circle" 
                          style="width: 28px; height: 28px; object-fit: cover; border: 2px solid #fff;"
                          alt="Admin Photo">
                 <?php else: ?>
                     <i class="fas fa-user-circle fa-fw" style="font-size: 20px;"></i>
                 <?php endif; ?>
                 <span class="d-none d-md-inline" style="font-size: 14px; font-weight: 600; margin: 0;">
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
 /* Slim Navbar Styling */
 nav.navbar {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
     box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
     background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
     border: none !important;
     box-shadow: 0 2px 8px rgba(40, 167, 69, 0.25) !important;
 }
 
 .btn-success:hover {
     background: linear-gradient(135deg, #218838 0%, #1aa179 100%) !important;
     transform: translateY(-1px);
     box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35) !important;
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
     background: rgba(255,255,255,0.1);
     border-radius: 50%;
 }
 
 /* User Dropdown Hover */
 #userDropdown {
     transition: all 0.3s ease;
     border-radius: 25px;
 }
 
 #userDropdown:hover {
     background: rgba(255,255,255,0.1);
 }
 
 /* Smooth transitions for all nav items */
 .nav-link {
     transition: all 0.3s ease;
 }
 </style>

<!-- Unified Notification System - Works on ALL admin pages -->
<?php include('unified-notification-system.php'); ?>

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