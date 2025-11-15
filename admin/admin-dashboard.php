<?php

  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  // Handle reassignment from dashboard
  if(isset($_POST['reassign_booking'])) {
      $booking_id = intval($_POST['booking_id']);
      $new_tech_id = intval($_POST['new_tech_id']);
      
      $update_query = "UPDATE tms_service_booking 
                      SET sb_technician_id = ?, 
                          sb_status = 'Pending',
                          sb_rejection_reason = NULL
                      WHERE sb_id = ?";
      $stmt = $mysqli->prepare($update_query);
      $stmt->bind_param('ii', $new_tech_id, $booking_id);
      
      if($stmt->execute()) {
          $_SESSION['dashboard_success'] = "Booking #$booking_id reassigned successfully!";
      } else {
          $_SESSION['dashboard_error'] = "Failed to reassign booking.";
      }
      header("Location: admin-dashboard.php");
      exit();
  }
  
  // Get session messages
  $dashboard_success = isset($_SESSION['dashboard_success']) ? $_SESSION['dashboard_success'] : '';
  $dashboard_error = isset($_SESSION['dashboard_error']) ? $_SESSION['dashboard_error'] : '';
  unset($_SESSION['dashboard_success']);
  unset($_SESSION['dashboard_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Technician Booking System - Book Professional Technicians">
    <meta name="author" content="MartDevelopers">

    <title>Technician Booking System - Admin Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="vendor/css/sb-admin.css" rel="stylesheet">

</head>

<body id="page-top">
    <!--Start Navigation Bar-->
    <?php include("vendor/inc/nav.php");?>
    <!--Navigation Bar-->

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include("vendor/inc/sidebar.php");?>
        <!--End Sidebar-->
        <div id="content-wrapper">

            <div class="container-fluid">
                <!-- Success/Error Messages -->
                <?php if(!empty($dashboard_success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $dashboard_success; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                <?php if(!empty($dashboard_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $dashboard_error; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <!-- Breadcrumbs-->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Overview</li>
                </ol>
                <!-- Icon Cards-->
                <div class="row">
                    <!-- Bookings first -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-calendar-check" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                                // Count all bookings
                                $result_service = "SELECT count(*) FROM tms_service_booking";
                                $stmt_service = $mysqli->prepare($result_service);
                                $stmt_service->execute();
                                $stmt_service->bind_result($service_book);
                                $stmt_service->fetch();
                                $stmt_service->close();

                                $result_legacy = "SELECT count(*) FROM tms_user where t_booking_status = 'Approved' || t_booking_status = 'Pending' ";
                                $stmt_legacy = $mysqli->prepare($result_legacy);
                                $stmt_legacy->execute();
                                $stmt_legacy->bind_result($legacy_book);
                                $stmt_legacy->fetch();
                                $stmt_legacy->close();

                                $total_bookings = $service_book + $legacy_book;
                                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700;"><?php echo $total_bookings;?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">All Bookings</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-all-bookings.php" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">View</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Unassigned Bookings -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                                $unassigned_query = "SELECT COUNT(*) FROM tms_service_booking 
                                                    WHERE sb_technician_id IS NULL 
                                                    AND sb_status NOT IN ('Rejected', 'Cancelled', 'Completed')";
                                $stmt_unassigned = $mysqli->prepare($unassigned_query);
                                $stmt_unassigned->execute();
                                $stmt_unassigned->bind_result($unassigned_count);
                                $stmt_unassigned->fetch();
                                $stmt_unassigned->close();
                                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700;"><?php echo $unassigned_count;?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">Unassigned</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-all-bookings.php?technician=unassigned" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">Assign Now</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Rejected Bookings -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-ban" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                                $rejected_query = "SELECT COUNT(*) FROM tms_service_booking 
                                                  WHERE sb_status = 'Rejected' OR sb_status = 'Cancelled'";
                                $stmt_rejected = $mysqli->prepare($rejected_query);
                                $stmt_rejected->execute();
                                $stmt_rejected->bind_result($rejected_count);
                                $stmt_rejected->fetch();
                                $stmt_rejected->close();
                                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700;"><?php echo $rejected_count;?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">Rejected</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-all-bookings.php?status=Rejected" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">View</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Today's Sales -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-rupee-sign" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                                $today_sales_query = "SELECT SUM(sb_total_price) FROM tms_service_booking 
                                                     WHERE DATE(sb_created_at) = CURDATE() AND sb_status = 'Completed'";
                                $stmt_today = $mysqli->prepare($today_sales_query);
                                $stmt_today->execute();
                                $stmt_today->bind_result($today_sales);
                                $stmt_today->fetch();
                                $stmt_today->close();
                                $today_sales = $today_sales ? $today_sales : 0;
                                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.3rem; font-weight: 700;">₹<?php echo number_format($today_sales, 0);?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">Today's Sales</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-all-bookings.php?date=today&status=Completed" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">View</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-wrench" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                                  $result = "SELECT count(*) FROM tms_service";
                                  $stmt = $mysqli->prepare($result);
                                  $stmt->execute();
                                  $stmt->bind_result($services);
                                  $stmt->fetch();
                                  $stmt->close();
                                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700;"><?php echo $services;?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">Services</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-view-service.php" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">View</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Technicians -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-user-cog" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                  $result ="SELECT count(*) FROM tms_technician";
                  $stmt = $mysqli->prepare($result);
                  $stmt->execute();
                  $stmt->bind_result($technician);
                  $stmt->fetch();
                  $stmt->close();
                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700;"><?php echo $technician;?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">Technicians</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-view-technician.php" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">View</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Users -->
                    <div class="col-xl col-lg col-md-3 col-sm-6 mb-2">
                        <div class="card text-white o-hidden shadow" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; border-radius: 10px;">
                            <div class="card-body p-3">
                                <div class="card-body-icon" style="opacity: 0.2; position: absolute; right: 10px; top: 10px;">
                                    <i class="fas fa-users-cog" style="font-size: 2rem;"></i>
                                </div>
                                <?php
                  $result ="SELECT count(*) FROM tms_user where u_category = 'User'";
                  $stmt = $mysqli->prepare($result);
                  $stmt->execute();
                  $stmt->bind_result($user);
                  $stmt->fetch();
                  $stmt->close();
                ?>
                                <div style="position: relative; z-index: 2;">
                                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700;"><?php echo $user;?></h3>
                                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.9;">Users</p>
                                </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1 py-1" href="admin-view-user.php" style="background: rgba(0,0,0,0.2); border: none; font-size: 0.75rem;">
                                <span class="float-left">View</span>
                                <span class="float-right">
                                    <i class="fas fa-arrow-circle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!--Recent Bookings-->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0" style="color: #2d3748;">
                                    <i class="fas fa-clipboard-list text-primary"></i> Recent Bookings
                                </h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <input id="dashboardSearch" class="form-control form-control-sm d-inline-block" style="max-width:250px;" type="search" placeholder="🔍 Search...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm" id="dataTable" width="100%" cellspacing="0" style="font-size: 0.875rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th style="width: 120px;">Type</th>
                                        <th>Details</th>
                                        <th style="width: 110px;">Date</th>
                                        <th style="width: 90px;">Status</th>
                                        <th style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Service Bookings - Unassigned (Pending/Approved) OR Rejected
                                    $ret_service = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name 
                                                    FROM tms_service_booking sb
                                                    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                                                    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                                                    WHERE (sb.sb_technician_id IS NULL AND sb.sb_status NOT IN ('Cancelled', 'Completed'))
                                                    OR sb.sb_status = 'Rejected'
                                                    ORDER BY sb.sb_created_at DESC
                                                    LIMIT 20";
                                    $stmt_service = $mysqli->prepare($ret_service);
                                    $stmt_service->execute();
                                    $res_service = $stmt_service->get_result();
                                    $cnt = 1;
                                    while($row_service = $res_service->fetch_object()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt;?></td>
                                        <td>
                                            <?php 
                                            if(!empty($row_service->u_fname)) {
                                                echo $row_service->u_fname . ' ' . $row_service->u_lname;
                                            } else {
                                                echo '<span class="text-muted">Customer</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $row_service->sb_phone;?></td>
                                        <td><span class="badge badge-primary">Service Booking</span></td>
                                        <td><?php echo $row_service->s_name;?></td>
                                        <td><?php echo date('M d, Y', strtotime($row_service->sb_booking_date));?></td>
                                        <td>
                                            <?php 
                                            if($row_service->sb_status == "Pending"){
                                                echo '<span class="badge badge-warning">'.$row_service->sb_status.'</span>';
                                            } elseif($row_service->sb_status == "Approved"){
                                                echo '<span class="badge badge-info">'.$row_service->sb_status.'</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">'.$row_service->sb_status.'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="admin-view-service-booking.php?sb_id=<?php echo $row_service->sb_id;?>" class="badge badge-info">View</a>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>

                                    <?php
                                    // Legacy Bookings - Only Pending (Unapproved) or Rejected
                                    $ret_legacy = "SELECT * FROM tms_user 
                                                  WHERE t_booking_status = 'Pending' 
                                                  OR t_booking_status = 'Rejected' 
                                                  ORDER BY u_id DESC LIMIT 10";
                                    $stmt_legacy = $mysqli->prepare($ret_legacy);
                                    $stmt_legacy->execute();
                                    $res_legacy = $stmt_legacy->get_result();
                                    while($row_legacy = $res_legacy->fetch_object()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt;?></td>
                                        <td><?php echo $row_legacy->u_fname;?> <?php echo $row_legacy->u_lname;?></td>
                                        <td><?php echo $row_legacy->u_phone;?></td>
                                        <td><span class="badge badge-secondary">Legacy Booking</span></td>
                                        <td>Technician: <?php echo $row_legacy->t_tech_category;?></td>
                                        <td><?php echo $row_legacy->t_booking_date;?></td>
                                        <td>
                                            <?php 
                                            if($row_legacy->t_booking_status == "Pending"){
                                                echo '<span class="badge badge-warning">'.$row_legacy->t_booking_status.'</span>';
                                            } elseif($row_legacy->t_booking_status == "Rejected"){
                                                echo '<span class="badge badge-danger">'.$row_legacy->t_booking_status.'</span>';
                                            } else {
                                                echo '<span class="badge badge-info">'.$row_legacy->t_booking_status.'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($row_legacy->t_booking_status == "Pending"): ?>
                                                <a href="admin-approve-booking.php?u_id=<?php echo $row_legacy->u_id;?>" class="badge badge-success"><i class="fa fa-check"></i> Approve</a>
                                            <?php endif; ?>
                                            <a href="admin-delete-booking.php?u_id=<?php echo $row_legacy->u_id;?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer small text-muted py-1" style="font-size: 0.75rem;">
                        <?php
                        date_default_timezone_set("Africa/Nairobi");
                        echo "Generated: " . date("h:i:sa");
                        ?>
                    </div>
                </div>

                <!-- Rejected/Cancelled Bookings Section -->
                <?php
                $rejected_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name, s.s_category, t.t_name as technician_name
                                   FROM tms_service_booking sb
                                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                                   WHERE sb.sb_status = 'Rejected' OR sb.sb_status = 'Cancelled'
                                   ORDER BY sb.sb_booking_date DESC
                                   LIMIT 10";
                $rejected_result = $mysqli->query($rejected_query);
                
                if($rejected_result && $rejected_result->num_rows > 0):
                ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-danger text-white py-2" style="font-size: 0.95rem;">
                        <i class="fas fa-exclamation-triangle"></i> Rejected/Cancelled Bookings
                        <span class="badge badge-light float-right"><?php echo $rejected_result->num_rows; ?></span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" style="font-size: 0.875rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th style="width: 100px;">Category</th>
                                        <th>Technician</th>
                                        <th style="width: 100px;">Date</th>
                                        <th style="width: 90px;">Status</th>
                                        <th>Reason</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($booking = $rejected_result->fetch_object()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?><br>
                                            <small><i class="fas fa-phone"></i> <?php echo $booking->u_phone; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking->s_name); ?></td>
                                        <td><span class="badge badge-secondary"><?php echo $booking->s_category; ?></span></td>
                                        <td>
                                            <?php if($booking->technician_name): ?>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($booking->technician_name); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted"><i>Not assigned</i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?></td>
                                        <td><span class="badge badge-danger"><?php echo $booking->sb_status; ?></span></td>
                                        <td><small><?php echo htmlspecialchars(substr($booking->sb_rejection_reason, 0, 50)); ?></small></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="openReassignModal(<?php echo $booking->sb_id; ?>, '<?php echo addslashes($booking->s_category); ?>', '<?php echo addslashes($booking->s_name); ?>')">
                                                <i class="fas fa-user-plus"></i> Reassign
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="admin-rejected-bookings.php" class="btn btn-danger btn-sm">
                            <i class="fas fa-list"></i> View All Rejected Bookings
                        </a>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <!-- /.container-fluid -->

            <!-- Sticky Footer -->
            <?php include("vendor/inc/footer.php");?>

        </div>
        <!-- /.content-wrapper -->

    </div>
    <!-- /#wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
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
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Page level plugin JavaScript-->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="vendor/js/sb-admin.min.js"></script>

    <!-- Demo scripts for this page-->
    <script src="vendor/js/demo/datatables-demo.js"></script>
    <script src="vendor/js/demo/chart-area-demo.js"></script>
    <script>
        // Initialize DataTable and wire up dashboard filters
        $(document).ready(function() {
            var table;
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                table = $('#dataTable').DataTable();
            } else {
                table = $('#dataTable').DataTable({
                    order: [[0, 'desc']]
                });
            }

            // Status filter (Status column index = 6)
            $('#dashboardStatusFilter').on('change', function() {
                var val = this.value;
                if (val === 'all') {
                    table.column(6).search('').draw();
                } else {
                    table.column(6).search('^' + val + '$', true, false).draw();
                }
            });

            // Global search
            $('#dashboardSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
    
    <!-- Reassign Modal -->
    <div class="modal fade" id="reassignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Reassign Technician</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="booking_id">
                        <div class="form-group">
                            <label><strong>Select Technician:</strong></label>
                            <select name="new_tech_id" id="tech_select" class="form-control" required>
                                <option value="">-- Loading technicians... --</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Showing available technicians for this service category
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="reassign_booking" class="btn btn-primary">
                            <i class="fas fa-check"></i> Reassign Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function openReassignModal(bookingId, category, serviceName) {
            document.getElementById('booking_id').value = bookingId;
            
            // Fetch available technicians matching service name OR category (same logic as assignment)
            $.ajax({
                url: 'vendor/inc/get-technicians.php',
                method: 'POST',
                data: { 
                    category: category,
                    service_name: serviceName || category
                },
                success: function(response) {
                    $('#tech_select').html(response);
                }
            });
            
            $('#reassignModal').modal('show');
        }
    </script>

</body>
<!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->

</html>