<?php
session_start();
include('admin/vendor/inc/config.php');

if(isset($_POST['book_service_guest'])) {
    $customer_name = $_POST['customer_name'];
    // Email is optional for guest booking
    $customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
    // Normalize and validate phone: digits only, exactly 10
    $customer_phone = preg_replace('/\D/', '', $_POST['customer_phone']);
    if (strlen($customer_phone) !== 10) {
        $_SESSION['booking_error'] = "Please enter a valid 10-digit phone number.";
        header("location: index.php#booking-form");
        exit();
    }
    // Pincode from form (6 digits)
    $customer_pincode = isset($_POST['customer_pincode']) ? preg_replace('/\D/', '', $_POST['customer_pincode']) : '';
    if (strlen($customer_pincode) !== 6) {
        $_SESSION['booking_error'] = "Please enter a valid 6-digit pincode.";
        header("location: index.php#booking-form");
        exit();
    }
    $customer_area = isset($_POST['customer_area']) ? trim($_POST['customer_area']) : '';
    
    // Validate service selection
    $sb_service_id_raw = isset($_POST['sb_service_id']) ? $_POST['sb_service_id'] : '';
    $is_other_service = ($sb_service_id_raw === 'other');
    $other_service_name = '';
    
    if($is_other_service) {
        // Handle "Other" service
        $other_service_name = isset($_POST['other_service_name']) ? trim($_POST['other_service_name']) : '';
        if(empty($other_service_name)) {
            $_SESSION['booking_error'] = "Please specify the service you need.";
            header("location: index.php#booking-form");
            exit();
        }
        // Set service ID to NULL for other services (to avoid foreign key constraint)
        $sb_service_id = null;
    } else {
        $sb_service_id = intval($sb_service_id_raw);
        if($sb_service_id <= 0) {
            $_SESSION['booking_error'] = "Please select a service.";
            header("location: index.php#booking-form");
            exit();
        }
    }
    
    // Validate required fields
    if(empty(trim($customer_name))) {
        $_SESSION['booking_error'] = "Please enter your name.";
        header("location: index.php#booking-form");
        exit();
    }
    
    if(empty($customer_area)) {
        $_SESSION['booking_error'] = "Please enter your area/locality.";
        header("location: index.php#booking-form");
        exit();
    }
    
    $sb_address = isset($_POST['sb_address']) ? trim($_POST['sb_address']) : '';
    if(empty($sb_address)) {
        $_SESSION['booking_error'] = "Please enter service address.";
        header("location: index.php#booking-form");
        exit();
    }
    
    // Automatically set booking date and time to current timestamp
    $sb_booking_date = date('Y-m-d');
    $sb_booking_time = date('H:i:s');
    $sb_description = isset($_POST['sb_description']) ? trim($_POST['sb_description']) : '';
    $sb_status = 'Pending'; // Default status

    // Split full name into first and last name
    $name_parts = explode(' ', trim($customer_name), 2);
    $u_fname = $name_parts[0];
    $u_lname = isset($name_parts[1]) ? $name_parts[1] : '';

    // Get service price and validate service exists
    if($is_other_service) {
        // For "Other" service, set price to 0 (to be determined by admin)
        $sb_total_price = 0;
        // Append the custom service name to description
        $sb_description = "CUSTOM SERVICE: " . $other_service_name . "\n\n" . $sb_description;
    } else {
        $query_price = "SELECT s_price, s_status FROM tms_service WHERE s_id = ?";
        $stmt_price = $mysqli->prepare($query_price);
        $stmt_price->bind_param('i', $sb_service_id);
        $stmt_price->execute();
        $result = $stmt_price->get_result();
        $service = $result->fetch_object();
        
        if(!$service) {
            $_SESSION['booking_error'] = "Selected service does not exist.";
            $stmt_price->close();
            header("location: index.php#booking-form");
            exit();
        }
        
        if($service->s_status != 'Active') {
            $_SESSION['booking_error'] = "Selected service is not available.";
            $stmt_price->close();
            header("location: index.php#booking-form");
            exit();
        }
        
        $sb_total_price = $service->s_price;
        $stmt_price->close();
    }

    // Ensure registration_type, u_area and u_pincode columns exist
    $mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS registration_type ENUM('admin', 'self', 'guest') DEFAULT 'admin'");
    $mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS u_area VARCHAR(100)");
    $mysqli->query("ALTER TABLE tms_user ADD COLUMN IF NOT EXISTS u_pincode VARCHAR(10)");
    
    // Check if customer already exists by phone number
    $check_user = "SELECT u_id FROM tms_user WHERE u_phone = ?";
    $stmt_check = $mysqli->prepare($check_user);
    $stmt_check->bind_param('s', $customer_phone);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if($result_check->num_rows > 0) {
        // Customer exists - use existing user ID
        $existing_user = $result_check->fetch_object();
        $customer_id = $existing_user->u_id;
        $stmt_check->close();
    } else {
        // New customer - insert into tms_user table as guest user with area and pincode
        $query_user = "INSERT INTO tms_user (u_fname, u_lname, u_email, u_phone, u_addr, u_area, u_pincode, u_category, u_pwd, registration_type) VALUES (?, ?, ?, ?, ?, ?, ?, 'Guest', '', 'guest')";
        $stmt_user = $mysqli->prepare($query_user);
        $stmt_user->bind_param('sssssss', $u_fname, $u_lname, $customer_email, $customer_phone, $sb_address, $customer_area, $customer_pincode);
        
        if(!$stmt_user->execute()) {
            $_SESSION['booking_error'] = "Failed to create customer profile. Please try again.";
            $stmt_user->close();
            $stmt_check->close();
            header("location: index.php#booking-form");
            exit();
        }
        
        $customer_id = $stmt_user->insert_id;
        $stmt_user->close();
        $stmt_check->close();
    }

    if($customer_id) {
        // Check active bookings limit (3 bookings per phone number)
        // Active bookings are those that are NOT 'Rejected', 'Cancelled', or 'Completed'
        $check_active_bookings = "SELECT COUNT(*) as active_count FROM tms_service_booking 
                                   WHERE sb_phone = ? 
                                   AND sb_status NOT IN ('Rejected', 'Cancelled', 'Completed')";
        $stmt_check_limit = $mysqli->prepare($check_active_bookings);
        $stmt_check_limit->bind_param('s', $customer_phone);
        $stmt_check_limit->execute();
        $result_limit = $stmt_check_limit->get_result();
        $limit_data = $result_limit->fetch_object();
        $active_bookings_count = $limit_data->active_count;
        $stmt_check_limit->close();
        
        // If customer already has 3 or more active bookings, reject the new booking
        if($active_bookings_count >= 3) {
            $_SESSION['booking_error'] = "You have reached the maximum limit of 3 active bookings. Please wait for one of your bookings to be completed, cancelled, or rejected before making a new booking.";
            header("location: index.php#booking-form");
            exit();
        }
        
        // Ensure sb_pincode and sb_custom_service columns exist, and sb_service_id allows NULL
        $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_pincode VARCHAR(10) DEFAULT NULL");
        $mysqli->query("ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_custom_service VARCHAR(255) DEFAULT NULL");
        $mysqli->query("ALTER TABLE tms_service_booking MODIFY COLUMN sb_service_id INT NULL");
        
        // Insert booking into tms_service_booking table with pincode and custom service
        if($is_other_service) {
            // For custom service, use NULL for service_id
            $query_booking = "INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_pincode, sb_phone, sb_description, sb_status, sb_total_price, sb_custom_service) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_booking = $mysqli->prepare($query_booking);
            // Parameters: user_id(i), date(s), time(s), address(s), pincode(s), phone(s), description(s), status(s), price(d), custom_service(s)
            // Type string: i s s s s s s s d s = 10 parameters
            $stmt_booking->bind_param('isssssssds', $customer_id, $sb_booking_date, $sb_booking_time, $sb_address, $customer_pincode, $customer_phone, $sb_description, $sb_status, $sb_total_price, $other_service_name);
        } else {
            // For regular service
            $query_booking = "INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_pincode, sb_phone, sb_description, sb_status, sb_total_price, sb_custom_service) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
            $stmt_booking = $mysqli->prepare($query_booking);
            // user_id(i), service_id(i), date(s), time(s), address(s), pincode(s), phone(s), description(s), status(s), price(d)
            $stmt_booking->bind_param('iisssssssd', $customer_id, $sb_service_id, $sb_booking_date, $sb_booking_time, $sb_address, $customer_pincode, $customer_phone, $sb_description, $sb_status, $sb_total_price);
        }
        
        if($stmt_booking->execute()) {
            $_SESSION['booking_success'] = "Booking submitted successfully! We will contact you shortly.";
        } else {
            $_SESSION['booking_error'] = "Booking failed, please try again.";
        }
        $stmt_booking->close();
    } else {
        $_SESSION['booking_error'] = "Failed to create customer profile.";
    }

    header("location: index.php#booking-form");
    exit();
}
?>