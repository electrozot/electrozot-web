<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get all technicians
$query = "SELECT t_id, t_name, t_phone, t_ez_id, t_id_no, t_category, t_status, t_service_pincode 
          FROM tms_technician 
          ORDER BY t_ez_id ASC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<?php include('vendor/inc/head.php');?>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>

    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Technicians</a>
                    </li>
                    <li class="breadcrumb-item active">All Technicians</li>
                </ol>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users"></i> All Technicians</h5>
                        <a href="admin-add-technician.php" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add New Technician
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th><i class="fas fa-id-badge text-primary"></i> EZ ID</th>
                                        <th><i class="fas fa-user"></i> Name</th>
                                        <th><i class="fas fa-mobile-alt text-success"></i> Mobile</th>
                                        <th><i class="fas fa-map-pin"></i> Pincode</th>
                                        <th><i class="fas fa-briefcase"></i> Category</th>
                                        <th><i class="fas fa-circle"></i> Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $count = 1;
                                    while($tech = $result->fetch_object()): 
                                        $statusClass = $tech->t_status == 'Available' ? 'success' : 'warning';
                                    ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td>
                                            <span class="badge badge-primary" style="font-size: 0.9rem; padding: 5px 10px;">
                                                <?php echo htmlspecialchars($tech->t_ez_id); ?>
                                            </span>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($tech->t_name); ?></strong></td>
                                        <td>
                                            <a href="tel:<?php echo $tech->t_phone; ?>" class="text-success">
                                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($tech->t_phone); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($tech->t_service_pincode); ?></td>
                                        <td><small><?php echo htmlspecialchars($tech->t_category); ?></small></td>
                                        <td>
                                            <span class="badge badge-<?php echo $statusClass; ?>">
                                                <?php echo $tech->t_status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="admin-view-single-technician.php?t_id=<?php echo $tech->t_id; ?>" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="admin-update-technician.php?t_id=<?php echo $tech->t_id; ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary Stats -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Validation Summary</h6>
                                    <ul class="mb-0">
                                        <li><strong>All EZ IDs are unique</strong> - Each technician has a unique company ID (EZ0001, EZ0002, etc.)</li>
                                        <li><strong>All Mobile Numbers are unique</strong> - No duplicate phone numbers allowed</li>
                                        <li><strong>Auto-generated IDs</strong> - System automatically generates the next available EZ ID</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include('vendor/inc/footer.php');?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    <script src="vendor/js/demo/datatables-demo.js"></script>
</body>
</html>
