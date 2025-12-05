<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle delete action
if(isset($_POST['delete_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    
    try {
        $delete_query = "DELETE FROM tms_service_booking WHERE sb_id = ?";
        $stmt = $mysqli->prepare($delete_query);
        $stmt->bind_param('i', $booking_id);
        
        if($stmt->execute()) {
            $_SESSION['success_msg'] = "Booking #$booking_id deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete booking.";
        }
    } catch(Exception $e) {
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    }
    
    header("Location: admin-rejected-bookings.php");
    exit();
}

// Handle reassignment
if(isset($_POST['reassign'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_tech_id = intval($_POST['new_tech_id']);
    
    $mysqli->begin_transaction();
    
    try {
        $old_tech_query = "SELECT sb_technician_id FROM tms_service_booking WHERE sb_id = ?";
        $stmt = $mysqli->prepare($old_tech_query);
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $old_tech_id = $result->fetch_object()->sb_technician_id ?? null;
        
        $update_query = "UPDATE tms_service_booking 
                        SET sb_technician_id = ?, 
                            sb_status = 'Approved', 
                            sb_assigned_at = NOW(), 
                            sb_updated_at = NOW(),
                            sb_rejection_reason = NULL
                        WHERE sb_id = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param('ii', $new_tech_id, $booking_id);
        $stmt->execute();
        
        if($old_tech_id) {
            $mysqli->query("UPDATE tms_technician SET t_current_bookings = GREATEST(t_current_bookings - 1, 0) WHERE t_id = $old_tech_id");
        }
        
        $mysqli->query("UPDATE tms_technician SET t_current_bookings = t_current_bookings + 1 WHERE t_id = $new_tech_id");
        
        $mysqli->commit();
        $_SESSION['success_msg'] = "Booking #$booking_id reassigned successfully!";
        
    } catch(Exception $e) {
        $mysqli->rollback();
        $_SESSION['error_msg'] = "Failed to reassign: " . $e->getMessage();
    }
    
    header("Location: admin-rejected-bookings.php");
    exit();
}

// Get rejected bookings
$rejected_query = "SELECT sb.*, 
                   COALESCE(u.u_fname, 'Guest') as u_fname,
                   COALESCE(u.u_lname, 'Customer') as u_lname,
                   COALESCE(u.u_phone, sb.sb_phone) as phone,
                   COALESCE(s.s_name, sb.sb_service_name, sb.sb_custom_service, 'Service') as service_name,
                   t.t_name as tech_name
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                   WHERE sb.sb_status IN ('Rejected', 'Not Done', 'Rejected by Technician', 'Not Completed', 'Cancelled')
                   ORDER BY sb.sb_updated_at DESC";
$rejected_result = $mysqli->query($rejected_query);

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<style>
    /* Modern UI Enhancements */
    body {
        background: #f4f6f9;
    }
    
    .page-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        overflow: hidden;
    }
    
    .stats-badge {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .modern-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .modern-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }
    
    .table-modern {
        margin: 0;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table-modern thead th {
        border: none;
        padding: 18px 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table-modern tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table-modern tbody tr:hover {
        background: linear-gradient(90deg, #f8f9ff 0%, #fff 100%);
        transform: scale(1.01);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }
    
    .table-modern tbody td {
        padding: 20px 15px;
        vertical-align: middle;
        border: none;
    }
    
    .booking-id-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-block;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }
    
    .customer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .customer-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }
    
    .customer-details {
        flex: 1;
    }
    
    .customer-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 1rem;
        margin-bottom: 3px;
    }
    
    .customer-phone {
        color: #718096;
        font-size: 0.85rem;
    }
    
    .service-info {
        padding: 10px 15px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 10px;
        color: white;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 3px 10px rgba(240, 147, 251, 0.3);
    }
    
    .tech-badge {
        padding: 8px 15px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 3px 10px rgba(79, 172, 254, 0.3);
    }
    
    .tech-badge.unassigned {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        color: #8b4513;
    }
    
    .date-time-box {
        background: #f7fafc;
        padding: 10px 15px;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }
    
    .date-time-box .date {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 3px;
    }
    
    .date-time-box .time {
        color: #718096;
        font-size: 0.9rem;
    }
    
    .status-badge {
        padding: 8px 18px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    }
    
    .status-badge.badge-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        border: none;
    }
    
    .status-badge.badge-warning {
        background: linear-gradient(135deg, #f9ca24 0%, #f0932b 100%);
        border: none;
    }
    
    .status-badge.badge-secondary {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        border: none;
    }
    
    .action-btn-group {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    
    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .action-btn.btn-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .action-btn.btn-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .action-btn.btn-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .modern-alert {
        border: none;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: slideInDown 0.5s ease;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
    
    .empty-state-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(67, 233, 123, 0.3);
    }
    
    .empty-state-icon i {
        font-size: 4rem;
        color: white;
    }
    
    .empty-state h3 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .empty-state p {
        color: #718096;
        font-size: 1.1rem;
    }
    
    .modern-modal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }
    
    .modern-modal .modal-header {
        border: none;
        padding: 25px 30px;
    }
    
    .modern-modal .modal-body {
        padding: 30px;
    }
    
    .modern-modal .modal-footer {
        border: none;
        padding: 20px 30px;
        background: #f7fafc;
    }
    
    .modern-input {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 1rem;
        transition: all 0.3s ease;
        height: auto;
        line-height: 1.5;
    }
    
    .modern-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    select.modern-input {
        padding: 14px 18px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }
    
    select.modern-input option {
        padding: 10px;
        font-size: 1rem;
    }
    
    .modern-btn {
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .modern-btn-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }
    
    .modern-btn-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 15px;
        margin-left: 10px;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 5px 10px;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .page-link {
        color: #667eea;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin: 0 3px;
    }
    
    .page-link:hover {
        background: #f7fafc;
        color: #667eea;
    }
    
    .rejection-reason {
        background: #fff5f5;
        border-left: 3px solid #ff6b6b;
        padding: 5px 10px;
        border-radius: 5px;
        margin-top: 5px;
        font-size: 0.85rem;
    }
</style>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rejected Bookings</li>
                </ol>

                <?php if($success_msg): ?>
                    <div class="alert alert-success alert-dismissible fade show modern-alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>

                <?php if($error_msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show modern-alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="card page-header-card mb-4">
                    <div class="card-body py-4 px-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-exclamation-triangle"></i> Rejected & Cancelled Bookings
                                </h4>
                                <p class="text-white-50 mb-0 mt-2">Manage and reassign rejected bookings</p>
                            </div>
                            <div class="col-auto">
                                <div class="stats-badge">
                                    <i class="fas fa-list-alt"></i> <?php echo $rejected_result->num_rows; ?> Bookings
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($rejected_result->num_rows > 0): ?>
                    <!-- Bookings List -->
                    <div class="card modern-card mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0" id="rejectedTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 100px;">ID</th>
                                            <th style="min-width: 200px;">Customer</th>
                                            <th style="min-width: 180px;">Service</th>
                                            <th style="min-width: 150px;">Technician</th>
                                            <th style="min-width: 150px;">Date & Time</th>
                                            <th class="text-center" style="width: 140px;">Status</th>
                                            <th class="text-center" style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = $rejected_result->fetch_object()): 
                                            $status_colors = [
                                                'Rejected' => 'danger',
                                                'Rejected by Technician' => 'danger',
                                                'Not Done' => 'warning',
                                                'Not Completed' => 'warning',
                                                'Cancelled' => 'secondary'
                                            ];
                                            $badge_color = $status_colors[$booking->sb_status] ?? 'danger';
                                            $initials = strtoupper(substr($booking->u_fname, 0, 1) . substr($booking->u_lname, 0, 1));
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="booking-id-badge">#<?php echo $booking->sb_id; ?></span>
                                            </td>
                                            <td>
                                                <div class="customer-info">
                                                    <div class="customer-avatar"><?php echo $initials; ?></div>
                                                    <div class="customer-details">
                                                        <div class="customer-name"><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></div>
                                                        <div class="customer-phone">
                                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking->phone); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="service-info">
                                                    <i class="fas fa-tools"></i> <?php echo htmlspecialchars($booking->service_name); ?>
                                                </div>
                                                <?php if($booking->sb_rejection_reason): ?>
                                                    <div class="rejection-reason" title="<?php echo htmlspecialchars($booking->sb_rejection_reason); ?>">
                                                        <i class="fas fa-info-circle"></i> <?php echo substr(htmlspecialchars($booking->sb_rejection_reason), 0, 40); ?><?php echo strlen($booking->sb_rejection_reason) > 40 ? '...' : ''; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($booking->tech_name): ?>
                                                    <span class="tech-badge">
                                                        <i class="fas fa-user-cog"></i> <?php echo htmlspecialchars($booking->tech_name); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="tech-badge unassigned">
                                                        <i class="fas fa-user-slash"></i> Not Assigned
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="date-time-box">
                                                    <div class="date">
                                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?>
                                                    </div>
                                                    <div class="time">
                                                        <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge badge-<?php echo $badge_color; ?>"><?php echo $booking->sb_status; ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-btn-group">
                                                    <a href="admin-view-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" 
                                                       class="action-btn btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="action-btn btn-success" 
                                                            onclick="openReassignModal(<?php echo $booking->sb_id; ?>, '<?php echo addslashes($booking->service_name); ?>')"
                                                            title="Reassign">
                                                        <i class="fas fa-user-plus"></i>
                                                    </button>
                                                    <button class="action-btn btn-danger" 
                                                            onclick="confirmDelete(<?php echo $booking->sb_id; ?>)"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="card modern-card">
                        <div class="card-body empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>All Clear!</h3>
                            <p class="mb-4">No rejected or cancelled bookings. Everything is running smoothly.</p>
                            <a href="admin-manage-service-booking.php" class="btn modern-btn modern-btn-success">
                                <i class="fas fa-list"></i> View All Bookings
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <!-- Reassign Modal -->
    <div class="modal fade modern-modal" id="reassignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Reassign Booking</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="booking_id">
                        
                        <div class="alert alert-info modern-alert" style="border-left: 4px solid #17a2b8;">
                            <i class="fas fa-tools"></i> <strong>Service:</strong> <span id="service_display"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold" style="color: #2d3748;">
                                <i class="fas fa-user-cog"></i> Select Available Technician:
                            </label>
                            <select name="new_tech_id" id="tech_select" class="form-control modern-input" required>
                                <option value="">Loading...</option>
                            </select>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle"></i> Only showing technicians with available capacity
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modern-btn" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" name="reassign" class="btn modern-btn modern-btn-success">
                            <i class="fas fa-check"></i> Confirm Reassignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade modern-modal" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body text-center">
                        <input type="hidden" name="booking_id" id="delete_booking_id">
                        <div class="empty-state-icon" style="width: 100px; height: 100px; background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%); margin-bottom: 20px;">
                            <i class="fas fa-trash-alt" style="font-size: 3rem;"></i>
                        </div>
                        <h5 style="color: #2d3748;">Are you sure you want to delete this booking?</h5>
                        <p class="text-muted">This action cannot be undone. Booking <strong>#<span id="delete_booking_number"></span></strong> will be permanently removed.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modern-btn" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" name="delete_booking" class="btn modern-btn modern-btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#rejectedTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 25,
                "language": {
                    "search": "Search bookings:",
                    "lengthMenu": "Show _MENU_ bookings per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ bookings",
                    "infoEmpty": "No bookings found",
                    "infoFiltered": "(filtered from _MAX_ total bookings)"
                }
            });
        });

        // Reassign Modal
        function openReassignModal(bookingId, serviceName) {
            $('#booking_id').val(bookingId);
            $('#service_display').text(serviceName);
            $('#tech_select').html('<option value="">Loading technicians...</option>');
            
            $.ajax({
                url: 'api-get-available-technicians.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success && response.technicians && response.technicians.length > 0) {
                        let options = '<option value="">-- Select Technician --</option>';
                        
                        let available = response.technicians.filter(t => (t.available_slots || 0) > 0);
                        let busy = response.technicians.filter(t => (t.available_slots || 0) <= 0);
                        
                        if(available.length > 0) {
                            options += '<optgroup label="✅ Available (' + available.length + ')">';
                            available.forEach(function(tech) {
                                options += '<option value="' + tech.t_id + '">' + 
                                          tech.t_name + ' (' + tech.available_slots + ' slots free)</option>';
                            });
                            options += '</optgroup>';
                        }
                        
                        if(busy.length > 0) {
                            options += '<optgroup label="🔴 At Capacity (' + busy.length + ')">';
                            busy.forEach(function(tech) {
                                options += '<option value="' + tech.t_id + '" disabled>' + 
                                          tech.t_name + ' (No slots)</option>';
                            });
                            options += '</optgroup>';
                        }
                        
                        $('#tech_select').html(options);
                    } else {
                        $('#tech_select').html('<option value="">❌ No available technicians</option>');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#tech_select').html('<option value="">Error loading technicians</option>');
                }
            });
            
            $('#reassignModal').modal('show');
        }

        // Delete Confirmation
        function confirmDelete(bookingId) {
            $('#delete_booking_id').val(bookingId);
            $('#delete_booking_number').text(bookingId);
            $('#deleteModal').modal('show');
        }

        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>
