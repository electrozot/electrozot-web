<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');
check_login();

$t_id = $_SESSION['t_id'];

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_conditions = ["sb.sb_technician_id = ?"];
$params = [$t_id];
$types = 'i';

if($filter != 'all') {
    $where_conditions[] = "sb.sb_status = ?";
    $params[] = $filter;
    $types .= 's';
}

if(!empty($search)) {
    $where_conditions[] = "(u.u_fname LIKE ? OR u.u_lname LIKE ? OR sb.sb_phone LIKE ? OR s.s_name LIKE ? OR sb.sb_id LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $types .= 'sssss';
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Get total count
$count_query = "SELECT COUNT(*) as total FROM tms_service_booking sb
                LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                $where_clause";

$count_stmt = $mysqli->prepare($count_query);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = ceil($total_records / $per_page);

// Get notifications
$query = "SELECT sb.*, 
                 u.u_fname, u.u_lname, sb.sb_phone, u.u_email,
                 s.s_name, s.s_category, s.s_price
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          $where_clause
          ORDER BY sb.sb_created_at DESC
          LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';
$stmt = $mysqli->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/head.php'); ?>
    <title>My Notifications - Technician Panel</title>
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .page-header h2 {
            margin: 0;
            color: #2d3748;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header h2 i {
            color: #ff4757;
            font-size: 2rem;
        }

        .page-header p {
            margin: 10px 0 0 0;
            color: #6c757d;
            font-size: 1.05rem;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .stat-card.pending { border-left-color: #ffa502; }
        .stat-card.progress { border-left-color: #00b4db; }
        .stat-card.completed { border-left-color: #11998e; }
        .stat-card.rejected { border-left-color: #ff4757; }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            color: #2d3748;
            line-height: 1;
            margin-bottom: 10px;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filters-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .filter-btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filter-btn {
            padding: 12px 25px;
            border-radius: 50px;
            border: 2px solid;
            font-weight: 700;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn.all {
            border-color: #667eea;
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .filter-btn.all.active, .filter-btn.all:hover {
            background: #667eea;
            color: white;
        }

        .filter-btn.pending {
            border-color: #ffa502;
            color: #ffa502;
            background: rgba(255, 165, 2, 0.1);
        }

        .filter-btn.pending.active, .filter-btn.pending:hover {
            background: #ffa502;
            color: white;
        }

        .filter-btn.progress {
            border-color: #00b4db;
            color: #00b4db;
            background: rgba(0, 180, 219, 0.1);
        }

        .filter-btn.progress.active, .filter-btn.progress:hover {
            background: #00b4db;
            color: white;
        }

        .filter-btn.completed {
            border-color: #11998e;
            color: #11998e;
            background: rgba(17, 153, 142, 0.1);
        }

        .filter-btn.completed.active, .filter-btn.completed:hover {
            background: #11998e;
            color: white;
        }

        .filter-btn.rejected {
            border-color: #ff4757;
            color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
        }

        .filter-btn.rejected.active, .filter-btn.rejected:hover {
            background: #ff4757;
            color: white;
        }

        .notification-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .notification-card:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }

        .notification-card.status-pending { border-left-color: #ffa502; }
        .notification-card.status-approved { border-left-color: #00b4db; }
        .notification-card.status-in-progress { border-left-color: #667eea; }
        .notification-card.status-completed { border-left-color: #11998e; }
        .notification-card.status-rejected, .notification-card.status-cancelled { border-left-color: #ff4757; }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .notification-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }

        .notification-badge {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending { background: #ffa502; color: white; }
        .badge-approved { background: #00b4db; color: white; }
        .badge-in-progress { background: #667eea; color: white; }
        .badge-completed { background: #11998e; color: white; }
        .badge-rejected, .badge-cancelled { background: #ff4757; color: white; }

        .notification-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 1rem;
            color: #2d3748;
            font-weight: 600;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .action-btn {
            padding: 10px 20px;
            border-radius: 50px;
            border: none;
            font-weight: 700;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-complete {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
        }

        .btn-complete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
            color: white;
            text-decoration: none;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        .pagination .page-link {
            border-radius: 50px;
            margin: 0 5px;
            border: 2px solid #667eea;
            color: #667eea;
            font-weight: 700;
        }

        .pagination .page-item.active .page-link {
            background: #667eea;
            border-color: #667eea;
        }

        .pagination .page-link:hover {
            background: #667eea;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .empty-state i {
            font-size: 5rem;
            color: #e2e8f0;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #6c757d;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .notification-header {
                flex-direction: column;
                gap: 15px;
            }

            .notification-details {
                grid-template-columns: 1fr;
            }

            .filter-btn-group {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include('includes/nav.php'); ?>

    <div class="container-fluid" style="padding: 30px;">
        <!-- Page Header -->
        <div class="page-header">
            <h2>
                <i class="fas fa-bell"></i>
                My Notifications
            </h2>
            <p>All your booking notifications and updates in one place</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card pending">
                <div class="stat-value">
                    <?php
                    $pending_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Pending'";
                    $stmt_p = $mysqli->prepare($pending_query);
                    $stmt_p->bind_param('i', $t_id);
                    $stmt_p->execute();
                    echo $stmt_p->get_result()->fetch_assoc()['count'];
                    ?>
                </div>
                <div class="stat-label">Pending</div>
            </div>

            <div class="stat-card progress">
                <div class="stat-value">
                    <?php
                    $progress_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'In Progress'";
                    $stmt_pr = $mysqli->prepare($progress_query);
                    $stmt_pr->bind_param('i', $t_id);
                    $stmt_pr->execute();
                    echo $stmt_pr->get_result()->fetch_assoc()['count'];
                    ?>
                </div>
                <div class="stat-label">In Progress</div>
            </div>

            <div class="stat-card completed">
                <div class="stat-value">
                    <?php
                    $completed_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND sb_status = 'Completed'";
                    $stmt_c = $mysqli->prepare($completed_query);
                    $stmt_c->bind_param('i', $t_id);
                    $stmt_c->execute();
                    echo $stmt_c->get_result()->fetch_assoc()['count'];
                    ?>
                </div>
                <div class="stat-label">Completed</div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-value">
                    <?php
                    $rejected_query = "SELECT COUNT(*) as count FROM tms_service_booking WHERE sb_technician_id = ? AND (sb_status = 'Rejected' OR sb_status = 'Cancelled')";
                    $stmt_r = $mysqli->prepare($rejected_query);
                    $stmt_r->bind_param('i', $t_id);
                    $stmt_r->execute();
                    echo $stmt_r->get_result()->fetch_assoc()['count'];
                    ?>
                </div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <div class="filter-btn-group">
                <a href="?filter=all" class="filter-btn all <?php echo $filter == 'all' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> All Notifications
                </a>
                <a href="?filter=Pending" class="filter-btn pending <?php echo $filter == 'Pending' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="?filter=In Progress" class="filter-btn progress <?php echo $filter == 'In Progress' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> In Progress
                </a>
                <a href="?filter=Completed" class="filter-btn completed <?php echo $filter == 'Completed' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i> Completed
                </a>
                <a href="?filter=Rejected" class="filter-btn rejected <?php echo $filter == 'Rejected' ? 'active' : ''; ?>">
                    <i class="fas fa-times-circle"></i> Rejected
                </a>
            </div>

            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by customer name, phone, service..." value="<?php echo htmlspecialchars($search); ?>" style="border-radius: 50px 0 0 50px; border: 2px solid #e2e8f0; padding: 12px 20px;">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <div class="input-group-append">
                        <button class="btn" type="submit" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 0 50px 50px 0; padding: 12px 30px; font-weight: 700;">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Notifications List -->
        <?php
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $status_class = strtolower(str_replace(' ', '-', $row['sb_status']));
                $customer_name = !empty($row['u_fname']) ? $row['u_fname'] . ' ' . $row['u_lname'] : 'Guest Customer';
                
                $badge_class = 'badge-' . $status_class;
                $time_ago = time_elapsed_string($row['sb_created_at']);
        ?>
        <div class="notification-card status-<?php echo $status_class; ?>">
            <div class="notification-header">
                <h3 class="notification-title">
                    Booking #<?php echo $row['sb_id']; ?> - <?php echo htmlspecialchars($row['s_name']); ?>
                </h3>
                <span class="notification-badge <?php echo $badge_class; ?>">
                    <?php echo $row['sb_status']; ?>
                </span>
            </div>

            <div class="notification-details">
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-user"></i> Customer</span>
                    <span class="detail-value"><?php echo htmlspecialchars($customer_name); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-phone"></i> Phone</span>
                    <span class="detail-value"><?php echo htmlspecialchars($row['sb_phone']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar"></i> Booking Date</span>
                    <span class="detail-value"><?php echo date('M d, Y', strtotime($row['sb_booking_date'])); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-clock"></i> Time</span>
                    <span class="detail-value"><?php echo $time_ago; ?></span>
                </div>
            </div>

            <?php if(!empty($row['sb_address'])): ?>
            <div class="detail-item" style="margin-top: 15px;">
                <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Address</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['sb_address']); ?></span>
            </div>
            <?php endif; ?>

            <div class="notification-actions">
                <a href="booking-details.php?id=<?php echo $row['sb_id']; ?>" class="action-btn btn-view">
                    <i class="fas fa-eye"></i> View Details
                </a>
                <?php if($row['sb_status'] == 'In Progress'): ?>
                <a href="complete-booking.php?id=<?php echo $row['sb_id']; ?>" class="action-btn btn-complete">
                    <i class="fas fa-check"></i> Mark Complete
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
            }
        } else {
        ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h3>No notifications found</h3>
            <p>You don't have any notifications matching your criteria.</p>
        </div>
        <?php } ?>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
