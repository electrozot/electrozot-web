<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    
    <!-- SEO Meta Tags -->
    <?php
    // Dynamic SEO based on current page
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    $base_url = 'https://electrozot.in';
    
    // Default SEO values
    $seo_title = 'Electrozot - Professional Electrical & Technical Services | We Make Perfect';
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
        "@type": "LocalBusiness",
        "name": "Electrozot",
        "description": "Professional electrical and technical services provider",
        "url": "https://electrozot.in",
        "logo": "https://electrozot.in/vendor/EZlogonew.png",
        "image": "https://electrozot.in/vendor/EZlogonew.png",
        "telephone": "+917559606925",
        "email": "electrozot@outlook.com",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "IN",
            "addressRegion": "India"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "20.5937",
            "longitude": "78.9629"
        },
        "openingHours": "Mo-Su 07:00-21:00",
        "serviceArea": {
            "@type": "Country",
            "name": "India"
        },
        "services": [
            "Electrical Wiring",
            "Appliance Repair",
            "Home Automation",
            "Emergency Electrical Services",
            "Electrical Installation",
            "Electrical Maintenance"
        ],
        "priceRange": "₹₹",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "reviewCount": "150"
        },
        "sameAs": [
            "https://www.instagram.com/electrozot.in/"
        ]
    }
    </script>
    
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
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="57x57" href="vendor/img/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="vendor/img/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="vendor/img/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="vendor/img/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="vendor/img/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="vendor/img/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="vendor/img/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="vendor/img/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="vendor/img/icons/apple-icon-180x180.png">
    
    <!-- Microsoft Tile Icons -->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="vendor/img/icons/ms-icon-144x144.png">
    <meta name="msapplication-square70x70logo" content="vendor/img/icons/ms-icon-70x70.png">
    <meta name="msapplication-square150x150logo" content="vendor/img/icons/ms-icon-150x150.png">
    <meta name="msapplication-square310x310logo" content="vendor/img/icons/ms-icon-310x310.png">
    
    <!-- Android Chrome Icons -->
    <link rel="icon" type="image/png" sizes="36x36" href="vendor/img/icons/android-icon-36x36.png">
    <link rel="icon" type="image/png" sizes="48x48" href="vendor/img/icons/android-icon-48x48.png">
    <link rel="icon" type="image/png" sizes="72x72" href="vendor/img/icons/android-icon-72x72.png">
    <link rel="icon" type="image/png" sizes="96x96" href="vendor/img/icons/android-icon-96x96.png">
    <link rel="icon" type="image/png" sizes="144x144" href="vendor/img/icons/android-icon-144x144.png">
    <link rel="icon" type="image/png" sizes="192x192" href="vendor/img/icons/android-icon-192x192.png">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="vendor/img/icons/favicon.ico" type="image/x-icon">
    <link rel="icon" href="vendor/img/icons/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="vendor/img/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="vendor/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="vendor/img/icons/favicon-96x96.png">
    
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
        
        /* Navbar color animation is defined in nav.php to avoid conflicts */
        
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
            
            /* Mobile navbar animation is handled in nav.php */
        }
        
        /* CRITICAL: Loaded state for smooth transitions */
        .navbar.loaded {
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        .navbar.loaded .navbar-logo,
        .navbar.loaded .navbar-brand {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css?v=<?php echo time(); ?>" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/modern-business.css?v=<?php echo time(); ?>" rel="stylesheet">
    <!--Font Awesome--->
    <link href="usr/vendor/fontawesome-free/css/all.min.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css">
    <!-- Compact UI overrides -->
    <link href="vendor/css/custom.css?v=<?php echo time(); ?>" rel="stylesheet">
    <!-- PWA Orientation Lock -->
    <link href="css/pwa-orientation-lock.css?v=<?php echo time(); ?>" rel="stylesheet">
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
    
    <!-- PWA Scripts -->
    <script defer src="pwa-install.js?v=<?php echo time(); ?>"></script>
    <script defer src="pwa-update-notification.js?v=<?php echo time(); ?>"></script>
    <script defer src="pwa-diagnostic.js?v=<?php echo time(); ?>"></script>
    <script defer src="js/orientation-lock.js?v=<?php echo time(); ?>"></script>
    

    
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