<?php
session_start();
include('../admin/vendor/inc/config.php');
include('includes/checklogin.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Favicon Check - Electrozot</title>
    
    <!-- Favicon -->
    <?php include('includes/favicon.php'); ?>
    
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        .check-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .back-btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="check-container">
        <h1>🎯 Favicon Status Check</h1>
        
        <div class="status success">
            ✅ <strong>Favicon files loaded successfully!</strong><br>
            Check your browser tab - you should see the Electrozot icon.
        </div>
        
        <div class="status info">
            📋 <strong>Files being loaded:</strong><br>
            • favicon.ico (main favicon)<br>
            • favicon-16x16.png<br>
            • favicon-32x32.png<br>
            • favicon-96x96.png<br>
            • Apple touch icons<br>
            • Android icons
        </div>
        
        <div class="status info">
            🔍 <strong>If favicon doesn't show:</strong><br>
            1. Clear browser cache (Ctrl+F5)<br>
            2. Close and reopen browser<br>
            3. Try incognito/private mode<br>
            4. Check different browser
        </div>
        
        <p><a href="dashboard.php" class="back-btn">🏠 Back to Dashboard</a></p>
        
        <script>
            // Test favicon loading
            console.log('🔍 Testing favicon loading...');
            
            // Test if favicon.ico exists
            fetch('../vendor/img/icons/favicon.ico')
                .then(response => {
                    if (response.ok) {
                        console.log('✅ favicon.ico loaded successfully');
                    } else {
                        console.error('❌ favicon.ico failed to load');
                    }
                })
                .catch(error => {
                    console.error('❌ favicon.ico error:', error);
                });
        </script>
    </div>
</body>
</html>