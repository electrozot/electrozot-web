 <style>
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
     padding: 0.85rem 1.5rem !important;
     font-size: 0.95rem !important;
     font-weight: 500 !important;
     border-radius: 4px !important;
     margin: 2px 8px !important;
 }
 
 .navbar-nav .dropdown-item:hover {
     background-color: #2d3748 !important;
     color: #ffffff !important;
     transform: translateX(3px);
     transition: all 0.2s ease;
 }
 
 .navbar-nav .dropdown-item i {
     margin-right: 8px;
     width: 20px;
     text-align: center;
     color: #a0aec0;
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
 
 /* Main sidebar items styling for contrast */
 .navbar-nav .nav-link {
     background-color: rgba(255,255,255,0.1);
     margin: 3px 10px;
     border-radius: 8px;
     transition: all 0.3s ease;
 }
 
 .navbar-nav .nav-link:hover {
     background-color: rgba(255,255,255,0.2);
 }
 </style>
 
 <ul class="sidebar navbar-nav">
     <li class="nav-item active">
         <a class="nav-link" href="admin-dashboard.php">
             <i class="fas fa-fw fa-chart-line"></i>
             <span>Dashboard</span>
         </a>
     </li>
     
     <!-- 1. Bookings -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-calendar-check"></i>
             <span>Bookings</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="pagesDropdown">
             <a class="dropdown-item bg-success text-white" href="admin-quick-booking.php"><i class="fas fa-phone-alt"></i> Quick Booking</a>
             <a class="dropdown-item" href="admin-all-bookings.php"><i class="fas fa-list-ul"></i> All Bookings</a>
         </div>
     </li>
     
     <!-- 2. Technicians -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-user-cog"></i>
             <span>Technicians</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="pagesDropdown">
             <a class="dropdown-item" href="admin-add-technician.php"><i class="fas fa-user-plus"></i> Add</a>
             <a class="dropdown-item" href="admin-manage-technician.php"><i class="fas fa-users-cog"></i> Manage</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-manage-technician-passwords.php"><i class="fas fa-key"></i> Manage Passwords</a>
         </div>
     </li>
     
     <!-- 3. Services -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-wrench"></i>
             <span>Services</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="pagesDropdown">
             <a class="dropdown-item" href="admin-add-service.php"><i class="fas fa-plus-square"></i> Add</a>
             <a class="dropdown-item" href="admin-manage-service.php"><i class="fas fa-edit"></i> Manage</a>
         </div>
     </li>
     
     <!-- 4. Users -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-users"></i>
             <span>Users</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="pagesDropdown">
             <a class="dropdown-item" href="admin-add-user.php"><i class="fas fa-user-plus"></i> Add</a>
             <a class="dropdown-item" href="admin-manage-user.php"><i class="fas fa-user-edit"></i> Manage</a>
             <div class="dropdown-divider"></div>
             <a class="dropdown-item" href="admin-manage-user-passwords.php"><i class="fas fa-key"></i> Manage Passwords</a>
         </div>
     </li>
     
     <!-- 5. Feedbacks -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-comment-dots"></i>
             <span>Feedbacks</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="pagesDropdown">
             <a class="dropdown-item" href="admin-add-feedback.php"><i class="fas fa-plus"></i> Add Feedback</a>
             <a class="dropdown-item" href="admin-manage-feedback.php"><i class="fas fa-edit"></i> Manage Feedbacks</a>
             <a class="dropdown-item" href="admin-view-feedback.php"><i class="fas fa-comments"></i> View All</a>
             <a class="dropdown-item" href="admin-publish-feedback.php"><i class="fas fa-thumbs-up"></i> Publish</a>
         </div>
     </li>

     <!-- 6. System Logs -->
     <li class="nav-item">
         <a class="nav-link" href="admin-view-syslogs.php">
             <i class="fas fa-fw fa-file-alt"></i>
             <span>System Logs</span></a>
     </li>

     <!-- Recycle Bin -->
     <li class="nav-item">
         <a class="nav-link" href="admin-recycle-bin.php">
             <i class="fas fa-fw fa-trash-restore"></i>
             <span>Recycle Bin</span></a>
     </li>

     <!-- 7. Settings -->
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-fw fa-cogs"></i>
             <span>Settings</span>
         </a>
         <div class="dropdown-menu" aria-labelledby="pagesDropdown">
             <a class="dropdown-item" href="admin-manage-gallery.php"><i class="fas fa-images"></i> Gallery Images</a>
             <a class="dropdown-item" href="admin-manage-slider.php"><i class="fas fa-sliders-h"></i> Home Slider</a>
         </div>
     </li>
 </ul>