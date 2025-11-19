<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');

$t_id = $_SESSION['t_id'];
$t_name = $_SESSION['t_name'];
$t_id_no = $_SESSION['t_id_no'];
$page_title = "My Profile";

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
$t_pic = isset($tech_data->t_pic) ? $tech_data->t_pic : '';
$t_category = isset($tech_data->t_category) ? $tech_data->t_category : '';
$t_experience = isset($tech_data->t_experience) ? $tech_data->t_experience : '';
$t_specialization = isset($tech_data->t_specialization) ? $tech_data->t_specialization : '';
$t_service_pincode = isset($tech_data->t_service_pincode) ? $tech_data->t_service_pincode : '';

// Extract pincode from address (for display)
$t_pincode = '';
if(!empty($t_addr)) {
    preg_match('/\b\d{6}\b/', $t_addr, $matches);
    if(!empty($matches)) {
        $t_pincode = $matches[0];
    }
}

// Get current month statistics
$current_month = date('Y-m');
$month_start = $current_month . '-01';
$month_end = date('Y-m-t');

// Total orders this month
$orders_query = "SELECT COUNT(*) as total_orders FROM tms_service_booking 
                 WHERE sb_technician_id = ? 
                 AND DATE(sb_booking_date) BETWEEN ? AND ?";
$stmt_orders = $mysqli->prepare($orders_query);
$stmt_orders->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
$orders_data = $orders_result->fetch_object();
$total_orders = $orders_data->total_orders;

// Completed orders this month
$completed_query = "SELECT COUNT(*) as completed_orders FROM tms_service_booking 
                    WHERE sb_technician_id = ? 
                    AND sb_status = 'Completed'
                    AND DATE(sb_booking_date) BETWEEN ? AND ?";
$stmt_completed = $mysqli->prepare($completed_query);
$stmt_completed->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_completed->execute();
$completed_result = $stmt_completed->get_result();
$completed_data = $completed_result->fetch_object();
$completed_orders = $completed_data->completed_orders;

// Calculate earnings (completed orders * service price)
$earnings_query = "SELECT SUM(s.s_price) as total_earnings 
                   FROM tms_service_booking sb
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_technician_id = ? 
                   AND sb.sb_status = 'Completed'
                   AND DATE(sb_booking_date) BETWEEN ? AND ?";
$stmt_earnings = $mysqli->prepare($earnings_query);
$stmt_earnings->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_earnings->execute();
$earnings_result = $stmt_earnings->get_result();
$earnings_data = $earnings_result->fetch_object();
$total_earnings = $earnings_data->total_earnings ? $earnings_data->total_earnings : 0;

// Count services in service pincode area
$services_in_pincode = 0;
$display_pincode = !empty($t_service_pincode) ? $t_service_pincode : $t_pincode;

