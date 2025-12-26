<?php
// Include database configuration
include('../admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Services - Electrozot</title>
    
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
    <meta name="description" content="Professional installation services in Kangra District - TV mounting, CCTV setup, smart home devices, appliance installation. Expert technicians with proper mounting & wiring. Book now!">
    <meta name="keywords" content="installation services Kangra, TV mounting Dharamshala, CCTV installation, smart home setup, appliance installation, professional mounting, DTH installation, chimney installation, technician near me">
    <meta name="author" content="Electrozot">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://electrozot.in/service/installation-services.php">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Professional Installation Services in Kangra District - ElectroZot">
    <meta property="og:description" content="Expert installation services for TV mounting, CCTV, smart home devices & appliances. Professional setup with proper mounting, wiring & testing in Kangra, Dharamshala & nearby areas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrozot.in/service/installation-services.php">
    <meta property="og:image" content="https://electrozot.in/vendor/img/service3.png">
    <meta property="og:site_name" content="ElectroZot">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Professional Installation Services in Kangra District - ElectroZot">
    <meta name="twitter:description" content="Expert installation services for TV mounting, CCTV, smart home devices & appliances with professional setup and testing.">
    <meta name="twitter:image" content="https://electrozot.in/vendor/img/service3.png">
    
    <!-- Structured Data Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "@id": "https://electrozot.in/service/installation-services.php#service",
        "name": "Installation Services",
        "description": "Professional installation services for TV mounting, CCTV setup, smart home devices, appliances and more with expert setup and testing.",
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
        "serviceType": "Installation Services",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Installation Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "TV & DTH Installation",
                        "description": "Professional TV wall mounting and DTH dish installation with channel tuning and setup"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "CCTV Installation",
                        "description": "Complete CCTV security system installation with camera setup and monitoring configuration"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Smart Home Setup",
                        "description": "Smart home devices installation and configuration for automated home control"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Appliance Installation",
                        "description": "Professional installation of home appliances with proper mounting and connections"
                    }
                }
            ]
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

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #f0fdfa 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("../vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 25%, #34D399 50%, #10B981 75%, #059669 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #ffffff; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                    <i class="fas fa-cog" style="color: #ffffff;"></i> Installation Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #f0fdfa; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Professional installation services for all your home and office appliances
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../index.php" style="color: #10B981; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../services.php" style="color: #10B981; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        Services
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Installation Services</li>
            </ol>
        </nav>

        <!-- Service Categories -->
        <div class="row">
            <!-- Appliance Setup -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-plug" style="color: #10B981; margin-right: 8px;"></i>Appliance Installation
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <p style="font-size: 0.9rem; color: #6c757d; margin-bottom: 15px; line-height: 1.6;">
                            Professional appliance installation services with proper mounting, wiring, and setup. Our certified technicians ensure safe and secure installation of all home appliances.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="tv-dish-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>TV & DTH Setup:</strong> Wall mounting, DTH dish installation, cable management, and channel tuning.
                            </li>
                            <li class="service-item" data-service="chimney-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Kitchen Chimney:</strong> Electric chimney installation with ducting, wiring, and ventilation setup.
                            </li>
                            <li class="service-item" data-service="fan-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Fan Installation:</strong> Ceiling fan, wall fan, exhaust fan mounting with electrical connections.
                            </li>
                            <li class="service-item" data-service="washing-machine-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Washing Machine:</strong> Installation, water connections, drain setup, and demo operation.
                            </li>
                            <li class="service-item" data-service="geyser-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Geyser Setup:</strong> Water heater installation with plumbing, electrical, and safety connections.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tech & Security -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-shield-alt" style="color: #10B981; margin-right: 8px;"></i>Tech & Security Setup
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <p style="font-size: 0.9rem; color: #6c757d; margin-bottom: 15px; line-height: 1.6;">
                            Advanced technology and security system installation services. We provide complete setup of CCTV, smart home devices, and networking solutions for modern homes and offices.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="cctv-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>CCTV Systems:</strong> Security camera installation, DVR setup, mobile app configuration, and monitoring.
                            </li>
                            <li class="service-item" data-service="wifi-setup" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Wi-Fi & Networking:</strong> Router setup, network configuration, range extender installation.
                            </li>
                            <li class="service-item" data-service="smart-home-installation" style="font-size: 0.85rem; color: #495057; padding: 8px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #10B981; font-size: 0.8rem; margin-right: 10px;"></i>
                                <strong>Smart Home:</strong> Smart switches, lights, automation hub, and voice control integration.
                            </li>
                        </ul>
                        
                        <!-- Additional Info -->
                        <div class="mt-4 p-3" style="background: rgba(16, 185, 129, 0.1); border-radius: 10px; border-left: 4px solid #10B981;">
                            <h6 style="color: #10B981; font-weight: 600; margin-bottom: 8px;">
                                <i class="fas fa-info-circle"></i> Complete Installation Package
                            </h6>
                            <p style="font-size: 0.8rem; color: #6c757d; margin: 0; line-height: 1.5;">
                                All installations include proper mounting, wiring, testing, warranty, and complete demonstration of operation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Electrozot for Installation -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h3 class="text-center mb-4" style="color: #10B981; font-weight: 700;">
                            <i class="fas fa-trophy"></i> Best Installation Service in Kangra District
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">🔧 Professional Installation Team</h5>
                                <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Certified Technicians:</strong> Trained professionals for all appliance brands</li>
                                    <li><strong>Proper Tools:</strong> Advanced equipment for safe and secure installation</li>
                                    <li><strong>Quality Materials:</strong> Genuine brackets, wires, and mounting accessories</li>
                                    <li><strong>Safety Standards:</strong> All installations meet manufacturer specifications</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">⭐ Top Installation Service</h5>
                                <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Complete Setup:</strong> Installation, testing, and operation demonstration</li>
                                    <li><strong>Same Day Service:</strong> Most installations completed within 2-4 hours</li>
                                    <li><strong>Warranty Coverage:</strong> Installation warranty on all mounting and wiring work</li>
                                    <li><strong>Customer Training:</strong> Complete guidance on appliance operation and maintenance</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p style="color: #6c757d; font-size: 1rem; margin: 0;">
                                <strong>Serving Kangra District:</strong> Professional installation services in Kangra, Dharamshala, Palampur, Baijnath, Nurpur, and all nearby areas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-tools" style="font-size: 2.5rem; color: #10B981; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Professional Setup</h5>
                        <p style="color: #6c757d; font-size: 0.9rem; margin: 0;">Expert installation with proper mounting, wiring, and testing</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-clock" style="font-size: 2.5rem; color: #10B981; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Quick Service</h5>
                        <p style="color: #6c757d; font-size: 0.9rem; margin: 0;">Fast and efficient installation with minimal disruption</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-graduation-cap" style="font-size: 2.5rem; color: #10B981; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Usage Training</h5>
                        <p style="color: #6c757d; font-size: 0.9rem; margin: 0;">Complete demonstration and usage guidance included</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="../index.php#booking-form" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); border: none; border-radius: 25px; padding: 15px 40px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                <i class="fas fa-calendar-check"></i> Book Installation Service Now
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
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.2) !important;
        }

        .service-list li:hover {
            color: #10B981 !important;
            padding-left: 15px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 8px;
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("../vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>