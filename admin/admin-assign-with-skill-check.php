<?php
/**
 * Assign Booking with Skill Matching
 * Shows only technicians who have the required skill
 */
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

$booking_id = isset($_GET['sb_id']) ? intval($_GET['sb_id']) : 0;

if ($booking_id == 0) {
    header("Location: admin-manage-bookings.php");
    exit;
}

// Get booking details
$booking_query = "SELECT sb.*, u.u_fname, u.u_lname, u.u_phone, u.u_addr
                  FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  WHERE sb.sb_id = ?";
$stmt = $mysqli->prepare($booking_query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    $_SESSION['error'] = "Booking not found";
    header("Location: admin-manage-bookings.php");
    exit;
}

// Get the required service/skill from booking
$required_skill = $booking['sb_service_name'] ?? $booking['sb_description'];

// Handle assignment
if (isset($_POST['assign_technician'])) {
    $technician_id = intval($_POST['technician_id']);
    
    // Verify technician has the skill
    $skill_check = "SELECT COUNT(*) as has_skill 
                    FROM tms_technician_skills 
                    WHERE t_id = ? AND skill_name = ?";
    $stmt = $mysqli->prepare($skill_check);
    $stmt->bind_param("is", $technician_id, $required_skill);
    $stmt->execute();
    $has_skill = $stmt->get_result()->fetch_assoc()['has_skill'];
    
    if ($has_skill > 0) {
        // Check booking limit
        $tech_check = "SELECT t_current_bookings, t_booking_limit, t_name 
                       FROM tms_technician WHERE t_id = ?";
        $stmt = $mysqli->prepare($tech_check);
        $stmt->bind_param("i", $technician_id);
        $stmt->execute();
        $tech = $stmt->get_result()->fetch_assoc();
        
        if ($tech['t_current_bookings'] < $tech['t_booking_limit']) {
            // Assign booking
            $update = "UPDATE tms_service_booking 
                       SET sb_technician_id = ?, sb_status = 'Approved', sb_assigned_at = NOW() 
                       WHERE sb_id = ?";
            $stmt = $mysqli->prepare($update);
            $stmt->bind_param("ii", $technician_id, $booking_id);
            
            if ($stmt->execute()) {
                // Increment technician booking count
                $mysqli->query("UPDATE tms_technician SET t_current_bookings = t_current_bookings + 1 WHERE t_id = $technician_id");
                
                $_SESSION['success'] = "Booking assigned to " . $tech['t_name'] . " successfully!";
                header("Location: admin-manage-bookings.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Technician has reached booking limit";
        }
    } else {
        $_SESSION['error'] = "Technician does not have the required skill";
    }
}

// Get matching technicians
$matching_query = "SELECT DISTINCT t.t_id, t.t_name, t.t_phone, t.t_email, t.t_category, 
                          t.t_experience, t.t_status, t.t_current_bookings, t.t_booking_limit,
                          (t.t_booking_limit - t.t_current_bookings) as available_slots,
                          GROUP_CONCAT(ts.skill_name SEPARATOR ', ') as all_skills
                   FROM tms_technician t
                   INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
                   WHERE ts.skill_name = ?
                     AND t.t_status = 'Available'
                     AND t.t_current_bookings < t.t_booking_limit
                   GROUP BY t.t_id
                   ORDER BY available_slots DESC, t.t_current_bookings ASC";

$stmt = $mysqli->prepare($matching_query);
$stmt->bind_param("s", $required_skill);
$stmt->execute();
$matching_technicians = $stmt->get_result();

// Get total available technicians for comparison
$total_query = "SELECT COUNT(*) as total FROM tms_technician WHERE t_status = 'Available'";
$total_techs = $mysqli->query($total_query)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assign Booking - Skill Match</title>
    <?php include('vendor/inc/head.php');?>
    <style>
        .tech-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #10b981; }
        .tech-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); transition: all 0.3s; }
        .skill-match-badge { background: #10b981; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .required-skill-box { background: #fef3c7; border: 2px solid #f59e0b; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <h2 class="mb-4"><i class="fas fa-user-check"></i> Assign Technician - Skill Match</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Booking Details -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Booking #<?php echo $booking_id; ?> Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Customer:</strong> <?php echo htmlspecialchars($booking['u_fname'] . ' ' . $booking['u_lname']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['sb_phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($booking['sb_booking_date'])); ?></p>
                                <p><strong>Time:</strong> <?php echo $booking['sb_booking_time']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Required Skill -->
                <div class="required-skill-box">
                    <h6><i class="fas fa-star"></i> Required Skill:</h6>
                    <h4 class="mb-0 text-primary">
                        <i class="fas fa-tools"></i> <?php echo htmlspecialchars($required_skill); ?>
                    </h4>
                </div>

                <!-- Matching Technicians -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle"></i> Matching Technicians 
                            <span class="badge badge-light text-success"><?php echo $matching_technicians->num_rows; ?> Found</span>
                            <small class="float-right">
                                (<?php echo $matching_technicians->num_rows; ?> of <?php echo $total_techs; ?> available)
                            </small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($matching_technicians->num_rows > 0): ?>
                            <form method="POST">
                                <?php while ($tech = $matching_technicians->fetch_assoc()): ?>
                                    <div class="tech-card">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-2">
                                                    <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($tech['t_name']); ?>
                                                    <span class="skill-match-badge ms-2">
                                                        <i class="fas fa-check"></i> Skill Match
                                                    </span>
                                                </h5>
                                                <p class="mb-2">
                                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($tech['t_phone']); ?> | 
                                                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($tech['t_email']); ?>
                                                </p>
                                                <p class="mb-2">
                                                    <i class="fas fa-briefcase"></i> <?php echo $tech['t_experience']; ?> years | 
                                                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($tech['t_category']); ?>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-tasks"></i> Bookings: <?php echo $tech['t_current_bookings']; ?>/<?php echo $tech['t_booking_limit']; ?> 
                                                        (<?php echo $tech['available_slots']; ?> slots free)
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="ms-3">
                                                <button type="submit" name="assign_technician" class="btn btn-success btn-lg"
                                                        onclick="return confirm('Assign to <?php echo htmlspecialchars($tech['t_name']); ?>?')">
                                                    <i class="fas fa-check-circle"></i> Assign
                                                </button>
                                                <input type="hidden" name="technician_id" value="<?php echo $tech['t_id']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <h5>No Matching Technicians Available</h5>
                                <p>No technician has the skill: <strong><?php echo htmlspecialchars($required_skill); ?></strong></p>
                                <a href="admin-manage-technician.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-users"></i> Manage Technicians
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="admin-manage-bookings.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Bookings
                    </a>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
