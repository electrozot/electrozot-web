<?php
/**
 * Dynamic Sitemap Generator for ElectroZot
 * Automatically includes all published blog posts and static pages
 */

// Include database configuration
include('config.php');

// Set content type to XML
header('Content-Type: application/xml; charset=utf-8');

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    <!-- ============================================ -->
    <!-- MAIN PAGES (HIGH PRIORITY) -->
    <!-- ============================================ -->
    
    <!-- Homepage - Main landing page -->
    <url>
        <loc>https://electrozot.in/</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Services - Main service offerings (HIGH SEO VALUE) -->
    <url>
        <loc>https://electrozot.in/services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <!-- About Us - Company information and team -->
    <url>
        <loc>https://electrozot.in/about</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- Contact - Contact information and inquiry form -->
    <url>
        <loc>https://electrozot.in/contact</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- FAQ - Frequently Asked Questions (HIGH SEO VALUE) -->
    <url>
        <loc>https://electrozot.in/faq</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- ============================================ -->
    <!-- CONTENT PAGES -->
    <!-- ============================================ -->
    
    <!-- Gallery - Work showcase and portfolio -->
    <url>
        <loc>https://electrozot.in/gallery</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <!-- Blog - Articles, tips, and company updates (HIGH SEO VALUE) -->
    <url>
        <loc>https://electrozot.in/blog</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- ============================================ -->
    <!-- DYNAMIC BLOG POSTS (AUTO-GENERATED) -->
    <!-- ============================================ -->
    
    <?php
    // Get all published blog posts
    $blog_query = "SELECT blog_id, blog_slug, blog_title, blog_published_at, blog_updated_at 
                   FROM tms_blog_posts 
                   WHERE blog_status = 'Published' 
                   ORDER BY blog_published_at DESC";
    
    $blog_result = $mysqli->query($blog_query);
    
    if ($blog_result && $blog_result->num_rows > 0) {
        while ($blog = $blog_result->fetch_assoc()) {
            // Use updated date if available, otherwise published date
            $lastmod = !empty($blog['blog_updated_at']) ? $blog['blog_updated_at'] : $blog['blog_published_at'];
            $lastmod_formatted = date('Y-m-d', strtotime($lastmod));
            
            // Clean the slug for URL
            $clean_slug = htmlspecialchars($blog['blog_slug']);
            $blog_id = intval($blog['blog_id']);
            
            echo "    <!-- Blog Post: " . htmlspecialchars($blog['blog_title']) . " -->\n";
            echo "    <url>\n";
            echo "        <loc>https://electrozot.in/blog-post?id={$blog_id}&amp;slug={$clean_slug}</loc>\n";
            echo "        <lastmod>{$lastmod_formatted}</lastmod>\n";
            echo "        <changefreq>monthly</changefreq>\n";
            echo "        <priority>0.6</priority>\n";
            echo "    </url>\n\n";
        }
    }
    ?>

    <!-- ============================================ -->
    <!-- BOOKING & CONVERSION PAGES -->
    <!-- ============================================ -->
    
    <!-- Guest Booking - Public service booking (HIGH CONVERSION VALUE) -->
    <url>
        <loc>https://electrozot.in/process-guest-booking</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- ============================================ -->
    <!-- PWA & UTILITY PAGES -->
    <!-- ============================================ -->
    
    <!-- Offline Page - PWA offline functionality -->
    <url>
        <loc>https://electrozot.in/offline</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    
    <!-- Splash Page - PWA splash screen -->
    <url>
        <loc>https://electrozot.in/splash</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.2</priority>
    </url>

    <!-- ============================================ -->
    <!-- LEGAL & COMPLIANCE PAGES -->
    <!-- ============================================ -->
    
    <!-- Privacy Policy - Legal compliance (SEO IMPORTANT) -->
    <url>
        <loc>https://electrozot.in/privacy-policy</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>

    <!-- ============================================ -->
    <!-- SERVICE CATEGORY PAGES (SEO LANDING PAGES) -->
    <!-- ============================================ -->
    
    <!-- Individual Service Pages - High SEO Value -->
    <url>
        <loc>https://electrozot.in/service/electrical-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/service/repair-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/service/installation-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/service/maintenance-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/service/plumbing-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <!-- Legacy Service URLs (for backward compatibility) -->
    <url>
        <loc>https://electrozot.in/electrical-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/appliance-repair</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/home-automation</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/emergency-electrical-service</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- ============================================ -->
    <!-- LOCATION-BASED SEO PAGES -->
    <!-- ============================================ -->
    
    <url>
        <loc>https://electrozot.in/electrician-near-me</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc>https://electrozot.in/electrical-repair-services</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

</urlset>