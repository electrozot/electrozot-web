<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Add admin_price column if it doesn't exist
$mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS s_admin_price DECIMAL(10,2) DEFAULT NULL");

// Handle single price update via AJAX
if(isset($_POST['update_single_price'])) {
    header('Content-Type: application/json');
    $service_id = intval($_POST['service_id']);
    $price = ($_POST['price'] === '' || $_POST['price'] === null) ? null : floatval($_POST['price']);
    
    $query = "UPDATE tms_service SET s_admin_price = ? WHERE s_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('di', $price, $service_id);
    
    if($stmt->execute()) {
        // If admin sets a price, update all existing bookings with this service
        if($price !== null) {
            $update_bookings = "UPDATE tms_service_booking 
                               SET sb_total_price = ? 
                               WHERE sb_service_id = ? 
                               AND sb_status NOT IN ('Completed', 'Cancelled')";
            $stmt_booking = $mysqli->prepare($update_bookings);
            $stmt_booking->bind_param('di', $price, $service_id);
            $stmt_booking->execute();
        }
        echo json_encode(['success' => true, 'message' => 'Price updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update price']);
    }
    exit();
}

// Handle bulk price update
if(isset($_POST['update_prices'])) {
    $success_count = 0;
    $error_count = 0;
    
    foreach($_POST['service_price'] as $service_id => $price) {
        $service_id = intval($service_id);
        $price = ($price === '' || $price === null) ? null : floatval($price);
        
        $query = "UPDATE tms_service SET s_admin_price = ? WHERE s_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('di', $price, $service_id);
        
        if($stmt->execute()) {
            $success_count++;
            
            // If admin sets a price, update all existing bookings with this service
            if($price !== null) {
                $update_bookings = "UPDATE tms_service_booking 
                                   SET sb_total_price = ? 
                                   WHERE sb_service_id = ? 
                                   AND sb_status NOT IN ('Completed', 'Cancelled')";
                $stmt_booking = $mysqli->prepare($update_bookings);
                $stmt_booking->bind_param('di', $price, $service_id);
                $stmt_booking->execute();
            }
        } else {
            $error_count++;
        }
    }
    
    if($success_count > 0) {
        $succ = "Successfully updated prices for $success_count service(s)";
    }
    if($error_count > 0) {
        $err = "Failed to update $error_count service(s)";
    }
}

// Get all services
$query = "SELECT * FROM tms_service ORDER BY s_category, s_name";
$result = $mysqli->query($query);
$services = [];
while($row = $result->fetch_object()) {
    $services[$row->s_category][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Services</a>
                    </li>
                    <li class="breadcrumb-item active">Service Prices</li>
                </ol>

                <?php if(isset($succ)) {?>
                <script>
                setTimeout(function() {
                    swal("Success!", "<?php echo $succ;?>!", "success");
                }, 100);
                </script>
                <?php } ?>
                
                <?php if(isset($err)) {?>
                <script>
                setTimeout(function() {
                    swal("Failed!", "<?php echo $err;?>!", "error");
                }, 100);
                </script>
                <?php } ?>

                <!-- Statistics -->
                <div class="row mb-4">
                    <?php
                    $total_services = $mysqli->query("SELECT COUNT(*) as count FROM tms_service")->fetch_object()->count;
                    $priced_services = $mysqli->query("SELECT COUNT(*) as count FROM tms_service WHERE s_admin_price IS NOT NULL")->fetch_object()->count;
                    $unpriced_services = $total_services - $priced_services;
                    ?>
                    <div class="col-md-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Services</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_services; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Priced by Admin</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $priced_services; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Technician Pricing</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $unpriced_services; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Management Form -->
                <form method="POST">
                    <?php foreach($services as $category => $category_services): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-wrench"></i> <?php echo $category; ?>
                                <span class="badge badge-light ml-2"><?php echo count($category_services); ?> services</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="35%">Service Name</th>
                                            <th width="15%">Current Price</th>
                                            <th width="20%">Admin Price (₹)</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Price Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($category_services as $service): ?>
                                        <tr>
                                            <td><?php echo $service->s_id; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($service->s_name); ?></strong>
                                                <?php if($service->s_subcategory): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($service->s_subcategory); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-muted">₹<?php echo number_format($service->s_price, 2); ?></span>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">₹</span>
                                                    </div>
                                                    <input type="number" 
                                                           name="service_price[<?php echo $service->s_id; ?>]" 
                                                           class="form-control price-input" 
                                                           data-service-id="<?php echo $service->s_id; ?>"
                                                           step="0.01" 
                                                           min="0"
                                                           value="<?php echo $service->s_admin_price !== null ? $service->s_admin_price : ''; ?>"
                                                           placeholder="Leave empty for tech pricing">
                                                    <div class="input-group-append">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success update-single-btn" 
                                                                data-service-id="<?php echo $service->s_id; ?>"
                                                                title="Update this price only">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="price-status text-muted" id="status-<?php echo $service->s_id; ?>"></small>
                                            </td>
                                            <td>
                                                <?php if($service->s_status == 'Active'): ?>
                                                <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($service->s_admin_price !== null): ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Admin Set
                                                </span>
                                                <?php else: ?>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-user-cog"></i> Tech Pricing
                                                </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" name="update_prices" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Update All Prices
                            </button>
                            <a href="admin-manage-service.php" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>

            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    <script src="vendor/js/swal.js"></script>
    
    <style>
        .update-single-btn {
            border-radius: 0 4px 4px 0;
            padding: 6px 12px;
        }
        .price-status {
            display: block;
            margin-top: 3px;
            font-size: 0.75rem;
        }
        .price-status.success {
            color: #28a745;
        }
        .price-status.error {
            color: #dc3545;
        }
    </style>
    
    <script>
    $(document).ready(function() {
        // Handle single price update
        $('.update-single-btn').on('click', function() {
            var btn = $(this);
            var serviceId = btn.data('service-id');
            var priceInput = $('.price-input[data-service-id="' + serviceId + '"]');
            var price = priceInput.val();
            var statusDiv = $('#status-' + serviceId);
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i>');
            statusDiv.removeClass('success error').text('Updating...');
            
            $.ajax({
                url: 'admin-service-prices.php',
                method: 'POST',
                data: {
                    update_single_price: true,
                    service_id: serviceId,
                    price: price
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        statusDiv.addClass('success').text('✓ Updated successfully');
                        btn.html('<i class="fas fa-check"></i>');
                        
                        // Clear status after 3 seconds
                        setTimeout(function() {
                            statusDiv.text('');
                        }, 3000);
                    } else {
                        statusDiv.addClass('error').text('✗ ' + response.message);
                        btn.html('<i class="fas fa-check"></i>');
                    }
                    btn.prop('disabled', false);
                },
                error: function() {
                    statusDiv.addClass('error').text('✗ Update failed');
                    btn.html('<i class="fas fa-check"></i>');
                    btn.prop('disabled', false);
                }
            });
        });
        
        // Allow Enter key to trigger single update
        $('.price-input').on('keypress', function(e) {
            if(e.which === 13) {
                e.preventDefault();
                var serviceId = $(this).data('service-id');
                $('.update-single-btn[data-service-id="' + serviceId + '"]').click();
            }
        });
    });
    </script>
</body>
</html>
