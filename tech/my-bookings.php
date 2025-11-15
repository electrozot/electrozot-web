<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$page_title = "My Bookings";

// Create cancelled bookings table if not exists
try {
    $create_cancelled_table = "CREATE TABLE IF NOT EXISTS tms_cancelled_bookings (
        cb_id INT AUTO_INCREMENT PRIMARY KEY,
        cb_booking_id INT NOT NULL,
        cb_technician_id INT NOT NULL,
        cb_cancelled_by VARCHAR(50) DEFAULT 'Admin',
        cb_reason VARCHAR(255) DEFAULT 'Technician reassigned by admin',
        cb_cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(cb_booking_id),
        INDEX(cb_technician_id)
    )";
    $mysqli->query($create_cancelled_table);
} catch(Exception $e) {}

// Get filter parameter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query based on filter
$where_clause = "WHERE sb.sb_technician_id = ?";
$filter_title = "All Bookings";

if($filter == 'new') {
    $where_clause .= " AND sb.sb_status = 'Pending' AND DATE(sb.sb_booking_date) >= CURDATE()";
    $filter_title = "New Bookings";
} elseif($filter == 'pending') {
    $where_clause .= " AND sb.sb_status = 'Pending'";
    $filter_title = "Pending Bookings";
} elseif($filter == 'completed') {
    $where_clause .= " AND sb.sb_status = 'Completed'";
    $filter_title = "Completed Bookings";
}

// Get all bookings assigned to this technician (exclude cancelled ones)
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, s.s_name, s.s_price
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_cancelled_bookings cb ON sb.sb_id = cb.cb_booking_id AND cb.cb_technician_id = ?
          $where_clause
          AND cb.cb_id IS NULL
          ORDER BY sb.sb_booking_date DESC, sb.sb_booking_time DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $t_id, $t_id);
$stmt->execute();
$result = $stmt->get_result();
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
                        <i class="fas fa-clipboard-list" style="color: var(--primary);"></i>
                        <?php echo $filter_title; ?>
                    </h2>
                    <p>View and manage all your assigned service bookings</p>
                </div>
                <div>
                    <a href="my-bookings.php" class="btn btn-sm <?php echo $filter == 'all' ? 'btn-primary-custom' : 'btn-outline-secondary'; ?>">
                        <i class="fas fa-list"></i> All
                    </a>
                    <a href="my-bookings.php?filter=new" class="btn btn-sm <?php echo $filter == 'new' ? 'btn-primary-custom' : 'btn-outline-secondary'; ?>">
                        <i class="fas fa-bell"></i> New
                    </a>
                    <a href="my-bookings.php?filter=pending" class="btn btn-sm <?php echo $filter == 'pending' ? 'btn-primary-custom' : 'btn-outline-secondary'; ?>">
                        <i class="fas fa-clock"></i> Pending
                    </a>
                    <a href="my-bookings.php?filter=completed" class="btn btn-sm <?php echo $filter == 'completed' ? 'btn-primary-custom' : 'btn-outline-secondary'; ?>">
                        <i class="fas fa-check-circle"></i> Completed
                    </a>
                </div>
            </div>
        </div>

        <!-- Bookings Section -->
        <?php if($result->num_rows > 0): ?>
            <div class="card-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $cnt = 1;
                            while($row = $result->fetch_object()): 
                                $status_class = '';
                                if($row->sb_status == 'Pending') {
                                    $status_class = 'badge-pending';
                                } elseif($row->sb_status == 'Completed') {
                                    $status_class = 'badge-completed';
                                } else {
                                    $status_class = 'badge-cancelled';
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary); font-size: 1.1rem;">#<?php echo $row->sb_id; ?></strong>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row->u_fname . ' ' . $row->u_lname); ?></strong><br>
                                    <small style="color: #6c757d;"><?php echo htmlspecialchars($row->u_email); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row->s_name); ?></td>
                                <td>
                                    <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($row->sb_booking_date)); ?><br>
                                    <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($row->sb_booking_time)); ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($row->sb_address, 0, 30)) . '...'; ?></td>
                                <td>
                                    <a href="tel:<?php echo $row->u_phone; ?>" class="btn btn-sm btn-success-custom">
                                        <i class="fas fa-phone"></i> <?php echo $row->u_phone; ?>
                                    </a>
                                </td>
                                <td><strong>$<?php echo number_format($row->sb_total_price, 2); ?></strong></td>
                                <td>
                                    <span class="badge-status <?php echo $status_class; ?>">
                                        <?php echo $row->sb_status; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="booking-details.php?id=<?php echo $row->sb_id; ?>" class="btn btn-sm btn-primary-custom">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card-custom text-center" style="padding: 60px 30px;">
                <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #e2e8f0; margin-bottom: 20px;"></i>
                <h4 style="color: #6c757d;">No Bookings Assigned Yet</h4>
                <p style="color: #a0aec0;">You don't have any bookings assigned to you at the moment.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
