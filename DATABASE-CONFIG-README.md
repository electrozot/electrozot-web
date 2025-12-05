# Database Configuration Guide

## Single Configuration File

All database settings are now centralized in one file: `/config.php`

### How to Change Database Settings

**Edit only ONE file:** `/config.php` in the root folder

```php
// LOCAL DEVELOPMENT
$dbuser = "root";
$dbpass = "";
$host = "localhost";
$db = "electrozot_db";

// PRODUCTION (Uncomment these and comment local settings)
// $dbuser = "u848820288_Mohit";
// $dbpass = "Moh2020@#@";
// $host = "localhost";
// $db = "u848820288_electrozot";
```

### What Uses This Config?

- ✅ Admin Panel (`/admin/`)
- ✅ User Portal (`/usr/`)
- ✅ Technician Portal (`/tech/`)
- ✅ Guest Booking (`/process-guest-booking.php`)
- ✅ All API endpoints

### Switching Between Local and Production

**For Local Development:**
1. Open `/config.php`
2. Make sure local settings are uncommented
3. Comment out production settings

**For Production Deployment:**
1. Open `/config.php`
2. Comment out local settings
3. Uncomment production settings

That's it! No need to change multiple files anymore.

### Files That Reference Central Config

- `/admin/vendor/inc/config.php` → Points to `/config.php`
- `/usr/vendor/inc/config.php` → Points to `/config.php`
- `/tech/` files → Use admin config which points to `/config.php`

---

**Note:** Never commit production credentials to Git. Add `/config.php` to `.gitignore` if needed.
