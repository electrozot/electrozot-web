<?php
/**
 * Test: Not Done Booking Reassignment
 * Verify that "Not Done" bookings show available technicians for reassignment
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
include('check-technician-availability.php');
check_login();

// Get a "Not Done" booking for testing
$not_done_query = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name, s.s_category
                   FROM tms_service_booking sb
                   LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                   LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                   WHERE sb.sb_status = 'Not Done'
                   ORDER BY sb.sb_not_done_at DESC
                   LIMIT 1";
$not_done_booking = $mysqli->query($not_done_query)->fetch_assoc();

$test_results = [];

if ($not_done_booking) {
    $booking_id = $not_done_booking['sb_id'];
    $service_category = $not_done_booking['s_category'];
    
    // Test 1: Check if technician ID is NULL
    $test_results[] = [
        'test' => 'Technician ID should be NULL for Not Done booking',
        'expected' => 'NULL',
        'actual' => $not_done_booking['sb_technician_id'] ?? 'NULL',
        'status' => empty($not_done_booking['sb_technician_id']) ? 'PASS' : 'FAIL'
    ];
    
    // Test 2: Get available technicians
    $available_techs = getAvailableTechnicians($service_category, $mysqli, $booking_id);
    $test_results[] = [
        'test' => 'Should find available technicians',
        'expected' => '> 0 technicians',
        'actual' => count($available_techs) . ' technicians found',
        'status' => count($available_techs) > 0 ? 'PASS' : 'FAIL'
    ];
    
    // Test 3: Check admin notification was created
    $notif_check = "SELECT COUNT(*) as count FROM tms_admin_notifications 
                    WHERE an_booking_id = ? AND an_type = 'SERVICE_NOT_DONE'";
    $stmt = $mysqli->prepare($notif_check);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $notif_count = $stmt->get_result()->fetch_assoc()['count'];
    
    $test_results[] = [
        'test' => 'Admin notification should be created',
        'expected' => '> 0 notifications',
        'actual' => $notif_count . ' notification(s)',
        'status' => $notif_count > 0 ? 'PASS' : 'FAIL'
    ];
    
} else {
    $test_results[] = [
        'test' => 'Find Not Done booking',
        'expected' => 'At least 1 Not Done booking',
        'actual' => 'No Not Done bookings found',
        'status' => 'SKIP'
    ];
}

// Count all Not Done bookings
$count_query = "SELECT COUNT(*) as total FROM tms_service_booking WHERE sb_status = 'Not Done'";
$total_not_done = $mysqli->query($count_query)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Not Done Booking Reassignment</title>
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
            <h2><i class="fas fa-vial"></i> Not Done Booking Reassignment Test</h2>
            <p class="text-muted">Testing if "Not Done" bookings show available technicians for reassignment</p>
            <div class="alert alert-info">
                <strong>Total "Not Done" Bookings:</strong> <?php echo $total_not_done; ?>
            </div>
        </div>

        <?php if ($not_done_booking): ?>
        <div class="test-card">
            <h4><i class="fas fa-clipboard-list"></i> Test Booking Details</h4>
            <table class="table table-sm">
                <tr>
                    <th>Booking ID:</th>
                    <td><?php echo $not_done_booking['sb_id']; ?></td>
                </tr>
                <tr>
                    <th>Customer:</th>
                    <td><?php echo htmlspecialchars($not_done_booking['u_fname'] . ' ' . $not_done_booking['u_lname']); ?></td>
                </tr>
                <tr>
                    <th>Service:</th>
                    <td><?php echo htmlspecialchars($not_done_booking['s_name']); ?></td>
                </tr>
                <tr>
                    <th>Category:</th>
                    <td><?php echo htmlspecialchars($not_done_booking['s_category']); ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge bg-warning">Not Done</span></td>
                </tr>
                <tr>
                    <th>Technician ID:</th>
                    <td><?php echo $not_done_booking['sb_technician_id'] ?? '<span class="text-success">NULL (Correct!)</span>'; ?></td>
                </tr>
                <tr>
                    <th>Not Done Reason:</th>
                    <td><?php echo htmlspecialchars($not_done_booking['sb_not_done_reason'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Not Done At:</th>
                    <td><?php echo $not_done_booking['sb_not_done_at'] ?? 'N/A'; ?></td>
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

        <?php if ($not_done_booking && isset($available_techs)): ?>
        <div class="test-card">
            <h4><i class="fas fa-users"></i> Available Technicians for Reassignment</h4>
            <?php if (count($available_techs) > 0): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Found <?php echo count($available_techs); ?> available technician(s)
                </div>
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
            <h5><i class="fas fa-info-circle"></i> How "Not Done" Works</h5>
            <ol>
                <li>Technician goes to booking and clicks "Mark as Not Done"</li>
                <li>Provides reason why service couldn't be completed</li>
                <li>System sets:
                    <ul>
                        <li>Status → "Not Done"</li>
                        <li>Technician ID → NULL (unassigned)</li>
                        <li>Technician status → "Available"</li>
                    </ul>
                </li>
                <li>Admin gets notification to reassign</li>
                <li>User gets notification that service will be rescheduled</li>
                <li>Admin can reassign to another technician</li>
            </ol>
        </div>

        <div class="test-card">
            <h5><i class="fas fa-link"></i> Quick Actions</h5>
            <?php if ($not_done_booking): ?>
                <a href="admin-assign-technician.php?sb_id=<?php echo $not_done_booking['sb_id']; ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Reassign This Booking
                </a>
            <?php endif; ?>
            <a href="admin-rejected-bookings.php" class="btn btn-warning">
                <i class="fas fa-times-circle"></i> View All Rejected/Not Done Bookings
            </a>
            <a href="admin-dashboard.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>
</body>
</html>
