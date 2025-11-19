<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_conditions = [];
$params = [];
$types = '';

if($filter != 'all') {
    // Handle Rejected filter to include both Rejected and Cancelled statuses
    if($filter == 'Rejected') {
        $where_conditions[] = "(sb.sb_status = 'Rejected' OR sb.sb_status = 'Cancelled')";
    } else {
        $where_conditions[] = "sb.sb_status = ?";
        $params[] = $filter;
        $types .= 's';
    }
}

if(!empty($search)) {
    $where_conditions[] = "(u.u_fname LIKE ? OR u.u_lname LIKE ? OR u.u_phone LIKE ? OR s.s_name LIKE ? OR sb.sb_id LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $types .= 'sssss';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM tms_service_booking sb
                LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                $where_clause";

if(!empty($params)) {
    $count_stmt = $mysqli->prepare($count_query);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $count_stmt->close();
} else {
    $count_result = $mysqli->query($count_query);
    $total_records = $count_result->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $per_page);

// Get notifications
$query = "SELECT sb.*, 
                 u.u_fname, u.u_lname, u.u_phone, u.u_email,
                 s.s_name, s.s_category, s.s_price,
                 t.t_name as technician_name, t.t_phone as technician_phone
          FROM tms_service_booking sb
          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
          $where_clause
          ORDER BY sb.sb_created_at DESC
          LIMIT ? OFFSET ?";

if(!empty($params)) {
    $stmt = $mysqli->prepare($query);
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Notifications - Admin Panel</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
    
    <style>
        .notification-card {
            transition: all 0.3s ease;
            border-left: 4px solid #6c757d;
        }
        .notification-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .notification-card.status-pending {
            border-left-color: #ffc107;
            background: linear-gradient(to right, #fff9e6 0%, #ffffff 100%);
        }
        .notification-card.status-approved {
            border-left-color: #17a2b8;
            background: linear-gradient(to right, #e6f7ff 0%, #ffffff 100%);
        }
        .notification-card.status-in-progress {
            border-left-color: #007bff;
            background: linear-gradient(to right, #e6f2ff 0%, #ffffff 100%);
        }
        .notification-card.status-completed {
            border-left-color: #28a745;
            background: linear-gradient(to right, #e6ffe6 0%, #ffffff 100%);
        }
        .notification-card.status-rejected, .notification-card.status-cancelled {
            border-left-color: #dc3545;
            background: linear-gradient(to right, #ffe6e6 0%, #ffffff 100%);
        }
        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .filter-btn {
            margin: 5px;
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        .filter-btn.active {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0"><i class="fas fa-bell text-primary"></i> Notifications Center</h2>
                        <p class="text-muted mb-0">All booking activities and updates</p>
                    </div>
                    <div>
                        <a href="admin-dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Stats Cards - Removed to clean up interface -->

                <!-- Filters -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="form-inline">
                            <div class="form-group mr-3">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="btn-group mr-3" role="group">
                                <a href="?filter=all" class="btn filter-btn <?php echo $filter == 'all' ? 'btn-primary active' : 'btn-outline-primary'; ?>">All</a>
                                <a href="?filter=Pending" class="btn filter-btn <?php echo $filter == 'Pending' ? 'btn-warning active' : 'btn-outline-warning'; ?>">Pending</a>
                                <a href="?filter=Approved" class="btn filter-btn <?php echo $filter == 'Approved' ? 'btn-info active' : 'btn-outline-info'; ?>">Approved</a>
                                <a href="?filter=In Progress" class="btn filter-btn <?php echo $filter == 'In Progress' ? 'btn-primary active' : 'btn-outline-primary'; ?>">In Progress</a>
                                <a href="?filter=Completed" class="btn filter-btn <?php echo $filter == 'Completed' ? 'btn-success active' : 'btn-outline-success'; ?>">Completed</a>
                                <a href="?filter=Rejected" class="btn filter-btn <?php echo $filter == 'Rejected' ? 'btn-danger active' : 'btn-outline-danger'; ?>">Rejected</a>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                        </form>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="notifications-container">
                    <?php
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $status_class = strtolower(str_replace(' ', '-', $row['sb_status']));
                            $customer_name = !empty($row['u_fname']) ? $row['u_fname'] . ' ' . $row['u_lname'] : 'Guest Customer';
                            
                            // Determine icon and color
                            $icon = 'fa-calendar-check';
                            $icon_bg = 'bg-secondary';
                            $status_badge = 'badge-secondary';
                            
                            switch($row['sb_status']) {
                                case 'Pending':
                                    $icon = 'fa-clock';
                                    $icon_bg = 'bg-warning';
                                    $status_badge = 'badge-warning';
                                    break;
                                case 'Approved':
                                case 'Assigned':
                                    $icon = 'fa-check';
                                    $icon_bg = 'bg-info';
                                    $status_badge = 'badge-info';
                                    break;
                                case 'In Progress':
                                    $icon = 'fa-cog fa-spin';
                                    $icon_bg = 'bg-primary';
                                    $status_badge = 'badge-primary';
                                    break;
                                case 'Completed':
                                    $icon = 'fa-check-circle';
                                    $icon_bg = 'bg-success';
                                    $status_badge = 'badge-success';
                                    break;
                                case 'Rejected':
                                case 'Cancelled':
                                    $icon = 'fa-times-circle';
                                    $icon_bg = 'bg-danger';
                                    $status_badge = 'badge-danger';
                                    break;
                            }
                            
                            $time_ago = time_elapsed_string($row['sb_created_at']);
                    ?>
                    <div class="card notification-card status-<?php echo $status_class; ?> mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="notification-icon <?php echo $icon_bg; ?> text-white">
                                        <i class="fas <?php echo $icon; ?>"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1">
                                                <span class="timeline-dot <?php echo $icon_bg; ?>"></span>
                                                Booking #<?php echo $row['sb_id']; ?> - <?php echo htmlspecialchars($row['s_name']); ?>
                                            </h5>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($customer_name); ?> 
                                                <i class="fas fa-phone ml-3"></i> <?php echo htmlspecialchars($row['sb_phone']); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge <?php echo $status_badge; ?> badge-pill px-3 py-2">
                                                <?php echo $row['sb_status']; ?>
                                            </span>
                                            <br>
                                            <small class="text-muted"><?php echo $time_ago; ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <small class="text-muted">Service Category:</small><br>
                                            <strong><?php echo htmlspecialchars($row['s_category']); ?></strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Booking Date:</small><br>
                                            <strong><?php echo date('M d, Y', strtotime($row['sb_booking_date'])); ?></strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Technician:</small><br>
                                            <strong><?php echo !empty($row['technician_name']) ? htmlspecialchars($row['technician_name']) : '<span class="text-danger">Not Assigned</span>'; ?></strong>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($row['sb_address'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> Address:</small>
                                        <span><?php echo htmlspecialchars($row['sb_address']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <a href="admin-view-service-booking.php?sb_id=<?php echo $row['sb_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <?php if($row['sb_status'] == 'Pending' || $row['sb_status'] == 'Approved'): ?>
                                        <a href="admin-assign-technician.php?sb_id=<?php echo $row['sb_id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-user-plus"></i> Assign Technician
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No notifications found.</div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
            <!-- /.container-fluid -->

            <!-- Sticky Footer -->
            <?php include("vendor/inc/footer.php");?>

        </div>
        <!-- /.content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="vendor/js/sb-admin.min.js"></script>

    <!-- Notification System Script -->
    <script>
        $(document).ready(function() {
            console.log('✅ Notification system active on this page');
            
            // Audio notification setup
            let audioContext = null;
            let audioBuffer = null;
            let customSoundEnabled = false;
            
            // Initialize audio context on first user interaction
            function initAudioContext() {
                if (!audioContext) {
                    try {
                        audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        console.log('🔊 Audio context initialized');
                        loadCustomSound();
                    } catch(e) {
                        console.warn('⚠️ Audio context not supported:', e);
                    }
                }
            }
            
            // Load custom notification sound
            function loadCustomSound() {
                // Skip loading external sound file, use Web Audio API directly
                console.log('ℹ️ Using Web Audio API for notifications');
                customSoundEnabled = false;
            }
            
            // Play notification sound (custom or fallback)
            function playNotificationSound() {
                // Try custom sound first
                if (customSoundEnabled && audioContext && audioBuffer) {
                    try {
                        const source = audioContext.createBufferSource();
                        source.buffer = audioBuffer;
                        source.connect(audioContext.destination);
                        source.start(0);
                        console.log('🔊 Custom sound played');
                        return true;
                    } catch(e) {
                        console.warn('⚠️ Custom sound failed:', e);
                    }
                }
                
                // Fallback to Web Audio API beep
                try {
                    if (!audioContext) {
                        audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                    
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.5);
                    
                    console.log('🔊 Web API beep played');
                    return true;
                } catch(e) {
                    console.warn('⚠️ Web Audio API failed:', e);
                    return false;
                }
            }
            
            // Show browser notification
            function showBrowserNotification(title, body, icon) {
                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        const notification = new Notification(title, {
                            body: body,
                            icon: icon || 'vendor/img/logo.png',
                            badge: 'vendor/img/logo.png',
                            tag: 'booking-notification',
                            requireInteraction: false,
                            silent: false
                        });
                        
                        notification.onclick = function() {
                            window.focus();
                            notification.close();
                        };
                        
                        console.log('🔔 Browser notification shown');
                        return true;
                    } catch(e) {
                        console.warn('⚠️ Browser notification failed:', e);
                        return false;
                    }
                }
                return false;
            }
            
            // Request notification permission
            function requestNotificationPermission() {
                if ('Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission().then(permission => {
                        console.log('🔔 Notification permission:', permission);
                        if (permission === 'granted') {
                            showBrowserNotification(
                                'Notifications Enabled',
                                'You will now receive booking notifications',
                                'vendor/img/logo.png'
                            );
                        }
                    });
                }
            }
            
            // Check for new notifications
            let lastCheckTime = Date.now();
            
            function checkNotifications() {
                $.ajax({
                    url: 'check-new-bookings.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.has_new && response.new_count > 0) {
                            console.log('🔔 NEW BOOKINGS:', response.new_count);
                            
                            // Play sound
                            playNotificationSound();
                            
                            // Show browser notification
                            const firstBooking = response.bookings[0];
                            showBrowserNotification(
                                `New Booking #${firstBooking.id}`,
                                `${firstBooking.customer} - ${firstBooking.service}`,
                                'vendor/img/logo.png'
                            );
                            
                            // Show toast notification
                            showToastNotification(response.bookings);
                            
                            // Reload page after 3 seconds to show new data
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Check notifications error:', error);
                    }
                });
            }
            
            // Show toast notification
            function showToastNotification(bookings) {
                const toastContainer = $('<div>')
                    .css({
                        position: 'fixed',
                        top: '80px',
                        right: '20px',
                        zIndex: 9999,
                        maxWidth: '400px'
                    })
                    .appendTo('body');
                
                bookings.forEach((booking, index) => {
                    setTimeout(() => {
                        const toast = $(`
                            <div class="alert alert-success alert-dismissible fade show" style="
                                animation: slideIn 0.5s ease-out;
                                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                margin-bottom: 10px;
                            ">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h5><i class="fas fa-bell"></i> New Booking #${booking.id}</h5>
                                <p class="mb-0">
                                    <strong>${booking.customer}</strong><br>
                                    ${booking.service}<br>
                                    <small>${booking.phone}</small>
                                </p>
                            </div>
                        `);
                        
                        toastContainer.append(toast);
                        
                        // Auto-remove after 5 seconds
                        setTimeout(() => {
                            toast.fadeOut(300, function() {
                                $(this).remove();
                                if (toastContainer.children().length === 0) {
                                    toastContainer.remove();
                                }
                            });
                        }, 5000);
                    }, index * 300);
                });
            }
            
            // Initialize on first user interaction
            let initialized = false;
            $(document).one('click keydown touchstart', function() {
                if (!initialized) {
                    initialized = true;
                    initAudioContext();
                    console.log('✅ Audio system initialized on user interaction');
                }
            });
            
            // Request notification permission on page load
            setTimeout(requestNotificationPermission, 2000);
            
            // Check for new notifications every 15 seconds
            setInterval(checkNotifications, 15000);
            
            // Add animation styles
            $('<style>')
                .text(`
                    @keyframes slideIn {
                        from {
                            transform: translateX(400px);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                `)
                .appendTo('head');
            
            console.log('🔔 Notification monitoring started (15s interval)');
        });
    </script>

</body>
</html>

<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
