<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Business Statistics - ElectroZot</title>
    
    <!-- Bootstrap CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .period-tab {
            cursor: pointer;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 5px;
            background: white;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .period-tab:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .period-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
        .top-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .top-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <?php include("vendor/inc/nav.php"); ?>
    
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php"); ?>
        
        <div id="content-wrapper">
            <div class="container-fluid" style="padding: 30px;">
                
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="color: #2d3748; font-weight: 700;">
                        <i class="fas fa-chart-line" style="color: #667eea;"></i> Business Statistics
                    </h2>
                    <a href="admin-dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                
                <!-- Period Selector -->
                <div class="d-flex flex-wrap mb-4" id="periodSelector">
                    <div class="period-tab active" data-period="today">
                        <i class="fas fa-calendar-day"></i> Today
                    </div>
                    <div class="period-tab" data-period="yesterday">
                        <i class="fas fa-history"></i> Yesterday
                    </div>
                    <div class="period-tab" data-period="week">
                        <i class="fas fa-calendar-week"></i> This Week
                    </div>
                    <div class="period-tab" data-period="month">
                        <i class="fas fa-calendar-alt"></i> This Month
                    </div>
                    <div class="period-tab" data-period="year">
                        <i class="fas fa-calendar"></i> This Year
                    </div>
                </div>
                
                <!-- Stats Content -->
                <div id="statsContent">
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: #667eea;"></i>
                        <p class="mt-3" style="color: #718096;">Loading statistics...</p>
                    </div>
                </div>
                
            </div>
            <?php include("vendor/inc/footer.php"); ?>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load today's stats by default
            loadStats('today');
            
            // Period tab click handler
            $('.period-tab').click(function() {
                $('.period-tab').removeClass('active');
                $(this).addClass('active');
                var period = $(this).data('period');
                loadStats(period);
            });
        });
        
        function loadStats(period) {
            console.log('Loading stats for period:', period);
            $('#statsContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: #667eea;"></i><p class="mt-3" style="color: #718096; font-weight: 600;">Loading statistics...</p></div>');
            
            $.ajax({
                url: 'get-stats-ajax.php',
                method: 'GET',
                data: { period: period },
                dataType: 'html',
                timeout: 10000,
                timeout: 15000,
                success: function(response) {
                    console.log('Stats loaded successfully, response length:', response.length);
                    if (response && response.trim().length > 0) {
                        $('#statsContent').html(response);
                    } else {
                        $('#statsContent').html('<div class="alert alert-warning"><i class="fas fa-info-circle"></i> No data returned from server.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    $('#statsContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Failed to load statistics: ' + status + '<br><small>' + error + '</small><br><button class="btn btn-sm btn-primary mt-2" onclick="loadStats(\'' + period + '\')">Retry</button></div>');
                }
            });
        }
    </script>
</body>
</html>
