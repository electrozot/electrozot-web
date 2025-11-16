<?php
session_start();
include('vendor/inc/config.php');

// Get available technicians
$query = "SELECT t_id, t_name, t_phone, t_email, t_service_pincode, t_is_available, t_current_booking_id 
          FROM tms_technician 
          WHERE t_is_available = 1 
          ORDER BY t_name ASC";

$result = $mysqli->query($query);

$available_technicians = [];
while($row = $result->fetch_assoc()) {
    $available_technicians[] = $row;
}

// Return as JSON for AJAX calls
if(isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode($available_technicians);
    exit();
}

// Display as HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Technicians</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        .available { color: #10b981; font-weight: bold; }
        .busy { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Available Technicians for New Bookings</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Service Pincode</th>
                <th>Status</th>
                <th>Current Booking</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($available_technicians) > 0): ?>
                <?php foreach($available_technicians as $tech): ?>
                <tr>
                    <td><?php echo $tech['t_id']; ?></td>
                    <td><?php echo htmlspecialchars($tech['t_name']); ?></td>
                    <td><?php echo htmlspecialchars($tech['t_phone']); ?></td>
                    <td><?php echo htmlspecialchars($tech['t_email']); ?></td>
                    <td><?php echo htmlspecialchars($tech['t_service_pincode']); ?></td>
                    <td class="available">Available</td>
                    <td>None</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #999;">No available technicians at the moment</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <br>
    <p><strong>Total Available:</strong> <?php echo count($available_technicians); ?></p>
    
    <br>
    <a href="admin-add-booking-usr.php" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;">Assign New Booking</a>
</body>
</html>
