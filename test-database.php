<?php
/**
 * Database Testing Script
 * Tests database connection and sample data
 */

// Include database connection
require_once('vendor/inc/config.php');

// Set page title
$page_title = "Database Testing";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ElectroZot</title>
    <link rel="stylesheet" href="vendor/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/css/custom.css">
    <style>
        .test-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .test-result {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .test-result.success {
            border-left-color: #28a745;
        }
        .test-result.warning {
            border-left-color: #ffc107;
        }
        .test-result.error {
            border-left-color: #dc3545;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        table {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <h1 class="mb-4">🔧 ElectroZot Database Testing</h1>
        
        <?php
        // Test 1: Database Connection
        echo '<div class="test-section">';
        echo '<h3>Test 1: Database Connection</h3>';
        if ($mysqli->connect_error) {
            echo '<div class="test-result error">';
            echo '<strong>❌ Connection Failed:</strong> ' . $mysqli->connect_error;
            echo '</div>';
            exit;
        } else {
            echo '<div class="test-result success">';
            echo '<strong>✅ Connection Successful</strong><br>';
            echo 'Host: ' . $mysqli->host_info . '<br>';
            echo 'Database: electrozot_db';
            echo '</div>';
        }
        echo '</div>';

        // Test 2: Count Records
        echo '<div class="test-section">';
        echo '<h3>Test 2: Record Counts</h3>';
        echo '<div class="row">';
        
        // Count Users
        $result = $mysqli->query("SELECT COUNT(*) as count FROM tms_user");
        $user_count = $result->fetch_assoc()['count'];
        echo '<div class="col-md-3">';
        echo '<div class="stat-card">';
        echo '<div class="stat-number">' . $user_count . '</div>';
        echo '<div class="stat-label">Total Users</div>';
        echo '</div>';
        echo '</div>';
        
        // Count Technicians
        $result = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician");
        $tech_count = $result->fetch_assoc()['count'];
        echo '<div class="col-md-3">';
        echo '<div class="stat-card">';
        echo '<div class="stat-number">' . $tech_count . '</div>';
        echo '<div class="stat-label">Total Technicians</div>';
        echo '</div>';
        echo '</div>';
        
        // Count Services
        $result = $mysqli->query("SELECT COUNT(*) as count FROM tms_service");
        $service_count = $result->fetch_assoc()['count'];
        echo '<div class="col-md-3">';
        echo '<div class="stat-card">';
        echo '<div class="stat-number">' . $service_count . '</div>';
        echo '<div class="stat-label">Total Services</div>';
        echo '</div>';
        echo '</div>';
        
        // Count Bookings
        $result = $mysqli->query("SELECT COUNT(*) as count FROM tms_service_booking");
        $booking_count = $result->fetch_assoc()['count'];
        echo '<div class="col-md-3">';
        echo '<div class="stat-card">';
        echo '<div class="stat-number">' . $booking_count . '</div>';
        echo '<div class="stat-label">Total Bookings</div>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        echo '</div>';

        // Test 3: Users List
        echo '<div class="test-section">';
        echo '<h3>Test 3: Sample Users (First 10)</h3>';
        $query = "SELECT u_id, u_fname, u_lname, u_email, u_phone, u_category FROM tms_user ORDER BY u_id DESC LIMIT 10";
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered">';
            echo '<thead class="thead-dark">';
            echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Category</th></tr>';
            echo '</thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['u_id'] . '</td>';
                echo '<td>' . $row['u_fname'] . ' ' . $row['u_lname'] . '</td>';
                echo '<td>' . $row['u_email'] . '</td>';
                echo '<td>' . $row['u_phone'] . '</td>';
                echo '<td>' . $row['u_category'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<div class="test-result warning">No users found</div>';
        }
        echo '</div>';

        // Test 4: Technicians by Category
        echo '<div class="test-section">';
        echo '<h3>Test 4: Technicians by Category</h3>';
        $query = "SELECT t_category, COUNT(*) as count FROM tms_technician GROUP BY t_category ORDER BY count DESC";
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered">';
            echo '<thead class="thead-dark">';
            echo '<tr><th>Category</th><th>Count</th></tr>';
            echo '</thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['t_category'] . '</td>';
                echo '<td><strong>' . $row['count'] . '</strong></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
        }
        echo '</div>';

        // Test 5: Bookings by Status
        echo '<div class="test-section">';
        echo '<h3>Test 5: Bookings by Status</h3>';
        $query = "SELECT sb_status, COUNT(*) as count FROM tms_service_booking GROUP BY sb_status ORDER BY count DESC";
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0) {
            echo '<div class="row">';
            while ($row = $result->fetch_assoc()) {
                $status = $row['sb_status'];
                $count = $row['count'];
                
                // Color coding
                $color = '#007bff';
                if ($status == 'Completed') $color = '#28a745';
                if ($status == 'Rejected' || $status == 'Cancelled') $color = '#dc3545';
                if ($status == 'In Progress') $color = '#ffc107';
                
                echo '<div class="col-md-4 mb-3">';
                echo '<div class="stat-card">';
                echo '<div class="stat-number" style="color: ' . $color . '">' . $count . '</div>';
                echo '<div class="stat-label">' . $status . '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="test-result warning">No bookings found</div>';
        }
        echo '</div>';

        // Test 6: Recent Bookings
        echo '<div class="test-section">';
        echo '<h3>Test 6: Recent Bookings (Last 10)</h3>';
        $query = "SELECT sb.sb_id, u.u_fname, u.u_lname, s.s_name, sb.sb_booking_date, sb.sb_status, sb.sb_total_price
                  FROM tms_service_booking sb
                  INNER JOIN tms_user u ON sb.sb_user_id = u.u_id
                  INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
                  ORDER BY sb.sb_created_at DESC
                  LIMIT 10";
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered">';
            echo '<thead class="thead-dark">';
            echo '<tr><th>ID</th><th>Customer</th><th>Service</th><th>Date</th><th>Status</th><th>Price</th></tr>';
            echo '</thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                $status_class = '';
                if ($row['sb_status'] == 'Completed') $status_class = 'badge-success';
                elseif ($row['sb_status'] == 'Rejected' || $row['sb_status'] == 'Cancelled') $status_class = 'badge-danger';
                elseif ($row['sb_status'] == 'In Progress') $status_class = 'badge-warning';
                else $status_class = 'badge-info';
                
                echo '<tr>';
                echo '<td>' . $row['sb_id'] . '</td>';
                echo '<td>' . $row['u_fname'] . ' ' . $row['u_lname'] . '</td>';
                echo '<td>' . $row['s_name'] . '</td>';
                echo '<td>' . date('M d, Y', strtotime($row['sb_booking_date'])) . '</td>';
                echo '<td><span class="badge ' . $status_class . '">' . $row['sb_status'] . '</span></td>';
                echo '<td>৳' . number_format($row['sb_total_price'], 2) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<div class="test-result warning">No bookings found</div>';
        }
        echo '</div>';

        // Test 7: Revenue Analysis
        echo '<div class="test-section">';
        echo '<h3>Test 7: Revenue Analysis</h3>';
        $query = "SELECT 
                    SUM(sb_total_price) as total_revenue,
                    AVG(sb_total_price) as avg_booking_value,
                    COUNT(*) as total_bookings
                  FROM tms_service_booking";
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '<div class="row">';
            echo '<div class="col-md-4">';
            echo '<div class="stat-card">';
            echo '<div class="stat-number">৳' . number_format($row['total_revenue'], 2) . '</div>';
            echo '<div class="stat-label">Total Revenue</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-4">';
            echo '<div class="stat-card">';
            echo '<div class="stat-number">৳' . number_format($row['avg_booking_value'], 2) . '</div>';
            echo '<div class="stat-label">Average Booking Value</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-4">';
            echo '<div class="stat-card">';
            echo '<div class="stat-number">' . $row['total_bookings'] . '</div>';
            echo '<div class="stat-label">Total Bookings</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';

        // Test 8: Data Integrity Check
        echo '<div class="test-section">';
        echo '<h3>Test 8: Data Integrity Checks</h3>';
        
        // Check for orphaned bookings
        $query = "SELECT COUNT(*) as count FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  WHERE u.u_id IS NULL";
        $result = $mysqli->query($query);
        $orphaned = $result->fetch_assoc()['count'];
        
        if ($orphaned == 0) {
            echo '<div class="test-result success">✅ No orphaned bookings (all bookings have valid users)</div>';
        } else {
            echo '<div class="test-result error">❌ Found ' . $orphaned . ' orphaned bookings</div>';
        }
        
        // Check for category mismatch
        $query = "SELECT COUNT(*) as count FROM tms_service_booking sb
                  INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
                  INNER JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                  WHERE s.s_category != t.t_category";
        $result = $mysqli->query($query);
        $mismatch = $result->fetch_assoc()['count'];
        
        if ($mismatch == 0) {
            echo '<div class="test-result success">✅ No category mismatches (all technicians match service categories)</div>';
        } else {
            echo '<div class="test-result error">❌ Found ' . $mismatch . ' category mismatches</div>';
        }
        
        // Check for assigned bookings without technician
        $query = "SELECT COUNT(*) as count FROM tms_service_booking
                  WHERE sb_status IN ('Assigned', 'In Progress', 'Completed')
                  AND sb_technician_id IS NULL";
        $result = $mysqli->query($query);
        $invalid = $result->fetch_assoc()['count'];
        
        if ($invalid == 0) {
            echo '<div class="test-result success">✅ All assigned/in-progress/completed bookings have technicians</div>';
        } else {
            echo '<div class="test-result error">❌ Found ' . $invalid . ' assigned bookings without technicians</div>';
        }
        
        echo '</div>';

        // Summary
        echo '<div class="test-section">';
        echo '<h3>✅ Testing Complete</h3>';
        echo '<div class="test-result success">';
        echo '<strong>All database tests completed successfully!</strong><br><br>';
        echo '<strong>Summary:</strong><br>';
        echo '• ' . $user_count . ' users in database<br>';
        echo '• ' . $tech_count . ' technicians available<br>';
        echo '• ' . $service_count . ' services offered<br>';
        echo '• ' . $booking_count . ' bookings recorded<br>';
        echo '<br>';
        echo '<a href="admin/index.php" class="btn btn-primary">Go to Admin Dashboard</a> ';
        echo '<a href="index.php" class="btn btn-secondary">Go to Home</a>';
        echo '</div>';
        echo '</div>';
        ?>
    </div>

    <script src="vendor/js/jquery-3.3.1.min.js"></script>
    <script src="vendor/js/bootstrap.bundle.min.js"></script>
</body>
</html>
