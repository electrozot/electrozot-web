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
    
    // Start transaction for atomic operation
    $mysqli->begin_transaction();
    
    try {
        // 1. Check if new technician is available
        $check_query = "SELECT t_is_available, t_current_booking_id, t_name FROM tms_technician WHERE t_id = ?";
        $stmt_check = $mysqli->prepare($check_query);
        $stmt_check->bind_param('i', $new_tech_id);
        $stmt_check->execute();
        $tech_result = $stmt_check->get_result();
        $tech = $tech_result->fetch_assoc();
        
        if(!$tech) {
            throw new Exception("Technician not found");
        }
        
        if(!$tech['t_is_available'] && $tech['t_current_booking_id']) {
            throw new Exception("Technician " . $tech['t_name'] . " is already assigned to booking #" . $tech['t_current_booking_id']);
        }
        
        // 2. Get old technician ID to free them up
        $old_tech_query = "SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?";
        $stmt_old = $mysqli->prepare($old_tech_query);
        $stmt_old->bind_param('i', $booking_id);
        $stmt_old->execute();
        $old_result = $stmt_old->get_result();
        $old_booking = $old_result->fetch_assoc();
        $old_tech_id = $old_booking['sb_technician_id'];
        
        // 3. Free up old technician (if exists)
        if($old_tech_id) {
            $free_old = "UPDATE tms_technician 
                        SET t_is_available = 1, 
                            t_current_booking_id = NULL 
                        WHERE t_id = ?";
            $stmt_free = $mysqli->prepare($free_old);
            $stmt_free->bind_param('i', $old_tech_id);
            $stmt_free->execute();
        }
        
        // 4. Reassign booking to new technician (only update essential fields)
        $update_query = "UPDATE tms_service_booking 
                        SET sb_technician_id = ?, 
                            sb_status = 'Pending'
                        WHERE sb_id = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param('ii', $new_tech_id, $booking_id);
        $stmt->execute();
        
        // 4b. Clear rejection/not done fields if they exist
        try {
            $mysqli->query("UPDATE tms_service_booking 
                           SET sb_rejection_reason = NULL, 
                               sb_rejected_at = NULL,
                               sb_not_done_reason = NULL,
                               sb_not_done_at = NULL
                           WHERE sb_id = $booking_id");
        } catch(Exception $e) {
            // Columns might not exist, ignore error
        }
        
        // 5. Mark new technician as unavailable
        $assign_tech = "UPDATE tms_technician 
                       SET t_is_available = 0, 
                           t_current_booking_id = ? 
                       WHERE t_id = ?";
        $stmt_assign = $mysqli->prepare($assign_tech);
        $stmt_assign->bind_param('ii', $booking_id, $new_tech_id);
        $stmt_assign->execute();
        
        // Commit transaction
        $mysqli->commit();
        
        $_SESSION['success'] = "Booking #$booking_id reassigned to " . $tech['t_name'] . " successfully! Old technician is now available.";
    } catch(Exception $e) {
        // Rollback on error
        $mysqli->rollback();
        $_SESSION['error'] = "Reassignment failed: " . $e->getMessage();
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

// Get rejected/not done bookings
$rejected_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name, s.s_category, t.t_name as tech_name,
                   sb.sb_not_done_reason, sb.sb_not_done_at
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                   WHERE sb.sb_status IN ('Rejected', 'Not Done')
                   ORDER BY sb.sb_not_done_at DESC, sb.sb_booking_date DESC";
$rejected_result = $mysqli->query($rejected_query);

// Get count for dashboard
$count_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_status IN ('Rejected', 'Not Done')";
$count_result = $mysqli->query($count_query);
$rejected_count = $count_result->fetch_object()->count;
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
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-times-circle"></i> Rejected / Not Done Bookings - Needs Reassignment
                        <span class="badge badge-light float-right"><?php echo $rejected_count; ?> Total</span>
                    </div>
                    <div class="card-body">
                        <?php if($rejected_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Technician</th>
                                            <th>Date & Time</th>
                                            <th>Status & Reason</th>
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
                                                    <small><?php echo htmlspecialchars($booking->tech_name); ?></small>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?><br>
                                                    <small><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-danger"><?php echo $booking->sb_status; ?></span><br>
                                                    <small class="text-muted">
                                                        <?php 
                                                        $reason = $booking->sb_not_done_reason ? $booking->sb_not_done_reason : $booking->sb_rejection_reason;
                                                        echo htmlspecialchars(substr($reason, 0, 50));
                                                        if(strlen($reason) > 50) echo '...';
                                                        ?>
                                                    </small><br>
                                                    <?php if($booking->sb_not_done_at): ?>
                                                        <small class="text-info">
                                                            <i class="fas fa-clock"></i> <?php echo date('M d, h:i A', strtotime($booking->sb_not_done_at)); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" onclick="openReassignModal(<?php echo $booking->sb_id; ?>, '<?php echo addslashes($booking->s_name); ?>', '<?php echo addslashes($booking->s_category); ?>')">
                                                        <i class="fas fa-user-plus"></i> Reassign
                                                    </button>
                                                    <a href="admin-view-service-booking.php?id=<?php echo $booking->sb_id; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h4>No Rejected/Not Done Bookings</h4>
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
        function openReassignModal(bookingId, serviceName, category) {
            document.getElementById('booking_id').value = bookingId;
            
            // Fetch available technicians for this specific service
            $.ajax({
                url: 'vendor/inc/get-technicians.php',
                method: 'POST',
                data: { 
                    service_name: serviceName,
                    category: category 
                },
                success: function(response) {
                    $('#tech_select').html(response);
                },
                error: function() {
                    $('#tech_select').html('<option>Error loading technicians</option>');
                }
            });
            
            $('#reassignModal').modal('show');
        }
    </script>
</body>
</html>
