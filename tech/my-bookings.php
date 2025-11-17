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
            <div class="row">
                <?php 
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
                <div class="col-md-6 mb-3">
                    <div class="order-card">
                        <div class="order-card-body">
                            <div class="row">
                                <!-- Left Side -->
                                <div class="col-6">
                                    <div class="order-field">
                                        <label>Order ID</label>
                                        <p><strong>#<?php echo $row->sb_id; ?></strong></p>
                                    </div>
                                    <div class="order-field">
                                        <label>Customer Name</label>
                                        <p><?php echo htmlspecialchars($row->u_fname . ' ' . $row->u_lname); ?></p>
                                    </div>
                                    <div class="order-field">
                                        <label>Address</label>
                                        <p><?php echo htmlspecialchars($row->sb_address); ?></p>
                                    </div>
                                </div>
                                <!-- Right Side -->
                                <div class="col-6">
                                    <div class="order-field">
                                        <label>Status</label>
                                        <p><span class="badge-status <?php echo $status_class; ?>"><?php echo $row->sb_status; ?></span></p>
                                    </div>
                                    <div class="order-field">
                                        <label>Pincode</label>
                                        <p><?php echo htmlspecialchars($row->u_pincode ?? 'N/A'); ?></p>
                                    </div>
                                    <div class="order-field">
                                        <label>Service</label>
                                        <p><?php echo htmlspecialchars($row->s_name); ?></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Action Buttons -->
                            <div class="order-actions">
                                <a href="tel:<?php echo $row->u_phone; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-phone"></i> Call
                                </a>
                                <a href="booking-details.php?id=<?php echo $row->sb_id; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card-custom text-center" style="padding: 60px 30px;">
                <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #e2e8f0; margin-bottom: 20px;"></i>
                <h4 style="color: #6c757d;">No Bookings Assigned Yet</h4>
                <p style="color: #a0aec0;">You don't have any bookings assigned to you at the moment.</p>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .order-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .order-card-body {
            padding: 20px;
        }

        .order-field {
            margin-bottom: 15px;
        }

        .order-field label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        .order-field p {
            font-size: 0.9rem;
            color: #2d3748;
            margin: 0;
            line-height: 1.4;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .order-actions .btn {
            flex: 1;
            font-size: 0.85rem;
            padding: 8px 12px;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 4px 10px;
        }
    </style>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
