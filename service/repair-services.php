<?php
// Include database configuration
include('../admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Services - Electrozot</title>
    
    <!-- Bootstrap core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="../css/modern-business.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../usr/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="../vendor/css/custom.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="../assets/favicon.ico" type="image/x-icon">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Expert appliance repair services in Kangra District - AC, refrigerator, washing machine, TV, geyser repair with 1-month warranty. Same-day service available. Best repair technicians near you!">
    <meta name="keywords" content="appliance repair Kangra, AC repair Dharamshala, refrigerator repair, washing machine repair, TV repair, geyser repair, home appliance service, repair technician near me, appliance repair warranty">
    <meta name="author" content="Electrozot">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://electrozot.in/service/repair-services.php">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Expert Appliance Repair Services in Kangra District - ElectroZot">
    <meta property="og:description" content="Professional repair services for AC, refrigerator, washing machine, TV, geyser & more. 1-month warranty on all repairs. Same-day service in Kangra, Dharamshala & nearby areas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrozot.in/service/repair-services.php">
    <meta property="og:image" content="https://electrozot.in/vendor/img/service2.png">
    <meta property="og:site_name" content="ElectroZot">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Expert Appliance Repair Services in Kangra District - ElectroZot">
    <meta name="twitter:description" content="Professional repair services for all home appliances with 1-month warranty. Same-day service available in Kangra District.">
    <meta name="twitter:image" content="https://electrozot.in/vendor/img/service2.png">
    
    <!-- Structured Data Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "@id": "https://electrozot.in/service/repair-services.php#service",
        "name": "Appliance Repair Services",
        "description": "Expert appliance repair services for AC, refrigerator, washing machine, TV, geyser and more with 1-month warranty on all repairs.",
        "provider": {
            "@type": "HomeAndConstructionBusiness",
            "name": "ElectroZot",
            "url": "https://electrozot.in",
            "logo": "https://electrozot.in/vendor/EZlogonew.png",
            "telephone": "+917559606925",
            "priceRange": "₹₹",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Kangra",
                "addressRegion": "Himachal Pradesh",
                "addressCountry": "IN"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "32.0998",
                "longitude": "76.2693"
            },
            "openingHours": "Mo-Su 08:00-20:00",
            "areaServed": [
                {
                    "@type": "City",
                    "name": "Kangra"
                },
                {
                    "@type": "City", 
                    "name": "Dharamshala"
                },
                {
                    "@type": "City",
                    "name": "Palampur"
                }
            ]
        },
        "areaServed": {
            "@type": "State",
            "name": "Himachal Pradesh"
        },
        "serviceType": "Appliance Repair",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Appliance Repair Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "AC Repair Service",
                        "description": "Air conditioner repair with gas charging, compressor repair and component replacement"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Refrigerator Repair",
                        "description": "Complete refrigerator repair including cooling issues, gas charging and thermostat repair"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Washing Machine Repair",
                        "description": "Professional repair for all types of washing machines with motor and control panel repair"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "TV Repair Service",
                        "description": "Television repair for LED, LCD, Smart TVs with display and audio troubleshooting"
                    }
                }
            ]
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "reviewCount": "150"
    }
    </script>
    
    <!-- Call-to-Action Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "HomeAndConstructionBusiness",
        "@id": "https://electrozot.in/#cta",
        "name": "ElectroZot",
        "url": "https://electrozot.in",
        "logo": "https://electrozot.in/vendor/EZlogonew.png",
        "telephone": "+917559606925",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+917559606925",
            "contactType": "customer service",
            "areaServed": "IN",
            "availableLanguage": ["English", "Hindi"]
        },
        "potentialAction": [
            {
                "@type": "CallAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "tel:+917559606925",
                    "actionPlatform": [
                        "http://schema.org/DesktopWebPlatform",
                        "http://schema.org/MobileWebPlatform"
                    ]
                },
                "name": "Call ElectroZot for Service Booking"
            },
            {
                "@type": "ReserveAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "https://electrozot.in/contact",
                    "actionPlatform": [
                        "http://schema.org/DesktopWebPlatform",
                        "http://schema.org/MobileWebPlatform"
                    ]
                },
                "name": "Book a Service with ElectroZot"
            }
        ]
    }
    </script>
    
    <!-- CRITICAL CSS for navbar -->
    <style>
        .navbar.fixed-top {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 10000 !important;
            padding: 8px 0 4px 0 !important;
            border: none !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }
        
        .navbar-logo {
            height: 70px !important;
            width: auto !important;
            max-height: 70px !important;
            object-fit: contain !important;
            display: block !important;
        }
        
        .navbar-brand {
            display: flex !important;
            align-items: center !important;
            color: #fff !important;
            text-decoration: none !important;
            font-weight: 700 !important;
        }
        
        .navbar-brand span {
            font-size: 2rem !important;
            line-height: 1.1 !important;
            font-weight: 600 !important;
            color: #fff !important;
        }
        
        .navbar-tagline {
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: rgba(255, 255, 255, 0.95) !important;
        }
        
        .navbar .container-fluid {
            max-width: 1400px !important;
            padding: 0 10px !important;
            position: relative !important;
            z-index: 2 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        
        @media (max-width: 768px) {
            .navbar-logo {
                height: 55px !important;
                max-height: 55px !important;
            }
            .navbar-brand span {
                font-size: 1.3rem !important;
            }
            .navbar-tagline {
                font-size: 0.65rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-logo {
                height: 50px !important;
                max-height: 50px !important;
            }
            .navbar-brand span {
                font-size: 1.1rem !important;
            }
            .navbar-tagline {
                font-size: 0.6rem !important;
            }
        }
    </style>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("../vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 25%, #F9A8D4 50%, #FBCFE8 75%, #FED7D7 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px;">
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
                    <i class="fas fa-wrench" style="color: #EC4899;"></i> Repair Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #6B7280; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Expert repair services for all your home and office appliances with warranty
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../index.php" style="color: #EC4899; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../services.php" style="color: #EC4899; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        Services
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Repair Services</li>
            </ol>
        </nav>

        <!-- Service Categories -->
        <div class="row">
            <!-- Major Appliances -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tv" style="color: #EC4899; margin-right: 8px;"></i>Major Appliances Repair
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <p style="font-size: 0.9rem; color: #6c757d; margin-bottom: 15px; line-height: 1.6;">
                            Expert repair services for all major home appliances by certified technicians. We fix AC, refrigerator, washing machine, and more with genuine spare parts and 1-month warranty.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="ac-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>AC Repair:</strong> Split, window, and central AC repair with gas charging, compressor repair, and cooling issues.
                            </li>
                            <li class="service-item" data-service="refrigerator-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Refrigerator Service:</strong> Single/double door fridge repair, gas charging, thermostat, and compressor issues.
                            </li>
                            <li class="service-item" data-service="washing-machine-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Washing Machine:</strong> Front load, top load, semi-automatic repair with motor, belt, and drainage issues.
                            </li>
                            <li class="service-item" data-service="microwave-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Microwave Oven:</strong> Heating problems, turntable issues, control panel, and magnetron repair.
                            </li>
                            <li class="service-item" data-service="geyser-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Geyser Repair:</strong> Electric and gas geyser repair, heating element, thermostat, and tank leakage issues.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Other Gadgets -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tools" style="color: #EC4899; margin-right: 8px;"></i>Electronics & Small Appliances
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <p style="font-size: 0.9rem; color: #6c757d; margin-bottom: 15px; line-height: 1.6;">
                            Professional repair services for all electronic gadgets and small appliances. Our skilled technicians handle TV, music systems, kitchen appliances, and more with precision.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="tv-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>TV Repair:</strong> LED, LCD, Smart TV repair with display, audio, and connectivity issues.
                            </li>
                            <li class="service-item" data-service="fan-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Fan Service:</strong> Ceiling, table, exhaust fan repair with motor, capacitor, and speed issues.
                            </li>
                            <li class="service-item" data-service="mixer-grinder-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Mixer Grinder:</strong> Motor repair, jar replacement, blade sharpening, and coupling issues.
                            </li>
                            <li class="service-item" data-service="iron-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Electric Iron:</strong> Steam iron, dry iron repair with heating element and temperature control.
                            </li>
                            <li class="service-item" data-service="induction-repair" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #EC4899; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Induction Cooktop:</strong> Heating coil, control panel, and power supply issues repair.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Electrozot for Repair Services -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%); border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h3 class="text-center mb-4" style="color: #EC4899; font-weight: 700;">
                            <i class="fas fa-award"></i> Best Appliance Repair Service in Kangra District
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">🔧 Expert Technicians Near You</h5>
                                <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Skilled Professionals:</strong> 10+ years experienced technicians for all brands</li>
                                    <li><strong>Genuine Parts:</strong> Original spare parts with manufacturer warranty</li>
                                    <li><strong>Quick Diagnosis:</strong> Advanced tools for accurate problem identification</li>
                                    <li><strong>All Brands:</strong> LG, Samsung, Whirlpool, Godrej, Haier, and more</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">⭐ Top-Rated Repair Service</h5>
                                <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>1-Month Warranty:</strong> Service warranty on all repair work</li>
                                    <li><strong>Same Day Service:</strong> Most repairs completed within 24 hours</li>
                                    <li><strong>Transparent Pricing:</strong> No hidden charges, upfront cost estimation</li>
                                    <li><strong>Customer Satisfaction:</strong> 4.8/5 rating from 500+ happy customers</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p style="color: #6c757d; font-size: 1rem; margin: 0;">
                                <strong>Serving Entire Kangra District:</strong> Best appliance repair service in Kangra, Dharamshala, Palampur, Baijnath, Nurpur, and all nearby areas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warranty Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%); border-radius: 15px;">
                    <div class="card-body text-center" style="padding: 30px;">
                        <h4 style="color: #EC4899; font-weight: 700; margin-bottom: 20px;">
                            <i class="fas fa-shield-alt"></i> 1-Month Warranty on All Repairs
                        </h4>
                        <p style="color: #6c757d; font-size: 1rem; margin-bottom: 0;">
                            All our repair services come with a comprehensive 1-month warranty. Your satisfaction and peace of mind are our top priorities.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="../index.php#booking-form" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); border: none; border-radius: 25px; padding: 15px 40px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 8px 20px rgba(236, 72, 153, 0.3);">
                <i class="fas fa-calendar-check"></i> Book Repair Service Now
            </a>
        </div>
    </div>

    <?php include("../vendor/inc/footer.php");?>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .service-card-compact {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .service-card-compact:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.2) !important;
        }

        .service-list li:hover {
            color: #EC4899 !important;
            padding-left: 15px;
            background: rgba(236, 72, 153, 0.1);
            border-radius: 8px;
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("../vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>