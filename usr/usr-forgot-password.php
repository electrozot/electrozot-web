<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Reset your Electrozot account password">
    <meta name="author" content="Electrozot">
    <meta name="theme-color" content="#000000">

    <title>Forgot Password - Electrozot</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { overflow-x: hidden; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #FFE4F0 0%, #FFC9E0 50%, #FFB3D9 100%);
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 20px 10px;
        }
        body::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .forgot-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            padding: 0 15px;
            margin: 100px auto 40px;
        }
        .forgot-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .forgot-header {
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            padding: 25px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .forgot-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        .logo-circle {
            width: 65px;
            height: 65px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .logo-circle i {
            font-size: 2rem;
            background: linear-gradient(135deg, #ec6ead 0%, #d13abd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .forgot-header h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        .forgot-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 8px 0 0;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }
        .forgot-body { 
            padding: 30px 35px;
            background: linear-gradient(180deg, #FFFEF0 0%, #FFF9E0 100%);
        }
        .info-box {
            background: linear-gradient(135deg, rgba(236, 110, 173, 0.1) 0%, rgba(209, 58, 189, 0.1) 100%);
            border-left: 4px solid #ec6ead;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 0;
            color: #4a5568;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .info-box i {
            color: #ec6ead;
            margin-right: 8px;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        .form-group label i {
            color: #ec6ead;
            margin-right: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        .form-control:focus {
            outline: none;
            border-color: #ec6ead;
            background: white;
            box-shadow: 0 0 0 4px rgba(236, 110, 173, 0.1);
        }
        .btn-reset {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }
        .btn-reset::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .btn-reset:hover::before {
            width: 300px;
            height: 300px;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }
        .btn-reset span {
            position: relative;
            z-index: 1;
        }
        .links-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
        }
        .btn-link {
            display: inline-block;
            padding: 10px 16px;
            background: white;
            border: 2px solid #ec6ead;
            border-radius: 10px;
            color: #ec6ead !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.15s ease;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(236, 110, 173, 0.15);
            cursor: pointer;
            text-align: center;
            white-space: nowrap;
        }
        .btn-link:hover {
            background: linear-gradient(135deg, #ec6ead 0%, #d13abd 100%);
            color: white !important;
            border-color: #d13abd;
            box-shadow: 0 4px 12px rgba(236, 110, 173, 0.35);
        }
        .btn-link:active {
            background: linear-gradient(135deg, #ff1493 0%, #ff69b4 100%) !important;
            color: white !important;
            border-color: #ff1493;
            transform: scale(0.96);
            box-shadow: 0 2px 8px rgba(255, 20, 147, 0.5);
            transition: all 0.05s ease;
        }
        .btn-link:visited {
            color: #ec6ead !important;
            text-decoration: none;
        }
        .btn-link:visited:hover {
            color: white !important;
        }
        .btn-link:visited:active {
            color: white !important;
        }
        .btn-link i {
            margin-right: 6px;
        }
        .btn-link-home {
            background: white;
            border-color: #667eea;
            color: #667eea !important;
        }
        .btn-link-home:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-color: #764ba2;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.35);
        }
        .btn-link-home:active {
            background: linear-gradient(135deg, #4169e1 0%, #6a5acd 100%) !important;
            color: white !important;
            border-color: #4169e1;
            transform: scale(0.96);
            box-shadow: 0 2px 8px rgba(65, 105, 225, 0.5);
            transition: all 0.05s ease;
        }
        .btn-link-home:visited {
            color: #667eea !important;
        }
        .btn-link-home:visited:hover {
            color: white !important;
        }
        .btn-link-home:visited:active {
            color: white !important;
        }
        @media (max-width: 768px) {
            .forgot-body { padding: 30px 25px; }
            .forgot-header { padding: 35px 25px; }
            .forgot-header h2 { font-size: 1.5rem; }
            .logo-circle { width: 70px; height: 70px; }
            .logo-circle i { font-size: 2rem; }
        }
        @media (max-width: 480px) {
            body { padding: 10px 5px; }
            .forgot-card { border-radius: 20px; }
            .forgot-body { padding: 25px 20px; }
            .forgot-header { padding: 30px 20px; }
            .forgot-header h2 { font-size: 1.3rem; }
            .forgot-header p { font-size: 0.9rem; }
            .logo-circle { width: 60px; height: 60px; margin-bottom: 15px; }
            .logo-circle i { font-size: 1.8rem; }
            .form-control { padding: 12px; font-size: 0.95rem; }
            .btn-reset { padding: 13px; font-size: 1rem; }
            .btn-link { display: block; margin: 10px auto; max-width: 280px; }
        }
    </style>
</head>

<body>
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="forgot-header">
                <div class="logo-circle">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Forgot Password?</h2>
                <p>Don't worry, we'll help you reset it</p>
            </div>
            <div class="forgot-body">
                <div class="info-box">
                    <p><i class="fas fa-info-circle"></i> Enter your registered email address and we'll send you instructions to reset your password.</p>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" class="form-control" name="a_email" placeholder="Enter your registered email" required autofocus>
                    </div>
                    
                    <button type="submit" name="reset-pwd" class="btn-reset">
                        <span><i class="fas fa-paper-plane"></i> Send Reset Instructions</span>
                    </button>
                </form>

                <div class="links-section">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                        <a href="index.php" class="btn-link" style="flex: 1; min-width: 130px;"><i class="fas fa-arrow-left"></i> Login</a>
                        <a href="../index.php" class="btn-link btn-link-home" style="flex: 1; min-width: 130px;"><i class="fas fa-home"></i> Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

</html>