<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "Search Bookings";

$search_phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$bookings = [];

if(!empty($search_phone)) {
    // Search for bookings by customer phone number
    $search_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name 
                     FROM tms_service_booking sb
                     LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                     LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                     WHERE sb.sb_technician_id = ? AND u.u_phone LIKE ?
                     ORDER BY sb.sb_booking_date DESC, sb.sb_booking_time DESC";
    $stmt = $mysqli->prepare($search_query);
    $search_param = "%{$search_phone}%";
    $stmt->bind_param('is', $t_id, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_object()) {
        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <div class="page-header">
            <h2>
                <i class="fas fa-search" style="color: var(--primary);"></i>
                Search Results
            </h2>
            <p>Showing results for: <strong><?php echo htmlspecialchars($search_phone); ?></strong></p>
        </div>

        <?php if(empty($search_phone)): ?>
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle"></i> Please enter a mobile number to search.
            </div>
        <?php elseif(count($bookings) == 0): ?>
            <div class="card-custom text-center" style="padding: 60px;">
                <i class="fas fa-search" style="font-size: 4rem; color: #e2e8f0; margin-bottom: 20px;"></i>
                <h4 style="color: #6c757d; font-weight: 700;">No Bookings Found</h4>
                <p style="color: #a0aec0;">No bookings found for mobile number: <strong><?php echo htmlspecialchars($search_phone); ?></strong></p>
                <a href="dashboard.php" class="btn btn-primary-custom mt-3">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        <?php else: ?>
            <div class="card-custom">
                <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 25px;">
                    <i class="fas fa-list" style="color: var(--primary);"></i>
                    Found <?php echo count($bookings); ?> Booking(s)
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($bookings as $booking): 
                                $status_class = '';
                                if($booking->sb_status == 'Pending') {
                                    $status_class = 'badge-pending';
                                } elseif($booking->sb_status == 'In Progress') {
                                    $status_class = 'badge-status';
                                } elseif($booking->sb_status == 'Completed') {
                                    $status_class = 'badge-completed';
                                } else {
                                    $status_class = 'badge-cancelled';
                                }
                            ?>
                            <tr>
                                <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                <td><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></td>
                                <td>
                                    <i class="fas fa-phone" style="color: var(--primary);"></i>
                                    <?php echo htmlspecialchars($booking->u_phone); ?>
                                </td>
                                <td><?php echo htmlspecialchars($booking->s_name); ?></td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?><br>
                                    <small><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></small>
                                </td>
                                <td>
                                    <span class="badge-status <?php echo $status_class; ?>">
                                        <?php echo $booking->sb_status; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="booking-details.php?id=<?php echo $booking->sb_id; ?>" class="btn btn-sm btn-primary-custom">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
