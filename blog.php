<?php
session_start();
include('admin/vendor/inc/config.php');

// SEO Meta
$seo_title = "Blog - Electrical Tips & Home Maintenance Guides | Electrozot";
$seo_description = "Read expert tips on electrical safety, home maintenance, DIY guides, and more from Electrozot's certified technicians.";
$seo_keywords = "electrical tips, home maintenance, DIY guides, safety tips, electrician blog";
?>
<!DOCTYPE html>
<html lang="en">
<?php include("vendor/inc/head.php"); ?>
<head>
    <?php include("vendor/inc/seo-meta.php"); ?>
</head>
<body>
    <?php include("vendor/inc/nav.php"); ?>
    
    <div class="container" style="margin-top: 100px; margin-bottom: 60px;">
        <h1 class="text-center mb-4">Our Blog</h1>
        <p class="text-center text-muted mb-5">Expert tips and guides from our certified technicians</p>
        
        <div class="row">
            <?php
            $query = "SELECT * FROM tms_blog_posts WHERE blog_status = 'Published' ORDER BY blog_published_at DESC";
            $result = $mysqli->query($query);
            
            if($result->num_rows > 0) {
                while($post = $result->fetch_object()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if($post->blog_image) { ?>
                        <img src="<?php echo $post->blog_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post->blog_title); ?>" style="height: 200px; object-fit: cover;">
                    <?php } else { ?>
                        <div class="card-img-top bg-gradient" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php } ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post->blog_title); ?></h5>
                        <p class="card-text text-muted">
                            <?php echo htmlspecialchars(substr($post->blog_excerpt ?: $post->blog_content, 0, 120)) . '...'; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($post->blog_published_at)); ?>
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> <?php echo $post->blog_views; ?> views
                            </small>
                        </div>
                        <?php if($post->blog_category) { ?>
                            <span class="badge badge-primary mt-2"><?php echo $post->blog_category; ?></span>
                        <?php } ?>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="blog-post.php?id=<?php echo $post->blog_id; ?>&slug=<?php echo $post->blog_slug; ?>" class="btn btn-sm btn-primary btn-block">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo '<div class="col-12"><p class="text-center">No blog posts available yet.</p></div>';
            }
            ?>
        </div>
    </div>
    
    <?php include("vendor/inc/bottom-nav-home.php"); ?>
</body>
</html>
