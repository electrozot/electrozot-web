<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get period from URL or default to today
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

// Get booking statistics
$stats_query = "SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN sb_status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN sb_status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN sb_status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN sb_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN sb_status = 'Completed' THEN COALESCE(sb_final_price, sb_total_price, 0) ELSE 0 END) as revenue
              FROM tms_service_booking 
              WHERE DATE(sb_created_at) BETWEEN ? AND ?";

$stmt = $mysqli->prepare($stats_query);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_object();

// Get all services with bookings
$all_services_query = "SELECT s.s_name, s.s_category, COUNT(*) as count, SUM(COALESCE(sb.sb_final_price, sb.sb_total_price, 0)) as revenue
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

// Get all technicians with completed jobs
$all_techs_query = "SELECT t.t_name, t.t_ez_id, COUNT(*) as jobs, SUM(COALESCE(sb.sb_final_price, sb.sb_total_price, 0)) as revenue
                    FROM tms_service_booking sb
                    JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                    WHERE sb.sb_status = 'Completed'
                    AND DATE(sb.sb_created_at) BETWEEN ? AND ?
                    GROUP BY sb.sb_technician_id
                    ORDER BY jobs DESC";
$stmt_techs = $mysqli->prepare($all_techs_query);
$stmt_techs->bind_param('ss', $start_date, $end_date);
$stmt_techs->execute();
$all_techs = $stmt_techs->get_result();

// Calculate completion rate
$completion_rate = $stats->total_bookings > 0 ? round(($stats->completed / $stats->total_bookings) * 100, 1) : 0;

// Get average booking value
$avg_booking_value = $stats->completed > 0 ? round($stats->revenue / $stats->completed, 0) : 0;

// Get category-wise revenue
$category_revenue_query = "SELECT s.s_category, COUNT(*) as bookings, SUM(COALESCE(sb.sb_final_price, sb.sb_total_price, 0)) as revenue
                           FROM tms_service_booking sb
                           JOIN tms_service s ON sb.sb_service_id = s.s_id
                           WHERE sb.sb_status = 'Completed'
                           AND DATE(sb.sb_created_at) BETWEEN ? AND ?
                           GROUP BY s.s_category
                           ORDER BY revenue DESC";
$stmt_cat = $mysqli->prepare($category_revenue_query);
$stmt_cat->bind_param('ss', $start_date, $end_date);
$stmt_cat->execute();
$category_revenue = $stmt_cat->get_result();

// Get daily trend (last 7 days for week/month/year view)
if($period != 'today' && $period != 'yesterday') {
    $trend_days = 7;
    $trend_start = date('Y-m-d', strtotime("-$trend_days days"));
    $daily_trend_query = "SELECT DATE(sb_created_at) as date, 
                          COUNT(*) as bookings,
                          SUM(CASE WHEN sb_status = 'Completed' THEN COALESCE(sb_final_price, sb_total_price, 0) ELSE 0 END) as revenue
                          FROM tms_service_booking
                          WHERE DATE(sb_created_at) BETWEEN ? AND ?
                          GROUP BY DATE(sb_created_at)
                          ORDER BY date ASC";
    $stmt_trend = $mysqli->prepare($daily_trend_query);
    $stmt_trend->bind_param('ss', $trend_start, $end_date);
    $stmt_trend->execute();
    $daily_trend = $stmt_trend->get_result();
}

// Get customer statistics
$customer_stats_query = "SELECT 
                         COUNT(DISTINCT sb_user_id) as unique_customers,
                         COUNT(*) / COUNT(DISTINCT sb_user_id) as avg_bookings_per_customer
                         FROM tms_service_booking
                         WHERE DATE(sb_created_at) BETWEEN ? AND ?
                         AND sb_user_id IS NOT NULL";
$stmt_cust = $mysqli->prepare($customer_stats_query);
$stmt_cust->bind_param('ss', $start_date, $end_date);
$stmt_cust->execute();
$customer_stats = $stmt_cust->get_result()->fetch_object();

// Get technician utilization
$tech_utilization_query = "SELECT 
                           COUNT(DISTINCT t.t_id) as active_technicians,
                           COUNT(*) as total_assignments
                           FROM tms_service_booking sb
                           JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                           WHERE DATE(sb.sb_created_at) BETWEEN ? AND ?";
