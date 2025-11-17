<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "Technician Dashboard";

// Ensure columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_phone VARCHAR(20) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_email VARCHAR(100) DEFAULT ''");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_addr TEXT DEFAULT ''");
} catch(Exception $e) {}

// Get technician details
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt_tech = $mysqli->prepare($tech_query);
$stmt_tech->bind_param('i', $t_id);
$stmt_tech->execute();
$tech_result = $stmt_tech->get_result();
$tech_data = $tech_result->fetch_object();

$t_phone = isset($tech_data->t_phone) ? $tech_data->t_phone : '';
$t_email = isset($tech_data->t_email) ? $tech_data->t_email : '';
$t_addr = isset($tech_data->t_addr) ? $tech_data->t_addr : '';
$t_pincode = '';
if(!empty($t_addr)) {
    preg_match('/\b\d{6}\b/', $t_addr, $matches);
    if(!empty($matches)) {
        $t_pincode = $matches[0];
    }
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filter
$where_clause = "WHERE sb.sb_technician_id = ?";
$params = [$t_id];
$types = 'i';

if($filter == 'new') {
    $where_clause .= " AND sb.sb_status = 'Pending'";
} elseif($filter == 'pending') {
    $where_clause .= " AND sb.sb_status = 'In Progress'";
} elseif($filter == 'completed') {
    $where_clause .= " AND sb.sb_status = 'Completed'";
}

if(!empty($search)) {
    $where_clause .= " AND u.u_phone LIKE ?";
    $params[] = "%{$search}%";
    $types .= 's';
}

$bookings_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name 
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   {$where_clause}
                   ORDER BY sb.sb_booking_date DESC, sb.sb_booking_time DESC";

$stmt_bookings = $mysqli->prepare($bookings_query);
if(count($params) == 1) {
    $stmt_bookings->bind_param($types, $params[0]);
} else {
    $stmt_bookings->bind_param($types, $params[0], $params[1]);
}
$stmt_bookings->execute();
$bookings_result = $stmt_bookings->get_result();

// Get counts
$new_count = 0;
$pending_count = 0;
$completed_count = 0;

$count_query = "SELECT 
                COUNT(CASE WHEN sb_status = 'Pending' THEN 1 END) as new_count,
                COUNT(CASE WHEN sb_status = 'In Progress' THEN 1 END) as pending_count,
                COUNT(CASE WHEN sb_status = 'Completed' THEN 1 END) as completed_count
                FROM tms_service_booking WHERE sb_technician_id = ?";
$stmt_count = $mysqli->prepare($count_query);
$stmt_count->bind_param('i', $t_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$counts = $count_result->fetch_object();
$new_count = $counts->new_count;
$pending_count = $counts->pending_count;
$completed_count = $counts->completed_count;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        /* Header */
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 800;
            color: #ff4757;
        }
        
        .dashboard-title {
            background: #ff4757;
            color: white;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        /* Control Bar */
        .control-bar {
            background: white;
            padding: 20px 30px;
            margin: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-box {
            flex: 1;
            min-width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #ff4757;
        }
        
        .filter-btn {
            padding: 12px 30px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            display: inline-block;
        }
        
        .filter-btn:hover {
            text-decoration: none;
            color: #333;
        }
        
        .filter-btn.active {
            background: #ff4757;
            color: white;
            border-color: #ff4757;
        }
        
        .filter-btn .badge {
            background: #ffd700;
            color: #ff4757;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.85rem;
            margin-left: 8px;
            font-weight: 900;
        }
        
        .filter-btn.active .badge {
            background: white;
            color: #ff4757;
        }
        
        /* Main Content */
        .main-container {
            display: flex;
            gap: 20px;
            padding: 0 30px 30px 30px;
        }
        
        .bookings-section {
            flex: 1;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .tech-card {
            width: 300px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        
        .tech-card-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .tech-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff4757, #ffa502);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 3rem;
            color: white;
        }
        
        .tech-name {
            font-size: 1.3rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 5px;
        }
        
        .tech-id {
            background: #ffd700;
            color: #ff4757;
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .tech-info {
            border-top: 2px solid #f0f0f0;
            padding-top: 15px;
        }
        
        .tech-info-item {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
        }
        
        .tech-info-item i {
            color: #ff4757;
            width: 20px;
        }
        
        .logout-btn {
            width: 100%;
            padding: 12px;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #ff6b9d;
            transform: translateY(-2px);
        }
        
        /* Table */
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bookings-table thead {
            background: #f8f9fa;
        }
        
        .bookings-table th {
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .bookings-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .bookings-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .status-new {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-pending {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .call-btn {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .call-btn:hover {
            background: #218838;
            text-decoration: none;
            color: white;
            transform: scale(1.05);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        @media (max-width: 1200px) {
            .main-container {
                flex-direction: column;
            }
            
            .tech-card {
                width: 100%;
                position: relative;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .control-bar {
                flex-direction: column;
            }
            
            .search-box {
                width: 100%;
            }
            
            .bookings-table {
                font-size: 0.85rem;
            }
            
            .bookings-table th,
            .bookings-table td {
                padding: 10px 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-bolt"></i> Electrozot
            </div>
            <div class="dashboard-title">
                Technician Dashboard
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="control-bar">
        <div class="search-box">
            <form action="" method="GET">
                <input type="search" name="search" placeholder="Search by mobile number..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if($filter != 'all'): ?>
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                <?php endif; ?>
            </form>
        </div>
        
        <a href="?filter=new" class="filter-btn <?php echo $filter == 'new' ? 'active' : ''; ?>">
            New
            <?php if($new_count > 0): ?>
                <span class="badge"><?php echo $new_count; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?filter=pending" class="filter-btn <?php echo $filter == 'pending' ? 'active' : ''; ?>">
            Pending
            <?php if($pending_count > 0): ?>
                <span class="badge"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?filter=completed" class="filter-btn <?php echo $filter == 'completed' ? 'active' : ''; ?>">
            Completed
            <?php if($completed_count > 0): ?>
                <span class="badge"><?php echo $completed_count; ?></span>
            <?php endif; ?>
        </a>
        
        <?php if($filter != 'all' || !empty($search)): ?>
            <a href="?" class="filter-btn">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Bookings Table -->
        <div class="bookings-section">
            <?php if($bookings_result->num_rows > 0): ?>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Name</th>
                            <th>Pincode</th>
                            <th>Address</th>
                            <th>Call</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($booking = $bookings_result->fetch_object()): 
                            // Extract pincode from customer address
                            $customer_pincode = '';
                            if(!empty($booking->u_addr)) {
                                preg_match('/\b\d{6}\b/', $booking->u_addr, $pin_matches);
                                if(!empty($pin_matches)) {
                                    $customer_pincode = $pin_matches[0];
                                }
                            }
                            
                            $status_class = '';
                            if($booking->sb_status == 'Pending') {
                                $status_class = 'status-new';
                            } elseif($booking->sb_status == 'In Progress') {
                                $status_class = 'status-pending';
                            } elseif($booking->sb_status == 'Completed') {
                                $status_class = 'status-completed';
                            }
                        ?>
                        <tr>
                            <td><strong>#<?php echo $booking->sb_id; ?></strong></td>
                            <td><?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></td>
                            <td><?php echo $customer_pincode ? $customer_pincode : '-'; ?></td>
                            <td><?php echo htmlspecialchars(substr($booking->u_addr, 0, 50)) . (strlen($booking->u_addr) > 50 ? '...' : ''); ?></td>
                            <td>
                                <?php if(!empty($booking->u_phone)): ?>
                                    <a href="tel:<?php echo $booking->u_phone; ?>" class="call-btn">
                                        <i class="fas fa-phone"></i> <?php echo $booking->u_phone; ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $booking->sb_status; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Bookings Found</h3>
                    <p>No bookings match your current filter.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tech Card -->
        <div class="tech-card">
            <div class="tech-card-header">
                <div class="tech-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="tech-name"><?php echo htmlspecialchars($t_name); ?></div>
                <div class="tech-id">ID: <?php echo htmlspecialchars($t_id_no); ?></div>
            </div>
            
            <div class="tech-info">
                <?php if(!empty($t_phone)): ?>
                    <div class="tech-info-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo htmlspecialchars($t_phone); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($t_email)): ?>
                    <div class="tech-info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo htmlspecialchars($t_email); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($t_pincode)): ?>
                    <div class="tech-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>PIN: <?php echo htmlspecialchars($t_pincode); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="tech-info-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo date('M d, Y'); ?></span>
                </div>
            </div>
            
            <a href="profile.php" class="logout-btn" style="background: #007bff; margin-bottom: 10px;">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
