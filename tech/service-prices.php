<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Service Prices";

// Get all services with admin prices
$query = "SELECT s_id, s_name, s_category, s_subcategory, s_price, s_admin_price, s_status 
          FROM tms_service 
          WHERE s_status = 'Active'
          ORDER BY s_category, s_name";
$result = $mysqli->query($query);

$services = [];
while($row = $result->fetch_object()) {
    $services[$row->s_category][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>
<body>
    <?php include('includes/nav.php'); ?>
    
    <div class="container main-content">
        <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-left: none;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 style="color: white;">
                        <i class="fas fa-rupee-sign"></i>
                        Service Prices
                    </h2>
                    <p style="color: rgba(255,255,255,0.95);">View current service pricing</p>
                </div>
                <a href="dashboard.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; border-radius: 50px; padding: 10px 25px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert-custom" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-left: 5px solid #667eea;">
            <i class="fas fa-info-circle" style="color: #667eea;"></i>
            <strong>Price Information:</strong>
            <ul class="mb-0 mt-2">
                <li><span class="badge badge-success"><i class="fas fa-lock"></i> Admin Set</span> - Price is fixed by admin, you cannot change it</li>
                <li><span class="badge badge-warning"><i class="fas fa-edit"></i> Flexible</span> - You can set the price during service completion</li>
            </ul>
        </div>

        <!-- Services by Category -->
        <?php foreach($services as $category => $category_services): ?>
        <div class="card-custom mb-4">
            <h5 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 15px;">
                <i class="fas fa-wrench" style="color: #667eea;"></i>
                <?php echo htmlspecialchars($category); ?>
                <span class="badge badge-primary ml-2"><?php echo count($category_services); ?> services</span>
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th style="border: none;">#</th>
                            <th style="border: none;">Service Name</th>
                            <th style="border: none;">Subcategory</th>
                            <th style="border: none; text-align: center;">Price (₹)</th>
                            <th style="border: none; text-align: center;">Pricing Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($category_services as $service): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo $service->s_id; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($service->s_name); ?></strong>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo htmlspecialchars($service->s_subcategory ?: '-'); ?></small>
                            </td>
                            <td style="text-align: center;">
                                <?php if($service->s_admin_price !== null && $service->s_admin_price > 0): ?>
                                <span style="font-size: 1.3rem; color: #28a745; font-weight: 700;">
                                    ₹<?php echo number_format($service->s_admin_price, 2); ?>
                                </span>
                                <?php else: ?>
                                <span style="font-size: 1.1rem; color: #6c757d; font-style: italic;">
                                    Set during completion
                                </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if($service->s_admin_price !== null && $service->s_admin_price > 0): ?>
                                <span class="badge badge-success" style="padding: 8px 12px; font-size: 0.9rem;">
                                    <i class="fas fa-lock"></i> Admin Set
                                </span>
                                <?php else: ?>
                                <span class="badge badge-warning" style="padding: 8px 12px; font-size: 0.9rem;">
                                    <i class="fas fa-edit"></i> Flexible
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($services)): ?>
        <div class="card-custom text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5>No Active Services</h5>
            <p class="text-muted">There are currently no active services in the system.</p>
        </div>
        <?php endif; ?>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transition: all 0.3s ease;
        }
        
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 15px 10px;
        }
    </style>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
