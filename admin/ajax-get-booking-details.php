<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

if(isset($_GET['sb_id'])) {
    $sb_id = $_GET['sb_id'];
    
    $query = "SELECT 
                sb.*,
                u.u_fname, u.u_lname, u.u_phone, u.u_email, u.u_addr,
                s.s_name, s.s_category, s.s_price, 
                COALESCE(s.s_description, '') as s_description, 
                COALESCE(s.s_duration, '1-2 hours') as s_duration,
                t.t_name, t.t_id_no, 
                COALESCE(t.t_phone, '') as t_phone, 
                COALESCE(t.t_email, '') as t_email, 
                COALESCE(t.t_specialization, '') as t_specialization
              FROM tms_service_booking sb
              LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
              LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
              LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
              WHERE sb.sb_id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $sb_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_object();
    
    if($booking):
?>
<div class="row">
    <!-- Booking Information -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-calendar"></i> Booking Information
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Booking ID:</th>
                        <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch($booking->sb_status) {
                                case 'Pending': $status_class = 'warning'; break;
                                case 'Confirmed': $status_class = 'info'; break;
                                case 'In Progress': $status_class = 'primary'; break;
                                case 'Completed': $status_class = 'success'; break;
                                case 'Cancelled': $status_class = 'danger'; break;
                                default: $status_class = 'secondary';
                            }
                            ?>
                            <span class="badge badge-<?php echo $status_class; ?>"><?php echo $booking->sb_status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Booking Date:</th>
                        <td><?php echo date('l, F d, Y', strtotime($booking->sb_booking_date)); ?></td>
                    </tr>
                    <tr>
                        <th>Booking Time:</th>
                        <td><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></td>
                    </tr>
                    <?php if(isset($booking->sb_created_at)): ?>
                    <tr>
                        <th>Created At:</th>
                        <td><?php echo date('M d, Y h:i A', strtotime($booking->sb_created_at)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(isset($booking->sb_updated_at)): ?>
                    <tr>
                        <th>Last Updated:</th>
                        <td><?php echo date('M d, Y h:i A', strtotime($booking->sb_updated_at)); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <i class="fas fa-user"></i> Customer Information
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Name:</th>
                        <td><strong><?php echo $booking->u_fname . ' ' . $booking->u_lname; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><i class="fas fa-phone"></i> <?php echo $booking->u_phone; ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><i class="fas fa-envelope"></i> <?php echo $booking->u_email; ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?php echo $booking->u_addr; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Service Information -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <i class="fas fa-cogs"></i> Service Information
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Service:</th>
                        <td><strong><?php echo $booking->s_name; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td><span class="badge badge-secondary"><?php echo $booking->s_category; ?></span></td>
                    </tr>
                    <tr>
                        <th>Duration:</th>
                        <td><?php echo $booking->s_duration; ?></td>
                    </tr>
                    <tr>
                        <th>Service Price:</th>
                        <td><strong class="text-success">₹<?php echo number_format($booking->s_price, 2); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Total Price:</th>
                        <td><strong class="text-success">₹<?php echo isset($booking->sb_total_price) ? number_format($booking->sb_total_price, 2) : '0.00'; ?></strong></td>
                    </tr>
                </table>
                <?php if(isset($booking->s_description) && !empty($booking->s_description)): ?>
                    <div class="mt-2">
                        <strong>Description:</strong>
                        <p class="text-muted small"><?php echo $booking->s_description; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Technician Information -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-user-cog"></i> Technician Information
            </div>
            <div class="card-body">
                <?php if($booking->t_name): ?>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Name:</th>
                            <td><strong><?php echo $booking->t_name; ?></strong></td>
                        </tr>
                        <tr>
                            <th>ID Number:</th>
                            <td><?php echo $booking->t_id_no; ?></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><i class="fas fa-phone"></i> <?php echo $booking->t_phone; ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><i class="fas fa-envelope"></i> <?php echo $booking->t_email; ?></td>
                        </tr>
                        <tr>
                            <th>Specialization:</th>
                            <td><?php echo $booking->t_specialization; ?></td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> No technician assigned yet
                    </div>
                    <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-success btn-block mt-2">
                        <i class="fas fa-user-plus"></i> Assign Technician
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Booking Address & Contact -->
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-map-marker-alt"></i> Service Location & Contact
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <strong>Address:</strong>
                        <p><?php echo isset($booking->sb_address) ? $booking->sb_address : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-4">
                        <strong>Contact Phone:</strong>
                        <p><i class="fas fa-phone"></i> <?php echo isset($booking->sb_phone) ? $booking->sb_phone : 'N/A'; ?></p>
                    </div>
                </div>
                <?php if(isset($booking->sb_description) && !empty($booking->sb_description)): ?>
                    <hr>
                    <strong>Additional Notes:</strong>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($booking->sb_description)); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-12">
        <div class="btn-group btn-block" role="group">
            <?php if($booking->sb_status == 'Pending'): ?>
                <a href="admin-assign-technician.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Assign Technician
                </a>
            <?php endif; ?>
            <a href="admin-view-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Edit Booking
            </a>
            <a href="admin-delete-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-danger" onclick="return confirm('Delete this booking?')">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
    </div>
</div>

<?php
    else:
        echo '<div class="alert alert-danger">Booking not found</div>';
    endif;
} else {
    echo '<div class="alert alert-danger">Invalid booking ID</div>';
}
?>
