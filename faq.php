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

    <!-- Hero Section -->
    <section class="faq-hero" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 25%, #ffffff 50%, #faf5ff 75%, #f3e8ff 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px; position: relative; overflow: hidden;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="faq-hero-overlay"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center">
                <div class="mb-3" style="display: inline-block; background: rgba(220, 20, 60, 0.15); padding: 8px 20px; border-radius: 50px; border: 2px solid #dc143c;">
                    <span style="color: #dc143c; font-weight: 700; font-size: 0.9rem; letter-spacing: 2px;">FAQ</span>
                </div>
                <h1 class="display-4 font-weight-bold mb-3 faq-title" style="color: #000000; font-size: 2.5rem; font-weight: 900;">
                    <i class="fas fa-question-circle" style="color: #dc143c;"></i> Frequently Asked Questions
                </h1>
                <p class="lead faq-subtitle" style="font-size: 1.1rem; color: #000000; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Get answers to common questions about our electrical and technical services
                </p>
                <div class="mt-4">
                    <a href="#booking-faq" class="btn btn-lg mr-3 mb-2" style="background: #e9d5ff; border: none; color: #000000; padding: 12px 25px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
                        <i class="fas fa-calendar-alt"></i> Booking Questions
                    </a>
                    <a href="#service-faq" class="btn btn-lg mb-2" style="background: transparent; border: 2px solid #dc143c; color: #dc143c; padding: 12px 25px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
                        <i class="fas fa-tools"></i> Service Questions
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Page Content -->
    <div class="container-fluid px-0" style="margin-top: -30px; position: relative; z-index: 3;">
        
        <!-- Breadcrumb -->
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb faq-breadcrumb" style="background: #faf5ff; border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid #dc143c; display: flex; flex-wrap: nowrap; align-items: center;">
                    <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                        <a href="index.php" style="color: #dc143c; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #000000; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">FAQ</li>
                </ol>
            </nav>
        </div>

        <!-- FAQ Content -->
        <section class="py-5">
            <div class="container" style="padding-bottom: 80px;">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        
                        <!-- Quick Search -->
                        <div class="card shadow-lg mb-5 border-0" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body text-center" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); padding: 30px;">
                                <h4 class="mb-3" style="color: #000000; font-weight: 700;">
                                    <i class="fas fa-search" style="color: #dc143c;"></i> Search FAQs
                                </h4>
                                <div class="input-group input-group-lg" style="max-width: 600px; margin: 0 auto;">
                                    <input type="text" id="faq-search" class="form-control" placeholder="Type your question here..." aria-label="Search FAQs" style="border-radius: 12px 0 0 12px; border: 2px solid #dc143c; padding: 15px 20px; font-size: 1rem; height: auto;">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background: #dc143c; color: white; border: none; border-radius: 0 12px 12px 0; padding: 15px 20px; height: auto; display: flex; align-items: center;">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking & Scheduling FAQs -->
                        <div id="booking-faq" class="mb-5">
                            <h2 class="mb-4" style="color: #000000; font-weight: 700;">
                                <i class="fas fa-calendar-alt" style="color: #dc143c;"></i> Booking & Scheduling
                            </h2>
                            
                            <div class="accordion" id="bookingAccordion">
                                <!-- FAQ 1 -->
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="booking1" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-booking1" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                How do I book an electrical service with Electrozot?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-booking1" class="collapse" data-parent="#bookingAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <p>Booking with Electrozot is simple and convenient:</p>
                                            <ul>
                                                <li><strong>Online:</strong> Fill out our booking form on the homepage</li>
                                                <li><strong>Phone:</strong> Call us at <a href="tel:7559606925" style="color: #dc143c; font-weight: 600;">7559606925</a></li>
                                                <li><strong>WhatsApp:</strong> Send us a message for quick booking</li>
                                                <li><strong>Walk-in:</strong> Visit our office during business hours</li>
                                            </ul>
                                            <p>We'll confirm your appointment within 30 minutes and assign a qualified technician.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="booking2" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-booking2" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                What are your service hours?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-booking2" class="collapse" data-parent="#bookingAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 style="color: #dc143c; font-weight: 700;">Regular Hours:</h6>
                                                    <ul>
                                                        <li>Monday - Sunday: 7:00 AM - 9:00 PM</li>
                                                        <li>No holidays - We work 365 days</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 style="color: #dc143c; font-weight: 700;">Emergency Services:</h6>
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
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="booking3" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-booking3" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                How quickly can you arrive for service?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-booking3" class="collapse" data-parent="#bookingAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="alert" style="background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%); border: 2px solid #22c55e; border-radius: 12px; color: #166534;">
                                                <h6><i class="fas fa-clock" style="color: #22c55e;"></i> Response Times:</h6>
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
                            <h2 class="mb-4" style="color: #000000; font-weight: 700;">
                                <i class="fas fa-tools" style="color: #dc143c;"></i> Services & Pricing
                            </h2>
                            
                            <div class="accordion" id="serviceAccordion">
                                <!-- FAQ 4 -->
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="service1" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-service1" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                What electrical services do you provide?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-service1" class="collapse" data-parent="#serviceAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 style="color: #dc143c; font-weight: 700;">Electrical Services:</h6>
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
                                                    <h6 style="color: #dc143c; font-weight: 700;">Appliance Services:</h6>
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
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="service2" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-service2" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                How do you calculate service charges?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-service2" class="collapse" data-parent="#serviceAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="alert" style="background: linear-gradient(135deg, #dbeafe 0%, #f0f9ff 100%); border: 2px solid #3b82f6; border-radius: 12px; color: #1e40af;">
                                                <h6><i class="fas fa-calculator" style="color: #3b82f6;"></i> Transparent Pricing:</h6>
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
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="service3" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-service3" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                Do you provide warranty on your work?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-service3" class="collapse" data-parent="#serviceAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card text-white" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 15px;">
                                                        <div class="card-body text-center">
                                                            <h5><i class="fas fa-shield-alt"></i> Service Warranty</h5>
                                                            <h3>30 Days</h3>
                                                            <p class="mb-0">On all electrical work</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card text-white" style="background: linear-gradient(135deg, #dc143c 0%, #8b1538 100%); border-radius: 15px;">
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
                            <h2 class="mb-4" style="color: #000000; font-weight: 700;">
                                <i class="fas fa-credit-card" style="color: #dc143c;"></i> Payment & Safety
                            </h2>
                            
                            <div class="accordion" id="paymentAccordion">
                                <!-- FAQ 7 -->
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="payment1" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-payment1" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                What payment methods do you accept?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-payment1" class="collapse" data-parent="#paymentAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="row text-center">
                                                <div class="col-md-3 mb-3">
                                                    <div class="card" style="border: 2px solid #22c55e; border-radius: 15px;">
                                                        <div class="card-body">
                                                            <i class="fas fa-money-bill-wave fa-2x mb-2" style="color: #22c55e;"></i>
                                                            <h6 style="color: #000000; font-weight: 600;">Cash</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card" style="border: 2px solid #dc143c; border-radius: 15px;">
                                                        <div class="card-body">
                                                            <i class="fas fa-mobile-alt fa-2x mb-2" style="color: #dc143c;"></i>
                                                            <h6 style="color: #000000; font-weight: 600;">UPI/Digital</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card" style="border: 2px solid #f59e0b; border-radius: 15px;">
                                                        <div class="card-body">
                                                            <i class="fas fa-credit-card fa-2x mb-2" style="color: #f59e0b;"></i>
                                                            <h6 style="color: #000000; font-weight: 600;">Card Payment</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card" style="border: 2px solid #3b82f6; border-radius: 15px;">
                                                        <div class="card-body">
                                                            <i class="fas fa-university fa-2x mb-2" style="color: #3b82f6;"></i>
                                                            <h6 style="color: #000000; font-weight: 600;">Bank Transfer</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 8 -->
                                <div class="card faq-item border-0 shadow-lg mb-3" style="border-radius: 15px; overflow: hidden;">
                                    <div class="card-header" id="payment2" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 100%); border: none; padding: 20px;">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-payment2" style="color: #000000; text-decoration: none; font-weight: 600; padding: 0; width: 100%; text-align: left;">
                                                <i class="fas fa-plus-circle mr-2" style="color: #dc143c;"></i>
                                                Are your technicians verified and insured?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-payment2" class="collapse" data-parent="#paymentAccordion">
                                        <div class="card-body" style="background: rgba(255, 255, 255, 0.9); padding: 25px; color: #374151; font-weight: 500;">
                                            <div class="alert" style="background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%); border: 2px solid #22c55e; border-radius: 12px; color: #166534;">
                                                <h6><i class="fas fa-shield-check" style="color: #22c55e;"></i> Safety & Verification:</h6>
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
                        <div class="card border-0 shadow-lg text-center" style="background: linear-gradient(135deg, #E9D5FF 0%, #F3E8FF 50%, #DDD6FE 100%); border-radius: 20px; overflow: hidden;">
                            <div class="card-body py-5" style="color: #6B46C1;">
                                <h3 class="mb-3" style="color: #6B46C1; font-weight: 700;">
                                    <i class="fas fa-question-circle" style="color: #8B5CF6;"></i> Still Have Questions?
                                </h3>
                                <p class="lead mb-4" style="color: #7C3AED; font-weight: 500;">Our customer support team is here to help you 24/7</p>
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <a href="tel:7559606925" class="btn btn-light btn-lg btn-block" style="border-radius: 15px; color: #6B46C1; font-weight: 600; padding: 15px; border: 2px solid #8B5CF6;">
                                                    <i class="fas fa-phone"></i><br>
                                                    <small>Call Now</small>
                                                </a>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <a href="https://wa.me/917559606925" class="btn btn-success btn-lg btn-block" target="_blank" style="border-radius: 15px; font-weight: 600; padding: 15px;">
                                                    <i class="fab fa-whatsapp"></i><br>
                                                    <small>WhatsApp</small>
                                                </a>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <a href="contact.php" class="btn btn-lg btn-block" style="background: white; border: 2px solid #8B5CF6; color: #6B46C1; border-radius: 15px; font-weight: 600; padding: 15px;">
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
                            '<div id="no-results" class="alert text-center" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); border: 2px solid #dc143c; border-radius: 15px; color: #000000;">' +
                            '<h5><i class="fas fa-search" style="color: #dc143c;"></i> No FAQs found</h5>' +
                            '<p>Try different keywords or <a href="contact.php" style="color: #dc143c; font-weight: 600;">contact us</a> directly.</p>' +
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

    <style>
        /* FAQ Page Specific Styles */
        .faq-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
            z-index: 1;
        }

        .faq-title {
            animation: fadeInUp 0.8s ease-out;
        }

        .faq-subtitle {
            animation: fadeInUp 1s ease-out;
        }

        .faq-item {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .faq-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(220, 20, 60, 0.3) !important;
        }

        .faq-breadcrumb {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }
        
        .faq-breadcrumb .breadcrumb-item {
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        
        .faq-breadcrumb a:hover {
            color: #8b1538 !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
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
            .faq-hero {
                padding: 100px 0 40px 0 !important;
            }
            
            .display-4 {
                font-size: 2rem;
            }

            .container {
                padding-bottom: 100px !important;
            }
            
            .faq-breadcrumb {
                padding: 10px 15px !important;
                font-size: 0.85rem !important;
            }
            
            .faq-breadcrumb .breadcrumb-item a,
            .faq-breadcrumb .breadcrumb-item {
                font-size: 0.85rem !important;
            }
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>