<?php
session_start();
include('admin/vendor/inc/config.php');
include('admin/vendor/inc/html-sanitizer.php');

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Get blog post
$query = "SELECT * FROM tms_blog_posts WHERE blog_id = ? AND blog_status = 'Published'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_object();

if(!$post) {
    header("location: blog.php");
    exit();
}

// Update view count
$mysqli->query("UPDATE tms_blog_posts SET blog_views = blog_views + 1 WHERE blog_id = $blog_id");

// Dynamic SEO Meta for Blog Post
$seo_title = $post->blog_seo_title ?: $post->blog_title . " | Electrozot Blog";
$seo_description = $post->blog_seo_description ?: substr(strip_tags($post->blog_content), 0, 160) . '...';
$seo_keywords = $post->blog_seo_keywords ?: $post->blog_tags;
$seo_image = $post->blog_image ? "https://" . $_SERVER['HTTP_HOST'] . "/" . $post->blog_image : "https://" . $_SERVER['HTTP_HOST'] . "/vendor/EZlogonew.png";
$seo_type = "article";
$canonical_url = "https://" . $_SERVER['HTTP_HOST'] . "/blog-post.php?id=" . $blog_id . ($slug ? "&slug=" . $slug : "");

// Article-specific Open Graph data
$og_article_author = "Electrozot Team";
$og_article_published_time = date('c', strtotime($post->blog_published_at));
$og_article_modified_time = date('c', strtotime($post->blog_updated_at));
$og_article_section = $post->blog_category ?: "Electrical Tips";
$og_article_tag = $post->blog_tags;
?>
<!DOCTYPE html>
<html lang="en">
<?php include("vendor/inc/head.php"); ?>
<head>
    <?php include("vendor/inc/seo-meta.php"); ?>
    
    <!-- Blog Content Styling -->
    <style>
        .blog-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        
        /* Handle both HTML and plain text content */
        .blog-content p:first-child {
            margin-top: 0;
        }
        
        .blog-content p:last-child {
            margin-bottom: 0;
        }
        
        /* Plain text content styling */
        .blog-content:not(:has(h1, h2, h3, h4, h5, h6, ul, ol, table, blockquote)) {
            white-space: pre-line;
        }
        
        .blog-content h1, .blog-content h2, .blog-content h3, 
        .blog-content h4, .blog-content h5, .blog-content h6 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .blog-content h1 { font-size: 2.5rem; }
        .blog-content h2 { font-size: 2rem; }
        .blog-content h3 { font-size: 1.75rem; }
        .blog-content h4 { font-size: 1.5rem; }
        .blog-content h5 { font-size: 1.25rem; }
        .blog-content h6 { font-size: 1.1rem; }
        
        .blog-content p {
            margin-bottom: 1.5rem;
            color: #4a5568;
        }
        
        .blog-content ul, .blog-content ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }
        
        .blog-content li {
            margin-bottom: 0.5rem;
            color: #4a5568;
        }
        
        .blog-content a {
            color: #EC4899;
            text-decoration: none;
            font-weight: 600;
        }
        
        .blog-content a:hover {
            color: #BE185D;
            text-decoration: underline;
        }
        
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1.5rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .blog-content blockquote {
            border-left: 4px solid #EC4899;
            padding-left: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: #6b7280;
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 0 8px 8px 0;
        }
        
        .blog-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .blog-content th, .blog-content td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .blog-content th {
            background: #f3f4f6;
            font-weight: 600;
            color: #374151;
        }
        
        .blog-content tr:hover {
            background: #f9fafb;
        }
        
        .blog-content pre {
            background: #1f2937;
            color: #f9fafb;
            padding: 1.5rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 2rem 0;
        }
        
        .blog-content code {
            background: #f3f4f6;
            color: #dc2626;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .blog-content pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .blog-content {
                font-size: 1rem;
            }
            
            .blog-content h1 { font-size: 2rem; }
            .blog-content h2 { font-size: 1.75rem; }
            .blog-content h3 { font-size: 1.5rem; }
            .blog-content h4 { font-size: 1.25rem; }
            
            .blog-content table {
                font-size: 0.9rem;
            }
            
            .blog-content th, .blog-content td {
                padding: 8px 10px;
            }
        }
    </style>
    
    <!-- Article Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "<?php echo htmlspecialchars($post->blog_title); ?>",
      "description": "<?php echo htmlspecialchars($seo_description); ?>",
      "image": "<?php echo $seo_image; ?>",
      "datePublished": "<?php echo date('c', strtotime($post->blog_published_at)); ?>",
      "dateModified": "<?php echo date('c', strtotime($post->blog_updated_at)); ?>",
      "author": {
        "@type": "Organization",
        "name": "Electrozot"
      },
      "publisher": {
        "@type": "Organization",
        "name": "Electrozot",
        "logo": {
          "@type": "ImageObject",
          "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/vendor/img/icons/icon-512x512.png"
        }
      }
    }
    </script>
