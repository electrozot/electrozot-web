# 🚀 START HERE - Contact Settings System

## What You Got

A complete system to manage all your website contact information (phone, WhatsApp, email, Instagram, etc.) from one admin panel. Update once, changes reflect everywhere automatically!

---

## ⚡ 3-Step Quick Start

### Step 1: Setup Database (1 minute)
Open in browser:
```
http://yourwebsite.com/admin/setup-site-settings.php
```
✅ Creates database table  
✅ Inserts default settings  
✅ Ready to use!

### Step 2: Update Your Info (2 minutes)
1. Login to Admin Panel
2. Go to: **Settings → Site Contact Info**
3. Update your contact details
4. Click **Save All Changes**

### Step 3: Test It (1 minute)
Open in browser:
```
http://yourwebsite.com/admin/test-site-settings.php
```
✅ Verifies everything works  
✅ Shows all your settings  
✅ Tests link generators

---

## 📱 What You Can Manage

From the admin panel, you can update:

- ☎️ Primary Phone Number
- ☎️ Secondary Phone Number
- 💬 WhatsApp Number
- 📧 Primary Email
- 📧 Secondary Email
- 📷 Instagram Handle
- 👍 Facebook Page
- 🐦 Twitter Handle
- 📍 Business Address
- 🏢 Business Name
- 💼 Business Tagline

---

## 💻 How to Use in Your Code

### Simple Example
```php
<?php
// Add this at the top of your PHP file
include('admin/vendor/inc/site-settings-helper.php');

// Get phone number
$phone = get_primary_phone($mysqli);
?>

<!-- Use in HTML -->
<a href="tel:<?php echo $phone; ?>">Call <?php echo $phone; ?></a>
```

### Complete Example
```php
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>

<!-- Phone Link -->
<a href="<?php echo get_phone_link($mysqli); ?>">
    <i class="fas fa-phone"></i> <?php echo get_primary_phone($mysqli); ?>
</a>

<!-- Email Link -->
<a href="<?php echo get_email_link($mysqli); ?>">
    <i class="fas fa-envelope"></i> <?php echo get_primary_email($mysqli); ?>
</a>

<!-- WhatsApp Link -->
<a href="<?php echo get_whatsapp_link($mysqli, 'Hi! I need help'); ?>">
    <i class="fab fa-whatsapp"></i> WhatsApp Us
</a>
```

---

## 🎯 Quick Functions Reference

```php
// Contact Info
get_primary_phone($mysqli)      // Primary phone
get_whatsapp($mysqli)           // WhatsApp number
get_primary_email($mysqli)      // Primary email
get_business_address($mysqli)   // Address

// Social Media
get_instagram($mysqli)          // Instagram
get_facebook($mysqli)           // Facebook
get_twitter($mysqli)            // Twitter

// Link Generators
get_phone_link($mysqli)                         // tel: link
get_email_link($mysqli, 'primary', 'Subject')   // mailto: link
get_whatsapp_link($mysqli, 'Message')           // WhatsApp link

// Utilities
format_phone($phone)            // Format phone display
```

---

## 📂 Files Created

| File | What It Does |
|------|--------------|
| `admin/setup-site-settings.php` | ⚙️ One-time setup |
| `admin/admin-site-settings.php` | 🎨 Admin panel |
| `admin/test-site-settings.php` | ✅ Test page |
| `admin/vendor/inc/site-settings-helper.php` | 🔧 Helper functions |
| `SITE_SETTINGS_INSTALLATION.md` | 📖 Full guide |
| `admin/SITE_SETTINGS_USAGE_GUIDE.md` | 📚 Usage examples |
| `admin/example-index-update.php` | 💡 Code examples |

---

## 🔄 Update Your Website

### Find & Replace Hardcoded Values

**Before (Hardcoded):**
```php
<a href="tel:7559606925">Call 7559606925</a>
```

**After (Dynamic):**
```php
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>
<a href="<?php echo get_phone_link($mysqli); ?>">
    Call <?php echo get_primary_phone($mysqli); ?>
</a>
```

### Files to Update
- ✅ `index.php` - Homepage
- ✅ `contact.php` - Contact page
- ✅ `about.php` - About page
- ✅ Footer includes
- ✅ Header includes

---

## ✨ Benefits

✅ **Update Once** - Changes reflect everywhere  
✅ **No Coding** - Admin can update without developer  
✅ **Consistent** - Same info across all pages  
✅ **Professional** - Enterprise-level management  
✅ **Time Saving** - No more editing multiple files  

---

## 🎓 Learn More

- **Quick Start:** You're reading it! ✅
- **Full Installation:** `SITE_SETTINGS_INSTALLATION.md`
- **Usage Guide:** `admin/SITE_SETTINGS_USAGE_GUIDE.md`
- **Code Examples:** `admin/example-index-update.php`

---

## 🆘 Need Help?

### Settings not showing?
Run setup first: `admin/setup-site-settings.php`

### Changes not reflecting?
Make sure you included the helper:
```php
include('admin/vendor/inc/site-settings-helper.php');
```

### Want to test?
Visit: `admin/test-site-settings.php`

---

## ✅ Your Action Plan

1. ✅ **Run Setup** → `admin/setup-site-settings.php`
2. ✅ **Update Info** → Admin Panel → Settings → Site Contact Info
3. ✅ **Test System** → `admin/test-site-settings.php`
4. ✅ **Update index.php** → Replace hardcoded phone number
5. ✅ **Update other pages** → Contact, About, Footer
6. ✅ **Test website** → Verify all contact links work

---

## 🎉 That's It!

You now have a professional contact management system!

**Next Steps:**
1. Run the setup
2. Update your contact info in admin
3. Start using the helper functions

**Questions?** Check the detailed guides in the documentation files.

---

**Made with ❤️ for easy website management**
