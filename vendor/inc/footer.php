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
<footer style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: white; padding: 30px 0 10px 0; border-top: 4px solid transparent; border-image: linear-gradient(90deg, #8E2DE2, #4A00E0, #FF6B9D, #FFA07A, #FFD700, #00D4FF) 1; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row align-items-start">
            <!-- Contact -->
            <div class="col-6 col-md-3 mb-2">
                <h5 style="font-weight: 700; margin-bottom: 15px; background: linear-gradient(90deg, #8E2DE2, #FF6B9D); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 1rem; letter-spacing: 1px;">CONTACT</h5>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 8px;">
                        <a href="tel:<?php echo htmlspecialchars($primary_phone); ?>" style="color: rgba(255,255,255,0.85); text-decoration: none; display: flex; align-items: center; gap: 6px; transition: all 0.3s; font-size: 0.8rem;" onmouseover="this.style.color='#F9A8D4'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                            <i class="fas fa-phone" style="color: #F9A8D4; font-size: 0.75rem;"></i>
                            <?php echo htmlspecialchars($primary_phone); ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="mailto:<?php echo htmlspecialchars($primary_email); ?>" style="color: rgba(255,255,255,0.85); text-decoration: none; display: flex; align-items: center; gap: 6px; transition: all 0.3s; font-size: 0.8rem; word-break: break-word;" onmouseover="this.style.color='#F9A8D4'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                            <i class="fas fa-envelope" style="color: #F9A8D4; font-size: 0.75rem;"></i>
                            <?php echo htmlspecialchars($primary_email); ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 0;">
                        <span style="color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 6px; font-size: 0.8rem;">
                            <i class="fas fa-clock" style="color: #F9A8D4; font-size: 0.75rem;"></i>
                            7:00 AM - 9:00 PM
                        </span>
                    </li>
                </ul>
            </div>
            
            <!-- Social Icons -->
            <div class="col-6 col-md-3 mb-2">
                <h5 style="font-weight: 700; margin-bottom: 15px; background: linear-gradient(90deg, #4A00E0, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 1rem; letter-spacing: 1px;">SOCIAL</h5>
                <div style="display: flex; gap: 18px; flex-wrap: wrap;">
                    <?php if(!empty($instagram_url) && $instagram_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener" style="width: 38px; height: 38px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(240, 147, 251, 0.3);" onmouseover="this.style.transform='translateY(-2px) scale(1.05)'" onmouseout="this.style.transform='translateY(0) scale(1)'">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($facebook_url) && $facebook_url != '#'): ?>
                    <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener" style="width: 38px; height: 38px; background: linear-gradient(135deg, #4267B2 0%, #3b5998 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(66, 103, 178, 0.3);" onmouseover="this.style.transform='translateY(-2px) scale(1.05)'" onmouseout="this.style.transform='translateY(0) scale(1)'">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>?text=Hi%20Electrozot%2C%20I%20want%20to%20book%20your%20services" target="_blank" rel="noopener" style="width: 38px; height: 38px; background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);" onmouseover="this.style.transform='translateY(-2px) scale(1.05)'" onmouseout="this.style.transform='translateY(0) scale(1)'">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="tel:<?php echo htmlspecialchars($primary_phone); ?>" style="width: 38px; height: 38px; background: linear-gradient(135deg, #A78BFA 0%, #EC4899 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(167, 139, 250, 0.3);" onmouseover="this.style.transform='translateY(-2px) scale(1.05)'" onmouseout="this.style.transform='translateY(0) scale(1)'">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>
            
            <!-- Brand -->
            <div class="col-6 col-md-3 mb-2">
                <h5 style="font-weight: 700; margin-bottom: 15px; background: linear-gradient(90deg, #FFD700, #FFA07A); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 1rem; letter-spacing: 1px;">ELECTROZOT</h5>
                <?php
                // Check if technician is logged in (session already started in main file)
                $tech_logged_in = isset($_SESSION['t_id']) && !empty($_SESSION['t_id']);
                $tech_link = $tech_logged_in ? 'tech/dashboard.php' : 'tech/index.php';
                $tech_text = $tech_logged_in ? 'Dashboard' : 'Technician';
                ?>
                <a href="<?php echo $tech_link; ?>" style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.25) 0%, rgba(236, 72, 153, 0.25) 100%); color: white; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 14px 24px; border-radius: 12px; transition: all 0.3s; font-weight: 700; font-size: 0.95rem; border: 2px solid rgba(167, 139, 250, 0.5); box-shadow: 0 4px 15px rgba(167, 139, 250, 0.2);" onmouseover="this.style.background='linear-gradient(135deg, rgba(167, 139, 250, 0.4) 0%, rgba(236, 72, 153, 0.4) 100%)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(167, 139, 250, 0.4)'; this.style.borderColor='#A78BFA'" onmouseout="this.style.background='linear-gradient(135deg, rgba(167, 139, 250, 0.25) 0%, rgba(236, 72, 153, 0.25) 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(167, 139, 250, 0.2)'; this.style.borderColor='rgba(167, 139, 250, 0.5)'">
                    <?php if($tech_logged_in): ?>
                        <i class="fas fa-chart-line" style="font-size: 1.1rem; color: #A78BFA;"></i>
                    <?php else: ?>
                        <i class="fas fa-tools" style="font-size: 1.1rem; color: #A78BFA;"></i>
                    <?php endif; ?>
                    <span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);"><?php echo $tech_text; ?></span>
                </a>
            </div>
            
            <!-- Empty space for balance -->
            <div class="col-6 col-md-3 mb-2">
            </div>
        </div>

        <!-- Bottom Bar -->
        <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: 20px; padding-top: 15px; text-align: center;">
            <p style="margin: 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; font-weight: 500;">
                &copy; <?php echo date('Y');?> <span style="background: linear-gradient(90deg, #FF6B9D, #FFD700); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;">Electrozot</span> - All rights reserved
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
