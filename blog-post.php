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
            color: #374151;
        }
        
        .blog-content h1, .blog-content h2, .blog-content h3, 
        .blog-content h4, .blog-content h5, .blog-content h6 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #000000;
        }
        
        .blog-content h1 { font-size: 2.5rem; }
        .blog-content h2 { font-size: 2rem; }
        .blog-content h3 { font-size: 1.75rem; }
        .blog-content h4 { font-size: 1.5rem; }
        .blog-content h5 { font-size: 1.25rem; }
        .blog-content h6 { font-size: 1.1rem; }
        
        .blog-content p {
            margin-bottom: 1.5rem;
            color: #374151;
            font-weight: 500;
        }
        
        .blog-content ul, .blog-content ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }
        
        .blog-content li {
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
        }
        
        .blog-content a {
            color: #dc143c;
            text-decoration: none;
            font-weight: 600;
        }
        
        .blog-content a:hover {
            color: #8b1538;
            text-decoration: underline;
        }
        
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            margin: 1.5rem 0;
            box-shadow: 0 8px 25px rgba(220, 20, 60, 0.2);
        }
        
        .blog-content blockquote {
            border-left: 4px solid #dc143c;
            padding-left: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: #6b7280;
            background: rgba(255, 255, 255, 0.8);
            padding: 1.5rem;
            border-radius: 0 15px 15px 0;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.1);
        }
        
        .blog-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            box-shadow: 0 8px 25px rgba(220, 20, 60, 0.15);
            border-radius: 15px;
            overflow: hidden;
            background: white;
        }
        
        .blog-content th, .blog-content td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .blog-content th {
            background: #dc143c;
            font-weight: 600;
            color: white;
        }
        
        .blog-content tr:hover {
            background: #faf5ff;
        }
        
        .blog-content pre {
            background: #1f2937;
            color: #f9fafb;
            padding: 1.5rem;
            border-radius: 15px;
            overflow-x: auto;
            margin: 2rem 0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .blog-content code {
            background: #faf5ff;
            color: #dc143c;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: 600;
        }
        
        .blog-content pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }

        /* Blog Post Page Specific Styles */
        .blog-post-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
            z-index: 1;
        }

        .blog-post-title {
            animation: fadeInUp 0.8s ease-out;
        }

        .blog-post-meta {
            animation: fadeInUp 1s ease-out;
        }

        .blog-post-article {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .blog-post-breadcrumb {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }
        
        .blog-post-breadcrumb .breadcrumb-item {
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        
        .blog-post-breadcrumb a:hover {
            color: #8b1538 !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .blog-post-hero {
                padding: 100px 0 40px 0 !important;
            }
            
            .display-4 {
                font-size: 2rem;
            }

            .container {
                padding-bottom: 100px !important;
            }
            
            .blog-post-breadcrumb {
                padding: 10px 15px !important;
                font-size: 0.85rem !important;
            }
            
            .blog-post-breadcrumb .breadcrumb-item a,
            .blog-post-breadcrumb .breadcrumb-item {
                font-size: 0.85rem !important;
            }
            
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
    
    <!-- Hero Section -->
    <section class="blog-post-hero" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 25%, #ffffff 50%, #faf5ff 75%, #f3e8ff 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px; position: relative; overflow: hidden;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="blog-post-hero-overlay"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center">
                <div class="mb-3" style="display: inline-block; background: rgba(220, 20, 60, 0.15); padding: 8px 20px; border-radius: 50px; border: 2px solid #dc143c;">
                    <span style="color: #dc143c; font-weight: 700; font-size: 0.9rem; letter-spacing: 2px;">BLOG ARTICLE</span>
                </div>
                <h1 class="display-4 font-weight-bold mb-3 blog-post-title" style="color: #000000; font-size: 2.5rem; font-weight: 900;">
                    <i class="fas fa-newspaper" style="color: #dc143c;"></i> <?php echo htmlspecialchars($post->blog_title); ?>
                </h1>
                <div class="blog-post-meta" style="color: #000000; font-weight: 500;">
                    <i class="fas fa-calendar" style="color: #dc143c;"></i> <?php echo date('F d, Y', strtotime($post->blog_published_at)); ?>
                    <span class="mx-2">|</span>
                    <i class="fas fa-eye" style="color: #dc143c;"></i> <?php echo $post->blog_views; ?> views
                    <?php if($post->blog_category) { ?>
                        <span class="mx-2">|</span>
                        <i class="fas fa-folder" style="color: #dc143c;"></i> <?php echo $post->blog_category; ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <div class="container" style="margin-top: -30px; position: relative; z-index: 3; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb blog-post-breadcrumb" style="background: #faf5ff; border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid #dc143c; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="index.php" style="color: #dc143c; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="blog.php" style="color: #dc143c; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">Blog</a>
                </li>
                <li class="breadcrumb-item active" style="color: #000000; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Article</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-lg-8">
                <article class="blog-post-article" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(220, 20, 60, 0.15); border: 2px solid rgba(220, 20, 60, 0.1);">
                    <header class="mb-4">
                        <?php if($post->blog_tags) { ?>
                            <div class="mb-3">
                                <?php 
                                $tags = explode(',', $post->blog_tags);
                                foreach($tags as $tag) {
                                    echo '<span class="badge mr-1" style="background: #dc143c; color: white; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;">' . trim($tag) . '</span>';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </header>
                    
                    <?php if($post->blog_image) { ?>
                        <img src="<?php echo $post->blog_image; ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($post->blog_title); ?>" style="border-radius: 15px !important; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                    <?php } ?>
                    
                    <div class="blog-content" style="line-height: 1.8; font-size: 1.1rem;">
                        <?php 
                        // Sanitize and display HTML content safely
                        echo sanitize_blog_content($post->blog_content);
                        ?>
                    </div>
                    
                    <!-- Social Sharing Section -->
                    <div class="social-sharing mt-5 mb-4" style="background: rgba(255, 255, 255, 0.8); padding: 25px; border-radius: 15px; border: 2px solid #dc143c;">
                        <h5 class="font-weight-bold mb-3" style="color: #000000;">
                            <i class="fas fa-share-alt" style="color: #dc143c; margin-right: 8px;"></i>Share This Article
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
                    
                    <hr class="my-5" style="border-color: #dc143c; opacity: 0.3;">
                    
                    <div class="text-center" style="background: rgba(255, 255, 255, 0.8); padding: 30px; border-radius: 15px; border: 2px solid #dc143c;">
                        <h4 style="color: #000000; font-weight: 700; margin-bottom: 15px;">Need Professional Help?</h4>
                        <p style="color: #374151; font-weight: 500; margin-bottom: 20px;">Book our certified technicians for quality service</p>
                        <a href="index.php#booking-form" class="btn btn-lg" style="background: #e9d5ff; border: 2px solid #dc143c; color: #000000; padding: 15px 30px; font-weight: 600; border-radius: 12px; font-size: 1.1rem; transition: all 0.3s ease;">
                            <i class="fas fa-calendar-check"></i> Book Service Now
                        </a>
                    </div>
                </article>
                
                <div class="mt-5">
                    <a href="blog.php" class="btn" style="background: #e9d5ff; border: 2px solid #dc143c; color: #000000; padding: 12px 25px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
                        <i class="fas fa-arrow-left"></i> Back to Blog
                    </a>
                </div>
            </div>
            
            <!-- Sidebar with Recent Articles -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- Recent Articles Section -->
                    <div class="recent-articles-widget mb-4" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(220, 20, 60, 0.15); border: 2px solid #dc143c;">
                        <h5 class="font-weight-bold mb-4" style="color: #000000;">
                            <i class="fas fa-newspaper" style="color: #dc143c; margin-right: 8px;"></i>Recent Articles
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
                                                 style="height: 60px; background: linear-gradient(135deg, #dc143c 0%, #8b1538 100%);">
                                                <i class="fas fa-newspaper text-white"></i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-8">
                                        <h6 class="mb-1">
                                            <a href="blog-post.php?id=<?php echo $recent_post->blog_id; ?>&slug=<?php echo $recent_post->blog_slug; ?>" 
                                               style="color: #000000; text-decoration: none; font-size: 0.9rem; line-height: 1.3; font-weight: 600;">
                                                <?php echo htmlspecialchars(substr($recent_post->blog_title, 0, 50)) . (strlen($recent_post->blog_title) > 50 ? '...' : ''); ?>
                                            </a>
                                        </h6>
                                        <small style="color: #6b7280; font-weight: 500;">
                                            <i class="fas fa-calendar" style="font-size: 0.7rem; color: #dc143c;"></i> 
                                            <?php echo date('M d, Y', strtotime($recent_post->blog_published_at)); ?>
                                        </small>
                                        <?php if($recent_post->blog_category) { ?>
                                            <br><span class="badge badge-sm" style="background: #dc143c; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px;"><?php echo $recent_post->blog_category; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<p style="color: #6b7280; font-weight: 500;">No other articles available yet.</p>';
                        }
                        ?>
                        
                        <div class="text-center mt-3">
                            <a href="blog.php" class="btn btn-sm" style="background: #e9d5ff; border: 2px solid #dc143c; color: #000000; border-radius: 20px; padding: 8px 20px; font-weight: 600;">
                                View All Articles <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Categories Widget -->
                    <div class="categories-widget mb-4" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(220, 20, 60, 0.15); border: 2px solid #dc143c;">
                        <h5 class="font-weight-bold mb-4" style="color: #000000;">
                            <i class="fas fa-folder-open" style="color: #dc143c; margin-right: 8px;"></i>Categories
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
                                   style="color: #374151; padding: 8px 12px; border-radius: 8px; transition: all 0.3s ease; font-weight: 500;">
                                    <span><?php echo htmlspecialchars($category->blog_category); ?></span>
                                    <span class="badge" style="background: #dc143c; color: white; border-radius: 12px; padding: 4px 8px;"><?php echo $category->post_count; ?></span>
                                </a>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<p style="color: #6b7280; font-weight: 500;">No categories available yet.</p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Quick Contact Widget -->
                    <div class="quick-contact-widget" style="background: linear-gradient(135deg, #dc143c 0%, #8b1538 100%); border-radius: 20px; padding: 25px; color: white; text-align: center; box-shadow: 0 10px 30px rgba(220, 20, 60, 0.3);">
                        <h5 class="font-weight-bold mb-3">
                            <i class="fas fa-phone-alt" style="margin-right: 8px;"></i>Need Help?
                        </h5>
                        <p class="mb-3" style="font-size: 0.9rem; opacity: 0.9;">
                            Get professional electrical and plumbing services
                        </p>
                        <a href="tel:7559606925" class="btn btn-light btn-sm mb-2" style="border-radius: 20px; color: #dc143c; font-weight: 600;">
                            <i class="fas fa-phone"></i> Call Now
                        </a>
                        <br>
                        <a href="index.php#booking-form" class="btn btn-outline-light btn-sm" style="border-radius: 20px; border: 2px solid white; font-weight: 600;">
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
                        'background': 'rgba(220, 20, 60, 0.1)',
                        'color': '#dc143c'
                    });
                },
                function() { 
                    $(this).css({
                        'background': 'transparent',
                        'color': '#374151'
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
