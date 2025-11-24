<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];

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
    <title>Notifications</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 0;
            margin: 0;
        }
        
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .back-btn {
            background: #3b82f6;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .back-btn:hover {
            background: #2563eb;
            color: white;
            text-decoration: none;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .notif-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #3b82f6;
        }
        
        .notif-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        
        .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .notif-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .notif-time {
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .notif-details {
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .notif-details strong {
            color: #1e293b;
        }
        
        .notif-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #64748b;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 1.2rem;
            }
            
            .back-btn {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            
            .container {
                padding: 0 15px;
                margin: 20px auto;
            }
            
            .notif-card {
                padding: 15px;
            }
            
            .notif-header {
                flex-direction: column;
                gap: 8px;
            }
            
            .notif-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="container">
        <?php if($result->num_rows > 0): ?>
            <?php while($notif = $result->fetch_object()): 
                $status_class = strtolower(str_replace(' ', '-', $notif->sb_status));
                $time_ago = time_elapsed_string($notif->sb_created_at);
            ?>
            <div class="notif-card">
                <div class="notif-header">
                    <h3 class="notif-title">New Booking #<?php echo $notif->sb_id; ?></h3>
                    <span class="notif-time"><?php echo $time_ago; ?></span>
                </div>
                <div class="notif-details">
                    <strong><?php echo htmlspecialchars($notif->u_fname . ' ' . $notif->u_lname); ?></strong> booked 
                    <strong><?php echo htmlspecialchars($notif->s_name); ?></strong>
                    <br>
                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($notif->u_phone); ?>
                    <br>
                    <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($notif->sb_booking_date)); ?>
                </div>
                <span class="notif-status status-<?php echo $status_class; ?>">
                    <?php echo $notif->sb_status; ?>
                </span>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications</h3>
                <p>You don't have any notifications yet.</p>
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
