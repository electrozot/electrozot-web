<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$page_title = "Booking Details";

// Create cancelled bookings table if not exists
try {
    $create_cancelled_table = "CREATE TABLE IF NOT EXISTS tms_cancelled_bookings (
        cb_id INT AUTO_INCREMENT PRIMARY KEY,
        cb_booking_id INT NOT NULL,
        cb_technician_id INT NOT NULL,
        cb_cancelled_by VARCHAR(50) DEFAULT 'Admin',
        cb_reason VARCHAR(255) DEFAULT 'Technician reassigned by admin',
        cb_cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(cb_booking_id),
        INDEX(cb_technician_id)
    )";
    $mysqli->query($create_cancelled_table);
} catch(Exception $e) {}

// Get booking ID
$sb_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle status update
if(isset($_POST['update_status'])){
    $new_status = $_POST['sb_status'];
    $update_query = "UPDATE tms_service_booking SET sb_status=? WHERE sb_id=? AND sb_technician_id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('sii', $new_status, $sb_id, $t_id);
    
    if($update_stmt->execute()){
        $success = "Booking status updated successfully!";
    } else {
        $error = "Failed to update status. Please try again.";
    }
}

// Check if this booking was cancelled for this technician
$cancel_check = "SELECT cb_id, cb_reason, cb_cancelled_at FROM tms_cancelled_bookings 
                 WHERE cb_booking_id = ? AND cb_technician_id = ?";
$cancel_stmt = $mysqli->prepare($cancel_check);
$cancel_stmt->bind_param('ii', $sb_id, $t_id);
$cancel_stmt->execute();
$cancel_result = $cancel_stmt->get_result();
$is_cancelled = $cancel_result->num_rows > 0;
$cancel_info = $is_cancelled ? $cancel_result->fetch_object() : null;

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_email, u.u_addr, s.s_name, s.s_price, s.s_description, s.s_category
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          WHERE sb.sb_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $sb_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header('Location: dashboard.php');
    exit();
}

$booking = $result->fetch_object();

// Prevent status updates if booking is cancelled for this technician
if($is_cancelled && isset($_POST['update_status'])){
    $error = "You cannot update this booking as it has been reassigned to another technician.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 70px;
            padding-bottom: 20px;
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
        
        @media (max-width: 576px) {
            .header {
                padding: 8px 15px;
            }
            
            .logo-image {
                width: 50px;
                height: 50px;
            }
            
            .brand-title {
                font-size: 1.2rem;
            }
            
            .brand-subtitle {
                font-size: 0.65rem;
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
            <a href="dashboard.php" class="notif-icon-btn" title="Dashboard">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </div>
    
    <div class="container" style="margin-top: 20px; padding: 0 15px;">

        <?php if($is_cancelled): ?>
            <div class="alert alert-warning" style="border-left: 5px solid #ff9800; background-color: #fff3e0; padding: 20px;">
                <h4 style="color: #ff9800; margin-bottom: 15px;">
                    <i class="fas fa-ban"></i> Booking Cancelled by Admin
                </h4>
                <p style="margin-bottom: 10px;">
                    <strong>Reason:</strong> <?php echo htmlspecialchars($cancel_info->cb_reason); ?>
                </p>
                <p style="margin-bottom: 10px;">
                    <strong>Cancelled At:</strong> <?php echo date('M d, Y h:i A', strtotime($cancel_info->cb_cancelled_at)); ?>
                </p>
                <p style="margin-bottom: 0; color: #666;">
                    <i class="fas fa-info-circle"></i> This booking has been reassigned to another technician. You cannot perform any actions on this booking. You are now available for new bookings.
                </p>
            </div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Clean Simple Booking Details -->
        <div class="card-custom" style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 30px;">
                <h3 style="color: #3b82f6; font-weight: 700; font-size: 2rem;">Order #<?php echo $sb_id; ?></h3>
            </div>

            <div style="background: #f1f5f9; padding: 25px; border-radius: 12px;">
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 5px;">Customer Name</label>
                    <p style="font-size: 1.3rem; color: #1e293b; font-weight: 700; margin: 0;">
                        <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?>
                    </p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 5px;">Phone</label>
                    <p style="margin: 0;">
                        <a href="tel:<?php echo $booking->u_phone; ?>" style="font-size: 1.2rem; color: #10b981; font-weight: 700; text-decoration: none;">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking->u_phone); ?>
                        </a>
                    </p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 5px;">Address</label>
                    <p style="font-size: 1.1rem; color: #1e293b; font-weight: 600; margin: 0; line-height: 1.6;">
                        <?php echo htmlspecialchars($booking->sb_address); ?>
                    </p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 5px;">Service</label>
                    <p style="font-size: 1.2rem; color: #1e293b; font-weight: 700; margin: 0;">
                        <?php echo htmlspecialchars($booking->s_name); ?>
                    </p>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 5px;">Date</label>
                        <p style="font-size: 1.05rem; color: #1e293b; font-weight: 600; margin: 0;">
                            <?php echo date('M d, Y', strtotime($booking->sb_booking_date)); ?>
                        </p>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 5px;">Time</label>
                        <p style="font-size: 1.05rem; color: #1e293b; font-weight: 600; margin: 0;">
                            <?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bottom Navigation Bar -->
    <?php include('includes/bottom-nav.php'); ?>
</body>
</html>
