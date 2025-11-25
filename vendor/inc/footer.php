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
$facebook_url = !empty($settings['facebook_url']) ? $settings['facebook_url'] : '#';
$twitter_url = !empty($settings['twitter_url']) ? $settings['twitter_url'] : '#';
$whatsapp_number = !empty($settings['whatsapp_number']) ? $settings['whatsapp_number'] : '917559606925';
$primary_phone = !empty($settings['primary_phone']) ? $settings['primary_phone'] : '7559606925';
$primary_email = !empty($settings['primary_email']) ? $settings['primary_email'] : 'electrozot.in@gmail.com';
?>
<footer style="background: linear-gradient(135deg, #4a5568 0%, #5a6c7d 100%); color: white; padding: 40px 0 20px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Contact Us -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 style="font-weight: 600; margin-bottom: 25px; color: #87ceeb; font-size: 1.1rem;">Contact Us</h5>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 18px; display: flex; align-items: start; gap: 12px;">
                        <i class="fas fa-map-marker-alt" style="color: #87ceeb; margin-top: 3px; font-size: 1rem;"></i>
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.95rem; line-height: 1.5;">Electrozot, Dharamshala</span>
                    </li>
                    <li style="margin-bottom: 18px;">
                        <a href="tel:<?php echo htmlspecialchars($primary_phone); ?>" style="color: rgba(255,255,255,0.85); text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s;" onmouseover="this.style.color='#87ceeb'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                            <i class="fas fa-phone" style="color: #87ceeb; font-size: 1rem;"></i>
                            <span><?php echo htmlspecialchars($primary_phone); ?></span>
                        </a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="mailto:<?php echo htmlspecialchars($primary_email); ?>" style="color: rgba(255,255,255,0.85); text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s; word-break: break-all;" onmouseover="this.style.color='#87ceeb'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                            <i class="fas fa-envelope" style="color: #87ceeb; font-size: 1rem;"></i>
                            <span><?php echo htmlspecialchars($primary_email); ?></span>
                        </a>
                    </li>
                    <li style="margin-bottom: 18px; display: flex; align-items: start; gap: 12px;">
                        <i class="fas fa-clock" style="color: #87ceeb; margin-top: 3px; font-size: 1rem;"></i>
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.95rem; line-height: 1.5;">Mon - Sun: 7:00 AM - 9:00 PM</span>
                    </li>
                </ul>
            </div>

            <!-- Follow Us -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 style="font-weight: 600; margin-bottom: 25px; color: #87ceeb; font-size: 1.1rem;">Follow Us</h5>
                <p style="color: rgba(255,255,255,0.75); margin-bottom: 25px; font-size: 0.95rem; line-height: 1.6;">Stay connected with us on social media</p>
                <div style="display: flex; gap: 15px;">
                    <?php if(!empty($instagram_url) && $instagram_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(240, 147, 251, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(240, 147, 251, 0.2)'">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($facebook_url) && $facebook_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener" style="width: 50px; height: 50px; background: linear-gradient(135deg, #4267B2 0%, #3b5998 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(66, 103, 178, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(66, 103, 178, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(66, 103, 178, 0.2)'">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>?text=Hi%20Electrozot%2C%20I%20want%20to%20book%20your%20services" target="_blank" rel="noopener" style="width: 50px; height: 50px; background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(37, 211, 102, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(37, 211, 102, 0.2)'">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="tel:<?php echo htmlspecialchars($primary_phone); ?>" style="width: 50px; height: 50px; background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0, 212, 255, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 212, 255, 0.2)'">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 style="font-weight: 600; margin-bottom: 25px; color: #87ceeb; font-size: 1.1rem;">Quick Access</h5>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <a href="tech/index.php" class="footer-login-btn footer-tech-btn" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); color: #4a5568; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 10px; transition: all 0.3s; box-shadow: 0 3px 15px rgba(255, 255, 255, 0.3); font-weight: 600; font-size: 0.9rem; border: 2px solid rgba(255, 255, 255, 0.5);" onmouseover="this.style.transform='translateY(-2px) scale(1.02)'; this.style.boxShadow='0 6px 20px rgba(135, 206, 235, 0.5)'; this.style.background='linear-gradient(135deg, #87ceeb 0%, #4facfe 100%)'; this.style.color='#000'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 3px 15px rgba(255, 255, 255, 0.3)'; this.style.background='linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)'; this.style.color='#4a5568'">
                        <i class="fas fa-user-cog" style="font-size: 1rem;"></i>
                        <span>Technician</span>
                    </a>
                    
                    <a href="admin/index.php" class="footer-login-btn footer-admin-btn" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); color: #4a5568; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 10px; transition: all 0.3s; box-shadow: 0 3px 15px rgba(255, 255, 255, 0.3); font-weight: 600; font-size: 0.9rem; border: 2px solid rgba(255, 255, 255, 0.5);" onmouseover="this.style.transform='translateY(-2px) scale(1.02)'; this.style.boxShadow='0 6px 20px rgba(135, 206, 235, 0.5)'; this.style.background='linear-gradient(135deg, #87ceeb 0%, #4facfe 100%)'; this.style.color='#000'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 3px 15px rgba(255, 255, 255, 0.3)'; this.style.background='linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)'; this.style.color='#4a5568'">
                        <i class="fas fa-user-shield" style="font-size: 1rem;"></i>
                        <span>Admin</span>
                    </a>
                    
                    
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 35px; padding-top: 20px; text-align: center;">
            <p style="margin: 0; color: rgba(255,255,255,0.5); font-size: 0.9rem;">
                Copyright &copy; <?php echo date('Y');?> Electrozot. All rights reserved.
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
  });
</script>