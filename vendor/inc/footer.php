<?php
// Load site settings for footer
$settings = [];
$settings_query = "SELECT setting_key, setting_value FROM tms_site_settings";
$settings_result = $mysqli->query($settings_query);
if($settings_result) {
    while($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Get social media URLs with defaults
$instagram_url = !empty($settings['instagram_url']) ? $settings['instagram_url'] : 'https://www.instagram.com/electrozot.in/';
$facebook_url = !empty($settings['facebook_url']) ? $settings['facebook_url'] : 'https://www.facebook.com/electrozot';
$twitter_url = !empty($settings['twitter_url']) ? $settings['twitter_url'] : 'https://twitter.com/electrozot_in';
$youtube_url = !empty($settings['youtube_url']) ? $settings['youtube_url'] : 'https://youtube.com/@electrozot_ez?si=UAyPrmU33S28VLlO';
$linkedin_url = !empty($settings['linkedin_url']) ? $settings['linkedin_url'] : 'https://www.linkedin.com/in/electrozot';
$whatsapp_number = !empty($settings['whatsapp_number']) ? $settings['whatsapp_number'] : '917559606925';
$primary_phone = !empty($settings['primary_phone']) ? $settings['primary_phone'] : '7559606925';
$primary_email = !empty($settings['primary_email']) ? $settings['primary_email'] : 'electrozot.in@gmail.com';
?>
<footer style="background: #2d3748; color: white; padding: 40px 0 15px 0; border-top: 3px solid #4a5568;">
    <div class="container">
        <div class="row align-items-start">
            <!-- Contact -->
            <div class="col-6 col-md-3 mb-3">
                <h5 style="font-weight: 700; margin-bottom: 15px; color: #ffffff; font-size: 1rem; letter-spacing: 0.5px; text-transform: uppercase;">Contact</h5>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="tel:<?php echo htmlspecialchars($primary_phone); ?>" style="color: #cbd5e0; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: color 0.3s; font-size: 0.9rem;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fas fa-phone" style="color: #63b3ed; font-size: 0.85rem;"></i>
                            <?php echo htmlspecialchars($primary_phone); ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="mailto:<?php echo htmlspecialchars($primary_email); ?>" style="color: #cbd5e0; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: color 0.3s; font-size: 0.9rem; word-break: break-word;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fas fa-envelope" style="color: #63b3ed; font-size: 0.85rem;"></i>
                            <?php echo htmlspecialchars($primary_email); ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 0;">
                        <span style="color: #cbd5e0; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;">
                            <i class="fas fa-clock" style="color: #63b3ed; font-size: 0.85rem;"></i>
                            7:00 AM - 9:00 PM
                        </span>
                    </li>
                </ul>
            </div>
            
            <!-- Social Icons -->
            <div class="col-6 col-md-3 mb-3">
                <h5 style="font-weight: 700; margin-bottom: 15px; color: #ffffff; font-size: 1rem; letter-spacing: 0.5px; text-transform: uppercase;">Social</h5>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <?php if(!empty($instagram_url) && $instagram_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener" style="width: 35px; height: 35px; background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 3px 8px rgba(225, 48, 108, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($youtube_url) && $youtube_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" rel="noopener" style="width: 35px; height: 35px; background: #FF0000; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 3px 8px rgba(255, 0, 0, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($facebook_url) && $facebook_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener" style="width: 35px; height: 35px; background: #4267B2; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 3px 8px rgba(66, 103, 178, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($twitter_url) && $twitter_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" rel="noopener" style="width: 35px; height: 35px; background: #1DA1F2; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 3px 8px rgba(29, 161, 242, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($linkedin_url) && $linkedin_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($linkedin_url); ?>" target="_blank" rel="noopener" style="width: 35px; height: 35px; background: #0077B5; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 3px 8px rgba(0, 119, 181, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>?text=Hi%20Electrozot%2C%20I%20want%20to%20book%20your%20services" target="_blank" rel="noopener" style="width: 35px; height: 35px; background: #25d366; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 3px 8px rgba(37, 211, 102, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            
            <!-- Brand & Download App - Side by Side on Mobile -->
            <div class="col-12 col-md-6 mb-3">
                <div class="row">
                    <div class="col-6 col-md-6">
                        <h5 style="font-weight: 700; margin-bottom: 15px; color: #ffffff; font-size: 1rem; letter-spacing: 0.5px; text-transform: uppercase;">Electrozot</h5>
                        <?php
                        // Check if technician is logged in (session already started in main file)
                        $tech_logged_in = isset($_SESSION['t_id']) && !empty($_SESSION['t_id']);
                        $tech_link = $tech_logged_in ? 'tech/dashboard.php' : 'tech/index.php';
                        $tech_text = $tech_logged_in ? 'Dashboard' : 'Technician';
                        ?>
                        <a href="<?php echo $tech_link; ?>" style="background: #4a5568; color: white; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border-radius: 8px; transition: all 0.3s; font-weight: 600; font-size: 0.85rem; border: 2px solid #63b3ed;" onmouseover="this.style.background='#63b3ed'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(99, 179, 237, 0.3)'" onmouseout="this.style.background='#4a5568'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <?php if($tech_logged_in): ?>
                                <i class="fas fa-chart-line" style="font-size: 0.9rem;"></i>
                            <?php else: ?>
                                <i class="fas fa-tools" style="font-size: 0.9rem;"></i>
                            <?php endif; ?>
                            <span><?php echo $tech_text; ?></span>
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-6">
                        <h5 style="font-weight: 700; margin-bottom: 15px; color: #ffffff; font-size: 1rem; letter-spacing: 0.5px; text-transform: uppercase;">Get App</h5>
                        <button id="pwa-install-footer-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border-radius: 8px; transition: all 0.3s; font-weight: 600; font-size: 0.85rem; border: 2px solid rgba(255,255,255,0.3); cursor: pointer;" onmouseover="this.style.background='linear-gradient(135deg, #764ba2 0%, #667eea 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)'" onmouseout="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fas fa-download" style="font-size: 0.9rem;"></i>
                            <span>Download</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div style="border-top: 1px solid #4a5568; margin-top: 25px; padding-top: 20px; text-align: center;">
            <div style="margin-bottom: 10px;">
                <a href="privacy-policy.php" style="color: #cbd5e0; text-decoration: none; font-size: 0.85rem; margin: 0 15px; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#cbd5e0'">
                    <i class="fas fa-shield-alt" style="margin-right: 5px;"></i>Privacy Policy
                </a>
                <a href="contact.php" style="color: #cbd5e0; text-decoration: none; font-size: 0.85rem; margin: 0 15px; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#cbd5e0'">
                    <i class="fas fa-envelope" style="margin-right: 5px;"></i>Contact Us
                </a>
                <a href="sitemap.xml" style="color: #cbd5e0; text-decoration: none; font-size: 0.85rem; margin: 0 15px; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#cbd5e0'">
                    <i class="fas fa-sitemap" style="margin-right: 5px;"></i>Sitemap
                </a>
            </div>
            <p style="margin: 0; color: #cbd5e0; font-size: 0.9rem; font-weight: 500;">
                &copy; <?php echo date('Y');?> <span style="color: #ffffff; font-weight: 700;">Electrozot</span> - All rights reserved
<span style="margin-left: 10px; color: #a0aec0; font-size: 0.8rem;">v4.4.5</span>
                <span style="margin-left: 10px; color: #a0aec0; font-size: 0.8rem;">v4.4.3</span>
            </p>
        </div>
    </div>
</footer>
<!-- Floating WhatsApp Chat Button -->
<a
  href="https://wa.me/917559606925?text=Hi%20Electrozot%2C%20I%20want%20to%20book%20your%20services"
  class="whatsapp-chat-btn"
  target="_blank"
  rel="noopener"
  aria-label="Chat on WhatsApp"
  title="Chat on WhatsApp"
>
  <i class="fab fa-whatsapp" aria-hidden="true"></i>
</a>
<!-- Floating Book Service Button -->
<?php if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php'): ?>
<a
  href="index.php#booking-form"
  class="book-service-fab"
  aria-label="Book Service"
  title="Book Service"
>
  <i class="fas fa-bolt" aria-hidden="true"></i>
  <span>Book</span>
</a>
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var bookFab = document.querySelector('.book-service-fab');
    var bookingAnchor = document.getElementById('booking-form');
    // If on home page and booking form exists, smooth scroll instead of hard navigation
    if (bookFab && bookingAnchor && /index\.php$/.test(window.location.pathname)) {
      bookFab.addEventListener('click', function(e) {
        e.preventDefault();
        bookingAnchor.scrollIntoView({ behavior: 'smooth', block: 'center' });
        var nameInput = document.querySelector('#booking-form input[name="customer_name"]');
        setTimeout(function() { if (nameInput) { nameInput.focus(); } }, 400);
      });
    }
    
    // Manual install guide function
    function showManualInstallGuide() {
      const userAgent = navigator.userAgent.toLowerCase();
      let instructions = '';
      
      if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
        instructions = 'Chrome:\n1. Look for install icon (⊕) in address bar\n2. Or Menu (⋮) > Install ElectroZot\n3. Click Install';
      } else if (userAgent.includes('safari')) {
        instructions = 'Safari:\n1. Tap Share button (□↗)\n2. Select "Add to Home Screen"\n3. Tap Add';
      } else if (userAgent.includes('firefox')) {
        instructions = 'Firefox:\n1. Look for install banner\n2. Or Menu > Install\n3. Follow prompts';
      } else {
        instructions = 'Look for "Install" or "Add to Home Screen" option in your browser menu';
      }
      
      alert('📱 Install ElectroZot App\n\n' + instructions + '\n\n💡 Tip: Use HTTPS and browse site for 2-3 minutes first');
    }
    
    // Footer PWA Install Button Handler
    var footerInstallBtn = document.getElementById('pwa-install-footer-btn');
    if (footerInstallBtn) {
      // Check if deferredPrompt exists (set in index.php)
      window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        window.deferredPrompt = e;
        footerInstallBtn.style.display = 'inline-flex';
      });
      
      footerInstallBtn.addEventListener('click', async () => {
        // Use the enhanced PWA installer
        if (window.PWAInstaller && window.PWAInstaller.install) {
          window.PWAInstaller.install();
        } else if (window.deferredPrompt) {
          try {
            await window.deferredPrompt.prompt();
            const { outcome } = await window.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
              footerInstallBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Installed</span>';
              footerInstallBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            }
            window.deferredPrompt = null;
          } catch (error) {
            console.error('Footer install error:', error);
            showManualInstallGuide();
          }
        } else {
          // Show enhanced manual guide
          const userAgent = navigator.userAgent.toLowerCase();
          let instructions = '';
          
          if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
            instructions = 'Chrome: Look for install icon (⊕) in address bar or Menu > Install ElectroZot';
          } else if (userAgent.includes('safari')) {
            instructions = 'Safari: Tap Share button (□↗) > Add to Home Screen';
          } else if (userAgent.includes('firefox')) {
            instructions = 'Firefox: Look for install prompt or Menu > Install';
          } else {
            instructions = 'Look for "Install" or "Add to Home Screen" in your browser menu';
          }
          
          alert('📱 Install ElectroZot App\n\n' + instructions + '\n\n💡 Make sure you\'re using HTTPS and browse for a few minutes first.');
        }
      });
      
      // Check if already installed
      if (window.matchMedia('(display-mode: standalone)').matches) {
        footerInstallBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Installed</span>';
        footerInstallBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
      }
    }
  });
</script>
