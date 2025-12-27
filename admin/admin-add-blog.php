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
<script src="https://cdn.tiny.cloud/1/p06fobmdfwb9p9piooby6kip531y3o8cmmmvidr9cg8rdd09/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#blog_content',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'template', 'codesample', 'hr', 'pagebreak', 'nonbreaking', 'toc'
    ],
    toolbar1: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
    toolbar2: 'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media table',
    toolbar3: 'hr pagebreak | codesample | emoticons charmap | code preview fullscreen | help',
    content_style: `
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; 
            font-size: 16px; 
            line-height: 1.6; 
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3, h4, h5, h6 { 
            color: #2d3748; 
            margin-top: 2rem; 
            margin-bottom: 1rem; 
        }
        p { margin-bottom: 1rem; }
        img { max-width: 100%; height: auto; border-radius: 8px; }
        blockquote { 
            border-left: 4px solid #EC4899; 
            padding-left: 1rem; 
            margin: 1rem 0; 
            font-style: italic; 
            background: #f9fafb; 
            padding: 1rem; 
        }
        table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 1rem; border-radius: 5px; overflow-x: auto; }
    `,
    branding: false,
    promotion: false,
    resize: true,
    elementpath: true,
    statusbar: true,
    
    // Enhanced image handling
    image_advtab: true,
    image_caption: true,
    image_title: true,
    
    // Link options
    link_title: true,
    link_target_list: [
        {title: 'Same window', value: ''},
        {title: 'New window', value: '_blank'}
    ],
    
    // Table options
    table_responsive_width: true,
    table_default_attributes: {
        'class': 'table table-striped'
    },
    
    // Code sample languages
    codesample_languages: [
        {text: 'HTML/XML', value: 'markup'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'CSS', value: 'css'},
        {text: 'PHP', value: 'php'},
        {text: 'Python', value: 'python'},
        {text: 'Java', value: 'java'},
        {text: 'C', value: 'c'},
        {text: 'C++', value: 'cpp'},
        {text: 'SQL', value: 'sql'}
    ],
    
    // Templates for common content
    templates: [
        {
            title: 'Electrical Safety Tip',
            description: 'Template for safety tips',
            content: '<h3>🔌 Electrical Safety Tip</h3><p><strong>Important:</strong> </p><blockquote><p>💡 <strong>Pro Tip:</strong> </p></blockquote><p><strong>Remember:</strong> Always consult a certified electrician for complex electrical work.</p>'
        },
        {
            title: 'Service Guide',
            description: 'Template for service guides',
            content: '<h2>📋 Service Guide</h2><h3>What You\'ll Need:</h3><ul><li>Item 1</li><li>Item 2</li></ul><h3>Steps:</h3><ol><li>Step 1</li><li>Step 2</li></ol><h3>Safety Precautions:</h3><p>⚠️ <strong>Warning:</strong> </p>'
        }
    ],
    
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
        
        // Auto-save functionality
        editor.on('keyup', function () {
            setTimeout(function() {
                editor.save();
            }, 1000);
        });
    },
    
    // Image upload settings
    images_upload_url: 'vendor/inc/tinymce-upload.php',
    images_upload_base_path: '../uploads/blog/',
    images_upload_credentials: true,
    automatic_uploads: true,
    
    file_picker_types: 'image',
    file_picker_callback: function (callback, value, meta) {
        if (meta.filetype === 'image') {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    callback(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };
            input.click();
        }
    },
    
    // Paste options
    paste_as_text: false,
    paste_auto_cleanup_on_paste: true,
    paste_remove_styles_if_webkit: true,
    
    // Word count
    wordcount_countregex: /[\w\u2019\'-]+/g,
    
    // Accessibility
    a11y_advanced_options: true,
    
    // Mobile responsive
    mobile: {
        theme: 'mobile',
        plugins: ['autosave', 'lists', 'autolink'],
        toolbar: ['undo', 'bold', 'italic', 'styleselect']
    }
});
</script>


