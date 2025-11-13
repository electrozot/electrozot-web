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

// Get all services
$services_query = "SELECT * FROM tms_service WHERE s_status = 'Active' ORDER BY s_category, s_name";
$services_result = $mysqli->query($services_query);

// Handle booking submission
if(isset($_POST['book_service'])) {
    $sb_service_id = $_POST['sb_service_id'];
    $sb_booking_date = $_POST['sb_booking_date'];
    $sb_booking_time = $_POST['sb_booking_time'];
    $sb_address = $_POST['sb_address'];
    $sb_phone = $_POST['sb_phone'];
    $sb_description = $_POST['sb_description'];
    
    // Get service price
    $price_query = "SELECT s_price FROM tms_service WHERE s_id = ?";
    $price_stmt = $mysqli->prepare($price_query);
    $price_stmt->bind_param('i', $sb_service_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    $service = $price_result->fetch_object();
    
    $insert_query = "INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_phone, sb_description, sb_status, sb_total_price) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param('iisssssd', $aid, $sb_service_id, $sb_booking_date, $sb_booking_time, $sb_address, $sb_phone, $sb_description, $service->s_price);
    
    if($insert_stmt->execute()) {
        $success = "Service booked successfully! We'll contact you soon.";
    } else {
        $error = "Failed to book service. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta name="description" content="Book Electrozot Services">
    <title>Book Service - Electrozot</title>
    
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 0;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .mobile-header {
            background: white;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .mobile-header h1 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            color: #667eea;
        }
        
        .mobile-container {
            padding: 15px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .service-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
            transform: scale(1.02);
        }
        
        .service-card:active {
            transform: scale(0.98);
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .service-name {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .service-category {
            font-size: 14px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .service-price {
            font-size: 24px;
            font-weight: 900;
            color: #28a745;
        }
        
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .form-section h3 {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .form-control {
            min-height: 50px;
            font-size: 16px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        textarea.form-control {
            min-height: 100px;
        }
        
        .btn-book {
            width: 100%;
            min-height: 56px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: sticky;
            bottom: 15px;
        }
        
        .btn-book:active {
            transform: scale(0.98);
        }
        
        .alert-mobile {
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .back-btn {
            color: #667eea;
            font-size: 24px;
            text-decoration: none;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            background: #e9ecef;
            margin: 0 5px;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
        }
        
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="mobile-header">
        <div class="d-flex align-items-center justify-content-between">
            <a href="user-dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <h1><i class="fas fa-bolt"></i> Book Service</h1>
            <a href="user-track-booking.php" style="color: #667eea; font-size: 20px;"><i class="fas fa-map-marker-alt"></i></a>
        </div>
    </div>

    <div class="mobile-container">
        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-mobile">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <div class="mt-2">
                    <a href="user-track-booking.php" class="btn btn-success btn-sm">Track Order</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-mobile">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="bookingForm">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1. Service</div>
                <div class="step" id="step2">2. Details</div>
                <div class="step" id="step3">3. Confirm</div>
            </div>

            <!-- Step 1: Select Service -->
            <div id="serviceSelection">
                <h3 style="color: white; font-weight: 800; margin-bottom: 15px;">
                    <i class="fas fa-wrench"></i> Select Service
                </h3>
                
                <?php while($service = $services_result->fetch_object()): ?>
                    <div class="service-card" onclick="selectService(<?php echo $service->s_id; ?>, '<?php echo addslashes($service->s_name); ?>', <?php echo $service->s_price; ?>)">
                        <div class="service-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="service-name"><?php echo $service->s_name; ?></div>
                        <div class="service-category">
                            <i class="fas fa-tag"></i> <?php echo $service->s_category; ?>
                        </div>
                        <div class="service-price">₹<?php echo number_format($service->s_price, 0); ?></div>
                        <small class="text-muted"><i class="fas fa-clock"></i> <?php echo $service->s_duration; ?></small>
                    </div>
                <?php endwhile; ?>
                
                <input type="hidden" name="sb_service_id" id="sb_service_id" required>
            </div>

            <!-- Step 2: Booking Details -->
            <div id="bookingDetails" style="display: none;">
                <div class="form-section">
                    <h3><i class="fas fa-calendar"></i> When?</h3>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="sb_booking_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" class="form-control" name="sb_booking_time" required>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Where?</h3>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="sb_address" required placeholder="Enter your complete address"><?php echo $user->u_addr; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" class="form-control" name="sb_phone" required value="<?php echo $user->u_phone; ?>" placeholder="Your contact number">
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-comment"></i> Additional Info</h3>
                    <div class="form-group">
                        <textarea class="form-control" name="sb_description" placeholder="Any specific requirements or issues? (Optional)"></textarea>
                    </div>
                </div>

                <button type="submit" name="book_service" class="btn btn-book">
                    <i class="fas fa-check-circle"></i> Confirm Booking
                </button>
            </div>
        </form>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let selectedServiceId = null;
        
        function selectService(id, name, price) {
            // Remove previous selection
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            event.currentTarget.classList.add('selected');
            
            // Set hidden input
            document.getElementById('sb_service_id').value = id;
            selectedServiceId = id;
            
            // Show next step after delay
            setTimeout(() => {
                document.getElementById('serviceSelection').style.display = 'none';
                document.getElementById('bookingDetails').style.display = 'block';
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step1').classList.add('completed');
                document.getElementById('step2').classList.add('active');
                
                // Scroll to top
                window.scrollTo({top: 0, behavior: 'smooth'});
            }, 300);
        }
        
        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if(!selectedServiceId) {
                e.preventDefault();
                alert('Please select a service first');
                return false;
            }
            
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step2').classList.add('completed');
            document.getElementById('step3').classList.add('active');
        });
    </script>
</body>
</html>
