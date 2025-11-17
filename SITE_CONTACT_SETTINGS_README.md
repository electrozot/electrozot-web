# 📞 Centralized Site Contact Settings System

## Overview

A professional contact management system that allows admins to update phone numbers, WhatsApp, email, Instagram, and other contact information from a single admin panel. Changes automatically reflect across the entire website.

## ✨ Features

- ✅ **Centralized Management** - Update once, reflect everywhere
- ✅ **Admin Panel** - Beautiful, user-friendly interface
- ✅ **Multiple Contacts** - Primary & secondary phone/email
- ✅ **Social Media** - Instagram, Facebook, Twitter integration
- ✅ **WhatsApp Integration** - Auto-generate WhatsApp links
- ✅ **Link Generators** - Automatic tel:, mailto:, WhatsApp links
- ✅ **Input Validation** - Phone, email, URL validation
- ✅ **Performance Optimized** - Built-in caching
- ✅ **Mobile Responsive** - Works on all devices
- ✅ **Easy Integration** - Simple helper functions

## 🚀 Quick Start (3 Steps)

### 1. Run Setup
```
http://yourwebsite.com/admin/setup-site-settings.php
```

### 2. Update Settings
```
Admin Dashboard → Settings → Site Contact Info
```

### 3. Use in Your Code
```php
<?php
include('admin/vendor/inc/site-settings-helper.php');
$phone = get_primary_phone($mysqli);
?>
<a href="tel:<?php echo $phone; ?>">Call <?php echo $phone; ?></a>
```

## 📁 Files Created

| File | Purpose |
|------|---------|
| `admin/setup-site-settings.php` | One-time database setup |
| `admin/admin-site-settings.php` | Admin panel for managing settings |
| `admin/test-site-settings.php` | Test page to verify system works |
| `admin/vendor/inc/site-settings-helper.php` | Helper functions library |
| `admin/example-index-update.php` | Example code snippets |
| `admin/SITE_SETTINGS_USAGE_GUIDE.md` | Detailed usage documentation |
| `SITE_SETTINGS_INSTALLATION.md` | Complete installation guide |

## 🎯 Available Settings

### Business Information
- Business Name
- Business Tagline

### Contact Information
- Primary Phone Number
- Secondary Phone Number
- WhatsApp Number
- Primary Email
- Secondary Email
- Business Address

### Social Media
- Instagram Handle
- Facebook Page URL
- Twitter Handle

## 💻 Helper Functions

### Quick Access
```php
get_primary_phone($mysqli)      // Primary phone
get_secondary_phone($mysqli)    // Secondary phone
get_whatsapp($mysqli)           // WhatsApp number
get_primary_email($mysqli)      // Primary email
get_secondary_email($mysqli)    // Secondary email
get_instagram($mysqli)          // Instagram handle
get_facebook($mysqli)           // Facebook page
get_twitter($mysqli)            // Twitter handle
get_business_address($mysqli)   // Business address
get_business_name($mysqli)      // Business name
get_business_tagline($mysqli)   // Business tagline
```

### Link Generators
```php
get_phone_link($mysqli, 'primary')              // tel: link
get_email_link($mysqli, 'primary', 'Subject')   // mailto: link
get_whatsapp_link($mysqli, 'Message')           // WhatsApp link
```

### Utilities
```php
format_phone($phone)                    // Format phone display
get_setting($mysqli, 'key', 'default')  // Get any setting
get_all_settings($mysqli)               // Get all settings
```

## 📝 Usage Examples

### Display Phone Number
```php
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>
<a href="<?php echo get_phone_link($mysqli); ?>">
    <i class="fas fa-phone"></i> <?php echo get_primary_phone($mysqli); ?>
</a>
```

### WhatsApp Button
```php
<a href="<?php echo get_whatsapp_link($mysqli, 'Hi! I need help'); ?>" 
   class="btn btn-whatsapp">
    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
</a>
```

### Email Link
```php
<a href="<?php echo get_email_link($mysqli, 'primary', 'Service Inquiry'); ?>">
    <i class="fas fa-envelope"></i> <?php echo get_primary_email($mysqli); ?>
</a>
```

### Social Media Links
```php
<?php 
$instagram = get_instagram($mysqli);
if(!empty($instagram)): 
?>
    <a href="https://instagram.com/<?php echo ltrim($instagram, '@'); ?>" 
       target="_blank">
        <i class="fab fa-instagram"></i> <?php echo $instagram; ?>
    </a>
<?php endif; ?>
```

