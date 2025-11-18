<!DOCTYPE html>
<html lang="en">

<?php include("vendor/inc/head.php");?>

<body>
    <?php include("vendor/inc/nav.php");?>
    
    <!-- Hero Section -->
    <section class="contact-hero" style="background: linear-gradient(135deg, #e0f29fff 0%, #fc8cb1ff 25%, #aef198ff 50%, #ff6b9d 75%, #ff4757 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 100px 0 60px 0; position: relative; overflow: hidden;">
        <div class="contact-hero-overlay"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center text-white">
                <h1 class="display-4 font-weight-bold mb-3 contact-title" style="text-shadow: 3px 3px 6px rgba(0,0,0,0.3);">
                    <i class="fas fa-envelope-open-text"></i> Contact Us
                </h1>
                <p class="lead contact-subtitle" style="font-size: 1.3rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                    Get in touch with us - We'd love to hear from you!
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="margin-top: -40px; position: relative; z-index: 3;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb contact-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 15px; padding: 15px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #11998e; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-weight: 500;">Contact</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div class="card contact-form-card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header contact-form-header text-white text-center py-4" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <h3 class="mb-0">
                            <i class="fas fa-paper-plane"></i> Send us a Message
                        </h3>
                        <p class="mb-0 mt-2" style="opacity: 0.95;">Fill out the form below and we'll get back to you</p>
                    </div>
                    <div class="card-body p-4" style="background: linear-gradient(180deg, #ffffff 0%, #fff5f7 100%);">
                        <form name="sentMessage" id="contactForm" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group form-group contact-form-group">
                                        <div class="controls">
                                            <label class="contact-label">
                                                <i class="fas fa-user text-danger"></i> Full Name *
                                            </label>
                                            <input type="text" class="form-control contact-input" id="name" required data-validation-required-message="Please enter your name." placeholder="Your name">
                                            <p class="help-block"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="control-group form-group contact-form-group">
                                        <div class="controls">
                                            <label class="contact-label">
                                                <i class="fas fa-phone text-danger"></i> Phone *
                                            </label>
                                            <input type="tel" class="form-control contact-input" id="phone" required data-validation-required-message="Please enter your phone number." placeholder="Your phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group form-group contact-form-group">
                                <div class="controls">
                                    <label class="contact-label">
                                        <i class="fas fa-envelope text-danger"></i> Email *
                                    </label>
                                    <input type="email" class="form-control contact-input" id="email" required data-validation-required-message="Please enter your email address." placeholder="Your email">
                                </div>
                            </div>
                            <div class="control-group form-group contact-form-group">
                                <div class="controls">
                                    <label class="contact-label">
                                        <i class="fas fa-comment-alt text-danger"></i> Message *
                                    </label>
                                    <textarea rows="5" class="form-control contact-input" id="message" required data-validation-required-message="Please enter your message" maxlength="999" style="resize:none" placeholder="Your message..."></textarea>
                                </div>
                            </div>
                            <div id="success"></div>
                            <button type="submit" class="btn btn-block contact-submit-btn text-white" id="sendMessageButton" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; padding: 12px; font-weight: 600; border-radius: 12px; font-size: 1rem; position: relative; overflow: hidden;">
                                <span style="position: relative; z-index: 2;">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Details -->
            <div class="col-lg-4 mb-4">
                <div class="card contact-details-card border-0 shadow-lg h-100" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header contact-details-header text-white text-center py-4" style="background: linear-gradient(135deg, #4facfe 0%, #11998e 100%);">
                        <h3 class="mb-0">
                            <i class="fas fa-address-card"></i> Contact Details
                        </h3>
                    </div>
                    <div class="card-body p-4" style="background: linear-gradient(180deg, #ffffff 0%, #fff5f7 100%);">
                        <div class="contact-info-item mb-4">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #11998e;">Address</h5>
                                <p class="mb-0" style="color: #6c757d; line-height: 1.8;">
                                    Electrozot<br>
                                    Dharamshala
                                </p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #11998e;">Phone
                                <p class="mb-0" style="color: #6c757d;">
                                    <a href="tel:7559606925" class="contact-link">7559606925</a>
                                </p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #11998e;">Email</h5>
                                <p class="mb-0" style="color: #6c757d;">
                                    <a href="mailto:electrozot.in@gmail.com" class="contact-link">electrozot.in@gmail.com</a>
                                </p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #11998e;">Business Hours</h5>
                                <p class="mb-0" style="color: #6c757d; line-height: 1.8;">
                                    Monday - Sunday<br>
                                    7:00 AM to 9:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("vendor/inc/footer.php");?>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <style>
        /* Contact Page Specific Styles */
        .contact-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
            z-index: 1;
        }

        .contact-title {
            animation: fadeInUp 0.8s ease-out;
        }

        .contact-subtitle {
            animation: fadeInUp 1s ease-out;
        }

        .contact-form-card, .contact-details-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .contact-form-card:hover, .contact-details-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(255, 71, 87, 0.3) !important;
        }

        .contact-input {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 14px 18px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 1rem;
            background: white;
        }

        .contact-input:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.3rem rgba(17, 153, 142, 0.2), 0 4px 15px rgba(17, 153, 142, 0.15);
            transform: translateY(-2px);
            background: #fff;
        }

        .contact-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            display: block;
            transition: color 0.3s ease;
        }

        .contact-label i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .contact-form-group:focus-within .contact-label {
            color: #11998e;
        }

        .contact-form-group:focus-within .contact-label i {
            transform: scale(1.2);
        }

        .contact-submit-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .contact-submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .contact-submit-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .contact-submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.5);
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%) !important;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            transition: all 0.3s ease;
            padding: 15px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.5);
        }

        .contact-info-item:hover {
            background: rgba(255, 71, 87, 0.1);
            transform: translateX(5px);
        }

        .contact-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-right: 15px;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
        }

        .contact-info-item:hover .contact-icon-wrapper {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 6px 20px rgba(255, 71, 87, 0.4);
        }

        .contact-info-content {
            flex: 1;
        }

        .contact-link {
            color: #11998e;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .contact-link:hover {
            color: #ff6b9d;
            text-decoration: none;
            transform: translateX(3px);
        }

        .contact-breadcrumb a:hover {
            color: #ff6b9d !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .contact-hero {
                padding: 60px 0 40px 0 !important;
            }
            
            .display-4 {
                font-size: 2rem;
            }
        }
    </style>

</body>

</html>