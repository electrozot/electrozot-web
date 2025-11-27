<?php
/**
 * Edit Custom Booking - Add Detailed Service Name
 * Allows admin to specify detailed service information for custom/other bookings
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
include('includes/smart-technician-matcher.php');
check_login();

$aid = $_SESSION['a_id'];

// Handle AJAX subcategory update from assign technician page
if(isset($_POST['ajax_update']) && isset($_POST['sb_id']) && isset($_POST['sb_subcategory'])) {
    $sb_id = intval($_POST['sb_id']);
    $sb_subcategory = trim($_POST['sb_subcategory']);
    
    $update_query = "UPDATE tms_service_booking SET sb_subcategory = ? WHERE sb_id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param('si', $sb_subcategory, $sb_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Subcategory updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
    exit;
}

$sb_id = isset($_GET['sb_id']) ? intval($_GET['sb_id']) : 0;

if($sb_id <= 0) {
    $_SESSION['error'] = "Invalid booking ID";
    header('Location: admin-all-bookings.php');
    exit;
}

// Handle form submission
if(isset($_POST['update_custom_booking'])) {
    $detailed_service_name = trim($_POST['detailed_service_name']);
    $sb_description = trim($_POST['sb_description']);
    $sb_subcategory = isset($_POST['sb_subcategory']) ? trim($_POST['sb_subcategory']) : '';
    
    if(empty($detailed_service_name)) {
        $err = "Please provide a detailed service name";
    } elseif(empty($sb_subcategory)) {
        $err = "Please select a service subcategory";
    } else {
        // Initialize smart matcher
        $matcher = new SmartTechnicianMatcher($mysqli);
        
        // Extract keywords from description
        $combined_text = $detailed_service_name . ' ' . $sb_description;
        $keywords = $matcher->extractKeywords($combined_text);
        $suggested_category = $matcher->suggestCategory($keywords);
        $keywords_str = implode(', ', $keywords);
        
        // Update booking
        $update_query = "UPDATE tms_service_booking 
                        SET sb_detailed_service_name = ?,
                            sb_description = ?,
                            sb_subcategory = ?,
                            sb_extracted_keywords = ?,
                            sb_suggested_category = ?,
                            sb_total_price = 0
                        WHERE sb_id = ?";
        
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param('sssssi', $detailed_service_name, $sb_description, $sb_subcategory, $keywords_str, $suggested_category, $sb_id);
        
        if($stmt->execute()) {
            $_SESSION['success'] = "Custom booking updated successfully! Subcategory: " . $sb_subcategory . " | Keywords: " . $keywords_str;
            header('Location: admin-assign-technician.php?sb_id=' . $sb_id);
            exit;
        } else {
            $err = "Failed to update booking: " . $mysqli->error;
        }
    }
}

// Get booking details
$query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          WHERE sb.sb_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $sb_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if(!$booking) {
    $_SESSION['error'] = "Booking not found";
    header('Location: admin-all-bookings.php');
    exit;
}

// Check if this is a custom booking
$is_custom = (empty($booking->sb_service_id) || !empty($booking->sb_custom_service));

if(!$is_custom) {
    $_SESSION['error'] = "This is not a custom booking";
    header('Location: admin-assign-technician.php?sb_id=' . $sb_id);
    exit;
}
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
                <?php if(isset($err)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin-all-bookings.php">Bookings</a></li>
                    <li class="breadcrumb-item active">Edit Custom Booking</li>
                </ol>
                
                <div class="card shadow-lg" style="border: none; border-radius: 15px;">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Custom Booking #<?php echo $sb_id; ?></h5>
                    </div>
                    <div class="card-body" style="padding: 30px;">
                        <!-- Customer Info -->
                        <div class="alert alert-info" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-left: 5px solid #667eea;">
                            <h6 style="color: #667eea; font-weight: 700;"><i class="fas fa-user"></i> Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($booking->u_fname . ' ' . $booking->u_lname); ?></p>
                            <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($booking->u_phone); ?></p>
                            <p class="mb-0"><strong>Address:</strong> <?php echo htmlspecialchars($booking->sb_address); ?></p>
                        </div>
                        
                        <!-- Current Custom Service -->
                        <div class="alert alert-warning" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 152, 0, 0.1) 100%); border-left: 5px solid #ffc107;">
                            <h6 style="color: #f57c00; font-weight: 700;"><i class="fas fa-tools"></i> Current Custom Service Request</h6>
                            <p class="mb-1"><strong>Service:</strong> <?php echo htmlspecialchars($booking->sb_custom_service ?? 'Other Service'); ?></p>
                            <?php if(!empty($booking->sb_description)): ?>
                            <p class="mb-0"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($booking->sb_description)); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Edit Form -->
                        <form method="POST" id="customBookingForm">
                            <!-- Subcategory Selection with Buttons -->
                            <div class="form-group">
                                <label style="font-weight: 700; color: #2d3748; font-size: 1.1rem; margin-bottom: 15px;">
                                    <i class="fas fa-th-large"></i> Service Subcategory <span class="text-danger">*</span>
                                </label>
                                
                                <?php if(!empty($booking->sb_subcategory)): ?>
                                <div class="alert alert-success" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%); border-left: 5px solid #10b981;">
                                    <i class="fas fa-check-circle"></i> <strong>Auto-detected from booking:</strong> <?php echo htmlspecialchars($booking->sb_subcategory); ?>
                                    <br><small>You can change it below if needed</small>
                                </div>
                                <?php endif; ?>
                                
                                <input type="hidden" name="sb_subcategory" id="selectedSubcategory" value="<?php echo htmlspecialchars($booking->sb_subcategory ?? ''); ?>" required>
                                
                                <!-- Subcategory Buttons Grid -->
                                <div class="row">
                                    <!-- ELECTRICAL -->
                                    <div class="col-md-6 mb-3">
                                        <div class="category-group" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border: 2px solid #e2e8f0;">
                                            <h6 style="color: #667eea; font-weight: 700; margin-bottom: 10px;">⚡ ELECTRICAL</h6>
                                            <button type="button" class="subcategory-btn" data-value="Wiring & Fixtures" style="width: 100%; margin-bottom: 8px;">
                                                <i class="fas fa-plug"></i> Wiring & Fixtures
                                            </button>
                                            <button type="button" class="subcategory-btn" data-value="Safety & Power" style="width: 100%;">
                                                <i class="fas fa-shield-alt"></i> Safety & Power
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- REPAIR -->
                                    <div class="col-md-6 mb-3">
                                        <div class="category-group" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border: 2px solid #e2e8f0;">
                                            <h6 style="color: #ec4899; font-weight: 700; margin-bottom: 10px;">🔧 REPAIR</h6>
                                            <button type="button" class="subcategory-btn" data-value="Major Appliances" style="width: 100%; margin-bottom: 8px;">
                                                <i class="fas fa-tv"></i> Major Appliances
                                            </button>
                                            <button type="button" class="subcategory-btn" data-value="Other Gadgets" style="width: 100%;">
                                                <i class="fas fa-mobile-alt"></i> Other Gadgets
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- INSTALL -->
                                    <div class="col-md-6 mb-3">
                                        <div class="category-group" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border: 2px solid #e2e8f0;">
                                            <h6 style="color: #10b981; font-weight: 700; margin-bottom: 10px;">🔌 INSTALL</h6>
                                            <button type="button" class="subcategory-btn" data-value="Appliance Setup" style="width: 100%; margin-bottom: 8px;">
                                                <i class="fas fa-tools"></i> Appliance Setup
                                            </button>
                                            <button type="button" class="subcategory-btn" data-value="Tech & Security" style="width: 100%;">
                                                <i class="fas fa-video"></i> Tech & Security
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- MAINTAIN & PLUMBING -->
                                    <div class="col-md-6 mb-3">
                                        <div class="category-group" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border: 2px solid #e2e8f0;">
                                            <h6 style="color: #f59e0b; font-weight: 700; margin-bottom: 10px;">🛠️ MAINTAIN</h6>
                                            <button type="button" class="subcategory-btn" data-value="Routine Care" style="width: 100%; margin-bottom: 15px;">
                                                <i class="fas fa-wrench"></i> Routine Care
                                            </button>
                                            
                                            <h6 style="color: #3b82f6; font-weight: 700; margin-bottom: 10px; margin-top: 15px;">💧 PLUMBING</h6>
                                            <button type="button" class="subcategory-btn" data-value="Fixtures & Taps" style="width: 100%;">
                                                <i class="fas fa-faucet"></i> Fixtures & Taps
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="subcategoryError" class="text-danger" style="display: none; margin-top: 10px;">
                                    <i class="fas fa-exclamation-circle"></i> Please select a subcategory
                                </div>
                            </div>
                            
                            <style>
                                .subcategory-btn {
                                    background: white;
                                    border: 2px solid #e2e8f0;
                                    padding: 12px 20px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    color: #4a5568;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    text-align: left;
                                }
                                
                                .subcategory-btn:hover {
                                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
                                    border-color: #667eea;
                                    transform: translateY(-2px);
                                    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
                                }
                                
                                .subcategory-btn.active {
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    border-color: #667eea;
                                    color: white;
                                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                                }
                                
                                .subcategory-btn i {
                                    margin-right: 8px;
                                }
                                
                                .category-group {
                                    transition: all 0.3s ease;
                                }
                                
                                .category-group:hover {
                                    border-color: #667eea;
                                    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
                                }
                            </style>
                            
                            <div class="form-group">
                                <label style="font-weight: 700; color: #2d3748; font-size: 1.1rem;">
                                    <i class="fas fa-tag"></i> Detailed Service Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="detailed_service_name" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($booking->sb_detailed_service_name ?? $booking->sb_custom_service ?? ''); ?>"
                                       placeholder="e.g., Ceiling Fan Regulator Repair and Wiring Check"
                                       required
                                       style="border-radius: 10px; padding: 15px; border: 2px solid #e2e8f0; font-size: 1rem;">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Provide a clear, detailed name for this service. This helps match with the right technician.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label style="font-weight: 700; color: #2d3748; font-size: 1.1rem;">
                                    <i class="fas fa-comment-alt"></i> Service Description
                                </label>
                                <textarea name="sb_description" 
                                          class="form-control" 
                                          rows="5"
                                          placeholder="Add any additional details about the work needed..."
                                          style="border-radius: 10px; padding: 15px; border: 2px solid #e2e8f0; font-size: 0.95rem;"><?php echo htmlspecialchars($booking->sb_description ?? ''); ?></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-lightbulb"></i> Include details like: parts needed, specific issues, location in house, etc.
                                </small>
                            </div>
                            
                            <!-- Smart Matching Info -->
                            <div class="alert" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%); border-left: 5px solid #10b981; border-radius: 10px;">
                                <h6 style="color: #047857; font-weight: 700;"><i class="fas fa-brain"></i> Smart Technician Matching</h6>
                                <p class="mb-0" style="color: #065f46;">
                                    Our system will analyze the service name and description to extract keywords (like "fan", "wiring", "repair") 
                                    and automatically suggest technicians with matching skills. This ensures the right person is assigned for the job!
                                </p>
                            </div>
                            
                            <!-- Current Analysis (if exists) -->
                            <?php if(!empty($booking->sb_extracted_keywords)): ?>
                            <div class="alert alert-secondary">
                                <h6><i class="fas fa-tags"></i> Current Keywords</h6>
                                <p class="mb-1"><strong>Extracted:</strong> <?php echo htmlspecialchars($booking->sb_extracted_keywords); ?></p>
                                <?php if(!empty($booking->sb_suggested_category)): ?>
                                <p class="mb-0"><strong>Suggested Category:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($booking->sb_suggested_category); ?></span></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="text-center mt-4">
                                <button type="submit" name="update_custom_booking" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 25px; padding: 12px 40px; font-weight: 700;">
                                    <i class="fas fa-save"></i> Update & Proceed to Assign Technician
                                </button>
                                <a href="admin-all-bookings.php" class="btn btn-lg btn-secondary" style="border-radius: 25px; padding: 12px 40px; font-weight: 700;">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Set initial active button if subcategory already selected
            var initialSubcategory = $('#selectedSubcategory').val();
            if(initialSubcategory) {
                $('.subcategory-btn[data-value="' + initialSubcategory + '"]').addClass('active');
            }
            
            // Handle subcategory button clicks
            $('.subcategory-btn').click(function() {
                // Remove active class from all buttons
                $('.subcategory-btn').removeClass('active');
                
                // Add active class to clicked button
                $(this).addClass('active');
                
                // Set hidden input value
                var selectedValue = $(this).data('value');
                $('#selectedSubcategory').val(selectedValue);
                
                // Hide error message
                $('#subcategoryError').hide();
                
                // Visual feedback
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                }, 100);
            });
            
            // Form validation
            $('#customBookingForm').submit(function(e) {
                var subcategory = $('#selectedSubcategory').val();
                if(!subcategory) {
                    e.preventDefault();
                    $('#subcategoryError').show();
                    $('html, body').animate({
                        scrollTop: $('#subcategoryError').offset().top - 100
                    }, 500);
                    return false;
                }
            });
        });
    </script>
</body>
</html>
