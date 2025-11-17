<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Reset your Electrozot account password">
    <meta name="author" content="Electrozot">

    <title>Forgot Password - Electrozot</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { overflow-x: hidden; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
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
        .logo-section {
            position: fixed;
            top: 20px;
            left: 30px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            animation: slideInLeft 0.6s ease-out;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .logo-section img { height: 40px; width: auto; }
        .logo-section .brand-name {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .forgot-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 0 15px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
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
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
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
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .forgot-header h2 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        .forgot-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }
        .forgot-body { padding: 40px 35px; }
        .info-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .info-box p {
            margin: 0;
            color: #4a5568;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .info-box i {
            color: #667eea;
            margin-right: 8px;
        }
        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .form-group label i {
            color: #667eea;
            margin-right: 8px;
        }
        .form-control {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        .btn-reset {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
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
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .links-section a {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        .links-section a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #667eea;
            transition: width 0.3s ease;
        }
        .links-section a:hover {
            color: #764ba2;
        }
        .links-section a:hover::after {
            width: 100%;
        }
        .links-section a i {
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .logo-section { top: 15px; left: 15px; padding: 10px 15px; }
            .logo-section img { height: 35px; }
            .logo-section .brand-name { font-size: 1.1rem; }
            .forgot-body { padding: 30px 25px; }
            .forgot-header { padding: 35px 25px; }
            .forgot-header h2 { font-size: 1.5rem; }
            .logo-circle { width: 70px; height: 70px; }
            .logo-circle i { font-size: 2rem; }
        }
        @media (max-width: 480px) {
            body { padding: 10px 5px; }
            .logo-section { top: 10px; left: 10px; padding: 8px 12px; gap: 8px; }
            .logo-section img { height: 30px; }
            .logo-section .brand-name { font-size: 1rem; }
            .forgot-card { border-radius: 20px; }
            .forgot-body { padding: 25px 20px; }
            .forgot-header { padding: 30px 20px; }
            .forgot-header h2 { font-size: 1.3rem; }
            .forgot-header p { font-size: 0.9rem; }
            .logo-circle { width: 60px; height: 60px; margin-bottom: 15px; }
            .logo-circle i { font-size: 1.8rem; }
            .form-control { padding: 12px; font-size: 0.95rem; }
            .btn-reset { padding: 13px; font-size: 1rem; }
        }
    </style>
</head>

<body>
    <div class="logo-section">
        <img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
        <span class="brand-name">Electrozot</span>
    </div>

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
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

</html>