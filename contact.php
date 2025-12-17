<?php include('admin/vendor/inc/config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<?php include("vendor/inc/head.php");?>

<body>
    <?php include("vendor/inc/nav.php");?>
    
    <main id="main-content" role="main">
    <!-- Hero Section -->
    <section class="contact-hero" style="background: linear-gradient(135deg, #F0FBFF 0%, #FFE5EE 25%, #E5FFE5 50%, #ff6b9d 75%, #ff4757 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px; position: relative; overflow: hidden;">
        <div class="contact-hero-overlay"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center">
                <h1 class="display-4 font-weight-bold mb-3 contact-title" style="color: #4a5568; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                    <i class="fas fa-envelope-open-text"></i> Contact Us
                </h1>
                <p class="lead contact-subtitle" style="font-size: 1.2rem; color: #5a6c7d; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">
                    Get in touch with us - We'd love to hear from you!
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="margin-top: -30px; position: relative; z-index: 3; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb contact-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="index.php" style="color: #0891b2; text-decoration: none; font-weight: 500; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Contact</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div class="card contact-form-card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header contact-form-header text-white text-center py-4" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #22d3ee 100%);">
                        <h3 class="mb-0">
                            <i class="fas fa-paper-plane"></i> Send us a Message
                        </h3>
                        <p class="mb-0 mt-2" style="opacity: 0.95;">Fill out the form below and we'll get back to you</p>
                    </div>
                    <div class="card-body p-4" style="background: linear-gradient(180deg, #ffffff 0%, #FFF5F7 100%);">
                        <form name="sentMessage" id="contactForm" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group form-group contact-form-group">
                                        <div class="controls">
                                            <label class="contact-label">
                                                <i class="fas fa-user" style="color: #0891b2;"></i> Full Name *
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
                                                <i class="fas fa-phone" style="color: #0891b2;"></i> Phone *
                                            </label>
                                            <input type="tel" class="form-control contact-input" id="phone" required data-validation-required-message="Please enter your phone number." placeholder="Your phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group form-group contact-form-group">
                                <div class="controls">
                                    <label class="contact-label">
                                        <i class="fas fa-envelope" style="color: #0891b2;"></i> Email *
                                    </label>
                                    <input type="email" class="form-control contact-input" id="email" required data-validation-required-message="Please enter your email address." placeholder="Your email">
                                </div>
                            </div>
                            <div class="control-group form-group contact-form-group">
                                <div class="controls">
                                    <label class="contact-label">
                                        <i class="fas fa-comment-alt" style="color: #0891b2;"></i> Message *
                                    </label>
                                    <textarea rows="5" class="form-control contact-input" id="message" required data-validation-required-message="Please enter your message" maxlength="999" style="resize:none" placeholder="Your message..."></textarea>
                                </div>
                            </div>
                            <div id="success"></div>
                            <button type="submit" class="btn btn-block contact-submit-btn text-white" id="sendMessageButton" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #22d3ee 100%); border: none; padding: 12px; font-weight: 600; border-radius: 12px; font-size: 1rem; position: relative; overflow: hidden;">
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
                    <div class="card-header contact-details-header text-white text-center py-4" style="background: linear-gradient(135deg, #7DD3C0 0%, #A7E9F5 100%);">
                        <h3 class="mb-0">
                            <i class="fas fa-address-card"></i> Contact Details
                        </h3>
                    </div>
                    <div class="card-body p-4" style="background: linear-gradient(180deg, #ffffff 0%, #F0FDFA 100%);">
                        <div class="contact-info-item mb-4">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #0d9488;">Address</h5>
                                <p class="mb-0" style="color: #1a202c; line-height: 1.8; font-weight: 600;">
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
                                <h5 class="font-weight-bold mb-2" style="color: #0d9488;">Phone</h5>
                                <p class="mb-0" style="color: #1a202c; font-weight: 600;">
                                    <a href="tel:7559606925" class="contact-link" style="color: #1a202c; font-weight: 700;">7559606925</a>
                                </p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #0d9488;">Email</h5>
                                <p class="mb-0" style="color: #1a202c; font-weight: 600;">
                                    <a href="mailto:electrozot@outlook.com" class="contact-link" style="color: #1a202c; font-weight: 700;">electrozot@outlook.com</a>
                                </p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5 class="font-weight-bold mb-2" style="color: #0d9488;">Business Hours</h5>
                                <p class="mb-0" style="color: #1a202c; line-height: 1.8; font-weight: 600;">
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
    </main>
    <!-- End Main Content -->

    <?php include("vendor/inc/footer.php");?>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/jqBootstrapValidation.js"></script>
    
    <!-- Enhanced Mobile menu fix -->
    <script>
        $(document).ready(function() {
            // Enhanced mobile menu toggle
            $('.navbar-toggler').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var target = $(this).attr('data-target') || $(this).attr('data-bs-target');
                var $target = $(target);
                
                if ($target.length) {
                    $target.toggleClass('show');
                    var isExpanded = $target.hasClass('show');
                    $(this).attr('aria-expanded', isExpanded);
                }
            });
            
            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar').length) {
                    $('.navbar-collapse').removeClass('show');
                    $('.navbar-toggler').attr('aria-expanded', 'false');
                }
            });
            
            // Close menu when clicking on menu items
            $('.navbar-nav .nav-link').on('click', function() {
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
            
            // Close menu with arrow button
            $('.mobile-menu-arrow-close').on('click', function(e) {
                e.preventDefault();
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
            
            // Prevent menu from closing when clicking inside it
            $('.navbar-collapse').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
    <!-- contact_me.js removed - using inline script instead -->

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

        .contact-form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(8, 145, 178, 0.3) !important;
        }

        .contact-details-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(125, 211, 192, 0.3) !important;
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
            border-color: #0891b2;
            box-shadow: 0 0 0 0.3rem rgba(8, 145, 178, 0.2), 0 4px 15px rgba(8, 145, 178, 0.15);
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
            color: #0891b2;
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
            box-shadow: 0 15px 35px rgba(8, 145, 178, 0.5);
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 50%, #0891b2 100%) !important;
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
            background: rgba(125, 211, 192, 0.1);
            transform: translateX(5px);
        }

        .contact-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7DD3C0 0%, #A7E9F5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-right: 15px;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(125, 211, 192, 0.3);
        }

        .contact-info-item:hover .contact-icon-wrapper {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 6px 20px rgba(125, 211, 192, 0.4);
        }

        .contact-info-content {
            flex: 1;
        }

        .contact-link {
            color: #5EBBAA;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .contact-link:hover {
            color: #7DD3C0;
            text-decoration: none;
            transform: translateX(3px);
        }

        .contact-breadcrumb {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }
        
        .contact-breadcrumb .breadcrumb-item {
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        
        .contact-breadcrumb a:hover {
            color: #22d3ee !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }
        
        @media (max-width: 576px) {
            .contact-breadcrumb {
                padding: 10px 15px !important;
                font-size: 0.85rem !important;
            }
            
            .contact-breadcrumb .breadcrumb-item a,
            .contact-breadcrumb .breadcrumb-item {
                font-size: 0.85rem !important;
            }
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
                padding: 100px 0 40px 0 !important;
            }
            
            .display-4 {
                font-size: 2rem;
            }

            .container {
                padding-bottom: 100px !important;
            }
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>

<script
>
$(document).ready(function() {
    $("#contactForm").submit(function(e) {
        e.preventDefault();
        
        var name = $("#name").val();
        var email = $("#email").val();
        var phone = $("#phone").val();
        var message = $("#message").val();
        
        if(name && email && phone && message) {
            $("#sendMessageButton").prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
            
            $.ajax({
                url: "mail/contact_me.php",
                type: "POST",
                data: {
                    name: name,
                    email: email,
                    phone: phone,
                    message: message
                },
                success: function(response) {
                    $("#success").html('<div class="alert alert-success mt-3"><strong>Success!</strong> Your message has been sent. We will get back to you soon!</div>');
                    $("#contactForm")[0].reset();
                    $("#sendMessageButton").prop("disabled", false).html('<i class="fas fa-paper-plane"></i> Send Message');
                },
                error: function() {
                    $("#success").html('<div class="alert alert-danger mt-3"><strong>Error!</strong> Something went wrong. Please try again.</div>');
                    $("#sendMessageButton").prop("disabled", false).html('<i class="fas fa-paper-plane"></i> Send Message');
                }
            });
        }
    });
});
</script>
