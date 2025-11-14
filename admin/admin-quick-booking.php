<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle form submission
if(isset($_POST['create_booking'])) {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_email = $_POST['customer_email'];
    $customer_address = $_POST['customer_address'];
    $customer_area = $_POST['customer_area'];
    $customer_pincode = $_POST['customer_pincode'];
    $service_id = $_POST['service_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $notes = $_POST['notes'];
    
    // Check if user exists by phone
    $check_user = "SELECT u_id FROM tms_user WHERE u_phone = ?";
    $stmt_check = $mysqli->prepare($check_user);
    $stmt_check->bind_param('s', $customer_phone);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if($result->num_rows > 0) {
        // User exists
        $user = $result->fetch_object();
        $user_id = $user->u_id;
    } else {
        // Create new user with area and pincode
        $password = password_hash('electrozot123', PASSWORD_DEFAULT);
        $insert_user = "INSERT INTO tms_user (u_fname, u_phone, u_email, u_addr, u_area, u_pincode, u_pwd, u_category, registration_type) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'User', 'admin')";
        $stmt_user = $mysqli->prepare($insert_user);
        $stmt_user->bind_param('sssssss', $customer_name, $customer_phone, $customer_email, $customer_address, $customer_area, $customer_pincode, $password);
        $stmt_user->execute();
        $user_id = $mysqli->insert_id;
    }
    
    // Get service price
    $price_query = "SELECT s_price FROM tms_service WHERE s_id = ?";
    $stmt_price = $mysqli->prepare($price_query);
    $stmt_price->bind_param('i', $service_id);
    $stmt_price->execute();
    $price_result = $stmt_price->get_result();
    $service = $price_result->fetch_object();
    $total_price = $service->s_price;
    
    // Create booking with pincode
    $insert_booking = "INSERT INTO tms_service_booking 
                      (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_phone, sb_address, sb_pincode, sb_description, sb_status, sb_total_price) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $stmt_booking = $mysqli->prepare($insert_booking);
    $stmt_booking->bind_param('iissssssd', $user_id, $service_id, $booking_date, $booking_time, $customer_phone, $customer_address, $customer_pincode, $notes, $total_price);
    
    if($stmt_booking->execute()) {
        $success = "Booking created successfully! Booking ID: " . $mysqli->insert_id;
    } else {
        $err = "Failed to create booking. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Quick Booking - Admin</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
</head>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Quick Booking</li>
                </ol>
                
                <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>
                
                <?php if(isset($err)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>
                
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-phone-alt"></i> Quick Booking - For Phone Orders
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary"><i class="fas fa-user"></i> Customer Information</h5>
                                    <hr>
                                    
                                    <div class="form-group">
                                        <label>Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_name" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_phone" class="form-control" required>
                                        <small class="form-text text-muted">If customer exists, we'll use their account</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="customer_email" class="form-control">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Address <span class="text-danger">*</span></label>
                                        <textarea name="customer_address" class="form-control" rows="2" required></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Area <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_area" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pincode <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_pincode" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5 class="text-primary"><i class="fas fa-calendar-check"></i> Booking Details</h5>
                                    <hr>
                                    
                                    <div class="form-group">
                                        <label>Service <span class="text-danger">*</span></label>
                                        <select name="service_id" class="form-control" required>
                                            <option value="">-- Select Service --</option>
                                            <?php
                                            $services = "SELECT * FROM tms_service ORDER BY s_name";
                                            $stmt_services = $mysqli->prepare($services);
                                            $stmt_services->execute();
                                            $res_services = $stmt_services->get_result();
                                            while($service = $res_services->fetch_object()):
                                            ?>
                                            <option value="<?php echo $service->s_id; ?>">
                                                <?php echo $service->s_name; ?> - ₹<?php echo $service->s_price; ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Booking Date <span class="text-danger">*</span></label>
                                        <input type="date" name="booking_date" class="form-control" 
                                               min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Preferred Time <span class="text-danger">*</span></label>
                                        <input type="time" name="booking_time" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Notes / Special Instructions</label>
                                        <textarea name="notes" class="form-control" rows="3" 
                                                  placeholder="Any special requirements or notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="text-center">
                                <button type="submit" name="create_booking" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle"></i> Create Booking
                                </button>
                                <a href="admin-dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
            
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>&times;</span>
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
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
</body>
</html>
