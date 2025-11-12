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
    $sb_service_id = $_POST['sb_service_id'];
    $sb_booking_date = $_POST['sb_booking_date'];
    // Time removed from form; default to 00:00 to keep DB insert stable
    $sb_booking_time = '00:00';
    $sb_address = $_POST['sb_address'];
    $sb_description = isset($_POST['sb_description']) ? $_POST['sb_description'] : '';
    $sb_status = 'Pending'; // Default status

    // Split full name into first and last name
    $name_parts = explode(' ', trim($customer_name), 2);
    $u_fname = $name_parts[0];
    $u_lname = isset($name_parts[1]) ? $name_parts[1] : '';

    // Get service price
    $query_price = "SELECT s_price FROM tms_service WHERE s_id = ?";
    $stmt_price = $mysqli->prepare($query_price);
    $stmt_price->bind_param('i', $sb_service_id);
    $stmt_price->execute();
    $result = $stmt_price->get_result();
    $service = $result->fetch_object();
    $sb_total_price = $service ? $service->s_price : 0.00;
    $stmt_price->close();

    // Insert customer into tms_user table
    $query_user = "INSERT INTO tms_user (u_fname, u_lname, u_email, u_phone, u_addr, u_category, u_pwd) VALUES (?, ?, ?, ?, ?, 'Guest', '')";
    $stmt_user = $mysqli->prepare($query_user);
    $stmt_user->bind_param('sssss', $u_fname, $u_lname, $customer_email, $customer_phone, $sb_address);
    $stmt_user->execute();
    $customer_id = $stmt_user->insert_id;
    $stmt_user->close();

    if($customer_id) {
        // Merge pincode into description to avoid schema changes
        $sb_description_full = "Pincode: " . $customer_pincode;
        if (!empty($sb_description)) {
            $sb_description_full .= "\n" . trim($sb_description);
        }

        // Insert booking into tms_service_booking table
        $query_booking = "INSERT INTO tms_service_booking (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, sb_address, sb_phone, sb_description, sb_status, sb_total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_booking = $mysqli->prepare($query_booking);
        $stmt_booking->bind_param('iissssssd', $customer_id, $sb_service_id, $sb_booking_date, $sb_booking_time, $sb_address, $customer_phone, $sb_description_full, $sb_status, $sb_total_price);
        
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