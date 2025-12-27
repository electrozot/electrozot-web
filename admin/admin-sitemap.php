<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Get sitemap statistics
$sitemap_file = __DIR__ . '/../sitemap.xml';
$sitemap_exists = file_exists($sitemap_file);
$sitemap_size = $sitemap_exists ? filesize($sitemap_file) : 0;
$sitemap_modified = $sitemap_exists ? filemtime($sitemap_file) : 0;

// Get blog post count
$blog_count_query = "SELECT COUNT(*) as total, 
                     SUM(CASE WHEN blog_status = 'Published' THEN 1 ELSE 0 END) as published,
                     SUM(CASE WHEN blog_status = 'Draft' THEN 1 ELSE 0 END) as draft
                     FROM tms_blog_posts";
$blog_stats = $mysqli->query($blog_count_query)->fetch_assoc();

// Get recent blog posts
$recent_blogs_query = "SELECT blog_id, blog_title, blog_slug, blog_status, blog_published_at, blog_updated_at 
                       FROM tms_blog_posts 
                       ORDER BY blog_created_at DESC 
                       LIMIT 5";
$recent_blogs = $mysqli->query($recent_blogs_query);
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
                
                <h1 class="mt-4 mb-4">
                    <i class="fas fa-sitemap"></i> Sitemap Management
                </h1>
                
                <!-- Sitemap Status -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Sitemap Status
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $sitemap_exists ? 'Active' : 'Missing'; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-<?php echo $sitemap_exists ? 'check-circle text-success' : 'exclamation-triangle text-warning'; ?> fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Published Posts
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $blog_stats['published']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-blog fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Last Updated
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $sitemap_modified ? date('M d, Y', $sitemap_modified) : 'Never'; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sitemap Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-tools"></i> Sitemap Actions
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Manual Operations</h5>
                                <p class="text-muted">Manually control your sitemap generation</p>
                                
                                <button id="regenerate-sitemap" class="btn btn-primary mb-2">
                                    <i class="fas fa-sync-alt"></i> Regenerate Sitemap
                                </button>
                                
                                <a href="../sitemap.xml" target="_blank" class="btn btn-info mb-2">
                                    <i class="fas fa-eye"></i> View Current Sitemap
                                </a>
                                
                                <a href="../sitemap-dynamic.php" target="_blank" class="btn btn-success mb-2">
                                    <i class="fas fa-bolt"></i> View Dynamic Sitemap
                                </a>
                                
                                <a href="../generate-sitemap.php" target="_blank" class="btn btn-secondary mb-2">
                                    <i class="fas fa-cog"></i> Generator Tool
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Automation Info</h5>
                                <p class="text-muted">Your sitemap updates automatically when you:</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> Add a new blog post</li>
                                    <li><i class="fas fa-check text-success"></i> Update an existing post</li>
                                    <li><i class="fas fa-check text-success"></i> Delete a blog post</li>
                                    <li><i class="fas fa-check text-success"></i> Change post status to Published</li>
                                </ul>
                                
                                <?php if ($sitemap_exists): ?>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>File Size:</strong> <?php echo number_format($sitemap_size); ?> bytes<br>
                                        <strong>Last Modified:</strong> <?php echo date('Y-m-d H:i:s', $sitemap_modified); ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Blog Posts -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-blog"></i> Recent Blog Posts in Sitemap
                    </div>
                    <div class="card-body">
                        <?php if ($recent_blogs->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Published</th>
                                        <th>In Sitemap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($blog = $recent_blogs->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="../blog-post.php?id=<?php echo $blog['blog_id']; ?>&slug=<?php echo $blog['blog_slug']; ?>" target="_blank">
                                                <?php echo htmlspecialchars($blog['blog_title']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $blog['blog_status'] == 'Published' ? 'success' : 'warning'; ?>">
                                                <?php echo $blog['blog_status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $blog['blog_published_at'] ? date('M d, Y', strtotime($blog['blog_published_at'])) : '-'; ?>
                                        </td>
                                        <td>
                                            <?php if ($blog['blog_status'] == 'Published'): ?>
                                                <i class="fas fa-check text-success" title="Included in sitemap"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times text-muted" title="Not in sitemap (not published)"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No blog posts found.</p>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="admin-manage-blog.php" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Manage All Blog Posts
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- SEO Tips -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-lightbulb"></i> SEO Tips
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Sitemap Best Practices</h6>
                                <ul class="small">
                                    <li>Submit your sitemap to Google Search Console</li>
                                    <li>Keep your sitemap under 50MB and 50,000 URLs</li>
                                    <li>Update sitemaps when content changes</li>
                                    <li>Use proper priority values (0.1 to 1.0)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Submit to Search Engines</h6>
                                <ul class="small">
                                    <li><strong>Google:</strong> <a href="https://search.google.com/search-console" target="_blank">Search Console</a></li>
                                    <li><strong>Bing:</strong> <a href="https://www.bing.com/webmasters" target="_blank">Webmaster Tools</a></li>
                                    <li><strong>Direct URL:</strong> <code>https://electrozot.in/sitemap.xml</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <!-- Sitemap Management Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const regenBtn = document.getElementById('regenerate-sitemap');
        
        if (regenBtn) {
            regenBtn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Regenerating...';
                this.disabled = true;
                
                fetch('vendor/inc/sitemap-hooks.php?action=regenerate_sitemap')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.innerHTML = '<i class="fas fa-check"></i> Success!';
                            this.className = 'btn btn-success mb-2';
                            
                            // Show success alert
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                <strong><i class="fas fa-check-circle"></i> Success!</strong> 
                                Sitemap regenerated successfully at ${data.timestamp}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            `;
                            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('h1').nextSibling);
                            
                            // Reload page after 2 seconds to show updated stats
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed';
                            this.className = 'btn btn-danger mb-2';
                            
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.className = 'btn btn-primary mb-2';
                                this.disabled = false;
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Sitemap regeneration error:', error);
                        this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                        this.className = 'btn btn-danger mb-2';
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.className = 'btn btn-primary mb-2';
                            this.disabled = false;
                        }, 3000);
                    });
            });
        }
    });
    </script>
</body>
</html>