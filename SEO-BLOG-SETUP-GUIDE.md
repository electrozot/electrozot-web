# SEO & Blog System Setup Guide

## ✅ What's Been Added

### 1. SEO Features
- ✅ Meta OG tags (Facebook, Twitter)
- ✅ Schema.org JSON-LD markup
- ✅ Canonical URLs
- ✅ SEO-friendly meta descriptions
- ✅ Robots meta tags

### 2. Admin Panel Hidden from Google
- ✅ robots.txt blocks /admin, /usr, /tech
- ✅ Meta robots noindex tags added
- ✅ Admin stays at /admin (no URL change needed)
- ✅ Google won't index admin/user/tech portals

### 3. Blog System
- ✅ Full blog management in admin panel
- ✅ SEO settings per blog post
- ✅ Categories and tags
- ✅ Featured images
- ✅ View counter
- ✅ Draft/Published/Archived status

---

## 🚀 Setup Instructions

### Step 1: Import Blog Database
```bash
mysql -u username -p database_name < "DATABASE FILE/blog_system.sql"
```

Or import through phpMyAdmin.

### Step 2: Create Blog Upload Folder
```bash
mkdir uploads/blog
chmod 777 uploads/blog
```

### Step 3: Access Admin Panel
**URL:** `http://yoursite.com/admin` (same as before)  
**Note:** Admin panel is now hidden from Google search results

### Step 4: Add Blog Menu to Admin Sidebar
Edit `admin/vendor/inc/sidebar.php` and add:
```php
<li class="nav-item">
    <a class="nav-link" href="admin-manage-blog.php">
        <i class="fas fa-blog"></i>
        <span>Manage Blog</span>
    </a>
</li>
```

### Step 5: Add Blog Link to Main Navigation
Edit `vendor/inc/nav.php` and add:
```php
<li class="nav-item">
    <a class="nav-link" href="blog.php">Blog</a>
</li>
```

---

## 📝 How to Use

### Adding SEO Meta to Pages

In any PHP page, before including head.php:

```php
<?php
// Set page-specific SEO
$seo_title = "Your Page Title | Electrozot";
$seo_description = "Your page description here";
$seo_keywords = "keyword1, keyword2, keyword3";
$seo_image = "https://yoursite.com/path/to/image.jpg"; // Optional
?>
<!DOCTYPE html>
<html>
<?php include("vendor/inc/head.php"); ?>
<head>
    <?php include("vendor/inc/seo-meta.php"); ?>
</head>
```

### Creating Blog Posts

1. Go to `/ez-admin-panel`
2. Click "Manage Blog"
3. Click "Add New Post"
4. Fill in:
   - Title (auto-generates SEO-friendly slug)
   - Content
   - Category
   - Tags
   - Featured Image
   - SEO settings
5. Set status to "Published"
6. Click "Publish Post"

### Blog URLs

- **Blog List:** `http://yoursite.com/blog`
- **Single Post:** `http://yoursite.com/blog/1/your-post-title`

---

## 🔒 Security Features

### .htaccess Protection
- ✅ Admin route hidden
- ✅ Directory listing disabled
- ✅ Security headers added
- ✅ SEO-friendly URLs

### Admin Access Protection
Admin panel is blocked from Google via:
1. `robots.txt` - Disallows /admin, /usr, /tech
2. Meta robots tags - noindex, nofollow on all admin pages
3. Security headers in .htaccess

---

## 📊 SEO Best Practices

### For Each Page:
1. Set unique title (50-60 characters)
2. Write compelling description (150-160 characters)
3. Add relevant keywords
4. Use proper heading structure (H1, H2, H3)
5. Add alt text to images

### For Blog Posts:
1. Use descriptive titles
2. Write quality content (500+ words)
3. Add relevant images
4. Use categories and tags
5. Fill SEO meta fields
6. Share on social media (OG tags will work)

---

## 🎨 Customization

### Change Admin Path
Edit `.htaccess` line 4-6:
```apache
RewriteRule ^YOUR-CUSTOM-PATH/?$ admin/index.php [L]
```

### Update SEO Defaults
Edit `vendor/inc/seo-meta.php`:
- Default title
- Default description
- Twitter handle
- Schema.org data

### Blog Styling
Edit `blog.php` and `blog-post.php` CSS classes.

---

## 📱 Features

- ✅ Mobile responsive
- ✅ PWA compatible
- ✅ Fast loading
- ✅ SEO optimized
- ✅ Social media ready
- ✅ Schema markup
- ✅ Secure admin access

---

## 🔗 Important URLs

- **Homepage:** `/`
- **Blog:** `/blog`
- **Admin Panel:** `/admin` (hidden from Google)
- **Services:** `/services`
- **Contact:** `/contact`
- **About:** `/about`

---

## 📞 Support

For issues or questions, check the code comments or contact your developer.
