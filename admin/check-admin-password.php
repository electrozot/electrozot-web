<?php
/**
 * ADMIN PASSWORD CHECKER & FIXER
 * Run this file once to check and fix admin password issues
 */

include('vendor/inc/config.php');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Admin Password Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h2 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #10b981;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #ef4444;
        }
        .info {
            background: #dbeafe;
            color: #1e40af;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #3b82f6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-danger {
            background: #ef4444;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        code {
            background: #1f2937;
            color: #10b981;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>";

echo "<div class='card'>";
echo "<h2>🔍 Admin Password Checker</h2>";

// Get all admin accounts
$result = $mysqli->query("SELECT a_id, a_name, a_email, a_phone, a_pwd FROM tms_admin");

if($result && $result->num_rows > 0) {
    echo "<div class='info'><strong>Found " . $result->num_rows . " admin account(s)</strong></div>";
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Password Hash</th><th>Test Password</th></tr>";
    
    while($admin = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $admin['a_id'] . "</td>";
        echo "<td>" . htmlspecialchars($admin['a_name']) . "</td>";
        echo "<td>" . htmlspecialchars($admin['a_email']) . "</td>";
        echo "<td>" . htmlspecialchars($admin['a_phone']) . "</td>";
        echo "<td><code>" . substr($admin['a_pwd'], 0, 20) . "...</code></td>";
        
        // Test common passwords
        $common_passwords = ['admin123', 'admin', '123456', 'password', 'admin@123'];
        $found_password = null;
        
        foreach($common_passwords as $test_pwd) {
            if(md5($test_pwd) === $admin['a_pwd']) {
                $found_password = $test_pwd;
                break;
            }
        }
        
        if($found_password) {
            echo "<td><span style='color: #10b981; font-weight: bold;'>✅ " . htmlspecialchars($found_password) . "</span></td>";
        } else {
            echo "<td><span style='color: #ef4444;'>❌ Unknown</span></td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
    
} else {
    echo "<div class='error'><strong>❌ No admin accounts found!</strong></div>";
}

echo "</div>";

// Password Reset Form
echo "<div class='card'>";
echo "<h2>🔧 Reset Admin Password</h2>";

if(isset($_POST['reset_password'])) {
    $admin_id = intval($_POST['admin_id']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if(empty($new_password)) {
        echo "<div class='error'>❌ Password cannot be empty!</div>";
    } elseif($new_password !== $confirm_password) {
        echo "<div class='error'>❌ Passwords do not match!</div>";
    } else {
        $hashed_password = md5($new_password);
        $stmt = $mysqli->prepare("UPDATE tms_admin SET a_pwd = ? WHERE a_id = ?");
        $stmt->bind_param('si', $hashed_password, $admin_id);
        
        if($stmt->execute()) {
            echo "<div class='success'>";
            echo "<strong>✅ Password Updated Successfully!</strong><br>";
            echo "Admin ID: " . $admin_id . "<br>";
            echo "New Password: <code>" . htmlspecialchars($new_password) . "</code><br>";
            echo "MD5 Hash: <code>" . $hashed_password . "</code>";
            echo "</div>";
            
            echo "<div class='info'>";
            echo "<strong>You can now login with:</strong><br>";
            echo "Email/Phone: (your admin email or phone)<br>";
            echo "Password: <code>" . htmlspecialchars($new_password) . "</code>";
            echo "</div>";
        } else {
            echo "<div class='error'>❌ Failed to update password: " . $mysqli->error . "</div>";
        }
    }
}

echo "<form method='POST'>";
echo "<div style='margin: 20px 0;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: 600;'>Select Admin Account:</label>";
echo "<select name='admin_id' required style='width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;'>";

$result = $mysqli->query("SELECT a_id, a_name, a_email FROM tms_admin");
while($admin = $result->fetch_assoc()) {
    echo "<option value='" . $admin['a_id'] . "'>" . htmlspecialchars($admin['a_name']) . " (" . htmlspecialchars($admin['a_email']) . ")</option>";
}
echo "</select>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: 600;'>New Password:</label>";
echo "<input type='text' name='new_password' required placeholder='Enter new password' style='width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;'>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: 600;'>Confirm Password:</label>";
echo "<input type='text' name='confirm_password' required placeholder='Re-enter new password' style='width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;'>";
echo "</div>";

echo "<button type='submit' name='reset_password' class='btn'>🔄 Reset Password</button>";
echo "</form>";

echo "</div>";

// Quick Fix Section
echo "<div class='card'>";
echo "<h2>⚡ Quick Fix - Set Default Password</h2>";
echo "<div class='info'>";
echo "<strong>Click below to set default password for all admins:</strong><br>";
echo "Default Password: <code>admin123</code>";
echo "</div>";

if(isset($_POST['quick_fix'])) {
    $default_password = 'admin123';
    $hashed = md5($default_password);
    
    $mysqli->query("UPDATE tms_admin SET a_pwd = '$hashed'");
    
    echo "<div class='success'>";
    echo "<strong>✅ All admin passwords reset to: <code>admin123</code></strong><br>";
    echo "You can now login with any admin email/phone and password: <code>admin123</code>";
    echo "</div>";
}

echo "<form method='POST'>";
echo "<button type='submit' name='quick_fix' class='btn btn-danger' onclick='return confirm(\"This will reset ALL admin passwords to admin123. Continue?\")'>⚡ Quick Fix - Reset All to admin123</button>";
echo "</form>";

echo "</div>";

// Testing Section
echo "<div class='card'>";
echo "<h2>🧪 Test Login</h2>";
echo "<div class='info'>";
echo "<strong>Test your credentials here before trying actual login:</strong>";
echo "</div>";

if(isset($_POST['test_login'])) {
    $test_email = $_POST['test_email'];
    $test_password = $_POST['test_password'];
    $test_hashed = md5($test_password);
    
    $stmt = $mysqli->prepare("SELECT a_id, a_name, a_email, a_pwd FROM tms_admin WHERE (a_email = ? OR a_phone = ?)");
    $stmt->bind_param('ss', $test_email, $test_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        echo "<div class='info'>";
        echo "<strong>Account Found:</strong><br>";
        echo "Name: " . htmlspecialchars($admin['a_name']) . "<br>";
        echo "Email: " . htmlspecialchars($admin['a_email']) . "<br>";
        echo "Stored Hash: <code>" . $admin['a_pwd'] . "</code><br>";
        echo "Your Hash: <code>" . $test_hashed . "</code><br>";
        echo "</div>";
        
        if($admin['a_pwd'] === $test_hashed) {
            echo "<div class='success'>";
            echo "<strong>✅ PASSWORD MATCH! Login should work.</strong><br>";
            echo "You can login with:<br>";
            echo "Email/Phone: <code>" . htmlspecialchars($test_email) . "</code><br>";
            echo "Password: <code>" . htmlspecialchars($test_password) . "</code>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<strong>❌ PASSWORD MISMATCH!</strong><br>";
            echo "The password you entered does not match the stored hash.<br>";
            echo "Use the 'Reset Password' form above to set a new password.";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ Account not found with email/phone: " . htmlspecialchars($test_email) . "</strong>";
        echo "</div>";
    }
}

echo "<form method='POST'>";
echo "<div style='margin: 20px 0;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: 600;'>Email or Phone:</label>";
echo "<input type='text' name='test_email' required placeholder='Enter email or phone' style='width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;'>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: 600;'>Password:</label>";
echo "<input type='text' name='test_password' required placeholder='Enter password to test' style='width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;'>";
echo "</div>";

echo "<button type='submit' name='test_login' class='btn'>🧪 Test Login</button>";
echo "</form>";

echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.php' class='btn'>← Back to Login Page</a>";
echo "</div>";

echo "</body></html>";
?>
