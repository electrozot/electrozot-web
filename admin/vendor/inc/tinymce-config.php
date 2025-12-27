<?php
/**
 * TinyMCE Configuration for ElectroZot Blog System
 * Centralized configuration to avoid duplication
 */

// Ensure config is loaded and API key is available
if (!isset($tinymce_api_key) || empty($tinymce_api_key)) {
    // Try to load config from different possible paths
    $config_paths = [
        __DIR__ . '/../../../config.php',
        __DIR__ . '/../../config.php',
        __DIR__ . '/../config.php'
    ];
    
    foreach ($config_paths as $config_path) {
        if (file_exists($config_path)) {
            include_once($config_path);
            break;
        }
    }
    
    // If still not found, use the direct API key
    if (!isset($tinymce_api_key) || empty($tinymce_api_key)) {
        $tinymce_api_key = 'p06fobmdfwb9p9piooby6kip531y3o8cmmmvidr9cg8rdd09';
    }
}
?>

<!-- TinyMCE Rich Text Editor -->
<script src="https://cdn.tiny.cloud/1/<?php echo htmlspecialchars($tinymce_api_key); ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#blog_content',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'template', 'codesample'
    ],
    toolbar1: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
    toolbar2: 'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media table',
    toolbar3: 'codesample | emoticons charmap | code preview fullscreen | help',
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
        },
        {
            title: 'Call to Action',
            description: 'ElectroZot branded CTA',
            content: '<div style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); color: white; padding: 20px; border-radius: 15px; text-align: center; margin: 20px 0;"><h4 style="color: white; margin-top: 0;">Need Professional Help?</h4><p style="margin-bottom: 15px;">Get expert electrical and plumbing services from certified technicians</p><a href="tel:7559606925" style="background: white; color: #EC4899; padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: bold; display: inline-block;">📞 Call 7559606925</a></div>'
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