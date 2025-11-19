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
    <div class="container-fluid px-0">
        
        <!-- Enhanced Hero Section with Background Elements -->
        <section class="hero-section-enhanced" style="background: linear-gradient(135deg, #F0E5D8 0%, #E8D5C4 100%); padding: 80px 0 80px 0; position: relative; overflow: hidden;">
            
            <!-- Enhanced Background Decorative Elements -->
            <div class="hero-bg-elements">
                <!-- Animated Large Circles with Glow -->
                <div class="hero-circle hero-circle-1" style="position: absolute; top: -100px; right: -100px; width: 450px; height: 450px; background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, rgba(255, 215, 0, 0.05) 50%, transparent 100%); border-radius: 50%; z-index: 1; animation: float 8s ease-in-out infinite;"></div>
                <div class="hero-circle hero-circle-2" style="position: absolute; bottom: -150px; left: -150px; width: 550px; height: 550px; background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.04) 50%, transparent 100%); border-radius: 50%; z-index: 1; animation: float 10s ease-in-out infinite reverse;"></div>
                <div class="hero-circle hero-circle-3" style="position: absolute; top: 50%; right: 10%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%); border-radius: 50%; z-index: 1; animation: pulse 6s ease-in-out infinite;"></div>
                <div class="hero-circle hero-circle-4" style="position: absolute; top: 20%; left: 15%; width: 180px; height: 180px; background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%); border-radius: 50%; z-index: 1; animation: float 7s ease-in-out infinite;"></div>
                
                <!-- Enhanced Floating Icons with Animation -->
                <div class="floating-icon" style="position: absolute; top: 15%; left: 8%; z-index: 1; opacity: 0.25; animation: floatIcon 4s ease-in-out infinite;">
                    <i class="fas fa-bolt" style="font-size: 3.5rem; color: #ffd700; filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));"></i>
                </div>
                <div class="floating-icon" style="position: absolute; top: 60%; right: 15%; z-index: 1; opacity: 0.2; animation: floatIcon 5s ease-in-out infinite 1s;">
                    <i class="fas fa-tools" style="font-size: 3rem; color: #ffffff; filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.4));"></i>
                </div>
                <div class="floating-icon" style="position: absolute; bottom: 20%; left: 12%; z-index: 1; opacity: 0.18; animation: floatIcon 6s ease-in-out infinite 2s;">
                    <i class="fas fa-cog" style="font-size: 2.5rem; color: #ffd700; filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5)); animation: rotate 20s linear infinite;"></i>
                </div>
                <div class="floating-icon" style="position: absolute; top: 25%; right: 25%; z-index: 1; opacity: 0.15; animation: floatIcon 5.5s ease-in-out infinite 1.5s;">
                    <i class="fas fa-wrench" style="font-size: 2.8rem; color: #ffffff; filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.4));"></i>
                </div>
                <div class="floating-icon" style="position: absolute; top: 70%; left: 25%; z-index: 1; opacity: 0.2; animation: floatIcon 4.5s ease-in-out infinite 0.5s;">
                    <i class="fas fa-plug" style="font-size: 2.3rem; color: #ffd700; filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));"></i>
                </div>
                
                <!-- Enhanced Geometric Shapes with Glow -->
                <div class="hero-shape" style="position: absolute; top: 10%; left: 5%; width: 90px; height: 90px; border: 4px solid rgba(255, 215, 0, 0.3); border-radius: 18px; transform: rotate(45deg); z-index: 1; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); animation: rotate 15s linear infinite;"></div>
                <div class="hero-shape" style="position: absolute; bottom: 15%; right: 8%; width: 70px; height: 70px; border: 4px solid rgba(255, 255, 255, 0.25); border-radius: 50%; z-index: 1; box-shadow: 0 0 15px rgba(255, 255, 255, 0.15); animation: pulse 4s ease-in-out infinite;"></div>
                <div class="hero-shape" style="position: absolute; top: 40%; left: 3%; width: 50px; height: 50px; background: rgba(255, 215, 0, 0.15); border-radius: 10px; transform: rotate(25deg); z-index: 1; box-shadow: 0 0 15px rgba(255, 215, 0, 0.2); animation: float 6s ease-in-out infinite;"></div>
                <div class="hero-shape" style="position: absolute; top: 35%; right: 5%; width: 65px; height: 65px; border: 3px solid rgba(255, 255, 255, 0.2); border-radius: 12px; transform: rotate(-30deg); z-index: 1; animation: rotate 12s linear infinite reverse;"></div>
                
                <!-- Enhanced Dots Pattern with Glow -->
                <div class="dots-pattern" style="position: absolute; top: 30%; right: 5%; z-index: 1; opacity: 0.3; animation: pulse 3s ease-in-out infinite;">
                    <div style="display: grid; grid-template-columns: repeat(4, 10px); gap: 15px;">
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                        <div style="width: 10px; height: 10px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                    </div>
                </div>
                
                <!-- Additional Dots Pattern -->
                <div class="dots-pattern" style="position: absolute; bottom: 25%; left: 8%; z-index: 1; opacity: 0.25; animation: pulse 4s ease-in-out infinite 1s;">
                    <div style="display: grid; grid-template-columns: repeat(3, 8px); gap: 12px;">
                        <div style="width: 8px; height: 8px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);"></div>
                        <div style="width: 8px; height: 8px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);"></div>
                        <div style="width: 8px; height: 8px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);"></div>
                        <div style="width: 8px; height: 8px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);"></div>
                        <div style="width: 8px; height: 8px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);"></div>
                        <div style="width: 8px; height: 8px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);"></div>
                    </div>
                </div>
                
                <!-- Enhanced Wave Lines with Multiple Layers -->
                <svg class="hero-wave" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 180px; z-index: 1; opacity: 0.15;" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M0,50 Q300,0 600,50 T1200,50 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.4)"></path>
                    <path d="M0,70 Q300,40 600,70 T1200,70 L1200,120 L0,120 Z" fill="rgba(255,215,0,0.2)"></path>
                </svg>
                
                <!-- Sparkle Effects -->
                <div style="position: absolute; top: 18%; right: 12%; width: 4px; height: 4px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 15px #ffd700; animation: sparkle 2s ease-in-out infinite;"></div>
                <div style="position: absolute; top: 45%; left: 18%; width: 3px; height: 3px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 12px #ffffff; animation: sparkle 3s ease-in-out infinite 1s;"></div>
                <div style="position: absolute; bottom: 30%; right: 20%; width: 5px; height: 5px; background: #ffd700; border-radius: 50%; box-shadow: 0 0 18px #ffd700; animation: sparkle 2.5s ease-in-out infinite 0.5s;"></div>
            </div>
            
            <style>
                @keyframes float {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-20px); }
                }
                
                @keyframes floatIcon {
                    0%, 100% { transform: translateY(0px) translateX(0px); }
                    25% { transform: translateY(-15px) translateX(5px); }
                    75% { transform: translateY(15px) translateX(-5px); }
                }
                
                @keyframes pulse {
                    0%, 100% { transform: scale(1); opacity: 0.3; }
                    50% { transform: scale(1.1); opacity: 0.5; }
                }
                
                @keyframes rotate {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                
                @keyframes sparkle {
                    0%, 100% { opacity: 0; transform: scale(0); }
                    50% { opacity: 1; transform: scale(1); }
                }
            </style>
            
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0 hero-content" style="padding-right: 30px;">
                        <h1 class="display-4 font-weight-bold mb-4 hero-title" style="color: #2d3748; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                            Welcome to <span class="electrozot-animated" style="color: #8b0000;">Electrozot</span>
                        </h1>
                        <p class="lead mb-4 hero-description" style="font-size: 1.3rem; line-height: 1.8; color: #4a5568; text-shadow: 1px 1px 2px rgba(255,255,255,0.3); font-weight: 700;">
                            Your Trusted Partner for Perfect Work. Quality Service. Certified Technicians. We Make Perfection Our Promise.
                        </p>
                        <div class="d-flex flex-wrap" style="gap: 12px;">
                            <a href="#booking-form" id="book-service-btn" class="feature-badge" role="button" aria-label="Book service now" style="text-decoration: none; background: #2d3748; color: white; padding: 10px 25px; border-radius: 25px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 12px rgba(45, 55, 72, 0.4); transition: all 0.3s ease; min-width: 150px; text-align: center;">
                                <i class="fas fa-bolt"></i> Book Service
                            </a>
                            <a href="tel:7559606925" class="feature-badge" style="text-decoration: none; background: #2d3748; color: white; padding: 10px 25px; border-radius: 25px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 12px rgba(45, 55, 72, 0.4); transition: all 0.3s ease; min-width: 180px; text-align: center;">
                                <i class="fas fa-phone"></i> 7559606925
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="booking-card" id="booking-form">
                            <div class="card shadow-lg border-0 booking-form-card" style="border-radius: 20px; overflow: hidden; position: relative; border: 3px solid #8b0000;">
                                
                                <div class="card-header text-center py-3 booking-header" style="background: linear-gradient(135deg, #0d7a6f 0%, #2bc569 100%); position: relative; z-index: 3; border-bottom: 3px solid #0d7a6f; overflow: hidden;">
                                    <h3 class="mb-0 font-weight-bold" style="color: white; font-size: 1.4rem; font-family: 'Segoe UI', sans-serif; position: relative; z-index: 2; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
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
                                        <div class="text-center mt-2">
                                            <button type="submit" name="book_service_guest" class="btn btn-primary btn-sm px-4 booking-submit-btn" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; font-weight: 600; padding: 8px 30px; transition: all 0.3s ease; position: relative; overflow: hidden; color: white;">
                                                <i class="fas fa-paper-plane"></i> Submit Booking
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
                font-size: 0.85rem !important;
                font-weight: 600 !important;
                color: #495057 !important;
                margin-bottom: 3px !important;
                display: block !important;
            }
            
            .booking-form-compact .form-label-compact i {
                color: #dc143c !important;
                margin-right: 5px !important;
            }
            
            .booking-form-compact .form-control {
                font-size: 0.9rem !important;
                padding: 8px 12px !important;
                height: auto !important;
                min-height: 40px !important;
                border-radius: 10px !important;
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
                padding: 8px 12px !important;
                line-height: 1.6;
                font-size: 0.9rem !important;
                font-weight: 500;
                width: 100% !important;
                resize: vertical !important;
            }
            
            .booking-form-compact select.form-control {
                font-size: 0.9rem !important;
                font-weight: 500;
                width: 100% !important;
                padding: 8px 12px !important;
                min-height: 40px !important;
                cursor: pointer !important;
                background-image: linear-gradient(45deg, transparent 50%, #dc143c 50%), linear-gradient(135deg, #dc143c 50%, transparent 50%) !important;
                background-position: calc(100% - 15px) center, calc(100% - 10px) center !important;
                background-size: 5px 5px, 5px 5px !important;
                background-repeat: no-repeat !important;
                padding-right: 35px !important;
            }
            
            .booking-form-compact .form-group {
                margin-bottom: 0.75rem !important;
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
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5) !important;
            }
            
            .booking-submit-btn:active {
                transform: translateY(0) !important;
            }
            
            /* Button shine effect */
            .booking-submit-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
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
                /* Reduce booking form width on small screens */
                .booking-card {
                    max-width: 95% !important;
                    margin: 0 auto !important;
                }
                
                .booking-form-card {
                    margin: 0 auto !important;
                }
                
                /* Compact spacing for mobile - keep text size same */
                .booking-form-compact .form-label-compact {
                    margin-bottom: 3px !important;
                }
                
                .booking-form-compact .form-group {
                    margin-bottom: 0.6rem !important;
                }
                
                /* Compact card on mobile */
                .card-body.p-3 {
                    padding: 0.75rem !important;
                }
                
                .card-header.py-3 {
                    padding: 0.6rem !important;
                }
                
                /* Stack columns properly on mobile */
                .booking-form-compact .col-md-6 {
                    padding-left: 8px !important;
                    padding-right: 8px !important;
                    flex: 0 0 100% !important;
                    max-width: 100% !important;
                }
                
                .booking-form-compact .row {
                    margin-left: -8px !important;
                    margin-right: -8px !important;
                }
                
                /* Ensure all form controls are full width and aligned on mobile */
                .booking-form-compact .form-control,
                .booking-form-compact select.form-control,
                .booking-form-compact textarea.form-control,
                .service-dropdown {
                    width: 100% !important;
                    max-width: 100% !important;
                }
                
                /* Hide duplicate dropdown error by ensuring proper spacing */
                .booking-form-compact .form-group + .form-group {
                    margin-top: 0.6rem !important;
                }
                
                /* Smaller service cards on mobile */
                .service-card {
                    transform: scale(0.9) !important;
                }
                
                .service-img-wrapper {
                    height: 150px !important;
                }
                
                .service-card .card-body {
                    padding: 1rem !important;
                }
                
                .service-card .card-title {
                    font-size: 1rem !important;
                }
                
                .service-card .card-text {
                    font-size: 0.85rem !important;
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
        <section class="py-5 features-section" style="background: linear-gradient(180deg, #ffffff 0%, #f0f4ff 100%); position: relative;">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="display-5 font-weight-bold mb-3 section-title">
                            <span class="gradient-text-2">Why Choose Electrozot?</span>
                        </h2>
                        <p class="lead" style="color: #6c757d; font-weight: 500;">Professional service you can trust</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card card h-100 border-0 feature-card-1" style="border-radius: 20px; overflow: hidden; position: relative;">
                            <div class="feature-gradient-bg"></div>
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-3 icon-bounce" style="font-size: 1.8rem;">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #2d3748;">Professional Trained Teams</h5>
                                <p class="card-text" style="color: #4a5568; line-height: 1.6; font-size: 0.95rem;">We have professional trained teams and experts for every service. Our skilled technicians are certified and experienced to handle all your electrical, plumbing, and appliance repair needs with precision and care.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card card h-100 border-0 feature-card-2" style="border-radius: 20px; overflow: hidden; position: relative;">
                            <div class="feature-gradient-bg-2"></div>
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-3 icon-bounce" style="animation-delay: 0.2s; font-size: 1.8rem;">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #2d3748;">On-Time & Affordable Service</h5>
                                <p class="card-text" style="color: #4a5568; line-height: 1.6; font-size: 0.95rem;">
                                    We commit our service on time with affordable and transparent prices for all. No hidden charges, no surprises - just honest pricing and punctual service delivery that respects your time and budget.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card card h-100 border-0 feature-card-3" style="border-radius: 20px; overflow: hidden; position: relative;">
                            <div class="feature-gradient-bg-3"></div>
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-3 icon-bounce" style="animation-delay: 0.4s; font-size: 1.8rem;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #2d3748;">1 Month Warranty & Trust</h5>
                                <p class="card-text" style="color: #4a5568; line-height: 1.6; font-size: 0.95rem;">
                                    We provide you 1 month warranty on all repairs and parts we provide, so we are trusted. Your satisfaction is guaranteed with our comprehensive warranty coverage and reliable after-service support.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Services Portfolio Section -->
        <section class="py-5 services-section" style="background: linear-gradient(180deg, #f0f4ff 0%, #ffffff 100%);">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="display-5 font-weight-bold mb-3 section-title">
                            <span class="gradient-text-2">Our Popular Services</span>
                        </h2>
                        <p class="lead" style="color: #6c757d; font-weight: 500;">Expert technicians for all your needs</p>
                    </div>
                </div>
                <div class="row">
                    <?php
                    // Add is_popular column if it doesn't exist
                    $mysqli->query("ALTER TABLE tms_service ADD COLUMN IF NOT EXISTS is_popular TINYINT(1) DEFAULT 0");
                    
                    // First try to get popular services
                    $ret="SELECT * FROM tms_service WHERE s_status = 'Active' AND is_popular = 1 ORDER BY s_id DESC LIMIT 3";
                    $stmt= $mysqli->prepare($ret);
                    $stmt->execute();
                    $res=$stmt->get_result();
                    
                    // If no popular services, get latest 3 active services
                    if($res->num_rows == 0) {
                        $ret="SELECT * FROM tms_service WHERE s_status = 'Active' ORDER BY s_id DESC LIMIT 3";
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
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="#booking-form" class="service-card-link" style="text-decoration: none; display: block;">
                        <div class="service-card card h-100 border-0 service-card-hover" style="border-radius: 20px; overflow: hidden; position: relative; cursor: pointer;">
                            <div class="service-gradient-overlay" style="background: <?php echo $gradient; ?>;"></div>
                            <div class="card-img-wrapper service-img-wrapper" style="height: 220px; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tools text-white service-icon" style="font-size: 5rem; position: relative; z-index: 2; transition: all 0.4s ease;"></i>
                                <div class="service-shine"></div>
                            </div>
                            <div class="card-body p-4" style="background: white; position: relative; z-index: 2;">
                                <h4 class="card-title font-weight-bold mb-3 service-title" style="color: #2d3748; transition: color 0.3s ease;">
                                    <?php echo $row->s_name; ?>
                                </h4>
                                <p class="text-muted mb-4" style="line-height: 1.7;"><?php echo substr($row->s_description, 0, 100); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price-badge px-4 py-2" style="background: <?php echo $gradient; ?>; color: white; font-size: 1.1rem; font-weight: 700; border-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        ₹<?php echo number_format($row->s_price, 0); ?>
                                    </span>
                                    <span class="text-muted" style="font-weight: 500;">
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
        <section class="py-5" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="display-5 font-weight-bold mb-3" style="color: #2d3748;">
                            <span class="gradient-text-2">Our Work</span> Portfolio
                        </h2>
                        <p class="lead text-muted">See the quality of our completed projects</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <?php
                        // Get active sliders from database
                        $slider_query = "SELECT * FROM tms_home_slider WHERE slider_status = 'Active' ORDER BY slider_order ASC, slider_id DESC";
                        $slider_result = $mysqli->query($slider_query);
                        $slider_count = $slider_result ? $slider_result->num_rows : 0;
                        ?>
                        
                        <?php if($slider_count > 0): ?>
                            <div id="workCarousel" class="carousel slide shadow-lg" data-ride="carousel" style="border-radius: 15px; overflow: hidden;">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
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
                                <div class="carousel-inner">
                                    <?php 
                                    $slider_result->data_seek(0);
                                    $index = 0;
                                    while($slider = $slider_result->fetch_object()): 
                                    ?>
                                        <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                                            <img src="admin/vendor/img/slider/<?php echo $slider->slider_image; ?>" 
                                                 class="d-block w-100" 
                                                 alt="<?php echo htmlspecialchars($slider->slider_title); ?>" 
                                                 style="height: 500px; object-fit: cover;">
                                            <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px;">
                                                <h5 class="font-weight-bold"><?php echo htmlspecialchars($slider->slider_title); ?></h5>
                                                <p><?php echo htmlspecialchars($slider->slider_description); ?></p>
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
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> No portfolio images available at the moment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section with Auto-Sliding -->
        <section class="py-5 testimonials-section" style="background: linear-gradient(135deg, #89c9c6 0%, #e8b4c0 100%); background-size: 200% 200%; animation: gradientShift 15s ease infinite; position: relative; overflow: hidden;">
            <div class="testimonial-overlay"></div>
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="display-5 font-weight-bold mb-3" style="color: #2d3748; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">
                            Client Testimonials
                        </h2>
                        <p class="lead" style="color: #4a5568; font-weight: 400;">What our customers say about us</p>
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
                        <div class="testimonial-slide" style="display: inline-block; width: 350px; margin: 0 15px; vertical-align: top;">
                            <div class="testimonial-card card border-0" style="border-radius: 20px; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0,0,0,0.15); height: 100%;">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-quote-left testimonial-quote" style="font-size: 2.5rem; background: <?php echo $testGradient; ?>; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; opacity: 0.4;"></i>
                                    </div>
                                    <p class="card-text mb-4 testimonial-text" style="font-style: italic; line-height: 1.9; color: #2d3748; font-size: 1.05rem; min-height: 120px;">
                                        "<?php echo $row->f_content; ?>"
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <?php if(isset($row->f_photo) && $row->f_photo) { ?>
                                            <img src="<?php echo $row->f_photo; ?>" alt="<?php echo $row->f_uname; ?>" class="mr-3" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        <?php } else { ?>
                                            <div class="avatar-circle mr-3" style="width: 60px; height: 60px; border-radius: 50%; background: <?php echo $testGradient; ?>; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                                <?php echo strtoupper(substr($row->f_uname, 0, 1)); ?>
                                            </div>
                                        <?php } ?>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold" style="color: #2d3748;"><?php echo $row->f_uname; ?></h6>
                                            <small class="text-muted" style="font-weight: 500;">Verified Customer</small>
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
        // Get the base path for the application
        const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
        const swPath = basePath ? basePath + '/sw.js' : '/sw.js';
        
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register(swPath)
                    .then((registration) => {
                        console.log('✅ Service Worker registered successfully:', registration.scope);
                        console.log('📍 Service Worker path:', swPath);
                    })
                    .catch((error) => {
                        console.error('❌ Service Worker registration failed:', error);
                        console.log('Tried path:', swPath);
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

</body>

</html>