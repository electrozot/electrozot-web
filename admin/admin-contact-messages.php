<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Create table if not exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_contact_messages (
    cm_id INT AUTO_INCREMENT PRIMARY KEY,
    cm_name VARCHAR(200) NOT NULL,
    cm_email VARCHAR(200) NOT NULL,
    cm_phone VARCHAR(20) NOT NULL,
    cm_message TEXT NOT NULL,
    cm_status VARCHAR(20) DEFAULT 'Unread',
    cm_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (cm_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Mark as read
if(isset($_GET['mark_read'])) {
    $cm_id = intval($_GET['mark_read']);
    $mysqli->query("UPDATE tms_contact_messages SET cm_status = 'Read' WHERE cm_id = $cm_id");
    header("Location: admin-contact-messages.php");
    exit;
}

// Delete
if(isset($_GET['delete'])) {
    $cm_id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM tms_contact_messages WHERE cm_id = $cm_id");
    header("Location: admin-contact-messages.php");
    exit;
}

$total = $mysqli->query("SELECT COUNT(*) as count FROM tms_contact_messages")->fetch_object()->count;
$unread = $mysqli->query("SELECT COUNT(*) as count FROM tms_contact_messages WHERE cm_status='Unread'")->fetch_object()->count;
?>
<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
<?php include("vendor/inc/nav.php"); ?>
<div id="wrapper">
<?php include('vendor/inc/sidebar.php'); ?>
<div id="content-wrapper">
<div class="container-fluid">
    
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="admin-manage-feedback.php">Feedbacks</a></li>
    <li class="breadcrumb-item active">Contact Messages</li>
</ol>

<div class="row mb-3">
    <div class="col-md-4">
        <div class="card border-left-primary shadow">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Messages</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo $total; ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-left-warning shadow">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unread</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo $unread; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-envelope"></i> Contact Messages
        <a href="admin-manage-feedback.php" class="btn btn-sm btn-secondary float-right">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $mysqli->query("SELECT * FROM tms_contact_messages ORDER BY cm_created_at DESC");
                $cnt = 1;
                while($row = $result->fetch_object()) {
                ?>
                <tr style="<?php echo $row->cm_status == 'Unread' ? 'background:#fff3cd;' : ''; ?>">
                    <td><?php echo $cnt++; ?></td>
                    <td><?php echo htmlspecialchars($row->cm_name); ?></td>
                    <td><a href="mailto:<?php echo $row->cm_email; ?>"><?php echo $row->cm_email; ?></a></td>
                    <td><?php echo $row->cm_phone; ?></td>
                    <td><?php echo substr($row->cm_message, 0, 50); ?>...</td>
                    <td><?php echo date('M d, Y', strtotime($row->cm_created_at)); ?></td>
                    <td><span class="badge badge-<?php echo $row->cm_status == 'Unread' ? 'warning' : 'success'; ?>"><?php echo $row->cm_status; ?></span></td>
                    <td>
                        <?php if($row->cm_status == 'Unread'): ?>
                        <a href="?mark_read=<?php echo $row->cm_id; ?>" class="btn btn-sm btn-success" title="Mark Read">
                            <i class="fas fa-check"></i>
                        </a>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view<?php echo $row->cm_id; ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="?delete=<?php echo $row->cm_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                
                <div class="modal fade" id="view<?php echo $row->cm_id; ?>">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Message from <?php echo htmlspecialchars($row->cm_name); ?></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($row->cm_name); ?></p>
                                <p><strong>Email:</strong> <?php echo $row->cm_email; ?></p>
                                <p><strong>Phone:</strong> <?php echo $row->cm_phone; ?></p>
                                <p><strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($row->cm_created_at)); ?></p>
                                <hr>
                                <p><strong>Message:</strong></p>
                                <div style="background:#f8f9fa;padding:15px;border-radius:5px;">
                                    <?php echo nl2br(htmlspecialchars($row->cm_message)); ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="mailto:<?php echo $row->cm_email; ?>" class="btn btn-primary">Reply</a>
                                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?php include('vendor/inc/footer.php'); ?>
</div>
</div>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.js"></script>
<script>$('#dataTable').DataTable();</script>
</body>
</html>
