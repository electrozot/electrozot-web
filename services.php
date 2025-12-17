<?php
// Include database configuration
include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <?php include("vendor/inc/head.php");?>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("vendor/inc/nav.php");?>

    <main id="main-content" role="main">
    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #E0F2FE 0%, #FECDD3 25%, #D1FAE5 50%, #FBCFE8 75%, #FED7D7 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                    <i class="fas fa-tools" style="color: #EC4899;"></i> Our Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #6B7280; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Expert installation, maintenance, and repair services for your home and office appliances
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="index.php" style="color: #EC4899; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Services</li>
            </ol>
        </nav>

        <!-- Service Images -->
        <style>
            @media (max-width: 768px) {
                .service-images-mobile {
                    display: flex !important;
                    flex-direction: row !important;
                    flex-wrap: nowrap !important;
                    overflow-x: auto !important;
                    overflow-y: hidden !important;
                    gap: 15px !important;
                    padding: 10px 5px !important;
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    -webkit-overflow-scrolling: touch !important;
                    scroll-snap-type: x mandatory !important;
                }
                .service-images-mobile .service-image-col {
                    flex: 0 0 auto !important;
                    width: 75% !important;
                    min-width: 250px !important;
                    max-width: 300px !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                    scroll-snap-align: center !important;
                }
                .service-images-mobile .service-image-wrapper {
                    width: 100% !important;
                    display: block !important;
                }
                .service-images-mobile .service-image {
                    width: 100% !important;
                    height: auto !important;
                    max-height: 220px !important;
                    object-fit: cover !important;
                    display: block !important;
                }
            }
        </style>
        <div class="row text-center mb-4 service-images-mobile" style="margin-bottom: 30px !important;">
            <div class="col-lg-4 col-md-4 mb-3 service-image-col">
                <div class="service-image-wrapper">
                    <img class="img-fluid rounded service-image" src="vendor/img/service1.png" alt="Electronic Service" style="max-height: 150px; width: auto; border-radius: 12px;">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 mb-3 service-image-col">
                <div class="service-image-wrapper">
                    <img class="img-fluid rounded service-image" src="vendor/img/service3.png" alt="Electrical Service" style="max-height: 150px; width: auto; border-radius: 12px;">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 mb-3 service-image-col">
                <div class="service-image-wrapper">
                    <img class="img-fluid rounded service-image" src="vendor/img/service2.png" alt="Plumbing Service" style="max-height: 150px; width: auto; border-radius: 12px;">
                </div>
            </div>
        </div>

        <!-- Service Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <a href="installation-services.php" class="service-card-link" style="text-decoration: none; color: inherit;">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 12px 15px; border: none;">
                        <h5 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-cog" style="color: #EC4899; margin-right: 8px;"></i>Installation Services
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; line-height: 1.5;">
                            Professional installation services for TV, AC, WiFi, washing machines, geysers, cameras, and all home appliances. Expert setup with warranty.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>TV Installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Dish installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Wifi installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Geyser installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Fan & Lights installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Electric Chimney installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Camera installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>AC servicing
                            </li>
                        </ul>
                    </div>
                </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <a href="electronic-appliance-repair.php" class="service-card-link" style="text-decoration: none; color: inherit;">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 12px 15px; border: none;">
                        <h5 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-wrench" style="color: #EC4899; margin-right: 8px;"></i>Electronic & Appliance Repair
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; line-height: 1.5;">
                            Expert repair services for AC, TV, refrigerator, washing machine, and all electronics. Certified technicians with 1-month warranty on all repairs.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Basic electrical work
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>AC, TV, Cooler, Fan, Heater Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Refrigerator Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Induction Cooktop Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Music system repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Expert motherboard & electronics repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Hand tools ( Drill/Cutter ) repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Replacement of switch sockets
                            </li>
                        </ul>
                    </div>
                </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <a href="plumbing-solutions.php" class="service-card-link" style="text-decoration: none; color: inherit;">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 12px 15px; border: none;">
                        <h5 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tint" style="color: #10B981; margin-right: 8px;"></i>Plumbing Solutions & Servicing
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; line-height: 1.5;">
                            Professional plumbing services including leak repairs, water filter servicing, tank cleaning, and fixture repairs. 24/7 emergency service available.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Plumbing services (leak fixes, fixture repair)
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Filter servicing
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; cursor: pointer;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water tank cleaning service
                            </li>
                        </ul>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <!-- SEO Content Section -->
        <section class="seo-content-section mt-5 mb-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #fef3f7 100%); border-radius: 15px; padding: 30px;">
                        <h2 style="color: #2d3748; font-weight: 700; font-size: 1.8rem; margin-bottom: 20px; text-align: center;">
                            <i class="fas fa-tools" style="color: #EC4899;"></i> Professional Services for Your Home & Office
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h3 style="color: #EC4899; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-cog"></i> Installation Services
                                </h3>
                                <p style="color: #6c757d; line-height: 1.8; font-size: 0.95rem;">
                                    Our expert technicians provide professional installation services for all types of home and office appliances. From TV wall mounting to AC installation, washing machine setup to CCTV camera installation, we ensure proper setup with safety compliance and optimal performance. We handle electrical connections, plumbing requirements, and complete configuration for all your appliances.
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h3 style="color: #EC4899; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-wrench"></i> Repair & Maintenance
                                </h3>
                                <p style="color: #6c757d; line-height: 1.8; font-size: 0.95rem;">
                                    We specialize in repairing all major home appliances including air conditioners, refrigerators, washing machines, televisions, and more. Our certified technicians diagnose issues accurately and provide cost-effective solutions with genuine parts. Every repair comes with a 1-month warranty, ensuring quality workmanship and customer satisfaction.
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h3 style="color: #10B981; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-tint"></i> Plumbing Solutions
                                </h3>
                                <p style="color: #6c757d; line-height: 1.8; font-size: 0.95rem;">
                                    From emergency leak repairs to routine maintenance, our licensed plumbers provide comprehensive plumbing services. We handle water filter servicing, tank cleaning, pipe repairs, fixture installations, and all plumbing needs. Available 24/7 for emergency services, we ensure clean, safe, and efficient water supply systems for your property.
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h3 style="color: #A855F7; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-shield-alt"></i> Why Choose Electrozot?
                                </h3>
                                <p style="color: #6c757d; line-height: 1.8; font-size: 0.95rem;">
                                    With certified technicians, transparent pricing, and quality service guarantee, Electrozot is your trusted partner for all electrical, electronic, and plumbing needs. We provide same-day service, use genuine parts, and offer competitive pricing. Our commitment to customer satisfaction and professional workmanship sets us apart in the industry.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Service Areas & Keywords for SEO -->
        <section class="service-keywords-section mb-4" style="display: none;" aria-hidden="true">
            <h2>Service Areas & Specializations</h2>
            <p>TV installation, wall mounting, AC installation, split AC, window AC, washing machine installation, water geyser installation, electric chimney installation, CCTV camera installation, WiFi router installation, dish antenna installation, fan installation, light fixture installation, AC repair, TV repair, refrigerator repair, fridge repair, washing machine repair, induction cooktop repair, microwave repair, motherboard repair, electronics repair, plumbing services, leak repair, pipe repair, water filter service, RO service, UV filter service, water tank cleaning, overhead tank cleaning, underground tank cleaning, fixture repair, faucet repair, toilet repair, emergency plumbing, 24/7 service, certified technicians, professional electrician, licensed plumber, home appliance repair, office appliance service, residential services, commercial services, same day service, warranty service, genuine parts, affordable pricing, transparent pricing, quality service, customer satisfaction, expert technicians, skilled professionals, reliable service, fast service, emergency service, doorstep service, home service, on-site repair, installation service, maintenance service, annual maintenance contract, AMC service</p>
        </section>

        <!-- Call to Action Banner -->
        <div class="row mt-5 mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 50%, #FBCFE8 100%); border-radius: 20px; overflow: hidden; cursor: pointer;" onclick="window.location.href='index.php#booking-form'">
                    <div class="card-body text-center" style="padding: 40px 20px;">
                        <h2 style="color: white; font-weight: 800; font-size: 2rem; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                            <i class="fas fa-calendar-check"></i> Book Your Service Today!
                        </h2>
                        <p style="color: rgba(255,255,255,0.95); font-size: 1.1rem; margin-bottom: 25px; max-width: 700px; margin-left: auto; margin-right: auto;">
                            Get professional service from certified technicians. Same-day service available. Call now or book online!
                        </p>
                        <div class="d-flex justify-content-center flex-wrap gap-3" style="gap: 15px;">
                            <a href="tel:7559606925" class="btn btn-light btn-lg" style="font-weight: 600; padding: 12px 30px; border-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                <i class="fas fa-phone-alt"></i> Call: 7559606925
                            </a>
                            <a href="index.php#booking-form" class="btn btn-outline-light btn-lg" style="font-weight: 600; padding: 12px 30px; border-radius: 25px; border: 2px solid white;">
                                <i class="fas fa-calendar-alt"></i> Book Online
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Cards -->
        <div class="row mt-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FDF5FF 100%); border-radius: 12px; overflow: hidden; cursor: pointer;">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-bolt" style="font-size: 2rem; color: #A855F7;"></i>
                        </div>
                        <h5 class="card-title" style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 10px;">
                            Faster And Safe Service
                        </h5>
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; line-height: 1.6; margin: 0;">
                            We provide professional technician services with accountability, reliability and ease of booking skilled technicians for all your needs.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm warranty-card" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 12px; overflow: hidden; cursor: pointer;" role="button" data-toggle="modal" data-target="#warrantyModal" aria-label="View warranty terms">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-shield-alt" style="font-size: 2rem; color: #EC4899;"></i>
                        </div>
                        <h5 class="card-title" style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 10px;">
                            1‑Month Warranty on Repair Services
                        </h5>
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; line-height: 1.6; margin: 0;">
                            Click to view warranty terms & conditions
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 12px; overflow: hidden;">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-star" style="font-size: 2rem; color: #10B981;"></i>
                        </div>
                        <h5 class="card-title" style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 10px;">
                            Our Commitment
                        </h5>
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; line-height: 1.6; margin: 0;">
                            Your satisfaction is our priority. We are committed to quality workmanship, transparent pricing, and timely service for every project.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
    <!-- End Main Content -->
    
    <!-- Warranty Terms Modal -->
    <div class="modal fade" id="warrantyModal" tabindex="-1" role="dialog" aria-labelledby="warrantyModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="warrantyModalLabel">Warranty Terms & Conditions</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul class="list-unstyled mb-0" style="font-size: 0.95rem; color: #4a5568;">
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Electrozot provides a 1‑month warranty on repair services from the date of completion.</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Warranty is void if the product seal is opened, broken, or tampered.</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Warranty does not cover any internal or external physical damage to the product.</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Warranty is void in case of any liquid damage or exposure to moisture.</li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <?php include("vendor/inc/footer.php");?>

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

    <style>
        /* Service Page Styles - Enhanced */
        .service-image-wrapper {
            transition: transform 0.3s ease;
            display: inline-block;
            cursor: pointer;
        }

        .service-image {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            display: inline-block;
            cursor: pointer;
        }

        .service-image:hover {
            transform: scale(1.08);
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);
        }

        .service-card-link {
            cursor: pointer !important;
        }

        .service-card-compact {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer !important;
            position: relative;
            overflow: hidden;
        }

        .service-card-compact::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(236, 72, 153, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .service-card-compact:hover::before {
            left: 100%;
        }

        .service-card-compact:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(236, 72, 153, 0.25) !important;
        }

        .service-card-header {
            transition: all 0.3s ease;
        }

        .service-card-compact:hover .service-card-header {
            background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%) !important;
            color: white !important;
        }

        .service-card-compact:hover .service-card-header h5 {
            color: white !important;
        }

        .service-card-compact:hover .service-card-header i {
            color: white !important;
        }

        .service-list li {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .service-list li:hover {
            color: #EC4899 !important;
            padding-left: 8px;
            background: rgba(236, 72, 153, 0.05);
            border-radius: 4px;
        }

        .service-list li:hover .fa-check-circle {
            transform: scale(1.2) rotate(360deg);
            transition: transform 0.3s ease;
        }

        .feature-card-compact {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }

        .feature-card-compact:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 40px rgba(236, 72, 153, 0.25) !important;
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .feature-card-compact:hover .feature-icon-wrapper {
            transform: scale(1.15) rotate(5deg);
            background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%);
        }

        .feature-card-compact:hover .feature-icon-wrapper i {
            color: white !important;
        }

        .services-breadcrumb {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }
        
        .services-breadcrumb .breadcrumb-item {
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        
        .services-breadcrumb a:hover {
            color: #F472B6 !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }
        
        @media (max-width: 576px) {
            .services-breadcrumb {
                padding: 10px 15px !important;
                font-size: 0.85rem !important;
            }
            
            .services-breadcrumb .breadcrumb-item a,
            .services-breadcrumb .breadcrumb-item {
                font-size: 0.85rem !important;
            }
        }

        @media (max-width: 768px) {
            .services-hero {
                padding: 100px 0 40px 0 !important;
            }
        }

        @media (max-width: 768px) {
            .service-image {
                max-height: 120px !important;
            }
        }

        /* Enhanced Button Styles */
        .btn {
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Service Card Link Enhancement */
        .service-card-link:hover {
            text-decoration: none !important;
        }

        /* Breadcrumb Enhancement */
        .breadcrumb-item a {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Modal Enhancement */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .modal-header .close {
            color: white;
            opacity: 1;
            cursor: pointer;
        }

        /* Call to Action Enhancement */
        .gap-3 > * {
            margin: 5px;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Loading Animation for Images */
        .service-image {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Service List Icon Animation */
        .service-list li .fa-check-circle {
            transition: all 0.3s ease;
        }

        /* Warranty Card Pulse Effect */
        .warranty-card {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 2px 10px rgba(236, 72, 153, 0.2);
            }
            50% {
                box-shadow: 0 4px 20px rgba(236, 72, 153, 0.4);
            }
        }

        .warranty-card:hover {
            animation: none;
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>