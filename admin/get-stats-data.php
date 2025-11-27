<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get today's stats
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$this_week_start = date('Y-m-d', strtotime('monday this week'));
$this_month_start = date('Y-m-01');
$this_year_start = date('Y-01-01');
$last_month_start = date('Y-m-01', strtotime('first day of last month'));
$last_month_end = date('Y-m-t', strtotime('last day of last month'));

// Function to get booking stats
function getBookingStats($mysqli, $start_date, $end_date = null) {
    if ($end_date === null) {
        $end_date = $start_date;
    }
    
    $query = "SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN sb_status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN sb_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN sb_status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN sb_status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN sb_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN sb_status = 'Completed' THEN sb_total_price ELSE 0 END) as revenue
              FROM tms_service_booking 
              WHERE DATE(sb_created_at) BETWEEN ? AND ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_object();
}

// Get stats for different periods
$today_stats = getBookingStats($mysqli, $today);
$yesterday_stats = getBookingStats($mysqli, $yesterday);
$week_stats = getBookingStats($mysqli, $this_week_start, $today);
$month_stats = getBookingStats($mysqli, $this_month_start, $today);
$year_stats = getBookingStats($mysqli, $this_year_start, $today);
$last_month_stats = getBookingStats($mysqli, $last_month_start, $last_month_end);

// Get top services
$top_services_query = "SELECT s.s_name, s.s_category, COUNT(*) as booking_count, SUM(sb.sb_total_price) as total_revenue
                       FROM tms_service_booking sb
                       JOIN tms_service s ON sb.sb_service_id = s.s_id
                       WHERE sb.sb_status = 'Completed'
                       GROUP BY sb.sb_service_id
                       ORDER BY booking_count DESC
                       LIMIT 5";
$top_services = $mysqli->query($top_services_query);

// Get top technicians
$top_techs_query = "SELECT t.t_name, COUNT(*) as completed_jobs, SUM(sb.sb_total_price) as total_revenue
                    FROM tms_service_booking sb
                    JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                    WHERE sb.sb_status = 'Completed'
                    GROUP BY sb.sb_technician_id
                    ORDER BY completed_jobs DESC
                    LIMIT 5";
$top_techs = $mysqli->query($top_techs_query);

// Calculate growth percentages
$today_vs_yesterday = $yesterday_stats->total_bookings > 0 
    ? (($today_stats->total_bookings - $yesterday_stats->total_bookings) / $yesterday_stats->total_bookings) * 100 
    : 0;

$month_vs_last_month = $last_month_stats->total_bookings > 0 
    ? (($month_stats->total_bookings - $last_month_stats->total_bookings) / $last_month_stats->total_bookings) * 100 
    : 0;
?>

<!-- Period Tabs -->
<ul class="nav nav-pills mb-4" id="statsPeriodTabs" role="tablist" style="background: white; padding: 10px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <li class="nav-item" style="flex: 1;">
        <a class="nav-link active" id="today-tab" data-toggle="pill" href="#today" role="tab" style="text-align: center; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            <i class="fas fa-calendar-day"></i> Today
        </a>
    </li>
    <li class="nav-item" style="flex: 1;">
        <a class="nav-link" id="yesterday-tab" data-toggle="pill" href="#yesterday" role="tab" style="text-align: center; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            <i class="fas fa-history"></i> Yesterday
        </a>
    </li>
    <li class="nav-item" style="flex: 1;">
        <a class="nav-link" id="week-tab" data-toggle="pill" href="#week" role="tab" style="text-align: center; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            <i class="fas fa-calendar-week"></i> This Week
        </a>
    </li>
    <li class="nav-item" style="flex: 1;">
        <a class="nav-link" id="month-tab" data-toggle="pill" href="#month" role="tab" style="text-align: center; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            <i class="fas fa-calendar-alt"></i> This Month
        </a>
    </li>
    <li class="nav-item" style="flex: 1;">
        <a class="nav-link" id="year-tab" data-toggle="pill" href="#year" role="tab" style="text-align: center; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            <i class="fas fa-calendar"></i> This Year
        </a>
    </li>
</ul>

<style>
    .nav-pills .nav-link {
        color: #4a5568;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
    }
    .nav-pills .nav-link:hover {
        background: rgba(102, 126, 234, 0.1);
    }
</style>

<div class="tab-content" id="statsPeriodContent">
    <!-- Today Tab -->
    <div class="tab-pane fade show active" id="today" role="tabpanel">
        <?php renderStatsCards($today_stats, 'Today', $today_vs_yesterday, 'vs Yesterday'); ?>
    </div>
    
    <!-- Yesterday Tab -->
    <div class="tab-pane fade" id="yesterday" role="tabpanel">
        <?php renderStatsCards($yesterday_stats, 'Yesterday'); ?>
    </div>
    
    <!-- Week Tab -->
    <div class="tab-pane fade" id="week" role="tabpanel">
        <?php renderStatsCards($week_stats, 'This Week'); ?>
    </div>
    
    <!-- Month Tab -->
    <div class="tab-pane fade" id="month" role="tabpanel">
        <?php renderStatsCards($month_stats, 'This Month', $month_vs_last_month, 'vs Last Month'); ?>
    </div>
    
    <!-- Year Tab -->
    <div class="tab-pane fade" id="year" role="tabpanel">
        <?php renderStatsCards($year_stats, 'This Year'); ?>
    </div>
