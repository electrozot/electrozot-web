# Cleanup Instructions

## Files with "vehicle" in name that should be renamed:
- `admin/admin-add-vehicle.php` → `admin/admin-add-technician.php`
- `admin/admin-view-vehicle.php` → `admin/admin-view-technician.php`
- `admin/admin-manage-vehicle.php` → `admin/admin-manage-technician.php`
- `admin/admin-manage-single-vehicle.php` → `admin/admin-manage-single-technician.php`
- `usr/usr-book-vehicle.php` → `usr/usr-book-technician.php`

**Note:** After renaming, update all references in:
- Sidebar files (`admin/vendor/inc/sidebar.php`, `usr/vendor/inc/sidebar.php`)
- Dashboard files
- Any other files that link to these pages

## Copyright Comments Removed From:
- ✅ `vendor/inc/head.php` - Updated title to "Technician Booking System"
- ✅ `vendor/inc/footer.php` - Updated copyright
- ✅ `usr/vendor/inc/footer.php` - Updated copyright
- ✅ `admin/vendor/inc/footer.php` - Updated copyright
- ✅ `admin/admin-add-vehicle.php` - Removed all copyright comments
- ✅ `admin/admin-dashboard.php` - Removed marquee and copyright comments

## Remaining Files to Clean:
Many PHP files still contain copyright comments. To remove them all, you can:

1. Use a text editor with find/replace across all files
2. Search for: `<!-- Author By: MH RONY` and replace with empty string
3. Search for marquee messages containing "Code Camp BD" or "MH RONY" and remove them
4. Update any remaining "Online Car Booking System" or "Vehicle Booking System" to "Technician Booking System"

## Key Updates Made:
- Database schema converted from vehicle to technician
- All admin and user panel files updated
- Footer copyrights updated to "Electrozot - Technician Booking System"
- Page titles updated to "Technician Booking System"
- Service categories changed from vehicle types to technician categories

