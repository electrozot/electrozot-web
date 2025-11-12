<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['u_id'];
  
  //Get user details
  $user_id = $_SESSION['u_id'];
  $user_query = "SELECT * FROM tms_user WHERE u_id = ?";
  $user_stmt = $mysqli->prepare($user_query);
  $user_stmt->bind_param('i', $user_id);
  $user_stmt->execute();
  $user_result = $user_stmt->get_result();
  $user_data = $user_result->fetch_object();
  
  //Add Service Booking
  if(isset($_POST['book_service']))
    {
            $sb_user_id = $_SESSION['u_id'];
            $sb_service_id = $_POST['sb_service_id'];
            $sb_booking_date = $_POST['sb_booking_date'];
            $sb_booking_time = $_POST['sb_booking_time'];
            $sb_address = $_POST['sb_address'];
            $sb_phone = $_POST['sb_phone'];
            $sb_description = $_POST['sb_description'];
            $sb_status = 'Pending';
            
            //Get service price
            $service_query = "SELECT s_price FROM tms_service WHERE s_id = ?";
            $service_stmt = $mysqli->prepare($service_query);
            $service_stmt->bind_param('i', $sb_service_id);
            $service_stmt->execute();
            $service_result = $service_stmt->get_result();
            $service_data = $service_result->fetch_object();
            $sb_total_price = $service_data->s_price;
            
            $query="INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_phone, sb_description, sb_status, sb_total_price) VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('iissssssd', $sb_user_id, $sb_service_id, $sb_booking_date, $sb_booking_time, $sb_address, $sb_phone, $sb_description, $sb_status, $sb_total_price);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Service Booking Submitted Successfully";
                }
                else 
                {
                    $err = "Please Try Again Later";
                }
            }
?>
<!DOCTYPE html>
<html lang="en">

<?php include('vendor/inc/head.php');?>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">

            <div class="container-fluid">
                <?php if(isset($succ)) {?>
                <script>
                setTimeout(function() {
                        swal("Success!", "<?php echo $succ;?>!", "success");
                    },
                    100);
                </script>

                <?php } ?>
                <?php if(isset($err)) {?>
                <script>
                setTimeout(function() {
                        swal("Failed!", "<?php echo $err;?>!", "Failed");
                    },
                    100);
                </script>

                <?php } ?>
                <!-- Breadcrumbs-->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="user-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">Service</li>
                    <li class="breadcrumb-item">Book Service</li>
                    <li class="breadcrumb-item active">Confirm Booking</li>
                </ol>
                <hr>
                <div class="card compact-card">
                    <div class="card-header">
                        Confirm Service Booking
                    </div>
                    <div class="card-body">
                        <!--Booking Form-->
                        <?php
            $aid=$_GET['s_id'];
            $ret="SELECT * FROM tms_service WHERE s_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute();
            $res=$stmt->get_result();
            while($row=$res->fetch_object())
        {
        ?>
                        <form method="POST" class="compact-form">
                            <input type="hidden" name="sb_service_id" value="<?php echo $row->s_id;?>">
                            <div class="form-group">
                                <label>Service Name</label>
                                <input type="text" class="form-control" value="<?php echo $row->s_name;?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Service Category</label>
                                <input type="text" class="form-control" value="<?php echo $row->s_category;?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Service Price</label>
                                <input type="text" class="form-control" value="$<?php echo number_format($row->s_price, 2);?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Service Duration</label>
                                <input type="text" class="form-control" value="<?php echo $row->s_duration;?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Service Description</label>
                                <textarea class="form-control" rows="3" readonly><?php echo $row->s_description;?></textarea>
                            </div>
                            <hr>
                            <h5>Booking Details</h5>
                            <div class="form-group">
                                <label for="sb_booking_date">Booking Date *</label>
                                <input type="date" required class="form-control" id="sb_booking_date" name="sb_booking_date" min="<?php echo date('Y-m-d');?>">
                            </div>
                            <div class="form-group">
                                <label for="sb_booking_time">Booking Time *</label>
                                <input type="time" required class="form-control" id="sb_booking_time" name="sb_booking_time">
                            </div>
                            <div class="form-group">
                                <label for="sb_address">Service Address *</label>
                                <textarea class="form-control" required name="sb_address" rows="3" placeholder="Enter complete service address"><?php echo $user_data->u_addr;?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="sb_phone">Contact Phone *</label>
                                <input type="text" required class="form-control" id="sb_phone" name="sb_phone" value="<?php echo $user_data->u_phone;?>" placeholder="Enter contact phone number">
                            </div>
                            <div class="form-group">
                                <label for="sb_description">Additional Notes</label>
                                <textarea class="form-control" name="sb_description" rows="3" placeholder="Any additional information or special requirements"></textarea>
                            </div>
                            <hr>
                            <button type="submit" name="book_service" class="btn btn-success">Confirm Service Booking</button>
                            <a href="usr-book-service.php" class="btn btn-secondary">Cancel</a>
                        </form>
                        <!-- End Form-->
                        <?php }?>
                    </div>
                </div>

                <hr>

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
                        <a class="btn btn-danger" href="user-logout.php">Logout</a>
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
        <script src="vendor/js/swal.js"></script>

</body>

</html>

