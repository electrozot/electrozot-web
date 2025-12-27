<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

if(isset($_POST['add_blog'])) {
    $title = $_POST['blog_title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $content = $_POST['blog_content'];
    $excerpt = $_POST['blog_excerpt'];
    $category = $_POST['blog_category'];
    $tags = $_POST['blog_tags'];
    $status = $_POST['blog_status'];
    $seo_title = $_POST['blog_seo_title'];
    $seo_description = $_POST['blog_seo_description'];
    $seo_keywords = $_POST['blog_seo_keywords'];
    
    $published_at = ($status == 'Published') ? date('Y-m-d H:i:s') : NULL;
    
    // Handle image upload
    $blog_image = NULL;
    if(isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] == 0) {
        $target_dir = "../uploads/blog/";
        if(!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION);
        $new_filename = $slug . '-' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if(move_uploaded_file($_FILES['blog_image']['tmp_name'], $target_file)) {
            $blog_image = 'uploads/blog/' . $new_filename;
        }
    }
    
    $query = "INSERT INTO tms_blog_posts (blog_title, blog_slug, blog_content, blog_excerpt, blog_image, blog_author_id, blog_category, blog_tags, blog_status, blog_seo_title, blog_seo_description, blog_seo_keywords, blog_published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('sssssssssssss', $title, $slug, $content, $excerpt, $blog_image, $aid, $category, $tags, $status, $seo_title, $seo_description, $seo_keywords, $published_at);
    
    if($stmt->execute()) {
        // Auto-regenerate sitemap after adding blog post
        include_once('vendor/inc/sitemap-hooks.php');
        update_sitemap_after_blog_change('add', $mysqli->insert_id);
        
        $_SESSION['success'] = "Blog post added successfully!";
        header("location: admin-manage-blog.php");
        exit();
    } else {
        $error = "Failed to add blog post.";
    }
}

// Get categories
$categories_query = "SELECT * FROM tms_blog_categories ORDER BY category_name";
$categories_result = $mysqli->query($categories_query);
?>
<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php'); ?>
<body>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <?php include('vendor/inc/nav.php'); ?>
                
                <h1 class="mt-4 mb-4">Add New Blog Post</h1>
                
                <?php if(isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Blog Title *</label>
                                        <input type="text" name="blog_title" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Content *</label>
                                        <textarea name="blog_content" id="blog_content" class="form-control" rows="15" required></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Excerpt (Short Description)</label>
                                        <textarea name="blog_excerpt" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select name="blog_status" class="form-control" required>
                                            <option value="Draft">Draft</option>
                                            <option value="Published">Published</option>
                                            <option value="Archived">Archived</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="blog_category" class="form-control">
                                            <option value="">Select Category</option>
                                            <?php while($cat = $categories_result->fetch_object()) { ?>
                                                <option value="<?php echo $cat->category_name; ?>"><?php echo $cat->category_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Tags (comma separated)</label>
                                        <input type="text" name="blog_tags" class="form-control" placeholder="electrical, tips, safety">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Featured Image</label>
                                        <input type="file" name="blog_image" class="form-control-file" accept="image/*">
                                    </div>
                                    
                                    <hr>
                                    <h5>SEO Settings</h5>
                                    
                                    <div class="form-group">
                                        <label>SEO Title</label>
                                        <input type="text" name="blog_seo_title" class="form-control">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>SEO Description</label>
                                        <textarea name="blog_seo_description" class="form-control" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>SEO Keywords</label>
                                        <input type="text" name="blog_seo_keywords" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="add_blog" class="btn btn-primary">
                                <i class="fas fa-save"></i> Publish Post
                            </button>
                            <a href="admin-manage-blog.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>
</body>

<!-- TinyMCE Rich Text Editor -->
<?php 
// Ensure we have the TinyMCE API key from config
if (!isset($tinymce_api_key) || empty($tinymce_api_key)) {
    // Load config from parent directory
    include_once(__DIR__ . '/../config.php');
}

// Include TinyMCE configuration
include('vendor/inc/tinymce-config.php'); 
?>

</html>
