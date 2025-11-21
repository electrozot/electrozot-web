<?php
/**
 * Test Script for Detailed Skill Matching System
 * 
 * This script tests the improved technician matching algorithm
 * Run this to verify the system is working correctly
 */

session_start();
include('vendor/inc/config.php');
require_once('vendor/inc/improved-technician-matcher.php');

// Check if admin is logged in
if (!isset($_SESSION['a_id'])) {
    die("Please login as admin first");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Skill Matching System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 20px; }
        .test-card { margin-bottom: 20px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .info { color: #3b82f6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-vial"></i> Skill Matching System Test</h4>
            </div>
            <div class="card-body">
                
                <?php
                // TEST 1: Check if improved matcher file exists
                echo '<div class="test-card card">';
                echo '<div class="card-header"><strong>Test 1:</strong> Check Improved Matcher File</div>';
                echo '<div class="card-body">';
                if (file_exists('vendor/inc/improved-technician-matcher.php')) {
                    echo '<p class="success"><i class="fas fa-check-circle"></i> ✓ File exists: vendor/inc/improved-technician-matcher.php</p>';
                } else {
                    echo '<p class="error"><i class="fas fa-times-circle"></i> ✗ File NOT found: vendor/inc/improved-technician-matcher.php</p>';
                }
                echo '</div></div>';
                
                // TEST 2: Check if functions are available
                echo '<div class="test-card card">';
                echo '<div class="card-header"><strong>Test 2:</strong> Check Functions</div>';
                echo '<div class="card-body">';
                
                $functions = [
                    'getAvailableTechniciansWithSkillAndSlot',
                    'checkTechnicianTimeSlotAvailability',
                    'formatTechniciansWithSkillAndSlot'
                ];
                
                foreach ($functions as $func) {
                    if (function_exists($func)) {
                        echo '<p class="success"><i class="fas fa-check-circle"></i> ✓ Function exists: ' . $func . '</p>';
                    } else {
                        echo '<p class="error"><i class="fas fa-times-circle"></i> ✗ Function NOT found: ' . $func . '</p>';
                    }
                }
                echo '</div></div>';
                
                // TEST 3: Check database tables
                echo '<div class="test-card card">';
                echo '<div class="card-header"><strong>Test 3:</strong> Check Database Tables</div>';
                echo '<div class="card-body">';
                
                $tables = ['tms_technician', 'tms_service', 'tms_service_booking'];
                foreach ($tables as $table) {
                    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
                    if ($result->num_rows > 0) {
                        echo '<p class="success"><i class="fas fa-check-circle"></i> ✓ Table exists: ' . $table . '</p>';
                    } else {
                        echo '<p class="error"><i class="fas fa-times-circle"></i> ✗ Table NOT found: ' . $table . '</p>';
                    }
                }
                echo '</div></div>';
                
                // TEST 4: Check technician skills column
                echo '<div class="test-card card">';
                echo '<div class="card-header"><strong>Test 4:</strong> Check Technician Skills Column</div>';
                echo '<div class="card-body">';
                
                $result = $mysqli->query("SHOW COLUMNS FROM tms_technician LIKE 't_skills'");
                if ($result->num_rows > 0) {
                    echo '<p class="success"><i class="fas fa-check-circle"></i> ✓ Column exists: t_skills</p>';
                    
                    // Check if any technicians have skills
                    $tech_result = $mysqli->query("SELECT COUNT(*) as count FROM tms_technician WHERE t_skills IS NOT NULL AND t_skills != ''");
                    $tech_data = $tech_result->fetch_assoc();
                    echo '<p class="info"><i class="fas fa-info-circle"></i> Technicians with skills: ' . $tech_data['count'] . '</p>';
                } else {
                    echo '<p class="error"><i class="fas fa-times-circle"></i> ✗ Column NOT found: t_skills</p>';
                    echo '<p class="warning"><i class="fas fa-exclamation-triangle"></i> You need to add t_skills column to tms_technician table</p>';
                }
                echo '</div></div>';
                
                // TEST 5: Sample matching test
                echo '<div class="test-card card">';
                echo '<div class="card-header"><strong>Test 5:</strong> Sample Skill Matching</div>';
                echo '<div class="card-body">';
                
                // Get a sample service
                $service_result = $mysqli->query("SELECT s_id, s_name, s_category FROM tms_service LIMIT 1");
                if ($service_result->num_rows > 0) {
                    $service = $service_result->fetch_assoc();
                    echo '<p class="info"><i class="fas fa-info-circle"></i> Testing with service: <strong>' . $service['s_name'] . '</strong></p>';
                    
                    // Test the matching function
                    $test_date = date('Y-m-d');
                    $test_time = '14:00:00';
                    
                    try {
                        $matched_techs = getAvailableTechniciansWithSkillAndSlot(
                            $mysqli,
                            $service['s_id'],
                            $test_date,
                            $test_time,
                            null
                        );
                        
                        echo '<p class="success"><i class="fas fa-check-circle"></i> ✓ Matching function executed successfully</p>';
                        echo '<p class="info"><i class="fas fa-users"></i> Found ' . count($matched_techs) . ' technician(s)</p>';
                        
                        if (count($matched_techs) > 0) {
                            echo '<table class="table table-sm table-bordered mt-2">';
                            echo '<thead><tr><th>Name</th><th>Match Type</th><th>Slot Available</th><th>Message</th></tr></thead>';
                            echo '<tbody>';
                            foreach ($matched_techs as $tech) {
                                $badge_class = $tech['slot_available'] ? 'badge-success' : 'badge-warning';
                                $match_badge = $tech['match_type'] === 'exact_skill' ? 'badge-primary' : 'badge-secondary';
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($tech['t_name']) . '</td>';
                                echo '<td><span class="badge ' . $match_badge . '">' . $tech['match_type'] . '</span></td>';
                                echo '<td><span class="badge ' . $badge_class . '">' . ($tech['slot_available'] ? 'Yes' : 'No') . '</span></td>';
                                echo '<td>' . htmlspecialchars($tech['slot_message']) . '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<p class="warning"><i class="fas fa-exclamation-triangle"></i> No technicians matched. This could mean:</p>';
                            echo '<ul>';
                            echo '<li>No technicians have the skill: ' . $service['s_name'] . '</li>';
                            echo '<li>All technicians are at capacity</li>';
                            echo '<li>Skills need to be added to technicians</li>';
                            echo '</ul>';
                        }
                    } catch (Exception $e) {
                        echo '<p class="error"><i class="fas fa-times-circle"></i> ✗ Error: ' . $e->getMessage() . '</p>';
                    }
                } else {
                    echo '<p class="warning"><i class="fas fa-exclamation-triangle"></i> No services found in database</p>';
                }
                echo '</div></div>';
                
                // TEST 6: Check booking columns
                echo '<div class="test-card card">';
                echo '<div class="card-header"><strong>Test 6:</strong> Check Booking Date/Time Columns</div>';
                echo '<div class="card-body">';
                
                $columns = ['sb_booking_date', 'sb_booking_time'];
                foreach ($columns as $col) {
                    $result = $mysqli->query("SHOW COLUMNS FROM tms_service_booking LIKE '$col'");
                    if ($result->num_rows > 0) {
                        echo '<p class="success"><i class="fas fa-check-circle"></i> ✓ Column exists: ' . $col . '</p>';
                    } else {
                        echo '<p class="error"><i class="fas fa-times-circle"></i> ✗ Column NOT found: ' . $col . '</p>';
                    }
                }
                echo '</div></div>';
                
                // SUMMARY
                echo '<div class="card bg-light">';
                echo '<div class="card-body">';
                echo '<h5><i class="fas fa-clipboard-check"></i> Test Summary</h5>';
                echo '<p>If all tests passed, the skill matching system is ready to use!</p>';
                echo '<p><strong>Next Steps:</strong></p>';
                echo '<ul>';
                echo '<li>Ensure all technicians have skills added in their profiles</li>';
                echo '<li>Test by assigning a real booking</li>';
                echo '<li>Check that time slot conflicts are detected</li>';
                echo '</ul>';
                echo '<hr>';
                echo '<a href="admin-assign-technician.php" class="btn btn-primary"><i class="fas fa-user-cog"></i> Go to Assign Technician</a> ';
                echo '<a href="admin-manage-technician.php" class="btn btn-secondary"><i class="fas fa-users"></i> Manage Technicians</a> ';
                echo '<a href="admin-dashboard.php" class="btn btn-info"><i class="fas fa-home"></i> Dashboard</a>';
                echo '</div></div>';
                ?>
                
            </div>
        </div>
    </div>
</body>
</html>
