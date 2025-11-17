<?php
            $aid=$_SESSION['u_id'];
            $ret="select * from tms_user where u_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>
<ul class="sidebar navbar-nav">
    <li class="nav-item active">
        <a class="nav-link" href="user-dashboard.php">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="usr-book-service-simple.php">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Book Service</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="user-view-booking.php">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>My Bookings</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="user-track-booking.php">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <span>Track Orders</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="user-give-feedback.php">
            <i class="fas fa-fw fa-comment-dots"></i>
            <span>Give Feedback</span>
        </a>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>My Account</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <h6 class="dropdown-header"><?php echo $row->u_fname;?> <?php echo $row->u_lname;?></h6>
            <a class="dropdown-item" href="user-view-profile.php"><i class="fas fa-id-card"></i> View Profile</a>
            <a class="dropdown-item" href="user-update-profile.php"><i class="fas fa-user-edit"></i> Update Profile</a>
            <a class="dropdown-item" href="user-change-pwd.php"><i class="fas fa-key"></i> Change Password</a>
        </div>
    </li>

</ul>
<?php }?>