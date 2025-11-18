<?php
/**
 * Test: Rejected Booking Reassignment
 * Verify that rejected bookings show available technicians
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
include('check-technician-availability.php');
check_login();

// Get a rejected booking for testing
$rejected_query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name, s.s_category
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_status IN ('Rejected', 'Rejected by Technician')
                   ORDER BY sb.sb_rejected_at DESC
                   LIMIT 1";
$rejected_booking = $mysqli->query($rejected_query)->fetch_assoc();

$test_results = [];

if ($rejected_booking) {
    $booking_id = $rejected_booking['sb_id'];
    $service_category = $rejected_booking['s_category'];
    $service_name = $rejected_booking['s_name'];
    
    // Test 1: Check if technician ID is NULL
    $test_results[] = [
        'test' => 'Technician ID should be NULL for rejected booking',
        'expected' => 'NULL',
        'actual' => $rejected_booking['sb_technician_id'] ?? 'NULL',
        'status' => empty($rejected_booking['sb_technician_id']) ? 'PASS' : 'FAIL'
    ];
    
    // Test 2: Get available technicians
    $available_techs = getAvailableTechnicians($service_category, $mysqli, $booking_id);
    $test_results[] = [
        'test' => 'Should find available technicians',
        'expected' => '> 0 technicians',
        'actual' => count($available_techs) . ' technicians found',
        'status' => count($available_techs) > 0 ? 'PASS' : 'FAIL'
    ];
    
    // Test 3: Check if technicians are truly available (not engaged)
    $engaged_count = 0;
    foreach ($available_techs as $tech) {
        $engagement = checkTechnicianEngagement($tech['t_id'], $mysqli);
        if ($engagement['is_engaged'] && $engagement['booking_id'] != $booking_id) {
            $engaged_count++;
        }
    }
    
    $test_results[] = [
        'test' => 'Technicians should not be engaged with other bookings',
        'expected' => '0 engaged',
        'actual' => $engaged_count . ' engaged with other bookings',
        'status' => $engaged_count == 0 ? 'PASS' : 'FAIL'
    ];
    
} else {
    $test_results[] = [
        'test' => 'Find rejected booking',
        'expected' => 'At least 1 rejected booking',
        'actual' => 'No rejected bookings found',
        'status' => 'SKIP'
    ];
}

// Get all technicians status
$all_techs_query = "SELECT t.t_id, t.t_name, t.t_status, t.t_category,
                           (SELECT COUNT(*) FROM tms_service_booking sb 
                            WHERE sb.sb_technician_id = t.t_id 
                            AND sb.sb_status NOT IN ('Completed', 'Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done')) as active_bookings
                    FROM tms_technician t
                    ORDER BY t.t_name";
$all_techs = $mysqli->query($all_techs_query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Rejected Booking Reassignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 30px; }
        .test-card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .pass { color: #10b981; font-weight: bold; }
        .fail { color: #ef4444; font-weight: bold; }
        .skip { color: #f59e0b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h2><i class="fas fa-vial"></i> Rejected Booking Reassignment Test</h2>
            <p class="text-muted">Testing if rejected bookings show available technicians for reassignment</p>
        </div>

        <?php if ($rejected_booking): ?>
        <div class="test-card">
            <h4><i class="fas fa-clipboard-list"></i> Test Booking Details</h4>
            <table class="table table-sm">
                <tr>
                    <th>Booking ID:</th>
                    <td><?php echo $rejected_booking['sb_id']; ?></td>
                </tr>
                <tr>
                    <th>Customer:</th>
                    <td><?php echo htmlspecialchars($rejected_booking['u_fname'] . ' ' . $rejected_booking['u_lname']); ?></td>
                </tr>
                <tr>
                    <th>Service:</th>
                    <td><?php echo htmlspecialchars($rejected_booking['s_name']); ?></td>
                </tr>
                <tr>
                    <th>Category:</th>
                    <td><?php echo htmlspecialchars($rejected_booking['s_category']); ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge bg-danger"><?php echo $rejected_booking['sb_status']; ?></span></td>
                </tr>
                <tr>
                    <th>Technician ID:</th>
                    <td><?php echo $rejected_booking['sb_technician_id'] ?? '<span class="text-success">NULL (Correct!)</span>'; ?></td>
                </tr>
                <tr>
                    <th>Rejection Reason:</th>
                    <td><?php echo htmlspecialchars($rejected_booking['sb_rejection_reason'] ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

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
                                <i class="fas fa-minus-circle"></i> SKIP
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($rejected_booking && isset($available_techs)): ?>
        <div class="test-card">
            <h4><i class="fas fa-users"></i> Available Technicians for Reassignment</h4>
            <?php if (count($available_techs) > 0): ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Match Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available_techs as $tech): ?>
                        <tr>
                            <td><?php echo $tech['t_id']; ?></td>
                            <td><?php echo htmlspecialchars($tech['t_name']); ?></td>
                            <td><?php echo htmlspecialchars($tech['t_category']); ?></td>
                            <td>
                                <?php if (isset($tech['match_type']) && $tech['match_type'] == 'skill'): ?>
                                    <span class="badge bg-success">Skill Match</span>
                                <?php elseif (isset($tech['match_type']) && $tech['match_type'] == 'category'): ?>
                                    <span class="badge bg-info">Category Match</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">General</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Available
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No available technicians found!
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="test-card">
            <h4><i class="fas fa-list"></i> All Technicians Status</h4>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Active Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tech = $all_techs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $tech['t_id']; ?></td>
                        <td><?php echo htmlspecialchars($tech['t_name']); ?></td>
                        <td><?php echo htmlspecialchars($tech['t_category']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $tech['t_status'] == 'Available' ? 'success' : 'warning'; ?>">
                                <?php echo $tech['t_status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($tech['active_bookings'] > 0): ?>
                                <span class="badge bg-danger"><?php echo $tech['active_bookings']; ?> active</span>
                            <?php else: ?>
                                <span class="badge bg-success">0 (Free)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="test-card">
            <h5><i class="fas fa-link"></i> Quick Actions</h5>
            <?php if ($rejected_booking): ?>
                <a href="admin-assign-technician.php?sb_id=<?php echo $rejected_booking['sb_id']; ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Reassign This Booking
                </a>
            <?php endif; ?>
            <a href="admin-rejected-bookings.php" class="btn btn-warning">
                <i class="fas fa-times-circle"></i> View All Rejected Bookings
            </a>
            <a href="admin-dashboard.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>
</body>
</html>
