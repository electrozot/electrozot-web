<?php
session_start();
require_once('../admin/vendor/inc/config.php');

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$phone = isset($_GET['phone']) ? $_GET['phone'] : (isset($_SESSION['u_phone']) ? $_SESSION['u_phone'] : '');

if (!$booking_id || !$phone) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Booking Status - EZ Technician</title>
    <link href="../admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../admin/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .status-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 600px;
            margin: 0 auto;
        }
        .status-header {
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .status-icon {
            font-size: 80px;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .status-body {
            padding: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .timeline {
            margin-top: 30px;
        }
        .timeline-item {
            display: flex;
            margin-bottom: 20px;
            position: relative;
        }
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #ddd;
        }
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            z-index: 1;
        }
        .timeline-content {
            flex: 1;
        }
        .timeline-event {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .timeline-time {
            font-size: 0.85rem;
            color: #666;
        }
        .refresh-indicator {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #666;
        }
        .technician-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        .btn-action {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-card">
            <div class="status-header" id="statusHeader">
                <div class="status-icon" id="statusIcon">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <h3 id="statusMessage">Loading...</h3>
                <p id="statusDescription" class="mb-0"></p>
            </div>
            
            <div class="status-body">
                <h5><i class="fas fa-info-circle"></i> Booking Details</h5>
                <div id="bookingDetails">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    </div>
                </div>
                
                <div id="technicianInfo" style="display: none;">
                    <h5 class="mt-4"><i class="fas fa-user-cog"></i> Assigned Technician</h5>
                    <div class="technician-card" id="technicianCard"></div>
                </div>
                
                <div id="timelineSection" style="display: none;">
                    <h5 class="mt-4"><i class="fas fa-history"></i> Booking Timeline</h5>
                    <div class="timeline" id="timeline"></div>
                </div>
                
                <div class="refresh-indicator">
                    <i class="fas fa-sync-alt"></i> Auto-refreshing every 10 seconds
                    <br><small>Last updated: <span id="lastUpdate">-</span></small>
                </div>
                
                <div id="actionButtons"></div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="user-manage-booking.php" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back to My Bookings
            </a>
        </div>
    </div>

    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const bookingId = <?php echo $booking_id; ?>;
        const phone = '<?php echo htmlspecialchars($phone); ?>';
        
        // Status color mapping
        const statusColors = {
            'warning': '#ffc107',
            'info': '#17a2b8',
            'primary': '#007bff',
            'success': '#28a745',
            'danger': '#dc3545',
            'secondary': '#6c757d'
        };
        
        function loadBookingStatus() {
            $.ajax({
                url: 'api-get-booking-status.php',
                method: 'GET',
                data: { booking_id: bookingId, phone: phone },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateUI(response.booking);
                    } else {
                        showError(response.message);
                    }
                },
                error: function() {
                    showError('Failed to load booking status');
                }
            });
        }
        
        function updateUI(booking) {
            // Update header
            const headerColor = statusColors[booking.status_color] || '#6c757d';
            $('#statusHeader').css('background', `linear-gradient(135deg, ${headerColor} 0%, ${adjustColor(headerColor, -20)} 100%)`);
            $('#statusIcon').html(`<i class="fas ${booking.status_icon}"></i>`);
            $('#statusMessage').text(booking.status_message);
            $('#statusDescription').text(booking.status_description);
            
            // Update booking details
            let detailsHTML = `
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-hashtag"></i> Booking ID</span>
                    <span class="info-value">#${booking.id}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-tools"></i> Service</span>
                    <span class="info-value">${booking.service_name}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar"></i> Date</span>
                    <span class="info-value">${booking.booking_date}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-clock"></i> Time</span>
                    <span class="info-value">${booking.booking_time}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-rupee-sign"></i> Total Price</span>
                    <span class="info-value">₹${booking.total_price}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-map-marker-alt"></i> Address</span>
                    <span class="info-value">${booking.address}</span>
                </div>
            `;
            $('#bookingDetails').html(detailsHTML);
            
            // Update technician info
            if (booking.technician) {
                let techHTML = `
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${booking.technician.name}</h6>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-id-badge"></i> ${booking.technician.ez_id}<br>
                                <i class="fas fa-phone"></i> ${booking.technician.phone}
                            </p>
                        </div>
                    </div>
                `;
                $('#technicianCard').html(techHTML);
                $('#technicianInfo').show();
            } else {
                $('#technicianInfo').hide();
            }
            
            // Update timeline
            if (booking.timeline && booking.timeline.length > 0) {
                let timelineHTML = '';
                booking.timeline.forEach(item => {
                    timelineHTML += `
                        <div class="timeline-item">
                            <div class="timeline-icon bg-${item.color} text-white">
                                <i class="fas ${item.icon}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-event">${item.event}</div>
                                <div class="timeline-time">${item.timestamp}</div>
                                ${item.note ? `<small class="text-muted">${item.note}</small>` : ''}
                            </div>
                        </div>
                    `;
                });
                $('#timeline').html(timelineHTML);
                $('#timelineSection').show();
            }
            
            // Update action buttons
            let buttonsHTML = '';
            if (booking.can_cancel) {
                buttonsHTML = `
                    <a href="user-cancel-service-booking.php?sb_id=${booking.id}" 
                       class="btn btn-danger btn-action"
                       onclick="return confirm('Are you sure you want to cancel this booking?')">
                        <i class="fas fa-ban"></i> Cancel Booking
                    </a>
                `;
            }
            $('#actionButtons').html(buttonsHTML);
            
            // Update last update time
            $('#lastUpdate').text(new Date().toLocaleTimeString());
        }
        
        function adjustColor(color, amount) {
            return '#' + color.replace(/^#/, '').replace(/../g, color => ('0'+Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
        }
        
        function showError(message) {
            $('#statusIcon').html('<i class="fas fa-exclamation-triangle"></i>');
            $('#statusMessage').text('Error');
            $('#statusDescription').text(message);
            $('#bookingDetails').html(`<div class="alert alert-danger">${message}</div>`);
        }
        
        // Load status on page load
        loadBookingStatus();
        
        // Auto-refresh every 10 seconds
        setInterval(loadBookingStatus, 10000);
    </script>
</body>
</html>
