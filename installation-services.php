<?php
// Include database configuration
include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Services - Electrozot</title>
    <?php include("vendor/inc/head.php");?>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("vendor/inc/nav.php");?>

    <main id="main-content" role="main">
    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 140px 0 50px 0; margin-top: -56px;">
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                    <i class="fas fa-cog" style="color: #EC4899;"></i> Installation Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #6B7280; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Professional installation services for all your home and office appliances
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px;">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #EC4899; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="services.php" style="color: #EC4899; text-decoration: none; font-weight: 600;">Services</a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-weight: 500;">Installation Services</li>
            </ol>
        </nav>

        <!-- Service Overview -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px;">
                    <div class="card-body p-4">
                        <h3 style="color: #2d3748; font-weight: 700; margin-bottom: 20px;">Why Choose Our Installation Services?</h3>
                        <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                            Our certified technicians provide quick, clean, and professional installation services for all types of appliances and systems. We ensure proper setup, safety compliance, and optimal performance.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installation Services Grid -->
        <div class="row">
            <!-- TV Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tv" style="color: #EC4899; margin-right: 8px;"></i>TV Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Wall mounting (all sizes)</li>
                            <li><i class="fas fa-check text-success me-2"></i>Cable management</li>
                            <li><i class="fas fa-check text-success me-2"></i>Setup & configuration</li>
                            <li><i class="fas fa-check text-success me-2"></i>Smart TV setup</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Dish Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-satellite-dish" style="color: #EC4899; margin-right: 8px;"></i>Dish Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>DTH dish setup</li>
                            <li><i class="fas fa-check text-success me-2"></i>Signal optimization</li>
                            <li><i class="fas fa-check text-success me-2"></i>Cable routing</li>
                            <li><i class="fas fa-check text-success me-2"></i>Multi-room setup</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- WiFi Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-wifi" style="color: #EC4899; margin-right: 8px;"></i>WiFi Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Router setup & configuration</li>
                            <li><i class="fas fa-check text-success me-2"></i>Network optimization</li>
                            <li><i class="fas fa-check text-success me-2"></i>Range extender setup</li>
                            <li><i class="fas fa-check text-success me-2"></i>Security configuration</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Washing Machine Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tshirt" style="color: #EC4899; margin-right: 8px;"></i>Washing Machine Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Plumbing connections</li>
                            <li><i class="fas fa-check text-success me-2"></i>Electrical connections</li>
                            <li><i class="fas fa-check text-success me-2"></i>Leveling & testing</li>
                            <li><i class="fas fa-check text-success me-2"></i>Demo & instructions</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Water Geyser Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-fire" style="color: #EC4899; margin-right: 8px;"></i>Water Geyser Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Wall mounting</li>
                            <li><i class="fas fa-check text-success me-2"></i>Plumbing connections</li>
                            <li><i class="fas fa-check text-success me-2"></i>Electrical wiring</li>
                            <li><i class="fas fa-check text-success me-2"></i>Safety testing</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Fan & Lights Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-lightbulb" style="color: #EC4899; margin-right: 8px;"></i>Fan & Lights Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Ceiling fan installation</li>
                            <li><i class="fas fa-check text-success me-2"></i>LED light fixtures</li>
                            <li><i class="fas fa-check text-success me-2"></i>Switch & dimmer setup</li>
                            <li><i class="fas fa-check text-success me-2"></i>Wiring & safety check</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Electric Chimney Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-wind" style="color: #EC4899; margin-right: 8px;"></i>Electric Chimney Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Wall mounting</li>
                            <li><i class="fas fa-check text-success me-2"></i>Ducting setup</li>
                            <li><i class="fas fa-check text-success me-2"></i>Electrical connections</li>
                            <li><i class="fas fa-check text-success me-2"></i>Performance testing</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Camera Installation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-video" style="color: #EC4899; margin-right: 8px;"></i>Camera Installation
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>CCTV camera setup</li>
                            <li><i class="fas fa-check text-success me-2"></i>DVR/NVR configuration</li>
                            <li><i class="fas fa-check text-success me-2"></i>Mobile app setup</li>
                            <li><i class="fas fa-check text-success me-2"></i>Remote monitoring</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- AC Servicing -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-snowflake" style="color: #EC4899; margin-right: 8px;"></i>AC Installation & Servicing
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Split AC installation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Copper piping</li>
                            <li><i class="fas fa-check text-success me-2"></i>Gas charging</li>
                            <li><i class="fas fa-check text-success me-2"></i>Regular servicing</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); border-radius: 20px;">
                    <div class="card-body p-4">
                        <h3 style="color: white; font-weight: 700; margin-bottom: 20px;">Ready to Get Started?</h3>
                        <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 25px;">
                            Book your installation service today and let our experts handle the setup professionally.
                        </p>
                        <a href="index.php#booking-form" class="btn btn-light btn-lg" style="font-weight: 600; padding: 12px 30px; border-radius: 25px;">
                            <i class="fas fa-calendar-check me-2"></i>Book Installation Service
                        </a>
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

    <style>
        .service-detail-card {
            transition: all 0.3s ease;
        }
        
        .service-detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.15) !important;
        }
        
        .service-detail-card .btn {
            transition: all 0.3s ease;
        }
        
        .service-detail-card:hover .btn {
            background: #EC4899;
            border-color: #EC4899;
            color: white;
        }

        /* Cursor pointer for all list items */
        .list-unstyled li {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .list-unstyled li:hover {
            padding-left: 5px;
            color: #EC4899 !important;
        }

        .list-unstyled li:hover i {
            transform: scale(1.2);
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>
</html>