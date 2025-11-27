<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$period = isset($_GET['period']) ? $_GET['period'] : 'today';

// Calculate date ranges
switch($period) {
    case 'today':
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $label = 'Today';
        break;
    case 'yesterday':
        $start_date = date('Y-m-d', strtotime('-1 day'));
        $end_date = date('Y-m-d', strtotime('-1 day'));
        $label = 'Yesterday';
        break;
    case 'week':
        $start_date = date('Y-m-d', strtotime('monday this week'));
        $end_date = date('Y-m-d');
        $label = 'This Week';
        break;
    case 'month':
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        $label = 'This Month';
        break;
    case 'year':
        $start_date = date('Y-01-01');
        $end_date = date('Y-m-d');
        $label = 'This Year';
        break;
    default:
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $label = 'Today';
}

// Get all services with bookings
$all_services_query = "SELECT s.s_name, s.s_category, s.s_subcategory, COUNT(*) as count, SUM(COALESCE(sb.sb_final_price, sb.sb_total_price, 0)) as revenue
                       FROM tms_service_booking sb
                       JOIN tms_service s ON sb.sb_service_id = s.s_id
                       WHERE sb.sb_status = 'Completed' 
                       AND DATE(sb.sb_created_at) BETWEEN ? AND ?
                       GROUP BY sb.sb_service_id
                       ORDER BY count DESC";
$stmt_services = $mysqli->prepare($all_services_query);
$stmt_services->bind_param('ss', $start_date, $end_date);
$stmt_services->execute();
$all_services = $stmt_services->get_result();
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
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin-stats.php?period=<?php echo $period; ?>">Statistics</a></li>
                    <li class="breadcrumb-item active">Services Performance</li>
                </ol>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="font-weight-bold text-primary mb-1">
                            <i class="fas fa-star"></i> Services Performance Report
                        </h3>
                        <p class="text-muted mb-0">Period: <strong><?php echo $label; ?></strong> (<?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?>)</p>
                    </div>
                    <a href="admin-stats.php?period=<?php echo $period; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Statistics
                    </a>
                </div>
                
                <div class="card shadow">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-list"></i> All Services Performance
                            <?php if($all_services): ?>
                                <span class="badge badge-light ml-2"><?php echo $all_services->num_rows; ?> Services</span>
                            <?php endif; ?>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if($all_services && $all_services->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th>Service Name</th>
                                            <th>Category</th>
                                            <th>Subcategory</th>
                                            <th class="text-center">Bookings</th>
                                            <th class="text-right">Total Revenue</th>
                                            <th class="text-right">Avg/Booking</th>
                                            <th class="text-center">Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rank = 1; 
                                        $medal_colors = ['#FFD700', '#C0C0C0', '#CD7F32'];
                                        $total_revenue = 0;
                                        $total_bookings = 0;
                                        
                                        // Calculate totals first
                                        $all_services->data_seek(0);
                                        while($s = $all_services->fetch_object()) {
                                            $total_revenue += $s->revenue;
                                            $total_bookings += $s->count;
                                        }
                                        
                                        // Reset pointer
                                        $all_services->data_seek(0);
                                        
                                        while($service = $all_services->fetch_object()): 
                                            $medal_color = isset($medal_colors[$rank - 1]) ? $medal_colors[$rank - 1] : '#667eea';
                                            $avg_per_booking = $service->count > 0 ? round($service->revenue / $service->count, 0) : 0;
                                            $revenue_percentage = $total_revenue > 0 ? round(($service->revenue / $total_revenue) * 100, 1) : 0;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center" style="background: <?php echo $medal_color; ?>; color: white; font-weight: 700; font-size: 0.9rem; width: 35px; height: 35px; border-radius: 50%;">
                                                        <?php echo $rank; ?>
                                                    </div>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($service->s_name); ?></strong></td>
                                                <td><span class="badge badge-secondary"><?php echo htmlspecialchars($service->s_category); ?></span></td>
                                                <td><small class="text-muted"><?php echo htmlspecialchars($service->s_subcategory); ?></small></td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary badge-pill"><?php echo $service->count; ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-success">₹<?php echo number_format($service->revenue, 0); ?></strong>
                                                </td>
                                                <td class="text-right text-muted">
                                                    ₹<?php echo number_format($avg_per_booking, 0); ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $revenue_percentage; ?>%" aria-valuenow="<?php echo $revenue_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                                            <?php echo $revenue_percentage; ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php 
                                            $rank++;
                                        endwhile; ?>
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="4" class="text-right">TOTAL:</td>
                                            <td class="text-center"><?php echo $total_bookings; ?></td>
                                            <td class="text-right text-success">₹<?php echo number_format($total_revenue, 0); ?></td>
                                            <td class="text-right">₹<?php echo $total_bookings > 0 ? number_format($total_revenue / $total_bookings, 0) : 0; ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-5 text-center">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No completed services in this period</h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            <?php include('vendor/inc/footer.php');?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
</body>
</html>
