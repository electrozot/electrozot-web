<!DOCTYPE html>
<html>
<head>
    <title>Enable User Deletion Protection</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; padding: 15px; background: #d4edda; border-radius: 5px; margin: 10px 0; border-left: 4px solid green; }
        .error { color: red; padding: 15px; background: #f8d7da; border-radius: 5px; margin: 10px 0; border-left: 4px solid red; }
        .warning { color: #856404; padding: 15px; background: #fff3cd; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
        .info { color: #004085; padding: 15px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        h2 { color: #333; }
        .step { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 0.85rem; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
<div class="container">
<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

echo "<h2>🔒 Enable User Deletion Protection</h2>";
echo "<p>This will permanently prevent user deletion from anywhere in the system.</p>";

$errors = [];
$success = [];
$warnings = [];

try {
    // Step 1: Create system logs table
    echo "<div class='step'><strong>Step 1:</strong> Creating system logs table...</div>";
    $create_logs = "CREATE TABLE IF NOT EXISTS `tms_system_logs` (
      `log_id` int NOT NULL AUTO_INCREMENT,
      `log_type` varchar(100) NOT NULL,
      `log_message` text,
      `log_data` text,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`log_id`),
      KEY `log_type` (`log_type`),
      KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if($mysqli->query($create_logs)) {
        $success[] = "✓ System logs table created/verified";
    } else {
        $errors[] = "✗ Failed to create system logs table: " . $mysqli->error;
    }
    
    // Step 2: Add protection columns to user table
    echo "<div class='step'><strong>Step 2:</strong> Adding protection columns to user table...</div>";
    
    $add_columns = [
        "ALTER TABLE `tms_user` ADD COLUMN IF NOT EXISTS `u_deletion_protected` tinyint(1) DEFAULT 1",
        "ALTER TABLE `tms_user` ADD COLUMN IF NOT EXISTS `u_registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP"
    ];
    
    foreach($add_columns as $sql) {
        if($mysqli->query($sql)) {
            $success[] = "✓ Protection column added/verified";
        } else {
            // Ignore "Duplicate column" errors
            if(strpos($mysqli->error, 'Duplicate column') === false) {
                $warnings[] = "⚠ Column may already exist: " . $mysqli->error;
            }
        }
    }
    
    // Step 3: Protect all existing users
    echo "<div class='step'><strong>Step 3:</strong> Protecting all existing users...</div>";
    $protect_users = "UPDATE `tms_user` SET `u_deletion_protected` = 1";
    if($mysqli->query($protect_users)) {
        $affected = $mysqli->affected_rows;
        $success[] = "✓ Protected $affected existing user(s)";
    }
    
    // Step 4: Create index
    echo "<div class='step'><strong>Step 4:</strong> Creating performance index...</div>";
    $create_index = "CREATE INDEX IF NOT EXISTS `idx_user_protected` ON `tms_user` (`u_deletion_protected`)";
    if($mysqli->query($create_index)) {
        $success[] = "✓ Performance index created";
    }
    
    // Step 5: Create DELETE trigger
    echo "<div class='step'><strong>Step 5:</strong> Creating DELETE protection trigger...</div>";
    
    // Drop existing trigger first
    $mysqli->query("DROP TRIGGER IF EXISTS `block_user_deletion`");
    
    $delete_trigger = "CREATE TRIGGER `block_user_deletion`
    BEFORE DELETE ON `tms_user`
    FOR EACH ROW
    BEGIN
        INSERT INTO tms_system_logs (log_type, log_message, log_data, created_at)
        VALUES (
            'USER_DELETE_BLOCKED_BY_TRIGGER', 
            CONCAT('BLOCKED: Attempted to delete user - ', OLD.u_fname, ' ', OLD.u_lname),
            CONCAT('User ID: ', OLD.u_id, ', Email: ', OLD.u_email, ', Phone: ', OLD.u_phone),
            NOW()
        );
        
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'USER DELETION BLOCKED: Users cannot be deleted once registered.';
    END";
    
    if($mysqli->query($delete_trigger)) {
        $success[] = "✓ DELETE protection trigger created";
    } else {
        $errors[] = "✗ Failed to create DELETE trigger: " . $mysqli->error;
    }
    
    // Step 6: Create UPDATE trigger (soft delete protection)
    echo "<div class='step'><strong>Step 6:</strong> Creating soft-delete protection trigger...</div>";
    
    // Drop existing trigger first
    $mysqli->query("DROP TRIGGER IF EXISTS `block_user_soft_delete`");
    
    $update_trigger = "CREATE TRIGGER `block_user_soft_delete`
    BEFORE UPDATE ON `tms_user`
    FOR EACH ROW
    BEGIN
        IF NEW.u_is_deleted = 1 AND OLD.u_is_deleted = 0 THEN
            INSERT INTO tms_system_logs (log_type, log_message, log_data, created_at)
            VALUES (
                'USER_SOFT_DELETE_BLOCKED', 
                CONCAT('BLOCKED: Attempted to soft-delete user - ', OLD.u_fname, ' ', OLD.u_lname),
                CONCAT('User ID: ', OLD.u_id, ', Email: ', OLD.u_email),
                NOW()
            );
            
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'USER SOFT DELETE BLOCKED: Users cannot be marked as deleted.';
        END IF;
    END";
    
    if($mysqli->query($update_trigger)) {
        $success[] = "✓ Soft-delete protection trigger created";
    } else {
        $errors[] = "✗ Failed to create UPDATE trigger: " . $mysqli->error;
    }
    
    // Step 7: Log the activation
    echo "<div class='step'><strong>Step 7:</strong> Logging activation...</div>";
    $count_query = "SELECT COUNT(*) as total FROM tms_user WHERE u_deletion_protected = 1";
    $count_result = $mysqli->query($count_query);
    $total_protected = $count_result->fetch_assoc()['total'];
    
    $log_activation = "INSERT INTO tms_system_logs (log_type, log_message, log_data)
                      VALUES ('USER_PROTECTION_ENABLED', 
                              'User deletion protection permanently enabled',
                              CONCAT('Total protected users: ', ?, ', Admin ID: ', ?))";
    $log_stmt = $mysqli->prepare($log_activation);
    $admin_id = $_SESSION['a_id'];
    $log_stmt->bind_param('ii', $total_protected, $admin_id);
    $log_stmt->execute();
    
    $success[] = "✓ Activation logged to system";
    
    // Verification
    echo "<div class='step'><strong>Step 8:</strong> Verifying protection...</div>";
    
    // Check triggers
    $check_triggers = "SHOW TRIGGERS FROM electrozot_db WHERE `Table` = 'tms_user'";
    $triggers_result = $mysqli->query($check_triggers);
    $trigger_count = $triggers_result->num_rows;
    
    if($trigger_count >= 2) {
        $success[] = "✓ Verified: $trigger_count protection trigger(s) active";
    } else {
        $warnings[] = "⚠ Warning: Only $trigger_count trigger(s) found (expected 2)";
    }
    
    // Display results
    echo "<hr>";
    
    if(count($success) > 0) {
        echo "<div class='success'>";
        echo "<h3>✅ Success!</h3>";
        foreach($success as $msg) {
            echo "<div>$msg</div>";
        }
        echo "</div>";
    }
    
    if(count($warnings) > 0) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Warnings</h3>";
        foreach($warnings as $msg) {
            echo "<div>$msg</div>";
        }
        echo "</div>";
    }
    
    if(count($errors) > 0) {
        echo "<div class='error'>";
        echo "<h3>❌ Errors</h3>";
        foreach($errors as $msg) {
            echo "<div>$msg</div>";
        }
        echo "</div>";
    }
    
    // Final status
    echo "<div class='info'>";
    echo "<h3>📊 Protection Status</h3>";
    echo "<p><span class='badge badge-success'>ENABLED</span> User deletion protection is now active</p>";
    echo "<p><strong>Protected Users:</strong> $total_protected</p>";
    echo "<p><strong>Active Triggers:</strong> $trigger_count</p>";
    echo "<p><strong>Activated By:</strong> Admin ID $admin_id</p>";
    echo "<p><strong>Activated At:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "<h3>⚠️ Important Notes</h3>";
    echo "<ul>";
    echo "<li>Users can NO LONGER be deleted from admin panel</li>";
    echo "<li>Users can NO LONGER be deleted from database directly</li>";
    echo "<li>All deletion attempts will be logged</li>";
    echo "<li>This protection is PERMANENT and database-enforced</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>📖 Next Steps</h3>";
    echo "<ol>";
    echo "<li>Read the complete guide: <code>admin/USER_DELETION_PROTECTION_GUIDE.md</code></li>";
    echo "<li>Test the protection by trying to delete a user</li>";
    echo "<li>Check system logs: <code>SELECT * FROM tms_system_logs</code></li>";
    echo "<li><strong>DELETE THIS FILE</strong> (<code>enable-user-protection.php</code>) for security</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p style='text-align: center; margin-top: 30px;'>";
    echo "<a href='admin-manage-user-passwords.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to User Management</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Fatal Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

$mysqli->close();
?>
</div>
</body>
</html>
