<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Completed Bookings";

// Get completed bookings
$query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_technician_id = ? AND sb.sb_status = 'Completed'
          ORDER BY sb.sb_completed_date DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <?php if(isset($_SESSION['success_msg'])): ?>
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <div class="page-header" style="background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%); color: white; border-left: none;">
            <h2 style="color: white;">
                <i class="fas fa-check-circle"></i>
                Completed Bookings
            </h2>
            <p style="color: rgba(255,255,255,0.95);">Your successfully completed services</p>
        </div>

        <?php if($result->num_rows > 0): ?>
            <div class="card-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Completed Date</th>
                                <th>Final Price</th>
                                <th>Images</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $cnt = 1;
                            while($booking = $result->fetch_object()): 
                            ?>
                            <tr>
                                <td><?php echo $cnt++; ?></td>
                                <td><strong><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking->s_name); ?></td>
                                <td>
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo $booking->sb_completed_date ? date('M d, Y', strtotime($booking->sb_completed_date)) : 'N/A'; ?>
                                </td>
                                <td><strong style="color: #38ef7d;">$<?php echo number_format($booking->sb_final_price ? $booking->sb_final_price : $booking->sb_total_price, 2); ?></strong></td>
                                <td>
                                    <?php if($booking->sb_completion_img): ?>
                                        <a href="../vendor/img/completions/<?php echo $booking->sb_completion_img; ?>" target="_blank" class="btn btn-sm btn-primary-custom">
                                            <i class="fas fa-image"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-status badge-completed">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card-custom text-center" style="padding: 80px 30px;">
                <i class="fas fa-clipboard-check" style="font-size: 5rem; color: #e2e8f0; margin-bottom: 20px;"></i>
                <h3 style="color: #2d3748; font-weight: 700;">No Completed Bookings Yet</h3>
                <p style="color: #6c757d; font-size: 1.1rem;">Complete your first service to see it here.</p>
                <a href="new-bookings.php" class="btn btn-primary-custom mt-3">
                    <i class="fas fa-bell"></i> View New Bookings
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
