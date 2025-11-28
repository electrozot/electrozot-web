<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$phone = "9816888656";

// Find user by phone
$query = "SELECT u_id, u_fname, u_lname, u_phone, u_pwd, registration_type FROM tms_user WHERE u_phone = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $phone);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $user = $result->fetch_object();
    
    // Calculate what the password should be
    $name_for_pwd = strtolower(substr($user->u_fname, 0, 3));
    $phone_for_pwd = substr($user->u_phone, -3);
    $calculated_password = $name_for_pwd . $phone_for_pwd;
    
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2>User Information for Phone: $phone</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>User ID</td><td>{$user->u_id}</td></tr>";
    echo "<tr><td>First Name</td><td>{$user->u_fname}</td></tr>";
    echo "<tr><td>Last Name</td><td>{$user->u_lname}</td></tr>";
    echo "<tr><td>Phone</td><td>{$user->u_phone}</td></tr>";
    echo "<tr><td>Registration Type</td><td>{$user->registration_type}</td></tr>";
    echo "<tr><td>Stored Hash</td><td style='font-size: 10px;'>{$user->u_pwd}</td></tr>";
    echo "</table>";
    
    echo "<div style='background: #d4edda; padding: 20px; margin-top: 20px; border-radius: 10px; border: 3px solid #28a745;'>";
    echo "<h3 style='color: #155724;'>🔑 CALCULATED PASSWORD:</h3>";
    echo "<p style='font-size: 32px; font-weight: bold; color: #155724; margin: 10px 0;'>$calculated_password</p>";
    echo "<p style='color: #155724;'>Formula: First 3 letters of '{$user->u_fname}' (lowercase) + Last 3 digits of '{$user->u_phone}'</p>";
    echo "<p style='color: #155724;'>= '$name_for_pwd' + '$phone_for_pwd' = <strong>$calculated_password</strong></p>";
    echo "</div>";
    
    // Test if password matches
    echo "<div style='background: #fff3cd; padding: 20px; margin-top: 20px; border-radius: 10px;'>";
    echo "<h3>Password Verification Test:</h3>";
    if(password_verify($calculated_password, $user->u_pwd)) {
        echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ PASSWORD MATCHES! User can login with: <span style='background: yellow;'>$calculated_password</span></p>";
    } else {
        echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ PASSWORD DOES NOT MATCH!</p>";
        echo "<p>This means the stored password is different from the auto-generated one.</p>";
        echo "<p>The user might have been created before the auto-password feature was added.</p>";
    }
    echo "</div>";
    
    echo "</div>";
} else {
    echo "<h2>No user found with phone number: $phone</h2>";
}
?>
