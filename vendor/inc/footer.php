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
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <?php if(!empty($instagram_url) && $instagram_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener" style="width: 40px; height: 40px; background: #e1306c; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 12px rgba(225, 48, 108, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($facebook_url) && $facebook_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener" style="width: 40px; height: 40px; background: #4267B2; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 12px rgba(66, 103, 178, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>?text=Hi%20Electrozot%2C%20I%20want%20to%20book%20your%20services" target="_blank" rel="noopener" style="width: 40px; height: 40px; background: #25d366; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 12px rgba(37, 211, 102, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="tel:<?php echo htmlspecialchars($primary_phone); ?>" style="width: 40px; height: 40px; background: #63b3ed; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 12px rgba(99, 179, 237, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>
            
            <!-- Brand -->
            <div class="col-6 col-md-3 mb-3">
                <h5 style="font-weight: 700; margin-bottom: 15px; color: #ffffff; font-size: 1rem; letter-spacing: 0.5px; text-transform: uppercase;">Electrozot</h5>
                <?php
                // Check if technician is logged in (session already started in main file)
                $tech_logged_in = isset($_SESSION['t_id']) && !empty($_SESSION['t_id']);
                $tech_link = $tech_logged_in ? 'tech/dashboard.php' : 'tech/index.php';
                $tech_text = $tech_logged_in ? 'Dashboard' : 'Technician';
                ?>
                <a href="<?php echo $tech_link; ?>" style="background: #4a5568; color: white; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 12px 24px; border-radius: 8px; transition: all 0.3s; font-weight: 600; font-size: 0.95rem; border: 2px solid #63b3ed;" onmouseover="this.style.background='#63b3ed'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(99, 179, 237, 0.3)'" onmouseout="this.style.background='#4a5568'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <?php if($tech_logged_in): ?>
                        <i class="fas fa-chart-line" style="font-size: 1rem;"></i>
                    <?php else: ?>
                        <i class="fas fa-tools" style="font-size: 1rem;"></i>
                    <?php endif; ?>
                    <span><?php echo $tech_text; ?></span>
                </a>
            </div>
            
            <!-- Empty space for balance -->
            <div class="col-6 col-md-3 mb-3">
            </div>
        </div>

        <!-- Bottom Bar -->
        <div style="border-top: 1px solid #4a5568; margin-top: 25px; padding-top: 20px; text-align: center;">
            <p style="margin: 0; color: #cbd5e0; font-size: 0.9rem; font-weight: 500;">
                &copy; <?php echo date('Y');?> <span style="color: #ffffff; font-weight: 700;">Electrozot</span> - All rights reserved
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
