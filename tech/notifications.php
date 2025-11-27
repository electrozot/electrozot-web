<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "Notifications";

// Get recent bookings as notifications
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, s.s_name
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_technician_id = ?
          ORDER BY sb.sb_created_at DESC
          LIMIT 20";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 80px;
            padding-bottom: 100px;
            position: relative;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 8px 20px;
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 2px solid rgba(6, 182, 212, 0.3);
            height: 70px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        
        .logo-image {
            width: 55px;
            height: 55px;
            background: transparent;
            border-radius: 8px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }
        
        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            justify-content: center;
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 900;
            color: white;
            margin: 0;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
            margin: 0;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .notif-icon-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            position: relative;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            text-decoration: none;
        }
        
        .notif-icon-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.4);
            color: white;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #0ea5e9 100%);
            padding: 25px 20px;
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4);
            margin-bottom: 30px;
            border-radius: 20px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 900;
            color: white;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header h1 i {
            font-size: 2rem;
            animation: bellShake 3s ease-in-out infinite;
        }

        @keyframes bellShake {
            0%, 90%, 100% { transform: rotate(0deg); }
            92%, 96% { transform: rotate(-10deg); }
            94%, 98% { transform: rotate(10deg); }
        }
        
        .notif-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border-left: 5px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .notif-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #06b6d4 100%);
        }
        
        .notif-card:hover {
            box-shadow: 0 6px 25px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }
        
        .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .notif-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notif-title i {
            color: #10b981;
            font-size: 1.3rem;
        }
        
        .notif-time {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            background: #f1f5f9;
            padding: 5px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }
        
        .notif-details {
            color: #475569;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .notif-details strong {
            color: #1e293b;
            font-weight: 700;
        }

        .notif-details i {
            color: #10b981;
            margin-right: 5px;
            width: 20px;
        }
        
        .notif-status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 800;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .status-pending {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #856404;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #00c853 0%, #00F260 100%);
            color: white;
        }
        
        .status-in-progress {
            background: linear-gradient(135deg, #0575E6 0%, #03a9f4 100%);
            color: white;
        }

        .status-approved {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 25px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            color: #64748b;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #94a3b8;
            font-size: 1rem;
        }

        .view-booking-btn {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
            margin-top: 10px;
        }

        .view-booking-btn:hover {
            background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%);
            text-decoration: none;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
                margin: 20px auto;
                padding-bottom: 100px;
            }

            .page-header {
                padding: 20px 15px;
                margin-bottom: 20px;
            }

            .page-header h1 {
                font-size: 1.4rem;
            }

            .page-header h1 i {
                font-size: 1.6rem;
            }
            
            .notif-card {
                padding: 15px;
            }
            
            .notif-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .notif-title {
                font-size: 1.1rem;
            }

            .notif-details {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <a href="dashboard.php" class="logo-section">
            <div class="logo-image">
                <img src="../vendor/EZlogonew.png" alt="EZ">
            </div>
            <div class="brand-info">
                <div class="brand-title">ELECTROZOT</div>
                <div class="brand-subtitle">We Make Perfect</div>
            </div>
        </a>
        <div class="header-actions">
            <a href="notifications.php" class="notif-icon-btn">
                <i class="fas fa-bell"></i>
            </a>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
        </div>
        <?php if($result->num_rows > 0): ?>
            <?php while($notif = $result->fetch_object()): 
                $status_class = strtolower(str_replace(' ', '-', $notif->sb_status));
                $time_ago = time_elapsed_string($notif->sb_created_at);
            ?>
            <div class="notif-card">
                <div class="notif-header">
                    <h3 class="notif-title">
                        <i class="fas fa-calendar-check"></i>
                        Booking #<?php echo $notif->sb_id; ?>
                    </h3>
                    <span class="notif-time">
                        <i class="fas fa-clock"></i> <?php echo $time_ago; ?>
                    </span>
                </div>
                <div class="notif-details">
                    <div style="margin-bottom: 10px;">
                        <i class="fas fa-user"></i>
                        <strong><?php echo htmlspecialchars($notif->u_fname . ' ' . $notif->u_lname); ?></strong>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <i class="fas fa-wrench"></i>
                        <strong><?php echo htmlspecialchars($notif->s_name); ?></strong>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <i class="fas fa-phone"></i>
                        <?php echo htmlspecialchars($notif->u_phone); ?>
                    </div>
                    <div>
                        <i class="fas fa-calendar"></i>
                        <?php echo date('M d, Y', strtotime($notif->sb_booking_date)); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="notif-status status-<?php echo $status_class; ?>">
                        <?php echo $notif->sb_status; ?>
                    </span>
                    <a href="booking-details.php?id=<?php echo $notif->sb_id; ?>" class="view-booking-btn">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications Yet</h3>
                <p>You'll see your booking notifications here when they arrive.</p>
                <a href="dashboard.php" class="view-booking-btn" style="margin-top: 20px;">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>

<?php
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}
?>
