<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Check if booking exists and belongs to user
$check_query = "SELECT sb_id, sb_technician_id, sb_status FROM tms_service_booking WHERE sb_id = ? AND sb_user_id = ?";
$check_stmt = $mysqli->prepare($check_query);
$check_stmt->bind_param('ii', $booking_id, $aid);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$booking = $check_result->fetch_object();

if(!$booking) {
    $_SESSION['error'] = "Booking not found or you don't have permission to cancel it.";
    header("Location: user-view-booking.php");
    exit();
}

// Check if technician is already assigned
if(!empty($booking->sb_technician_id)) {
    $_SESSION['error'] = "Cannot cancel booking. A technician has already been assigned. Please contact support.";
    header("Location: user-booking-details.php?booking_id=" . $booking_id);
    exit();
}

// Check if booking is already cancelled or completed
if($booking->sb_status == 'Cancelled' || $booking->sb_status == 'Completed') {
    $_SESSION['error'] = "This booking cannot be cancelled as it is already " . strtolower($booking->sb_status) . ".";
    header("Location: user-booking-details.php?booking_id=" . $booking_id);
    exit();
}

// Handle cancellation
if(isset($_POST['confirm_cancel'])) {
    $cancel_reason = isset($_POST['cancel_reason']) ? $_POST['cancel_reason'] : 'Cancelled by customer';
    
    // Update booking status to Cancelled
    $update_query = "UPDATE tms_service_booking SET sb_status = 'Cancelled' WHERE sb_id = ? AND sb_user_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('ii', $booking_id, $aid);
    
    if($update_stmt->execute()) {
        $_SESSION['success'] = "Booking cancelled successfully.";
        header("Location: user-view-booking.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to cancel booking. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include("vendor/inc/nav.php"); ?>
    
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php"); ?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="user-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="user-view-booking.php">My Bookings</a></li>
                    <li class="breadcrumb-item active">Cancel Booking</li>
                </ol>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg" style="border: none; border-radius: 15px;">
                    <div class="card-header bg-danger text-white" style="border-radius: 15px 15px 0 0;">
                        <h5 class="m-0"><i class="fas fa-times-circle"></i> Cancel Booking #<?php echo $booking_id; ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Are you sure?</h5>
                            <p class="mb-0">You are about to cancel this booking. This action cannot be undone.</p>
                        </div>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="cancel_reason">Reason for Cancellation (Optional)</label>
                                <textarea class="form-control" name="cancel_reason" id="cancel_reason" rows="3" placeholder="Please tell us why you're cancelling..."></textarea>
                            </div>
                            
                            <hr>
                            
                            <button type="submit" name="confirm_cancel" class="btn btn-danger btn-lg">
                                <i class="fas fa-times-circle"></i> Confirm Cancellation
                            </button>
                            <a href="user-booking-details.php?booking_id=<?php echo $booking_id; ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Go Back
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php include("vendor/inc/footer.php"); ?>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="user-logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
</body>
</html>
