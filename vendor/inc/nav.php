<nav class="navbar fixed-top navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #dc143c 0%, #a01030 50%, #5a0a0a 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; box-shadow: 0 4px 20px rgba(0,0,0,0.15); backdrop-filter: blur(10px); padding: 15px 0;">
    <div class="container-fluid" style="max-width: 1400px; padding: 0 30px;">
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-weight: 700; color: #fff !important; text-decoration: none; padding: 5px 0;">
            <img src="vendor/EZlogonew.png" alt="Electrozot Logo" class="navbar-logo" style="height: 55px; width: auto; margin-right: 15px; transition: transform 0.3s ease; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
            <i class="fas fa-bolt logo-fallback" style="font-size: 2rem; margin-right: 12px; display: none; animation: pulse 2s ease-in-out infinite; color: #ffd700;"></i>
            <div class="d-flex flex-column">
                <span style="font-size: 1.6rem; line-height: 1.2; font-weight: 700;">Electrozot</span>
                <small class="navbar-tagline" style="font-size: 0.85rem; font-weight: 400; font-style: italic; line-height: 1; color: rgba(255, 255, 255, 0.95); letter-spacing: 0.5px;">We Make Perfect</small>
            </div>
        </a>
        <!-- Mobile Login Button (visible only on mobile) - Direct to Client Login -->
        <div class="d-lg-none ml-auto" style="display: flex; align-items: center; gap: 12px;">
            <a href="usr/" class="btn mobile-login-btn" style="background: #ffffff; border: 2px solid #ffffff; color: #8b0000; font-weight: 700; padding: 10px 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3); text-decoration: none; font-size: 1rem; transition: all 0.3s ease;">
                <i class="fas fa-user"></i> Login
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" style="border: 2px solid rgba(255,255,255,0.8); padding: 10px 14px; background: rgba(255,255,255,0.1);">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <style>
            .mobile-login-btn:hover {
                background: #f8f9fa !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 255, 255, 0.5) !important;
                color: #8b0000 !important;
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
            
            .mobile-login-btn.blink-active {
                animation: blink 0.6s ease-in-out;
            }
            
            /* Mobile menu styling - Slide from right */
            @media (max-width: 991px) {
                .navbar-collapse {
                    position: fixed !important;
                    top: 70px !important;
                    right: -100% !important;
                    width: 160px !important;
                    height: auto !important;
                    max-height: calc(100vh - 90px) !important;
                    background: #4a5568 !important;
                    padding: 12px !important;
                    box-shadow: -4px 4px 15px rgba(0,0,0,0.3) !important;
                    transition: right 0.3s ease-in-out !important;
                    z-index: 9999 !important;
                    overflow-y: auto !important;
                    overflow-x: hidden !important;
                    margin-top: 0 !important;
                    border-radius: 10px 0 0 10px !important;
                }
                
                .navbar-collapse.show {
                    right: 0 !important;
                }
                
                .navbar-collapse .navbar-nav {
                    flex-direction: column !important;
                    width: 100% !important;
                }
                
                .navbar-collapse .nav-link {
                    color: #ffffff !important;
                    font-weight: 600 !important;
                    padding: 12px 15px !important;
                    margin-bottom: 5px !important;
                    border-radius: 8px !important;
                    font-size: 0.9rem !important;
                }
                
                .navbar-collapse .nav-link:hover {
                    background: rgba(102, 126, 234, 0.3) !important;
                }
                
                .navbar-collapse .nav-link i {
                    color: #a0aec0 !important;
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
            <ul class="navbar-nav ml-auto" style="align-items: center;">
                <li class="nav-item">
                    <a class="nav-link" href="index.php" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">Home</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="about.php" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">About</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="services.php" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">Services</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="contact.php" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">Contact</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="gallery.php" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">Gallery</a>
                 </li>
                 <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="usr/" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">
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