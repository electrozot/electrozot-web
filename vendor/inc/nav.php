<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-color-transition" style="backdrop-filter: blur(10px); padding: 8px 0 4px 0; overflow: visible; position: fixed !important; top: 0 !important; left: 0; right: 0; width: 100%; z-index: 10000 !important; border: none; margin: 0; box-sizing: border-box;">
    <style>
        /* Force navbar to stay fixed at top */
        .navbar.fixed-top {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 10000 !important;
            border: none !important;
            border-top: none !important;
            border-bottom: none !important;
            margin: 0 !important;
            padding-top: 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Remove any white lines or borders on mobile */
        @media (max-width: 991px) {
            .navbar.fixed-top {
                border: none !important;
                border-top: none !important;
                border-bottom: none !important;
                box-shadow: 0 6px 25px rgba(139, 0, 0, 0.5), 0 2px 10px rgba(0,0,0,0.4) !important;
                padding: 4px 0 2px 0 !important;
            }
            
            .navbar.fixed-top::after,
            .navbar.fixed-top::before {
                display: none !important;
            }
            
            .navbar.fixed-top > div {
                border-top: none !important;
            }
        }
        
        /* Remove any container borders */
        .navbar .container-fluid {
            border: none !important;
            border-top: none !important;
        }
        
        @keyframes navbarColorChange {
            0% {
                background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%);
                box-shadow: 0 6px 25px rgba(139, 0, 0, 0.5), 0 2px 10px rgba(0,0,0,0.4);
            }
            25% {
                background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%);
                box-shadow: 0 6px 25px rgba(139, 0, 0, 0.5), 0 2px 10px rgba(0,0,0,0.4);
            }
            37.5% {
                background: linear-gradient(135deg, #155e75 0%, #0891b2 50%, #06b6d4 100%);
                box-shadow: 0 6px 25px rgba(21, 94, 117, 0.5), 0 2px 10px rgba(0,0,0,0.4);
            }
            62.5% {
                background: linear-gradient(135deg, #155e75 0%, #0891b2 50%, #06b6d4 100%);
                box-shadow: 0 6px 25px rgba(21, 94, 117, 0.5), 0 2px 10px rgba(0,0,0,0.4);
            }
            75% {
                background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%);
                box-shadow: 0 6px 25px rgba(139, 0, 0, 0.5), 0 2px 10px rgba(0,0,0,0.4);
            }
            100% {
                background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%);
                box-shadow: 0 6px 25px rgba(139, 0, 0, 0.5), 0 2px 10px rgba(0,0,0,0.4);
            }
        }
        
        /* Enable animation on both desktop and mobile */
        .navbar-color-transition {
            background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%);
            animation: navbarColorChange 8s ease-in-out infinite !important;
        }
    </style>
    <!-- Glossy overlay effect - removed to fix white line -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 50%; background: transparent; pointer-events: none; z-index: 1; display: none;"></div>
    <div class="container-fluid" style="max-width: 1400px; padding: 0 10px; position: relative; z-index: 2;">
        <?php
        // Detect if we're in a subdirectory and adjust paths accordingly
        $currentDir = dirname($_SERVER['PHP_SELF']);
        $isSubdirectory = (strpos($currentDir, '/service') !== false || strpos($currentDir, '/admin') !== false || strpos($currentDir, '/usr') !== false);
        $basePath = $isSubdirectory ? '../' : '';
        $homeLink = $isSubdirectory ? '../index.php' : 'index.php';
        $logoPath = $basePath . 'vendor/EZlogonew.png';
        $loginPath = $basePath . 'usr/';
        ?>
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $homeLink; ?>" style="font-weight: 700; color: #fff !important; text-decoration: none; padding: 0; margin-left: 0; gap: 0px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; -webkit-touch-callout: none; -webkit-tap-highlight-color: transparent; touch-action: manipulation;">
            <img src="<?php echo $logoPath; ?>" alt="Electrozot - Best Electrical & Plumbing Service in Kangra District" class="navbar-logo" style="height: 95px; width: auto; transition: transform 0.3s ease; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
            <i class="fas fa-bolt logo-fallback" style="font-size: 3rem; display: none; animation: pulse 2s ease-in-out infinite; color: #ffd700;"></i>
            <div class="d-flex flex-column" style="margin-left: -10px;">
                <span style="font-size: 2rem; line-height: 1.1; font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Electrozot</span>
                <small class="navbar-tagline" style="font-size: 0.9rem; font-weight: 500; font-style: italic; line-height: 1; color: rgba(255, 255, 255, 0.95); letter-spacing: 0.5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">We Make Perfect</small>
            </div>
        </a>
        <!-- PWA Install Button - Mobile Left Side -->
        <button id="pwa-install-mobile-btn" class="btn d-lg-none" style="background: transparent; border: none; color: #ffffff; font-weight: 600; padding: 2px 4px; border-radius: 4px; box-shadow: none; font-size: 0.5rem; transition: all 0.3s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1; white-space: nowrap; min-height: 32px; margin-left: -5px;">
            <i class="fas fa-download" style="font-size: 0.65rem; margin-bottom: 1px;"></i><span style="font-weight: 600; font-size: 0.5rem; white-space: nowrap;">App</span>
        </button>
        <!-- Mobile Buttons (visible only on mobile) -->
        <div class="d-lg-none ml-auto" style="display: flex; align-items: center; gap: 3px; margin-right: 10px;">
            <a href="<?php echo $loginPath; ?>" class="btn mobile-login-btn" style="background: transparent; border: none; color: #ffffff; font-weight: 600; padding: 2px 5px; border-radius: 4px; box-shadow: none; text-decoration: none; font-size: 0.5rem; transition: all 0.3s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1; white-space: nowrap; min-height: 32px;">
                <i class="fas fa-user" style="font-size: 0.65rem; margin-bottom: 1px;"></i><span style="font-weight: 600; font-size: 0.5rem; white-space: nowrap;">Login</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" style="border: none; padding: 2px 4px; background: transparent; box-shadow: none; border-radius: 4px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; min-height: 32px; width: 26px;">
                <i class="fas fa-bars" style="font-size: 0.6rem; color: #ffffff;"></i>
            </button>
        </div>
        
        <style>
            #pwa-install-mobile-btn:hover,
            .mobile-login-btn:hover {
                background: rgba(255, 255, 255, 0.4) !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3) !important;
                color: #ffffff !important;
                border-color: rgba(255, 255, 255, 0.6) !important;
            }
            
            #pwa-install-mobile-btn:active,
            .mobile-login-btn:active {
                transform: scale(0.95);
            }
            
            /* Hamburger menu button hover effect */
            .navbar-toggler:hover {
                background: rgba(255, 255, 255, 0.4) !important;
                box-shadow: 0 6px 16px rgba(255, 255, 255, 0.3) !important;
                transform: scale(1.05);
                border-color: rgba(255, 255, 255, 0.6) !important;
            }
            
            .navbar-toggler:hover .navbar-toggler-icon {
                background: #ffffff !important;
            }
            
            .navbar-toggler:active {
                transform: scale(0.95);
            }
            
            /* Logo hover effect - disabled on mobile */
            @media (min-width: 992px) {
                .navbar-logo:hover {
                    transform: scale(1.05) rotate(2deg);
                }
                
                .navbar-brand:hover .navbar-tagline {
                    color: #ffd700 !important;
                }
            }
            
            /* Prevent navbar brand movement on touch */
            .navbar-brand {
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
                user-select: none !important;
                -webkit-touch-callout: none !important;
                -webkit-tap-highlight-color: transparent !important;
                touch-action: manipulation !important;
                position: relative !important;
                transform: translateZ(0) !important;
                backface-visibility: hidden !important;
            }
            
            .navbar-brand,
            .navbar-brand *,
            .navbar-logo,
            .logo-fallback {
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
                user-select: none !important;
                -webkit-touch-callout: none !important;
                -webkit-tap-highlight-color: transparent !important;
                touch-action: manipulation !important;
                pointer-events: auto !important;
            }
            
            /* Prevent any transform animations on touch */
            @media (max-width: 991px) {
                .navbar-brand,
                .navbar-logo,
                .logo-fallback {
                    transform: none !important;
                    transition: none !important;
                }
                
                .navbar-brand:active,
                .navbar-brand:focus,
                .navbar-logo:active,
                .navbar-logo:focus {
                    transform: none !important;
                    outline: none !important;
                }
            }
            
            @keyframes blink {
                0%, 100% {
                    opacity: 1;
                    background: #ffffff;
                }
                25% {
                    opacity: 0.3;
                    background: #8b0000;
                }
                50% {
                    opacity: 1;
                    background: #ffffff;
                }
                75% {
                    opacity: 0.3;
                    background: #8b0000;
                }
            }
            
            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
            }
            
            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
                100% {
                    background-position: 0% 50%;
                }
            }
            
            .mobile-login-btn.blink-active {
                animation: blink 0.6s ease-in-out;
            }
            
            /* Responsive logo sizing */
            @media (max-width: 768px) {
                .navbar-logo {
                    height: 65px !important;
                }
                
                .navbar-brand span {
                    font-size: 1.3rem !important;
                }
                
                .navbar-tagline {
                    font-size: 0.65rem !important;
                }
                
                .navbar-brand {
                    gap: 0px !important;
                }
                
                .navbar-brand .d-flex.flex-column {
                    margin-left: -8px !important;
                }
            }
            
            @media (max-width: 576px) {
                .navbar-logo {
                    height: 60px !important;
                }
                
                .navbar-brand span {
                    font-size: 1.1rem !important;
                }
                
                .navbar-tagline {
                    font-size: 0.6rem !important;
                }
                
                .navbar-brand {
                    gap: 0px !important;
                }
                
                .navbar-brand .d-flex.flex-column {
                    margin-left: -6px !important;
                }
            }
            
            /* Mobile menu styling - Slide from right */
            @media (max-width: 991px) {
                .navbar-collapse {
                    position: fixed !important;
                    top: 94px !important;
                    right: -100% !important;
                    width: 150px !important;
                    height: auto !important;
                    max-height: calc(100vh - 100px) !important;
                    background: #4a5568 !important;
                    padding: 10px !important;
                    padding-top: 50px !important;
                    box-shadow: -3px 3px 12px rgba(0,0,0,0.25) !important;
                    transition: right 0.3s ease-in-out !important;
                    z-index: 99999 !important;
                    overflow-y: auto !important;
                    overflow-x: hidden !important;
                    margin-top: 0 !important;
                    border-radius: 8px 0 0 8px !important;
                }
                
                .navbar-collapse.show {
                    right: 0 !important;
                }
                
                /* Add backdrop overlay when menu is open */
                .navbar-collapse.show::before {
                    content: '' !important;
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    background: rgba(0, 0, 0, 0.5) !important;
                    z-index: -1 !important;
                    animation: fadeIn 0.3s ease !important;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                .navbar-collapse .navbar-nav {
                    flex-direction: column !important;
                    width: 100% !important;
                }
                
                .navbar-collapse .nav-link {
                    color: #ffffff !important;
                    font-weight: 600 !important;
                    padding: 8px 12px !important;
                    margin-bottom: 4px !important;
                    border-radius: 6px !important;
                    font-size: 0.85rem !important;
                }
                
                .navbar-collapse .nav-link:hover {
                    background: rgba(102, 126, 234, 0.3) !important;
                }
                
                .navbar-collapse .nav-link i {
                    color: #a0aec0 !important;
                }
                
                /* Arrow close button styling */
                .mobile-menu-arrow-close {
                    display: none !important;
                }
                
                .navbar-collapse.show .mobile-menu-arrow-close {
                    display: flex !important;
                    position: absolute !important;
                    top: 10px !important;
                    left: 10px !important;
                    background: rgba(255, 255, 255, 0.2) !important;
                    border: 2px solid rgba(255, 255, 255, 0.4) !important;
                    color: #ffffff !important;
                    width: 36px !important;
                    height: 36px !important;
                    border-radius: 8px !important;
                    align-items: center !important;
                    justify-content: center !important;
                    font-size: 1.2rem !important;
                    font-weight: bold !important;
                    cursor: pointer !important;
                    transition: all 0.3s ease !important;
                    z-index: 10001 !important;
                    backdrop-filter: blur(10px) !important;
                }
                
                .mobile-menu-arrow-close:hover {
                    background: rgba(255, 255, 255, 0.35) !important;
                    border-color: rgba(255, 255, 255, 0.6) !important;
                    transform: translateX(-3px) !important;
                    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3) !important;
                }
                
                .mobile-menu-arrow-close:active {
                    transform: translateX(-1px) scale(0.95) !important;
                }
            }
        </style>
        
        <script>
            // Immediate navbar optimization - prevent hanging
            (function() {
                // Force navbar to be visible immediately
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    navbar.style.opacity = '1';
                    navbar.style.visibility = 'visible';
                }
                
                // Optimize logo loading
                const logo = document.querySelector('.navbar-logo');
                if (logo) {
                    logo.style.display = 'block';
                    logo.style.opacity = '1';
                }
            })();
            
            document.addEventListener('DOMContentLoaded', function() {
                var loginBtn = document.querySelector('.mobile-login-btn');
                if (loginBtn) {
                    loginBtn.addEventListener('click', function(e) {
                        this.classList.add('blink-active');
                        setTimeout(function() {
                            loginBtn.classList.remove('blink-active');
                        }, 600);
                    });
                }
                
                // Close mobile menu on scroll - optimized
                var navbarCollapse = document.querySelector('.navbar-collapse');
                var navbarToggler = document.querySelector('.navbar-toggler');
                
                if (navbarCollapse && navbarToggler) {
                    let scrollTimeout;
                    window.addEventListener('scroll', function() {
                        clearTimeout(scrollTimeout);
                        scrollTimeout = setTimeout(function() {
                            if (navbarCollapse.classList.contains('show')) {
                                navbarToggler.click();
                            }
                        }, 100);
                    }, { passive: true });
                }
            });
        </script>
         <div class="collapse navbar-collapse" id="navbarResponsive">
            <!-- Arrow Close Button -->
            <button class="mobile-menu-arrow-close d-lg-none" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-label="Close menu">
                ›
            </button>
            <ul class="navbar-nav ml-auto" style="align-items: center;">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>index.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Home</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>about.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">About</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>services.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Services</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>contact.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Contact</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>gallery.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Gallery</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>blog.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Blog</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>faq.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">FAQ</a>
                 </li>
                 <li class="nav-item d-none d-lg-block">
                    <button id="pwa-install-nav-btn" class="btn nav-link" style="color: #fff !important; font-weight: 600; font-size: 0.75rem; padding: 5px 10px !important; background: rgba(255, 255, 255, 0.25); border: 1.5px solid rgba(255, 255, 255, 0.5); border-radius: 5px; transition: all 0.3s ease; display: inline-block !important; z-index: 1000; position: relative;">
                         <i class="fas fa-download" style="font-size: 0.7rem;"></i> Download
                     </button>
                 </li>
                 <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="usr/" style="color: #fff !important; font-weight: 600; font-size: 0.7rem; padding: 5px 8px !important; display: flex; flex-direction: column; align-items: center; line-height: 1.2; white-space: nowrap;">
                         <i class="fas fa-user" style="font-size: 1.1rem; margin-bottom: 2px;"></i><span style="font-weight: 600; font-size: 0.7rem; white-space: nowrap;">Login</span>
                     </a>
                 </li>
             </ul>
         </div>
     </div>
    <script>
        // Show fallback icon if logo doesn't load
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.navbar-logo');
            const fallback = document.querySelector('.logo-fallback');
            if (logo) {
                logo.onerror = function() {
                    this.style.display = 'none';
                    if (fallback) fallback.style.display = 'inline-block';
                };
                // Check if image loaded successfully
                if (logo.complete && logo.naturalHeight === 0) {
                    logo.style.display = 'none';
                    if (fallback) fallback.style.display = 'inline-block';
                }
            }
        });
        
        // PWA Install Button Handler - Handled by pwa-install.js
        // The install functionality is now managed by the pwa-install.js file
        // which provides better mobile popups and centralized handling
    </script>
    
    <style>
        #pwa-install-nav-btn {
            animation: pulseGlow 2s ease-in-out infinite;
        }
        
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            }
            50% {
                box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
            }
        }
        
        #pwa-install-nav-btn:hover {
            background: rgba(255, 255, 255, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.7) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.5) !important;
            animation: none;
        }
        
        #pwa-install-nav-btn:active {
            transform: translateY(0);
        }
    </style>
 </nav>