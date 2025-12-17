<?php
// Include database configuration
include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronic & Appliance Repair - Electrozot</title>
    <?php include("vendor/inc/head.php");?>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("vendor/inc/nav.php");?>

    <main id="main-content" role="main">
    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 140px 0 50px 0; margin-top: -56px;">
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                    <i class="fas fa-wrench" style="color: #EC4899;"></i> Electronic & Appliance Repair
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #6B7280; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Expert diagnosis and repair for all major home appliances and electronic devices
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
                <li class="breadcrumb-item active" style="color: #6c757d; font-weight: 500;">Electronic & Appliance Repair</li>
            </ol>
        </nav>

        <!-- Service Overview -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px;">
                    <div class="card-body p-4">
                        <h3 style="color: #2d3748; font-weight: 700; margin-bottom: 20px;">Expert Repair Services</h3>
                        <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                            Our skilled technicians provide comprehensive repair services for all types of electronic devices and home appliances. We diagnose issues accurately and provide cost-effective solutions with a 1-month warranty.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repair Services Grid -->
        <div class="row">
            <!-- Basic Electrical Work -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-bolt" style="color: #EC4899; margin-right: 8px;"></i>Basic Electrical Work
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Wiring repairs</li>
                            <li><i class="fas fa-check text-success me-2"></i>Circuit troubleshooting</li>
                            <li><i class="fas fa-check text-success me-2"></i>Power outlet repairs</li>
                            <li><i class="fas fa-check text-success me-2"></i>Electrical safety checks</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- AC, TV, Cooler, Fan, Heater Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tv" style="color: #EC4899; margin-right: 8px;"></i>AC, TV, Cooler & Fan Repair
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>AC gas refilling & servicing</li>
                            <li><i class="fas fa-check text-success me-2"></i>TV display & sound issues</li>
                            <li><i class="fas fa-check text-success me-2"></i>Cooler pump & motor repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Fan speed & noise issues</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Refrigerator Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-snowflake" style="color: #EC4899; margin-right: 8px;"></i>Refrigerator Repair
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Cooling issues</li>
                            <li><i class="fas fa-check text-success me-2"></i>Compressor problems</li>
                            <li><i class="fas fa-check text-success me-2"></i>Door seal replacement</li>
                            <li><i class="fas fa-check text-success me-2"></i>Thermostat repair</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Washing Machine Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tshirt" style="color: #EC4899; margin-right: 8px;"></i>Washing Machine Repair
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Motor & belt issues</li>
                            <li><i class="fas fa-check text-success me-2"></i>Water inlet problems</li>
                            <li><i class="fas fa-check text-success me-2"></i>Spin cycle repairs</li>
                            <li><i class="fas fa-check text-success me-2"></i>Control panel fixes</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Induction Cooktop Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-fire" style="color: #EC4899; margin-right: 8px;"></i>Induction Cooktop Repair
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Heating element repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Touch panel issues</li>
                            <li><i class="fas fa-check text-success me-2"></i>Power supply problems</li>
                            <li><i class="fas fa-check text-success me-2"></i>Circuit board repair</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Music System Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-music" style="color: #EC4899; margin-right: 8px;"></i>Music System Repair
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Speaker repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Amplifier issues</li>
                            <li><i class="fas fa-check text-success me-2"></i>CD/DVD player repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Bluetooth connectivity</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Motherboard & Electronics Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-microchip" style="color: #EC4899; margin-right: 8px;"></i>Motherboard & Electronics
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>PCB repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Component replacement</li>
                            <li><i class="fas fa-check text-success me-2"></i>Soldering services</li>
                            <li><i class="fas fa-check text-success me-2"></i>Circuit analysis</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Hand Tools Repair -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tools" style="color: #EC4899; margin-right: 8px;"></i>Hand Tools Repair
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Drill machine repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Angle grinder repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>Motor replacement</li>
                            <li><i class="fas fa-check text-success me-2"></i>Switch & cord repair</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>

            <!-- Switch Socket Replacement -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-plug" style="color: #EC4899; margin-right: 8px;"></i>Switch & Socket Replacement
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-3">
                            <li><i class="fas fa-check text-success me-2"></i>Power socket replacement</li>
                            <li><i class="fas fa-check text-success me-2"></i>Light switch repair</li>
                            <li><i class="fas fa-check text-success me-2"></i>USB socket installation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Modular switch upgrade</li>
                        </ul>
                        <a href="index.php#booking-form" class="btn btn-outline-primary btn-sm">Book Repair</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warranty Information -->
        <div class="row mt-5 mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%); border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                <i class="fas fa-shield-alt" style="font-size: 3rem; color: #EC4899;"></i>
                            </div>
                            <div class="col-md-10">
                                <h4 style="color: #2d3748; font-weight: 700; margin-bottom: 15px;">1-Month Warranty on All Repairs</h4>
                                <p style="color: #6c757d; margin-bottom: 10px;">We stand behind our work with a comprehensive warranty on all repair services.</p>
                                <ul class="list-unstyled mb-0" style="font-size: 0.9rem; color: #6c757d;">
                                    <li><i class="fas fa-check text-success me-2"></i>Free re-service if the same issue occurs within 30 days</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Quality parts and professional workmanship guaranteed</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Transparent pricing with no hidden charges</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); border-radius: 20px;">
                    <div class="card-body p-4">
                        <h3 style="color: white; font-weight: 700; margin-bottom: 20px;">Need Expert Repair Service?</h3>
                        <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 25px;">
                            Get your appliances fixed by certified technicians with genuine parts and warranty coverage.
                        </p>
                        <a href="index.php#booking-form" class="btn btn-light btn-lg" style="font-weight: 600; padding: 12px 30px; border-radius: 25px;">
                            <i class="fas fa-wrench me-2"></i>Book Repair Service
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