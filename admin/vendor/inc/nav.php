 <nav class="navbar navbar-expand navbar-dark static-top" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">

     <a class="navbar-brand mr-1" href="admin-dashboard.php" style="display: flex; align-items: center; gap: 12px; padding: 8px 15px; background: rgba(255,255,255,0.15); border-radius: 12px; backdrop-filter: blur(10px); border: 2px solid rgba(255,255,255,0.2);">
         <div class="logo-container" style="background: white; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); position: relative; overflow: hidden; padding: 5px;">
             <img src="../vendor/EZlogonew.png" alt="Electrozot Logo" style="width: 100%; height: 100%; object-fit: contain;">
             <div style="position: absolute; top: -5px; right: -5px; width: 15px; height: 15px; background: #ffc107; border-radius: 50%; box-shadow: 0 0 10px #ffc107;"></div>
         </div>
         <div style="display: flex; flex-direction: column; line-height: 1.3;">
             <span style="font-size: 19px; font-weight: 900; color: white; text-shadow: 2px 2px 6px rgba(0,0,0,0.4); letter-spacing: 0.5px;">
                 Hi, <span style="color: #ffd700;">Electrozot</span> Admin
             </span>
             <span style="font-size: 11px; color: rgba(255,255,255,0.95); font-weight: 600; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">
                 <i class="fas fa-shield-alt" style="font-size: 10px;"></i> Admin Control Panel
             </span>
         </div>
     </a>

     <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
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
             <a style="display: flex; align-items: center; gap: 8px;" class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <?php if(isset($_SESSION['a_photo']) && !empty($_SESSION['a_photo'])): ?>
                     <img src="../vendor/img/<?php echo htmlspecialchars($_SESSION['a_photo']); ?>" 
                          class="rounded-circle" 
                          style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff;"
                          alt="Admin Photo">
                 <?php else: ?>
                     <i class="fas fa-user-circle fa-fw"></i>
                 <?php endif; ?>
                 <h6 style="margin: 0;">
                     <?php 
                     if(isset($_SESSION['a_name'])) {
                         echo htmlspecialchars($_SESSION['a_name']);
                     } else {
                         echo 'Admin';
                     }
                     ?>
                 </h6>
             </a>
             <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                 <a class="dropdown-item" href="admin-profile.php"><i class="fas fa-user"></i> Profile</a>
                 <a class="dropdown-item" href="admin-change-password.php"><i class="fas fa-key"></i> Change Password</a>
                 <div class="dropdown-divider"></div>
                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt"></i> Logout</a>
             </div>
         </li>
     </ul>
 </nav>

 <style>
 /* Enhanced Navbar Styling */
 .navbar-brand {
     transition: all 0.3s ease;
 }
 
 .navbar-brand:hover {
     transform: translateY(-3px);
     filter: brightness(1.1);
 }
 
 .navbar-brand:hover .logo-container {
     box-shadow: 0 8px 25px rgba(255,193,7,0.5);
     transform: rotate(5deg) scale(1.05);
 }
 
 /* Animated gradient background */
 @keyframes gradientShift {
     0% { background-position: 0% 50%; }
     50% { background-position: 100% 50%; }
     100% { background-position: 0% 50%; }
 }
 
 nav.navbar {
     background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%) !important;
     background-size: 200% 200%;
     animation: gradientShift 15s ease infinite;
     box-shadow: 0 4px 20px rgba(0,0,0,0.3);
 }
 
 /* Logo animations */
 @keyframes logoPulse {
     0%, 100% { transform: scale(1) rotate(0deg); }
     50% { transform: scale(1.08) rotate(-5deg); }
 }
 
 @keyframes sparkle {
     0%, 100% { opacity: 1; transform: scale(1); }
     50% { opacity: 0.6; transform: scale(1.3); }
 }
 
 .logo-container {
     animation: logoPulse 3s ease-in-out infinite;
     transition: all 0.3s ease;
 }
 
 .logo-container > div {
     animation: sparkle 2s ease-in-out infinite;
 }
 
 .logo-container img {
     animation: logoPulse 2.5s ease-in-out infinite;
     transition: all 0.3s ease;
 }
 
 /* Glow effect on hover */
 .navbar-brand:hover .logo-container img {
     filter: drop-shadow(0 0 8px rgba(255,193,7,0.6)) drop-shadow(2px 2px 6px rgba(0,0,0,0.2));
     transform: scale(1.05);
 }
 
 /* Responsive adjustments */
 @media (max-width: 768px) {
     .navbar-brand {
         padding: 5px 10px !important;
         gap: 8px !important;
     }
     .navbar-brand > div:last-child span:first-child {
         font-size: 14px !important;
     }
     .navbar-brand > div:last-child span:last-child {
         font-size: 9px !important;
     }
     .logo-container {
         width: 40px !important;
         height: 40px !important;
         padding: 3px !important;
     }
     .logo-container img {
         width: 100% !important;
         height: 100% !important;
     }
 }
 
 @media (max-width: 576px) {
     .navbar-brand > div:last-child span:first-child {
         font-size: 12px !important;
     }
     .navbar-brand > div:last-child span:last-child {
         display: none;
     }
 }
 </style>

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