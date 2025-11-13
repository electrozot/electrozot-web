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
        <section class="hero-section-enhanced" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 80px 0 80px 0; position: relative; overflow: hidden;">
            
            <!-- Background Decorative Elements -->
            <div class="hero-bg-elements">
                <!-- Large Circles -->
                <div class="hero-circle hero-circle-1" style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(255, 215, 0, 0.08); border-radius: 50%; z-index: 1;"></div>
                <div class="hero-circle hero-circle-2" style="position: absolute; bottom: -150px; left: -150px; width: 500px; height: 500px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; z-index: 1;"></div>
                <div class="hero-circle hero-circle-3" style="position: absolute; top: 50%; right: 10%; width: 200px; height: 200px; background: rgba(255, 215, 0, 0.05); border-radius: 50%; z-index: 1;"></div>
                
                <!-- Floating Icons -->
                <div class="floating-icon" style="position: absolute; top: 15%; left: 8%; z-index: 1; opacity: 0.15;">
                    <i class="fas fa-bolt" style="font-size: 3rem; color: #ffd700;"></i>
                </div>
                <div class="floating-icon" style="position: absolute; top: 60%; right: 15%; z-index: 1; opacity: 0.12;">
                    <i class="fas fa-tools" style="font-size: 2.5rem; color: #ffffff;"></i>
                </div>
                <div class="floating-icon" style="position: absolute; bottom: 20%; left: 12%; z-index: 1; opacity: 0.1;">
                    <i class="fas fa-cog" style="font-size: 2rem; color: #ffd700;"></i>
                </div>
                <div class="floating-icon" style="position: absolute; top: 25%; right: 25%; z-index: 1; opacity: 0.08;">
                    <i class="fas fa-wrench" style="font-size: 2.2rem; color: #ffffff;"></i>
                </div>
                
                <!-- Geometric Shapes -->
                <div class="hero-shape" style="position: absolute; top: 10%; left: 5%; width: 80px; height: 80px; border: 3px solid rgba(255, 215, 0, 0.2); border-radius: 15px; transform: rotate(45deg); z-index: 1;"></div>
                <div class="hero-shape" style="position: absolute; bottom: 15%; right: 8%; width: 60px; height: 60px; border: 3px solid rgba(255, 255, 255, 0.15); border-radius: 50%; z-index: 1;"></div>
                <div class="hero-shape" style="position: absolute; top: 40%; left: 3%; width: 40px; height: 40px; background: rgba(255, 215, 0, 0.1); border-radius: 8px; transform: rotate(25deg); z-index: 1;"></div>
                
                <!-- Dots Pattern -->
                <div class="dots-pattern" style="position: absolute; top: 30%; right: 5%; z-index: 1; opacity: 0.2;">
                    <div style="display: grid; grid-template-columns: repeat(4, 8px); gap: 12px;">
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                        <div style="width: 8px; height: 8px; background: #ffd700; border-radius: 50%;"></div>
                    </div>
                </div>
                
                <!-- Wave Lines -->
                <svg class="hero-wave" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 150px; z-index: 1; opacity: 0.1;" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M0,50 Q300,0 600,50 T1200,50 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.3)"></path>
                </svg>
            </div>
            
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-white mb-5 mb-lg-0 hero-content">
                        <h1 class="display-4 font-weight-bold mb-4 hero-title" style="text-shadow: 3px 3px 6px rgba(0,0,0,0.3);">
                            Welcome to <span class="gradient-text">Electrozot</span>
                        </h1>
                        <p class="lead mb-4 hero-description" style="font-size: 1.3rem; line-height: 1.8; text-shadow: 1px 1px 3px rgba(0,0,0,0.2);">
                            Professional Technician Booking System - Book expert technicians for all your electrical, plumbing, HVAC, and appliance repair needs. Fast, reliable, and hassle-free service.
                        </p>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            <a href="#booking-form" id="book-service-btn" class="feature-badge pulse-animation" role="button" aria-label="Book service now" style="text-decoration: none;">
                                <i class="fas fa-bolt"></i> Book Service
                            </a>
                            <a href="tel:7559606925" class="feature-badge pulse-animation" style="animation-delay: 0.2s; text-decoration: none;" aria-label="Call 7559606925">
                                <i class="fas fa-phone"></i> Call 7559606925
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="booking-card" id="booking-form">
                            <div class="card shadow-lg border-0 booking-form-card" style="border-radius: 20px; overflow: hidden; position: relative; border: 3px solid #ffd700;">
                                <!-- Sliding Images Background -->
                                <div class="form-sliding-bg">
                                    <div class="slide-image active" style="background-image: url('vendor/img/slide_2.jpg');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/slide01.jpeg');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/p_banner1.jpg');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/service1.png');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/service2.png');"></div>
                                </div>
                                <div class="form-overlay"></div>
                                
                                <div class="card-header text-center py-4 booking-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; z-index: 3; border-bottom: 4px solid #ffd700;">
                                    <h3 class="mb-1 font-weight-bold" style="color: #ffffff; font-size: 1.8rem; font-family: 'Segoe UI', sans-serif;">
                                        <i class="fas fa-calendar-check" style="color: #ffd700; margin-right: 10px;"></i>
                                        Book Service Now
                                    </h3>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.9); font-size: 0.95rem;">Fill the form below to get started</p>
                                </div>
                                <div class="card-body p-4" style="position: relative; z-index: 2; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);">
                                    <?php if(isset($_SESSION['booking_success'])) { ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <?php if(isset($_SESSION['booking_error'])) { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <form method="POST" action="process-guest-booking.php" class="booking-form-enhanced" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label-enhanced"><i class="fas fa-user"></i> Full Name <span style="color: #e74c3c;">*</span></label>
                                                    <input type="text" class="form-control form-control-enhanced" name="customer_name" required placeholder="Enter your full name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label-enhanced"><i class="fas fa-phone"></i> Phone Number <span style="color: #e74c3c;">*</span></label>
                                                    <input type="tel" class="form-control form-control-enhanced" name="customer_phone" required placeholder="10-digit mobile number" maxlength="10" pattern="^[0-9]{10}$" title="Enter exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label style="font-size:0.95rem;"><i class="fas fa-map-pin text-primary"></i> Pincode</label>
                                                    <input type="text" class="form-control form-control-sm" name="customer_pincode" required placeholder="6-digit" maxlength="6" pattern="^[0-9]{6}$" title="Enter exactly 6 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group ">
                                                    <label style="font-size:0.95rem;"><i class="fas fa-tools text-primary"></i> Select Service</label>
                                                    <select class="p-0 form-control form-control-sm" name="sb_service_id" required>
                                                        <option value="">Choose a service...</option>
                                                        <?php
                                                        $ret="SELECT * FROM tms_service WHERE s_status = 'Active'";
                                                        $stmt= $mysqli->prepare($ret);
                                                        $stmt->execute();
                                                        $res=$stmt->get_result();
                                                        while($row=$res->fetch_object()) {
                                                            echo '<option value="'.$row->s_id.'">'.$row->s_name.' - $'.number_format($row->s_price, 2).'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label style="font-size:0.95rem;"><i class="fas fa-calendar text-primary"></i> Date</label>
                                                    <input type="date" class="form-control form-control-sm" name="sb_booking_date" required min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label style="font-size:0.95rem;"><i class="fas fa-map-marker-alt text-primary"></i> Address</label>
                                                    <textarea class="form-control form-control-sm" name="sb_address" rows="2" required placeholder="Enter complete service address"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group mb-3">
                                                    <label style="font-size:0.95rem;"><i class="fas fa-comment text-primary"></i> Additional Notes</label>
                                                    <textarea class="form-control form-control-sm" name="sb_description" rows="2" placeholder="Any special requirements or additional information"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="submit" name="book_service_guest" class="btn btn-block btn-sm text-gray submit-btn" style="background: linear-gradient(135deg, #13b7f8ff 0%, #40ff22ff 50%, #4facfe 100%); background-size: 200% 200%; border: none; padding: 10px; font-weight: 600; border-radius: 10px; position: relative; overflow: hidden; transition: all 0.4s ease;">
                                                    <span style="position: relative; z-index: 2;"><i class="fas fa-paper-plane"></i> Submit</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
                                    <i class="fas fa-star"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #2d3748;">Why Us</h5>
                                <p class="card-text" style="color: #4a5568; line-height: 1.6; font-size: 0.95rem;">We create accountability in the transport sector, enhance mobility and ease of accessing various transport modes. Our commitment to excellence ensures you get the best service every time.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card card h-100 border-0 feature-card-2" style="border-radius: 20px; overflow: hidden; position: relative;">
                            <div class="feature-gradient-bg-2"></div>
                            <div class="card-body p-3" style="position: relative; z-index: 2; text-align: left;">
                                <div class="feature-icon mb-3 icon-bounce" style="animation-delay: 0.2s; font-size: 1.8rem;">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #2d3748;">Core Values</h5>
                                <p class="card-text" style="color: #4a5568; line-height: 1.6; font-size: 0.95rem;">
                                    Excellence, Trust and Openness, Integrity, Take Responsibility, Customer Orientation. These values guide everything we do and ensure your satisfaction.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100 border-0" style="border-radius: 20px; overflow: hidden; background: #ffffff; box-shadow: 0 6px 20px rgba(0,0,0,0.06);">
                            <div class="card-body p-3" style="position: relative; z-index: 2;">
                                <h5 class="font-weight-bold mb-2" style="color: #2d3748;">Services We Done & Our Happy Customers</h5>
                                <div id="home-gallery-slider" class="home-slider">
                                    <div class="home-slider-track">
                                        <?php
                                        // Ensure gallery table exists
                                        $mysqli->query("CREATE TABLE IF NOT EXISTS tms_gallery (\n                                            g_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,\n                                            g_title VARCHAR(255) NOT NULL,\n                                            g_image VARCHAR(255) NOT NULL,\n                                            g_service_id INT NULL,\n                                            g_description TEXT NULL,\n                                            g_status VARCHAR(20) NOT NULL DEFAULT 'Active',\n                                            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP\n                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
                                        $q = $mysqli->prepare("SELECT g_image, g_title FROM tms_gallery WHERE g_status='Active' ORDER BY created_at DESC LIMIT 12");
                                        $q->execute();
                                        $r = $q->get_result();
                                        while($item = $r->fetch_object()) {
                                        ?>
                                            <div class="home-slider-item">
                                                <img src="<?php echo htmlspecialchars($item->g_image); ?>" alt="<?php echo htmlspecialchars($item->g_title); ?>">
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
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
                    $ret="SELECT * FROM tms_service WHERE s_status = 'Active' LIMIT 3";
                    $stmt= $mysqli->prepare($ret);
                    $stmt->execute();
                    $res=$stmt->get_result();
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
                                        $<?php echo number_format($row->s_price, 2); ?>
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
                        <div id="workCarousel" class="carousel slide shadow-lg" data-ride="carousel" style="border-radius: 15px; overflow: hidden;">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <li data-target="#workCarousel" data-slide-to="0" class="active"></li>
                                <li data-target="#workCarousel" data-slide-to="1"></li>
                                <li data-target="#workCarousel" data-slide-to="2"></li>
                                <li data-target="#workCarousel" data-slide-to="3"></li>
                                <li data-target="#workCarousel" data-slide-to="4"></li>
                            </ol>

                            <!-- Slides -->
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="vendor/img/completions/sb19_1762849470.jpg" class="d-block w-100" alt="Completed Work 1" style="height: 500px; object-fit: cover;">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px;">
                                        <h5 class="font-weight-bold">Professional Service Completed</h5>
                                        <p>Quality work delivered with customer satisfaction</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="vendor/img/completions/sb21_1762850637.png" class="d-block w-100" alt="Completed Work 2" style="height: 500px; object-fit: cover;">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px;">
                                        <h5 class="font-weight-bold">Expert Installation</h5>
                                        <p>Professional installation with attention to detail</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="vendor/img/completions/sb23_1762856579.png" class="d-block w-100" alt="Completed Work 3" style="height: 500px; object-fit: cover;">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px;">
                                        <h5 class="font-weight-bold">Quality Workmanship</h5>
                                        <p>Delivering excellence in every project</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="vendor/img/completions/sb27_1762875513.jpg" class="d-block w-100" alt="Completed Work 4" style="height: 500px; object-fit: cover;">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px;">
                                        <h5 class="font-weight-bold">Reliable Service</h5>
                                        <p>Trusted by hundreds of satisfied customers</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="vendor/img/completions/sb30_1762947816.jpg" class="d-block w-100" alt="Completed Work 5" style="height: 500px; object-fit: cover;">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px;">
                                        <h5 class="font-weight-bold">Professional Results</h5>
                                        <p>Your satisfaction is our priority</p>
                                    </div>
                                </div>
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

                        <!-- Stats Below Carousel -->
                        <div class="row mt-5 text-center">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3" style="background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    <h3 class="font-weight-bold mb-0" style="color: #667eea;">500+</h3>
                                    <p class="text-muted mb-0 small">Projects Completed</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3" style="background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    <h3 class="font-weight-bold mb-0" style="color: #667eea;">450+</h3>
                                    <p class="text-muted mb-0 small">Happy Clients</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3" style="background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    <h3 class="font-weight-bold mb-0" style="color: #667eea;">50+</h3>
                                    <p class="text-muted mb-0 small">Expert Technicians</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3" style="background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    <h3 class="font-weight-bold mb-0" style="color: #667eea;">5+</h3>
                                    <p class="text-muted mb-0 small">Years Experience</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-5 testimonials-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); background-size: 200% 200%; animation: gradientShift 15s ease infinite; position: relative; overflow: hidden;">
            <div class="testimonial-overlay"></div>
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="display-5 font-weight-bold mb-3 text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                            Client Testimonials
                        </h2>
                        <p class="lead text-white" style="opacity: 0.95; font-weight: 400;">What our customers say about us</p>
                    </div>
                </div>
                <div class="row">
                    <?php
                    $ret="SELECT * FROM tms_feedback where f_status ='Published' ORDER BY RAND() LIMIT 3";
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
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card card h-100 border-0 testimonial-card-hover" style="border-radius: 20px; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="fas fa-quote-left testimonial-quote" style="font-size: 2.5rem; background: <?php echo $testGradient; ?>; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; opacity: 0.4;"></i>
                                </div>
                                <p class="card-text mb-4 testimonial-text" style="font-style: italic; line-height: 1.9; color: #4a5568; font-size: 1.05rem;">
                                    "<?php echo $row->f_content; ?>"
                                </p>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3 testimonial-avatar" style="width: 60px; height: 60px; border-radius: 50%; background: <?php echo $testGradient; ?>; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        <?php echo strtoupper(substr($row->f_uname, 0, 1)); ?>
                                    </div>
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
            let currentSlide = 0;
            const slides = $('.slide-image');
            const totalSlides = slides.length;
            
            function showSlide(index) {
                slides.removeClass('active');
                slides.eq(index).addClass('active');
            }
            
            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                showSlide(currentSlide);
            }
            
            // Initialize first slide
            showSlide(0);
            
            // Change slide every 4 seconds
            setInterval(nextSlide, 4000);

            // Initialize Work Carousel with auto-play
            $('#workCarousel').carousel({
                interval: 4000,
                pause: 'hover',
                wrap: true
            });
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
    <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->


</body>

</html>