### Complete Contact Section
```php
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>

<div class="contact-info">
    <!-- Phone -->
    <div class="contact-item">
        <i class="fas fa-phone"></i>
        <a href="<?php echo get_phone_link($mysqli); ?>">
            <?php echo format_phone(get_primary_phone($mysqli)); ?>
        </a>
    </div>
    
    <!-- Email -->
    <div class="contact-item">
        <i class="fas fa-envelope"></i>
        <a href="<?php echo get_email_link($mysqli); ?>">
            <?php echo get_primary_email($mysqli); ?>
        </a>
    </div>
    
    <!-- WhatsApp -->
    <div class="contact-item">
        <i class="fab fa-whatsapp"></i>
        <a href="<?php echo get_whatsapp_link($mysqli); ?>">
            <?php echo format_phone(get_whatsapp($mysqli)); ?>
        </a>
    </div>
    
    <!-- Address -->
    <div class="contact-item">
        <i class="fas fa-map-marker-alt"></i>
        <?php echo get_business_address($mysqli); ?>
    </div>
</div>
```

## 🎨 Admin Panel Features

- **Grouped Settings** - Organized by category
- **Visual Icons** - Easy identification
- **Input Validation** - Automatic validation
- **Active Indicators** - Shows which fields are set
- **Responsive Design** - Mobile-friendly
- **Success Messages** - Confirmation feedback
- **Beautiful UI** - Modern gradient design

## 🗄️ Database Structure

**Table:** `tms_site_settings`

```sql
CREATE TABLE tms_site_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_label VARCHAR(255),
    setting_type VARCHAR(50) DEFAULT 'text',
    setting_group VARCHAR(100) DEFAULT 'general',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🔧 Testing

Visit the test page to verify everything works:
```
http://yourwebsite.com/admin/test-site-settings.php
```

The test page checks:
- ✅ Database table exists
- ✅ Settings are loaded
- ✅ Helper functions work
- ✅ Link generators work
- ✅ Performance is optimal

## 📚 Documentation

- **Installation Guide:** `SITE_SETTINGS_INSTALLATION.md`
- **Usage Guide:** `admin/SITE_SETTINGS_USAGE_GUIDE.md`
- **Example Code:** `admin/example-index-update.php`

## 🔄 Migration Path

### Step 1: Identify Hardcoded Values
Search your codebase for:
- Phone numbers (e.g., `7559606925`)
- Email addresses
- Social media handles

### Step 2: Replace with Helper Functions
```php
// Before
<a href="tel:7559606925">Call 7559606925</a>

// After
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>
<a href="<?php echo get_phone_link($mysqli); ?>">
    Call <?php echo get_primary_phone($mysqli); ?>
</a>
```

### Step 3: Test
1. Update settings in admin panel
2. Verify changes appear on website
3. Test all contact links work

## 🎯 Files to Update

Common files with contact information:
- ✅ `index.php` - Homepage
- ✅ `contact.php` - Contact page
- ✅ `about.php` - About page
- ✅ Footer includes
- ✅ Header includes
- ✅ Email templates

## 🔐 Security

- ✅ Admin authentication required
- ✅ SQL injection protection
- ✅ Input validation
- ✅ Prepared statements
- ✅ Session-based access control

## 🚀 Performance

- ✅ Built-in caching
- ✅ Single query for all settings
- ✅ Optimized database structure
- ✅ Minimal overhead

## 💡 Benefits

1. **No Code Changes** - Update contact info without editing code
2. **Consistency** - Same information everywhere
3. **Easy Maintenance** - Non-technical staff can update
4. **Professional** - Enterprise-level management
5. **Time Saving** - Update once instead of multiple files
6. **Error Prevention** - No typos across different pages

## 🆘 Troubleshooting

### Settings page not loading?
```bash
# Run setup first
http://yourwebsite.com/admin/setup-site-settings.php
```

### Changes not showing?
```php
// Make sure helper is included
include('admin/vendor/inc/site-settings-helper.php');
```

### Table doesn't exist?
```bash
# Run setup again
http://yourwebsite.com/admin/setup-site-settings.php
```

## 📞 Support

For detailed help, see:
- `SITE_SETTINGS_INSTALLATION.md` - Installation steps
- `admin/SITE_SETTINGS_USAGE_GUIDE.md` - Usage examples
- `admin/test-site-settings.php` - System test

## ✅ Checklist

- [ ] Run `setup-site-settings.php`
- [ ] Update settings in admin panel
- [ ] Include helper in your files
- [ ] Replace hardcoded values
- [ ] Test all contact links
- [ ] Update footer/header
- [ ] Train staff on admin panel

---

**Made with ❤️ for easy contact management**
