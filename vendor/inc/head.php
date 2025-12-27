<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    
    <!-- SEO Meta Tags -->
    <?php
    // Dynamic SEO based on current page
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    $base_url = 'https://electrozot.in';
    
    // Default SEO values
    $seo_title = 'Best Electrical and Plumbing Service | Electrozot';
    $seo_description = 'Book certified electricians and technicians in your area. Expert electrical repairs, appliance services, wiring, and home automation. Available 24/7 with 30-day warranty.';
    $seo_keywords = 'electrician, electrical services, appliance repair, home automation, wiring, electrical repair, technician booking, emergency electrician';
    $seo_image = $base_url . '/vendor/EZlogonew.png';
    $canonical_url = $base_url . '/' . ($current_page == 'index' ? '' : $current_page . '.php');
    
    // Page-specific SEO
    switch($current_page) {
        case 'about':
            $seo_title = 'About Electrozot - Professional Electrical Services Team | We Make Perfect';
            $seo_description = 'Learn about Electrozot\'s certified electricians and technicians. Professional electrical services with experienced team, quality work, and customer satisfaction guarantee.';
            $seo_keywords = 'about electrozot, professional electricians, certified technicians, electrical services team, company profile';
            break;
        case 'services':
            $seo_title = 'Electrical Services - Wiring, Repairs & Appliance Service | Electrozot';
            $seo_description = 'Complete electrical services including wiring, repairs, appliance service, home automation, and emergency electrical work. Professional technicians available 24/7.';
            $seo_keywords = 'electrical services, wiring services, appliance repair, home automation, electrical installation, emergency electrician';
            break;
        case 'contact':
            $seo_title = 'Contact Electrozot - Book Electrical Services | Call 7559606925';
            $seo_description = 'Contact Electrozot for professional electrical services. Call 7559606925 or book online. Available 24/7 for emergency electrical repairs and installations.';
            $seo_keywords = 'contact electrician, book electrical service, emergency electrician contact, electrical service booking';
            break;
        case 'faq':
            $seo_title = 'FAQ - Electrical Services Questions & Answers | Electrozot';
            $seo_description = 'Get answers to common questions about electrical services, pricing, booking, and technician qualifications. Learn about Electrozot\'s service process and warranty.';
            $seo_keywords = 'electrical services FAQ, electrician questions, service pricing, booking process, electrical repair questions';
            break;
        case 'gallery':
            $seo_title = 'Gallery - Electrical Work Portfolio & Projects | Electrozot';
            $seo_description = 'View our electrical work portfolio including wiring projects, appliance installations, and home automation setups. Quality electrical services showcase.';
            $seo_keywords = 'electrical work portfolio, wiring projects, appliance installation gallery, electrical services showcase';
            break;
        case 'blog':
            $seo_title = 'Electrical Tips & Articles Blog | Electrozot - We Make Perfect';
            $seo_description = 'Read expert electrical tips, safety guides, and home maintenance articles. Stay updated with latest electrical technology and DIY safety tips.';
            $seo_keywords = 'electrical tips, electrical safety, home maintenance, electrical blog, DIY electrical safety';
            break;
        case 'privacy-policy':
            $seo_title = 'Privacy Policy - Data Protection & Security | Electrozot';
            $seo_description = 'Learn how Electrozot protects your personal information and data. Our privacy policy explains data collection, usage, and security measures.';
            $seo_keywords = 'privacy policy, data protection, personal information security, electrozot privacy';
            break;
    }
    ?>
    
    <title><?php echo htmlspecialchars($seo_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>">
    <meta name="author" content="Electrozot">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="<?php echo $canonical_url; ?>">
    
    <!-- Open Graph Meta Tags (Facebook, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($seo_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta property="og:image" content="<?php echo $seo_image; ?>">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">
    <meta property="og:site_name" content="Electrozot">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($seo_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta name="twitter:image" content="<?php echo $seo_image; ?>">
    
    <!-- Business/Local SEO -->
    <meta name="geo.region" content="IN">
    <meta name="geo.placename" content="India">
    <meta name="geo.position" content="20.5937;78.9629">
    <meta name="ICBM" content="20.5937, 78.9629">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="rating" content="general">
    <meta name="distribution" content="global">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <meta name="expires" content="never">
    <meta name="format-detection" content="telephone=yes">
    <meta name="format-detection" content="address=yes">
    
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "HomeAndConstructionBusiness",
        "@id": "https://electrozot.in/#business",
        "name": "ElectroZot",
        "url": "https://electrozot.in",
        "logo": "https://electrozot.in/vendor/EZlogonew.png",
        "description": "ElectroZot provides trusted electronic repair, electrical installation, and plumbing services with a commitment to quality materials, professional workmanship, and perfection.",
        "priceRange": "₹₹",
        "areaServed": {
            "@type": "Country",
            "name": "India"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "reviewCount": "126"
        },
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "ElectroZot Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Electronic Repair Services"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Electrical Installation Services"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Plumbing Solutions"
                    }
                }
            ]
        },
        "mainEntity": [
            {
                "@type": "Question",
                "name": "How do I book an electrical service with ElectroZot?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "You can book an electrical service through the ElectroZot website by selecting the required service and submitting a booking request."
                }
            },
            {
                "@type": "Question",
                "name": "What are your service hours?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "ElectroZot operates during standard business hours with flexible scheduling based on service availability."
                }
            },
            {
                "@type": "Question",
                "name": "How quickly can you arrive for service?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Response times depend on location and service type, but we aim to provide prompt assistance."
                }
            },
            {
                "@type": "Question",
                "name": "What electrical services do you provide?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We provide electrical repairs, installations, wiring, fault diagnosis, and maintenance services."
                }
            },
            {
                "@type": "Question",
                "name": "How do you calculate service charges?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Charges are based on service type, work complexity, materials used, and time required, with transparent pricing."
                }
            },
            {
                "@type": "Question",
                "name": "Do you provide warranty on your work?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, ElectroZot provides warranty coverage on services performed."
                }
            },
            {
                "@type": "Question",
                "name": "What payment methods do you accept?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We accept cash, UPI, and other digital payment methods."
                }
            },
            {
                "@type": "Question",
                "name": "Are your technicians verified and insured?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, all technicians are trained, background-verified, and insured."
                }
            }
        ]
    }
    </script>
    
    <!-- Call-to-Action Schema for Main Pages -->
    <?php if (in_array($current_page, ['index', 'services', 'contact', 'about'])): ?>
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
    <?php endif; ?>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ElectroZot">
    <meta name="mobile-web-app-capable" content="yes">
    
    <!-- PWA Splash Screens for iOS -->
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-2048x2732.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-1668x2224.png" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-1536x2048.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-1125x2436.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-1242x2208.png" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-750x1334.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="vendor/img/splash/splash-640x1136.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    
    <!-- CRITICAL: Preload navbar logo to prevent loading hang -->
    <link rel="preload" href="vendor/EZlogonew.png" as="image" type="image/png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Favicon - Primary Icons for Google -->
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon-96x96.png">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="assets/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="assets/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="assets/web-app-manifest-512x512.png">
    
    <!-- Microsoft Tile Icons -->
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="assets/web-app-manifest-192x192.png">
    <meta name="msapplication-config" content="assets/site.webmanifest">
    
    <!-- CRITICAL CSS - Prevents navbar loading hang -->
    <style>
        /* CRITICAL: Immediate navbar rendering - prevents loading hang */
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
            /* Prevent loading hang */
            will-change: auto !important;
            transform: translateZ(0) !important;
            backface-visibility: hidden !important;
        }
        
        /* CRITICAL: Logo and brand immediate sizing */
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
        
        /* CRITICAL: Container immediate sizing */
        .navbar .container-fluid {
            max-width: 1400px !important;
            padding: 0 10px !important;
            position: relative !important;
            z-index: 2 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        
        /* CRITICAL: Mobile responsive sizes - immediate loading */
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
        
        /* CRITICAL: Mobile buttons immediate display */
        @media (max-width: 991px) {
            .d-lg-none {
                display: flex !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            .navbar-toggler {
                border: none !important;
                background: transparent !important;
                padding: 2px 4px !important;
                min-height: 32px !important;
                width: 26px !important;
                display: flex !important;
            }
            
            .navbar-collapse {
                position: fixed !important;
                top: 94px !important;
                right: -100% !important;
                width: 150px !important;
                background: #4a5568 !important;
                padding: 10px !important;
                z-index: 99999 !important;
                transition: right 0.2s ease !important;
            }
            
            .navbar-collapse.show {
                right: 0 !important;
            }
        }
        
        /* CRITICAL: Basic body and container styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* CRITICAL: Button styles */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #EC4899;
            color: white;
        }
        
        .btn-primary:hover {
            background: #BE185D;
        }
        
        /* CRITICAL: Lazy loading */
        .lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .loaded {
            opacity: 1;
        }
    </style>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/modern-business.css" rel="stylesheet">
    <!--Font Awesome--->
    <link href="usr/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Compact UI overrides -->
    <link href="vendor/css/custom.css" rel="stylesheet">
    <!-- PWA Orientation Lock -->
    <link href="css/pwa-orientation-lock.css" rel="stylesheet">
    <!-- CRITICAL: Immediate navbar loading script - prevents hang -->
    <script>
        // Immediate execution to prevent navbar loading hang
        (function() {
            // Force immediate navbar visibility
            document.addEventListener('DOMContentLoaded', function() {
                const navbar = document.querySelector('.navbar');
                const logo = document.querySelector('.navbar-logo');
                const brand = document.querySelector('.navbar-brand');
                
                if (navbar) {
                    navbar.style.opacity = '1';
                    navbar.style.visibility = 'visible';
                    navbar.style.display = 'block';
                }
                
                if (logo) {
                    logo.style.opacity = '1';
                    logo.style.visibility = 'visible';
                    logo.style.display = 'block';
                }
                
                if (brand) {
                    brand.style.opacity = '1';
                    brand.style.visibility = 'visible';
                    brand.style.display = 'flex';
                }
            });
            
            // Fallback for immediate loading
            window.addEventListener('load', function() {
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    navbar.classList.add('loaded');
                }
            });
        })();
    </script>
    
    <!-- PWA Scripts - Load deferred for better performance -->
    <script defer src="pwa-install.js"></script>
    <script defer src="pwa-update-notification.js"></script>
    <script defer src="js/orientation-lock.js"></script>
    
    <!-- Performance Optimizer - Load immediately for better UX -->
    <script src="js/performance-optimizer.js"></script>
    

    
    <!-- Enhanced PWA Installation Support -->
    <script>
        // Global PWA installer function that can be called from anywhere
        window.installElectroZotPWA = function() {
            // Try to use the PWA installer from pwa-install.js
            if (window.PWAInstaller && window.PWAInstaller.install) {
                window.PWAInstaller.install();
            } else {
                // Fallback to manual guide
                console.log('PWA installer not available, showing manual guide');
                if (typeof $ !== 'undefined' && $('#pwaGuideModal').length) {
                    $('#pwaGuideModal').modal('show');
                } else {
                    // Ultimate fallback - show browser-specific instructions
                    const userAgent = navigator.userAgent.toLowerCase();
                    let instructions = '';
                    
                    if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
                        instructions = 'Chrome: Look for the install icon (⊕) in the address bar, or go to Menu > Install ElectroZot';
                    } else if (userAgent.includes('firefox')) {
                        instructions = 'Firefox: This app can be installed. Look for the install prompt or add to home screen option';
                    } else if (userAgent.includes('safari')) {
                        instructions = 'Safari: Tap the Share button and select "Add to Home Screen"';
                    } else if (userAgent.includes('edg')) {
                        instructions = 'Edge: Look for the install icon in the address bar, or go to Menu > Apps > Install ElectroZot';
                    } else {
                        instructions = 'Look for an "Install" or "Add to Home Screen" option in your browser menu';
                    }
                    
                    alert('Install ElectroZot App\n\n' + instructions);
                }
            }
        };
    </script>

</head>