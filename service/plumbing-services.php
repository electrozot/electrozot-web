<?php
// Include database configuration
include('../admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plumbing Services - Electrozot</title>
    
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
    <meta name="description" content="Professional plumbing services in Kangra District - Tap repair, basin installation, toilet installation, emergency plumbing solutions. 24/7 emergency plumber available. Best plumbing services near you!">
    <meta name="keywords" content="plumbing services Kangra, plumber Dharamshala, tap repair, basin installation, toilet installation, plumbing repair, emergency plumber, bathroom plumbing, kitchen plumbing, plumber near me">
    <meta name="author" content="Electrozot">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://electrozot.in/service/plumbing-services.php">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Professional Plumbing Services in Kangra District - ElectroZot">
    <meta property="og:description" content="Expert plumbing services including tap repair, basin installation, toilet installation & emergency plumbing solutions. 24/7 emergency service in Kangra, Dharamshala & nearby areas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrozot.in/service/plumbing-services.php">
    <meta property="og:image" content="https://electrozot.in/vendor/img/service2.png">
    <meta property="og:site_name" content="ElectroZot">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Professional Plumbing Services in Kangra District - ElectroZot">
    <meta name="twitter:description" content="Expert plumbing services including tap repair, basin installation, toilet installation & emergency plumbing solutions. 24/7 emergency service available.">
    <meta name="twitter:image" content="https://electrozot.in/vendor/img/service2.png">
    
    <!-- Structured Data Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "@id": "https://electrozot.in/service/plumbing-services.php#service",
        "name": "Plumbing Services",
        "description": "Professional plumbing services including tap repair, basin installation, toilet installation and emergency plumbing solutions with 24/7 availability.",
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
        "serviceType": "Plumbing Services",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Plumbing Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Tap & Faucet Repair",
                        "description": "Professional tap and faucet repair and replacement services for kitchen and bathroom"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Basin & Sink Installation",
                        "description": "Complete basin and sink installation with proper plumbing connections and fittings"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Toilet Installation",
                        "description": "Professional toilet installation and plumbing setup with proper drainage connections"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Emergency Plumbing",
                        "description": "24/7 emergency plumbing services for urgent repairs and blockage clearing"
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
                    <i class="fas fa-tint" style="color: #dc143c;"></i> Plumbing Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #000000; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Professional plumbing installation and repair services for your home and office
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
                <li class="breadcrumb-item active" style="color: #000000; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Plumbing Services</li>
            </ol>
        </nav>

        <!-- Service Categories -->
        <div class="row">
            <!-- Fixtures & Taps -->
            <div class="col-lg-8 col-md-8 mb-4 mx-auto">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: #f3e8ff; padding: 20px 25px; border: none;">
                        <h5 class="mb-0 text-center" style="font-size: 1.2rem; font-weight: 600; color: #000000;">
                            <i class="fas fa-faucet" style="color: #dc143c; margin-right: 10px;"></i>Professional Plumbing Services
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 25px;">
                        <p style="font-size: 1rem; color: #000000; margin-bottom: 20px; line-height: 1.6; text-align: center;">
                            Expert plumbing solutions by licensed plumbers in Kangra district. We provide reliable installation, repair, and maintenance services for all your plumbing needs with quality materials and workmanship.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="tap-repair" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #dc143c; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Tap & Faucet Services:</strong> Leaky tap repair, new faucet installation, mixer repair, shower head replacement, and water pressure optimization for kitchen and bathroom.
                            </li>
                            <li class="service-item" data-service="basin-installation" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #dc143c; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Basin & Sink Installation:</strong> Washbasin mounting, kitchen sink installation, drain pipe connections, waterproofing, and complete plumbing setup.
                            </li>
                            <li class="service-item" data-service="toilet-installation" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #dc143c; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Toilet & Commode Services:</strong> Complete toilet installation, flush tank repair, commode replacement, pipe connections, and bathroom plumbing solutions.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Electrozot for Plumbing -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h3 class="text-center mb-4" style="color: #dc143c; font-weight: 700;">
                            <i class="fas fa-medal"></i> Best Plumber & Plumbing Service in Kangra District
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color: #000000; font-weight: 600; margin-bottom: 15px;">🔧 Expert Plumbers Near You</h5>
                                <ul style="color: #000000; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Licensed Plumbers:</strong> Certified professionals with 15+ years experience</li>
                                    <li><strong>Quality Materials:</strong> ISI marked pipes, fittings, and plumbing accessories</li>
                                    <li><strong>Leak-Free Guarantee:</strong> Proper sealing and testing for all installations</li>
                                    <li><strong>Emergency Service:</strong> 24/7 available for urgent plumbing issues</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color: #000000; font-weight: 600; margin-bottom: 15px;">🏆 Top Plumbing Solutions</h5>
                                <ul style="color: #000000; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Complete Solutions:</strong> From minor repairs to complete bathroom renovation</li>
                                    <li><strong>Transparent Pricing:</strong> Upfront cost estimation with no hidden charges</li>
                                    <li><strong>Quick Response:</strong> Same-day service for most plumbing issues</li>
                                    <li><strong>Customer Satisfaction:</strong> 4.9/5 rating from satisfied customers</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p style="color: #6c757d; font-size: 1rem; margin: 0;">
                                <strong>Serving Kangra District:</strong> Best plumber and plumbing solutions in Kangra, Dharamshala, Palampur, Baijnath, Nurpur, Dehra, and all surrounding areas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Plumbing Service -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px; border-left: 5px solid #dc143c;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <h4 style="color: #dc143c; font-weight: 700; margin-bottom: 15px;">
                            <i class="fas fa-exclamation-triangle"></i> Emergency Plumbing Service - Available 24/7
                        </h4>
                        <p style="color: #000000; font-size: 1rem; margin-bottom: 15px;">
                            Facing a plumbing emergency in Kangra district? Our expert plumbers provide immediate response for burst pipes, major leaks, blocked drains, and toilet overflow issues.
                        </p>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div style="color: #dc143c; font-weight: 600;">
                                    <i class="fas fa-clock"></i> 24/7 Available
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div style="color: #dc143c; font-weight: 600;">
                                    <i class="fas fa-map-marker-alt"></i> All Kangra District
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div style="color: #dc143c; font-weight: 600;">
                                    <i class="fas fa-phone"></i> Quick Response
                                </div>
                            </div>
                        </div>
                        <a href="tel:+917559606925" class="btn mt-3" style="background: #e9d5ff; border: 2px solid #dc143c; border-radius: 20px; padding: 12px 30px; font-weight: 600; font-size: 1rem; color: #000000; text-decoration: none;">
                            <i class="fas fa-phone"></i> Call Now: +91 7559606925
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plumbing Features -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-center mb-4" style="color: #dc143c; font-weight: 700;">
                    <i class="fas fa-wrench"></i> Our Plumbing Expertise
                </h3>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: #faf5ff; border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-tools" style="font-size: 2.5rem; color: #dc143c; margin-bottom: 15px;"></i>
                        <h5 style="color: #000000; font-weight: 600; margin-bottom: 10px;">Professional Installation</h5>
                        <p style="color: #000000; font-size: 0.85rem; margin: 0;">Expert installation with proper sealing, connections, and leak testing</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: #faf5ff; border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-tint" style="font-size: 2.5rem; color: #dc143c; margin-bottom: 15px;"></i>
                        <h5 style="color: #000000; font-weight: 600; margin-bottom: 10px;">Leak-Free Guarantee</h5>
                        <p style="color: #000000; font-size: 0.85rem; margin: 0;">All installations come with proper sealing and leak-free guarantee</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: #faf5ff; border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-clock" style="font-size: 2.5rem; color: #dc143c; margin-bottom: 15px;"></i>
                        <h5 style="color: #000000; font-weight: 600; margin-bottom: 10px;">Quick Response</h5>
                        <p style="color: #000000; font-size: 0.85rem; margin: 0;">Fast response for emergency plumbing issues and repairs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Plumbing Issues -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h4 class="text-center mb-4" style="color: #dc143c; font-weight: 700;">
                            <i class="fas fa-exclamation-triangle"></i> Common Plumbing Issues We Fix
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: #000000; font-weight: 600; margin-bottom: 15px;">Water Flow Issues:</h6>
                                <ul style="color: #000000; font-size: 0.9rem;">
                                    <li>Low water pressure in taps</li>
                                    <li>Blocked or clogged drains</li>
                                    <li>Water not flowing properly</li>
                                    <li>Irregular water temperature</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #000000; font-weight: 600; margin-bottom: 15px;">Fixture Problems:</h6>
                                <ul style="color: #000000; font-size: 0.9rem;">
                                    <li>Leaky taps and faucets</li>
                                    <li>Toilet flush not working</li>
                                    <li>Shower head problems</li>
                                    <li>Basin and sink drainage issues</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Service -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: #faf5ff; border-radius: 15px; border-left: 5px solid #dc143c;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <h5 style="color: #dc143c; font-weight: 700; margin-bottom: 15px;">
                            <i class="fas fa-exclamation-circle"></i> Emergency Plumbing Service Available
                        </h5>
                        <p style="color: #000000; font-size: 1rem; margin-bottom: 15px;">
                            Facing a plumbing emergency? We provide quick response for urgent plumbing issues like major leaks, blocked drains, and toilet problems.
                        </p>
                        <a href="tel:+917559606925" class="btn" style="background: #e9d5ff; border: 2px solid #dc143c; border-radius: 20px; padding: 10px 25px; font-weight: 600; color: #000000; text-decoration: none;">
                            <i class="fas fa-phone"></i> Call for Emergency Service
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="../index.php#booking-form" class="btn btn-primary btn-lg" style="background: #e9d5ff; border: 2px solid #dc143c; border-radius: 25px; padding: 15px 40px; font-weight: 600; font-size: 1.1rem; color: #000000; text-decoration: none;">
                <i class="fas fa-calendar-check"></i> Book Plumbing Service Now
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

        .service-list li:hover p {
            color: #dc143c !important;
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("../vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>