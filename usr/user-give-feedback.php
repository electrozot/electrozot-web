<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info
$user_query = "SELECT * FROM tms_user WHERE u_id = ?";
$user_stmt = $mysqli->prepare($user_query);
$user_stmt->bind_param('i', $aid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_object();

// Handle feedback submission
$success = false;
$error = false;
if(isset($_POST['give_feedback'])) {
    $f_uname = $_POST['f_uname'];
    $f_content = $_POST['f_content'];
    $f_rating = isset($_POST['f_rating']) ? $_POST['f_rating'] : 5;
    
    $query = "INSERT INTO tms_feedback (f_uname, f_content) VALUES(?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $f_uname, $f_content);
    
    if($stmt->execute()) {
        $success = true;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Give Feedback - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
            padding-bottom: 70px;
            min-height: 100vh;
        }
        
        .top-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 20px 15px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .logo {
            height: 45px;
            width: auto;
        }
        
        .brand-text h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 11px;
            opacity: 0.85;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        
        .back-btn {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
            flex-shrink: 0;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.35);
            transform: scale(1.05);
        }
        
        .content {
            padding: 20px 15px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .intro-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .intro-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 36px;
            color: white;
        }
        
        .intro-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }
        
        .intro-text {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        .feedback-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .form-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-section-title i {
            color: #6366f1;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .form-control:disabled {
            background: #f8f9fa;
            color: #666;
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .rating-container {
            margin-bottom: 25px;
        }
        
        .rating-stars {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }
        
        .star {
            font-size: 40px;
            color: #e0e0e0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .star:hover,
        .star.active {
            color: #fbbf24;
            transform: scale(1.1);
        }
        
        .rating-text {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            font-weight: 600;
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .success-modal,
        .error-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            background: white;
            border-radius: 25px;
            padding: 40px 30px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
            animation: slideUp 0.3s;
        }
        
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        
        .success-modal .modal-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .error-modal .modal-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .modal-text {
            font-size: 15px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .modal-btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0 8px;
            z-index: 1000;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            text-decoration: none;
            color: #999;
            transition: all 0.3s;
            padding: 5px;
        }
        
        .nav-item.active { color: #667eea; }
        
        .nav-item i {
            font-size: 24px;
            display: block;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 600;
        }
        
        @media (min-width: 768px) {
            .content {
                padding: 30px 20px;
            }
            
            .intro-card {
                padding: 35px;
            }
            
            .feedback-card {
                padding: 35px;
            }
            
            .intro-icon {
                width: 100px;
                height: 100px;
                font-size: 45px;
            }
            
            .intro-title {
                font-size: 26px;
            }
            
            .intro-text {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <?php if($success): ?>
    <div class="success-modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="modal-title">Thank You!</div>
            <div class="modal-text">Your feedback has been submitted successfully. We appreciate your input!</div>
            <a href="user-dashboard.php" class="modal-btn">Back to Dashboard</a>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if($error): ?>
    <div class="error-modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-times"></i>
            </div>
            <div class="modal-title">Oops!</div>
            <div class="modal-text">Something went wrong. Please try again later.</div>
            <button onclick="location.reload()" class="modal-btn">Try Again</button>
        </div>
    </div>
    <?php endif; ?>

    <div class="top-header">
        <div class="header-content">
            <div class="brand-section">
                <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
                <div class="brand-text">
                    <h2>Electrozot</h2>
                    <p>We make perfect</p>
                </div>
            </div>
            <a href="user-dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="content">
        <div class="intro-card">
            <div class="intro-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="intro-title">We Value Your Opinion</div>
            <div class="intro-text">
                Your feedback helps us improve our services and provide better experiences for all our customers.
            </div>
        </div>

        <div class="feedback-card">
            <form method="POST" id="feedbackForm">
                <div class="form-section-title">
                    <i class="fas fa-user-circle"></i>
                    Your Information
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your Name</label>
                    <input type="text" name="f_uname" class="form-control" 
                           value="<?php echo htmlspecialchars($user->u_fname . ' ' . $user->u_lname); ?>" 
                           readonly required>
                </div>

                <div class="form-section-title" style="margin-top: 25px;">
                    <i class="fas fa-star-half-alt"></i>
                    Rate Your Experience
                </div>
                
                <div class="rating-container">
                    <div class="rating-stars">
                        <i class="fas fa-star star" data-rating="1"></i>
                        <i class="fas fa-star star" data-rating="2"></i>
                        <i class="fas fa-star star" data-rating="3"></i>
                        <i class="fas fa-star star" data-rating="4"></i>
                        <i class="fas fa-star star" data-rating="5"></i>
                    </div>
                    <div class="rating-text">Tap to rate</div>
                    <input type="hidden" name="f_rating" id="ratingValue" value="5">
                </div>

                <div class="form-section-title">
                    <i class="fas fa-comment-dots"></i>
                    Your Feedback
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tell us about your experience</label>
                    <textarea name="f_content" class="form-control" 
                              placeholder="Share your thoughts, suggestions, or any issues you faced..." 
                              required></textarea>
                </div>

                <button type="submit" name="give_feedback" class="submit-btn">
                    <i class="fas fa-paper-plane"></i>
                    Submit Feedback
                </button>
            </form>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="user-dashboard.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="book-service-step1.php" class="nav-item">
            <i class="fas fa-calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="user-manage-booking.php" class="nav-item">
            <i class="fas fa-list-alt"></i>
            <span>Orders</span>
        </a>
        <a href="user-view-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>

    <script>
        // Star rating functionality
        const stars = document.querySelectorAll('.star');
        const ratingValue = document.getElementById('ratingValue');
        const ratingText = document.querySelector('.rating-text');
        
        const ratingLabels = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };
        
        // Set initial rating to 5
        updateStars(5);
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingValue.value = rating;
                updateStars(rating);
                ratingText.textContent = ratingLabels[rating];
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                updateStars(rating);
            });
        });
        
        document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
            const currentRating = ratingValue.value;
            updateStars(currentRating);
        });
        
        function updateStars(rating) {
            stars.forEach(star => {
                const starRating = star.getAttribute('data-rating');
                if (starRating <= rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
