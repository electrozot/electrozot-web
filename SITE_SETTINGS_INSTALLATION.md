# 📞 Site Contact Settings - Installation Guide

## What This Does

This system allows you to manage all your website contact information (phone numbers, WhatsApp, email, Instagram, etc.) from one central admin panel. When you update the information in the admin panel, it automatically reflects everywhere on your website.

## ✅ Benefits

- **Update Once, Reflect Everywhere** - No need to edit multiple files
- **Admin-Friendly** - Non-technical staff can update contact info
- **Consistent Information** - Same contact details across all pages
- **Easy Maintenance** - No code changes needed for contact updates
- **Professional** - Centralized management like enterprise systems

## 🚀 Quick Installation (3 Steps)

### Step 1: Run Setup File
1. Open your browser
2. Go to: `http://yourwebsite.com/admin/setup-site-settings.php`
3. Wait for success message
4. Click "Go to Site Settings"

**That's it!** The database table is created and default settings are inserted.

### Step 2: Update Your Contact Information
1. Login to Admin Panel
2. Go to: **Dashboard → Settings → Site Contact Info**
3. Update your information:
   - Primary Phone Number
   - Secondary Phone Number (optional)
   - WhatsApp Number
   - Primary Email
   - Secondary Email (optional)
   - Instagram Handle
   - Facebook Page (optional)
   - Twitter Handle (optional)
   - Business Address
   - Business Name
   - Business Tagline
4. Click **"Save All Changes"**

### Step 3: Update Your Website Files
Replace hardcoded contact information with dynamic settings.

#### Example: Update index.php

**Add at the top (after config.php):**
```php
<?php
  session_start();
  include('admin/vendor/inc/config.php');
  include('admin/vendor/inc/site-settings-helper.php'); // ADD THIS LINE
?>
```

**Replace hardcoded phone number:**

Before:
```php
<a href="tel:7559606925">Call 7559606925</a>
```

After:
```php
<?php $phone = get_primary_phone($mysqli); ?>
<a href="tel:<?php echo $phone; ?>">Call <?php echo $phone; ?></a>
```

Or use the helper function directly:
```php
<a href="<?php echo get_phone_link($mysqli); ?>">
    Call <?php echo get_primary_phone($mysqli); ?>
</a>
```

## 📝 Files Created

1. **admin/setup-site-settings.php** - One-time setup script
2. **admin/admin-site-settings.php** - Admin panel for managing settings
3. **admin/vendor/inc/site-settings-helper.php** - Helper functions
4. **admin/SITE_SETTINGS_USAGE_GUIDE.md** - Detailed usage guide
5. **admin/example-index-update.php** - Example code snippets

## 🎯 Quick Usage Examples

### Display Phone Number
```php
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>
<a href="<?php echo get_phone_link($mysqli); ?>">
    <?php echo get_primary_phone($mysqli); ?>
</a>
```

### Display Email
```php
<a href="<?php echo get_email_link($mysqli); ?>">
    <?php echo get_primary_email($mysqli); ?>
</a>
```

### WhatsApp Link with Message
```php
<a href="<?php echo get_whatsapp_link($mysqli, 'Hi! I need help'); ?>">
    WhatsApp Us
</a>
```

### Display Instagram
```php
<?php 
$instagram = get_instagram($mysqli);
if(!empty($instagram)): 
?>
    <a href="https://instagram.com/<?php echo ltrim($instagram, '@'); ?>">
        <?php echo $instagram; ?>
    </a>
<?php endif; ?>
```

### Display Business Info
```php
<h1><?php echo get_business_name($mysqli); ?></h1>
<p><?php echo get_business_tagline($mysqli); ?></p>
<p><?php echo get_business_address($mysqli); ?></p>
```

## 🔧 Available Helper Functions

### Contact Information
- `get_primary_phone($mysqli)` - Primary phone number
- `get_secondary_phone($mysqli)` - Secondary phone number
- `get_whatsapp($mysqli)` - WhatsApp number
- `get_primary_email($mysqli)` - Primary email
- `get_secondary_email($mysqli)` - Secondary email
- `get_business_address($mysqli)` - Business address

### Social Media
- `get_instagram($mysqli)` - Instagram handle
- `get_facebook($mysqli)` - Facebook page URL
- `get_twitter($mysqli)` - Twitter handle

