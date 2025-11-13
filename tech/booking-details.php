<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Booking Details";

// Get booking ID
$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle status update
if(isset($_POST['update_status'])){
    $new_status = $_POST['sb_status'];
    $update_query = "UPDATE tms_service_booking SET sb_status=? WHERE sb_id=? AND sb_technician_id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('sii', $new_status, $sb_id, $t_id);
    
    if($update_stmt->execute()){
        $success = "Booking status updated successfully!";
    } else {
        $error = "Failed to update status. Please try again.";
    }
}

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, u.u_addr, s.s_name, s.s_price, s.s_description, s.s_category
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ? AND sb.sb_technician_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $sb_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header('Location: my-bookings.php');
    exit();
}

$booking = $result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>
                        <i class="fas fa-file-alt" style="color: var(--primary);"></i>
                        Booking Details
                    </h2>
                    <p>Booking ID: #<?php echo $sb_id; ?></p>
                </div>
                <a href="my-bookings.php" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-left"></i> Back to Bookings
                </a>
            </div>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Customer Information -->
            <div class="col-md-6 mb-4">
                <div class="card-custom">
                    <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 25px; border-bottom: 3px solid var(--primary); padding-bottom: 15px;">
                        <i class="fas fa-user" style="color: var(--primary);"></i>
                        Customer Information
                    </h5>
                    
                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Name</label>
                        <p style="font-size: 1.1rem; color: #2d3748; margin: 5px 0;">
                            <i class="fas fa-user-circle" style="color: var(--primary);"></i>
                            <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?>
                        </p>
                    </div>

                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Phone</label>
                        <p style="font-size: 1.1rem; color: #2d3748; margin: 5px 0;">
                            <a href="tel:<?php echo $booking->u_phone; ?>" class="btn btn-success-custom btn-sm">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking->u_phone); ?>
                            </a>
                        </p>
                    </div>

                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Email</label>
                        <p style="font-size: 1rem; color: #2d3748; margin: 5px 0;">
                            <i class="fas fa-envelope" style="color: var(--primary);"></i>
                            <?php echo htmlspecialchars($booking->u_email); ?>
                        </p>
                    </div>

                    <div class="info-item">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Address</label>
                        <p style="font-size: 1rem; color: #2d3748; margin: 5px 0; line-height: 1.6;">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i>
                            <?php echo htmlspecialchars($booking->sb_address); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="col-md-6 mb-4">
                <div class="card-custom">
                    <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 25px; border-bottom: 3px solid var(--primary); padding-bottom: 15px;">
                        <i class="fas fa-tools" style="color: var(--primary);"></i>
                        Service Information
                    </h5>
                    
                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Service Name</label>
                        <p style="font-size: 1.1rem; color: #2d3748; margin: 5px 0; font-weight: 600;">
                            <?php echo htmlspecialchars($booking->s_name); ?>
                        </p>
                    </div>

                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Category</label>
                        <p style="font-size: 1rem; color: #2d3748; margin: 5px 0;">
                            <span class="badge-status badge-pending"><?php echo htmlspecialchars($booking->s_category); ?></span>
                        </p>
                    </div>

                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Price</label>
                        <p style="font-size: 1.5rem; color: var(--primary); margin: 5px 0; font-weight: 700;">
                            $<?php echo number_format($booking->sb_total_price, 2); ?>
                        </p>
                    </div>

                    <div class="info-item mb-3">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Scheduled Date & Time</label>
                        <p style="font-size: 1rem; color: #2d3748; margin: 5px 0;">
                            <i class="fas fa-calendar" style="color: var(--primary);"></i>
                            <?php echo date('l, F d, Y', strtotime($booking->sb_booking_date)); ?>
                            <br>
                            <i class="fas fa-clock" style="color: var(--primary);"></i>
                            <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                        </p>
                    </div>

                    <?php if($booking->sb_description): ?>
                    <div class="info-item">
                        <label style="font-weight: 600; color: #6c757d; font-size: 0.9rem;">Additional Notes</label>
                        <p style="font-size: 1rem; color: #2d3748; margin: 5px 0; line-height: 1.6; background: #f8f9fa; padding: 15px; border-radius: 10px;">
                            <?php echo nl2br(htmlspecialchars($booking->sb_description)); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="card-custom">
            <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 25px; border-bottom: 3px solid var(--primary); padding-bottom: 15px;">
                <i class="fas fa-tasks" style="color: var(--primary);"></i>
                Update Booking Status
            </h5>
            
            <form method="POST" class="row align-items-end">
                <div class="col-md-6 mb-3">
                    <label style="font-weight: 600; color: #2d3748;">
                        <i class="fas fa-info-circle"></i> Current Status
                    </label>
                    <div style="margin-top: 10px;">
                        <?php
                        $status_class = '';
                        if($booking->sb_status == 'Pending') $status_class = 'badge-pending';
                        elseif($booking->sb_status == 'Completed') $status_class = 'badge-completed';
                        else $status_class = 'badge-cancelled';
                        ?>
                        <span class="badge-status <?php echo $status_class; ?>" style="font-size: 1.1rem; padding: 12px 25px;">
                            <?php echo $booking->sb_status; ?>
                        </span>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label style="font-weight: 600; color: #2d3748;">
                        <i class="fas fa-edit"></i> Change Status To
                    </label>
                    <select name="sb_status" class="form-control" required style="border-radius: 10px; padding: 12px; font-weight: 600;">
                        <option value="Pending" <?php echo $booking->sb_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?php echo $booking->sb_status == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Completed" <?php echo $booking->sb_status == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo $booking->sb_status == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <button type="submit" name="update_status" class="btn btn-primary-custom btn-block">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
