<?php
  session_start();
  include('admin/vendor/inc/config.php');
  //include('vendor/inc/checklogin.php');
  //check_login();
  //$aid=$_SESSION['a_id'];
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include("vendor/inc/head.php");?>

<body>

    <!-- Navigation -->
    <?php include("vendor/inc/nav.php");?>
    <!--End Navigation-->

    <!-- Page Content -->
    <div class="container-fluid px-0" style="margin-top: -56px;">
        
        <!-- Enhanced Hero Section with Background Elements -->
        <section class="hero-section-enhanced" style="background: linear-gradient(135deg, #95e3f3ff 0%, #FECDD3 25%, #a9f6ceff 50%, #f783c5ff 75%, #FED7D7 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 100px 0 80px 0; position: relative; overflow: hidden;">
            
            <style>
                @keyframes gradientShift {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                
                @keyframes textGradientShift {
                    0% { background-position: 0% center; }
                    100% { background-position: 200% center; }
                }
                
                /* Mobile responsive heading */
                @media (max-width: 768px) {
                    .hero-title {
                        font-size: 3rem !important;
                        padding: 0 15px !important;
                    }
                    
                    .hero-description {
                        font-size: 0.85rem !important;
                        line-height: 1.5 !important;
                        padding: 0 15px !important;
                    }
                    
                    .hero-buttons-wrapper {
                        display: flex !important;
                        flex-direction: row !important;
                        justify-content: center !important;
                        gap: 10px !important;
                        flex-wrap: wrap !important;
                    }
                    
                    .hero-btn {
                        flex: 0 1 auto !important;
                        min-width: 150px !important;
                        padding: 10px 20px !important;
                        font-size: 0.85rem !important;
                    }
                }
                
                @media (max-width: 576px) {
                    .hero-title {
                        font-size: 2.5rem !important;
                        padding: 0 10px !important;
                    }
                    
                    .hero-description {
                        font-size: 0.75rem !important;
                        line-height: 1.5 !important;
                        padding: 0 10px !important;
                        word-wrap: break-word !important;
                        text-align: center !important;
                    }
                    
                    .hero-buttons-wrapper {
                        display: flex !important;
                        flex-direction: row !important;
                        justify-content: center !important;
                        gap: 8px !important;
                        flex-wrap: wrap !important;
                        padding: 0 10px !important;
                    }
                    
                    .hero-btn {
                        flex: 1 1 auto !important;
                        min-width: 140px !important;
                        max-width: 48% !important;
                        padding: 10px 15px !important;
                        font-size: 0.8rem !important;
                        white-space: nowrap !important;
                    }
                    
                    .hero-btn i {
                        font-size: 0.75rem !important;
                    }
                }
                
                /* Extra small screens (below 400px) */
                @media (max-width: 400px) {
                    .hero-title {
                        font-size: 2rem !important;
                        padding: 0 5px !important;
                    }
                    
                    .hero-description {
                        font-size: 0.7rem !important;
                        line-height: 1.4 !important;
                        padding: 0 5px !important;
                    }
                    
                    .hero-description br {
                        display: inline !important;
                    }
                    
                    .hero-description br::after {
                        content: " " !important;
                    }
                    
                    .hero-btn {
                        min-width: 120px !important;
                        padding: 8px 12px !important;
                        font-size: 0.75rem !important;
                    }
                }
            </style>
            
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0 hero-content" style="padding-right: 30px;">
                        <h1 class="display-1 mb-4 hero-title" style="color: #2d3748; font-size: 4.5rem; text-align: center; font-weight: 400;">
                            <span class="electrozot-animated">Electrozot</span>
                        </h1>
                        <p class="lead mb-4 hero-description" style="font-size: 0.95rem; line-height: 1.6; color: #5B4A7D; text-shadow: 1px 1px 3px rgba(255,255,255,0.6); font-weight: 700; text-align: center; padding: 0 15px;">
                            Your Trusted Partner for electrical & plumbing services,<br> Quality Service & Certified Technicians.<br>We Deliver Perfection in Every Job.
                        </p>
                        <div class="hero-buttons-wrapper d-flex flex-wrap justify-content-center" style="gap: 12px;">
                            <a href="#booking-form" id="book-service-btn" class="feature-badge hero-btn" role="button" aria-label="Book service now" style="text-decoration: none; background: linear-gradient(135deg, #E88A77 0%, #E89BB8 50%, #A876D3 100%); color: #2d3748; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; min-width: 160px; text-align: center; border: none;">
                                <i class="fas fa-bolt" style="color: #000000;"></i> Book Service
                            </a>
                            <a href="tel:7559606925" class="feature-badge hero-btn" style="text-decoration: none; background: linear-gradient(135deg, #E88A77 0%, #E89BB8 50%, #A876D3 100%); color: #2d3748; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; min-width: 190px; text-align: center; border: none;">
                                <i class="fas fa-phone" style="color: #000000;"></i> 7559606925
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="booking-card" id="booking-form">
                            <div class="card shadow-lg border-0 booking-form-card" style="border-radius: 20px; overflow: hidden; position: relative; border: 3px solid #E88A77;">
                                
                                <div class="card-header text-center py-3 booking-header" style="background: linear-gradient(135deg, #E88A77 0%, #E89BB8 50%, #A876D3 100%); position: relative; z-index: 3; border-bottom: 3px solid #E88A77; overflow: hidden; box-shadow: 0 4px 15px rgba(232, 138, 119, 0.4);">
                                    <h3 class="mb-0 font-weight-bold" style="color: white; font-size: 1.5rem; font-family: 'Segoe UI', sans-serif; position: relative; z-index: 2; text-shadow: 2px 2px 6px rgba(0,0,0,0.3);">
                                        <i class="fas fa-calendar-check" style="color: white; margin-right: 8px;"></i>
                                        Book Service Now
                                    </h3>
                                </div>
                                <div class="card-body p-3" style="position: relative; z-index: 2; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);">
                                    <?php if(isset($_SESSION['booking_success'])) { ?>
                                        <div class="alert alert-success alert-dismissible fade show py-2" role="alert" style="font-size: 0.9rem;">
                                            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <?php if(isset($_SESSION['booking_error'])) { ?>
                                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert" style="font-size: 0.9rem;">
                                            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <form method="POST" action="process-guest-booking.php" class="booking-form-compact" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-phone"></i> Phone Number *</label>
                                                    <input type="tel" class="form-control" id="guest_phone" name="customer_phone" required placeholder="" maxlength="10" pattern="^[0-9]{10}$" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                                    <small id="customer_status" class="form-text"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-user"></i> Full Name *</label>
                                                    <input type="text" class="form-control" id="guest_name" name="customer_name">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-map-marker-alt"></i> Area / Locality *</label>
                                                    <input type="text" class="form-control" id="guest_area" name="customer_area" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-map-pin"></i> Pincode *</label>
                                                    <input type="text" class="form-control" id="guest_pincode" name="customer_pincode"  maxlength="6" pattern="^[0-9]{6}$" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-th-large"></i> Service Category *</label>
                                                    <select class="form-control service-dropdown" id="guestServiceSubcategory" required>
                                                        <option value="">-- Choose Service Type --</option>
                                                        <optgroup label="⚡ ELECTRICAL">
                                                            <option value="Wiring & Fixtures">Wiring & Fixtures</option>
                                                            <option value="Safety & Power">Safety & Power</option>
                                                        </optgroup>
                                                        <optgroup label="🔧 REPAIR">
                                                            <option value="Major Appliances">Major Appliances</option>
                                                            <option value="Other Gadgets">Other Gadgets</option>
                                                        </optgroup>
                                                        <optgroup label="🔌 INSTALL">
                                                            <option value="Appliance Setup">Appliance Setup</option>
                                                            <option value="Tech & Security">Tech & Security</option>
                                                        </optgroup>
                                                        <optgroup label="🛠️ MAINTAIN">
                                                            <option value="Routine Care">Routine Care</option>
                                                        </optgroup>
                                                        <optgroup label="💧 PLUMBING">
                                                            <option value="Fixtures & Taps">Fixtures & Taps</option>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-tools"></i> Specific Service *</label>
                                                    <select class="form-control service-dropdown" name="sb_service_id" id="guestService" disabled required>
                                                        <option value="">First select category above</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-2" id="otherServiceDiv" style="display: none;">
                                                    <label class="form-label-compact"><i class="fas fa-edit"></i> Custom Service *</label>
                                                    <input type="text" class="form-control" name="other_service_name" id="otherServiceInput" placeholder="Describe your service requirement">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-home"></i> Service Address *</label>
                                                    <textarea class="form-control" id="guest_address" name="sb_address" rows="2" , Building, Street"></textarea>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="form-label-compact"><i class="fas fa-comment-dots"></i> Additional Notes</label>
                                                    <textarea class="form-control" name="sb_description" rows="2" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Hidden field to store selected subcategory -->
                                        <input type="hidden" name="sb_subcategory" id="guestSubcategoryHidden" value="">
                                        <div class="text-center mt-2">
                                            <button type="submit" name="book_service_guest" class="btn btn-primary btn-sm px-4 booking-submit-btn" id="submitBookingBtn" style="background: #10b981; border: none; font-weight: 600; padding: 8px 30px; transition: all 0.3s ease; position: relative; overflow: hidden; color: white;">
                                                <i class="fas fa-paper-plane" id="submitIcon"></i> <span id="submitText">Submit Booking</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <style>
            /* Compact Form Styling - Enhanced Design */
            .booking-form-compact .form-label-compact {
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                color: #495057 !important;
                margin-bottom: 2px !important;
                display: block !important;
            }
            
            .booking-form-compact .form-label-compact i {
                color: #dc143c !important;
                margin-right: 5px !important;
            }
            
            .booking-form-compact .form-control {
                font-size: 1rem !important;
                padding: 6px 10px !important;
                height: auto !important;
                min-height: 36px !important;
                border-radius: 8px !important;
                border: 2px solid #d1d5db !important;
                background: linear-gradient(to bottom, #ffffff, #f9fafb) !important;
                font-weight: 500;
                width: 100% !important;
                box-sizing: border-box !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            }
            
            .booking-form-compact .form-control:hover {
                border-color: #9ca3af !important;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08) !important;
            }
            
            .booking-form-compact .form-control:focus {
                border-color: #dc143c !important;
                background: #ffffff !important;
                box-shadow: 0 0 0 4px rgba(220, 20, 60, 0.15), 0 4px 10px rgba(220, 20, 60, 0.1) !important;
                outline: none !important;
                transform: translateY(-1px) !important;
            }
            
            .booking-form-compact textarea.form-control {
                padding: 6px 10px !important;
                line-height: 1.5;
                font-size: 1rem !important;
                font-weight: 500;
                width: 100% !important;
                resize: vertical !important;
            }
            
            .booking-form-compact select.form-control {
                font-size: 1rem !important;
                font-weight: 500;
                width: 100% !important;
                padding: 6px 10px !important;
                min-height: 36px !important;
                cursor: pointer !important;
                background: linear-gradient(to bottom, #ffffff, #f9fafb) !important;
            }
            
            .booking-form-compact .form-group {
                margin-bottom: 0.5rem !important;
                width: 100% !important;
                position: relative !important;
            }
            
            /* Add icon styling inside inputs */
            .booking-form-compact .form-group::before {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.85rem;
                pointer-events: none;
                z-index: 1;
            }
            
            /* Ensure columns align properly */
            .booking-form-compact .row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin-left: -15px !important;
                margin-right: -15px !important;
            }
            
            .booking-form-compact .col-md-6 {
                padding-left: 15px !important;
                padding-right: 15px !important;
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
            
            .booking-form-compact .form-control::placeholder {
                font-size: 0.85rem;
                color: #9ca3af;
                font-weight: 400;
            }
            
            /* Enhanced card styling */
            .booking-form-card {
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
                border-radius: 20px !important;
            }
            
            .card-body {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(20px) !important;
            }
            
            .booking-form-compact select.form-control option {
                font-size: 1.05rem;
                padding: 10px;
            }
            
            .booking-form-compact select.form-control optgroup {
                font-size: 1rem;
                font-weight: 600;
            }
            
            /* Fix for service dropdown - stable rendering with smooth opening */
            .service-dropdown {
                width: 100% !important;
                max-width: 100% !important;
                appearance: auto !important;
                -webkit-appearance: menulist !important;
                -moz-appearance: menulist !important;
                transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
            }
            
            .service-dropdown option {
                padding: 10px !important;
                background: white !important;
                color: #333 !important;
                white-space: normal !important;
                line-height: 1.5 !important;
            }
            
            .service-dropdown optgroup {
                font-weight: 700 !important;
                color: #000 !important;
                background: #f0f0f0 !important;
                padding: 8px 5px !important;
            }
            
            /* Smooth focus effect */
            .service-dropdown:focus {
                outline: none !important;
                border-color: #dc143c !important;
                box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.15) !important;
                transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
            }
            
            /* Smooth hover effect */
            .service-dropdown:hover {
                border-color: #a0a0a0 !important;
                transition: border-color 0.2s ease !important;
            }
            
            /* Custom service input styling */
            #otherServiceDiv {
                animation: slideDown 0.3s ease-out;
            }
            
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            #otherServiceInput {
                border: 2px solid #ffc107 !important;
                background: linear-gradient(to bottom, #fffbf0, #fff8e1) !important;
            }
            
            #otherServiceInput:focus {
                border-color: #ff9800 !important;
                box-shadow: 0 0 0 4px rgba(255, 152, 0, 0.2) !important;
            }
            
            /* Submit button hover effect */
            .booking-submit-btn:hover {
                background: #059669 !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6) !important;
            }
            
            .booking-submit-btn:active {
                transform: translateY(0) scale(0.95) !important;
            }
            
            /* Click success animation */
            @keyframes successPulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); box-shadow: 0 0 20px rgba(30, 41, 59, 0.8); }
                100% { transform: scale(1); }
            }
            
            .booking-submit-btn.clicked {
                animation: successPulse 0.6s ease;
            }
            
            /* Button shine effect */
            .booking-submit-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }
            
            .booking-submit-btn:hover::before {
                left: 100%;
            }
            
            /* Compact alert messages */
            .booking-form-compact + .alert {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            /* Reduce row spacing */
            .booking-form-compact .row {
                margin-bottom: 0;
            }
            
            /* Mobile Responsive Styles for Small Screens */
            @media (max-width: 576px) {
                /* Hero section mobile adjustments */
                .hero-section-enhanced {
                    padding: 80px 0 40px 0 !important;
                }
                
                .hero-content {
                    padding-right: 15px !important;
                    margin-bottom: 20px !important;
                }
                
                .hero-description {
                    font-size: 0.85rem !important;
                    line-height: 1.4 !important;
                }
                
                /* Reduce booking form width on small screens */
                .booking-card {
                    max-width: 100% !important;
                    margin: 0 auto !important;
                    padding: 0 10px !important;
                }
                
                .booking-form-card {
                    margin: 0 auto !important;
                    border-radius: 15px !important;
                }
                
                /* Compact spacing for mobile - keep text size same */
                .booking-form-compact .form-label-compact {
                    margin-bottom: 2px !important;
                    font-size: 0.7rem !important;
                }
                
                .booking-form-compact .form-group {
                    margin-bottom: 0.5rem !important;
                }
                
                /* Compact card on mobile */
                .card-body.p-3 {
                    padding: 0.75rem !important;
                }
                
                .card-header.py-3 {
                    padding: 0.6rem !important;
                }
                
                .card-header h3 {
                    font-size: 1rem !important;
                }
                
                .card-header h3 i {
                    font-size: 0.9rem !important;
                }
                
                /* Keep two columns on mobile like desktop */
                .booking-form-compact .col-md-6 {
                    padding-left: 5px !important;
                    padding-right: 5px !important;
                    flex: 0 0 50% !important;
                    max-width: 50% !important;
                }
                
                .booking-form-compact .row {
                    margin-left: -5px !important;
                    margin-right: -5px !important;
                }
                
                /* Ensure all form controls are full width and aligned on mobile */
                .booking-form-compact .form-control,
                .booking-form-compact select.form-control,
                .booking-form-compact textarea.form-control,
                .service-dropdown {
                    width: 100% !important;
                    max-width: 100% !important;
                    font-size: 0.85rem !important;
                    padding: 5px 8px !important;
                    min-height: 32px !important;
                }
                
                .booking-form-compact textarea.form-control {
                    font-size: 0.85rem !important;
                    padding: 5px 8px !important;
                }
                
                /* Hide duplicate dropdown error by ensuring proper spacing */
                .booking-form-compact .form-group + .form-group {
                    margin-top: 0.5rem !important;
                }
                
                /* Submit button mobile */
                .booking-submit-btn {
                    padding: 8px 20px !important;
                    font-size: 0.85rem !important;
                }
                
                /* Section spacing mobile */
                .features-section,
                .services-section {
                    padding: 15px 0 10px 0 !important;
                }
                
                .section-title {
                    font-size: 1.3rem !important;
                    margin-bottom: 15px !important;
                }
                
                /* Feature cards mobile */
                .feature-card {
                    margin-bottom: 15px !important;
                }
                
                .feature-card .card-body {
                    padding: 15px !important;
                }
                
                .feature-card .card-title {
                    font-size: 0.95rem !important;
                }
                
                .feature-card .card-text {
                    font-size: 0.8rem !important;
                }
                
                .feature-icon {
                    width: 45px !important;
                    height: 45px !important;
                    font-size: 1.3rem !important;
                }
                
                /* Service cards mobile */
                .service-card {
                    margin-bottom: 15px !important;
                }
                
                .service-img-wrapper {
                    height: 120px !important;
                }
                
                .service-icon {
                    font-size: 2.5rem !important;
                }
                
                .service-card .card-body {
                    padding: 15px !important;
                }
                
                .service-card .card-title {
                    font-size: 0.95rem !important;
                }
                
                .service-card .card-text {
                    font-size: 0.8rem !important;
                    margin-bottom: 10px !important;
                }
                
                .price-badge {
                    font-size: 0.85rem !important;
                    padding: 5px 12px !important;
                }
                
                /* Container padding mobile */
                .container-fluid {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                
                .container {
                    padding-left: 15px !important;
                    padding-right: 15px !important;
                }
                
                /* Portfolio section mobile */
                #portfolioSection {
                    padding: 15px 0 0 0 !important;
                }
                
                #workCarousel {
                    border-radius: 8px !important;
                }
            }
            
            /* Extra small screens (very small phones) */
            @media (max-width: 375px) {
                .hero-title {
                    font-size: 2.5rem !important;
                }
                
                .hero-btn {
                    padding: 6px 10px !important;
                    font-size: 0.75rem !important;
                    min-width: 0 !important;
                }
                
                .booking-form-compact .form-label-compact {
                    font-size: 0.65rem !important;
                }
                
                .booking-form-compact .form-control,
                .booking-form-compact select.form-control,
                .booking-form-compact textarea.form-control {
                    font-size: 0.8rem !important;
                    padding: 4px 6px !important;
                    min-height: 30px !important;
                }
                
                .card-header h3 {
                    font-size: 0.9rem !important;
                }
                
                .section-title {
                    font-size: 1.1rem !important;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('book-service-btn');
                var bookingAnchor = document.getElementById('booking-form');
                var nameInput = document.querySelector('#booking-form input[name="customer_name"]');

                function scrollToBookingForm() {
                    if (!bookingAnchor) return;
                    var header = document.querySelector('nav.navbar.fixed-top');
                    var offset = (header ? header.offsetHeight : 0) + 20; // extra spacing
                    var top = bookingAnchor.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                    // Briefly highlight the form container
                    bookingAnchor.classList.add('blink-form');
                    setTimeout(function(){ bookingAnchor.classList.remove('blink-form'); }, 1200);
                    // Focus the Name input shortly after scroll begins
                    setTimeout(function() { if (nameInput) { nameInput.focus(); } }, 400);
                }

                if (btn && bookingAnchor) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Blink the button briefly
                        btn.classList.add('blink-button');
                        scrollToBookingForm();
                        setTimeout(function() { btn.classList.remove('blink-button'); }, 600);
                    });
                }

                // Smooth scroll when clicking service cards
                var serviceLinks = document.querySelectorAll('.service-card-link');
                if (serviceLinks && bookingAnchor) {
                    serviceLinks.forEach(function(link){
                        link.addEventListener('click', function(e){
                            e.preventDefault();
                            scrollToBookingForm();
                        });
                    });
                }

                // If the page loads with #booking-form hash, adjust to account for fixed header
                if (bookingAnchor && window.location.hash === '#booking-form') {
                    setTimeout(scrollToBookingForm, 100);
                }
                
                // Simplified Service Dropdowns - Direct Subcategory to Service
                var subcategorySelect = document.getElementById('guestServiceSubcategory');
                var serviceSelect = document.getElementById('guestService');
                
                if(subcategorySelect && serviceSelect) {
                    var otherServiceDiv = document.getElementById('otherServiceDiv');
                    var otherServiceInput = document.getElementById('otherServiceInput');
                    
                    // Handle subcategory change - load services via AJAX
                    subcategorySelect.addEventListener('change', function() {
                        var subcategory = this.value;
                        
                        console.log('Selected subcategory:', subcategory);
                        
                        // Update hidden field with selected subcategory
                        var hiddenSubcategory = document.getElementById('guestSubcategoryHidden');
                        if(hiddenSubcategory) {
                            hiddenSubcategory.value = subcategory;
                        }
                        
                        serviceSelect.innerHTML = '<option value="">Loading...</option>';
                        serviceSelect.disabled = true;
                        
                        // Hide other service input when category changes
                        if(otherServiceDiv) {
                            otherServiceDiv.style.display = 'none';
                            if(otherServiceInput) {
                                otherServiceInput.removeAttribute('required');
                                otherServiceInput.value = '';
                            }
                        }
                        
                        if(subcategory) {
                            fetch('admin/get-services-by-subcategory.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'subcategory=' + encodeURIComponent(subcategory)
                            })
                            .then(response => {
                                console.log('Response status:', response.status);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Response data:', data);
                                if(data.success && data.services && data.services.length > 0) {
                                    serviceSelect.innerHTML = '<option value="">Select service...</option>';
                                    data.services.forEach(function(service) {
                                        var option = document.createElement('option');
                                        option.value = service.id;
                                        
                                        // Show full service name (gadget_name) as it is
                                        var displayName = service.gadget_name || service.name;
                                        
                                        option.textContent = displayName;
                                        option.title = displayName; // Show full name on hover
                                        serviceSelect.appendChild(option);
                                    });
                                    // Reset min-width and enable
                                    serviceSelect.style.minWidth = '';
                                    serviceSelect.disabled = false;
                                } else {
                                    console.warn('No services found or error:', data);
                                    serviceSelect.innerHTML = '<option value="">No services available</option>';
                                    serviceSelect.style.minWidth = '';
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                serviceSelect.innerHTML = '<option value="">Error loading services</option>';
                            });
                        } else {
                            serviceSelect.innerHTML = '<option value="">Select service type first...</option>';
                            serviceSelect.disabled = true;
                        }
                    });
                    
                    // Handle service selection - show/hide "Other" input
                    serviceSelect.addEventListener('change', function() {
                        var selectedValue = this.value;
                        
                        if(selectedValue === 'other') {
                            // Show the custom service input
                            if(otherServiceDiv) {
                                otherServiceDiv.style.display = 'block';
                                if(otherServiceInput) {
                                    otherServiceInput.setAttribute('required', 'required');
                                    otherServiceInput.focus();
                                }
                            }
                        } else {
                            // Hide the custom service input
                            if(otherServiceDiv) {
                                otherServiceDiv.style.display = 'none';
                                if(otherServiceInput) {
                                    otherServiceInput.removeAttribute('required');
                                    otherServiceInput.value = '';
                                }
                            }
                        }
                    });
                }
                

            });
        </script>

        <!-- Features Section -->
        <section class="features-section" style="background: linear-gradient(180deg, #ffffff 0%, #f0f4ff 100%); position: relative; padding: 20px 0 15px 0;">
            <div class="container">
                <div class="row text-center mb-3">
                    <div class="col-12">
                        <h2 class="font-weight-bold mb-2 section-title" style="font-size: 1.5rem;">
                            <span class="gradient-text-2">Why Choose Electrozot?</span>
                        </h2>
                        <p class="d-none d-md-block" style="color: #6c757d; font-weight: 500; font-size: 0.9rem; margin-bottom: 0;">Professional service you can trust</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="feature-card card h-100 border-0 feature-card-1" style="border-radius: 15px; overflow: hidden; position: relative; background: linear-gradient(135deg, #FDF5FF 0%, #FAE8FF 100%);">
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-2 icon-bounce" style="font-size: 1.5rem; color: #A855F7; background: linear-gradient(135deg, #FAE8FF 0%, #F3E8FF 100%); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #7C3AED; font-size: 1rem;">Professional Trained Teams</h5>
                                <p class="card-text" style="color: #475569; line-height: 1.5; font-size: 0.85rem;">We have professional trained teams and experts for every service. Our skilled technicians are certified and experienced.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="feature-card card h-100 border-0 feature-card-2" style="border-radius: 15px; overflow: hidden; position: relative; background: linear-gradient(135deg, #FFF5F7 0%, #FFE4E9 100%);">
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-2 icon-bounce" style="animation-delay: 0.2s; font-size: 1.5rem; color: #EC4899; background: linear-gradient(135deg, #FFE4E9 0%, #FECDD6 100%); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #DB2777; font-size: 1rem;">On-Time & Affordable Service</h5>
                                <p class="card-text" style="color: #475569; line-height: 1.5; font-size: 0.85rem;">
                                    We commit our service on time with affordable and transparent prices. No hidden charges, just honest pricing and punctual delivery.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="feature-card card h-100 border-0 feature-card-3" style="border-radius: 15px; overflow: hidden; position: relative; background: linear-gradient(135deg, #FCF5FF 0%, #F5E8FF 100%);">
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-2 icon-bounce" style="animation-delay: 0.4s; font-size: 1.5rem; color: #9333EA; background: linear-gradient(135deg, #F5E8FF 0%, #EDD5FF 100%); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #7E22CE; font-size: 1rem;">1 Month Warranty & Trust</h5>
                                <p class="card-text" style="color: #475569; line-height: 1.5; font-size: 0.85rem;">
                                    We provide 1 month warranty on all repairs and parts. Your satisfaction is guaranteed with comprehensive warranty coverage.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Portfolio Section -->
        <section class="services-section" style="background: linear-gradient(180deg, #f0f4ff 0%, #ffffff 100%); padding: 20px 0 15px 0;">
            <div class="container">
                <div class="row text-center mb-3">
                    <div class="col-12">
                        <h2 class="font-weight-bold mb-2 section-title" style="font-size: 1.5rem;">
                            <span class="gradient-text-2">Our Popular Services</span>
                        </h2>
                        <p class="d-none d-md-block" style="color: #6c757d; font-weight: 500; font-size: 0.9rem; margin-bottom: 0;">Expert technicians for all your needs</p>
                    </div>
                </div>
                <div class="row">
                    <?php
                    // Get popular services (marked by admin)
                    $ret="SELECT DISTINCT s.* FROM tms_service s WHERE s.s_status = 'Active' AND s.is_popular = 1 ORDER BY s.s_id DESC LIMIT 3";
                    $stmt= $mysqli->prepare($ret);
                    $stmt->execute();
                    $res=$stmt->get_result();
                    
                    // If no popular services marked, show latest 3 active services
                    if($res->num_rows == 0) {
                        $ret="SELECT DISTINCT s.* FROM tms_service s WHERE s.s_status = 'Active' ORDER BY s.s_id DESC LIMIT 3";
                        $stmt= $mysqli->prepare($ret);
                        $stmt->execute();
                        $res=$stmt->get_result();
                    }
                    $cnt=1;
                    $gradients = [
                        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                    ];
                    while($row=$res->fetch_object()) {
                        $gradient = $gradients[($cnt-1) % count($gradients)];
                    ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="#booking-form" class="service-card-link" style="text-decoration: none; display: block;">
                        <div class="service-card card h-100 border-0 service-card-hover" style="border-radius: 15px; overflow: hidden; position: relative; cursor: pointer;">
                            <div class="service-gradient-overlay" style="background: <?php echo $gradient; ?>;"></div>
                            <div class="card-img-wrapper service-img-wrapper" style="height: 150px; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tools text-white service-icon" style="font-size: 3.5rem; position: relative; z-index: 2; transition: all 0.4s ease;"></i>
                                <div class="service-shine"></div>
                            </div>
                            <div class="card-body p-3" style="background: white; position: relative; z-index: 2;">
                                <h4 class="card-title font-weight-bold mb-2 service-title" style="color: #2d3748; transition: color 0.3s ease; font-size: 1.1rem;">
                                    <?php echo $row->s_name; ?>
                                </h4>
                                <p class="text-muted mb-3" style="line-height: 1.5; font-size: 0.85rem;"><?php echo substr($row->s_description, 0, 80); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price-badge px-3 py-1" style="background: <?php echo $gradient; ?>; color: white; font-size: 0.95rem; font-weight: 700; border-radius: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);">
                                        ₹<?php echo number_format($row->s_price, 0); ?>
                                    </span>
                                    <span class="text-muted" style="font-weight: 500; font-size: 0.8rem;">
                                        <i class="fas fa-clock"></i> <?php echo $row->s_duration; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                    <?php $cnt++; } ?>
                </div>
            </div>
        </section>

        <!-- Our Work Showcase Section -->
        <section id="portfolioSection" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%); padding: 15px 0 0 0; margin-bottom: 0;">
            <div class="container" style="padding-bottom: 0; margin-bottom: 0;">
                <div class="row text-center" style="margin-bottom: 10px;">
                    <div class="col-12">
                        <h2 class="font-weight-bold" style="color: #2d3748; font-size: 1.3rem; margin-bottom: 5px;">
                            <span class="gradient-text-2">Our Work</span> Portfolio
                        </h2>
                        <p class="text-muted d-none d-md-block" style="font-size: 0.9rem; margin-bottom: 0;">See the quality of our completed projects</p>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 0;">
                    <div class="col-lg-10 mx-auto" style="padding-bottom: 0; margin-bottom: 0;">
                        <?php
                        // Get active sliders from database
                        $slider_query = "SELECT * FROM tms_home_slider WHERE slider_status = 'Active' ORDER BY slider_order ASC, slider_id DESC";
                        $slider_result = $mysqli->query($slider_query);
                        $slider_count = $slider_result ? $slider_result->num_rows : 0;
                        ?>
                        
                        <?php if($slider_count > 0): ?>
                            <div id="workCarousel" class="carousel slide shadow" data-ride="carousel" style="border-radius: 10px; overflow: hidden; margin: 0; padding: 0; height: 250px;">
                                <!-- Indicators -->
                                <ol class="carousel-indicators d-none d-md-flex">
                                    <?php 
                                    $slider_result->data_seek(0);
                                    $index = 0;
                                    while($slider = $slider_result->fetch_object()): 
                                    ?>
                                        <li data-target="#workCarousel" data-slide-to="<?php echo $index; ?>" class="<?php echo $index == 0 ? 'active' : ''; ?>"></li>
                                    <?php 
                                        $index++;
                                    endwhile; 
                                    ?>
                                </ol>

                                <!-- Slides -->
                                <div class="carousel-inner" style="margin: 0; padding: 0; height: 250px;">
                                    <?php 
                                    $slider_result->data_seek(0);
                                    $index = 0;
                                    while($slider = $slider_result->fetch_object()): 
                                    ?>
                                        <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>" style="height: 250px; margin: 0; padding: 0; position: relative;">
                                            <img src="admin/vendor/img/slider/<?php echo $slider->slider_image; ?>" 
                                                 class="d-block w-100" 
                                                 alt="<?php echo htmlspecialchars($slider->slider_title); ?>" 
                                                 style="height: 250px; object-fit: cover; display: block; margin: 0; padding: 0;">
                                            <div class="carousel-caption d-block" style="background: rgba(0,0,0,0.8); padding: 6px 10px; border-radius: 6px; bottom: 8px; left: 8px; right: 8px; z-index: 10; position: absolute;">
                                                <h5 class="font-weight-bold mb-0" style="font-size: 0.8rem; color: white; line-height: 1.2;"><?php echo htmlspecialchars($slider->slider_title); ?></h5>
                                                <p class="d-none d-md-block mb-0" style="font-size: 0.75rem; color: white; margin-top: 4px;"><?php echo htmlspecialchars($slider->slider_description); ?></p>
                                            </div>
                                        </div>
                                    <?php 
                                        $index++;
                                    endwhile; 
                                    ?>
                                </div>

                                <!-- Controls -->
                                <a class="carousel-control-prev" href="#workCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#workCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                            
                            <style>
                                #workCarousel {
                                    margin-bottom: 0 !important;
                                }
                                #workCarousel .carousel-inner {
                                    margin-bottom: 0 !important;
                                }
                                #workCarousel .carousel-item {
                                    margin-bottom: 0 !important;
                                }
                                /* Mobile - Fixed height */
                                @media (max-width: 767px) {
                                    #workCarousel {
                                        height: 250px !important;
                                    }
                                    #workCarousel .carousel-inner {
                                        height: 250px !important;
                                    }
                                    #workCarousel .carousel-item {
                                        height: 250px !important;
                                    }
                                    #workCarousel .carousel-item img {
                                        height: 250px !important;
                                    }
                                }
                                
                                /* Desktop - Larger height */
                                @media (min-width: 768px) {
                                    #workCarousel {
                                        height: 400px !important;
                                    }
                                    #workCarousel .carousel-inner {
                                        height: 400px !important;
                                    }
                                    #workCarousel .carousel-item {
                                        height: 400px !important;
                                    }
                                    #workCarousel .carousel-inner img {
                                        height: 400px !important;
                                    }
                                    #portfolioSection {
                                        padding: 3rem 0 !important;
                                    }
                                }
                                @media (max-width: 767px) {
                                    #portfolioSection {
                                        padding: 10px 0 0 0 !important;
                                        margin-bottom: 0 !important;
                                    }
                                    #portfolioSection .container {
                                        padding-bottom: 0 !important;
                                        margin-bottom: 0 !important;
                                    }
                                    #portfolioSection .row {
                                        margin-bottom: 0 !important;
                                    }
                                    #portfolioSection .col-lg-10 {
                                        padding-bottom: 0 !important;
                                        margin-bottom: 0 !important;
                                    }
                                    #workCarousel,
                                    #workCarousel .carousel-inner,
                                    #workCarousel .carousel-item,
                                    #workCarousel .carousel-item img {
                                        margin-bottom: 0 !important;
                                        padding-bottom: 0 !important;
                                    }
                                    .testimonials-section {
                                        margin-top: 0 !important;
                                        padding-top: 10px !important;
                                    }
                                    /* Force caption to show on mobile */
                                    #workCarousel .carousel-caption {
                                        display: block !important;
                                        visibility: visible !important;
                                        opacity: 1 !important;
                                        position: absolute !important;
                                        bottom: 8px !important;
                                        left: 8px !important;
                                        right: 8px !important;
                                        z-index: 100 !important;
                                    }
                                    #workCarousel .carousel-caption h5 {
                                        display: block !important;
                                        visibility: visible !important;
                                    }
                                }
                            </style>
                        <?php else: ?>
                            <div class="alert alert-info text-center" style="font-size: 0.85rem; padding: 10px;">
                                <i class="fas fa-info-circle"></i> No portfolio images available.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section with Auto-Sliding -->
        <section class="testimonials-section" style="background: linear-gradient(135deg, #89c9c6 0%, #e8b4c0 100%); background-size: 200% 200%; animation: gradientShift 15s ease infinite; position: relative; overflow: hidden; padding: 15px 0;">
            <div class="testimonial-overlay"></div>
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row text-center mb-2">
                    <div class="col-12">
                        <h2 class="font-weight-bold" style="color: #2d3748; text-shadow: 1px 1px 2px rgba(255,255,255,0.5); font-size: 1.3rem; margin-bottom: 0;">
                            Client Testimonials
                        </h2>
                    </div>
                </div>
                
                <!-- Testimonials Slider Container -->
                <div class="testimonials-slider-wrapper" style="position: relative; overflow: hidden;">
                    <div class="testimonials-slider" id="testimonialsSlider">
                        <?php
                        $ret="SELECT * FROM tms_feedback where f_status ='Published' ORDER BY f_id DESC";
                        $stmt= $mysqli->prepare($ret);
                        $stmt->execute();
                        $res=$stmt->get_result();
                        $cnt=1;
                        $testimonialGradients = [
                            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
                        ];
                        while($row=$res->fetch_object()) {
                            $testGradient = $testimonialGradients[($cnt-1) % count($testimonialGradients)];
                        ?>
                        <div class="testimonial-slide" style="display: inline-block; width: 220px; margin: 0 8px; vertical-align: top;">
                            <div class="testimonial-card card border-0" style="border-radius: 10px; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.08); height: 100%;">
                                <div class="card-body" style="padding: 10px;">
                                    <div style="margin-bottom: 6px;">
                                        <i class="fas fa-quote-left testimonial-quote" style="font-size: 1rem; background: <?php echo $testGradient; ?>; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; opacity: 0.4;"></i>
                                    </div>
                                    <p class="card-text testimonial-text" style="font-style: italic; line-height: 1.4; color: #2d3748; font-size: 0.75rem; min-height: 55px; margin-bottom: 8px;">
                                        "<?php echo $row->f_content; ?>"
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <?php if(isset($row->f_photo) && $row->f_photo) { ?>
                                            <img src="<?php echo $row->f_photo; ?>" alt="<?php echo $row->f_uname; ?>" class="mr-2" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                        <?php } else { ?>
                                            <div class="avatar-circle mr-2" style="width: 30px; height: 30px; border-radius: 50%; background: <?php echo $testGradient; ?>; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                                <?php echo strtoupper(substr($row->f_uname, 0, 1)); ?>
                                            </div>
                                        <?php } ?>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold" style="color: #2d3748; font-size: 0.75rem;"><?php echo $row->f_uname; ?></h6>
                                            <small class="text-muted" style="font-weight: 500; font-size: 0.65rem;">Verified</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $cnt++; } ?>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <?php include("vendor/inc/footer.php");?>
    <!--.Footer-->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sliding Images Script for Booking Form -->
    <script>
        $(document).ready(function() {
            // Initialize Work Carousel with auto-play (4 seconds)
            $('#workCarousel').carousel({
                interval: 4000,
                pause: 'hover',
                wrap: true
            });
            
            // Testimonials Auto-Sliding (Right to Left every 7 seconds)
            const slider = document.getElementById('testimonialsSlider');
            if(slider) {
                const slides = slider.querySelectorAll('.testimonial-slide');
                
                // Clone slides for seamless loop
                slides.forEach(slide => {
                    const clone = slide.cloneNode(true);
                    slider.appendChild(clone);
                });
                
                // Calculate total width
                let totalWidth = 0;
                slides.forEach(slide => {
                    totalWidth += slide.offsetWidth + 30; // 30 = margin
                });
                
                // Set animation duration based on number of slides (7 seconds per slide transition)
                const duration = slides.length * 7;
                slider.style.animation = `slideTestimonials ${duration}s linear infinite`;
            }
        });
    </script>
    
    <!-- Home Gallery Slider (moves right-to-left every 6 seconds) -->
    <script>
      $(function(){
        var $track = $('.home-slider-track');
        var $items = $('.home-slider-item');
        if ($track.length && $items.length) {
          var idx = 0;
          function goTo(i){
            idx = i % $items.length;
            var offset = -idx * $items.first().outerWidth(true);
            $track.css('transform','translateX(' + offset + 'px)');
          }
          goTo(0);
          setInterval(function(){ goTo(idx + 1); }, 6000);
          $(window).on('resize', function(){ goTo(idx); });
        }
      });
    </script>

    <!-- Guest Booking Auto-Fill Script -->
    <script>
    $(document).ready(function() {
        $('#guest_phone').on('blur', function() {
            var phone = $(this).val();
            
            // Only check if phone is exactly 10 digits
            if (phone.length === 10) {
                $.ajax({
                    url: 'admin/vendor/inc/check-customer.php',
                    method: 'POST',
                    data: { phone: phone },
                    dataType: 'json',
                    success: function(response) {
                        if (response.exists && response.user) {
                            // Customer found - auto-fill details
                            var fullName = response.user.u_fname + ' ' + response.user.u_lname;
                            $('#guest_name').val(fullName);
                            $('#guest_area').val(response.user.u_area);
                            $('#guest_pincode').val(response.user.u_pincode);
                            $('#guest_address').val(response.user.u_addr);
                            
                            // Show success message
                            $('#customer_status').html('<i class="fas fa-check-circle text-success"></i> Registered customer - details auto-filled').css('color', '#28a745');
                            
                            // Name is readonly for registered customers, other fields editable
                            $('#guest_name').prop('readonly', true).css('background-color', '#f0f0f0');
                            $('#guest_area').prop('readonly', false);
                            $('#guest_pincode').prop('readonly', false);
                            $('#guest_address').prop('readonly', false);
                        } else {
                            // New customer - clear any previous data
                            $('#customer_status').html('<i class="fas fa-info-circle text-info"></i> New customer - please fill all details').css('color', '#17a2b8');
                            
                            // Ensure all fields are editable
                            $('#guest_name').prop('readonly', false).css('background-color', '');
                            $('#guest_area').prop('readonly', false);
                            $('#guest_pincode').prop('readonly', false);
                            $('#guest_address').prop('readonly', false);
                        }
                    },
                    error: function() {
                        $('#customer_status').html('<i class="fas fa-exclamation-triangle text-warning"></i> Could not verify customer').css('color', '#ffc107');
                    }
                });
            } else {
                $('#customer_status').html('');
            }
        });
    });
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        // Register Service Worker with relative path (works for any subdirectory)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // Use relative path - works regardless of subdirectory
                navigator.serviceWorker.register('./sw.js')
                    .then((registration) => {
                        console.log('✅ Service Worker registered successfully');
                        console.log('📍 Scope:', registration.scope);
                        
                        // Check for updates
                        registration.update();
                    })
                    .catch((error) => {
                        console.error('❌ Service Worker registration failed:', error);
                    });
            });
        } else {
            console.log('⚠️ Service Workers not supported in this browser');
        }

        // PWA Install Prompt with better debugging
        let deferredPrompt;
        const installButton = document.createElement('button');
        installButton.innerHTML = '<i class="fas fa-download"></i> Install App';
        installButton.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            z-index: 9999;
            display: none;
            transition: all 0.3s ease;
        `;
        installButton.onmouseover = () => {
            installButton.style.transform = 'translateY(-3px)';
            installButton.style.boxShadow = '0 6px 20px rgba(102, 126, 234, 0.6)';
        };
        installButton.onmouseout = () => {
            installButton.style.transform = 'translateY(0)';
            installButton.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.4)';
        };
        document.body.appendChild(installButton);

        // Debug: Check if PWA is installable
        console.log('🔍 Checking PWA installability...');
        console.log('- Service Worker support:', 'serviceWorker' in navigator);
        console.log('- Manifest link:', document.querySelector('link[rel="manifest"]') ? '✅ Found' : '❌ Missing');
        console.log('- HTTPS:', window.location.protocol === 'https:' || window.location.hostname === 'localhost');

        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('✅ beforeinstallprompt event fired - App is installable!');
            e.preventDefault();
            deferredPrompt = e;
            installButton.style.display = 'block';
        });

        installButton.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response to install prompt: ${outcome}`);
                deferredPrompt = null;
                installButton.style.display = 'none';
            }
        });

        window.addEventListener('appinstalled', () => {
            console.log('✅ PWA installed successfully!');
            installButton.style.display = 'none';
        });

        // Check if app is installed
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('✅ Running as installed PWA');
        }
    </script>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>