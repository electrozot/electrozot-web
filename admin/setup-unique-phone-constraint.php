<?php
/**
 * Database Setup: Add UNIQUE constraint to phone numbers
 * 
 * This script ensures that one mobile number can only be associated with one user account.
 * Run this once to add the constraint to the database.
 * 
 * IMPORTANT: This will check for duplicate phone numbers first and report them.
 */

include('vendor/inc/config.php');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Unique Phone Constraint</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 0 0; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔒 Setup Unique Phone Number Constraint</h1>";

// Step 1: Check for duplicate phone numbers
echo "<div class='info'><strong>Step 1:</strong> Checking for duplicate phone numbers...</div>";

$duplicate_check = "SELECT u_phone, COUNT(*) as count, GROUP_CONCAT(u_id) as user_ids, GROUP_CONCAT(u_fname) as names 
                    FROM tms_user 
                    WHERE u_phone IS NOT NULL AND u_phone != '' 
                    GROUP BY u_phone 
                    HAVING count > 1";

$result = $mysqli->query($duplicate_check);

if($result && $result->num_rows > 0) {
    echo "<div class='warning'><strong>⚠️ Warning:</strong> Found duplicate phone numbers in the database!</div>";
    echo "<table>
            <tr>
                <th>Phone Number</th>
                <th>Count</th>
                <th>User IDs</th>
                <th>Names</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['u_phone']}</td>
                <td>{$row['count']}</td>
                <td>{$row['user_ids']}</td>
                <td>{$row['names']}</td>
              </tr>";
    }
    echo "</table>";
    
    echo "<div class='error'>
            <strong>Action Required:</strong> You need to manually resolve these duplicate phone numbers before adding the UNIQUE constraint.
            <br><br>
            <strong>Options:</strong>
            <ul>
                <li>Delete duplicate accounts (keep only one per phone number)</li>
                <li>Update phone numbers to make them unique</li>
                <li>Merge accounts if they belong to the same person</li>
            </ul>
          </div>";
    
} else {
    echo "<div class='success'>✅ No duplicate phone numbers found!</div>";
    
    // Step 2: Add UNIQUE constraint
    echo "<div class='info'><strong>Step 2:</strong> Adding UNIQUE constraint to u_phone column...</div>";
    
    // First, check if constraint already exists
    $check_constraint = "SELECT COUNT(*) as count 
                        FROM information_schema.statistics 
                        WHERE table_schema = DATABASE() 
                        AND table_name = 'tms_user' 
                        AND index_name = 'unique_phone'";
    
    $constraint_result = $mysqli->query($check_constraint);
    $constraint_row = $constraint_result->fetch_assoc();
    
    if($constraint_row['count'] > 0) {
        echo "<div class='warning'>⚠️ UNIQUE constraint already exists on u_phone column.</div>";
    } else {
        // Add the UNIQUE constraint
        $add_constraint = "ALTER TABLE tms_user ADD UNIQUE KEY unique_phone (u_phone)";
        
        if($mysqli->query($add_constraint)) {
            echo "<div class='success'>✅ Successfully added UNIQUE constraint to u_phone column!</div>";
            echo "<div class='info'>
                    <strong>What this means:</strong>
                    <ul>
                        <li>✅ One mobile number = One user account</li>
                        <li>✅ Database will automatically reject duplicate phone numbers</li>
                        <li>✅ Enhanced data integrity and security</li>
                        <li>✅ Prevents accidental duplicate registrations</li>
                    </ul>
                  </div>";
        } else {
            echo "<div class='error'>❌ Error adding constraint: " . $mysqli->error . "</div>";
        }
    }
    
    // Step 3: Verify the constraint
    echo "<div class='info'><strong>Step 3:</strong> Verifying constraint...</div>";
    
    $verify = "SHOW INDEX FROM tms_user WHERE Key_name = 'unique_phone'";
    $verify_result = $mysqli->query($verify);
    
    if($verify_result && $verify_result->num_rows > 0) {
        echo "<div class='success'>✅ Constraint verified successfully!</div>";
        
        echo "<table>
                <tr>
                    <th>Column</th>
                    <th>Index Type</th>
                    <th>Status</th>
                </tr>";
        
        while($row = $verify_result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Column_name']}</td>
                    <td>" . ($row['Non_unique'] == 0 ? 'UNIQUE' : 'NON-UNIQUE') . "</td>
                    <td>✅ Active</td>
                  </tr>";
        }
        echo "</table>";
    }
}

// Display current statistics
echo "<div class='info'><strong>Database Statistics:</strong></div>";

$stats = "SELECT 
            COUNT(*) as total_users,
            COUNT(DISTINCT u_phone) as unique_phones,
            COUNT(*) - COUNT(DISTINCT u_phone) as duplicate_count
          FROM tms_user 
          WHERE u_phone IS NOT NULL AND u_phone != ''";

$stats_result = $mysqli->query($stats);
$stats_row = $stats_result->fetch_assoc();

echo "<table>
        <tr>
            <th>Metric</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Total Users</td>
            <td>{$stats_row['total_users']}</td>
        </tr>
        <tr>
            <td>Unique Phone Numbers</td>
            <td>{$stats_row['unique_phones']}</td>
        </tr>
        <tr>
            <td>Duplicate Phone Numbers</td>
            <td>{$stats_row['duplicate_count']}</td>
        </tr>
      </table>";

echo "<br><a href='admin-dashboard.php' class='btn'>← Back to Dashboard</a>";
echo "<a href='admin-manage-user.php' class='btn'>View Users</a>";

echo "</div></body></html>";

$mysqli->close();
?>
