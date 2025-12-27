<?php
/**
 * Sitemap Generator Script for ElectroZot
 * Generates static sitemap.xml file with dynamic blog posts
 * Can be run manually or via cron job
 */

// Include database configuration
include('config.php');

// Function to generate sitemap content
function generateSitemapContent($mysqli) {
    $sitemap_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $sitemap_content .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
    $sitemap_content .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
    $sitemap_content .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n\n";

    // Static pages
    $static_pages = [
        // Main pages
        ['url' => 'https://electrozot.in/', 'priority' => '1.0', 'changefreq' => 'weekly', 'comment' => 'Homepage - Main landing page'],
        ['url' => 'https://electrozot.in/services', 'priority' => '0.9', 'changefreq' => 'weekly', 'comment' => 'Services - Main service offerings (HIGH SEO VALUE)'],
        ['url' => 'https://electrozot.in/about', 'priority' => '0.8', 'changefreq' => 'monthly', 'comment' => 'About Us - Company information and team'],
        ['url' => 'https://electrozot.in/contact', 'priority' => '0.8', 'changefreq' => 'monthly', 'comment' => 'Contact - Contact information and inquiry form'],
        ['url' => 'https://electrozot.in/faq', 'priority' => '0.8', 'changefreq' => 'monthly', 'comment' => 'FAQ - Frequently Asked Questions (HIGH SEO VALUE)'],
        
        // Content pages
        ['url' => 'https://electrozot.in/gallery', 'priority' => '0.7', 'changefreq' => 'weekly', 'comment' => 'Gallery - Work showcase and portfolio'],
        ['url' => 'https://electrozot.in/blog', 'priority' => '0.7', 'changefreq' => 'weekly', 'comment' => 'Blog - Articles, tips, and company updates (HIGH SEO VALUE)'],
        
        // Booking pages
        ['url' => 'https://electrozot.in/process-guest-booking', 'priority' => '0.8', 'changefreq' => 'weekly', 'comment' => 'Guest Booking - Public service booking (HIGH CONVERSION VALUE)'],
        
        // PWA pages
        ['url' => 'https://electrozot.in/offline', 'priority' => '0.3', 'changefreq' => 'monthly', 'comment' => 'Offline Page - PWA offline functionality'],
        ['url' => 'https://electrozot.in/splash', 'priority' => '0.2', 'changefreq' => 'monthly', 'comment' => 'Splash Page - PWA splash screen'],
        
        // Legal pages
        ['url' => 'https://electrozot.in/privacy-policy', 'priority' => '0.4', 'changefreq' => 'monthly', 'comment' => 'Privacy Policy - Legal compliance (SEO IMPORTANT)'],
        
        // Service pages
        ['url' => 'https://electrozot.in/service/electrical-services', 'priority' => '0.9', 'changefreq' => 'monthly', 'comment' => 'Electrical Services - Individual service page'],
        ['url' => 'https://electrozot.in/service/repair-services', 'priority' => '0.9', 'changefreq' => 'monthly', 'comment' => 'Repair Services - Individual service page'],
        ['url' => 'https://electrozot.in/service/installation-services', 'priority' => '0.9', 'changefreq' => 'monthly', 'comment' => 'Installation Services - Individual service page'],
        ['url' => 'https://electrozot.in/service/maintenance-services', 'priority' => '0.9', 'changefreq' => 'monthly', 'comment' => 'Maintenance Services - Individual service page'],
        ['url' => 'https://electrozot.in/service/plumbing-services', 'priority' => '0.9', 'changefreq' => 'monthly', 'comment' => 'Plumbing Services - Individual service page'],
        
        // Legacy service URLs
        ['url' => 'https://electrozot.in/electrical-services', 'priority' => '0.7', 'changefreq' => 'monthly', 'comment' => 'Legacy electrical services URL'],
        ['url' => 'https://electrozot.in/appliance-repair', 'priority' => '0.7', 'changefreq' => 'monthly', 'comment' => 'Legacy appliance repair URL'],
        ['url' => 'https://electrozot.in/home-automation', 'priority' => '0.7', 'changefreq' => 'monthly', 'comment' => 'Legacy home automation URL'],
        ['url' => 'https://electrozot.in/emergency-electrical-service', 'priority' => '0.8', 'changefreq' => 'monthly', 'comment' => 'Legacy emergency service URL'],
        
        // Location-based pages
        ['url' => 'https://electrozot.in/electrician-near-me', 'priority' => '0.8', 'changefreq' => 'monthly', 'comment' => 'Location-based SEO page'],
        ['url' => 'https://electrozot.in/electrical-repair-services', 'priority' => '0.7', 'changefreq' => 'monthly', 'comment' => 'Location-based SEO page'],
    ];

    // Add static pages
    $current_section = '';
    foreach ($static_pages as $page) {
        // Determine section based on priority and URL
        if (strpos($page['url'], '/service/') !== false && $current_section !== 'SERVICE_PAGES') {
            $sitemap_content .= "    <!-- ============================================ -->\n";
            $sitemap_content .= "    <!-- SERVICE CATEGORY PAGES (SEO LANDING PAGES) -->\n";
            $sitemap_content .= "    <!-- ============================================ -->\n\n";
            $current_section = 'SERVICE_PAGES';
        } elseif ($page['priority'] == '1.0' && $current_section !== 'MAIN_PAGES') {
            $sitemap_content .= "    <!-- ============================================ -->\n";
            $sitemap_content .= "    <!-- MAIN PAGES (HIGH PRIORITY) -->\n";
            $sitemap_content .= "    <!-- ============================================ -->\n\n";
            $current_section = 'MAIN_PAGES';
        } elseif ($page['priority'] == '0.7' && strpos($page['url'], 'blog') !== false && $current_section !== 'CONTENT_PAGES') {
            $sitemap_content .= "    <!-- ============================================ -->\n";
            $sitemap_content .= "    <!-- CONTENT PAGES -->\n";
            $sitemap_content .= "    <!-- ============================================ -->\n\n";
            $current_section = 'CONTENT_PAGES';
        }

        $sitemap_content .= "    <!-- {$page['comment']} -->\n";
        $sitemap_content .= "    <url>\n";
        $sitemap_content .= "        <loc>{$page['url']}</loc>\n";
        $sitemap_content .= "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $sitemap_content .= "        <changefreq>{$page['changefreq']}</changefreq>\n";
        $sitemap_content .= "        <priority>{$page['priority']}</priority>\n";
        $sitemap_content .= "    </url>\n\n";
    }

    // Add dynamic blog posts section
    $sitemap_content .= "    <!-- ============================================ -->\n";
    $sitemap_content .= "    <!-- DYNAMIC BLOG POSTS (AUTO-GENERATED) -->\n";
    $sitemap_content .= "    <!-- ============================================ -->\n\n";

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
            $clean_title = htmlspecialchars($blog['blog_title']);
            
            $sitemap_content .= "    <!-- Blog Post: {$clean_title} -->\n";
            $sitemap_content .= "    <url>\n";
            $sitemap_content .= "        <loc>https://electrozot.in/blog-post?id={$blog_id}&amp;slug={$clean_slug}</loc>\n";
            $sitemap_content .= "        <lastmod>{$lastmod_formatted}</lastmod>\n";
            $sitemap_content .= "        <changefreq>monthly</changefreq>\n";
            $sitemap_content .= "        <priority>0.6</priority>\n";
            $sitemap_content .= "    </url>\n\n";
        }
    } else {
        $sitemap_content .= "    <!-- No published blog posts found -->\n\n";
    }

    $sitemap_content .= "</urlset>\n";
    
    return $sitemap_content;
}