if(!empty($display_pincode)) {
    $pincode_query = "SELECT COUNT(*) as pincode_services 
                      FROM tms_service_booking sb
                      LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                      WHERE sb.sb_technician_id = ? 
                      AND u.u_addr LIKE ?";
    $stmt_pincode = $mysqli->prepare($pincode_query);
    $pincode_param = "%{$display_pincode}%";
    $stmt_pincode->bind_param('is', $t_id, $pincode_param);
    $stmt_pincode->execute();
    $pincode_result = $stmt_pincode->get_result();
    $pincode_data = $pincode_result->fetch_object();
    $services_in_pincode = $pincode_data->pincode_services;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
            padding: 30px 15px;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .back-btn {
            background: white;
            color: #2d3748;
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #e2e8f0;
        }
        
        .back-btn:hover {
            background: #f7fafc;
            text-decoration: none;
            color: #2d3748;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        
        .profile-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #c0fa87ff 0%, #667eea 100%);
            padding: 40px;
            color: white;
            position: relative;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        
        .service-pincode-badge {
            background: rgba(255,255,255,0.15);
            padding: 12px 25px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.25);
        }
        
        .service-pincode-badge h5 {
            margin: 0;
            font-weight: 800;
            font-size: 1.2rem;
        }
        
        .profile-main {
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
            z-index: 2;
        }
        
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 20px;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            object-fit: cover;
            background: white;
        }
        
        .profile-photo-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 20px;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }
        
        .profile-info h2 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 10px;
        }
        
        .tech-id-badge {
            background: rgba(255,255,255,0.25);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 900;
            display: inline-block;
            margin-bottom: 15px;
            font-size: 1.1rem;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .contact-info {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.15);
            padding: 10px 20px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .contact-item i {
            font-size: 1.2rem;
        }
        
        .profile-actions {
            padding: 30px 40px;
            background: #f7fafc;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            border-top: 1px solid #e2e8f0;
        }
        
        .action-btn {
            flex: 1;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-change-password {
            background: linear-gradient(135deg, #4299e1 0%, #667eea 100%);
            color: white;
        }
        
        .btn-change-password:hover {
            background: linear-gradient(135deg, #3182ce 0%, #5a67d8 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(66, 153, 225, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            color: white;
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #be13eeff 0%, #e53ea8ff 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(245, 101, 101, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .btn-call-admin {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }
        
        .btn-call-admin:hover {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .btn-whatsapp-admin {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
        }
        
        .btn-whatsapp-admin:hover {
            background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .profile-details {
            padding: 40px;
        }
        
        .detail-row {
            display: flex;
            padding: 20px 0;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 700;
            color: #666;
            width: 200px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-label i {
            color: #4299e1;
            width: 25px;
        }
        
        .detail-value {
            color: #333;
            flex: 1;
            font-weight: 600;
        }
        
        .stats-section {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .stats-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .stats-header h3 {
            font-size: 2rem;
            font-weight: 900;
            color: #333;
            margin-bottom: 10px;
        }
        
        .stats-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f85959ff 0%, #fc9484ff 100%);
            padding: 30px;
            border-radius: 20px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        
        .stat-card.stat-orders {
            background: linear-gradient(135deg, #f75af7ff 0%, #f4e18cff 100%);
        }
        
        .stat-card.stat-earnings {
            background: linear-gradient(135deg, #60f3e4ff 0%, #d368a3ff 100%);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stat-value {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .stat-label {
            font-size: 1.1rem;
            font-weight: 600;
            opacity: 0.95;
            position: relative;
            z-index: 2;
        }
        
        @media (max-width: 768px) {
            .profile-main {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-info h2 {
                font-size: 1.8rem;
            }
            
            .contact-info {
                justify-content: center;
            }
            
            .profile-actions {
                flex-direction: column;
            }
            
            .action-btn {
                width: 100%;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .detail-label {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <!-- Profile Card -->
        <div class="profile-card">
            <!-- Header Section -->
            <div class="profile-header">
                <div class="service-pincode-badge">
                    <h5>
                        <i class="fas fa-map-marker-alt"></i> 
                        Service Area Pincode: <?php echo $display_pincode ? $display_pincode : 'Not Set'; ?> 
                        <span class="badge badge-light ml-2"><?php echo $services_in_pincode; ?> Services</span>
                    </h5>
                </div>
                
                <div class="profile-main">
                    <div>
                        <?php if(!empty($t_pic)): ?>
                            <img src="../vendor/img/<?php echo htmlspecialchars($t_pic); ?>" class="profile-photo" alt="Profile Photo">
                        <?php else: ?>
                            <div class="profile-photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($t_name); ?></h2>
                        <div class="tech-id-badge">
                            Technician ID: <?php echo htmlspecialchars($t_id_no); ?>
                        </div>
                        
                        <div class="contact-info">
                            <?php if(!empty($t_phone)): ?>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo htmlspecialchars($t_phone); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($t_email)): ?>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo htmlspecialchars($t_email); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="profile-actions">
                <a href="tel:7559606925" class="action-btn btn-call-admin">
                    <i class="fas fa-phone-alt"></i> Call Admin
                </a>
                <a href="https://wa.me/917559606925?text=Hi%20Admin,%20I%20am%20<?php echo urlencode($t_name); ?>%20(ID:%20<?php echo urlencode($t_id_no); ?>).%20I%20need%20assistance." target="_blank" class="action-btn btn-whatsapp-admin">
                    <i class="fab fa-whatsapp"></i> WhatsApp Admin
                </a>
                <a href="change-password.php" class="action-btn btn-change-password">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="logout.php" class="action-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            <!-- Profile Details -->
            <div class="profile-details">
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-briefcase"></i>
                        Category
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_category); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-star"></i>
                        Specialization
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_specialization); ?></div>
                </div>
                
                <?php if(!empty($t_service_pincode)): ?>
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-map-pin"></i>
                        Service Pincode
                    </div>
                    <div class="detail-value">
                        <span style="background: linear-gradient(135deg, #4299e1, #667eea); color: white; padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 1.1rem;">
                            <?php echo htmlspecialchars($t_service_pincode); ?>
                        </span>
                        <small style="display: block; margin-top: 8px; color: #666;">
                            <i class="fas fa-info-circle"></i> This is your designated service area
                        </small>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-clock"></i>
                        Experience
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_experience); ?> years</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Address
                    </div>
                    <div class="detail-value"><?php echo htmlspecialchars($t_addr); ?></div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="stats-section">
            <div class="stats-header">
                <h3>
                    <i class="fas fa-chart-line"></i> Data of This Month
                </h3>
                <p><?php echo date('F Y'); ?></p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card stat-orders">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_orders; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                
                <div class="stat-card stat-earnings">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-value">₹<?php echo number_format($total_earnings, 0); ?></div>
                    <div class="stat-label">Earnings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $completed_orders; ?></div>
                    <div class="stat-label">Completed Orders</div>
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
