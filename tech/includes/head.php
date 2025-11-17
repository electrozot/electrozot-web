<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Technician Dashboard - Electrozot</title>
    <link rel="stylesheet" href="../admin/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usr/vendor/fontawesome-free/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #ffd700;
            --success: #38ef7d;
            --danger: #ff4757;
        }
        
        body {
            background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%);
            font-family: 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
        }
        
        .main-content {
            padding: 40px 15px;
        }
        
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid var(--primary);
        }
        
        .page-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #2d3748;
            margin: 0;
        }
        
        .page-header p {
            color: #6c757d;
            margin: 10px 0 0 0;
            font-size: 1rem;
        }
        
        .card-custom {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: none;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, #11998e 0%, var(--success) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(56, 239, 125, 0.4);
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #ff6b9d 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 71, 87, 0.4);
            color: white;
        }
        
        .table-custom {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }
        
        .table-custom thead th {
            border: none;
            padding: 15px;
            font-weight: 700;
        }
        
        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .badge-status {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-pending {
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
            color: white;
        }
        
        .badge-completed {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .badge-cancelled {
            background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
            color: white;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .alert-success-custom {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.1) 0%, rgba(56, 239, 125, 0.1) 100%);
            border-left: 5px solid var(--success);
            color: #2d3748;
        }
        
        .alert-danger-custom {
            background: linear-gradient(135deg, rgba(255, 71, 87, 0.1) 0%, rgba(255, 107, 157, 0.1) 100%);
            border-left: 5px solid var(--danger);
            color: #2d3748;
        }
    </style>
</head>
