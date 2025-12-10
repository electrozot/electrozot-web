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
    

    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="72x72" href="vendor/img/icons/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="96x96" href="vendor/img/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="128x128" href="vendor/img/icons/icon-128x128.png">
    <link rel="apple-touch-icon" sizes="144x144" href="vendor/img/icons/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="vendor/img/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="vendor/img/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="384x384" href="vendor/img/icons/icon-384x384.png">
    <link rel="apple-touch-icon" sizes="512x512" href="vendor/img/icons/icon-512x512.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="vendor/img/icons/icon-72x72.png">
    <link rel="icon" type="image/png" sizes="16x16" href="vendor/img/icons/icon-72x72.png">
    
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
    <!-- PWA Scripts -->
    <script defer src="pwa-install.js"></script>
    <script defer src="pwa-update-notification.js"></script>
    <script defer src="js/orientation-lock.js"></script>

</head>