<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id == 0) {
    header("Location: admin-manage-bookings.php");
    exit;
}

// Get booking details
$query = "SELECT * FROM tms_service_booking WHERE sb_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    $_SESSION['error'] = "Booking not found";
    header("Location: admin-manage-bookings.php");
    exit;
}

// Handle technician assignment
if (isset($_POST['assign_technician'])) {
    $technician_id = intval($_POST['technician_id']);
    
    // Check if technician has the required skill
    $check_skill_query = "SELECT t_id, t_name, t_skills, t_current_bookings, t_booking_limit 
                          FROM tms_technician 
                          WHERE t_id = ?";
    $stmt = $mysqli->prepare($check_skill_query);
    $stmt->bind_param("i", $technician_id);
    $stmt->execute();
    $tech = $stmt->get_result()->fetch_assoc();
    
    if ($tech) {
        $tech_skills = explode(',', $tech['t_skills']);
        $required_service = $booking['sb_service_name'];
        
        // Check if technician has this skill
        if (in_array($required_service, $tech_skills)) {
            // Check booking limit
            if ($tech['t_current_bookings'] < $tech['t_booking_limit']) {
                // Assign booking
                $update_query = "UPDATE tms_service_booking 
                                SET sb_technician_id = ?, 
                                    sb_status = 'Approved',
                                    sb_assigned_at = NOW()
                                WHERE sb_id = ?";
                $stmt = $mysqli->prepare($update_query);
                $stmt->bind_param("ii", $technician_id, $booking_id);
                
                if ($stmt->execute()) {
                    // Update technician booking count
                    $mysqli->query("UPDATE tms_technician SET t_current_bookings = t_current_bookings + 1 WHERE t_id = $technician_id");
                    
                    $_SESSION['success'] = "Booking assigned to " . $tech['t_name'] . " successfully!";
                    header("Location: admin-manage-bookings.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Error assigning booking";
                }
            } else {
                $_SESSION['error'] = "Technician has reached booking limit (" . $tech['t_booking_limit'] . ")";
            }
        } else {
            $_SESSION['error'] = "Technician does not have the required skill: " . $required_service;
        }
    } else {
        $_SESSION['error'] = "Technician not found";
    }
}

// Get matching technicians (those who have this skill)
$required_service = $booking['sb_service_name'];
$matching_technicians = [];

$query = "SELECT t_id, t_name, t_phone, t_email, t_experience, t_category, t_status, 
                 t_current_bookings, t_booking_limit, t_skills,
                 (t_booking_limit - t_current_bookings) as available_slots
          FROM tms_technician 
          WHERE t_status = 'Available' 
            AND t_current_bookings < t_booking_limit
            AND FIND_IN_SET(?, t_skills) > 0
          ORDER BY available_slots DESC, t_current_bookings ASC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $required_service);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $matching_technicians[] = $row;
}

