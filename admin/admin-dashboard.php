<?php

  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
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
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-dark o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fas fa-fw fa fa-address-book"></i>
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
                                <div class="mr-5"><span class="badge badge-danger"><?php echo $total_bookings;?></span> All Bookings</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="admin-manage-all-bookings.php">
                                <span class="float-left">View Details</span>
                                <span class="float-right">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Services second -->
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-dark o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fas fa-fw fa-cogs"></i>
                                </div>
                                <?php
                                  //count services
                                  $result = "SELECT count(*) FROM tms_service";
                                  $stmt = $mysqli->prepare($result);
                                  $stmt->execute();
                                  $stmt->bind_result($services);
                                  $stmt->fetch();
                                  $stmt->close();
                                ?>
                                <div class="mr-5"><span class="badge badge-danger"><?php echo $services;?></span> Services</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="admin-view-service.php">
                                <span class="float-left">View Details</span>
                                <span class="float-right">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Technicians third -->
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-dark o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fas fa-fw fa-tools"></i>
                                </div>
                                <?php
                  //code for summing up number of technicians
                  $result ="SELECT count(*) FROM tms_technician";
                  $stmt = $mysqli->prepare($result);
                  $stmt->execute();
                  $stmt->bind_result($technician);
                  $stmt->fetch();
                  $stmt->close();
                ?>
                                <div class="mr-5"><span class="badge badge-danger"><?php echo $technician;?></span> Technicians</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="admin-view-technician.php">
                                <span class="float-left">View Details</span>
                                <span class="float-right">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Users fourth -->
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-dark o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fas fa-fw fa-users"></i>
                                </div>
                                <?php
                  //code for summing up number of users 
                  $result ="SELECT count(*) FROM tms_user where u_category = 'User'";
                  $stmt = $mysqli->prepare($result);
                  $stmt->execute();
                  $stmt->bind_result($user);
                  $stmt->fetch();
                  $stmt->close();
                ?>
                                <div class="mr-5"><span class="badge badge-danger"><?php echo $user;?></span> Users</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="admin-view-user.php">
                                <span class="float-left">View Details</span>
                                <span class="float-right">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <!--All Bookings-->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-table"></i>
                        Recent Bookings
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="form-inline">
                                <label for="dashboardStatusFilter" class="mr-2 font-weight-bold">Status</label>
                                <select id="dashboardStatusFilter" class="form-control form-control-sm mr-3">
                                    <option value="all">All</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <input id="dashboardSearch" class="form-control form-control-sm" style="max-width:280px;" type="search" placeholder="Search recent bookings...">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Booking Type</th>
                                        <th>Details</th>
                                        <th>Booking Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Service Bookings (only unassigned, most recent)
                                    $ret_service = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name 
                                                    FROM tms_service_booking sb
                                                    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                                                    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                                                    WHERE sb.sb_technician_id IS NULL AND (sb.sb_status = 'Pending' OR sb.sb_status = 'Approved')
                                                    ORDER BY sb.sb_created_at DESC LIMIT 5";
                                    $stmt_service = $mysqli->prepare($ret_service);
                                    $stmt_service->execute();
                                    $res_service = $stmt_service->get_result();
                                    $cnt = 1;
                                    while($row_service = $res_service->fetch_object()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt;?></td>
                                        <td><?php echo $row_service->u_fname;?> <?php echo $row_service->u_lname;?></td>
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
                                    // Legacy Bookings
                                    $ret_legacy = "SELECT * FROM tms_user where t_booking_status = 'Approved' || t_booking_status = 'Pending' ORDER BY u_id DESC LIMIT 5";
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
                                            } else {
                                                echo '<span class="badge badge-success">'.$row_legacy->t_booking_status.'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="admin-approve-booking.php?u_id=<?php echo $row_legacy->u_id;?>" class="badge badge-success"><i class="fa fa-check"></i> Approve</a>
                                            <a href="admin-delete-booking.php?u_id=<?php echo $row_legacy->u_id;?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
                        <?php
                        date_default_timezone_set("Africa/Nairobi");
                        echo "Generated : " . date("h:i:sa");
                        ?>
                    </div>
                </div>

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

</body>
<!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->

</html>