<!-- TinyMCE Rich Text Editor -->
<script src="https://cdn.tiny.cloud/1/p06fobmdfwb9p9piooby6kip531y3o8cmmmvidr9cg8rdd09/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#blog_content',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'template', 'codesample', 'hr', 'pagebreak', 'nonbreaking', 'toc'
    ],
    toolbar1: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
    toolbar2: 'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media table',
    toolbar3: 'hr pagebreak | codesample | emoticons charmap | code preview fullscreen | help',
    content_style: `
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; 
            font-size: 16px; 
            line-height: 1.6; 
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3, h4, h5, h6 { 
            color: #2d3748; 
            margin-top: 2rem; 
            margin-bottom: 1rem; 
        }
        p { margin-bottom: 1rem; }
        img { max-width: 100%; height: auto; border-radius: 8px; }
        blockquote { 
            border-left: 4px solid #EC4899; 
            padding-left: 1rem; 
            margin: 1rem 0; 
            font-style: italic; 
            background: #f9fafb; 
            padding: 1rem; 
        }
        table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 1rem; border-radius: 5px; overflow-x: auto; }
    `,
    branding: false,
    promotion: false,
    resize: true,
    elementpath: true,
    statusbar: true,
    
    // Enhanced image handling
    image_advtab: true,
    image_caption: true,
    image_title: true,
    
    // Link options
    link_title: true,
    link_target_list: [
        {title: 'Same window', value: ''},
        {title: 'New window', value: '_blank'}
    ],
    
    // Table options
    table_responsive_width: true,
    table_default_attributes: {
        'class': 'table table-striped'
    },
    
    // Code sample languages
    codesample_languages: [
        {text: 'HTML/XML', value: 'markup'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'CSS', value: 'css'},
        {text: 'PHP', value: 'php'},
        {text: 'Python', value: 'python'},
        {text: 'Java', value: 'java'},
        {text: 'C', value: 'c'},
        {text: 'C++', value: 'cpp'},
        {text: 'SQL', value: 'sql'}
    ],
    
    // Templates for common content
    templates: [
        {
            title: 'Electrical Safety Tip',
            description: 'Template for safety tips',
            content: '<h3>🔌 Safety Tip: [Title]</h3><div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 10px 0;"><p><strong>⚠️ Important:</strong> [Your safety tip here]</p><p><strong>Why it matters:</strong> [Explanation]</p><p><strong>What to do:</strong> [Action steps]</p></div>'
        },
        {
            title: 'Service Highlight',
            description: 'Template for highlighting services',
            content: '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin: 15px 0;"><h4 style="color: white; margin-top: 0;">🔧 [Service Name]</h4><p>[Service description]</p><p><strong>Benefits:</strong></p><ul><li>[Benefit 1]</li><li>[Benefit 2]</li><li>[Benefit 3]</li></ul><p style="margin-bottom: 0;"><a href="tel:7559606925" style="color: #FFD700; font-weight: bold;">📞 Call 7559606925 to book this service</a></p></div>'
        }
    ],
    
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
        
        // Custom button for ElectroZot branding
        editor.ui.registry.addButton('electrozot_cta', {
            text: 'Add CTA',
            tooltip: 'Insert ElectroZot Call-to-Action',
            onAction: function () {
                editor.insertContent('<div style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); color: white; padding: 20px; border-radius: 15px; text-align: center; margin: 20px 0;"><h4 style="color: white; margin-top: 0;">Need Professional Help?</h4><p style="margin-bottom: 15px;">Get expert electrical and plumbing services from certified technicians</p><a href="tel:7559606925" style="background: white; color: #EC4899; padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: bold; display: inline-block;">📞 Call 7559606925</a></div>');
            }
        });
    },
    
    // Image upload settings
    images_upload_url: 'vendor/inc/tinymce-upload.php',
    images_upload_base_path: '../uploads/blog/',
    images_upload_credentials: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function (callback, value, meta) {
        if (meta.filetype === 'image') {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    callback(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };
            input.click();
        }
    }
});
</script>

</html>
