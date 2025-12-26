<?php
// Include database configuration
include('../admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Services - Electrozot</title>
    
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
    <meta name="description" content="Regular maintenance services in Kangra District - AC deep cleaning, geyser descaling, water tank cleaning, washing machine service. Keep your appliances running efficiently. Book maintenance now!">
    <meta name="keywords" content="appliance maintenance Kangra, AC servicing Dharamshala, washing machine cleaning, geyser descaling, water filter service, preventive maintenance, AC deep cleaning, appliance care, maintenance technician near me">
    <meta name="author" content="Electrozot">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://electrozot.in/service/maintenance-services.php">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Regular Maintenance Services in Kangra District - ElectroZot">
    <meta property="og:description" content="Professional maintenance services for AC deep cleaning, geyser descaling, water tank cleaning & more. Keep your appliances running efficiently in Kangra, Dharamshala & nearby areas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrozot.in/service/maintenance-services.php">
    <meta property="og:image" content="https://electrozot.in/vendor/img/service1.png">
    <meta property="og:site_name" content="ElectroZot">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Regular Maintenance Services in Kangra District - ElectroZot">
    <meta name="twitter:description" content="Professional maintenance services for AC, geyser, water tank cleaning and more. Keep your appliances running efficiently.">
    <meta name="twitter:image" content="https://electrozot.in/vendor/img/service1.png">
    
    <!-- Structured Data Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "@id": "https://electrozot.in/service/maintenance-services.php#service",
        "name": "Maintenance Services",
        "description": "Regular maintenance services for AC deep cleaning, geyser descaling, water tank cleaning and appliance care to keep them running efficiently.",
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
        "serviceType": "Maintenance Services",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Maintenance Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "AC Deep Cleaning",
                        "description": "Comprehensive AC cleaning and maintenance service for optimal cooling performance"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Geyser Descaling",
                        "description": "Professional geyser descaling and maintenance to improve heating efficiency"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Water Tank Cleaning",
                        "description": "Thorough water tank cleaning and sanitization for clean water supply"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Washing Machine Service",
                        "description": "Complete washing machine cleaning and maintenance service"
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

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fdf5ff 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("../vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #DDD6FE 0%, #C4B5FD 25%, #A78BFA 50%, #8B5CF6 75%, #7C3AED 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px;">
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
                    <i class="fas fa-tools" style="color: #ffffff;"></i> Maintenance Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #f3f4f6; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Regular maintenance services to keep your appliances running efficiently
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../index.php" style="color: #8B5CF6; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="../services.php" style="color: #8B5CF6; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        Services
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Maintenance Services</li>
            </ol>
        </nav>

        <!-- Service Categories -->
        <div class="row">
            <!-- Routine Care Services -->
            <div class="col-lg-8 col-md-8 mb-4 mx-auto">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FDF5FF 100%); border-radius: 15px; overflow: hidden;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #DDD6FE 0%, #C4B5FD 100%); padding: 20px 25px; border: none;">
                        <h5 class="mb-0 text-center" style="font-size: 1.2rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-cog" style="color: #8B5CF6; margin-right: 10px;"></i>Professional Maintenance Services
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 25px;">
                        <p style="font-size: 1rem; color: #6c757d; margin-bottom: 20px; line-height: 1.6; text-align: center;">
                            Regular maintenance services to keep your appliances running efficiently and extend their lifespan. Our expert technicians provide comprehensive cleaning, servicing, and preventive maintenance.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="ac-servicing" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #8B5CF6; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>AC Deep Servicing:</strong> Complete wet and dry cleaning, gas checking, filter replacement, coil cleaning, and performance optimization for all AC types.
                            </li>
                            <li class="service-item" data-service="washing-machine-maintenance" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #8B5CF6; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Washing Machine Care:</strong> Drum cleaning, filter maintenance, pipe cleaning, motor inspection, and performance calibration for optimal washing.
                            </li>
                            <li class="service-item" data-service="geyser-servicing" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #8B5CF6; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Geyser Descaling:</strong> Tank descaling, heating element inspection, thermostat calibration, safety valve checking, and efficiency improvement.
                            </li>
                            <li class="service-item" data-service="water-filter-servicing" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #f1f3f4;">
                                <i class="fas fa-check-circle" style="color: #8B5CF6; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Water Filter Service:</strong> Filter cartridge replacement, RO membrane change, UV lamp replacement, system sanitization, and water quality testing.
                            </li>
                            <li class="service-item" data-service="water-tank-cleaning" style="font-size: 0.9rem; color: #495057; padding: 12px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #8B5CF6; font-size: 0.85rem; margin-right: 12px;"></i>
                                <strong>Water Tank Cleaning:</strong> Professional overhead and underground tank cleaning, disinfection, sanitization, and quality assurance for safe water supply.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Electrozot for Maintenance -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h3 class="text-center mb-4" style="color: #8B5CF6; font-weight: 700;">
                            <i class="fas fa-crown"></i> Best Maintenance Service in Kangra District
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">🛠️ Expert Maintenance Team</h5>
                                <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Skilled Technicians:</strong> Trained professionals with 12+ years maintenance experience</li>
                                    <li><strong>Advanced Equipment:</strong> Professional cleaning tools and testing instruments</li>
                                    <li><strong>Genuine Products:</strong> Original filters, parts, and cleaning chemicals</li>
                                    <li><strong>Preventive Care:</strong> Comprehensive maintenance to prevent future problems</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">⚡ Top Maintenance Solutions</h5>
                                <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.8;">
                                    <li><strong>Extended Lifespan:</strong> Regular maintenance increases appliance life by 40%</li>
                                    <li><strong>Energy Efficiency:</strong> Proper servicing reduces electricity bills significantly</li>
                                    <li><strong>Health Benefits:</strong> Clean appliances ensure better air and water quality</li>
                                    <li><strong>Cost Savings:</strong> Prevents expensive repairs and replacements</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p style="color: #6c757d; font-size: 1rem; margin: 0;">
                                <strong>Serving Kangra District:</strong> Professional maintenance services in Kangra, Dharamshala, Palampur, Baijnath, Nurpur, and all surrounding areas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Maintenance -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-center mb-4" style="color: #8B5CF6; font-weight: 700;">
                    <i class="fas fa-star"></i> Why Regular Maintenance Matters
                </h3>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-chart-line" style="font-size: 2.5rem; color: #8B5CF6; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Better Performance</h5>
                        <p style="color: #6c757d; font-size: 0.85rem; margin: 0;">Regular maintenance keeps appliances running at peak efficiency</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-clock" style="font-size: 2.5rem; color: #8B5CF6; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Longer Lifespan</h5>
                        <p style="color: #6c757d; font-size: 0.85rem; margin: 0;">Extends the life of your appliances significantly</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-rupee-sign" style="font-size: 2.5rem; color: #8B5CF6; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Cost Savings</h5>
                        <p style="color: #6c757d; font-size: 0.85rem; margin: 0;">Prevents costly repairs and reduces energy bills</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border-radius: 12px;">
                    <div class="card-body text-center" style="padding: 25px;">
                        <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #8B5CF6; margin-bottom: 15px;"></i>
                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 10px;">Safety First</h5>
                        <p style="color: #6c757d; font-size: 0.85rem; margin: 0;">Ensures safe operation and prevents hazards</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Schedule -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h4 class="text-center mb-4" style="color: #8B5CF6; font-weight: 700;">
                            <i class="fas fa-calendar-alt"></i> Recommended Maintenance Schedule
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">Every 3 Months:</h6>
                                <ul style="color: #6c757d; font-size: 0.9rem;">
                                    <li>AC Filter Cleaning</li>
                                    <li>Water Filter Cartridge Check</li>
                                    <li>Geyser Safety Inspection</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">Every 6 Months:</h6>
                                <ul style="color: #6c757d; font-size: 0.9rem;">
                                    <li>AC Deep Servicing</li>
                                    <li>Washing Machine Deep Clean</li>
                                    <li>Water Tank Cleaning</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="../index.php#booking-form" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); border: none; border-radius: 25px; padding: 15px 40px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);">
                <i class="fas fa-calendar-check"></i> Schedule Maintenance Service
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
            box-shadow: 0 15px 35px rgba(139, 92, 246, 0.2) !important;
        }

        .service-list li:hover {
            color: #8B5CF6 !important;
            padding-left: 15px;
            background: rgba(139, 92, 246, 0.1);
            border-radius: 8px;
        }

        .service-list li:hover p {
            color: #8B5CF6 !important;
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("../vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>