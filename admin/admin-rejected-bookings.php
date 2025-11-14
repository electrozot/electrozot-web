<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle reassignment
if(isset($_POST['reassign'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_tech_id = intval($_POST['new_tech_id']);
    
    $update_query = "UPDATE tms_service_booking 
                    SET sb_technician_id = ?, 
                        sb_status = 'Pending',
                        sb_rejection_reason = NULL,
                        sb_rejected_at = NULL
                    WHERE sb_id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param('ii', $new_tech_id, $booking_id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Booking #$booking_id reassigned successfully to new technician!";
    } else {
        $_SESSION['error'] = "Failed to reassign booking: " . $stmt->error;
    }
    header("Location: admin-rejected-bookings.php");
    exit();
}

// Get session messages
if(isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Get rejected bookings
$rejected_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name, s.s_category
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_status = 'Rejected'
                   ORDER BY sb.sb_booking_date DESC";
$rejected_result = $mysqli->query($rejected_query);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Rejected Bookings</li>
                </ol>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-times-circle"></i> Rejected Bookings - Needs Reassignment
                    </div>
                    <div class="card-body">
                        <?php if($rejected_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Date & Time</th>
                                            <th>Rejection Reason</th>
                                            <th>Action</th>
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
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?><br>
                                                    <small><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-danger">Rejected</span><br>
                                                    <small><?php echo htmlspecialchars($booking->sb_rejection_reason); ?></small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" onclick="openReassignModal(<?php echo $booking->sb_id; ?>, '<?php echo $booking->s_category; ?>')">
                                                        <i class="fas fa-user-plus"></i> Reassign
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h4>No Rejected Bookings</h4>
                                <p class="text-muted">All bookings are being handled properly.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <!-- Reassign Modal -->
    <div class="modal fade" id="reassignModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Reassign Technician
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="booking_id">
                        
                        <div class="form-group">
                            <label><strong>Select New Technician:</strong></label>
                            <select name="new_tech_id" id="tech_select" class="form-control" required>
                                <option value="">-- Select Technician --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="reassign" class="btn btn-primary">
                            <i class="fas fa-check"></i> Reassign Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <script>
        function openReassignModal(bookingId, category) {
            document.getElementById('booking_id').value = bookingId;
            
            // Fetch available technicians for this category
            $.ajax({
                url: 'vendor/inc/get-technicians.php',
                method: 'POST',
                data: { category: category },
                success: function(response) {
                    $('#tech_select').html(response);
                }
            });
            
            $('#reassignModal').modal('show');
        }
    </script>
</body>
</html>
