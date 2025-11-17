# Site Settings Usage Guide

## Overview
The centralized contact management system allows you to update contact information in one place and have it reflect everywhere across your website.

## Setup Instructions

### 1. Run the Setup File (One Time Only)
Visit: `admin/setup-site-settings.php`

This will:
- Create the `tms_site_settings` database table
- Insert default contact information
- Set up the system for use

### 2. Access Admin Panel
Go to: **Admin Dashboard → Settings → Site Contact Info**

Update your:
- Phone numbers (Primary & Secondary)
- WhatsApp number
- Email addresses
- Instagram, Facebook, Twitter handles
- Business address
- Business name and tagline

## How to Use in Your PHP Files

### Method 1: Include Helper Functions (Recommended)

```php
<?php
// Include at the top of your file
include('admin/vendor/inc/config.php');
include('admin/vendor/inc/site-settings-helper.php');

// Use the quick access functions
$phone = get_primary_phone($mysqli);
$whatsapp = get_whatsapp($mysqli);
$email = get_primary_email($mysqli);
$instagram = get_instagram($mysqli);
$address = get_business_address($mysqli);
$business_name = get_business_name($mysqli);
?>

<!-- Display in HTML -->
<a href="tel:<?php echo $phone; ?>">Call <?php echo $phone; ?></a>
<a href="<?php echo get_whatsapp_link($mysqli, 'Hello!'); ?>">WhatsApp Us</a>
<a href="mailto:<?php echo $email; ?>">Email Us</a>
```

### Method 2: Direct Database Query

```php
<?php
include('admin/vendor/inc/config.php');
include('admin/vendor/inc/site-settings-helper.php');

// Get a specific setting
$phone = get_setting($mysqli, 'contact_phone_1', '7559606925');

// Get all settings at once
$settings = get_all_settings($mysqli);
echo $settings['contact_phone_1'];
echo $settings['contact_email_1'];
echo $settings['contact_whatsapp'];
?>
```

## Available Helper Functions

### Quick Access Functions
- `get_primary_phone($mysqli)` - Primary phone number
- `get_secondary_phone($mysqli)` - Secondary phone number
- `get_whatsapp($mysqli)` - WhatsApp number
- `get_primary_email($mysqli)` - Primary email
- `get_secondary_email($mysqli)` - Secondary email
- `get_instagram($mysqli)` - Instagram handle
- `get_facebook($mysqli)` - Facebook page
- `get_twitter($mysqli)` - Twitter handle
- `get_business_address($mysqli)` - Business address
- `get_business_name($mysqli)` - Business name
- `get_business_tagline($mysqli)` - Business tagline

### Link Generation Functions
- `get_whatsapp_link($mysqli, $message)` - Generate WhatsApp link with optional message
- `get_phone_link($mysqli, 'primary')` - Generate tel: link
- `get_email_link($mysqli, 'primary', $subject)` - Generate mailto: link

### Utility Functions
- `get_setting($mysqli, $key, $default)` - Get any setting by key
- `get_all_settings($mysqli)` - Get all settings as array
- `get_settings_by_group($mysqli, $group)` - Get settings by group (contact, social, general)
- `format_phone($phone)` - Format phone number for display

## Example: Update Homepage

### Before (Hardcoded):
```php
<a href="tel:7559606925">Call 7559606925</a>
```

### After (Dynamic):
```php
<?php include('admin/vendor/inc/site-settings-helper.php'); ?>
<a href="<?php echo get_phone_link($mysqli); ?>">
    Call <?php echo get_primary_phone($mysqli); ?>
</a>
```

## Example: Contact Page

