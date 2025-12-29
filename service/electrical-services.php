<?php
// Include database configuration
include('../admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrical Services - Electrozot</title>
    
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
    <meta name="description" content="Best electrical services in Kangra District - Professional wiring, fixtures, safety systems & power installations. Licensed electricians for home & office. Same-day service available. Call now!">
    <meta name="keywords" content="electrical services Kangra, electrician Dharamshala, home wiring, electrical installation, power systems, electrical repair, circuit breaker, LED installation, electrical safety, best electrician near me">
    <meta name="author" content="Electrozot">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://electrozot.in/service/electrical-services.php">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Professional Electrical Services in Kangra District - ElectroZot">
    <meta property="og:description" content="Expert electrical services including wiring, fixtures, safety systems & power installations. Licensed electricians available for same-day service in Kangra, Dharamshala & nearby areas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrozot.in/service/electrical-services.php">
    <meta property="og:image" content="https://electrozot.in/vendor/img/service1.png">
    <meta property="og:site_name" content="ElectroZot">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Professional Electrical Services in Kangra District - ElectroZot">
    <meta name="twitter:description" content="Expert electrical services including wiring, fixtures, safety systems & power installations. Licensed electricians available for same-day service.">
    <meta name="twitter:image" content="https://electrozot.in/vendor/img/service1.png">
    
    <!-- Structured Data Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "@id": "https://electrozot.in/service/electrical-services.php#service",
        "name": "Electrical Services",
        "description": "Professional electrical services including wiring, fixtures, safety systems and power installations by certified electricians in Kangra District.",
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
        "serviceType": "Electrical Services",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Electrical Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Home Wiring Installation",
                        "description": "Complete electrical wiring for new constructions and rewiring with ISI certified materials"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Switch & Socket Installation",
                        "description": "Installation and replacement of modular switches, sockets, and USB charging points"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Light Fixture Installation",
                        "description": "LED panels, tube lights, chandeliers, and decorative lighting solutions"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Circuit Protection Systems",
                        "description": "MCB, RCCB installation and electrical panel upgrades for safety"
                    }
                }
            ]
        }
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

<body style="background: linear-gradient(180deg, #faf5ff 0%, #ffffff 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("../vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 25%, #ffffff 50%, #faf5ff 75%, #f3e8ff 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #000000; margin-bottom: 15px;">
                    <i class="fas fa-bolt" style="color: #dc143c;"></i> Electrical Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #000000; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Professional electrical installation, wiring, and repair services for your home and office
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: #faf5ff; border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../index.php" style="color: #dc143c; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../services.php" style="color: #dc143c; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        Services
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #000000; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Electrical Services</li>
            </ol>
        </nav>

        <!-- Service Categories -->
        <div class="row">
            <!-- Professional Electrical Services -->
            <div class="col-lg-8 col-md-8 mb-4 mx-auto">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: #f3e8ff; padding: 20px 25px; border: none;">
                        <h5 class="mb-0 text-center" style="font-size: 1.2rem; font-weight: 600; color: #000000;">
                            <i class="fas fa-bolt" style="color: #dc143c; margin-right: 10px;"></i>Professional Electrical Services
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 25px;">
                        <p style="font-size: 1rem; color: #000000; margin-bottom: 20px; line-height: 1.6; text-align: center;">
                            Expert electrical solutions by certified electricians in Kangra district. We provide safe, reliable electrical installation, wiring, and repair services for all your electrical needs with quality materials and workmanship.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="home-wiring" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #dc143c; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Home Wiring & Installation:</strong> Complete electrical wiring for new constructions and rewiring with ISI certified materials, modular switches, sockets, and USB charging points.
                            </li>
                            <li class="service-item" data-service="light-fixture" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #dc143c; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Light Fixtures & Decorative Lighting:</strong> LED panel installation, tube lights, chandeliers, decorative lighting solutions, and beautiful Diwali festival lighting decoration.
                            </li>
                            <li class="service-item" data-service="safety-power" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #dc143c; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Safety & Power Systems:</strong> MCB, RCCB installation, electrical panel upgrades, inverter/UPS installation, proper grounding systems, and electrical fault diagnosis.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Electrozot for Electrical Services -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h3 class="text-center mb-4" style="color: #dc143c; font-weight: 700;">
                            <i class="fas fa-star"></i> Why Electrozot is the Best Electrical Service in Kangra District
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color: #000000; font-weight: 600; margin-bottom: 15px;">🏆 Top-Rated Electricians Near You</h5>
                                <ul style="color: #000000; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Certified Professionals:</strong> Licensed electricians with 10+ years experience</li>
                                    <li><strong>Local Expertise:</strong> Best electrical service provider in Kangra, Dharamshala, and surrounding areas</li>
                                    <li><strong>Quick Response:</strong> Same-day service available for emergency electrical issues</li>
                                    <li><strong>Quality Materials:</strong> Only ISI certified wires, switches, and electrical components</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color: #000000; font-weight: 600; margin-bottom: 15px;">⚡ Best Electrical Solutions</h5>
                                <ul style="color: #000000; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Safety First:</strong> All work complies with Indian Electricity Rules 2005</li>
                                    <li><strong>Transparent Pricing:</strong> No hidden charges, upfront cost estimation</li>
                                    <li><strong>Warranty Coverage:</strong> 1-month service warranty on all electrical work</li>
                                    <li><strong>24/7 Support:</strong> Emergency electrical services available round the clock</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p style="color: #6c757d; font-size: 1rem; margin: 0;">
                                <strong>Serving Kangra District:</strong> Kangra, Dharamshala, Palampur, Baijnath, Nurpur, Dehra, and all nearby areas with the best electrical services.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="../index.php#booking-form" class="btn btn-primary btn-lg" style="background: #e9d5ff; border: 2px solid #dc143c; border-radius: 25px; padding: 15px 40px; font-weight: 600; font-size: 1.1rem; color: #000000; text-decoration: none;">
                <i class="fas fa-calendar-check"></i> Book Electrical Service Now
            </a>
        </div>
    </div>

    <?php 
    // Set base URL for links in components
    $base_url = '../';
    include("../vendor/inc/how-to-book.php");
    include("../vendor/inc/contact-us.php");
    ?>

    <?php include("../vendor/inc/footer.php");?>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .service-card-compact {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .service-card-compact:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(220, 20, 60, 0.2) !important;
        }

        .service-list li:hover {
            color: #dc143c !important;
            padding-left: 15px;
            background: rgba(220, 20, 60, 0.1);
            border-radius: 8px;
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("../vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>