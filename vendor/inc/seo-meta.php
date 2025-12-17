<?php
// Dynamic SEO Meta Tags with Open Graph Support
// This file should be included after setting $seo_title, $seo_description, etc.

// Get current page info
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$base_url = 'https://electrozot.in';
$current_url = $base_url . $_SERVER['REQUEST_URI'];

// Default values if not set
if (!isset($seo_title)) {
    $seo_title = 'Electrozot - Professional Electrical & Technical Services | We Make Perfect';
}
if (!isset($seo_description)) {
    $seo_description = 'Book certified electricians and technicians in your area. Expert electrical repairs, appliance services, wiring, and home automation. Available 24/7 with 30-day warranty.';
}
if (!isset($seo_keywords)) {
    $seo_keywords = 'electrician, electrical services, appliance repair, home automation, wiring, electrical repair, technician booking, emergency electrician';
}
if (!isset($seo_image)) {
    $seo_image = $base_url . '/vendor/EZlogonew.png';
}
if (!isset($seo_type)) {
    $seo_type = 'website';
}
if (!isset($canonical_url)) {
    $canonical_url = $current_url;
}

// Additional Open Graph properties for articles
$og_article_author = isset($og_article_author) ? $og_article_author : 'Electrozot';
$og_article_published_time = isset($og_article_published_time) ? $og_article_published_time : '';
$og_article_modified_time = isset($og_article_modified_time) ? $og_article_modified_time : '';
$og_article_section = isset($og_article_section) ? $og_article_section : '';
$og_article_tag = isset($og_article_tag) ? $og_article_tag : '';

// Business information
$business_name = 'Electrozot';
$business_phone = '+917559606925';
$business_email = 'electrozot@outlook.com';
?>

<!-- Enhanced SEO Meta Tags -->
<title><?php echo htmlspecialchars($seo_title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>">
<meta name="author" content="<?php echo htmlspecialchars($og_article_author); ?>">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<link rel="canonical" href="<?php echo $canonical_url; ?>">

<!-- Open Graph Meta Tags (Facebook, LinkedIn, WhatsApp) -->
<meta property="og:type" content="<?php echo $seo_type; ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($seo_description); ?>">
<meta property="og:image" content="<?php echo $seo_image; ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta property="og:url" content="<?php echo $canonical_url; ?>">
<meta property="og:site_name" content="<?php echo $business_name; ?>">
<meta property="og:locale" content="en_IN">

<?php if ($seo_type === 'article' && $og_article_published_time): ?>
<!-- Article-specific Open Graph tags -->
<meta property="article:author" content="<?php echo htmlspecialchars($og_article_author); ?>">
<meta property="article:published_time" content="<?php echo $og_article_published_time; ?>">
<?php if ($og_article_modified_time): ?>
<meta property="article:modified_time" content="<?php echo $og_article_modified_time; ?>">
<?php endif; ?>
<?php if ($og_article_section): ?>
<meta property="article:section" content="<?php echo htmlspecialchars($og_article_section); ?>">
<?php endif; ?>
<?php if ($og_article_tag): ?>
<?php 
$tags = explode(',', $og_article_tag);
foreach($tags as $tag): 
?>
<meta property="article:tag" content="<?php echo htmlspecialchars(trim($tag)); ?>">
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($seo_description); ?>">
<meta name="twitter:image" content="<?php echo $seo_image; ?>">
<meta name="twitter:image:alt" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta name="twitter:site" content="@electrozot">
<meta name="twitter:creator" content="@electrozot">

<!-- WhatsApp specific (uses Open Graph) -->
<meta property="og:image:type" content="image/png">

<!-- Business/Local SEO -->
<meta name="geo.region" content="IN">
<meta name="geo.placename" content="India">
<meta name="geo.position" content="20.5937;78.9629">
<meta name="ICBM" content="20.5937, 78.9629">

<!-- Contact Information -->
<meta name="contact" content="<?php echo $business_email; ?>">
<meta name="phone" content="<?php echo $business_phone; ?>">

<!-- Additional SEO Meta Tags -->
<meta name="rating" content="general">
<meta name="distribution" content="global">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">
<meta name="expires" content="never">
<meta name="format-detection" content="telephone=yes">
<meta name="format-detection" content="address=yes">

<!-- Mobile Optimization -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="<?php echo $business_name; ?>">

<!-- Preconnect for performance -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com">

<!-- DNS Prefetch -->
<link rel="dns-prefetch" href="//www.google-analytics.com">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">