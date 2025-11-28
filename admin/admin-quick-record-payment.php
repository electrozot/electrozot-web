<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

if(isset($_POST['record_payment'])) {
    $tech_id = $_POST['tech_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'];
    $admin_id = $_SESSION['a_id'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'admin-stats-technicians.php';
    
    // Validate inputs
    if(empty($tech_id) || empty($amount) || empty($payment_date)) {
        $_SESSION['error'] = "Please fill all required fields.";
        header("location: $redirect");
        exit();
    }
    
    // Insert payment record
    $query = "INSERT INTO tms_commission_payments (cp_technician_id, cp_amount, cp_date, cp_payment_method, cp_notes, cp_recorded_by) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('idsssi', $tech_id, $amount, $payment_date, $payment_method, $notes, $admin_id);
    
    if($stmt->execute()) {
        // Get technician name
        $tech_query = "SELECT t_name FROM tms_technician WHERE t_id = ?";
        $stmt_tech = $mysqli->prepare($tech_query);
        $stmt_tech->bind_param('i', $tech_id);
        $stmt_tech->execute();
        $tech_result = $stmt_tech->get_result();
        $tech = $tech_result->fetch_object();
        $stmt_tech->close();
        
        // Auto-unlock technician account if it was locked
        $unlock_query = "UPDATE tms_technician 
                        SET account_locked = 0, 
                            lock_reason = NULL, 
                            locked_at = NULL 
                        WHERE t_id = ?";
        $stmt_unlock = $mysqli->prepare($unlock_query);
        $stmt_unlock->bind_param('i', $tech_id);
        $stmt_unlock->execute();
        $stmt_unlock->close();
        
        $_SESSION['success'] = "Payment of ₹" . number_format($amount, 0) . " recorded successfully for " . $tech->t_name . "! Account unlocked.";
    } else {
        $_SESSION['error'] = "Failed to record payment. Please try again.";
    }
    $stmt->close();
    
    header("location: $redirect");
    exit();
} else {
    header("location: admin-stats-technicians.php");
    exit();
}
?>
