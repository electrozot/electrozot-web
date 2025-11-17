<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle form submission
if(isset($_POST['create_booking'])) {
    // Validate and sanitize inputs
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $customer_phone = isset($_POST['customer_phone']) ? preg_replace('/\D/', '', $_POST['customer_phone']) : '';
    $customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
    $customer_address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : '';
    $customer_area = isset($_POST['customer_area']) ? trim($_POST['customer_area']) : '';
    $customer_pincode = isset($_POST['customer_pincode']) ? preg_replace('/\D/', '', $_POST['customer_pincode']) : '';
    $service_id_raw = isset($_POST['service_id']) ? $_POST['service_id'] : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    // Handle "Other" service
    $is_other_service = ($service_id_raw === 'other');
    $other_service_name = '';
    
    if($is_other_service) {
        $other_service_name = isset($_POST['other_service_name']) ? trim($_POST['other_service_name']) : '';
        if(empty($other_service_name)) {
            $err = "Please specify the service you need.";
        }
        $service_id = null; // Set to NULL for other services (to avoid foreign key constraint)
    } else {
        $service_id = intval($service_id_raw);
    }
    
    // Validation checks
    if(empty($customer_name)) {
        $err = "Customer name is required.";
    } elseif(strlen($customer_phone) !== 10) {
        $err = "Please enter a valid 10-digit phone number.";
    } elseif(strlen($customer_pincode) !== 6) {
        $err = "Please enter a valid 6-digit pincode.";
    } elseif(empty($customer_area)) {
        $err = "Area/locality is required.";
    } elseif(empty($customer_address)) {
        $err = "Service address is required.";
    } elseif(!$is_other_service && $service_id <= 0) {
        $err = "Please select a service.";
    } elseif($is_other_service && empty($other_service_name)) {
        $err = "Please specify the custom service you need.";
    } else {
        // Automatically set booking date and time to current timestamp
        $booking_date = date('Y-m-d');
        $booking_time = date('H:i:s');
    
    // Check if user exists by phone
    $check_user = "SELECT u_id FROM tms_user WHERE u_phone = ?";
    $stmt_check = $mysqli->prepare($check_user);
    $stmt_check->bind_param('s', $customer_phone);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if($result->num_rows > 0) {
        // User exists
        $user = $result->fetch_object();
        $user_id = $user->u_id;
    } else {
        // Create new user with area and pincode
        $password = password_hash('electrozot123', PASSWORD_DEFAULT);
        $insert_user = "INSERT INTO tms_user (u_fname, u_phone, u_email, u_addr, u_area, u_pincode, u_pwd, u_category, registration_type) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'User', 'admin')";
        $stmt_user = $mysqli->prepare($insert_user);
        $stmt_user->bind_param('sssssss', $customer_name, $customer_phone, $customer_email, $customer_address, $customer_area, $customer_pincode, $password);
        $stmt_user->execute();
        $user_id = $mysqli->insert_id;
    }
    
    // Get service price or set to 0 for custom service
    if($is_other_service) {
        $total_price = 0;
        // Prepend custom service name to notes
        $notes = "CUSTOM SERVICE: " . $other_service_name . "\n\n" . $notes;
    } else {
        $price_query = "SELECT s_price FROM tms_service WHERE s_id = ?";
        $stmt_price = $mysqli->prepare($price_query);
        $stmt_price->bind_param('i', $service_id);
        $stmt_price->execute();
        $price_result = $stmt_price->get_result();
        $service = $price_result->fetch_object();
        $total_price = $service->s_price;
    }
    
    // Ensure sb_custom_service column exists and sb_service_id allows NULL
    $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_custom_service VARCHAR(255) DEFAULT NULL");
    $mysqli->query("ALTER TABLE tms_service_booking MODIFY COLUMN sb_service_id INT NULL");
    
    // Create booking with pincode and custom service
    if($is_other_service) {
        // For custom service, use NULL for service_id
        $insert_booking = "INSERT INTO tms_service_booking 
                          (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_phone, sb_address, sb_pincode, sb_description, sb_status, sb_total_price, sb_custom_service) 
                          VALUES (?, NULL, ?, ?, ?, ?, ?, ?, 'Pending', ?, ?)";
        $stmt_booking = $mysqli->prepare($insert_booking);
        // Parameters: user_id(i), date(s), time(s), phone(s), address(s), pincode(s), description(s), price(d), custom_service(s)
        // Type string: i s s s s s s d s = 9 parameters
        $stmt_booking->bind_param('issssssds', $user_id, $booking_date, $booking_time, $customer_phone, $customer_address, $customer_pincode, $notes, $total_price, $other_service_name);
    } else {
        // For regular service
        $insert_booking = "INSERT INTO tms_service_booking 
                          (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_phone, sb_address, sb_pincode, sb_description, sb_status, sb_total_price, sb_custom_service) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, NULL)";
        $stmt_booking = $mysqli->prepare($insert_booking);
        // Parameters: user_id(i), service_id(i), date(s), time(s), phone(s), address(s), pincode(s), description(s), price(d)
        // Type string: i i s s s s s s d = 9 parameters
        $stmt_booking->bind_param('iisssssssd', $user_id, $service_id, $booking_date, $booking_time, $customer_phone, $customer_address, $customer_pincode, $notes, $total_price);
    }
    
        if($stmt_booking->execute()) {
            $success = "Booking created successfully! Booking ID: " . $mysqli->insert_id;
        } else {
            $err = "Failed to create booking. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Quick Booking - Admin</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
</head>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Quick Booking</li>
                </ol>
                
                <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>
                
                <?php if(isset($err)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>
                
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-phone-alt"></i> Quick Booking - For Phone Orders
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary"><i class="fas fa-user"></i> Customer Information</h5>
                                    <hr>
                                    
                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="customer_phone" id="customer_phone" class="form-control" required maxlength="10" pattern="[0-9]{10}" title="Enter exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" placeholder="10-digit mobile number">
                                        <small class="form-text text-muted">Enter phone to auto-fill registered customer details</small>
                                        <div id="customerStatus" class="mt-2"></div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_name" id="customer_name" class="form-control" required placeholder="Will auto-fill if registered">
                                    </div>
                                    
                                    <input type="hidden" name="customer_email" id="customer_email" value="">
                                    
                                    <div class="form-group">
                                        <label>Service Address <span class="text-danger">*</span></label>
                                        <textarea name="customer_address" id="customer_address" class="form-control" rows="2" required placeholder="Enter service location address"></textarea>
                                        <small class="form-text text-info" id="addressHint" style="display:none;">
                                            <i class="fas fa-info-circle"></i> Registered address shown - You can change if service needed at different location
                                        </small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Area <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_area" id="customer_area" class="form-control" required placeholder="Service area">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pincode <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_pincode" id="customer_pincode" class="form-control" required maxlength="6" pattern="[0-9]{6}" placeholder="6-digit pincode" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)">
                                                <small class="form-text text-info" id="pincodeHint" style="display:none;">
                                                    <i class="fas fa-info-circle"></i> Confirm or change pincode for service location
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5 class="text-primary"><i class="fas fa-calendar-check"></i> Booking Details</h5>
                                    <hr>
                                    
                                    <div class="form-group">
                                        <label>Service Type <span class="text-danger">*</span></label>
                                        <select name="service_subcategory" id="quickBookSubcategory" class="form-control" required>
                                            <option value="">-- Select Service Type --</option>
                                            <option value="Wiring & Fixtures">Wiring & Fixtures</option>
                                            <option value="Safety & Power">Safety & Power</option>
                                            <option value="Major Appliances">Major Appliances</option>
                                            <option value="Small Gadgets">Small Gadgets</option>
                                            <option value="Appliance Setup">Appliance Setup</option>
                                            <option value="Tech & Security">Tech & Security</option>
                                            <option value="Routine Care">Routine Care</option>
                                            <option value="Fixtures & Taps">Fixtures & Taps</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Service <span class="text-danger">*</span></label>
                                        <select name="service_id" id="quickBookService" class="form-control" required disabled>
                                            <option value="">-- Select Service Type First --</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" id="quickOtherServiceDiv" style="display: none;">
                                        <label><i class="fas fa-edit text-warning"></i> Specify Your Service <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="other_service_name" id="quickOtherServiceInput" placeholder="Enter the service you need" style="border: 2px solid #ffc107; background-color: #fffbf0;">
                                        <small class="text-info"><i class="fas fa-info-circle"></i> Please describe the service you need</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Notes / Special Instructions</label>
                                        <textarea name="notes" class="form-control" rows="5" 
                                                  placeholder="Any special requirements or notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="text-center">
                                <button type="submit" name="create_booking" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle"></i> Create Booking
                                </button>
                                <a href="admin-dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
            
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Handle subcategory change - load services directly
        $('#quickBookSubcategory').on('change', function() {
            var subcategory = $(this).val();
            var serviceSelect = $('#quickBookService');
            
            serviceSelect.html('<option value="">-- Loading... --</option>').prop('disabled', true);
            $('#servicePriceDisplay').text('');
            
            // Hide other service input when category changes
            $('#quickOtherServiceDiv').hide();
            $('#quickOtherServiceInput').removeAttr('required').val('');
            
            if(subcategory) {
                $.ajax({
                    url: 'get-services-by-subcategory.php',
                    method: 'POST',
                    data: {subcategory: subcategory},
                    dataType: 'json',
                    success: function(response) {
                        if(response.success && response.services.length > 0) {
                            serviceSelect.html('<option value="">-- Select Service --</option>');
                            $.each(response.services, function(index, service) {
                                var displayName = service.gadget_name || service.name;
                                serviceSelect.append('<option value="' + service.id + '">' + displayName + '</option>');
                            });
                            serviceSelect.prop('disabled', false);
                        } else {
                            serviceSelect.html('<option value="">No services available</option>');
                        }
                    },
                    error: function() {
                        serviceSelect.html('<option value="">Error loading services</option>');
                    }
                });
            }
        });
        
        // Handle service selection - show/hide "Other" input
        $('#quickBookService').on('change', function() {
            var selectedValue = $(this).val();
            
            if(selectedValue === 'other') {
                // Show the custom service input
                $('#quickOtherServiceDiv').slideDown(300);
                $('#quickOtherServiceInput').attr('required', 'required').focus();
            } else {
                // Hide the custom service input
                $('#quickOtherServiceDiv').slideUp(300);
                $('#quickOtherServiceInput').removeAttr('required').val('');
            }
        });

        
        // Auto-fill customer details when phone number is entered
        $('#customer_phone').on('blur', function() {
            var phone = $(this).val();
            
            if(phone.length === 10) {
                // Show loading
                $('#customerStatus').html('<span class="badge badge-info"><i class="fas fa-spinner fa-spin"></i> Checking...</span>');
                
                // AJAX request to check if customer exists
                $.ajax({
                    url: 'vendor/inc/check-customer.php',
                    method: 'POST',
                    data: {phone: phone},
                    dataType: 'json',
                    success: function(response) {
                        if(response.exists) {
                            // Customer found - auto-fill name only, suggest address/pincode
                            $('#customerStatus').html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Registered Customer Found!</span>');
                            
                            // Auto-fill name (readonly)
                            $('#customer_name').val(response.user.u_fname + ' ' + response.user.u_lname);
                            $('#customer_name').prop('readonly', true);
                            
                            // Store email in hidden field (don't show to admin)
                            $('#customer_email').val(response.user.u_email);
                            
                            // Suggest address but keep editable
                            if(response.user.u_addr) {
                                $('#customer_address').val(response.user.u_addr);
                                $('#addressHint').show();
                            }
                            
                            // Suggest area and pincode but keep editable
                            if(response.user.u_area) {
                                $('#customer_area').val(response.user.u_area);
                            }
                            if(response.user.u_pincode) {
                                $('#customer_pincode').val(response.user.u_pincode);
                                $('#pincodeHint').show();
                            }
                            
                            // Focus on address field for admin to confirm/change
                            $('#customer_address').focus().select();
                        } else {
                            // New customer
                            $('#customerStatus').html('<span class="badge badge-warning"><i class="fas fa-user-plus"></i> New Customer - Fill Details Below</span>');
                            
                            // Clear all fields and make name editable
                            $('#customer_name').val('').prop('readonly', false);
                            $('#customer_email').val('');
                            $('#customer_address').val('');
                            $('#customer_area').val('');
                            $('#customer_pincode').val('');
                            $('#addressHint').hide();
                            $('#pincodeHint').hide();
                        }
                    },
                    error: function() {
                        $('#customerStatus').html('<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Error checking customer</span>');
                    }
                });
            } else if(phone.length > 0) {
                $('#customerStatus').html('<span class="badge badge-danger">Phone must be exactly 10 digits</span>');
            } else {
                $('#customerStatus').html('');
            }
        });
    });
    </script>
</body>
</html>
