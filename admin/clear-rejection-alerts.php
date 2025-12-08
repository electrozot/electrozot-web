<?php
/**
 * Clear Rejection Alert Session Data
 * Use this if you need to reset the alert system
 */

session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Rejection Alerts</title>
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
    <style>
        body { padding: 40px; font-family: Arial, sans-serif; background: #f5f5f5; text-align: center; }
        .card { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; margin: 10px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔄 Clear Rejection Alert Data</h1>
        <p>This will clear browser session storage for rejection alerts.</p>
        <p><strong>Note:</strong> With the new system, this is rarely needed since alerts show until admin takes action.</p>
        
        <div id="status"></div>
        
        <button onclick="clearAlerts()" class="btn btn-danger">
            Clear Session Storage
        </button>
        
        <a href="admin-dashboard.php" class="btn btn-success">
            Back to Dashboard
        </a>
    </div>
    
    <script>
        function clearAlerts() {
            // Clear session storage
            sessionStorage.removeItem('rejection_alert_shown');
            
            // Show success message
            document.getElementById('status').innerHTML = `
                <div class="success">
                    ✅ Session storage cleared successfully!<br>
                    Rejection alerts will now show again if technicians have 3+ unnotified rejections.
                </div>
            `;
            
            console.log('✅ Rejection alert session storage cleared');
        }
    </script>
</body>
</html>
