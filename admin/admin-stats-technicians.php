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

// Get all technicians with completed jobs and accurate revenue
$all_techs_query = "SELECT 
                        t.t_name, 
                        t.t_ez_id, 
                        t.t_phone, 
                        t.t_category, 
                        COUNT(*) as jobs,
                        SUM(COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0)) as revenue,
                        SUM(CASE WHEN DATE(sb.sb_completed_at) = CURDATE() THEN COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0) ELSE 0 END) as today_revenue,
                        SUM(CASE WHEN MONTH(sb.sb_completed_at) = MONTH(CURDATE()) AND YEAR(sb.sb_completed_at) = YEAR(CURDATE()) THEN COALESCE(sb.sb_bill_amount, sb.sb_final_price, sb.sb_tech_decided_price, sb.sb_total_price, 0) ELSE 0 END) as monthly_revenue,
                        COUNT(CASE WHEN DATE(sb.sb_completed_at) = CURDATE() THEN 1 END) as today_jobs,
                        COUNT(CASE WHEN MONTH(sb.sb_completed_at) = MONTH(CURDATE()) AND YEAR(sb.sb_completed_at) = YEAR(CURDATE()) THEN 1 END) as monthly_jobs
                    FROM tms_service_booking sb
                    JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                    WHERE sb.sb_status = 'Completed'
                    AND DATE(sb.sb_completed_at) BETWEEN ? AND ?
                    GROUP BY sb.sb_technician_id
                    ORDER BY revenue DESC";
$stmt_techs = $mysqli->prepare($all_techs_query);
$stmt_techs->bind_param('ss', $start_date, $end_date);
$stmt_techs->execute();
$all_techs = $stmt_techs->get_result();
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
                    <li class="breadcrumb-item active">Technicians Performance</li>
                </ol>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="font-weight-bold text-success mb-1">
                            <i class="fas fa-trophy"></i> Technicians Performance Report
                        </h3>
                        <p class="text-muted mb-0">Period: <strong><?php echo $label; ?></strong> (<?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?>)</p>
                    </div>
                    <a href="admin-stats.php?period=<?php echo $period; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Statistics
                    </a>
                </div>
                
                <div class="card shadow">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-list"></i> All Technicians Performance
                            <?php if($all_techs): ?>
                                <span class="badge badge-light ml-2"><?php echo $all_techs->num_rows; ?> Technicians</span>
                            <?php endif; ?>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if($all_techs && $all_techs->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th>Technician Name</th>
                                            <th>EZ ID</th>
                                            <th>Phone</th>
                                            <th>Category</th>
                                            <th class="text-center">Jobs</th>
                                            <th class="text-right">Today Revenue</th>
                                            <th class="text-right">Monthly Revenue</th>
                                            <th class="text-right">Total Revenue</th>
                                            <th class="text-center">Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rank = 1; 
                                        $medal_colors = ['#FFD700', '#C0C0C0', '#CD7F32'];
                                        $total_revenue = 0;
                                        $total_jobs = 0;
                                        
                                        // Calculate totals first
                                        $all_techs->data_seek(0);
                                        while($t = $all_techs->fetch_object()) {
                                            $total_revenue += $t->revenue;
                                            $total_jobs += $t->jobs;
                                        }
                                        
                                        // Reset pointer
                                        $all_techs->data_seek(0);
                                        
                                        while($tech = $all_techs->fetch_object()): 
                                            $medal_color = isset($medal_colors[$rank - 1]) ? $medal_colors[$rank - 1] : '#4facfe';
                                            $avg_per_job = $tech->jobs > 0 ? round($tech->revenue / $tech->jobs, 0) : 0;
                                            $revenue_percentage = $total_revenue > 0 ? round(($tech->revenue / $total_revenue) * 100, 1) : 0;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center" style="background: <?php echo $medal_color; ?>; color: white; font-weight: 700; font-size: 0.9rem; width: 35px; height: 35px; border-radius: 50%;">
                                                        <?php echo $rank; ?>
                                                    </div>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($tech->t_name); ?></strong></td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($tech->t_ez_id ?? 'N/A'); ?></span></td>
                                                <td><small class="text-muted"><?php echo htmlspecialchars($tech->t_phone); ?></small></td>
                                                <td><span class="badge badge-secondary"><?php echo htmlspecialchars($tech->t_category ?? 'N/A'); ?></span></td>
                                                <td class="text-center">
                                                    <span class="badge badge-success badge-pill"><?php echo $tech->jobs; ?></span>
                                                    <br><small class="text-muted">(Today: <?php echo $tech->today_jobs; ?>)</small>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-primary">₹<?php echo number_format($tech->today_revenue, 0); ?></strong>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-info">₹<?php echo number_format($tech->monthly_revenue, 0); ?></strong>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-success">₹<?php echo number_format($tech->revenue, 0); ?></strong>
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $revenue_percentage; %>%" aria-valuenow="<?php echo $revenue_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
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
                                            <td colspan="5" class="text-right">TOTAL:</td>
                                            <td class="text-center"><?php echo $total_jobs; ?></td>
                                            <td class="text-right text-success">₹<?php echo number_format($total_revenue, 0); ?></td>
                                            <td class="text-right">₹<?php echo $total_jobs > 0 ? number_format($total_revenue / $total_jobs, 0) : 0; ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-5 text-center">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No completed jobs in this period</h5>
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