```php
<?php
include('admin/vendor/inc/config.php');
include('admin/vendor/inc/site-settings-helper.php');

$phone1 = get_primary_phone($mysqli);
$phone2 = get_secondary_phone($mysqli);
$email1 = get_primary_email($mysqli);
$whatsapp = get_whatsapp($mysqli);
$instagram = get_instagram($mysqli);
$address = get_business_address($mysqli);
?>

<div class="contact-info">
    <h3>Contact Us</h3>
    
    <p><i class="fas fa-phone"></i> 
        <a href="<?php echo get_phone_link($mysqli); ?>">
            <?php echo format_phone($phone1); ?>
        </a>
    </p>
    
    <?php if(!empty($phone2)): ?>
    <p><i class="fas fa-phone"></i> 
        <a href="<?php echo get_phone_link($mysqli, 'secondary'); ?>">
            <?php echo format_phone($phone2); ?>
        </a>
    </p>
    <?php endif; ?>
    
    <p><i class="fas fa-envelope"></i> 
        <a href="<?php echo get_email_link($mysqli); ?>">
            <?php echo $email1; ?>
        </a>
    </p>
    
    <p><i class="fab fa-whatsapp"></i> 
        <a href="<?php echo get_whatsapp_link($mysqli, 'Hi, I need help!'); ?>">
            WhatsApp: <?php echo format_phone($whatsapp); ?>
        </a>
    </p>
    
    <?php if(!empty($instagram)): ?>
    <p><i class="fab fa-instagram"></i> 
        <a href="https://instagram.com/<?php echo ltrim($instagram, '@'); ?>">
            <?php echo $instagram; ?>
        </a>
    </p>
    <?php endif; ?>
    
    <p><i class="fas fa-map-marker-alt"></i> <?php echo $address; ?></p>
</div>
```

## Example: Footer (Global)

```php
<?php
include_once('admin/vendor/inc/site-settings-helper.php');
$phone = get_primary_phone($mysqli);
$email = get_primary_email($mysqli);
$whatsapp = get_whatsapp($mysqli);
?>

<footer>
    <div class="footer-contact">
        <a href="<?php echo get_phone_link($mysqli); ?>">
            <i class="fas fa-phone"></i> <?php echo $phone; ?>
        </a>
        <a href="<?php echo get_email_link($mysqli); ?>">
            <i class="fas fa-envelope"></i> <?php echo $email; ?>
        </a>
        <a href="<?php echo get_whatsapp_link($mysqli); ?>">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
    </div>
</footer>
```

## Files to Update

Replace hardcoded contact information in these files:

1. **index.php** - Homepage phone numbers, WhatsApp links
2. **contact.php** - Contact page information
3. **about.php** - About page contact details
4. **Footer includes** - Any footer files with contact info
5. **Header includes** - Any header files with contact info
6. **Email templates** - Signature contact information

## Database Structure

Table: `tms_site_settings`

| Column | Type | Description |
|--------|------|-------------|
| setting_id | INT | Primary key |
| setting_key | VARCHAR(100) | Unique setting identifier |
| setting_value | TEXT | Setting value |
| setting_label | VARCHAR(255) | Display label |
| setting_type | VARCHAR(50) | Input type (text, tel, email, url, textarea) |
| setting_group | VARCHAR(100) | Group (general, contact, social) |
| display_order | INT | Display order in admin |
| created_at | TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | Last update time |

## Benefits

✅ **Centralized Management** - Update once, reflect everywhere
✅ **No Code Changes** - Change contact info without editing code
✅ **Easy Maintenance** - Admin can update without developer help
✅ **Consistent Information** - Same info across all pages
✅ **Future Proof** - Easy to add new settings
✅ **Type Safety** - Proper validation for phone, email, URL fields

## Troubleshooting

### Settings not showing?
1. Make sure you ran `setup-site-settings.php` first
2. Check database connection in `config.php`
3. Verify table `tms_site_settings` exists

### Changes not reflecting?
1. Clear browser cache
2. Check if helper file is included
3. Verify database was updated in admin panel

### Need to add new settings?
1. Go to admin panel → Settings → Site Contact Info
2. Or manually insert into database:
```sql
INSERT INTO tms_site_settings (setting_key, setting_value, setting_label, setting_type, setting_group, display_order) 
VALUES ('new_setting', 'value', 'Label', 'text', 'contact', 99);
```

## Support

For issues or questions, check:
- Database connection in `admin/vendor/inc/config.php`
- Helper functions in `admin/vendor/inc/site-settings-helper.php`
- Admin panel at `admin/admin-site-settings.php`
