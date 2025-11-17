<?php
/**
 * EXAMPLE: How to update index.php to use dynamic contact settings
 * 
 * This file shows the changes needed in index.php
 * Copy the relevant sections to your actual index.php file
 */

// ============================================
// STEP 1: Add at the top of index.php (after config.php include)
// ============================================
?>

<?php
  session_start();
  include('admin/vendor/inc/config.php');
  include('admin/vendor/inc/site-settings-helper.php'); // ADD THIS LINE
  
  // Now you can use settings anywhere in the file
  $primary_phone = get_primary_phone($mysqli);
  $whatsapp = get_whatsapp($mysqli);
  $primary_email = get_primary_email($mysqli);
  $business_name = get_business_name($mysqli);
?>

<!-- ============================================ -->
<!-- STEP 2: Replace hardcoded phone number in hero section -->
<!-- ============================================ -->

<!-- BEFORE (Line ~89): -->
<a href="tel:7559606925" class="feature-badge pulse-animation">
    <i class="fas fa-phone"></i> Call 7559606925
</a>

<!-- AFTER: -->
<a href="<?php echo get_phone_link($mysqli); ?>" class="feature-badge pulse-animation" style="animation-delay: 0.2s; text-decoration: none;" aria-label="Call <?php echo $primary_phone; ?>">
    <i class="fas fa-phone"></i> Call <?php echo $primary_phone; ?>
</a>

<!-- ============================================ -->
<!-- STEP 3: Add WhatsApp button (optional) -->
<!-- ============================================ -->

<a href="<?php echo get_whatsapp_link($mysqli, 'Hi! I want to book a service'); ?>" class="feature-badge pulse-animation" style="animation-delay: 0.4s; text-decoration: none; background: #25D366;" aria-label="WhatsApp <?php echo $whatsapp; ?>">
    <i class="fab fa-whatsapp"></i> WhatsApp <?php echo $whatsapp; ?>
</a>

<!-- ============================================ -->
<!-- STEP 4: If you have a footer with contact info -->
<!-- ============================================ -->

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p><i class="fas fa-phone"></i> <a href="<?php echo get_phone_link($mysqli); ?>"><?php echo format_phone($primary_phone); ?></a></p>
                <p><i class="fas fa-envelope"></i> <a href="<?php echo get_email_link($mysqli); ?>"><?php echo $primary_email; ?></a></p>
                <p><i class="fab fa-whatsapp"></i> <a href="<?php echo get_whatsapp_link($mysqli); ?>"><?php echo format_phone($whatsapp); ?></a></p>
            </div>
            <div class="col-md-4">
                <h5>Follow Us</h5>
                <?php 
                $instagram = get_instagram($mysqli);
                $facebook = get_facebook($mysqli);
                if(!empty($instagram)): 
                ?>
                    <a href="https://instagram.com/<?php echo ltrim($instagram, '@'); ?>" target="_blank">
                        <i class="fab fa-instagram"></i> <?php echo $instagram; ?>
                    </a>
                <?php endif; ?>
                
                <?php if(!empty($facebook)): ?>
                    <a href="<?php echo $facebook; ?>" target="_blank">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <h5>Address</h5>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo get_business_address($mysqli); ?></p>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================ -->
<!-- COMPLETE EXAMPLE: Full integration -->
<!-- ============================================ -->

<!DOCTYPE html>
<html>
<head>
    <title><?php echo get_business_name($mysqli); ?> - <?php echo get_business_tagline($mysqli); ?></title>
</head>
<body>
    
    <!-- Hero Section with Dynamic Contact -->
    <section class="hero">
        <div class="container">
            <h1><?php echo get_business_name($mysqli); ?></h1>
            <p><?php echo get_business_tagline($mysqli); ?></p>
            
            <div class="cta-buttons">
                <a href="#booking-form" class="btn btn-primary">
                    <i class="fas fa-bolt"></i> Book Service
                </a>
                
                <a href="<?php echo get_phone_link($mysqli); ?>" class="btn btn-success">
                    <i class="fas fa-phone"></i> Call <?php echo $primary_phone; ?>
                </a>
                
                <a href="<?php echo get_whatsapp_link($mysqli, 'Hi! I need help with a service'); ?>" class="btn btn-whatsapp" style="background: #25D366; color: white;">
                    <i class="fab fa-whatsapp"></i> WhatsApp Us
                </a>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="contact-info">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="contact-card">
                        <i class="fas fa-phone fa-3x"></i>
                        <h4>Call Us</h4>
                        <a href="<?php echo get_phone_link($mysqli); ?>">
                            <?php echo format_phone($primary_phone); ?>
                        </a>
                        <?php 
                        $secondary_phone = get_secondary_phone($mysqli);
                        if(!empty($secondary_phone)): 
                        ?>
                            <br>
                            <a href="<?php echo get_phone_link($mysqli, 'secondary'); ?>">
                                <?php echo format_phone($secondary_phone); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="contact-card">
                        <i class="fas fa-envelope fa-3x"></i>
                        <h4>Email Us</h4>
                        <a href="<?php echo get_email_link($mysqli, 'primary', 'Service Inquiry'); ?>">
                            <?php echo $primary_email; ?>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="contact-card">
                        <i class="fab fa-whatsapp fa-3x"></i>
                        <h4>WhatsApp</h4>
                        <a href="<?php echo get_whatsapp_link($mysqli); ?>">
                            <?php echo format_phone($whatsapp); ?>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="contact-card">
                        <i class="fas fa-map-marker-alt fa-3x"></i>
                        <h4>Visit Us</h4>
                        <p><?php echo get_business_address($mysqli); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</body>
</html>

<?php
/**
 * QUICK REFERENCE:
 * 
 * Phone Numbers:
 * - get_primary_phone($mysqli)
 * - get_secondary_phone($mysqli)
 * - get_phone_link($mysqli, 'primary') or 'secondary'
 * 
 * Email:
 * - get_primary_email($mysqli)
 * - get_secondary_email($mysqli)
 * - get_email_link($mysqli, 'primary', 'Optional Subject')
 * 
 * WhatsApp:
 * - get_whatsapp($mysqli)
 * - get_whatsapp_link($mysqli, 'Optional pre-filled message')
 * 
 * Social Media:
 * - get_instagram($mysqli)
 * - get_facebook($mysqli)
 * - get_twitter($mysqli)
 * 
 * Business Info:
 * - get_business_name($mysqli)
 * - get_business_tagline($mysqli)
 * - get_business_address($mysqli)
 * 
 * Utilities:
 * - format_phone($phone) - Formats 10-digit number with space
 * - get_setting($mysqli, 'any_key', 'default_value')
 * - get_all_settings($mysqli) - Returns array of all settings
 */
?>
