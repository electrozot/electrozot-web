<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];
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
                
                <h1 class="mt-4 mb-4">Manage Blog Posts</h1>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-blog"></i> All Blog Posts
                        <a href="admin-add-blog.php" class="btn btn-primary btn-sm float-right">
                            <i class="fas fa-plus"></i> Add New Post
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Published</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM tms_blog_posts ORDER BY blog_created_at DESC";
                                    $result = $mysqli->query($query);
                                    while($row = $result->fetch_object()) {
                                        $status_badge = $row->blog_status == 'Published' ? 'success' : 
                                                       ($row->blog_status == 'Draft' ? 'warning' : 'secondary');
                                    ?>
                                    <tr>
                                        <td><?php echo $row->blog_id; ?></td>
                                        <td><?php echo htmlspecialchars($row->blog_title); ?></td>
                                        <td><?php echo htmlspecialchars($row->blog_category); ?></td>
                                        <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo $row->blog_status; ?></span></td>
                                        <td><?php echo $row->blog_views; ?></td>
                                        <td><?php echo $row->blog_published_at ? date('M d, Y', strtotime($row->blog_published_at)) : '-'; ?></td>
                                        <td>
                                            <a href="admin-edit-blog.php?id=<?php echo $row->blog_id; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="admin-delete-blog.php?id=<?php echo $row->blog_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="../blog-post.php?id=<?php echo $row->blog_id; ?>&slug=<?php echo $row->blog_slug; ?>" class="btn btn-sm btn-success" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>
</body>
</html>
