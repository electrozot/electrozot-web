<?php
session_start();
include('admin/vendor/inc/config.php');

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
            <div class="col-lg-8 mx-auto">
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
                        <?php echo nl2br(htmlspecialchars($post->blog_content)); ?>
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
        </div>
    </div>
    
    <?php include("vendor/inc/bottom-nav-home.php"); ?>
</body>
</html>
