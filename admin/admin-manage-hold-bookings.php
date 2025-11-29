<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$page_title = "Manage Hold Bookings";

// Ensure hold system columns exist
try {
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_on_hold TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_reason TEXT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_start_date TIMESTAMP NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_hold_end_date TIMESTAMP NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_is_high_priority TINYINT(1) DEFAULT 0");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_priority_reason VARCHAR(255) NULL");
    
    // Create hold requests table
    $create_hold_table = "CREATE TABLE IF NOT EXISTS tms_booking_hold_requests (
        bhr_id INT AUTO_INCREMENT PRIMARY KEY,
        bhr_booking_id INT NOT NULL,
        bhr_technician_id INT NOT NULL,
        bhr_reason TEXT NOT NULL,
        bhr_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        bhr_requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        bhr_responded_at TIMESTAMP NULL,
        bhr_customer_response TEXT NULL,
        INDEX(bhr_booking_id),
        INDEX(bhr_technician_id),
        INDEX(bhr_status)
    )";
    $mysqli->query($create_hold_table);
} catch(Exception $e) {}

// Handle admin actions
if(isset($_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];
    
    if($action == 'hold') {
        // Admin puts booking on hold
        $reason = $_POST['reason'] ?? 'Admin hold - requires attention';
        $hold_start = date('Y-m-d H:i:s');
        $hold_end = date('Y-m-d H:i:s', strtotime('+4 days'));
        
        $update = "UPDATE tms_service_booking 
                   SET sb_is_on_hold = 1,
                       sb_hold_reason = ?,
                       sb_hold_start_date = ?,
                       sb_hold_end_date = ?,
                       sb_status = 'On Hold'
                   WHERE sb_id = ?";
        $stmt = $mysqli->prepare($update);
        $stmt->bind_param('sssi', $reason, $hold_start, $hold_end, $booking_id);
        $stmt->execute();
        
        $_SESSION['success'] = "Booking #$booking_id put on hold successfully";
        
    } elseif($action == 'unhold') {
        // Admin removes hold
        $update = "UPDATE tms_service_booking 
                   SET sb_is_on_hold = 0,
                       sb_hold_reason = NULL,
                       sb_hold_start_date = NULL,
                       sb_hold_end_date = NULL,
                       sb_is_high_priority = 1,
                       sb_priority_reason = 'Admin unholded - high priority',
                       sb_status = 'In Progress'
                   WHERE sb_id = ?";
        $stmt = $mysqli->prepare($update);
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        
        $_SESSION['success'] = "Booking #$booking_id unholded and marked as high priority";
        
    } elseif($action == 'cancel') {
        // Admin cancels hold booking
        $cancel_reason = $_POST['cancel_reason'] ?? 'Cancelled by admin';
        
        $update = "UPDATE tms_service_booking 
                   SET sb_is_on_hold = 0,
                       sb_hold_reason = NULL,
                       sb_hold_start_date = NULL,
                       sb_hold_end_date = NULL,
                       sb_status = 'Cancelled'
                   WHERE sb_id = ?";
        $stmt = $mysqli->prepare($update);
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        
        $_SESSION['success'] = "Booking #$booking_id cancelled successfully";
    }
    
    header("Location: admin-manage-hold-bookings.php");
    exit;
}

// Get all bookings with hold status
$query = "SELECT sb.sb_id, sb.sb_status, sb.sb_is_on_hold, sb.sb_hold_reason, 
          sb.sb_hold_start_date, sb.sb_hold_end_date, sb.sb_is_high_priority,
          sb.sb_service_id, sb.sb_user_id, sb.sb_technician_id,
          s.s_name, u.u_fname, u.u_lname, u.u_phone, t.t_name, t.t_phone as t_phone_num
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          ORDER BY sb.sb_is_on_hold DESC, sb.sb_created_at DESC
          LIMIT 100";
$result = $mysqli->query($query);

if(!$result) {
    die("Query error: " . $mysqli->error);
}

// Get pending hold requests
$hold_requests_query = "SELECT bhr.*, sb.sb_id, t.t_name, u.u_fname, u.u_lname, s.s_name
                        FROM tms_booking_hold_requests bhr
                        LEFT JOIN tms_service_booking sb ON bhr.bhr_booking_id = sb.sb_id
                        LEFT JOIN tms_technician t ON bhr.bhr_technician_id = t.t_id
                        LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                        LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                        WHERE bhr.bhr_status = 'Pending'
                        ORDER BY bhr.bhr_requested_at DESC";
