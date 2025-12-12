<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get blog ID from URL
if(!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Blog post ID is required.";
    header("location: admin-manage-blog.php");
    exit();
}

$blog_id = intval($_GET['id']);

// Handle form submission
if(isset($_POST['update_blog'])) {
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
    $blog_image = $_POST['existing_image']; // Keep existing image by default
    if(isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] == 0) {
        $target_dir = "../uploads/blog/";
        if(!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION);
        $new_filename = $slug . '-' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if(move_uploaded_file($_FILES['blog_image']['tmp_name'], $target_file)) {
            // Delete old image if exists
            if($blog_image && file_exists('../' . $blog_image)) {
                unlink('../' . $blog_image);
            }
            $blog_image = 'uploads/blog/' . $new_filename;
        }
    }
    
    $query = "UPDATE tms_blog_posts SET blog_title=?, blog_slug=?, blog_content=?, blog_excerpt=?, blog_image=?, blog_category=?, blog_tags=?, blog_status=?, blog_seo_title=?, blog_seo_description=?, blog_seo_keywords=?, blog_published_at=?, blog_updated_at=NOW() WHERE blog_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssssssssssssi', $title, $slug, $content, $excerpt, $blog_image, $category, $tags, $status, $seo_title, $seo_description, $seo_keywords, $published_at, $blog_id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Blog post updated successfully!";
        header("location: admin-manage-blog.php");
        exit();
    } else {
        $error = "Failed to update blog post.";
    }
}

// Get blog post data
$query = "SELECT * FROM tms_blog_posts WHERE blog_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    $_SESSION['error'] = "Blog post not found.";
    header("location: admin-manage-blog.php");
    exit();
}

$blog = $result->fetch_object();

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
                
                <h1 class="mt-4 mb-4">Edit Blog Post</h1>
                
                <?php if(isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($blog->blog_image); ?>">
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Blog Title *</label>
                                        <input type="text" name="blog_title" class="form-control" value="<?php echo htmlspecialchars($blog->blog_title); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Content *</label>
                                        <textarea name="blog_content" class="form-control" rows="15" required><?php echo htmlspecialchars($blog->blog_content); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Excerpt (Short Description)</label>
                                        <textarea name="blog_excerpt" class="form-control" rows="3"><?php echo htmlspecialchars($blog->blog_excerpt); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select name="blog_status" class="form-control" required>
                                            <option value="Draft" <?php echo $blog->blog_status == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                                            <option value="Published" <?php echo $blog->blog_status == 'Published' ? 'selected' : ''; ?>>Published</option>
                                            <option value="Archived" <?php echo $blog->blog_status == 'Archived' ? 'selected' : ''; ?>>Archived</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="blog_category" class="form-control">
                                            <option value="">Select Category</option>
                                            <?php 
                                            $categories_result = $mysqli->query($categories_query);
                                            while($cat = $categories_result->fetch_object()) { 
                                            ?>
                                                <option value="<?php echo $cat->category_name; ?>" <?php echo $blog->blog_category == $cat->category_name ? 'selected' : ''; ?>>
                                                    <?php echo $cat->category_name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Tags (comma separated)</label>
                                        <input type="text" name="blog_tags" class="form-control" value="<?php echo htmlspecialchars($blog->blog_tags); ?>" placeholder="electrical, tips, safety">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Featured Image</label>
                                        <?php if($blog->blog_image) { ?>
                                            <div class="mb-2">
                                                <img src="../<?php echo $blog->blog_image; ?>" alt="Current Image" style="max-width: 200px; height: auto;" class="img-thumbnail">
                                                <p class="text-muted mt-1">Current image</p>
                                            </div>
                                        <?php } ?>
                                        <input type="file" name="blog_image" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Leave empty to keep current image</small>
                                    </div>
                                    
                                    <hr>
                                    <h5>SEO Settings</h5>
                                    
                                    <div class="form-group">
                                        <label>SEO Title</label>
                                        <input type="text" name="blog_seo_title" class="form-control" value="<?php echo htmlspecialchars($blog->blog_seo_title); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>SEO Description</label>
                                        <textarea name="blog_seo_description" class="form-control" rows="3"><?php echo htmlspecialchars($blog->blog_seo_description); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>SEO Keywords</label>
                                        <input type="text" name="blog_seo_keywords" class="form-control" value="<?php echo htmlspecialchars($blog->blog_seo_keywords); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="update_blog" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Post
                                </button>
                                <a href="admin-manage-blog.php" class="btn btn-secondary">Cancel</a>
                                <a href="../blog-post.php?id=<?php echo $blog->blog_id; ?>&slug=<?php echo $blog->blog_slug; ?>" class="btn btn-success" target="_blank">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>
</body>
</html>