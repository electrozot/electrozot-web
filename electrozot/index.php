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
        
        <!-- Hero Section with Booking Form -->
        <section class="hero-section animated-gradient" style="padding: 80px 0 100px 0; position: relative; overflow: hidden;">
            <div class="gradient-overlay"></div>
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
                            <div class="feature-badge pulse-animation">
                                <i class="fas fa-bolt"></i> Fast Service
                            </div>
                            <div class="feature-badge pulse-animation" style="animation-delay: 0.2s;">
                                <i class="fas fa-shield-alt"></i> Trusted Experts
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="booking-card" id="booking-form">
                            <div class="card shadow-lg border-0 booking-form-card" style="border-radius: 25px; overflow: hidden; position: relative;">
                                <!-- Sliding Images Background -->
                                <div class="form-sliding-bg">
                                    <div class="slide-image active" style="background-image: url('vendor/img/slide_2.jpg');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/slide01.jpeg');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/p_banner1.jpg');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/service1.png');"></div>
                                    <div class="slide-image" style="background-image: url('vendor/img/service2.png');"></div>
                                </div>
                                <div class="form-overlay"></div>
                                
                                <div class="card-header text-gray  text-center py-4 booking-header" style="background: linear-gradient(135deg, #36d7f4ff 0%, #79fbabff 50%, #66ece0ff 100%); background-size: 300% 300%; animation: gradientShift 8s ease infinite; position: relative; z-index: 3;">
                                    <h3 class="mb-0"><i class="fas fa-calendar-check"></i> Book Service Now</h3>
                                
                                </div>
                                <div class="card-body p-4" style="position: relative; z-index: 2; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px);">
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
                                    <form method="POST" action="process-guest-booking.php">
                                        <div class="form-group">
                                            <label><i class="fas fa-user text-primary"></i> Full Name </label>
                                            <input type="text" class="form-control" name="customer_name" required placeholder="Enter your full name">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-envelope text-primary"></i> Email Address </label>
                                            <input type="email" class="form-control" name="customer_email" required placeholder="your email">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-phone text-primary"></i> Phone Number </label>
                                            <input type="tel" class="form-control" name="customer_phone" required placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-tools text-primary"></i> Select Service </label>
                                            <select class="form-control" name="sb_service_id" required>
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
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-calendar text-primary"></i> Booking Date </label>
                                                    <input type="date" class="form-control" name="sb_booking_date" required min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-clock text-primary"></i> Preferred Time </label>
                                                    <input type="time" class="form-control" name="sb_booking_time" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-map-marker-alt text-primary"></i> Service Address </label>
                                            <textarea class="form-control" name="sb_address" rows="2" required placeholder="Enter complete service address"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-comment text-primary"></i> Additional Notes</label>
                                            <textarea class="form-control" name="sb_description" rows="2" placeholder="Any special requirements or additional information"></textarea>
                                        </div>
                                        <button type="submit" name="book_service_guest" class="btn btn-block btn-lg text-gray submit-btn" style="background: linear-gradient(135deg, #13b7f8ff 0%, #40ff22ff 50%, #4facfe 100%); background-size: 200% 200%; border: none; padding: 15px; font-weight: 600; border-radius: 12px; position: relative; overflow: hidden; transition: all 0.4s ease;">
                                            <span style="position: relative; z-index: 2;"><i class="fas fa-paper-plane"></i> Submit Booking</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
                    <div class="col-lg-6 mb-4">
                        <div class="feature-card card h-100 border-0 feature-card-1" style="border-radius: 20px; overflow: hidden; position: relative;">
                            <div class="feature-gradient-bg"></div>
                            <div class="card-body p-5 text-center" style="position: relative; z-index: 2;">
                                <div class="feature-icon mb-4 icon-bounce">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4 class="card-title font-weight-bold mb-3" style="color: #2d3748;">Why Us</h4>
                                <p class="card-text" style="color: #4a5568; line-height: 1.8;">We create accountability in the transport sector, enhance mobility and ease of accessing various transport modes. Our commitment to excellence ensures you get the best service every time.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="feature-card card h-100 border-0 feature-card-2" style="border-radius: 20px; overflow: hidden; position: relative;">
                            <div class="feature-gradient-bg-2"></div>
                            <div class="card-body p-5 text-center" style="position: relative; z-index: 2;">
                                <div class="feature-icon mb-4 icon-bounce" style="animation-delay: 0.2s;">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <h4 class="card-title font-weight-bold mb-3" style="color: #2d3748;">Core Values</h4>
                                <p class="card-text" style="color: #4a5568; line-height: 1.8;">
                                    Excellence, Trust and Openness, Integrity, Take Responsibility, Customer Orientation. These values guide everything we do and ensure your satisfaction.
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
                        <div class="service-card card h-100 border-0 service-card-hover" style="border-radius: 20px; overflow: hidden; position: relative;">
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
                    </div>
                    <?php $cnt++; } ?>
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
        });
    </script>
    
</body>

</html>