// Generate sitemap content
$sitemap_content = generateSitemapContent($mysqli);

// Write to sitemap.xml file
$sitemap_file = __DIR__ . '/sitemap.xml';
$result = file_put_contents($sitemap_file, $sitemap_content);

// Output result
if (php_sapi_name() === 'cli') {
    // Command line output
    if ($result !== false) {
        echo "✅ Sitemap generated successfully!\n";
        echo "📁 File: {$sitemap_file}\n";
        echo "📊 Size: " . number_format(strlen($sitemap_content)) . " bytes\n";
        
        // Count blog posts
        $blog_count_query = "SELECT COUNT(*) as count FROM tms_blog_posts WHERE blog_status = 'Published'";
        $blog_count_result = $mysqli->query($blog_count_query);
        $blog_count = $blog_count_result ? $blog_count_result->fetch_assoc()['count'] : 0;
        echo "📝 Blog posts included: {$blog_count}\n";
        echo "🕒 Generated at: " . date('Y-m-d H:i:s') . "\n";
    } else {
        echo "❌ Failed to generate sitemap!\n";
        exit(1);
    }
} else {
    // Web output
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html>\n<html>\n<head>\n<title>Sitemap Generator - ElectroZot</title>\n";
    echo "<style>body{font-family:Arial,sans-serif;margin:40px;background:#f5f5f5;} .container{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;} pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;}</style>\n";
    echo "</head>\n<body>\n<div class='container'>\n";
    echo "<h1>🗺️ Sitemap Generator</h1>\n";
    
    if ($result !== false) {
        echo "<p class='success'>✅ <strong>Sitemap generated successfully!</strong></p>\n";
        echo "<p><strong>File:</strong> {$sitemap_file}</p>\n";
        echo "<p><strong>Size:</strong> " . number_format(strlen($sitemap_content)) . " bytes</p>\n";
        
        // Count blog posts
        $blog_count_query = "SELECT COUNT(*) as count FROM tms_blog_posts WHERE blog_status = 'Published'";
        $blog_count_result = $mysqli->query($blog_count_query);
        $blog_count = $blog_count_result ? $blog_count_result->fetch_assoc()['count'] : 0;
        echo "<p><strong>Blog posts included:</strong> {$blog_count}</p>\n";
        echo "<p><strong>Generated at:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
        
        echo "<h3>🔗 Quick Links</h3>\n";
        echo "<p>\n";
        echo "<a href='sitemap.xml' target='_blank' style='background:#007bff;color:white;padding:8px 16px;text-decoration:none;border-radius:4px;margin-right:10px;'>View Sitemap</a>\n";
        echo "<a href='sitemap-dynamic.php' target='_blank' style='background:#28a745;color:white;padding:8px 16px;text-decoration:none;border-radius:4px;margin-right:10px;'>Dynamic Sitemap</a>\n";
        echo "<a href='admin/admin-manage-blog.php' style='background:#6c757d;color:white;padding:8px 16px;text-decoration:none;border-radius:4px;'>Manage Blog</a>\n";
        echo "</p>\n";
        
    } else {
        echo "<p class='error'>❌ <strong>Failed to generate sitemap!</strong></p>\n";
        echo "<p>Please check file permissions and try again.</p>\n";
    }
    
    echo "</div>\n</body>\n</html>\n";
}

// Close database connection
$mysqli->close();
?>