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
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_pic VARCHAR(200) DEFAULT ''");
} catch(Exception $e) {}

// Get technician details
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt = $mysqli->prepare($tech_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$result = $stmt->get_result();
$tech = $result->fetch_object();

$t_phone = isset($tech->t_phone) ? $tech->t_phone : '';
$t_email = isset($tech->t_email) ? $tech->t_email : '';
$t_addr = isset($tech->t_addr) ? $tech->t_addr : '';
$t_pic = isset($tech->t_pic) ? $tech->t_pic : '';
$t_pincode = '';

if(!empty($t_addr)) {
    preg_match('/\b\d{6}\b/', $t_addr, $matches);
    if(!empty($matches)) {
        $t_pincode = $matches[0];
    }
}

// Get this month's data
$current_month = date('Y-m');
$month_start = $current_month . '-01';
$month_end = date('Y-m-t');

// Total orders this month
$orders_query = "SELECT COUNT(*) as total_orders FROM tms_service_booking 
                 WHERE sb_technician_id = ? 
                 AND sb_booking_date BETWEEN ? AND ?";
$stmt_orders = $mysqli->prepare($orders_query);
$stmt_orders->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
$orders_data = $orders_result->fetch_object();
$total_orders = $orders_data->total_orders;

// Total earnings this month (completed bookings)
$earnings_query = "SELECT SUM(s.s_price) as total_earning 
                   FROM tms_service_booking sb
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_technician_id = ? 
                   AND sb.sb_status = 'Completed'
                   AND sb.sb_booking_date BETWEEN ? AND ?";
$stmt_earnings = $mysqli->prepare($earnings_query);
$stmt_earnings->bind_param('iss', $t_id, $month_start, $month_end);
$stmt_earnings->execute();
$earnings_result = $stmt_earnings->get_result();
$earnings_data = $earnings_result->fetch_object();
$total_earning = $earnings_data->total_earning ? $earnings_data->total_earning : 0;

// Get pincode filter
$filter_pincode = isset($_GET['pincode']) ? $_GET['pincode'] : '';

// Get services on pincode
if(!empty($filter_pincode)) {
    $services_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr, s.s_name, s.s_price
                       FROM tms_service_booking sb
                       LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                       LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                       WHERE sb.sb_technician_id = ? AND u.u_addr LIKE ?
                       ORDER BY sb.sb_booking_date DESC
                       LIMIT 10";
    $stmt_services = $mysqli->prepare($services_query);
    $pincode_search = "%{$filter_pincode}%";
    $stmt_services->bind_param('is', $t_id, $pincode_search);
    $stmt_services->execute();
    $services_result = $stmt_services->get_result();
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
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        
        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: #ff4757;
        }
        
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Profile Card */
        .profile-card {
         