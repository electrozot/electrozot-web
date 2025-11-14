<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user details
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();

// Get all active services
$services_query = "SELECT * FROM tms_service WHERE s_status = 'Active' ORDER BY s_name";
$services_result = $mysqli->query($services_query);

// Ensure pincode column exists
try {
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_pincode VARCHAR(10) DEFAULT NULL");
} catch(Exception $e) {}

// Handle booking submission
if(isset($_POST['book_service'])) {
    $sb_service_id = $_POST['sb_service_id'];
    $sb_booking_date = $_POST['sb_booking_date'];
    $sb_booking_time = $_POST['sb_booking_time'];
    $sb_address = $_POST['sb_address'];
    $sb_pincode = $_POST['sb_pincode'];
    $sb_phone = $_POST['sb_phone'];
    $sb_description = isset($_POST['sb_description']) ? $_POST['sb_description'] : '';
    
    // Get service price
    $price_query = "SELECT s_price FROM tms_service WHERE s_id = ?";
    $price_stmt = $mysqli->prepare($price_query);
    $price_stmt->bind_param('i', $sb_service_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    $service = $price_result->fetch_object();
    
    $insert_query = "INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_pincode, sb_phone, sb_description, sb_status, sb_total_price) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param('iissssssd', $aid, $sb_service_id, $sb_booking_date, $sb_booking_time, $sb_address, $sb_pincode, $sb_phone, $sb_description, $service->s_price);
    
    if($insert_stmt->execute()) {
        $success = "Service booked successfully! We'll contact you soon.";
        $booking_id = $mysqli->insert_id;
    } else {
        $error = "Failed to book service. Please try again.";
    }
}

// Check if service is pre-selected
$selected_service = isset($_GET['service_id']) ? $_GET['service_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="user-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Book Service</li>
                </ol>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible shadow-lg" style="border-radius: 15px; border: none;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <h5><i class="fas fa-check-circle"></i> Booking Confirmed!</h5>
                        <p class="mb-2"><?php echo $success; ?></p>
                        <hr>
                        <a href="user-track-booking.php" class="btn btn-success">
                            <i class="fas fa-map-marker-alt"></i> Track Your Order
                        </a>
                        <a href="user-view-booking.php" class="btn btn-info">
                            <i class="fas fa-list"></i> View All Bookings
                        </a>
                        <a href="usr-book-service-simple.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Book Another Service
                        </a>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible shadow-lg" style="border-radius: 15px; border: none;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Service Selection -->
                <div class="card shadow-lg mb-4" style="border: none; border-radius: 15px;">
                    <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 15px 15px 0 0;">
                        <h5 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-wrench"></i> Select Service to Book
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <?php while($service = $services_result->fetch_object()): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <button type="button" 
                                            class="service-btn-green <?php echo ($selected_service == $service->s_id) ? 'selected' : ''; ?>" 
                                            onclick="selectService(<?php echo $service->s_id; ?>, '<?php echo addslashes($service->s_name); ?>', <?php echo $service->s_price; ?>)">
                                        <div class="service-btn-content">
                                            <div class="service-btn-icon">
                                                <i class="fas fa-tools"></i>
                                            </div>
                                            <div class="service-btn-text">
                                                <h6><?php echo $service->s_name; ?></h6>
                                                <p class="text-muted small mb-1"><?php echo substr($service->s_description, 0, 50) . '...'; ?></p>
                                                <p class="service-btn-price">৳<?php echo number_format($service->s_price, 0); ?></p>
                                                <small><i class="fas fa-clock"></i> <?php echo $service->s_duration; ?></small>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <!-- Booking Form (Hidden until service selected) -->
                <div id="bookingForm" style="display: <?php echo $selected_service ? 'block' : 'none'; ?>;">
                    <form method="POST">
                        <input type="hidden" name="sb_service_id" id="sb_service_id" value="<?php echo $selected_service; ?>">
                        
                        <div class="card shadow-lg mb-4" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                                <h5 class="m-0 font-weight-bold">
                                    <i class="fas fa-calendar-alt"></i> Booking Details
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-calendar"></i> Booking Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control form-control-lg" 
                                                   name="sb_booking_date" 
                                                   required 
                                                   min="<?php echo date('Y-m-d'); ?>"
                                                   style="border-radius: 10px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-clock"></i> Booking Time <span class="text-danger">*</span>
                                            </label>
                                            <input type="time" 
                                                   class="form-control form-control-lg" 
                                                   name="sb_booking_time" 
                                                   required
                                                   style="border-radius: 10px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-map-marker-alt"></i> Service Address <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control form-control-lg" 
                                                      name="sb_address" 
                                                      rows="3" 
                                                      required
                                                      placeholder="Enter your complete address"
                                                      style="border-radius: 10px;"><?php echo $user->u_addr; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-map-pin"></i> Pincode <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control form-control-lg" 
                                                   name="sb_pincode" 
                                                   required 
                                                   pattern="[0-9]{6}"
                                                   maxlength="6"
                                                   placeholder="Enter 6-digit pincode"
                                                   style="border-radius: 10px;">
                                            <small class="form-text text-muted">Enter 6-digit area pincode</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-phone"></i> Contact Phone <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel" 
                                                   class="form-control form-control-lg" 
                                                   name="sb_phone" 
                                                   required 
                                                   pattern="[0-9]{10}"
                                                   maxlength="10"
                                                   value="<?php echo $user->u_phone; ?>"
                                                   placeholder="10-digit mobile number"
                                                   style="border-radius: 10px;">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-comment"></i> Additional Notes (Optional)
                                            </label>
                                            <textarea class="form-control" 
                                                      name="sb_description" 
                                                      rows="4"
                                                      placeholder="Any specific requirements or issues you want to mention..."
                                                      style="border-radius: 10px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="text-center">
                                    <button type="submit" name="book_service" class="btn btn-success btn-lg px-5" style="border-radius: 50px; font-size: 1.2rem;">
                                        <i class="fas fa-check-circle"></i> Confirm Booking
                                    </button>
                                    <button type="button" onclick="cancelBooking()" class="btn btn-secondary btn-lg px-5 ml-2" style="border-radius: 50px;">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <style>
        /* Category Button Styles */
        .category-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 30px 20px;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            text-align: center;
        }
        
        .category-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }
        
        .category-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        
        /* Subcategory Button Styles */
        .subcategory-btn {
            width: 100%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 12px;
            padding: 20px;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(240, 147, 251, 0.3);
            text-align: left;
        }
        
        .subcategory-btn:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(240, 147, 251, 0.5);
        }
        
        /* Service Button Styles */
        .service-btn-green {
            width: 100%;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 10px;
            color: #495057;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(168, 237, 234, 0.3);
        }
        
        .service-btn-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(168, 237, 234, 0.5);
            border-color: #17a2b8;
            background: linear-gradient(135deg, #90e5e0 0%, #ffc8db 100%);
            color: #343a40;
        }
        
        .service-btn-green.selected {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border-color: #28a745;
            box-shadow: 0 4px 20px rgba(67, 233, 123, 0.5);
            transform: scale(1.02);
        }
        
        .service-btn-content {
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }
        
        .service-btn-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .service-btn-text h6 {
            margin: 0 0 2px 0;
            font-size: 14px;
            font-weight: 700;
        }
        
        .service-btn-text p {
            margin: 0;
            font-size: 11px;
            opacity: 0.95;
        }
        
        .service-btn-price {
            font-size: 16px !important;
            font-weight: 900 !important;
            margin: 2px 0 !important;
        }
        
        .service-btn-text small {
            font-size: 10px;
            opacity: 0.85;
        }
        
        @media (max-width: 768px) {
            .service-btn-content {
                flex-direction: column;
                text-align: center;
            }
            
            .service-btn-icon {
                width: 38px;
                height: 38px;
                font-size: 18px;
            }
            
            .category-icon {
                width: 60px;
                height: 60px;
            }
        }
    </style>

    <script>
        // Select service
        function selectService(id, name, price) {
            // Remove selection from all buttons
            document.querySelectorAll('.service-btn-green').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            // Add selection to clicked button
            event.currentTarget.classList.add('selected');
            
            // Set service ID
            document.getElementById('sb_service_id').value = id;
            
            // Show booking form
            document.getElementById('bookingForm').style.display = 'block';
            
            // Scroll to form
            document.getElementById('bookingForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Cancel booking
        function cancelBooking() {
            // Hide form
            document.getElementById('bookingForm').style.display = 'none';
            
            // Remove all selections
            document.querySelectorAll('.service-btn-green').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Auto-select if service_id in URL
        <?php if($selected_service): ?>
            window.onload = function() {
                const btn = document.querySelector('.service-btn-green.selected');
                if(btn) {
                    btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            };
        <?php endif; ?>
    </script>
</body>
</html>