$stmt_tech_util = $mysqli->prepare($tech_utilization_query);
$stmt_tech_util->bind_param('ss', $start_date, $end_date);
$stmt_tech_util->execute();
$tech_utilization = $stmt_tech_util->get_result()->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<style>
.stat-card {
    border: none;
    border-radius: 15px;
    transition: all 0.3s ease;
    overflow: hidden;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
.gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.gradient-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
}
.gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.gradient-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
}
.gradient-secondary {
    background: linear-gradient(135deg, #868f96 0%, #596164 100%);
}
.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
}
.chart-container {
    position: relative;
    height: 300px;
}
.top-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.top-item:hover {
    background: #f8f9fa;
    border-left-color: #667eea;
    transform: translateX(5px);
}
.period-btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.period-btn:hover {
    transform: translateY(-2px);
}
</style>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <!-- Breadcrumb -->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Business Statistics</li>
                </ol>
                
                <!-- Header with Period Selector -->
                <div class="card shadow mb-3 stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="mb-2 mb-md-0">
                                <h5 class="mb-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-line"></i> Business Statistics - <?php echo $label; ?>
                                </h5>
                                <small class="text-muted"><?php echo date('d M Y', strtotime($start_date)); ?> to <?php echo date('d M Y', strtotime($end_date)); ?></small>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="?period=today" class="btn <?php echo $period == 'today' ? 'btn-primary' : 'btn-outline-primary'; ?>">Today</a>
                                <a href="?period=yesterday" class="btn <?php echo $period == 'yesterday' ? 'btn-primary' : 'btn-outline-primary'; ?>">Yesterday</a>
                                <a href="?period=week" class="btn <?php echo $period == 'week' ? 'btn-primary' : 'btn-outline-primary'; ?>">Week</a>
                                <a href="?period=month" class="btn <?php echo $period == 'month' ? 'btn-primary' : 'btn-outline-primary'; ?>">Month</a>
                                <a href="?period=year" class="btn <?php echo $period == 'year' ? 'btn-primary' : 'btn-outline-primary'; ?>">Year</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Stats Cards - Compact -->
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow h-100">
                            <div class="card-body gradient-primary text-white p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small style="opacity: 0.9;">Total Bookings</small>
                                        <h3 class="mb-0 font-weight-bold"><?php echo $stats->total_bookings; ?></h3>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x" style="opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow h-100">
                            <div class="card-body gradient-success text-white p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small style="opacity: 0.9;">Revenue</small>
                                        <h3 class="mb-0 font-weight-bold">₹<?php echo number_format($stats->revenue, 0); ?></h3>
                                        <small style="opacity: 0.8; font-size: 0.7rem;">Avg: ₹<?php echo number_format($avg_booking_value, 0); ?></small>
                                    </div>
                                    <i class="fas fa-rupee-sign fa-2x" style="opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow h-100">
                            <div class="card-body gradient-info text-white p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div style="width: 100%;">
                                        <small style="opacity: 0.9;">Completed</small>
                                        <h3 class="mb-1 font-weight-bold"><?php echo $stats->completed; ?> <small style="font-size: 0.6rem;">(<?php echo $completion_rate; ?>%)</small></h3>
                                        <div class="progress" style="height: 4px; background: rgba(255,255,255,0.3);">
                                            <div class="progress-bar bg-white" style="width: <?php echo $completion_rate; ?>%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x" style="opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow h-100">
                            <div class="card-body gradient-warning text-white p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small style="opacity: 0.9;">In Progress</small>
                                        <h3 class="mb-0 font-weight-bold"><?php echo ($stats->pending + $stats->approved); ?></h3>
                                    </div>
                                    <i class="fas fa-clock fa-2x" style="opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Secondary Stats - Compact Row -->
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-users text-primary mb-1"></i>
                                <h5 class="mb-0 font-weight-bold"><?php echo $customer_stats->unique_customers ?? 0; ?></h5>
                                <small class="text-muted">Customers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-user-cog text-success mb-1"></i>
                                <h5 class="mb-0 font-weight-bold"><?php echo $tech_utilization->active_technicians ?? 0; ?></h5>
                                <small class="text-muted">Technicians</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-clock text-warning mb-1"></i>
                                <h5 class="mb-0 font-weight-bold"><?php echo $stats->pending; ?></h5>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-thumbs-up text-info mb-1"></i>
                                <h5 class="mb-0 font-weight-bold"><?php echo $stats->approved; ?></h5>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-times-circle text-danger mb-1"></i>
                                <h5 class="mb-0 font-weight-bold"><?php echo $stats->rejected; ?></h5>
                                <small class="text-muted">Rejected</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-ban text-secondary mb-1"></i>
                                <h5 class="mb-0 font-weight-bold"><?php echo $stats->cancelled; ?></h5>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Category Revenue Breakdown - Compact -->
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <div class="card shadow stat-card">
                            <div class="card-header py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h6 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-chart-bar"></i> Revenue by Category
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <?php if($category_revenue && $category_revenue->num_rows > 0): ?>
                                    <div class="row">
                                        <?php 
                                        $cat_colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                        $cat_index = 0;
                                        while($cat = $category_revenue->fetch_object()): 
                                            $color = $cat_colors[$cat_index % count($cat_colors)];
                                            $cat_percentage = $stats->revenue > 0 ? round(($cat->revenue / $stats->revenue) * 100, 1) : 0;
                                        ?>
                                        <div class="col-md-4 col-sm-6 mb-2">
                                            <div class="card border-left-<?php echo $color; ?> h-100">
                                                <div class="card-body p-2">
                                                    <small class="text-<?php echo $color; ?> font-weight-bold">
                                                        <?php echo htmlspecialchars($cat->s_category); ?>
                                                    </small>
                                                    <h5 class="mb-1 font-weight-bold">₹<?php echo number_format($cat->revenue, 0); ?></h5>
                                                    <div class="progress mb-1" style="height: 4px;">
                                                        <div class="progress-bar bg-<?php echo $color; ?>" style="width: <?php echo $cat_percentage; ?>%"></div>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        <?php echo $cat->bookings; ?> bookings • <?php echo $cat_percentage; ?>%
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            $cat_index++;
                                        endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-2 mb-0">No completed bookings in this period</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Reports Buttons -->
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <div class="card shadow-lg stat-card h-100">
                            <div class="card-body text-center p-4" style="background: linear-gradient(135deg, rgba(240, 147, 251, 0.1), rgba(245, 87, 108, 0.1));">
                                <div class="icon-shape mx-auto mb-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 80px; height: 80px;">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <h4 class="font-weight-bold mb-2">Services Performance</h4>
                                <p class="text-muted mb-3">
                                    View detailed performance report for all services
                                    <?php if($all_services): ?>
                                        <br><span class="badge badge-primary"><?php echo $all_services->num_rows; ?> Services with bookings</span>
                                    <?php endif; ?>
                                </p>
                                <a href="admin-stats-services.php?period=<?php echo $period; ?>" class="btn btn-lg btn-primary">
                                    <i class="fas fa-chart-line"></i> View Full Services Report
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-3">
                        <div class="card shadow-lg stat-card h-100">
                            <div class="card-body text-center p-4" style="background: linear-gradient(135deg, rgba(79, 172, 254, 0.1), rgba(0, 242, 254, 0.1));">
                                <div class="icon-shape mx-auto mb-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 80px; height: 80px;">
                                    <i class="fas fa-trophy fa-2x"></i>
                                </div>
                                <h4 class="font-weight-bold mb-2">Technicians Performance</h4>
                                <p class="text-muted mb-3">
                                    View detailed performance report for all technicians
                                    <?php if($all_techs): ?>
                                        <br><span class="badge badge-success"><?php echo $all_techs->num_rows; ?> Active Technicians</span>
                                    <?php endif; ?>
                                </p>
                                <a href="admin-stats-technicians.php?period=<?php echo $period; ?>" class="btn btn-lg btn-success">
                                    <i class="fas fa-user-cog"></i> View Full Technicians Report
                                </a>
                            </div>
                        </div>
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