</div>

<!-- Top Performers Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 15px 20px;">
                <h6 class="mb-0" style="color: white; font-weight: 700;">
                    <i class="fas fa-star"></i> Top Services
                </h6>
            </div>
            <div class="card-body" style="padding: 20px;">
                <?php if($top_services && $top_services->num_rows > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php $rank = 1; while($service = $top_services->fetch_object()): ?>
                            <div class="list-group-item border-0 px-0 py-3" style="background: transparent;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <span style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                            <?php echo $rank++; ?>
                                        </span>
                                        <div>
                                            <strong style="color: #2d3748; font-size: 0.95rem;"><?php echo htmlspecialchars($service->s_name); ?></strong><br>
                                            <small style="color: #718096;">
                                                <span class="badge badge-secondary" style="font-size: 0.7rem;"><?php echo $service->s_category; ?></span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <strong style="color: #10b981; font-size: 1rem;">₹<?php echo number_format($service->total_revenue, 0); ?></strong><br>
                                        <small style="color: #718096;"><?php echo $service->booking_count; ?> bookings</small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No completed services yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; padding: 15px 20px;">
                <h6 class="mb-0" style="color: white; font-weight: 700;">
                    <i class="fas fa-trophy"></i> Top Technicians
                </h6>
            </div>
            <div class="card-body" style="padding: 20px;">
                <?php if($top_techs && $top_techs->num_rows > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php $rank = 1; while($tech = $top_techs->fetch_object()): ?>
                            <div class="list-group-item border-0 px-0 py-3" style="background: transparent;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <span style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                            <?php echo $rank++; ?>
                                        </span>
                                        <div>
                                            <strong style="color: #2d3748; font-size: 0.95rem;"><?php echo htmlspecialchars($tech->t_name); ?></strong><br>
                                            <small style="color: #718096;"><?php echo $tech->completed_jobs; ?> jobs completed</small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <strong style="color: #10b981; font-size: 1rem;">₹<?php echo number_format($tech->total_revenue, 0); ?></strong>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No completed jobs yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
function renderStatsCards($stats, $period, $growth = null, $growth_label = '') {
    ?>
    <div class="row">
        <!-- Total Bookings -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 600;">Total Bookings</p>
                            <h2 class="mb-0" style="font-weight: 900; font-size: 2.5rem;"><?php echo $stats->total_bookings; ?></h2>
                            <?php if($growth !== null): ?>
                                <small style="opacity: 0.8;">
                                    <i class="fas fa-<?php echo $growth >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                    <?php echo abs(round($growth, 1)); ?>% <?php echo $growth_label; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div style="opacity: 0.3;">
                            <i class="fas fa-calendar-check" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);">
                <div class="card-body text-white" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 600;">Revenue</p>
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
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body text-white" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 600;">Completed</p>
                            <h2 class="mb-0" style="font-weight: 900; font-size: 2.5rem;"><?php echo $stats->completed; ?></h2>
                            <small style="opacity: 0.8;">
                                <?php echo $stats->total_bookings > 0 ? round(($stats->completed / $stats->total_bookings) * 100, 1) : 0; ?>% completion rate
                            </small>
                        </div>
                        <div style="opacity: 0.3;">
                            <i class="fas fa-check-circle" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending/Approved -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 600;">In Progress</p>
                            <h2 class="mb-0" style="font-weight: 900; font-size: 2.5rem;"><?php echo ($stats->pending + $stats->approved); ?></h2>
                            <small style="opacity: 0.8;">
                                Pending: <?php echo $stats->pending; ?> | Approved: <?php echo $stats->approved; ?>
                            </small>
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
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm" style="border: none; border-radius: 15px;">
                <div class="card-body" style="padding: 25px;">
                    <h6 style="color: #2d3748; font-weight: 700; margin-bottom: 20px;">
                        <i class="fas fa-chart-pie"></i> Status Breakdown - <?php echo $period; ?>
                    </h6>
                    <div class="row text-center">
                        <div class="col">
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-check-circle" style="font-size: 2rem; color: #10b981;"></i>
                                <h4 class="mt-2 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->completed; ?></h4>
                                <small style="color: #718096;">Completed</small>
                            </div>
                        </div>
                        <div class="col">
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-clock" style="font-size: 2rem; color: #f59e0b;"></i>
                                <h4 class="mt-2 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->pending; ?></h4>
                                <small style="color: #718096;">Pending</small>
                            </div>
                        </div>
                        <div class="col">
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-thumbs-up" style="font-size: 2rem; color: #3b82f6;"></i>
                                <h4 class="mt-2 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->approved; ?></h4>
                                <small style="color: #718096;">Approved</small>
                            </div>
                        </div>
                        <div class="col">
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-times-circle" style="font-size: 2rem; color: #ef4444;"></i>
                                <h4 class="mt-2 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->rejected; ?></h4>
                                <small style="color: #718096;">Rejected</small>
                            </div>
                        </div>
                        <div class="col">
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-ban" style="font-size: 2rem; color: #6b7280;"></i>
                                <h4 class="mt-2 mb-0" style="color: #2d3748; font-weight: 700;"><?php echo $stats->cancelled; ?></h4>
                                <small style="color: #718096;">Cancelled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
