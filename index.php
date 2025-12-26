<?php
  // Prevent caching to ensure changes are visible immediately
  header("Cache-Control: no-cache, no-store, must-revalidate");
  header("Pragma: no-cache");
  header("Expires: 0");
  
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
        <section class="hero-section-enhanced" style="background: linear-gradient(135deg, #e6f7ff 0%, #d1fae5 20%, #e5e7eb 40%, #bbf7d0 60%, #d1d5db 80%, #e9d5ff 100%); padding: 100px 0 80px 0; position: relative; overflow: hidden;">
            
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
                            Your Trusted Partner for Electrical & Plumbing Services,<br> Quality Service & Certified Technicians.<br>We Deliver Perfection in Every Job.
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



        <!-- PWA Install Guide Modal -->
        <div class="modal fade" id="pwaGuideModal" tabindex="-1" role="dialog" aria-labelledby="pwaGuideModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
                    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <h5 class="modal-title font-weight-bold" id="pwaGuideModalLabel">
                            <i class="fas fa-mobile-alt"></i> Install ElectroZot App
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding: 25px;">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="install-step">
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        <i class="fab fa-chrome"></i> Chrome / Edge Browser
                                    </h6>
                                    <ol class="install-steps" style="font-size: 0.9rem; line-height: 1.6;">
                                        <li>Look for the <strong>"Install"</strong> button in the address bar</li>
                                        <li>Or click the <strong>menu (⋮)</strong> → "Install ElectroZot"</li>
                                        <li>Click <strong>"Install"</strong> in the popup</li>
                                        <li>App will be added to your home screen!</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="install-step">
                                    <h6 class="font-weight-bold text-success mb-3">
                                        <i class="fab fa-safari"></i> Safari (iPhone/iPad)
                                    </h6>
                                    <ol class="install-steps" style="font-size: 0.9rem; line-height: 1.6;">
                                        <li>Tap the <strong>Share button</strong> (□↗)</li>
                                        <li>Scroll down and tap <strong>"Add to Home Screen"</strong></li>
                                        <li>Tap <strong>"Add"</strong> in the top right</li>
                                        <li>ElectroZot app will appear on your home screen!</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pwa-benefits mt-4 p-3" style="background: #f8f9fa; border-radius: 10px;">
                            <h6 class="font-weight-bold mb-3 text-center">
                                <i class="fas fa-star text-warning"></i> App Benefits
                            </h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <i class="fas fa-bolt text-primary" style="font-size: 1.5rem;"></i>
                                    <p class="mb-0 mt-2" style="font-size: 0.8rem; font-weight: 600;">Faster Loading</p>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-wifi text-success" style="font-size: 1.5rem;"></i>
                                    <p class="mb-0 mt-2" style="font-size: 0.8rem; font-weight: 600;">Works Offline</p>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-bell text-warning" style="font-size: 1.5rem;"></i>
                                    <p class="mb-0 mt-2" style="font-size: 0.8rem; font-weight: 600;">Push Notifications</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border: none; padding: 15px 25px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="tryInstallAgain">
                            <i class="fas fa-download"></i> Try Install
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                        <h2 class="font-weight-bold mb-2 section-title" style="font-size: 1.5rem; color: #16a34a;">
                            Why Choose Electrozot?
                        </h2>
                        <p class="d-none d-md-block" style="color: #000000; font-weight: 500; font-size: 0.9rem; margin-bottom: 0;">Professional service you can trust</p>
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
                        <h2 class="font-weight-bold mb-2 section-title" style="font-size: 1.5rem; color: #16a34a;">
                            Our Popular Services
                        </h2>
                        <p class="d-none d-md-block" style="color: #000000; font-weight: 500; font-size: 0.9rem; margin-bottom: 0;">Expert technicians for all your needs</p>
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

        <!-- Recent Blog Posts Section -->
        <section style="background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%); padding: 30px 0;">
            <div class="container">
                <div class="row text-center mb-3">
                    <div class="col-12">
                        <h2 class="font-weight-bold" style="color: #2d3748; font-size: 1.8rem;">Latest from Our Blog</h2>
                        <p class="text-muted" style="font-size: 0.9rem;">Expert tips and guides from our certified technicians</p>
                    </div>
                </div>
                
                <?php
                // Get all published blog posts for carousel
                $blog_query = "SELECT * FROM tms_blog_posts WHERE blog_status = 'Published' ORDER BY blog_published_at DESC";
                $blog_result = $mysqli->query($blog_query);
                $blogs = [];
                if($blog_result && $blog_result->num_rows > 0) {
                    while($blog = $blog_result->fetch_object()) {
                        $blogs[] = $blog;
                    }
                }
                ?>
                
                <?php if(!empty($blogs)) { ?>
                <!-- Desktop View - Show 3 blogs in a row -->
                <div class="row d-none d-md-flex" id="desktop-blogs">
                    <?php 
                    $desktop_blogs = array_slice($blogs, 0, 3); // Show first 3 blogs on desktop
                    foreach($desktop_blogs as $blog) { 
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <?php if($blog->blog_image) { ?>
                                <img src="<?php echo $blog->blog_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($blog->blog_title); ?>" style="height: 140px; object-fit: cover;">
                            <?php } else { ?>
                                <div class="card-img-top" style="height: 140px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-blog" style="font-size: 2.5rem; color: rgba(255,255,255,0.8);"></i>
                                </div>
                            <?php } ?>
                            <div class="card-body p-3 d-flex flex-column">
                                <?php if($blog->blog_category) { ?>
                                    <span class="badge badge-primary mb-1" style="width: fit-content; font-size: 0.7rem;"><?php echo htmlspecialchars($blog->blog_category); ?></span>
                                <?php } ?>
                                <h6 class="card-title font-weight-bold mb-2" style="color: #2d3748; font-size: 0.95rem;"><?php echo htmlspecialchars($blog->blog_title); ?></h6>
                                <p class="card-text text-muted mb-2" style="flex-grow: 1; font-size: 0.8rem; line-height: 1.3;">
                                    <?php 
                                    $excerpt = $blog->blog_excerpt ?: strip_tags($blog->blog_content);
                                    echo htmlspecialchars(substr($excerpt, 0, 80)) . '...'; 
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d', strtotime($blog->blog_published_at)); ?>
                                    </small>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <i class="fas fa-eye"></i> <?php echo $blog->blog_views; ?>
                                    </small>
                                </div>
                                <a href="blog-post.php?id=<?php echo $blog->blog_id; ?>&slug=<?php echo $blog->blog_slug; ?>" class="btn btn-primary btn-sm btn-block" style="border-radius: 15px; font-size: 0.8rem; padding: 6px 12px;">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <!-- Mobile View - Carousel with auto-rotation -->
                <div class="d-block d-md-none" id="mobile-blog-carousel">
                    <div class="blog-carousel-container" style="position: relative; overflow: hidden; border-radius: 15px;">
                        <?php foreach($blogs as $index => $blog) { ?>
                        <div class="blog-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>; animation: <?php echo $index === 0 ? 'slideInRight 0.5s ease-in-out' : 'none'; ?>;">
                            <div class="card shadow-sm mx-2" style="border-radius: 10px; overflow: hidden;">
                                <?php if($blog->blog_image) { ?>
                                    <img src="<?php echo $blog->blog_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($blog->blog_title); ?>" style="height: 140px; object-fit: cover;">
                                <?php } else { ?>
                                    <div class="card-img-top" style="height: 140px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-blog" style="font-size: 2.5rem; color: rgba(255,255,255,0.8);"></i>
                                    </div>
                                <?php } ?>
                                <div class="card-body p-3">
                                    <?php if($blog->blog_category) { ?>
                                        <span class="badge badge-primary mb-1" style="width: fit-content; font-size: 0.7rem;"><?php echo htmlspecialchars($blog->blog_category); ?></span>
                                    <?php } ?>
                                    <h6 class="card-title font-weight-bold mb-2" style="color: #2d3748; font-size: 0.95rem;"><?php echo htmlspecialchars($blog->blog_title); ?></h6>
                                    <p class="card-text text-muted mb-2" style="font-size: 0.8rem; line-height: 1.3;">
                                        <?php 
                                        $excerpt = $blog->blog_excerpt ?: strip_tags($blog->blog_content);
                                        echo htmlspecialchars(substr($excerpt, 0, 70)) . '...'; 
                                        ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-calendar"></i> <?php echo date('M d', strtotime($blog->blog_published_at)); ?>
                                        </small>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-eye"></i> <?php echo $blog->blog_views; ?>
                                        </small>
                                    </div>
                                    <a href="blog-post.php?id=<?php echo $blog->blog_id; ?>&slug=<?php echo $blog->blog_slug; ?>" class="btn btn-primary btn-sm btn-block" style="border-radius: 15px; font-size: 0.8rem; padding: 6px 12px;">
                                        Read More <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Carousel indicators -->
                        <div class="carousel-indicators" style="position: absolute; bottom: -20px; left: 50%; transform: translateX(-50%); display: flex; gap: 6px;">
                            <?php foreach($blogs as $index => $blog) { ?>
                            <button class="indicator-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>" style="width: 8px; height: 8px; border-radius: 50%; border: none; background: <?php echo $index === 0 ? '#007bff' : '#ccc'; ?>; cursor: pointer; transition: all 0.3s ease;"></button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- View All Button -->
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <a href="blog.php" class="btn btn-outline-primary" style="border-radius: 20px; padding: 8px 24px; font-size: 0.9rem;">
                            View All Posts <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <?php } else { ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-muted">No blog posts available yet. Check back soon!</p>
                    </div>
                </div>
                <?php } ?>
            </div>
        </section>

        <!-- Blog Carousel Styles and Script -->
        <style>
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .blog-carousel-container {
                margin-bottom: 25px;
            }
            
            .blog-slide {
                transition: all 0.5s ease-in-out;
            }
            
            .indicator-dot:hover {
                background: #007bff !important;
                transform: scale(1.2);
            }
            
            .indicator-dot.active {
                background: #007bff !important;
                transform: scale(1.1);
            }
            
            /* Mobile responsive adjustments */
            @media (max-width: 768px) {
                .blog-carousel-container {
                    margin: 0 10px;
                }
                
                .card {
                    margin: 0 !important;
                }
                
                /* Compact blog images on mobile */
                #mobile-blog-carousel .card-img-top {
                    height: 120px !important;
                    width: 100% !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    overflow: hidden !important;
                }
                
                #mobile-blog-carousel .card-img-top img {
                    height: 120px !important;
                    width: 100% !important;
                    object-fit: cover !important;
                    object-position: center !important;
                    border-radius: 0 !important;
                }
                
                #mobile-blog-carousel .card-img-top i {
                    font-size: 2rem !important;
                }
                
                #mobile-blog-carousel .card-body {
                    padding: 0.75rem !important;
                }
                
                #mobile-blog-carousel .card-title {
                    font-size: 0.85rem !important;
                    line-height: 1.2 !important;
                    margin-bottom: 0.5rem !important;
                }
                
                #mobile-blog-carousel .card-text {
                    font-size: 0.75rem !important;
                    line-height: 1.2 !important;
                    margin-bottom: 0.5rem !important;
                }
                
                #mobile-blog-carousel .badge {
                    font-size: 0.6rem !important;
                    padding: 0.2rem 0.4rem !important;
                }
                
                #mobile-blog-carousel .btn {
                    font-size: 0.75rem !important;
                    padding: 4px 8px !important;
                }
            }
            
            /* Extra compact for very small screens */
            @media (max-width: 480px) {
                #mobile-blog-carousel .card-img-top {
                    height: 100px !important;
                    width: 100% !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    overflow: hidden !important;
                }
                
                #mobile-blog-carousel .card-img-top img {
                    height: 100px !important;
                    width: 100% !important;
                    object-fit: cover !important;
                    object-position: center !important;
                    border-radius: 0 !important;
                }
                
                #mobile-blog-carousel .card-img-top i {
                    font-size: 1.5rem !important;
                }
                
                #mobile-blog-carousel .card-body {
                    padding: 0.5rem !important;
                }
                
                #mobile-blog-carousel .card-title {
                    font-size: 0.8rem !important;
                    margin-bottom: 0.3rem !important;
                }
                
                #mobile-blog-carousel .card-text {
                    font-size: 0.7rem !important;
                    margin-bottom: 0.3rem !important;
                }
                
                #mobile-blog-carousel .d-flex {
                    margin-bottom: 0.3rem !important;
                }
                
                #mobile-blog-carousel .d-flex small {
                    font-size: 0.6rem !important;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slides = document.querySelectorAll('.blog-slide');
                const indicators = document.querySelectorAll('.indicator-dot');
                
                if (slides.length === 0) return;
                
                let currentSlide = 0;
                let carouselInterval;
                
                function showSlide(index) {
                    // Hide all slides
                    slides.forEach((slide, i) => {
                        slide.style.display = 'none';
                        slide.style.animation = 'none';
                    });
                    
                    // Update indicators
                    indicators.forEach((indicator, i) => {
                        indicator.classList.remove('active');
                        indicator.style.background = '#ccc';
                    });
                    
                    // Show current slide with animation
                    if (slides[index]) {
                        slides[index].style.display = 'block';
                        slides[index].style.animation = 'slideInRight 0.5s ease-in-out';
                        
                        // Update active indicator
                        if (indicators[index]) {
                            indicators[index].classList.add('active');
                            indicators[index].style.background = '#007bff';
                        }
                    }
                }
                
                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }
                
                function startCarousel() {
                    if (slides.length > 1) {
                        carouselInterval = setInterval(nextSlide, 5000); // Change every 5 seconds
                    }
                }
                
                // Indicator click handlers
                indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', () => {
                        currentSlide = index;
                        showSlide(currentSlide);
                        
                        // Restart carousel
                        clearInterval(carouselInterval);
                        startCarousel();
                    });
                });
                
                // Start the carousel automatically
                startCarousel();
                
                // Pause on hover (optional)
                const carouselContainer = document.querySelector('.blog-carousel-container');
                if (carouselContainer) {
                    carouselContainer.addEventListener('mouseenter', () => {
                        clearInterval(carouselInterval);
                    });
                    
                    carouselContainer.addEventListener('mouseleave', () => {
                        startCarousel();
                    });
                }
            });
        </script>

        <!-- About Section -->
        <section id="about-section" class="about-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 60px 0;">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="font-weight-bold mb-3" style="font-size: 2.5rem; color: #2d3748;">
                            <i class="fas fa-info-circle" style="color: #EC4899; margin-right: 10px;"></i>About ElectroZot
                        </h2>
                        <p class="lead" style="color: #6c757d; max-width: 800px; margin: 0 auto;">
                            Your trusted partner for professional electrical, repair, and plumbing services
                        </p>
                    </div>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="about-content">
                            <h3 class="font-weight-bold mb-4" style="color: #2d3748; font-size: 1.8rem;">
                                We Make Perfect - Every Time
                            </h3>
                            <p style="color: #6c757d; line-height: 1.8; font-size: 1.1rem; margin-bottom: 20px;">
                                ElectroZot is your reliable partner for all electrical, repair, installation, maintenance, and plumbing services. 
                                With years of experience and a team of certified professionals, we deliver quality workmanship that you can trust.
                            </p>
                            <p style="color: #6c757d; line-height: 1.8; font-size: 1.1rem; margin-bottom: 30px;">
                                Our commitment to excellence, transparent pricing, and customer satisfaction has made us the preferred choice 
                                for thousands of customers across India.
                            </p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-center">
                                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                            <i class="fas fa-certificate" style="color: white; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1" style="color: #2d3748;">Certified Technicians</h6>
                                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">Licensed & experienced professionals</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-center">
                                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                            <i class="fas fa-shield-alt" style="color: white; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1" style="color: #2d3748;">1-Month Warranty</h6>
                                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">Guaranteed service quality</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-center">
                                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                            <i class="fas fa-clock" style="color: white; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1" style="color: #2d3748;">24/7 Support</h6>
                                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">Emergency services available</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-center">
                                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                            <i class="fas fa-rupee-sign" style="color: white; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1" style="color: #2d3748;">Transparent Pricing</h6>
                                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">No hidden charges</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="about-stats-card" style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                            <div class="row text-center">
                                <div class="col-6 mb-4">
                                    <div class="stat-item">
                                        <h3 class="font-weight-bold mb-2" style="color: #EC4899; font-size: 2.5rem;">1000+</h3>
                                        <p class="mb-0" style="color: #6c757d; font-weight: 600;">Happy Customers</p>
                                    </div>
                                </div>
                                <div class="col-6 mb-4">
                                    <div class="stat-item">
                                        <h3 class="font-weight-bold mb-2" style="color: #10B981; font-size: 2.5rem;">5+</h3>
                                        <p class="mb-0" style="color: #6c757d; font-weight: 600;">Years Experience</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h3 class="font-weight-bold mb-2" style="color: #8B5CF6; font-size: 2.5rem;">50+</h3>
                                        <p class="mb-0" style="color: #6c757d; font-weight: 600;">Services Offered</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h3 class="font-weight-bold mb-2" style="color: #F59E0B; font-size: 2.5rem;">24/7</h3>
                                        <p class="mb-0" style="color: #6c757d; font-weight: 600;">Support Available</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq-section" class="faq-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 60px 0;">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="font-weight-bold mb-3" style="font-size: 2.5rem; color: #2d3748;">
                            <i class="fas fa-question-circle" style="color: #EC4899; margin-right: 10px;"></i>Frequently Asked Questions
                        </h2>
                        <p class="lead" style="color: #6c757d; max-width: 800px; margin: 0 auto;">
                            Get answers to common questions about our services
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="accordion" id="faqAccordion">
                            <!-- FAQ 1 -->
                            <div class="card border-0 mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                                <div class="card-header" id="faq1" style="background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%); border: none; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link font-weight-bold text-left w-100" type="button" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1" style="color: #2d3748; text-decoration: none; padding: 20px;">
                                            <i class="fas fa-plus-circle" style="color: #EC4899; margin-right: 10px;"></i>
                                            How do I book an electrical service with ElectroZot?
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse1" class="collapse show" aria-labelledby="faq1" data-parent="#faqAccordion">
                                    <div class="card-body" style="padding: 20px; color: #6c757d; line-height: 1.6;">
                                        You can book an electrical service through the ElectroZot website by selecting the required service and submitting a booking request. Simply fill out the booking form on our homepage or call us directly at <strong>7559606925</strong>.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 2 -->
                            <div class="card border-0 mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                                <div class="card-header" id="faq2" style="background: linear-gradient(135deg, #F0FDFA 0%, #CCFBF1 100%); border: none; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link font-weight-bold text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2" style="color: #2d3748; text-decoration: none; padding: 20px;">
                                            <i class="fas fa-plus-circle" style="color: #10B981; margin-right: 10px;"></i>
                                            What are your service hours?
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse2" class="collapse" aria-labelledby="faq2" data-parent="#faqAccordion">
                                    <div class="card-body" style="padding: 20px; color: #6c757d; line-height: 1.6;">
                                        ElectroZot operates during standard business hours with flexible scheduling based on service availability. We also provide <strong>24/7 emergency services</strong> for urgent electrical and plumbing issues.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 3 -->
                            <div class="card border-0 mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                                <div class="card-header" id="faq3" style="background: linear-gradient(135deg, #FDF5FF 0%, #F3E8FF 100%); border: none; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link font-weight-bold text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3" style="color: #2d3748; text-decoration: none; padding: 20px;">
                                            <i class="fas fa-plus-circle" style="color: #8B5CF6; margin-right: 10px;"></i>
                                            Do you provide warranty on your work?
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse3" class="collapse" aria-labelledby="faq3" data-parent="#faqAccordion">
                                    <div class="card-body" style="padding: 20px; color: #6c757d; line-height: 1.6;">
                                        Yes, ElectroZot provides <strong>1-month warranty coverage</strong> on all repair services performed. This warranty covers the quality of workmanship and parts used during the service.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 4 -->
                            <div class="card border-0 mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                                <div class="card-header" id="faq4" style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border: none; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link font-weight-bold text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4" style="color: #2d3748; text-decoration: none; padding: 20px;">
                                            <i class="fas fa-plus-circle" style="color: #F59E0B; margin-right: 10px;"></i>
                                            What payment methods do you accept?
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse4" class="collapse" aria-labelledby="faq4" data-parent="#faqAccordion">
                                    <div class="card-body" style="padding: 20px; color: #6c757d; line-height: 1.6;">
                                        We accept <strong>cash, UPI, and other digital payment methods</strong> for your convenience. Payment is typically collected after the service is completed to your satisfaction.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 5 -->
                            <div class="card border-0 mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                                <div class="card-header" id="faq5" style="background: linear-gradient(135deg, #FEF2F2 0%, #FECACA 100%); border: none; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link font-weight-bold text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapse5" aria-expanded="false" aria-controls="collapse5" style="color: #2d3748; text-decoration: none; padding: 20px;">
                                            <i class="fas fa-plus-circle" style="color: #EF4444; margin-right: 10px;"></i>
                                            Are your technicians verified and insured?
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse5" class="collapse" aria-labelledby="faq5" data-parent="#faqAccordion">
                                    <div class="card-body" style="padding: 20px; color: #6c757d; line-height: 1.6;">
                                        Yes, all our technicians are <strong>trained, background-verified, and insured</strong>. We ensure that only qualified and trustworthy professionals work on your property.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 6 -->
                            <div class="card border-0 mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                                <div class="card-header" id="faq6" style="background: linear-gradient(135deg, #F0F9FF 0%, #DBEAFE 100%); border: none; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link font-weight-bold text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapse6" aria-expanded="false" aria-controls="collapse6" style="color: #2d3748; text-decoration: none; padding: 20px;">
                                            <i class="fas fa-plus-circle" style="color: #3B82F6; margin-right: 10px;"></i>
                                            How do you calculate service charges?
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse6" class="collapse" aria-labelledby="faq6" data-parent="#faqAccordion">
                                    <div class="card-body" style="padding: 20px; color: #6c757d; line-height: 1.6;">
                                        Charges are based on service type, work complexity, materials used, and time required, with <strong>transparent pricing</strong>. We provide upfront cost estimates before starting any work, with no hidden charges.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact-section" class="contact-section" style="background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%); padding: 60px 0; color: white;">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="font-weight-bold mb-3" style="font-size: 2.5rem; color: white;">
                            <i class="fas fa-phone" style="color: #EC4899; margin-right: 10px;"></i>Contact Us
                        </h2>
                        <p class="lead" style="color: #cbd5e0; max-width: 800px; margin: 0 auto;">
                            Get in touch with our expert team for all your service needs
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="contact-card text-center" style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 40px 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                            <div class="contact-icon mb-3" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-phone" style="color: white; font-size: 1.8rem;"></i>
                            </div>
                            <h4 class="font-weight-bold mb-3" style="color: white;">Call Us</h4>
                            <p style="color: #cbd5e0; margin-bottom: 20px;">Available 24/7 for emergency services</p>
                            <a href="tel:7559606925" class="btn btn-outline-light" style="border-radius: 25px; padding: 12px 30px; font-weight: 600; border: 2px solid #EC4899; color: #EC4899;">
                                <i class="fas fa-phone"></i> 7559606925
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="contact-card text-center" style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 40px 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                            <div class="contact-icon mb-3" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-envelope" style="color: white; font-size: 1.8rem;"></i>
                            </div>
                            <h4 class="font-weight-bold mb-3" style="color: white;">Email Us</h4>
                            <p style="color: #cbd5e0; margin-bottom: 20px;">Send us your service requirements</p>
                            <a href="mailto:electrozot@outlook.com" class="btn btn-outline-light" style="border-radius: 25px; padding: 12px 30px; font-weight: 600; border: 2px solid #10B981; color: #10B981;">
                                <i class="fas fa-envelope"></i> Email Now
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="contact-card text-center" style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 40px 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                            <div class="contact-icon mb-3" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-calendar-check" style="color: white; font-size: 1.8rem;"></i>
                            </div>
                            <h4 class="font-weight-bold mb-3" style="color: white;">Book Online</h4>
                            <p style="color: #cbd5e0; margin-bottom: 20px;">Quick and easy online booking</p>
                            <a href="#booking-form" class="btn btn-outline-light" style="border-radius: 25px; padding: 12px 30px; font-weight: 600; border: 2px solid #8B5CF6; color: #8B5CF6;">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <div class="service-areas" style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                            <h4 class="font-weight-bold mb-3" style="color: white;">
                                <i class="fas fa-map-marker-alt" style="color: #F59E0B; margin-right: 10px;"></i>Service Areas
                            </h4>
                            <p style="color: #cbd5e0; font-size: 1.1rem; line-height: 1.6; margin: 0;">
                                We provide services across <strong>Himachal Pradesh</strong> with special focus on 
                                <strong>Kangra District</strong> including Kangra, Dharamshala, Palampur, Baijnath, Nurpur, Dehra, and nearby areas.
                            </p>
                        </div>
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
    <script src="vendor/jquery/jquery.min.js?v=<?php echo time(); ?>"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js?v=<?php echo time(); ?>"></script>
    
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
        // Production-ready Service Worker registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    // Register service worker with production settings
                    const registration = await navigator.serviceWorker.register('./sw.js', {
                        scope: './',
                        updateViaCache: 'none' // Always check for updates in production
                    });
                    
                    // Wait for service worker to be ready
                    await navigator.serviceWorker.ready;
                    
                    // Check for updates periodically in production
                    registration.update();
                    
                    // Set up automatic update checking
                    setInterval(() => {
                        registration.update();
                    }, 60000); // Check every minute in production
                    
                } catch (error) {
                    // Graceful fallback - try alternative registration
                    try {
                        await navigator.serviceWorker.register('sw.js');
                    } catch (altError) {
                        // PWA will still work without service worker, just no offline support
                        console.warn('Service Worker registration failed, PWA will work without offline support');
                    }
                }
            });
        }

        // Enhanced PWA Installation Handler
        let deferredPrompt;
        let installPromptAvailable = false;
        const mainInstallBtn = document.getElementById('pwa-main-install-btn');
        const guideBtn = document.getElementById('pwa-guide-btn');
        const installedMsg = document.getElementById('pwa-installed-msg');
        const tryInstallAgainBtn = document.getElementById('tryInstallAgain');



        // Check if app is already installed
        function checkIfInstalled() {
            if (window.matchMedia('(display-mode: standalone)').matches || 
                window.navigator.standalone === true) {
                console.log('✅ Running as installed PWA');
                showInstalledState();
                return true;
            }
            return false;
        }

        function showInstalledState() {
            if (mainInstallBtn) mainInstallBtn.style.display = 'none';
            if (guideBtn) guideBtn.style.display = 'none';
            if (installedMsg) installedMsg.style.display = 'block';
        }

        function showInstallableState() {
            // Always show the install button with consistent messaging
            if (mainInstallBtn) {
                mainInstallBtn.style.display = 'inline-block';
                // Always show as install button, regardless of prompt availability
                mainInstallBtn.innerHTML = '<i class="fas fa-download"></i> Install App';
                mainInstallBtn.title = 'Click to install the ElectroZot app';
                
                // Make sure button is enabled
                mainInstallBtn.disabled = false;
                mainInstallBtn.style.opacity = '1';
                mainInstallBtn.style.cursor = 'pointer';
            }
            if (guideBtn) guideBtn.style.display = 'inline-block';
            if (installedMsg) installedMsg.style.display = 'none';
        }

        // Listen for install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installPromptAvailable = true;
            
            if (!checkIfInstalled()) {
                showInstallableState();
                window.pwaInstallEvent = e;
                
                // Update button text for desktop
                if (window.screen.width >= 1024 && mainInstallBtn) {
                    mainInstallBtn.innerHTML = '<i class="fas fa-download"></i> Install Desktop App';
                    mainInstallBtn.title = 'Install ElectroZot as a desktop application';
                }
            }
        });

        // Enhanced install function for direct installation
        async function installPWA() {
            console.log('🔄 Install PWA function called');
            console.log('- Deferred prompt available:', !!deferredPrompt);
            console.log('- Install prompt available:', installPromptAvailable);
            
            if (deferredPrompt && installPromptAvailable) {
                try {
                    // Force direct install prompt without instructions
                    console.log('🚀 Triggering direct install...');
                    
                    // Immediately show the install prompt
                    const promptResult = deferredPrompt.prompt();
                    
                    // Wait for the user to respond
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    console.log(`👤 User response: ${outcome}`);
                    
                    if (outcome === 'accepted') {
                        console.log('✅ PWA installed successfully');
                        showInstalledState();
                        showInstallSuccessNotification();
                    } else {
                        console.log('❌ User dismissed install prompt');
                        // Don't show manual guide if user dismissed
                    }
                    
                    // Clear the deferredPrompt
                    deferredPrompt = null;
                    installPromptAvailable = false;
                } catch (error) {
                    console.error('❌ Install error:', error);
                    // Try alternative installation method
                    tryAlternativeInstall();
                }
            } else {
                // Try to trigger installation through other means
                console.log('ℹ️ Trying alternative install methods...');
                tryAlternativeInstall();
            }
        }
        
        function tryAlternativeInstall() {
            // Try to trigger browser's native install mechanism
            if (window.chrome && window.chrome.webstore) {
                // Chrome extension API (if available)
                console.log('Trying Chrome install API...');
            } else if ('serviceWorker' in navigator && 'PushManager' in window) {
                // Force PWA criteria check
                console.log('PWA criteria met, waiting for browser install prompt...');
                
                // Show a brief message instead of full instructions
                showBriefInstallMessage();
            } else {
                showBriefInstallMessage();
            }
        }
        
        function showBriefInstallMessage(customMessage = null) {
            // Show a minimal, non-intrusive message
            const message = customMessage || '📱 Look for the install icon in your browser\'s address bar';
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); 
                            background: #667eea; color: white; padding: 12px 20px; border-radius: 25px; 
                            box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 10000; 
                            animation: slideUp 0.3s ease-out; font-size: 14px; text-align: center; max-width: 90%;">
                    ${message}
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideDown 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
        
        function showInstallSuccessNotification() {
            const notification = document.createElement('div');
            notification.innerHTML = `
                <div style="position: fixed; top: 20px; right: 20px; background: #28a745; color: white; 
                            padding: 15px 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); 
                            z-index: 10000; animation: slideInRight 0.3s ease-out;">
                    <strong>✅ App Installed Successfully!</strong>
                    <p style="margin: 5px 0 0 0; font-size: 14px;">You can now use ElectroZot offline</p>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        function showManualInstallGuide() {
            if ($('#pwaGuideModal').length) {
                $('#pwaGuideModal').modal('show');
            } else {
                // Use global fallback function
                if (window.installElectroZotPWA) {
                    window.installElectroZotPWA();
                }
            }
        }

        // Main install button click - Direct installation with robust attachment
        function attachInstallButtonHandler() {
            const installBtn = document.getElementById('pwa-main-install-btn');
            if (installBtn) {
                // Remove any existing listeners
                installBtn.replaceWith(installBtn.cloneNode(true));
                const newBtn = document.getElementById('pwa-main-install-btn');
                
                newBtn.addEventListener('click', async (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Visual feedback
                    newBtn.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        newBtn.style.transform = 'scale(1)';
                    }, 150);
                    
                    // Try multiple installation methods
                    await attemptDirectInstall();
                });
                return true;
            }
            return false;
        }
        
        // Try to attach handler immediately and on DOM ready
        if (!attachInstallButtonHandler()) {
            document.addEventListener('DOMContentLoaded', attachInstallButtonHandler);
        }
        
        async function attemptDirectInstall() {
            
            // Method 1: Use stored deferred prompt
            if (deferredPrompt) {
                try {
                    await deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    if (outcome === 'accepted') {
                        showInstalledState();
                        showInstallSuccessNotification();
                        return true;
                    } else {
                        return false;
                    }
                } catch (error) {
                    // Continue to next method
                }
            }
            
            // Method 2: Use global install event
            if (window.pwaInstallEvent) {
                try {
                    await window.pwaInstallEvent.prompt();
                    const { outcome } = await window.pwaInstallEvent.userChoice;
                    
                    if (outcome === 'accepted') {
                        showInstalledState();
                        showInstallSuccessNotification();
                        return true;
                    }
                } catch (error) {
                    // Continue to next method
                }
            }
            
            // Method 3: Try PWA installer
            if (window.PWAInstaller && window.PWAInstaller.install) {
                try {
                    await window.PWAInstaller.install();
                    return true;
                } catch (error) {
                    // Continue to next method
                }
            }
            
            // Method 4: Show browser-specific instructions
            const isChrome = navigator.userAgent.includes('Chrome');
            if (isChrome) {
                showChromeInstallInstructions();
            } else {
                showBrowserSpecificInstructions();
            }
            return false;
        }
        
        function showChromeInstallInstructions() {
            // Create a more prominent notification for Chrome users
            const notification = document.createElement('div');
            notification.innerHTML = `
                <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                            background: white; padding: 30px; border-radius: 15px; 
                            box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 10000; 
                            max-width: 400px; text-align: center; border: 3px solid #667eea;">
                    <h3 style="color: #667eea; margin-bottom: 15px;">📱 Install ElectroZot App</h3>
                    <p style="margin-bottom: 20px; color: #333;">Look for the <strong>install icon (⊕)</strong> in your Chrome address bar and click it!</p>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0;">
                        <p style="margin: 0; font-size: 14px; color: #666;">
                            <strong>Alternative:</strong><br>
                            Chrome Menu (⋮) → "Install ElectroZot"
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            style="background: #667eea; color: white; border: none; padding: 10px 20px; 
                                   border-radius: 25px; cursor: pointer; font-weight: bold;">
                        Got it!
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 10000);
        }
        
        function showBrowserSpecificInstructions() {
            const userAgent = navigator.userAgent.toLowerCase();
            let instructions = '';
            
            if (userAgent.includes('firefox')) {
                instructions = 'Firefox: Look for the install prompt or "Add to Home Screen" option';
            } else if (userAgent.includes('safari')) {
                instructions = 'Safari: Tap Share button → "Add to Home Screen"';
            } else if (userAgent.includes('edg')) {
                instructions = 'Edge: Look for the install icon in the address bar';
            } else {
                instructions = 'Look for "Install" or "Add to Home Screen" in your browser menu';
            }
            
            showBriefInstallMessage(instructions);
        }
        }

        // Guide button click
        if (guideBtn) {
            guideBtn.addEventListener('click', () => {
                $('#pwaGuideModal').modal('show');
            });
        }

        // Try install again from modal
        if (tryInstallAgainBtn) {
            tryInstallAgainBtn.addEventListener('click', () => {
                $('#pwaGuideModal').modal('hide');
                if (mainInstallBtn) {
                    mainInstallBtn.click();
                }
            });
        }

        // Listen for successful installation
        window.addEventListener('appinstalled', () => {
            showInstalledState();
            
            // Show success message
            const successToast = document.createElement('div');
            successToast.innerHTML = `
                <div style="position: fixed; top: 20px; right: 20px; background: #28a745; color: white; 
                           padding: 15px 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
                           z-index: 10000; animation: slideInRight 0.3s ease-out;">
                    <i class="fas fa-check-circle"></i> App installed successfully!
                </div>
            `;
            document.body.appendChild(successToast);
            
            setTimeout(() => {
                successToast.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => successToast.remove(), 300);
            }, 3000);
        });

        // Function to simulate user engagement and trigger install prompt
        function simulateUserEngagement() {
            // Dispatch various user interaction events
            const events = ['click', 'scroll', 'keydown', 'touchstart'];
            events.forEach(eventType => {
                const event = new Event(eventType, { bubbles: true });
                document.dispatchEvent(event);
            });
            
            // Simulate scroll
            window.scrollBy(0, 1);
            window.scrollBy(0, -1);
            
            // Try again if prompt not available
            setTimeout(() => {
                if (!deferredPrompt && !window.pwaInstallEvent) {
                    setTimeout(simulateUserEngagement, 3000);
                }
            }, 1000);
        }
        
        // Event delegation backup for install button
        document.addEventListener('click', async (event) => {
            if (event.target.id === 'pwa-main-install-btn' || 
                event.target.closest('#pwa-main-install-btn')) {
                
                event.preventDefault();
                event.stopPropagation();
                
                // Visual feedback
                const btn = event.target.closest('#pwa-main-install-btn') || event.target;
                btn.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    btn.style.transform = 'scale(1)';
                }, 150);
                
                await attemptDirectInstall();
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            if (!checkIfInstalled()) {
                // Always show both buttons - they handle their own logic
                showInstallableState();
                
                // Ensure install button handler is attached
                setTimeout(attachInstallButtonHandler, 500);
                
                // Start simulating user engagement to trigger install prompt
                setTimeout(simulateUserEngagement, 2000);
            }
        });

        // Add CSS animations
        const pwaStyles = document.createElement('style');
        pwaStyles.textContent = `
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(100%); }
                to { opacity: 1; transform: translateX(0); }
            }
            @keyframes slideOutRight {
                from { opacity: 1; transform: translateX(0); }
                to { opacity: 0; transform: translateX(100%); }
            }
            
            .pwa-install-section .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important;
            }
            
            .install-steps li {
                margin-bottom: 8px;
                padding-left: 5px;
            }
            
            .install-steps li strong {
                color: #007bff;
            }
            
            /* Mobile responsive adjustments */
            @media (max-width: 768px) {
                .pwa-install-section {
                    padding: 15px 0 !important;
                }
                
                .pwa-install-section h3 {
                    font-size: 1.2rem !important;
                }
                
                .pwa-install-section p {
                    font-size: 0.85rem !important;
                }
                
                .pwa-install-buttons .btn {
                    padding: 10px 20px !important;
                    font-size: 0.9rem !important;
                    margin: 5px !important;
                    display: block !important;
                    width: 100% !important;
                }
                
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
                
                @keyframes slideUp {
                    from { transform: translateX(-50%) translateY(100px); opacity: 0; }
                    to { transform: translateX(-50%) translateY(0); opacity: 1; }
                }
                
                @keyframes slideDown {
                    from { transform: translateX(-50%) translateY(0); opacity: 1; }
                    to { transform: translateX(-50%) translateY(100px); opacity: 0; }
                }
                
                .modal-dialog {
                    margin: 10px !important;
                }
                
                .modal-body {
                    padding: 15px !important;
                }
                
                .install-steps {
                    font-size: 0.8rem !important;
                }
            }
        `;
        document.head.appendChild(pwaStyles);
    </script>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>