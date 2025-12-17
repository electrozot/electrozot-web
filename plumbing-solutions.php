<?php
// Include database configuration
include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plumbing Solutions & Servicing - Electrozot</title>
    <?php include("vendor/inc/head.php");?>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #f0fdfa 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("vendor/inc/nav.php");?>

    <main id="main-content" role="main">
    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 140px 0 50px 0; margin-top: -56px;">
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                    <i class="fas fa-tint" style="color: #10B981;"></i> Plumbing Solutions & Servicing
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #6B7280; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Comprehensive water system solutions, leak repairs, and maintenance services
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px;">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #10B981; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="services.php" style="color: #10B981; text-decoration: none; font-weight: 600;">Services</a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-weight: 500;">Plumbing Solutions</li>
            </ol>
        </nav>

        <!-- Service Overview -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px;">
                    <div class="card-body p-4">
                        <h3 style="color: #2d3748; font-weight: 700; margin-bottom: 20px;">Professional Plumbing Services</h3>
                        <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                            From emergency leak repairs to routine maintenance, our experienced plumbers provide reliable solutions for all your water system needs. We ensure clean, safe, and efficient water supply systems.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plumbing Services Grid -->
        <div class="row">
            <!-- General Plumbing Services -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 20px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748; font-size: 1.1rem;">
                            <i class="fas fa-wrench" style="color: #10B981; margin-right: 10px;"></i>General Plumbing Services
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Leak Repairs</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Pipe leak detection</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Faucet drip repairs</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Toilet leak fixes</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Underground leak repair</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Fixture Repairs</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Sink & basin repair</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Shower head replacement</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Toilet seat & flush repair</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Tap installation & repair</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Pipe Work</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>New pipe installation</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pipe replacement</li>
                                    <li><i class="fas fa-check text-success me-2"></i>PVC & copper piping</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Water line connections</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Emergency Services</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>24/7 emergency calls</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Burst pipe repairs</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Water damage prevention</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Quick response time</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="index.php#booking-form" class="btn btn-outline-success btn-lg">Book Plumbing Service</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Water Filter Servicing -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 20px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748; font-size: 1.1rem;">
                            <i class="fas fa-filter" style="color: #10B981; margin-right: 10px;"></i>Water Filter Servicing
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">RO System Service</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Membrane replacement</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Filter cartridge change</li>
                                    <li><i class="fas fa-check text-success me-2"></i>TDS level checking</li>
                                    <li><i class="fas fa-check text-success me-2"></i>System sanitization</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">UV & UF Systems</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>UV lamp replacement</li>
                                    <li><i class="fas fa-check text-success me-2"></i>UF membrane service</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pre-filter cleaning</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Flow rate optimization</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Installation</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>New filter installation</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Wall mounting</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Plumbing connections</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Electrical connections</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Maintenance</h6>
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Regular servicing</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Water quality testing</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Performance optimization</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Troubleshooting</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="index.php#booking-form" class="btn btn-outline-success btn-lg">Book Filter Service</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Water Tank Cleaning -->
            <div class="col-lg-12 mb-4">
                <div class="card service-detail-card border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 20px; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; color: #2d3748; font-size: 1.1rem;">
                            <i class="fas fa-water" style="color: #10B981; margin-right: 10px;"></i>Water Tank Cleaning Service
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Overhead Tank Cleaning</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Complete tank draining</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Scrubbing & disinfection</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Sediment removal</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Leak inspection</li>
                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Underground Tank</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Sump tank cleaning</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Algae & bacteria removal</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Wall & floor cleaning</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Waterproofing check</li>
                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Sanitization Process</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Chemical disinfection</li>
                                    <li><i class="fas fa-check text-success me-2"></i>UV sterilization</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Safe cleaning agents</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Quality certification</li>
                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <h6 style="color: #10B981; font-weight: 600; margin-bottom: 15px;">Additional Services</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Tank cover repair</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Inlet/outlet cleaning</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Water quality testing</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Maintenance tips</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <div class="row">
                                <div class="col-md-8 mx-auto">
                                    <div class="alert alert-info" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #065f46;">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Recommended:</strong> Clean your water tanks every 6 months for optimal water quality and health safety.
                                    </div>
                                </div>
                            </div>
                            <a href="index.php#booking-form" class="btn btn-success btn-lg" style="padding: 12px 30px;">Book Tank Cleaning</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Our Plumbing Services -->
        <div class="row mt-5 mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border-radius: 15px;">
                    <div class="card-body p-4">
                        <h4 class="text-center" style="color: #2d3748; font-weight: 700; margin-bottom: 30px;">Why Choose Our Plumbing Services?</h4>
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3 text-center">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-clock" style="font-size: 2.5rem; color: #10B981;"></i>
                                </div>
                                <h6 style="color: #2d3748; font-weight: 600;">24/7 Emergency Service</h6>
                                <p style="color: #6c757d; font-size: 0.9rem;">Quick response for urgent plumbing issues</p>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3 text-center">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-certificate" style="font-size: 2.5rem; color: #10B981;"></i>
                                </div>
                                <h6 style="color: #2d3748; font-weight: 600;">Licensed Plumbers</h6>
                                <p style="color: #6c757d; font-size: 0.9rem;">Certified and experienced professionals</p>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3 text-center">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-tools" style="font-size: 2.5rem; color: #10B981;"></i>
                                </div>
                                <h6 style="color: #2d3748; font-weight: 600;">Quality Tools & Parts</h6>
                                <p style="color: #6c757d; font-size: 0.9rem;">Professional equipment and genuine parts</p>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3 text-center">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #10B981;"></i>
                                </div>
                                <h6 style="color: #2d3748; font-weight: 600;">Service Warranty</h6>
                                <p style="color: #6c757d; font-size: 0.9rem;">1-month warranty on all plumbing work</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); border-radius: 20px;">
                    <div class="card-body p-4">
                        <h3 style="color: white; font-weight: 700; margin-bottom: 20px;">Need Plumbing Solutions?</h3>
                        <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 25px;">
                            From emergency repairs to routine maintenance, we provide reliable plumbing services for your home and office.
                        </p>
                        <a href="index.php#booking-form" class="btn btn-light btn-lg" style="font-weight: 600; padding: 12px 30px; border-radius: 25px;">
                            <i class="fas fa-tint me-2"></i>Book Plumbing Service
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
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15) !important;
        }
        
        .service-detail-card .btn {
            transition: all 0.3s ease;
        }
        
        .service-detail-card:hover .btn-outline-success {
            background: #10B981;
            border-color: #10B981;
            color: white;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }
        
        .feature-icon:hover {
            background: rgba(16, 185, 129, 0.2);
            transform: scale(1.1);
        }

        /* Cursor pointer for all list items */
        .list-unstyled li {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .list-unstyled li:hover {
            padding-left: 5px;
            color: #10B981 !important;
        }

        .list-unstyled li:hover i {
            transform: scale(1.2);
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>
</html>