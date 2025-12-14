<?php
  session_start();
  include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<?php include("vendor/inc/head.php");?>

<body>

    <!-- Navigation -->
    <?php include("vendor/inc/nav.php");?>
    <!--End Navigation-->

    <!-- Page Content -->
    <div class="container-fluid px-0" style="margin-top: 0px;">
        
        <!-- Header Section -->
        <section class="py-5" style="background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%); color: white;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="display-4 font-weight-bold mb-3">
                            <i class="fas fa-question-circle"></i> Frequently Asked Questions
                        </h1>
                        <p class="lead">Get answers to common questions about our electrical and technical services</p>
                        <div class="mt-4">
                            <a href="#booking-faq" class="btn btn-light btn-lg mr-3 mb-2">
                                <i class="fas fa-calendar-alt"></i> Booking Questions
                            </a>
                            <a href="#service-faq" class="btn btn-outline-light btn-lg mb-2">
                                <i class="fas fa-tools"></i> Service Questions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Content -->
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        
                        <!-- Quick Search -->
                        <div class="card shadow-sm mb-5">
                            <div class="card-body text-center">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-search"></i> Search FAQs
                                </h4>
                                <div class="input-group input-group-lg">
                                    <input type="text" id="faq-search" class="form-control" placeholder="Type your question here..." aria-label="Search FAQs">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking & Scheduling FAQs -->
                        <div id="booking-faq" class="mb-5">
                            <h2 class="text-primary mb-4">
                                <i class="fas fa-calendar-alt"></i> Booking & Scheduling
                            </h2>
                            
                            <div class="accordion" id="bookingAccordion">
                                <!-- FAQ 1 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="booking1">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-booking1">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                How do I book an electrical service with Electrozot?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-booking1" class="collapse" data-parent="#bookingAccordion">
                                        <div class="card-body">
                                            <p>Booking with Electrozot is simple and convenient:</p>
                                            <ul>
                                                <li><strong>Online:</strong> Fill out our booking form on the homepage</li>
                                                <li><strong>Phone:</strong> Call us at <a href="tel:7559606925">7559606925</a></li>
                                                <li><strong>WhatsApp:</strong> Send us a message for quick booking</li>
                                                <li><strong>Walk-in:</strong> Visit our office during business hours</li>
                                            </ul>
                                            <p>We'll confirm your appointment within 30 minutes and assign a qualified technician.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="booking2">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-booking2">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                What are your service hours?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-booking2" class="collapse" data-parent="#bookingAccordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Regular Hours:</h6>
                                                    <ul>
                                                        <li>Monday - Sunday: 7:00 AM - 9:00 PM</li>
                                                        <li>No holidays - We work 365 days</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Emergency Services:</h6>
                                                    <ul>
                                                        <li>24/7 emergency support available</li>
                                                        <li>Additional charges may apply for late hours</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="booking3">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-booking3">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                How quickly can you arrive for service?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-booking3" class="collapse" data-parent="#bookingAccordion">
                                        <div class="card-body">
                                            <div class="alert alert-success">
                                                <h6><i class="fas fa-clock"></i> Response Times:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Emergency:</strong> 30-60 minutes</li>
                                                    <li><strong>Same Day:</strong> 2-4 hours</li>
                                                    <li><strong>Scheduled:</strong> Next available slot</li>
                                                    <li><strong>Peak Hours:</strong> May take longer during busy periods</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Services & Pricing FAQs -->
                        <div id="service-faq" class="mb-5">
                            <h2 class="text-primary mb-4">
                                <i class="fas fa-tools"></i> Services & Pricing
                            </h2>
                            
                            <div class="accordion" id="serviceAccordion">
                                <!-- FAQ 4 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="service1">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-service1">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                What electrical services do you provide?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-service1" class="collapse" data-parent="#serviceAccordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Electrical Services:</h6>
                                                    <ul>
                                                        <li>Wiring & Rewiring</li>
                                                        <li>Switch & Socket Installation</li>
                                                        <li>Fan Installation & Repair</li>
                                                        <li>Light Fitting & LED Installation</li>
                                                        <li>Electrical Panel Upgrades</li>
                                                        <li>Circuit Breaker Repair</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Appliance Services:</h6>
                                                    <ul>
                                                        <li>AC Installation & Repair</li>
                                                        <li>Refrigerator Repair</li>
                                                        <li>Washing Machine Service</li>
                                                        <li>Microwave Repair</li>
                                                        <li>Water Heater Installation</li>
                                                        <li>Home Automation Setup</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="service2">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-service2">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                How do you calculate service charges?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-service2" class="collapse" data-parent="#serviceAccordion">
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-calculator"></i> Transparent Pricing:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Service Charge:</strong> ₹100-200 (varies by location)</li>
                                                    <li><strong>Labor Cost:</strong> Based on work complexity</li>
                                                    <li><strong>Material Cost:</strong> At market rates (optional)</li>
                                                    <li><strong>No Hidden Charges:</strong> Everything discussed upfront</li>
                                                </ul>
                                            </div>
                                            <p><strong>Note:</strong> Final quote provided after technician inspection.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 6 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="service3">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-service3">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                Do you provide warranty on your work?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-service3" class="collapse" data-parent="#serviceAccordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card bg-success text-white">
                                                        <div class="card-body text-center">
                                                            <h5><i class="fas fa-shield-alt"></i> Service Warranty</h5>
                                                            <h3>30 Days</h3>
                                                            <p class="mb-0">On all electrical work</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card bg-primary text-white">
                                                        <div class="card-body text-center">
                                                            <h5><i class="fas fa-tools"></i> Parts Warranty</h5>
                                                            <h3>As Per Brand</h3>
                                                            <p class="mb-0">Manufacturer warranty applies</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment & Safety FAQs -->
                        <div id="payment-faq" class="mb-5">
                            <h2 class="text-primary mb-4">
                                <i class="fas fa-credit-card"></i> Payment & Safety
                            </h2>
                            
                            <div class="accordion" id="paymentAccordion">
                                <!-- FAQ 7 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="payment1">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-payment1">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                What payment methods do you accept?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-payment1" class="collapse" data-parent="#paymentAccordion">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-success">
                                                        <div class="card-body">
                                                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                            <h6>Cash</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-primary">
                                                        <div class="card-body">
                                                            <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                                            <h6>UPI/Digital</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-warning">
                                                        <div class="card-body">
                                                            <i class="fas fa-credit-card fa-2x text-warning mb-2"></i>
                                                            <h6>Card Payment</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-info">
                                                        <div class="card-body">
                                                            <i class="fas fa-university fa-2x text-info mb-2"></i>
                                                            <h6>Bank Transfer</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 8 -->
                                <div class="card faq-item">
                                    <div class="card-header" id="payment2">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-payment2">
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                Are your technicians verified and insured?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-payment2" class="collapse" data-parent="#paymentAccordion">
                                        <div class="card-body">
                                            <div class="alert alert-success">
                                                <h6><i class="fas fa-shield-check"></i> Safety & Verification:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Background Verified:</strong> All technicians undergo verification</li>
                                                    <li><strong>Trained Professionals:</strong> Certified and experienced</li>
                                                    <li><strong>ID Cards:</strong> All technicians carry official ID</li>
                                                    <li><strong>Insurance:</strong> Work covered under service insurance</li>
                                                    <li><strong>Safety Equipment:</strong> Proper tools and safety gear</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Still Have Questions -->
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body py-5">
                                <h3 class="mb-3">
                                    <i class="fas fa-question-circle"></i> Still Have Questions?
                                </h3>
                                <p class="lead mb-4">Our customer support team is here to help you 24/7</p>
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <a href="tel:7559606925" class="btn btn-light btn-lg btn-block">
                                                    <i class="fas fa-phone"></i><br>
                                                    <small>Call Now</small>
                                                </a>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <a href="https://wa.me/917559606925" class="btn btn-success btn-lg btn-block" target="_blank">
                                                    <i class="fab fa-whatsapp"></i><br>
                                                    <small>WhatsApp</small>
                                                </a>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <a href="contact.php" class="btn btn-warning btn-lg btn-block">
                                                    <i class="fas fa-envelope"></i><br>
                                                    <small>Contact Form</small>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <?php include("vendor/inc/footer.php");?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

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

    <!-- FAQ Search Functionality -->
    <script>
        $(document).ready(function() {
            // FAQ Search
            $('#faq-search').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                
                $('.faq-item').each(function() {
                    var faqText = $(this).text().toLowerCase();
                    if (faqText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                
                // Show message if no results
                if ($('.faq-item:visible').length === 0) {
                    if ($('#no-results').length === 0) {
                        $('.container .row .col-lg-10').append(
                            '<div id="no-results" class="alert alert-info text-center">' +
                            '<h5><i class="fas fa-search"></i> No FAQs found</h5>' +
                            '<p>Try different keywords or <a href="contact.php">contact us</a> directly.</p>' +
                            '</div>'
                        );
                    }
                } else {
                    $('#no-results').remove();
                }
            });
            
            // Smooth scroll to sections
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $($(this).attr('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });
            
            // Change plus/minus icons on accordion
            $('.collapse').on('show.bs.collapse', function() {
                $(this).prev().find('.fa-plus-circle').removeClass('fa-plus-circle').addClass('fa-minus-circle');
            });
            
            $('.collapse').on('hide.bs.collapse', function() {
                $(this).prev().find('.fa-minus-circle').removeClass('fa-minus-circle').addClass('fa-plus-circle');
            });
        });
    </script>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>