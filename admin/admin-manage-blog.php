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
                        <div class="float-right">
                            <button id="regenerate-sitemap-btn" class="btn btn-secondary btn-sm mr-2" title="Regenerate sitemap with current blog posts">
                                <i class="fas fa-sitemap"></i> Update Sitemap
                            </button>
                            <a href="admin-add-blog.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Post
                            </a>
                        </div>
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

    <!-- Sitemap Regeneration Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const regenBtn = document.getElementById('regenerate-sitemap-btn');
        
        if (regenBtn) {
            regenBtn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                this.disabled = true;
                
                fetch('vendor/inc/sitemap-hooks.php?action=regenerate_sitemap')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.innerHTML = '<i class="fas fa-check"></i> Updated!';
                            this.className = 'btn btn-success btn-sm mr-2';
                            
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                <strong>Success!</strong> Sitemap regenerated successfully at ${data.timestamp}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            `;
                            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('h1').nextSibling);
                            
                            // Reset button after 3 seconds
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.className = 'btn btn-secondary btn-sm mr-2';
                                this.disabled = false;
                            }, 3000);
                        } else {
                            this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed';
                            this.className = 'btn btn-danger btn-sm mr-2';
                            
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.className = 'btn btn-secondary btn-sm mr-2';
                                this.disabled = false;
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Sitemap regeneration error:', error);
                        this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                        this.className = 'btn btn-danger btn-sm mr-2';
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.className = 'btn btn-secondary btn-sm mr-2';
                            this.disabled = false;
                        }, 3000);
                    });
            });
        }
    });
    </script>
</body>
</html>
