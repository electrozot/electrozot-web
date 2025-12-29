<?php
session_start();
include('admin/vendor/inc/config.php');
include('admin/vendor/inc/html-sanitizer.php');

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
    
    <!-- Hero Section -->
    <section class="blog-hero" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 25%, #ffffff 50%, #faf5ff 75%, #f3e8ff 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px; position: relative; overflow: hidden;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="blog-hero-overlay"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center">
                <div class="mb-3" style="display: inline-block; background: rgba(220, 20, 60, 0.15); padding: 8px 20px; border-radius: 50px; border: 2px solid #dc143c;">
                    <span style="color: #dc143c; font-weight: 700; font-size: 0.9rem; letter-spacing: 2px;">OUR BLOG</span>
                </div>
                <h1 class="display-4 font-weight-bold mb-3 blog-title" style="color: #000000; font-size: 2.5rem; font-weight: 900;">
                    <i class="fas fa-blog" style="color: #dc143c;"></i> Expert Tips & Guides
                </h1>
                <p class="lead blog-subtitle" style="font-size: 1.1rem; color: #000000; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Professional insights and maintenance guides from our certified technicians
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="margin-top: -30px; position: relative; z-index: 3; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb blog-breadcrumb" style="background: #faf5ff; border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid #dc143c; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="index.php" style="color: #dc143c; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #000000; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Blog</li>
            </ol>
        </nav>
        
        <div class="row">
            <?php
            // Handle category filtering
            $category_filter = isset($_GET['category']) ? $_GET['category'] : '';
            
            if($category_filter) {
                $query = "SELECT * FROM tms_blog_posts WHERE blog_status = 'Published' AND blog_category = ? ORDER BY blog_published_at DESC";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('s', $category_filter);
                $stmt->execute();
                $result = $stmt->get_result();
                
                echo '<div class="col-12 mb-4">';
                echo '<div class="alert" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); border: 2px solid #dc143c; border-radius: 12px; color: #000000;">';
                echo '<i class="fas fa-filter" style="color: #dc143c;"></i> Showing articles in category: <strong>' . htmlspecialchars($category_filter) . '</strong> ';
                echo '<a href="blog.php" class="btn btn-sm ml-2" style="background: #e9d5ff; border: 2px solid #dc143c; color: #000000; border-radius: 8px; font-weight: 600;">Show All</a>';
                echo '</div>';
                echo '</div>';
            } else {
                $query = "SELECT * FROM tms_blog_posts WHERE blog_status = 'Published' ORDER BY blog_published_at DESC";
                $result = $mysqli->query($query);
            }
            
            if($result->num_rows > 0) {
                while($post = $result->fetch_object()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 blog-card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <?php if($post->blog_image) { ?>
                        <img src="<?php echo $post->blog_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post->blog_title); ?>" style="height: 200px; object-fit: cover;">
                    <?php } else { ?>
                        <div class="card-img-top" style="height: 200px; background: linear-gradient(135deg, #dc143c 0%, #8b1538 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-blog" style="font-size: 3rem; color: white; opacity: 0.8;"></i>
                        </div>
                    <?php } ?>
                    <div class="card-body" style="background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); padding: 20px;">
                        <h5 class="card-title" style="color: #000000; font-weight: 700; margin-bottom: 15px;"><?php echo htmlspecialchars($post->blog_title); ?></h5>
                        <p class="card-text" style="color: #374151; line-height: 1.6; font-weight: 500;">
                            <?php 
                            $excerpt_content = $post->blog_excerpt ?: $post->blog_content;
                            echo htmlspecialchars(get_blog_excerpt($excerpt_content, 120));
                            ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <small style="color: #6b7280; font-weight: 600;">
                                <i class="fas fa-calendar" style="color: #dc143c;"></i> <?php echo date('M d, Y', strtotime($post->blog_published_at)); ?>
                            </small>
                            <small style="color: #6b7280; font-weight: 600;">
                                <i class="fas fa-eye" style="color: #dc143c;"></i> <?php echo $post->blog_views; ?> views
                            </small>
                        </div>
                        <?php if($post->blog_category) { ?>
                            <span class="badge mt-2" style="background: #dc143c; color: white; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;"><?php echo $post->blog_category; ?></span>
                        <?php } ?>
                    </div>
                    <div class="card-footer" style="background: rgba(255, 255, 255, 0.8); border: none; padding: 20px;">
                        <a href="blog-post.php?id=<?php echo $post->blog_id; ?>&slug=<?php echo $post->blog_slug; ?>" class="btn btn-block blog-read-btn" style="background: #e9d5ff; border: 2px solid #dc143c; color: #000000; padding: 12px; font-weight: 600; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease;">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo '<div class="col-12"><div class="text-center" style="padding: 60px 20px; background: linear-gradient(135deg, #E9D5FF 0%, #FDF2F8 50%, #FEF3C7 100%); border-radius: 20px; border: 2px solid #dc143c;"><i class="fas fa-blog" style="font-size: 4rem; color: #dc143c; margin-bottom: 20px;"></i><h3 style="color: #000000; font-weight: 700; margin-bottom: 10px;">No Blog Posts Available</h3><p style="color: #374151; font-weight: 500;">Check back soon for expert tips and guides from our certified technicians.</p></div></div>';
            }
            ?>
        </div>
    </div>
    
    <?php include("vendor/inc/footer.php"); ?>
    
    <style>
        /* Blog Page Specific Styles */
        .blog-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
            z-index: 1;
        }

        .blog-title {
            animation: fadeInUp 0.8s ease-out;
        }

        .blog-subtitle {
            animation: fadeInUp 1s ease-out;
        }

        .blog-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(220, 20, 60, 0.3) !important;
        }

        .blog-read-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .blog-read-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(220, 20, 60, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .blog-read-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .blog-read-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(220, 20, 60, 0.5);
            background: #f3e8ff !important;
            border-color: #8b1538 !important;
            color: #000000 !important;
            text-decoration: none;
        }

        .blog-breadcrumb {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }
        
        .blog-breadcrumb .breadcrumb-item {
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        
        .blog-breadcrumb a:hover {
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

        @media (max-width: 768px) {
            .blog-hero {
                padding: 100px 0 40px 0 !important;
            }
            
            .display-4 {
                font-size: 2rem;
            }

            .container {
                padding-bottom: 100px !important;
            }
            
            .blog-breadcrumb {
                padding: 10px 15px !important;
                font-size: 0.85rem !important;
            }
            
            .blog-breadcrumb .breadcrumb-item a,
            .blog-breadcrumb .breadcrumb-item {
                font-size: 0.85rem !important;
            }
        }
    </style>
    
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Mobile menu fix -->
    <script>
        $(document).ready(function() {
            console.log('Mobile menu script loaded');
            
            // Enhanced mobile menu toggle - works with both Bootstrap 4 and 5
            $('.navbar-toggler').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Menu toggle clicked');
                
                var target = $(this).attr('data-target') || $(this).attr('data-bs-target');
                var $target = $(target);
                
                if ($target.length) {
                    $target.toggleClass('show');
                    console.log('Menu toggled, show class:', $target.hasClass('show'));
                    
                    // Update aria-expanded
                    var isExpanded = $target.hasClass('show');
                    $(this).attr('aria-expanded', isExpanded);
                } else {
                    console.error('Target not found:', target);
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
        });
    </script>
    
    <?php include("vendor/inc/bottom-nav-home.php"); ?>
</body>
</html>