### Business Information
- `get_business_name($mysqli)` - Business name
- `get_business_tagline($mysqli)` - Business tagline

### Link Generators
- `get_phone_link($mysqli, 'primary')` - Generate tel: link
- `get_email_link($mysqli, 'primary', 'Subject')` - Generate mailto: link
- `get_whatsapp_link($mysqli, 'Message')` - Generate WhatsApp link

### Utilities
- `format_phone($phone)` - Format phone number (12345 67890)
- `get_setting($mysqli, 'key', 'default')` - Get any setting
- `get_all_settings($mysqli)` - Get all settings as array

## 📂 Files to Update

Replace hardcoded contact information in these files:

1. ✅ **index.php** - Homepage
2. ✅ **contact.php** - Contact page
3. ✅ **about.php** - About page
4. ✅ **Footer includes** - Footer files
5. ✅ **Header includes** - Header files
6. ✅ **Email templates** - Email signatures

## 🎨 Admin Panel Features

- **Grouped Settings** - Organized by Business Info, Contact, and Social Media
- **Visual Icons** - Easy to identify each field
- **Input Validation** - Phone numbers must be 10 digits
- **Active Indicators** - Shows which fields have values
- **Responsive Design** - Works on mobile and desktop
- **Success Messages** - Confirms when settings are saved

## 🗄️ Database Structure

**Table:** `tms_site_settings`

| Field | Type | Description |
|-------|------|-------------|
| setting_id | INT | Auto-increment ID |
| setting_key | VARCHAR(100) | Unique key (e.g., contact_phone_1) |
| setting_value | TEXT | The actual value |
| setting_label | VARCHAR(255) | Display label in admin |
| setting_type | VARCHAR(50) | Input type (text, tel, email, url) |
| setting_group | VARCHAR(100) | Group (general, contact, social) |
| display_order | INT | Display order in admin |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## 🔍 Troubleshooting

### Settings page not loading?
1. Make sure you ran `setup-site-settings.php` first
2. Check database connection in `admin/vendor/inc/config.php`
3. Verify MySQL user has CREATE TABLE permissions

### Changes not showing on website?
1. Clear browser cache (Ctrl+F5)
2. Make sure you included `site-settings-helper.php` in your file
3. Check if you're using the helper functions correctly

### "Table doesn't exist" error?
Run the setup file again: `admin/setup-site-settings.php`

### Want to add new settings?
Option 1: Add via database:
```sql
INSERT INTO tms_site_settings 
(setting_key, setting_value, setting_label, setting_type, setting_group, display_order) 
VALUES ('new_field', 'value', 'Label', 'text', 'contact', 99);
```

Option 2: Edit `admin/setup-site-settings.php` and add to `$default_settings` array

## 📱 Mobile Admin Access

The admin panel is fully responsive. You can update contact information from:
- Desktop computer
- Tablet
- Mobile phone

## 🔐 Security

- Only logged-in admins can access settings
- SQL injection protection with prepared statements
- Input validation for phone numbers and emails
- Session-based authentication

## 🎓 Learning Resources

- **Full Usage Guide:** `admin/SITE_SETTINGS_USAGE_GUIDE.md`
- **Example Code:** `admin/example-index-update.php`
- **Helper Functions:** `admin/vendor/inc/site-settings-helper.php`

## 💡 Pro Tips

1. **Test First** - Update one page first, test it, then update others
2. **Backup** - Backup your files before making changes
3. **Use Helpers** - Always use helper functions instead of direct queries
4. **Cache Friendly** - Helper functions cache results for performance
5. **Consistent Format** - Use `format_phone()` for consistent phone display

## 🆘 Need Help?

1. Check `SITE_SETTINGS_USAGE_GUIDE.md` for detailed examples
2. Look at `example-index-update.php` for code snippets
3. Verify database table exists: `SHOW TABLES LIKE 'tms_site_settings'`
4. Check PHP error logs for specific errors

## ✨ What's Next?

After installation:
1. ✅ Update all contact information in admin panel
2. ✅ Replace hardcoded values in index.php
3. ✅ Update contact.php page
4. ✅ Update footer and header includes
5. ✅ Test all contact links (phone, email, WhatsApp)
6. ✅ Train staff on how to update settings

---

**Congratulations!** 🎉 You now have a professional centralized contact management system!