$hold_requests_result = $mysqli->query($hold_requests_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Electrozot</title>
    <?php include('vendor/inc/head.php'); ?>
    <style>
        .hold-badge {
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        
        .priority-badge {
            background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            margin: 2px;
        }
        
        .btn-hold {
            background: #ffa502;
            color: white;
        }
        
        .btn-unhold {
            background: #00c853;
            color: white;
        }
        
        .btn-cancel {
            background: #ff4757;
            color: white;
        }
        
        .pending-requests {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include('vendor/inc/sidebar.php'); ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-pause-circle"></i> <?php echo $page_title; ?></h1>
                </div>
                
                <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Pending Hold Requests -->
                <?php if($hold_requests_result->num_rows > 0): ?>
                <div class="pending-requests">
                    <h5><i class="fas fa-clock"></i> Pending Hold Requests (<?php echo $hold_requests_result->num_rows; ?>)</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Service</th>
                                    <th>Customer</th>
                                    <th>Technician</th>
                                    <th>Reason</th>
                                    <th>Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($req = $hold_requests_result->fetch_object()): ?>
                                <tr>
                                    <td><strong>#<?php echo $req->bhr_booking_id; ?></strong></td>
                                    <td><?php echo htmlspecialchars($req->s_name); ?></td>
                                    <td><?php echo htmlspecialchars($req->u_fname . ' ' . $req->u_lname); ?></td>
                                    <td><?php echo htmlspecialchars($req->t_name); ?></td>
                                    <td><?php echo htmlspecialchars(substr($req->bhr_reason, 0, 50)); ?>...</td>
                                    <td><?php echo date('M d, h:i A', strtotime($req->bhr_requested_at)); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- All Bookings -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> All Bookings (On Hold First)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Service</th>
                                        <th>Customer</th>
                                        <th>Technician</th>
                                        <th>Status</th>
                                        <th>Hold Info</th>
                                        <th>Admin Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <p class="text-muted">No bookings found</p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    
                                    <?php while($booking = $result->fetch_object()): ?>
                                    <tr style="<?php echo $booking->sb_is_on_hold == 1 ? 'background: #fff3cd;' : ''; ?>">
                                        <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                        <td><?php echo htmlspecialchars($booking->s_name); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?><br>
                                            <small><?php echo htmlspecialchars($booking->u_phone); ?></small>
                                        </td>
                                        <td>
                                            <?php if(!empty($booking->t_name)): ?>
                                                <?php echo htmlspecialchars($booking->t_name); ?><br>
                                                <small><?php echo htmlspecialchars($booking->t_phone_num); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if($booking->sb_status == 'Completed') echo 'success';
                                                elseif($booking->sb_status == 'In Progress') echo 'primary';
                                                elseif($booking->sb_status == 'Pending') echo 'warning';
                                                else echo 'secondary';
                                            ?>"><?php echo $booking->sb_status; ?></span>
                                            
                                            <?php if($booking->sb_is_on_hold == 1): ?>
                                                <br><span class="hold-badge"><i class="fas fa-pause-circle"></i> ON HOLD</span>
                                            <?php endif; ?>
                                            
                                            <?php if($booking->sb_is_high_priority == 1): ?>
                                                <br><span class="priority-badge"><i class="fas fa-fire"></i> PRIORITY</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($booking->sb_is_on_hold == 1): ?>
                                                <small>
                                                    <strong>Reason:</strong> <?php echo htmlspecialchars(substr($booking->sb_hold_reason, 0, 40)); ?>...<br>
                                                    <strong>Until:</strong> <?php echo date('M d, Y', strtotime($booking->sb_hold_end_date)); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($booking->sb_status != 'Completed' && $booking->sb_status != 'Cancelled'): ?>
                                                <?php if($booking->sb_is_on_hold == 1): ?>
                                                    <!-- Unhold Button -->
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking->sb_id; ?>">
                                                        <input type="hidden" name="action" value="unhold">
                                                        <button type="submit" class="action-btn btn-unhold" onclick="return confirm('Unhold this booking? It will be marked as HIGH PRIORITY.')">
                                                            <i class="fas fa-play-circle"></i> Unhold
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Cancel Button -->
                                                    <button class="action-btn btn-cancel" onclick="cancelHoldBooking(<?php echo $booking->sb_id; ?>)">
                                                        <i class="fas fa-ban"></i> Cancel
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Hold Button -->
                                                    <button class="action-btn btn-hold" onclick="holdBooking(<?php echo $booking->sb_id; ?>)">
                                                        <i class="fas fa-pause-circle"></i> Put on Hold
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No actions</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    function holdBooking(bookingId) {
        const reason = prompt('Enter reason for holding this booking:', 'Admin hold - requires attention');
        if(reason) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="booking_id" value="${bookingId}">
                <input type="hidden" name="action" value="hold">
                <input type="hidden" name="reason" value="${reason}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function cancelHoldBooking(bookingId) {
        const reason = prompt('Enter reason for cancelling this booking:', 'Cancelled by admin');
        if(reason && confirm('Are you sure you want to CANCEL this booking? This cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="booking_id" value="${bookingId}">
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="cancel_reason" value="${reason}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>