</head>
<body>
    <?php include("vendor/inc/nav.php"); ?>
    
    <div class="container" style="margin-top: 100px; margin-bottom: 60px;">
        <div class="row">
            <div class="col-lg-8">
                <article>
                    <header class="mb-4">
                        <h1 class="mb-3"><?php echo htmlspecialchars($post->blog_title); ?></h1>
                        <div class="text-muted mb-3">
                            <i class="fas fa-calendar"></i> <?php echo date('F d, Y', strtotime($post->blog_published_at)); ?>
                            <span class="mx-2">|</span>
                            <i class="fas fa-eye"></i> <?php echo $post->blog_views; ?> views
                            <?php if($post->blog_category) { ?>
                                <span class="mx-2">|</span>
                                <i class="fas fa-folder"></i> <?php echo $post->blog_category; ?>
                            <?php } ?>
                        </div>
                        <?php if($post->blog_tags) { ?>
                            <div class="mb-3">
                                <?php 
                                $tags = explode(',', $post->blog_tags);
                                foreach($tags as $tag) {
                                    echo '<span class="badge badge-secondary mr-1">' . trim($tag) . '</span>';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </header>
                    
                    <?php if($post->blog_image) { ?>
                        <img src="<?php echo $post->blog_image; ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($post->blog_title); ?>">
                    <?php } ?>
                    
                    <div class="blog-content" style="line-height: 1.8; font-size: 1.1rem;">
                        <?php 
                        // Handle both HTML and plain text content
                        echo sanitize_blog_content($post->blog_content);
                        ?>
                    </div>
                    
                    <!-- Social Sharing Section -->
                    <div class="social-sharing mt-5 mb-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 25px; border-radius: 15px; border-left: 4px solid #EC4899;">
                        <h5 class="font-weight-bold mb-3" style="color: #2d3748;">
                            <i class="fas fa-share-alt" style="color: #EC4899; margin-right: 8px;"></i>Share This Article
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($canonical_url); ?>" 
                               target="_blank" 
                               class="btn btn-facebook" 
                               style="background: #1877F2; color: white; border: none; padding: 10px 20px; border-radius: 25px; text-decoration: none; margin-right: 10px; margin-bottom: 10px; transition: all 0.3s ease;">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($canonical_url); ?>&text=<?php echo urlencode($post->blog_title); ?>" 
                               target="_blank" 
                               class="btn btn-twitter" 
                               style="background: #1DA1F2; color: white; border: none; padding: 10px 20px; border-radius: 25px; text-decoration: none; margin-right: 10px; margin-bottom: 10px; transition: all 0.3s ease;">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($canonical_url); ?>" 
                               target="_blank" 
                               class="btn btn-linkedin" 
                               style="background: #0A66C2; color: white; border: none; padding: 10px 20px; border-radius: 25px; text-decoration: none; margin-right: 10px; margin-bottom: 10px; transition: all 0.3s ease;">
                                <i class="fab fa-linkedin-in"></i> LinkedIn
                            </a>
                            
                            <a href="https://wa.me/?text=<?php echo urlencode($post->blog_title . ' - ' . $canonical_url); ?>" 
                               target="_blank" 
                               class="btn btn-whatsapp" 
                               style="background: #25D366; color: white; border: none; padding: 10px 20px; border-radius: 25px; text-decoration: none; margin-right: 10px; margin-bottom: 10px; transition: all 0.3s ease;">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            
                            <button onclick="copyToClipboard('<?php echo $canonical_url; ?>')" 
                                    class="btn btn-copy" 
                                    style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 25px; margin-right: 10px; margin-bottom: 10px; transition: all 0.3s ease;">
                                <i class="fas fa-copy"></i> Copy Link
                            </button>
                        </div>
                    </div>
                    
                    <hr class="my-5">
                    
                    <div class="text-center">
                        <h4>Need Professional Help?</h4>
                        <p>Book our certified technicians for quality service</p>
                        <a href="index.php#booking-form" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check"></i> Book Service Now
                        </a>
                    </div>
                </article>
                
                <div class="mt-5">
                    <a href="blog.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Blog
                    </a>
                </div>
            </div>
            
            <!-- Sidebar with Recent Articles -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- Recent Articles Section -->
                    <div class="recent-articles-widget mb-4" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 4px solid #EC4899;">
                        <h5 class="font-weight-bold mb-4" style="color: #2d3748;">
                            <i class="fas fa-newspaper" style="color: #EC4899; margin-right: 8px;"></i>Recent Articles
                        </h5>
                        
                        <?php
                        // Get recent articles (excluding current post)
                        $recent_query = "SELECT blog_id, blog_title, blog_slug, blog_image, blog_published_at, blog_views, blog_category 
                                        FROM tms_blog_posts 
                                        WHERE blog_status = 'Published' AND blog_id != ? 
                                        ORDER BY blog_published_at DESC 
                                        LIMIT 5";
                        $recent_stmt = $mysqli->prepare($recent_query);
                        $recent_stmt->bind_param('i', $blog_id);
                        $recent_stmt->execute();
                        $recent_result = $recent_stmt->get_result();
                        
                        if($recent_result->num_rows > 0) {
                            while($recent_post = $recent_result->fetch_object()) {
                        ?>
                            <div class="recent-article-item mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                <div class="row align-items-center">
                                    <div class="col-4">
                                        <?php if($recent_post->blog_image) { ?>
                                            <img src="<?php echo $recent_post->blog_image; ?>" 
                                                 class="img-fluid rounded" 
                                                 alt="<?php echo htmlspecialchars($recent_post->blog_title); ?>"
                                                 style="height: 60px; width: 100%; object-fit: cover;">
                                        <?php } else { ?>
                                            <div class="bg-gradient rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 60px; background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%);">
                                                <i class="fas fa-newspaper text-white"></i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-8">
                                        <h6 class="mb-1">
                                            <a href="blog-post.php?id=<?php echo $recent_post->blog_id; ?>&slug=<?php echo $recent_post->blog_slug; ?>" 
                                               style="color: #2d3748; text-decoration: none; font-size: 0.9rem; line-height: 1.3;">
                                                <?php echo htmlspecialchars(substr($recent_post->blog_title, 0, 50)) . (strlen($recent_post->blog_title) > 50 ? '...' : ''); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar" style="font-size: 0.7rem;"></i> 
                                            <?php echo date('M d, Y', strtotime($recent_post->blog_published_at)); ?>
                                        </small>
                                        <?php if($recent_post->blog_category) { ?>
                                            <br><span class="badge badge-sm" style="background: rgba(236,72,153,0.1); color: #EC4899; font-size: 0.7rem; padding: 2px 6px;"><?php echo $recent_post->blog_category; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<p class="text-muted">No other articles available yet.</p>';
                        }
                        ?>
                        
                        <div class="text-center mt-3">
                            <a href="blog.php" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                                View All Articles <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Categories Widget -->
                    <div class="categories-widget mb-4" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 4px solid #10B981;">
                        <h5 class="font-weight-bold mb-4" style="color: #2d3748;">
                            <i class="fas fa-folder-open" style="color: #10B981; margin-right: 8px;"></i>Categories
                        </h5>
                        
                        <?php
                        // Get categories with post counts
                        $cat_query = "SELECT blog_category, COUNT(*) as post_count 
                                     FROM tms_blog_posts 
                                     WHERE blog_status = 'Published' AND blog_category IS NOT NULL AND blog_category != '' 
                                     GROUP BY blog_category 
                                     ORDER BY post_count DESC";
                        $cat_result = $mysqli->query($cat_query);
                        
                        if($cat_result->num_rows > 0) {
                            while($category = $cat_result->fetch_object()) {
                        ?>
                            <div class="category-item mb-2">
                                <a href="blog.php?category=<?php echo urlencode($category->blog_category); ?>" 
                                   class="d-flex justify-content-between align-items-center text-decoration-none" 
                                   style="color: #4a5568; padding: 8px 12px; border-radius: 8px; transition: all 0.3s ease;">
                                    <span><?php echo htmlspecialchars($category->blog_category); ?></span>
                                    <span class="badge" style="background: #10B981; color: white;"><?php echo $category->post_count; ?></span>
                                </a>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<p class="text-muted">No categories available yet.</p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Quick Contact Widget -->
                    <div class="quick-contact-widget" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); border-radius: 15px; padding: 25px; color: white; text-align: center;">
                        <h5 class="font-weight-bold mb-3">
                            <i class="fas fa-phone-alt" style="margin-right: 8px;"></i>Need Help?
                        </h5>
                        <p class="mb-3" style="font-size: 0.9rem; opacity: 0.9;">
                            Get professional electrical and plumbing services
                        </p>
                        <a href="tel:7559606925" class="btn btn-light btn-sm mb-2" style="border-radius: 20px; color: #EC4899; font-weight: 600;">
                            <i class="fas fa-phone"></i> Call Now
                        </a>
                        <br>
                        <a href="index.php#booking-form" class="btn btn-outline-light btn-sm" style="border-radius: 20px;">
                            <i class="fas fa-calendar-check"></i> Book Service
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("vendor/inc/footer.php"); ?>
    
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Mobile menu fix -->
    <script>
        $(document).ready(function() {
            // Enhanced mobile menu toggle
            $('.navbar-toggler').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var target = $(this).attr('data-target') || $(this).attr('data-bs-target');
                var $target = $(target);
                
                if ($target.length) {
                    $target.toggleClass('show');
                    var isExpanded = $target.hasClass('show');
                    $(this).attr('aria-expanded', isExpanded);
                }
            });
            
            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar').length) {
                    $('.navbar-collapse').removeClass('show');
                    $('.navbar-toggler').attr('aria-expanded', 'false');
                }
            });
            
            // Close menu when clicking on menu items
            $('.navbar-nav .nav-link').on('click', function() {
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
            
            // Close menu with arrow button
            $('.mobile-menu-arrow-close').on('click', function(e) {
                e.preventDefault();
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
            
            // Prevent menu from closing when clicking inside it
            $('.navbar-collapse').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Social sharing hover effects
            $('.btn-facebook').hover(
                function() { $(this).css('background', '#166FE5'); },
                function() { $(this).css('background', '#1877F2'); }
            );
            
            $('.btn-twitter').hover(
                function() { $(this).css('background', '#1A91DA'); },
                function() { $(this).css('background', '#1DA1F2'); }
            );
            
            $('.btn-linkedin').hover(
                function() { $(this).css('background', '#095BA8'); },
                function() { $(this).css('background', '#0A66C2'); }
            );
            
            $('.btn-whatsapp').hover(
                function() { $(this).css('background', '#22C55E'); },
                function() { $(this).css('background', '#25D366'); }
            );
            
            $('.btn-copy').hover(
                function() { $(this).css('background', '#5A6268'); },
                function() { $(this).css('background', '#6c757d'); }
            );
            
            // Recent articles hover effect
            $('.recent-article-item').hover(
                function() { 
                    $(this).css({
                        'background': '#f8f9fa',
                        'transform': 'translateX(5px)',
                        'transition': 'all 0.3s ease'
                    });
                },
                function() { 
                    $(this).css({
                        'background': 'transparent',
                        'transform': 'translateX(0)',
                        'transition': 'all 0.3s ease'
                    });
                }
            );
            
            // Category hover effect
            $('.category-item a').hover(
                function() { 
                    $(this).css({
                        'background': 'rgba(16,185,129,0.1)',
                        'color': '#10B981'
                    });
                },
                function() { 
                    $(this).css({
                        'background': 'transparent',
                        'color': '#4a5568'
                    });
                }
            );
        });
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopySuccess();
                }, function(err) {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }
        
        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    showCopySuccess();
                } else {
                    showCopyError();
                }
            } catch (err) {
                showCopyError();
            }
            
            document.body.removeChild(textArea);
        }
        
        function showCopySuccess() {
            var btn = $('.btn-copy');
            var originalText = btn.html();
            btn.html('<i class="fas fa-check"></i> Copied!');
            btn.css('background', '#28a745');
            setTimeout(function() {
                btn.html(originalText);
                btn.css('background', '#6c757d');
            }, 2000);
        }
        
        function showCopyError() {
            var btn = $('.btn-copy');
            var originalText = btn.html();
            btn.html('<i class="fas fa-times"></i> Error');
            btn.css('background', '#dc3545');
            setTimeout(function() {
                btn.html(originalText);
                btn.css('background', '#6c757d');
            }, 2000);
        }
    </script>
    
    <?php include("vendor/inc/bottom-nav-home.php"); ?>
</body>
</html>