// Get all technicians (for reference)
$all_technicians_query = "SELECT COUNT(*) as total FROM tms_technician WHERE t_status = 'Available'";
$total_techs = $mysqli->query($all_technicians_query)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assign Booking - Skill Match</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; }
        .booking-card { background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .tech-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #10b981; }
        .tech-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); transition: all 0.3s; }
        .no-match-card { background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 10px; }
        .skill-badge { background: #e0e7ff; color: #667eea; padding: 5px 12px; border-radius: 15px; font-size: 12px; margin: 3px; display: inline-block; }
        .match-badge { background: #10b981; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .slots-badge { background: #f59e0b; color: white; padding: 5px 12px; border-radius: 15px; font-size: 12px; }
        .required-skill { background: #fef3c7; border: 2px solid #f59e0b; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-user-check"></i> Assign Technician</h2>
                    <p class="mb-0">Booking #<?php echo $booking_id; ?> - Skill-Based Matching</p>
                </div>
                <a href="admin-manage-bookings.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Booking Details -->
        <div class="booking-card">
            <h5 class="mb-3"><i class="fas fa-clipboard-list"></i> Booking Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($booking['sb_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['sb_phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($booking['sb_address']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($booking['sb_booking_date'])); ?></p>
                    <p><strong>Time:</strong> <?php echo $booking['sb_booking_time']; ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-warning"><?php echo $booking['sb_status']; ?></span></p>
                </div>
            </div>
        </div>

        <!-- Required Skill -->
        <div class="required-skill">
            <h6><i class="fas fa-star"></i> Required Skill for this Booking:</h6>
            <h4 class="mb-0 text-primary">
                <i class="fas fa-tools"></i> <?php echo htmlspecialchars($required_service); ?>
            </h4>
            <?php if ($booking['sb_category']): ?>
                <small class="text-muted">Category: <?php echo htmlspecialchars($booking['sb_category']); ?> 
                <?php if ($booking['sb_subcategory']): ?>
                    > <?php echo htmlspecialchars($booking['sb_subcategory']); ?>
                <?php endif; ?>
                </small>
            <?php endif; ?>
        </div>

        <!-- Matching Technicians -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle"></i> Matching Technicians 
                    <span class="badge bg-light text-success"><?php echo count($matching_technicians); ?> Found</span>
                    <small class="float-end" style="font-size: 14px;">
                        (<?php echo count($matching_technicians); ?> of <?php echo $total_techs; ?> available technicians have this skill)
                    </small>
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($matching_technicians) > 0): ?>
                    <form method="POST">
                        <?php foreach ($matching_technicians as $tech): ?>
                            <div class="tech-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2">
                                            <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($tech['t_name']); ?>
                                            <span class="match-badge ms-2">
                                                <i class="fas fa-check"></i> Skill Match
                                            </span>
                                        </h5>
                                        <p class="mb-2">
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($tech['t_phone']); ?> | 
                                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($tech['t_email']); ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-briefcase"></i> <?php echo $tech['t_experience']; ?> years experience | 
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($tech['t_category']); ?>
                                        </p>
                                        <p class="mb-2">
                                            <span class="slots-badge">
                                                <i class="fas fa-tasks"></i> Current: <?php echo $tech['t_current_bookings']; ?>/<?php echo $tech['t_booking_limit']; ?> 
                                                (<?php echo $tech['available_slots']; ?> slots available)
                                            </span>
                                        </p>
                                        <div class="mt-2">
                                            <small class="text-muted"><strong>All Skills:</strong></small><br>
                                            <?php 
                                            $skills = explode(',', $tech['t_skills']);
                                            foreach ($skills as $skill): 
                                                $is_required = (trim($skill) == $required_service);
                                            ?>
                                                <span class="skill-badge" style="<?php echo $is_required ? 'background: #10b981; color: white; font-weight: 600;' : ''; ?>">
                                                    <?php if ($is_required): ?><i class="fas fa-star"></i> <?php endif; ?>
                                                    <?php echo htmlspecialchars(trim($skill)); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <button type="submit" name="assign_technician" value="<?php echo $tech['t_id']; ?>" 
                                                class="btn btn-success btn-lg"
                                                onclick="return confirm('Assign this booking to <?php echo htmlspecialchars($tech['t_name']); ?>?')">
                                            <i class="fas fa-check-circle"></i> Assign
                                        </button>
                                        <input type="hidden" name="technician_id" value="<?php echo $tech['t_id']; ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </form>
                <?php else: ?>
                    <div class="no-match-card text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">No Matching Technicians Available</h5>
                        <p class="mb-3">No technician has the skill: <strong><?php echo htmlspecialchars($required_service); ?></strong></p>
                        <p class="text-muted">Please:</p>
                        <ul class="text-start" style="max-width: 500px; margin: 0 auto;">
                            <li>Add this skill to an existing technician, OR</li>
                            <li>Hire a new technician with this skill, OR</li>
                            <li>Contact the customer to reschedule</li>
                        </ul>
                        <div class="mt-4">
                            <a href="admin-manage-technician.php" class="btn btn-primary">
                                <i class="fas fa-users"></i> Manage Technicians
                            </a>
                            <a href="admin-add-technician-with-skills.php" class="btn btn-success">
                                <i class="fas fa-user-plus"></i> Add New Technician
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
