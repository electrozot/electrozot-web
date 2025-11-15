<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
?>
<!DOCTYPE html>
<html lang="en">

<?php include('vendor/inc/head.php');?>

<body id="page-top">

    <?php include("vendor/inc/nav.php");?>


    <div id="wrapper">

        <!-- Sidebar -->
        <?php include('vendor/inc/sidebar.php');?>

        <div id="content-wrapper">

            <div class="container-fluid">
                <p>
                </p>
                <!-- Breadcrumbs-->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">System Logs</li>
                </ol>
                <?php
                // Check if syslogs table exists, if not create it
                $table_check = $mysqli->query("SHOW TABLES LIKE 'tms_syslogs'");
                if($table_check->num_rows == 0) {
                    $create_table = "CREATE TABLE IF NOT EXISTS tms_syslogs (
                        log_id INT AUTO_INCREMENT PRIMARY KEY,
                        u_email VARCHAR(200),
                        u_ip VARCHAR(50),
                        u_city VARCHAR(100),
                        u_country VARCHAR(100),
                        u_logintime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        log_type VARCHAR(50) DEFAULT 'login',
                        user_type VARCHAR(50) DEFAULT 'admin'
                    )";
                    $mysqli->query($create_table);
                }
                ?>
                
                <!--System Logs-->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-file-alt"></i>
                        <strong>System Activity Logs</strong>
                    </div>
                    <div class="card-body p-2">
                        <?php
                        // Check if there are any logs
                        $count_query = "SELECT COUNT(*) as total FROM tms_syslogs";
                        $count_result = $mysqli->query($count_query);
                        $count_data = $count_result->fetch_object();
                        
                        if($count_data->total == 0) {
                        ?>
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> No System Logs Yet</h5>
                            <p class="mb-0">System logs will appear here when users log in. The logging system is now active and will track:</p>
                            <ul class="mt-2 mb-0">
                                <li>Admin login activities</li>
                                <li>User login activities</li>
                                <li>Technician login activities</li>
                                <li>IP addresses and locations</li>
                            </ul>
                        </div>
                        <?php
                        } else {
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm" id="dataTable" width="100%" cellspacing="0" style="font-size: 0.875rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>User Email</th>
                                        <th style="width: 120px;">User Type</th>
                                        <th style="width: 130px;">IP Address</th>
                                        <th>City</th>
                                        <th>Country</th>
                                        <th style="width: 150px;">Login Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret="SELECT * FROM tms_syslogs ORDER BY u_logintime DESC LIMIT 100";
                                    $stmt= $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res=$stmt->get_result();
                                    $cnt=1;
                                    while($row=$res->fetch_object())
                                    {
                                        $user_type = isset($row->user_type) ? $row->user_type : 'Unknown';
                                        $badge_class = 'badge-secondary';
                                        if($user_type == 'admin') $badge_class = 'badge-danger';
                                        elseif($user_type == 'user') $badge_class = 'badge-primary';
                                        elseif($user_type == 'technician') $badge_class = 'badge-success';
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $cnt;?></td>
                                        <td><?php echo htmlspecialchars($row->u_email);?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $badge_class;?> badge-pill">
                                                <?php echo ucfirst($user_type);?>
                                            </span>
                                        </td>
                                        <td class="text-center"><code><?php echo htmlspecialchars($row->u_ip);?></code></td>
                                        <td><?php echo htmlspecialchars($row->u_city ? $row->u_city : 'N/A');?></td>
                                        <td><?php echo htmlspecialchars($row->u_country ? $row->u_country : 'N/A');?></td>
                                        <td class="text-center"><?php echo date('M d, Y h:i A', strtotime($row->u_logintime));?></td>
                                    </tr>
                                    <?php $cnt = $cnt +1; }?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="card-footer small text-muted py-1" style="font-size: 0.75rem;">
                        <?php
                        date_default_timezone_set("Africa/Nairobi");
                        echo "Last updated: " . date("M d, Y h:i:s A");
                        ?>
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
        <script src="vendor/datatables/jquery.dataTables.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin.min.js"></script>

        <!-- Demo scripts for this page-->
        <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
<!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->