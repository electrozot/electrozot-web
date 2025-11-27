<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in AJAX response
ini_set('log_errors', 1);

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$period = isset($_GET['period']) ? $_GET['period'] : 'today';

// Calculate date ranges based on period
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

// Get booking stats
$stats_query = "SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN sb_status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN sb_status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN sb_status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN sb_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN sb_status = 'Completed' THEN sb_total_price ELSE 0 END) as revenue
              FROM tms_service_booking 
              WHERE DATE(sb_created_at) BETWEEN ? AND ?";

$stmt = $mysqli->prepare($stats_query);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_object();

// Get top services
$top_services_query = "SELECT s.s_name, s.s_category, COUNT(*) as count, SUM(sb.sb_total_price) as revenue
                       FROM tms_service_booking sb
                       JOIN tms_service s ON sb.sb_service_id = s.s_id
                       WHERE sb.sb_status = 'Completed' 
                       AND DATE(sb.sb_created_at) BETWEEN ? AND ?
                       GROUP BY sb.sb_service_id
                       ORDER BY count DESC
                       LIMIT 5";
$stmt_services = $mysqli->prepare($top_services_query);
$stmt_services->bind_param('ss', $start_date, $end_date);
$stmt_services->execute();
$top_services = $stmt_services->get_result();

// Get top technicians
$top_techs_query = "SELECT t.t_name, COUNT(*) as jobs, SUM(sb.sb_total_price) as revenue
                    FROM tms_service_booking sb
                    JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                    WHERE sb.sb_status = 'Completed'
                    AND DATE(sb.sb_created_at) BETWEEN ? AND ?
                    GROUP BY sb.sb_technician_id
                    ORDER BY jobs DESC
                    LIMIT 5";
$stmt_techs = $mysqli->prepare($top_techs_query);
$stmt_techs->bind_param('ss', $start_date, $end_date);
$stmt_techs->execute();
$top_techs = $stmt_techs->get_result();

// Calculate completion rate
$completion_rate = $stats->total_bookings > 0 ? round(($stats->completed / $stats->total_bookings) * 100, 1) : 0;
?>

<!-- Main Stats Cards -->
<div class="row">
    <!-- Total Bookings -->
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1" style="opacity: 0.9; font-size: 0.9rem;">Total Bookings</p>
                        <h2 class="mb-0" style="font-weight: 900; font-size: 2.5rem;"><?php echo $stats->total_bookings; ?></h2>
                        <small style="opacity: 0.8;"><?php echo $label; ?></small>
                    </div>
                    <div style="opacity: 0.3;">
                        <i class="fas fa-calendar-check" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue -->
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1" style="opacity: 0.9; font-size: 0.9rem;">Revenue</p>
                        <h2 class="mb-0" style="font-weight: 900; font-size: 2rem;">₹<?php echo number_format($stats->revenue, 0); ?></h2>
                        <small style="opacity: 0.8;">From completed jobs</small>
                    </div>
                    <div style="opacity: 0.3;">
                        <i class="fas fa-rupee-sign" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Completed -->
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1" style="opacity: 0.9; font-size: 0.9rem;">Completed</p>
                        <h2 class="mb-0" style="font-weight: 900; font-size: 2.5rem;"><?php echo $stats->completed; ?></h2>
                        <small style="opacity: 0.8;"><?php echo $completion_rate; ?>% completion rate</small>
                    </div>
                    <div style="opacity: 0.3;">
                        <i class="fas fa-check-circle" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- In Progress -->
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1" style="opacity: 0.9; font-size: 0.9rem;">In Progress</p>
                        <h2 class="mb-0" style="font-weight: 900; font-size: 2.5rem;"><?php echo ($stats->pending + $stats->approved); ?></h2>
                        <small style="opacity: 0.8;">Pending + Approved</small>
                    </div>
                    <div style="opacity: 0.3;">
                        <i class="fas fa-clock" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm stat-card">
            <div class="card-body p-4">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px;">
                    <i class="fas fa-chart-pie"></i> Status Breakdown - <?php echo $label; ?>
                </h5>
                <div class="row text-center">
                    <div class="col">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #10b981;"></i>
                            <h3 class="mt-3 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->completed; ?></h3>
                            <small style="color: #718096;">Completed</small>
                        </div>
                    </div>
                    <div class="col">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-clock" style="font-size: 2.5rem; color: #f59e0b;"></i>
                            <h3 class="mt-3 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->pending; ?></h3>
                            <small style="color: #718096;">Pending</small>
                        </div>
                    </div>
                    <div class="col">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-thumbs-up" style="font-size: 2.5rem; color: #3b82f6;"></i>
                            <h3 class="mt-3 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->approved; ?></h3>
                            <small style="color: #718096;">Approved</small>
                        </div>
                    </div>
                    <div class="col">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-times-circle" style="font-size: 2.5rem; color: #ef4444;"></i>
                            <h3 class="mt-3 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->rejected; ?></h3>
                            <small style="color: #718096;">Rejected</small>
                        </div>
                    </div>
                    <div class="col">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-ban" style="font-size: 2.5rem; color: #6b7280;"></i>
                            <h3 class="mt-3 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->cancelled; ?></h3>
                            <small style="color: #718096;">Cancelled</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Performers -->
<div class="row mt-4">
    <!-- Top Services -->
    <div class="col-md-6">
        <div class="card shadow-sm stat-card">
            <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 15px 20px;">
                <h6 class="mb-0" style="color: white; font-weight: 700;">
                    <i class="fas fa-star"></i> Top Services - <?php echo $label; ?>
                </h6>
            </div>
            <div class="card-body p-3">
                <?php if($top_services && $top_services->num_rows > 0): ?>
                    <?php $rank = 1; while($service = $top_services->fetch_object()): ?>
                        <div class="top-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 15px;">
                                        <?php echo $rank++; ?>
                                    </span>
                                    <div>
                                        <strong style="color: #2d3748;"><?php echo htmlspecialchars($service->s_name); ?></strong><br>
                                        <small class="badge badge-secondary"><?php echo $service->s_category; ?></small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <strong style="color: #10b981;">₹<?php echo number_format($service->revenue, 0); ?></strong><br>
                                    <small style="color: #718096;"><?php echo $service->count; ?> bookings</small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No completed services in this period</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Top Technicians -->
    <div class="col-md-6">
        <div class="card shadow-sm stat-card">
            <div class="card-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; padding: 15px 20px;">
                <h6 class="mb-0" style="color: white; font-weight: 700;">
                    <i class="fas fa-trophy"></i> Top Technicians - <?php echo $label; ?>
                </h6>
            </div>
            <div class="card-body p-3">
                <?php if($top_techs && $top_techs->num_rows > 0): ?>
                    <?php $rank = 1; while($tech = $top_techs->fetch_object()): ?>
                        <div class="top-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 15px;">
                                        <?php echo $rank++; ?>
                                    </span>
                                    <div>
                                        <strong style="color: #2d3748;"><?php echo htmlspecialchars($tech->t_name); ?></strong><br>
                                        <small style="color: #718096;"><?php echo $tech->jobs; ?> jobs completed</small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <strong style="color: #10b981;">₹<?php echo number_format($tech->revenue, 0); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No completed jobs in this period</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
