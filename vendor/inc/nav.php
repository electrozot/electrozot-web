<nav class="navbar fixed-top navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #155e75 0%, #0891b2 25%, #06b6d4 50%, #0891b2 75%, #155e75 100%); background-size: 400% 400%; animation: gradientShift 20s ease infinite; box-shadow: 0 6px 25px rgba(21, 94, 117, 0.5), 0 2px 10px rgba(0,0,0,0.4); backdrop-filter: blur(10px); padding: 12px 0; overflow: visible; position: fixed; top: 0; left: 0; right: 0; z-index: 10000;">
    <!-- Glossy overlay effect -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 50%; background: linear-gradient(180deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%); pointer-events: none; z-index: 1;"></div>
    <div class="container-fluid" style="max-width: 1400px; padding: 0 10px; position: relative; z-index: 2;">
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-weight: 700; color: #fff !important; text-decoration: none; padding: 0; margin-left: 0; gap: 3px;">
            <img src="vendor/EZlogonew.png" alt="Electrozot Logo" class="navbar-logo" style="height: 70px; width: auto; transition: transform 0.3s ease; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
            <i class="fas fa-bolt logo-fallback" style="font-size: 2.5rem; display: none; animation: pulse 2s ease-in-out infinite; color: #ffd700;"></i>
            <div class="d-flex flex-column">
                <span style="font-size: 2rem; line-height: 1.1; font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Electrozot</span>
                <small class="navbar-tagline" style="font-size: 0.9rem; font-weight: 500; font-style: italic; line-height: 1; color: rgba(255, 255, 255, 0.95); letter-spacing: 0.5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">We Make Perfect</small>
            </div>
        </a>
        <!-- Mobile Login Button (visible only on mobile) - Direct to Client Login -->
        <div class="d-lg-none ml-auto" style="display: flex; align-items: center; gap: 12px;">
            <a href="usr/" class="btn mobile-login-btn" style="background: rgba(255, 255, 255, 0.25); border: 1.5px solid rgba(255, 255, 255, 0.4); color: #ffffff; font-weight: 600; padding: 5px 10px; border-radius: 5px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); text-decoration: none; font-size: 0.7rem; transition: all 0.3s ease; height: 28px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                <i class="fas fa-user" style="font-size: 0.65rem; margin-right: 4px;"></i> Login
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" style="border: 1.5px solid rgba(255, 255, 255, 0.4); padding: 5px 10px; background: rgba(255, 255, 255, 0.25); box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); border-radius: 5px; transition: all 0.3s ease; height: 28px; width: 36px; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                <span class="navbar-toggler-icon" style="width: 16px; height: 1.5px; background: #ffffff; display: block; position: relative; transition: all 0.3s ease;"></span>
                <span class="navbar-toggler-icon" style="width: 16px; height: 1.5px; background: #ffffff; display: block; position: relative; margin-top: 3px; transition: all 0.3s ease;"></span>
                <span class="navbar-toggler-icon" style="width: 16px; height: 1.5px; background: #ffffff; display: block; position: relative; margin-top: 3px; transition: all 0.3s ease;"></span>
            </button>
        </div>
        
        <style>
            .mobile-login-btn:hover {
                background: rgba(255, 255, 255, 0.4) !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3) !important;
                color: #ffffff !important;
                border-color: rgba(255, 255, 255, 0.6) !important;
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
            
            /* Logo hover effect */
            .navbar-logo:hover {
                transform: scale(1.05) rotate(2deg);
            }
            
            .navbar-brand:hover .navbar-tagline {
                color: #ffd700 !important;
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
                    height: 55px !important;
                }
                
                .navbar-brand span {
                    font-size: 1.6rem !important;
                }
                
                .navbar-tagline {
                    font-size: 0.8rem !important;
                }
            }
            
            @media (max-width: 576px) {
                .navbar-logo {
                    height: 50px !important;
                }
                
                .navbar-brand span {
                    font-size: 1.4rem !important;
                }
                
                .navbar-tagline {
                    font-size: 0.75rem !important;
                }
                
                .navbar-brand {
                    gap: 3px !important;
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
                
                // Close mobile menu on scroll
                var navbarCollapse = document.querySelector('.navbar-collapse');
                var navbarToggler = document.querySelector('.navbar-toggler');
                
                if (navbarCollapse && navbarToggler) {
                    window.addEventListener('scroll', function() {
                        if (navbarCollapse.classList.contains('show')) {
                            navbarToggler.click();
                        }
                    });
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
                    <a class="nav-link" href="index.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Home</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="about.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">About</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="services.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Services</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="contact.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Contact</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="gallery.php" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">Gallery</a>
                 </li>
                 <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="usr/" style="color: #fff !important; font-weight: 500; font-size: 0.92rem; padding: 7px 15px !important;">
                         <i class="fas fa-user"></i> Login
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
    </script>
 </nav>