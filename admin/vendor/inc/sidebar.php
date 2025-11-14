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
             <a class="dropdown-item" href="admin-all-bookings.php"><i class="fas fa-list-ul"></i> All Bookings</a>
             <a class="dropdown-item" href="admin-add-booking.php"><i class="fas fa-plus-circle"></i> Add</a>
             <a class="dropdown-item" href="admin-view-booking.php"><i class="fas fa-eye"></i> View</a>
             <a class="dropdown-item" href="admin-manage-booking.php"><i class="fas fa-tasks"></i> Manage</a>
             <a class="dropdown-item" href="admin-manage-service-booking.php"><i class="fas fa-clipboard-list"></i> Service Bookings</a>
             <a class="dropdown-item" href="admin-completed-bookings.php"><i class="fas fa-check-double"></i> Completed (Images)</a>
             <a class="dropdown-item" href="admin-rejected-bookings.php"><i class="fas fa-ban"></i> Rejected</a>
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
             <a class="dropdown-item" href="admin-view-technician.php"><i class="fas fa-id-card"></i> View</a>
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
             <a class="dropdown-item" href="admin-view-service.php"><i class="fas fa-list"></i> View</a>
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
             <a class="dropdown-item" href="admin-view-user.php"><i class="fas fa-address-book"></i> View</a>
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