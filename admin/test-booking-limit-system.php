<?php
/**
 * Test: Booking Limit System
 * Verify that technicians can handle multiple bookings based on their limit
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
include('check-technician-availability.php');
check_login();

// Get all technicians with their booking stats
$techs_query = "SELECT t.t_id, t.t_name, t.t_category, t.t_booking_limit, t.t_current_bookings,
                       (t.t_booking_limit - t.t_current_bookings) as available_slots,
                       t.t_status
                FROM tms_technician t
                ORDER BY t.t_name ASC";
$all_techs = $mysqli->query($techs_query);

// Test results
$test_results = [];

// Test 1: Check if booking limit columns exist
$columns_check = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_booking_limit'");
$test_results[] = [
    'test' => 'Booking limit column exists',
    'expected' => 'Column exists',
    'actual' => $columns_check->num_rows > 0 ? 'Exists' : 'Missing',
    'status' => $columns_check->num_rows > 0 ? 'PASS' : 'FAIL'
];

// Test 2: Check if current bookings column exists
$columns_check2 = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_current_bookings'");
$test_results[] = [
    'test' => 'Current bookings column exists',
    'expected' => 'Column exists',
    'actual' => $columns_check2->num_rows > 0 ? 'Exists' : 'Missing',
    'status' => $columns_check2->num_rows > 0 ? 'PASS' : 'FAIL'
];

// Test 3: Check if any technician has limit > 1
$multi_booking_query = "SELECT COUNT(*) as count FROM tms_technician WHERE t_booking_limit > 1";
$multi_count = $mysqli->query($multi_booking_query)->fetch_assoc()['count'];
$test_results[] = [
    'test' => 'Technicians with multi-booking capability',
    'expected' => '> 0 technicians',
    'actual' => $multi_count . ' technician(s)',
    'status' => $multi_count > 0 ? 'PASS' : 'INFO'
];

// Test 4: Test checkTechnicianEngagement function
if ($all_techs->num_rows > 0) {
    $all_techs->data_seek(0);
    $first_tech = $all_techs->fetch_assoc();
    $engagement = checkTechnicianEngagement($first_tech['t_id'], $mysqli);
    
    $test_results[] = [
        'test' => 'checkTechnicianEngagement returns booking limit data',
        'expected' => 'Has booking_limit and current_bookings',
        'actual' => 'Limit: ' . ($engagement['booking_limit'] ?? 'N/A') . ', Current: ' . ($engagement['current_bookings'] ?? 'N/A'),
        'status' => isset($engagement['booking_limit']) && isset($engagement['current_bookings']) ? 'PASS' : 'FAIL'
    ];
}

// Test 5: Check available technicians function
$available = getAvailableTechnicians('Electrical', $mysqli);
$test_results[] = [
    'test' => 'getAvailableTechnicians returns technicians with slots',
    'expected' => 'Technicians with available_slots field',
    'actual' => count($available) . ' technician(s) found',
    'status' => count($available) > 0 ? 'PASS' : 'INFO'
];

// Calculate system stats
$total_capacity = 0;
$total_used = 0;
$all_techs->data_seek(0);
while ($tech = $all_techs->fetch_assoc()) {
    $total_capacity += $tech['t_booking_limit'];
    $total_used += $tech['t_current_bookings'];
}
$total_available = $total_capacity - $total_used;
$utilization = $total_capacity > 0 ? round(($total_used / $total_capacity) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Booking Limit System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 30px; }
        .test-card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .pass { color: #10b981; font-weight: bold; }
        .fail { color: #ef4444; font-weight: bold; }
        .info { color: #3b82f6; font-weight: bold; }
        .stat-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; }
        .progress-bar-custom { height: 30px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h2><i class="fas fa-vial"></i> Booking Limit System Test</h2>
            <p class="text-muted">Testing multi-booking capability based on technician limits</p>
        </div>

        <!-- System Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total_capacity; ?></div>
                    <div>Total Capacity</div>
                    <small>Max concurrent bookings</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="stat-number"><?php echo $total_used; ?></div>
                    <div>Currently Used</div>
                    <small>Active bookings</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="stat-number"><?php echo $total_available; ?></div>
                    <div>Available Slots</div>
                    <small>Can accept new bookings</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <div class="stat-number"><?php echo $utilization; ?>%</div>
                    <div>Utilization</div>
                    <small>System capacity used</small>
                </div>
            </div>
        </div>

        <!-- Utilization Bar -->
        <div class="test-card">
            <h5><i class="fas fa-chart-bar"></i> System Capacity</h5>
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success progress-bar-custom" style="width: <?php echo $utilization; ?>%">
                    <?php echo $total_used; ?> / <?php echo $total_capacity; ?> bookings (<?php echo $utilization; ?>%)
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="test-card">
            <h4><i class="fas fa-check-circle"></i> Test Results</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Test</th>
                        <th>Expected</th>
                        <th>Actual</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($test_results as $result): ?>
                    <tr>
                        <td><?php echo $result['test']; ?></td>
                        <td><?php echo $result['expected']; ?></td>
                        <td><?php echo $result['actual']; ?></td>
                        <td class="<?php echo strtolower($result['status']); ?>">
                            <?php if ($result['status'] == 'PASS'): ?>
                                <i class="fas fa-check-circle"></i> PASS
                            <?php elseif ($result['status'] == 'FAIL'): ?>
                                <i class="fas fa-times-circle"></i> FAIL
                            <?php else: ?>
                                <i class="fas fa-info-circle"></i> INFO
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- All Technicians -->
        <div class="test-card">
            <h4><i class="fas fa-users"></i> All Technicians - Booking Capacity</h4>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Limit</th>
                        <th>Current</th>
                        <th>Available</th>
                        <th>Utilization</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $all_techs->data_seek(0);
                    while ($tech = $all_techs->fetch_assoc()): 
                        $tech_util = $tech['t_booking_limit'] > 0 ? round(($tech['t_current_bookings'] / $tech['t_booking_limit']) * 100) : 0;
                        $status_class = $tech['available_slots'] > 0 ? 'success' : 'danger';
                    ?>
                    <tr>
                        <td><?php echo $tech['t_id']; ?></td>
                        <td><?php echo htmlspecialchars($tech['t_name']); ?></td>
                        <td><?php echo htmlspecialchars($tech['t_category']); ?></td>
                        <td><span class="badge bg-primary"><?php echo $tech['t_booking_limit']; ?></span></td>
                        <td><span class="badge bg-warning"><?php echo $tech['t_current_bookings']; ?></span></td>
                        <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo $tech['available_slots']; ?></span></td>
                        <td>
                            <div class="progress" style="height: 20px; min-width: 100px;">
                                <div class="progress-bar bg-<?php echo $tech_util >= 100 ? 'danger' : ($tech_util >= 75 ? 'warning' : 'success'); ?>" 
                                     style="width: <?php echo $tech_util; ?>%">
                                    <?php echo $tech_util; ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($tech['available_slots'] > 0): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Can Accept
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times"></i> At Capacity
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="test-card">
            <h5><i class="fas fa-info-circle"></i> How Booking Limits Work</h5>
            <ol>
                <li><strong>Booking Limit (1-5):</strong> Maximum concurrent bookings a technician can handle</li>
                <li><strong>Current Bookings:</strong> Number of active bookings currently assigned</li>
                <li><strong>Available Slots:</strong> Limit - Current = How many more bookings they can take</li>
                <li><strong>Assignment Logic:</strong> System only shows technicians with available_slots > 0</li>
                <li><strong>Auto-Update:</strong> Current bookings increment on assignment, decrement on completion/rejection</li>
            </ol>
            
            <div class="alert alert-success mt-3">
                <strong><i class="fas fa-lightbulb"></i> Example:</strong><br>
                Technician with limit=3 can handle 3 bookings simultaneously:<br>
                - Booking 1 assigned → current=1, available=2 ✅ Still available<br>
                - Booking 2 assigned → current=2, available=1 ✅ Still available<br>
                - Booking 3 assigned → current=3, available=0 ❌ At capacity<br>
                - Booking 1 completed → current=2, available=1 ✅ Available again
            </div>
        </div>

        <div class="test-card">
            <h5><i class="fas fa-link"></i> Quick Actions</h5>
            <a href="setup-booking-limits.php" class="btn btn-primary">
                <i class="fas fa-cog"></i> Setup/Update Booking Limits
            </a>
            <a href="admin-manage-technician.php" class="btn btn-info">
                <i class="fas fa-users"></i> Manage Technicians
            </a>
            <a href="admin-dashboard.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>
</body>
</html>
