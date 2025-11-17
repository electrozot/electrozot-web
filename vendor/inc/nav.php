<nav class="navbar fixed-top navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #00c853 0%, #00b0ff 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; box-shadow: 0 4px 20px rgba(0,0,0,0.15); backdrop-filter: blur(10px); padding: 15px 0;">
    <div class="container-fluid" style="max-width: 1400px; padding: 0 30px;">
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-weight: 700; color: #fff !important; text-decoration: none; padding: 5px 0;">
            <img src="vendor/EZlogonew.png" alt="Electrozot Logo" class="navbar-logo" style="height: 55px; width: auto; margin-right: 15px; transition: transform 0.3s ease; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
            <i class="fas fa-bolt logo-fallback" style="font-size: 2rem; margin-right: 12px; display: none; animation: pulse 2s ease-in-out infinite; color: #ffd700;"></i>
            <div class="d-flex flex-column">
                <span style="font-size: 1.6rem; line-height: 1.2; font-weight: 700;">Electrozot</span>
                <small class="navbar-tagline" style="font-size: 0.85rem; font-weight: 400; font-style: italic; line-height: 1; color: rgba(255, 255, 255, 0.95); letter-spacing: 0.5px;">We Make Perfect</small>
            </div>
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" style="border: 2px solid rgba(255,255,255,0.5); padding: 8px 12px;">
             <span class="navbar-toggler-icon"></span>
         </button>
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
                 <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownBlog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #fff !important; font-weight: 500; font-size: 1rem; padding: 10px 18px !important;">
                         Login Panel
                     </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownBlog" style="border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.15); border-radius: 12px; padding: 10px 0; margin-top: 10px;">
                        <a class="dropdown-item" href="admin/" style="color: #11998e; font-weight: 500;"><i class="fas fa-user-shield"></i> Admin Login</a>
                        <a class="dropdown-item" href="usr/" style="color: #11998e; font-weight: 500;"><i class="fas fa-user"></i> Client Login</a>
                     </div>
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