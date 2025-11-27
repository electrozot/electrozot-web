<?php
ob_start();
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if($booking_id <= 0) {
    header("Location: user-manage-booking.php");
    exit();
}

// Get booking details with all related information
$query = "SELECT 
            sb.*,
            s.s_name, s.s_category, s.s_price, s.s_description, s.s_duration,
            t.t_name, t.t_phone, t.t_email, t.t_id_no, t.t_specialization,
            u.u_fname, u.u_lname, u.u_phone as user_phone, u.u_email as user_email
          FROM tms_service_booking sb
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          WHERE sb.sb_id = ? AND sb.sb_user_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $aid);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if(!$booking) {
    header("Location: user-manage-booking.php");
    exit();
}

$service_name = $booking->s_name ?? 'Service';
$status = $booking->sb_status ?? 'Pending';
$has_technician = !empty($booking->sb_technician_id);

// Display status
$display_status = $status;
if($status == 'Approved' && $has_technician) {
    $display_status = 'In Progress';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Booking Details - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <?php include('vendor/inc/user-header-styles.php'); ?>
    <!-- Force reload: <?php echo time(); ?> -->
    <style>
        .content {
            padding: 15px;
        }
        
        .status-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.15);
            text-align: center;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }
        
        .booking-number {
            font-size: 12px;
            color: #6366f1;
            margin-bottom: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .service-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-card {
            background: white;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 12px;
            box-shadow: 0 2px 12px rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.08);
        }
        
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 10px;
            font-size: 14px;
        }
        
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            flex: 0 0 110px;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }
        
        .info-value {
            flex: 1;
            font-size: 13px;
            color: #1e293b;
            font-weight: 500;
        }
        
        .price-highlight {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 14px;
            border-radius: 12px;
            text-align: center;
            margin-top: 12px;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.2);
        }
        
        .price-label {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .price-amount {
            font-size: 24px;
            font-weight: 700;
        }
        
        .payment-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .payment-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .payment-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .image-gallery {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .image-item {
            position: relative;
        }
        
        .image-label {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
        }
        
        .image-wrapper {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .image-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        
        .btn-download {
            background: white;
            color: #6366f1;
            border: 2px solid #6366f1;
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-track {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        
        .btn-orders {
            background: white;
            color: #6366f1;
            border: 2px solid #6366f1;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #3b82f6;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        
        .alert i {
            margin-right: 8px;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px 20px;
            color: #999;
            font-size: 14px;
        }
        
        .empty-message i {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/user-header.php'); ?>

    <div class="content">
        <!-- Status Header -->
        <div class="status-header">
            <div class="booking-number">
                <i class="fas fa-receipt"></i> Booking #<?php echo str_pad($booking->sb_id, 5, '0', STR_PAD_LEFT); ?>
            </div>
            <div class="service-name"><?php echo htmlspecialchars($service_name); ?></div>
            <?php
            $badge_style = '';
            if($display_status == 'Completed') {
                $badge_style = 'background: #10b981;';
            } elseif($display_status == 'In Progress') {
                $badge_style = 'background: #8b5cf6;';
            } elseif($display_status == 'Pending') {
                $badge_style = 'background: #f59e0b;';
            } elseif(in_array($display_status, ['Cancelled', 'Rejected', 'Not Done'])) {
                $badge_style = 'background: #ef4444;';
            } else {
                $badge_style = 'background: #6366f1;';
            }
            ?>
            <div class="status-badge" style="<?php echo $badge_style; ?>">
                <?php echo $display_status; ?>
            </div>
        </div>

        <!-- Service Details -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-tools"></i>
                <span>Service Information</span>
            </div>
            
            <div class="info-row">
                <div class="info-label">Service Name</div>
                <div class="info-value"><?php echo htmlspecialchars($service_name); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Category</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->s_category ?? 'N/A'); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Duration</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->s_duration ?? 'N/A'); ?></div>
            </div>
            
            <?php if(!empty($booking->s_description)): ?>
            <div class="info-row">
                <div class="info-label">Description</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->s_description); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Booking Schedule -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule & Location</span>
            </div>
            
            <div class="info-row">
                <div class="info-label">Booking Date</div>
                <div class="info-value"><?php echo date('d M Y', strtotime($booking->sb_booking_date)); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Booking Time</div>
                <div class="info-value"><?php echo date('h:i A', strtotime($booking->sb_booking_time)); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Contact Phone</div>
                <div class="info-value">
                    <a href="tel:<?php echo htmlspecialchars($booking->sb_phone); ?>" style="color: #6366f1; text-decoration: none;">
                        <?php echo htmlspecialchars($booking->sb_phone); ?>
                    </a>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Pincode</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->sb_pincode); ?></div>
            </div>
            
            <?php if(!empty($booking->sb_address)): ?>
            <div class="info-row">
                <div class="info-label">Address</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->sb_address); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($booking->sb_description)): ?>
            <div class="info-row">
                <div class="info-label">Special Notes</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->sb_description); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Payment Information -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-rupee-sign"></i>
                <span>Payment Details</span>
            </div>
            
            <div class="info-row">
                <div class="info-label">Base Price</div>
                <div class="info-value">₹<?php echo number_format($booking->sb_total_price, 2); ?></div>
            </div>
            
            <?php if(!empty($booking->sb_final_price) || !empty($booking->sb_charged_price)): ?>
            <div class="info-row">
                <div class="info-label">Final Amount</div>
                <div class="info-value" style="color: #10b981; font-weight: 700;">
                    ₹<?php echo number_format(!empty($booking->sb_final_price) ? $booking->sb_final_price : $booking->sb_charged_price, 2); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="info-row">
                <div class="info-label">Payment Status</div>
                <div class="info-value">
                    <?php 
                    $payment_status = $booking->sb_payment_status ?? 'Pending';
                    $payment_class = ($payment_status == 'Paid') ? 'payment-paid' : 'payment-pending';
                    ?>
                    <span class="payment-status <?php echo $payment_class; ?>">
                        <?php echo htmlspecialchars($payment_status); ?>
                    </span>
                </div>
            </div>
            
            <?php if(!empty($booking->sb_payment_method)): ?>
            <div class="info-row">
                <div class="info-label">Payment Method</div>
                <div class="info-value"><?php echo htmlspecialchars($booking->sb_payment_method); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($booking->sb_payment_date)): ?>
            <div class="info-row">
                <div class="info-label">Payment Date</div>
                <div class="info-value"><?php echo date('d M Y, h:i A', strtotime($booking->sb_payment_date)); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($booking->sb_transaction_id)): ?>
            <div class="info-row">
                <div class="info-label">Transaction ID</div>
                <div class="info-value" style="font-family: monospace; font-size: 12px;">
                    <?php echo htmlspecialchars($booking->sb_transaction_id); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($display_status == 'Completed' && (!empty($booking->sb_final_price) || !empty($booking->sb_charged_price))): ?>
            <div class="price-highlight">
                <div class="price-label">Total Amount Charged</div>
                <div class="price-amount">
                    ₹<?php echo number_format(!empty($booking->sb_final_price) ? $booking->sb_final_price : $booking->sb_charged_price, 2); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Technician Information -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-user-cog"></i>
                <span>Technician Details</span>
            </div>
            
            <?php if($has_technician): ?>
                <div class="info-row">
                    <div class="info-label">Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($booking->t_name); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">ID Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($booking->t_id_no); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Specialization</div>
                    <div class="info-value"><?php echo htmlspecialchars($booking->t_specialization); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Phone</div>
                    <div class="info-value">
                        <a href="tel:<?php echo htmlspecialchars($booking->t_phone); ?>" style="color: #6366f1; text-decoration: none;">
                            <?php echo htmlspecialchars($booking->t_phone); ?>
                        </a>
                    </div>
                </div>
                
                <?php if(!empty($booking->t_email)): ?>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        <a href="mailto:<?php echo htmlspecialchars($booking->t_email); ?>" style="color: #6366f1; text-decoration: none;">
                            <?php echo htmlspecialchars($booking->t_email); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Technician Not Assigned Yet</strong><br>
                    We'll assign a qualified technician soon and notify you.
                </div>
            <?php endif; ?>
        </div>

        <!-- Service Completion Details (Only for Completed) -->
        <?php if($display_status == 'Completed'): ?>
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-check-circle"></i>
                <span>Completion Details</span>
            </div>
            
            <?php if(!empty($booking->sb_completed_date)): ?>
            <div class="info-row">
                <div class="info-label">Completed On</div>
                <div class="info-value"><?php echo date('d M Y, h:i A', strtotime($booking->sb_completed_date)); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($booking->sb_completion_notes)): ?>
            <div class="info-row">
                <div class="info-label">Technician Notes</div>
                <div class="info-value"><?php echo nl2br(htmlspecialchars($booking->sb_completion_notes)); ?></div>
            </div>
            <?php endif; ?>
            
            <?php 
            // Check for completion images
            $completion_img = !empty($booking->sb_completion_img) ? $booking->sb_completion_img : (!empty($booking->sb_service_image) ? $booking->sb_service_image : '');
            $bill_img = !empty($booking->sb_bill_img) ? $booking->sb_bill_img : (!empty($booking->sb_bill_image) ? $booking->sb_bill_image : '');
            
            if(!empty($completion_img) || !empty($bill_img)): 
            ?>
            <div class="image-gallery">
                <?php if(!empty($completion_img)): ?>
                <div class="image-item">
                    <div class="image-label">
                        <i class="fas fa-camera"></i> Service Completion Image
                    </div>
                    <div class="image-wrapper">
                        <img src="../vendor/img/completions/<?php echo htmlspecialchars($completion_img); ?>" alt="Service Completion">
                    </div>
                    <div class="image-actions">
                        <a href="../vendor/img/completions/<?php echo htmlspecialchars($completion_img); ?>" target="_blank" class="btn btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="../vendor/img/completions/<?php echo htmlspecialchars($completion_img); ?>" download class="btn btn-download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($bill_img)): ?>
                <div class="image-item">
                    <div class="image-label">
                        <i class="fas fa-file-invoice-dollar"></i> Service Bill
                    </div>
                    <div class="image-wrapper">
                        <img src="../vendor/img/bills/<?php echo htmlspecialchars($bill_img); ?>" alt="Service Bill">
                    </div>
                    <div class="image-actions">
                        <a href="../vendor/img/bills/<?php echo htmlspecialchars($bill_img); ?>" target="_blank" class="btn btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="../vendor/img/bills/<?php echo htmlspecialchars($bill_img); ?>" download class="btn btn-download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Booking Timeline -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-history"></i>
                <span>Booking Timeline</span>
            </div>
            
            <div class="info-row">
                <div class="info-label">Created At</div>
                <div class="info-value"><?php echo date('d M Y, h:i A', strtotime($booking->sb_created_at)); ?></div>
            </div>
            
            <?php if(!empty($booking->sb_updated_at)): ?>
            <div class="info-row">
                <div class="info-label">Last Updated</div>
                <div class="info-value"><?php echo date('d M Y, h:i A', strtotime($booking->sb_updated_at)); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($booking->sb_completed_date) && $display_status == 'Completed'): ?>
            <div class="info-row">
                <div class="info-label">Completed At</div>
                <div class="info-value"><?php echo date('d M Y, h:i A', strtotime($booking->sb_completed_date)); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="user-track-booking.php?booking_id=<?php echo $booking->sb_id; ?>" class="btn btn-track">
                <i class="fas fa-map-marker-alt"></i> Track Order
            </a>
            <a href="user-manage-booking.php" class="btn btn-orders">
                <i class="fas fa-list"></i> All Orders
            </a>
        </div>
    </div>

    <?php include('vendor/inc/user-footer.php'); ?>
</body>
</html>
