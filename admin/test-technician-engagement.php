<?php
/**
 * Test Technician Engagement System
 * 
 * This page demonstrates the one-booking-per-technician rule
 */
session_start();
include('vendor/inc/config.php');
include('check-technician-availability.php');

// Get all technicians with their engagement status
$summary = getTechnicianEngagementSummary($mysqli);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Engagement Status</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .header h1 {
            color: #667eea;
            font-weight: 900;
            margin: 0 0 10px 0;
        }
        
        .header p {
            color: #64748b;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .status-available {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-engaged {
            background: #fee2e2;
            color: #991b1b;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 700;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        tr:hover {
            background: #f8fafc;
        }
        
        .info-box {
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
            border-left: 5px solid #667eea;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .info-box h3 {
            color: #667eea;
            font-weight: 800;
            margin: 0 0 10px 0;
        }
        
        .info-box ul {
            margin: 10px 0 0 20px;
            color: #475569;
        }
        
        .info-box li {
            margin-bottom: 8px;
        }
        
        .btn-back {
            background: white;
            color: #667eea;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 3px solid #667eea;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card h2 {
            font-size: 3rem;
            font-weight: 900;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card p {
            color: #64748b;
            font-weight: 700;
            margin: 10px 0 0 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-check"></i> Technician Engagement Status</h1>
            <p>Real-time view of technician availability and current assignments</p>
        </div>
        
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> One Booking Per Technician Rule</h3>
            <p><strong>A technician can only handle ONE booking at a time, regardless of how it was assigned:</strong></p>
            <ul>
                <li><strong>Fresh Assignment:</strong> Admin assigns a new booking to technician</li>
                <li><strong>Reassignment:</strong> Admin reassigns a rejected/cancelled booking</li>
                <li><strong>Change Technician:</strong> Admin changes technician for an existing booking</li>
            </ul>
            <p><strong>Technician becomes available ONLY when they:</strong></p>
            <ul>
                <li>✅ Complete the booking (mark as "Done")</li>
                <li>❌ Reject the booking (mark as "Not Done")</li>
                <li>🔄 Admin cancels or removes their assignment</li>
            </ul>
        </div>
        
        <?php
        // Calculate statistics
        $total_techs = count($summary);
        $engaged_count = 0;
        $available_count = 0;
        
        foreach($summary as $tech) {
            if($tech['is_engaged']) {
                $engaged_count++;
            } else {
                $available_count++;
            }
        }
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <h2><?php echo $total_techs; ?></h2>
                <p>Total Technicians</p>
            </div>
            <div class="stat-card">
                <h2 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <?php echo $available_count; ?>
                </h2>
                <p>Available</p>
            </div>
            <div class="stat-card">
                <h2 style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <?php echo $engaged_count; ?>
                </h2>
                <p>Engaged</p>
            </div>
        </div>
        
        <div class="card">
            <h3 style="color: #667eea; font-weight: 800; margin-bottom: 20px;">
                <i class="fas fa-list"></i> All Technicians
            </h3>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Engagement</th>
                        <th>Current Booking</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($summary as $tech): ?>
                    <tr>
                        <td><strong>#<?php echo $tech['technician_id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($tech['technician_name']); ?></td>
                        <td><?php echo htmlspecialchars($tech['category']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $tech['status'] == 'Available' ? 'status-available' : 'status-engaged'; ?>">
                                <?php echo $tech['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if($tech['is_engaged']): ?>
                                <span class="status-badge status-engaged">
                                    <i class="fas fa-lock"></i> Engaged
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-available">
                                    <i class="fas fa-check-circle"></i> Free
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($tech['is_engaged']): ?>
                                <a href="admin-view-single-booking.php?sb_id=<?php echo $tech['current_booking_id']; ?>" 
                                   style="color: #667eea; font-weight: 700; text-decoration: none;">
                                    Booking #<?php echo $tech['current_booking_id']; ?>
                                    <span style="color: #64748b; font-size: 0.9rem;">
                                        (<?php echo $tech['current_booking_status']; ?>)
                                    </span>
                                </a>
                            <?php else: ?>
                                <span style="color: #94a3b8;">None</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="admin-dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
