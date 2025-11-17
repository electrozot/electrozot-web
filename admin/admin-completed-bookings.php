<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Ensure required columns exist
try {
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_image VARCHAR(200) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_image VARCHAR(200) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completion_date DATETIME DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_rejection_reason TEXT DEFAULT ''");
} catch(Exception $e) {
    // Columns might already exist
}

// Get completed bookings with images
$completed_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name, s.s_price, t.t_name
                    FROM tms_service_booking sb
                    LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                    LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                    LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                    WHERE sb.sb_status = 'Completed'
                    ORDER BY sb.sb_booking_date DESC";
$completed_result = $mysqli->query($completed_query);
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
                    <li class="breadcrumb-item active">Completed Bookings with Images</li>
                </ol>

                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-check-circle"></i> Completed Bookings - Bill & Service Images
                    </div>
                    <div class="card-body">
                        <?php if($completed_result && $completed_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Original Price</th>
                                            <th>Bill Amount</th>
                                            <th>Technician</th>
                                            <th>Date</th>
                                            <th>Bill Image</th>
                                            <th>Service Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = $completed_result->fetch_object()): ?>
                                            <tr>
                                                <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                                                <td>
                                                    <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?><br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking->u_phone); ?>
                                                    </small>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking->s_name); ?></td>
                                                <td>
                                                    <strong class="text-muted">
                                                        ₹<?php echo number_format($booking->s_price, 2); ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <strong class="text-success" style="font-size:1.1rem;">
                                                        ₹<?php echo isset($booking->sb_bill_amount) ? number_format($booking->sb_bill_amount, 2) : '0.00'; ?>
                                                    </strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking->t_name); ?></td>
                                                <td>
                                                    <?php 
                                                    if(!empty($booking->sb_completion_date)) {
                                                        echo date('M d, Y', strtotime($booking->sb_completion_date));
                                                        echo '<br><small>' . date('h:i A', strtotime($booking->sb_completion_date)) . '</small>';
                                                    } else {
                                                        echo date('M d, Y', strtotime($booking->sb_booking_date));
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                    $bill_img = !empty($booking->sb_bill_attachment) ? $booking->sb_bill_attachment : $booking->sb_bill_image;
                                                    if(!empty($bill_img)): 
                                                    ?>
                                                        <img src="../<?php echo htmlspecialchars($bill_img); ?>" 
                                                             class="img-thumbnail" 
                                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                             onclick="viewImage('../<?php echo htmlspecialchars($bill_img); ?>', 'Bill Image - Booking #<?php echo $booking->sb_id; ?>')">
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                    $service_img = !empty($booking->sb_completion_image) ? $booking->sb_completion_image : $booking->sb_service_image;
                                                    if(!empty($service_img)): 
                                                    ?>
                                                        <img src="../<?php echo htmlspecialchars($service_img); ?>" 
                                                             class="img-thumbnail" 
                                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                             onclick="viewImage('../<?php echo htmlspecialchars($service_img); ?>', 'Service Image - Booking #<?php echo $booking->sb_id; ?>')">
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="admin-view-service-booking.php?sb_id=<?php echo $booking->sb_id; ?>" class="btn btn-info btn-sm mb-1">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h4>No Completed Bookings</h4>
                                <p class="text-muted">No bookings have been completed yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <!-- Single Image Modal -->
    <div class="modal fade" id="singleImageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="imageModalTitle">
                        <i class="fas fa-image"></i> Image Preview
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" style="background: #f8f9fa;">
                    <img id="singleImage" src="" class="img-fluid" style="max-height: 70vh; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                </div>
                <div class="modal-footer">
                    <a id="downloadLink" href="" download class="btn btn-success">
                        <i class="fas fa-download"></i> Download Image
                    </a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Both Images Modal -->
    <div class="modal fade" id="bothImagesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-images"></i> Bill & Service Images - Booking #<span id="bookingIdDisplay"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: #f8f9fa;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-invoice"></i> Bill Image
                                    </h6>
                                </div>
                                <div class="card-body text-center p-2">
                                    <img id="billImageBoth" src="" class="img-fluid" style="max-height: 50vh; border-radius: 5px;">
                                </div>
                                <div class="card-footer text-center">
                                    <a id="downloadBill" href="" download class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i> Download Bill
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-camera"></i> Service Image
                                    </h6>
                                </div>
                                <div class="card-body text-center p-2">
                                    <img id="serviceImageBoth" src="" class="img-fluid" style="max-height: 50vh; border-radius: 5px;">
                                </div>
                                <div class="card-footer text-center">
                                    <a id="downloadService" href="" download class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i> Download Service
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 25
            });
        });

        function viewImage(imagePath, title) {
            document.getElementById('singleImage').src = imagePath;
            document.getElementById('imageModalTitle').innerHTML = '<i class="fas fa-image"></i> ' + title;
            document.getElementById('downloadLink').href = imagePath;
            $('#singleImageModal').modal('show');
        }

        function viewBothImages(billImage, serviceImage, bookingId) {
            const billPath = '../vendor/img/' + billImage;
            const servicePath = '../vendor/img/' + serviceImage;
            
            document.getElementById('billImageBoth').src = billPath;
            document.getElementById('serviceImageBoth').src = servicePath;
            document.getElementById('downloadBill').href = billPath;
            document.getElementById('downloadService').href = servicePath;
            document.getElementById('bookingIdDisplay').textContent = bookingId;
            
            $('#bothImagesModal').modal('show');
        }
    </script>
</body>
</html>
