<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Blog post ID is required.";
    header("location: admin-manage-blog.php");
    exit();
}

$blog_id = intval($_GET['id']);

// Get blog post details before deletion (for image cleanup)
$query = "SELECT blog_title, blog_image FROM tms_blog_posts WHERE blog_id = ?";
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

// Delete the blog post
$delete_query = "DELETE FROM tms_blog_posts WHERE blog_id = ?";
$delete_stmt = $mysqli->prepare($delete_query);
$delete_stmt->bind_param('i', $blog_id);

if($delete_stmt->execute()) {
    // Delete associated image file if exists
    if($blog->blog_image && file_exists('../' . $blog->blog_image)) {
        unlink('../' . $blog->blog_image);
    }
    
    // Auto-regenerate sitemap after deleting blog post
    include_once('vendor/inc/sitemap-hooks.php');
    update_sitemap_after_blog_change('delete', $blog_id);
    
    $_SESSION['success'] = "Blog post '{$blog->blog_title}' deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete blog post.";
}

header("location: admin-manage-blog.php");
exit();
?>