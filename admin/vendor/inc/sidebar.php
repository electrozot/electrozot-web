 <style>
 /* Pulse animation for notification badge */
 @keyframes pulse {
     0%, 100% {
         transform: scale(1);
         box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
     }
     50% {
         transform: scale(1.05);
         box-shadow: 0 0 0 5px rgba(220, 53, 69, 0);
     }
 }
 
 /* Reduce Sidebar Width Only */
 .sidebar {
     width: 200px !important;
     overflow-x: hidden !important;
 }
 
 /* Dropdown menu styling for better visibility */
 .navbar-nav .dropdown-menu {
     background-color: #4a5568 !important;
     border: 2px solid #2d3748 !important;
     box-shadow: 0 6px 16px rgba(0,0,0,0.4) !important;
     border-radius: 8px !important;
     margin-top: 5px !important;
 }
 
 .navbar-nav .dropdown-item {
     color: #ffffff !important;
     padding: 0.65rem 0.5rem !important;
     font-size: 0.9rem !important;
     font-weight: 500 !important;
     border-radius: 4px !important;
     margin-left: 8px !important;
     margin-right: 8px !important;
     margin-top: 2px !important;
     margin-bottom: 2px !important;
     display: flex !important;
     align-items: center !important;
     max-width: calc(100% - 16px) !important;
 }
 
 .navbar-nav .dropdown-item:hover {
     background-color: #2d3748 !important;
     color: #ffffff !important;
     transform: translateX(3px);
     transition: all 0.2s ease;
 }
 
 .navbar-nav .dropdown-item i {
     margin-right: 6px !important;
     width: 16px !important;
     text-align: center !important;
     color: #a0aec0;
     flex-shrink: 0 !important;
 }
 
 .navbar-nav .dropdown-item span {
     white-space: nowrap !important;
     overflow: hidden !important;
     text-overflow: ellipsis !important;
 }
 
 .navbar-nav .dropdown-item:hover i {
     color: #ffffff;
 }
 
 .navbar-nav .dropdown-item.bg-success {
     background-color: #28a745 !important;
     color: white !important;
 }
 
 .navbar-nav .dropdown-item.bg-success:hover {
     background-color: #218838 !important;
     color: white !important;
 }
 
 .navbar-nav .dropdown-divider {
     border-top: 2px solid #2d3748 !important;
     margin: 0.5rem 0.5rem !important;
 }
 
 /* Main sidebar items styling - Equal spacing on both sides */
 .navbar-nav .nav-link {
     background-color: rgba(255,255,255,0.1);
     margin-left: 10px !important;
     margin-right: 10px !important;
     margin-top: 3px !important;
     margin-bottom: 3px !important;
     padding: 0.75rem 0.6rem 0.75rem 0.6rem !important;
     border-radius: 8px;
     transition: all 0.3s ease;
     display: flex !important;
     align-items: center !important;
     max-width: calc(100% - 20px) !important;
 }
 
 .navbar-nav .nav-link:hover {
     background-color: rgba(255,255,255,0.2);
 }
 
 .navbar-nav .nav-link i {
     width: 18px !important;
     margin-right: 6px !important;
     text-align: center !important;
     flex-shrink: 0 !important;
 }
 
 .navbar-nav .nav-link span {
     white-space: nowrap !important;
     overflow: hidden !important;
     text-overflow: ellipsis !important;
 }
 </style>
 
 <ul class="sidebar navbar-nav">
     <!-- Dashboard -->
     <li class="nav-item active">
         <a class="nav-link" href="admin-dashboard.php">
             <i class="fas fa-fw fa-tachometer-alt"></i>
             <span>Dashboard</span>
         </a>
     </li>
     
     <!-- Bookings (Merged: All booking management) -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="bookingsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-calendar-check"></i>
             <span>Bookings</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="bookingsDropdown">
             <a class="dropdown-item bg-success text-white" href="admin-quick-booking.php"><i class="fas fa-phone-alt"></i> Quick Booking</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-all-bookings.php"><i class="fas fa-list"></i> All Bookings</a>
             <a class="dropdown-item" href="admin-all-bookings.php?technician=unassigned"><i class="fas fa-exclamation-triangle"></i> Unassigned</a>
             <a class="dropdown-item" href="admin-rejected-bookings.php"><i class="fas fa-times-circle"></i> Rejected / Not Done</a>
             <a class="dropdown-item" href="admin-completed-bookings.php"><i class="fas fa-check-circle"></i> Completed</a>
         </div>
     </li>
     
     <!-- Technicians -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="techniciansDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-user-cog"></i>
             <span>Technicians</span>
             <?php
             $guest_count_main_query = "SELECT COUNT(*) as count FROM tms_technician WHERE t_is_guest = 1 AND t_status = 'Pending'";
             $guest_count_main_result = $mysqli->query($guest_count_main_query);
             $guest_count_main = $guest_count_main_result->fetch_object()->count;
             
             // Get locked technicians count (both types: commission and rejection)
             $locked_count_query = "SELECT COUNT(*) as count FROM tms_technician WHERE account_locked = 1 OR t_status = 'Locked'";
             $locked_count_result = $mysqli->query($locked_count_query);
             $locked_count = $locked_count_result ? $locked_count_result->fetch_object()->count : 0;
             
             $total_badges = $guest_count_main + $locked_count;
             if($total_badges > 0):
             ?>
                 <span class="badge badge-danger badge-counter ml-auto" style="animation: pulse 2s infinite;"><?php echo $total_badges; ?></span>
             <?php endif; ?>
         </a>
         <div class="dropdown-menu" aria-labelledby="techniciansDropdown">
             <a class="dropdown-item" href="admin-add-technician.php"><i class="fas fa-user-plus"></i> Add Technician</a>
             <a class="dropdown-item" href="admin-manage-technician.php"><i class="fas fa-users-cog"></i> Manage All</a>
             <a class="dropdown-item" href="admin-guest-technicians.php" style="background: linear-gradient(135deg, rgba(5, 117, 230, 0.1) 0%, rgba(0, 242, 96, 0.1) 100%); font-weight: 700;">
                 <i class="fas fa-user-clock"></i> 
                 <span>Guest Technicians</span>
                 <?php
                 $guest_count_query = "SELECT COUNT(*) as count FROM tms_technician WHERE t_is_guest = 1 AND t_status = 'Pending'";
                 $guest_count_result = $mysqli->query($guest_count_query);
                 $guest_count = $guest_count_result->fetch_object()->count;
                 if($guest_count > 0):
                 ?>
                     <span class="badge badge-danger ml-auto" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; animation: pulse 2s infinite;"><?php echo $guest_count; ?> NEW</span>
                 <?php endif; ?>
             </a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-unlock-technician.php" style="<?php echo ($locked_count > 0) ? 'background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(255, 193, 7, 0.15) 100%); font-weight: 700;' : ''; ?>">
                 <i class="fas fa-unlock"></i> 
                 <span>Unlock Technicians</span>
                 <?php if($locked_count > 0): ?>
                     <span class="badge badge-danger ml-auto" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; animation: pulse 2s infinite;"><?php echo $locked_count; ?> LOCKED</span>
                 <?php endif; ?>
             </a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-manage-technician-passwords.php"><i class="fas fa-key"></i> Manage Passwords</a>
         </div>
     </li>
     
     <!-- Services (Merged: Add + Manage) -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-wrench"></i>
             <span>Services</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="servicesDropdown">
             <a class="dropdown-item" href="admin-add-service.php"><i class="fas fa-plus-square"></i> Add Service</a>
             <a class="dropdown-item" href="admin-manage-service.php"><i class="fas fa-edit"></i> Manage All</a>
             <a class="dropdown-item" href="admin-service-prices.php"><i class="fas fa-rupee-sign"></i> Service Prices</a>
         </div>
     </li>
     
     <!-- Customers -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-users"></i>
             <span>Customers</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="usersDropdown">
             <a class="dropdown-item" href="admin-add-user.php"><i class="fas fa-user-plus"></i> Add Customer</a>
             <a class="dropdown-item" href="admin-manage-user.php"><i class="fas fa-user-edit"></i> Manage All</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-manage-user-passwords.php"><i class="fas fa-key"></i> Manage Passwords</a>
         </div>
     </li>
     
     <!-- Feedbacks (Merged: All feedback operations) -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="feedbacksDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-comment-dots"></i>
             <span>Feedbacks</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="feedbacksDropdown">
             <a class="dropdown-item" href="admin-manage-feedback.php"><i class="fas fa-comments"></i> Manage All</a>
             <a class="dropdown-item" href="admin-publish-feedback.php"><i class="fas fa-thumbs-up"></i> Publish</a>
         </div>
     </li>

     <!-- Blog Management -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="blogDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-blog"></i>
             <span>Blog</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="blogDropdown">
             <a class="dropdown-item" href="admin-add-blog.php"><i class="fas fa-plus"></i> Add Post</a>
             <a class="dropdown-item" href="admin-manage-blog.php"><i class="fas fa-edit"></i> Manage Posts</a>
         </div>
     </li>

     <!-- Contact Messages -->
     <li class="nav-item">
         <a class="nav-link" href="admin-contact-messages.php">
             <i class="fas fa-fw fa-envelope"></i>
             <span>Messages</span>
             <?php
             // Get unread message count
             $unread_query = $mysqli->query("SELECT COUNT(*) as count FROM tms_contact_messages WHERE cm_status='Unread'");
             if($unread_query) {
                 $unread_count = $unread_query->fetch_object()->count;
                 if($unread_count > 0) {
                     echo '<span class="badge badge-danger badge-counter ml-2">' . $unread_count . '</span>';
                 }
             }
             ?>
         </a>
     </li>

     <!-- Notifications -->
     <li class="nav-item">
         <a class="nav-link" href="admin-notifications.php">
             <i class="fas fa-fw fa-bell"></i>
             <span>Notifications</span>
         </a>
     </li>

     <!-- Recycle Bin -->
     <li class="nav-item">
         <a class="nav-link" href="admin-recycle-bin.php">
             <i class="fas fa-fw fa-trash-restore"></i>
             <span>Recycle Bin</span>
         </a>
     </li>

     <!-- Settings (Merged: Slider + Gallery + System) -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-cogs"></i>
             <span>Settings</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="settingsDropdown">
             <a class="dropdown-item" href="admin-site-settings.php"><i class="fas fa-address-book"></i> Site Contact Info</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-payment-settings.php"><i class="fas fa-qrcode"></i> Payment QR</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-generate-id-card.php"><i class="fas fa-id-card"></i> Generate ID Card</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-home-slider.php"><i class="fas fa-images"></i> Home Slider</a>
             <a class="dropdown-item" href="admin-manage-gallery.php"><i class="fas fa-photo-video"></i> Gallery</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-view-syslogs.php"><i class="fas fa-file-alt"></i> System Logs</a>
         </div>
     </li>
 </ul>
