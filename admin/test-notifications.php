<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Create test notification
if(isset($_GET['create'])) {
    $test_title = "Test Notification";
    $test_message = "This is a test notification created at " . date('H:i:s');
    $test_type = "TEST";
    
    $stmt = $mysqli->prepare("INSERT INTO tms_admin_notifications (an_type, an_title, an_message, an_booking_id, an_technician_id) VALUES (?, ?, ?, NULL, NULL)");
    $stmt->bind_param('sss', $test_type, $test_title, $test_message);
    
    if($stmt->execute()) {
        $success = "Test notification created!";
    } else {
        $error = "Failed to create notification: " . $mysqli->error;
    }
}

// Get all notifications
$query = "SELECT * FROM tms_admin_notifications ORDER BY an_created_at DESC LIMIT 20";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Notifications</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2><i class="fas fa-bell"></i> Notification System Test</h2>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5>Create Test Notification</h5>
                <a href="?create=1" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Test Notification
                </a>
                <a href="admin-dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>All Notifications (Last 20)</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Read</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($notif = $result->fetch_object()): ?>
                        <tr class="<?php echo $notif->an_is_read ? '' : 'table-warning'; ?>">
                            <td><?php echo $notif->an_id; ?></td>
                            <td><span class="badge badge-info"><?php echo $notif->an_type; ?></span></td>
                            <td><?php echo htmlspecialchars($notif->an_title); ?></td>
                            <td><?php echo htmlspecialchars($notif->an_message); ?></td>
                            <td><?php echo $notif->an_is_read ? '✓ Read' : '✗ Unread'; ?></td>
                            <td><?php echo $notif->an_created_at; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5>API Test</h5>
            </div>
            <div class="card-body">
                <button onclick="testAPI()" class="btn btn-success">
                    <i class="fas fa-play"></i> Test API
                </button>
                <button onclick="testSound()" class="btn btn-warning">
                    <i class="fas fa-volume-up"></i> Test Sound
                </button>
                <pre id="apiResult" class="mt-3 bg-light p-3" style="display:none;"></pre>
            </div>
        </div>
    </div>
    
    <script>
    function testAPI() {
        fetch('api-admin-notifications.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('apiResult').style.display = 'block';
                document.getElementById('apiResult').textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                document.getElementById('apiResult').style.display = 'block';
                document.getElementById('apiResult').textContent = 'Error: ' + error;
            });
    }
    
    function testSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
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
            
            alert('Sound played! If you didn\'t hear it, check your volume.');
        } catch(e) {
            alert('Sound failed: ' + e.message);
        }
    }
    </script>
</body>
</html>
