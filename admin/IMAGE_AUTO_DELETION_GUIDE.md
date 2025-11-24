# Automatic Image Deletion System

## Overview
Service completion and bill images are automatically managed with time-based visibility and deletion:

- **Customer View**: Images hidden after 31 days
- **Admin View**: Images hidden after 40 days  
- **Physical Deletion**: Images permanently deleted after 40 days

**Important**: No messages are shown to users about deletion. Images simply become unavailable after the specified period.

## How It Works

### 1. Image Visibility Control
The system uses `image-visibility-helper.php` to control when images are displayed:

```php
// Customer view - hide after 31 days
isImageVisible($completed_date, 'customer')

// Admin view - hide after 40 days
isImageVisible($completed_date, 'admin')
```

### 2. Automatic Deletion
The cron job `cron-delete-old-images.php` runs daily to:
- Find completed bookings older than 40 days
- Delete physical image files from server
- Clear image references in database
- Log all actions

## Setup Instructions

### Step 1: Run Setup Script
Visit: `http://yourdomain.com/admin/setup-image-auto-deletion.php`

This will:
- Add `sb_completed_date` column to database
- Create necessary directories
- Update existing completed bookings

### Step 2: Configure Cron Job

**Option A: Linux Cron (Recommended)**
```bash
# Edit crontab
crontab -e

# Add this line (runs daily at 2 AM)
0 2 * * * /usr/bin/php /path/to/admin/cron-delete-old-images.php
```

**Option B: Web-based Cron**
Set up a scheduled task to access:
```
https://yourdomain.com/admin/cron-delete-old-images.php?token=electrozot_secure_cron_2024
```

**Option C: cPanel Cron Jobs**
1. Go to cPanel → Cron Jobs
2. Add new cron job
3. Command: `/usr/bin/php /home/username/public_html/admin/cron-delete-old-images.php`
4. Schedule: Daily at 2:00 AM

### Step 3: Security
Change the cron token in `cron-delete-old-images.php`:
```php
define('CRON_TOKEN', 'your_random_secure_token_here');
```

## File Structure

```
admin/
├── cron-delete-old-images.php          # Cron job script
├── setup-image-auto-deletion.php       # One-time setup
├── vendor/inc/
│   └── image-visibility-helper.php     # Visibility control functions
└── logs/
    └── image-deletion.log              # Deletion log file

vendor/img/
├── completions/                        # Service completion images
└── bills/                              # Bill/receipt images
```

## Database Schema

### Required Column
```sql
ALTER TABLE tms_service_booking 
ADD COLUMN sb_completed_date DATETIME DEFAULT NULL;
```

This column is automatically populated when a service is marked as completed.

## Image Upload Process

When technician completes a service (`tech/complete-service.php`):
1. Service image uploaded to `vendor/img/completions/`
2. Bill image uploaded to `vendor/img/bills/`
3. `sb_completed_date` set to current timestamp
4. Images visible to both customer and admin

## Visibility Timeline

```
Day 0:  Service completed, images uploaded
Day 1-31: Images visible to customer and admin
Day 32-40: Images hidden from customer, visible to admin only
Day 41+: Images deleted permanently, not visible to anyone
```

## User Experience

### Customer View (`usr/user-booking-details.php`)
- Days 1-31: Full access to images
- Day 32+: Images section not displayed
- No notification about deletion

### Admin View (`admin/admin-view-service-booking.php`)
- Days 1-40: Full access to images
- Day 41+: Shows "Image has been archived (older than 40 days)"
- No notification about deletion

## Monitoring

### Check Deletion Log
```bash
cat admin/logs/image-deletion.log
```

### Manual Test Run
```bash
php admin/cron-delete-old-images.php
```

### Check Last Run
```bash
tail -20 admin/logs/image-deletion.log
```

## Troubleshooting

### Images Not Deleting
1. Check cron job is running: `crontab -l`
2. Check file permissions: `ls -la vendor/img/completions/`
3. Check log file: `cat admin/logs/image-deletion.log`
4. Run manually: `php admin/cron-delete-old-images.php`

### Images Still Visible After 31/40 Days
1. Verify `sb_completed_date` is set in database
2. Check `image-visibility-helper.php` is included
3. Clear browser cache
4. Check server timezone settings

### Permission Errors
```bash
# Fix directory permissions
chmod 755 vendor/img/completions/
chmod 755 vendor/img/bills/
chmod 755 admin/logs/

# Fix file permissions
chmod 644 vendor/img/completions/*
chmod 644 vendor/img/bills/*
```

## Important Notes

1. **No User Notifications**: Users are NOT notified about image deletion
2. **Silent Operation**: Images simply become unavailable after the period
3. **Permanent Deletion**: After 40 days, images cannot be recovered
4. **Admin Grace Period**: Admins get 9 extra days (40 vs 31) to review images
5. **Automatic Process**: No manual intervention required once set up

## Testing

### Test Visibility Logic
```php
// In any PHP file
include('admin/vendor/inc/image-visibility-helper.php');

// Test customer view (31 days)
$test_date = date('Y-m-d H:i:s', strtotime('-32 days'));
var_dump(isImageVisible($test_date, 'customer')); // Should be false

// Test admin view (40 days)
var_dump(isImageVisible($test_date, 'admin')); // Should be true
```

### Test Cron Job
```bash
# Dry run (check what would be deleted)
php admin/cron-delete-old-images.php

# Check log
cat admin/logs/image-deletion.log
```

## Maintenance

### Regular Checks
- Weekly: Review deletion log
- Monthly: Verify cron job is running
- Quarterly: Check disk space savings

### Backup Strategy
If you need to keep images longer:
1. Modify days in `image-visibility-helper.php`
2. Update cron job cutoff date
3. Consider archiving to external storage before deletion

## Support

For issues or questions:
1. Check log file: `admin/logs/image-deletion.log`
2. Verify setup: Run `setup-image-auto-deletion.php` again
3. Test manually: `php admin/cron-delete-old-images.php`
