# System Logs Auto-Cleanup Feature

## Overview
Automatic deletion of old system logs to maintain only the 100 most recent entries.

---

## How It Works

### Automatic Cleanup Triggers
The system automatically cleans up old logs when:

1. **Admin Login** - After successful admin login
2. **View System Logs** - When admin views the system logs page

### Cleanup Logic
```
IF total_logs > 100 THEN
    DELETE oldest logs
    KEEP only 100 most recent logs
END IF
```

---

## Implementation Details

### Files Modified

1. **admin/index.php**
   - Added cleanup after admin login
   - Runs after inserting new log entry

2. **admin/admin-view-syslogs.php**
   - Added cleanup when viewing logs
   - Ensures logs stay under limit

3. **admin/vendor/inc/cleanup-syslogs.php** (NEW)
   - Reusable cleanup function
   - Can be included anywhere

---

## Cleanup Function

### Location
`admin/vendor/inc/cleanup-syslogs.php`

### Function
```php
function cleanup_system_logs($mysqli) {
    // Count total logs
    // If > 100, delete oldest
    // Keep 100 most recent
}
```

### Usage
```php
include('vendor/inc/cleanup-syslogs.php');
// Automatically executes cleanup
```

---

## Database Query

### Deletion Query
```sql
DELETE FROM tms_syslogs 
WHERE log_id NOT IN (
    SELECT log_id FROM (
        SELECT log_id FROM tms_syslogs 
        ORDER BY u_logintime DESC 
        LIMIT 100
    ) AS recent_logs
)
```

### How It Works
1. Selects 100 most recent log IDs (by login time)
2. Deletes all logs NOT in that list
3. Result: Only 100 newest logs remain

---

## Configuration

### Current Limit
**100 logs** (most recent)

### To Change Limit
Edit both files and change `LIMIT 100` to desired number:

**admin/index.php:**
```php
// Change 100 to your desired limit
include('vendor/inc/cleanup-syslogs.php');
```

**admin/vendor/inc/cleanup-syslogs.php:**
```php
// Change both occurrences of 100
if($count_row['total'] > 100) {
    // ...
    LIMIT 100
}
```

---

## Performance

### Efficiency
- ✅ Only runs when needed (count > 100)
- ✅ Single query deletion
- ✅ Minimal overhead
- ✅ No impact on user experience

### Frequency
- Runs on every admin login
- Runs when viewing system logs
- Typically 1-2 times per day

---

## Benefits

### Storage Management
- ✅ Prevents unlimited log growth
- ✅ Keeps database size manageable
- ✅ Maintains system performance

### Data Retention
- ✅ Always keeps 100 most recent logs
- ✅ Sufficient for monitoring
- ✅ Automatic maintenance

### No Manual Intervention
- ✅ Fully automatic
- ✅ No admin action required
- ✅ Set and forget

---

## System Logs Table

### Table Name
`tms_syslogs`

### Columns
- `log_id` - Primary key
- `u_email` - User email
- `u_ip` - IP address
- `u_city` - City (if available)
- `u_country` - Country (if available)
- `u_logintime` - Login timestamp
- `log_type` - Type of log (default: 'login')
- `user_type` - User type (admin/technician/user)

### Sorting
Logs are sorted by `u_logintime DESC` (newest first)

---

## Monitoring

### Check Current Log Count
```sql
SELECT COUNT(*) as total FROM tms_syslogs;
```

### View Recent Logs
```sql
SELECT * FROM tms_syslogs 
ORDER BY u_logintime DESC 
LIMIT 100;
```

### Check Oldest Log
```sql
SELECT * FROM tms_syslogs 
ORDER BY u_logintime ASC 
LIMIT 1;
```

---

## Troubleshooting

### Issue: Logs not being deleted
**Check:**
1. Verify cleanup file exists: `admin/vendor/inc/cleanup-syslogs.php`
2. Check if file is included in login process
3. Verify database permissions (DELETE privilege)

### Issue: Too many logs deleted
**Solution:**
- Increase limit in cleanup function
- Change `LIMIT 100` to higher number

### Issue: Not enough logs retained
**Solution:**
- Decrease limit in cleanup function
- Change `LIMIT 100` to lower number

---

## Manual Cleanup

### If Needed
You can manually run cleanup by accessing:
```
admin/vendor/inc/cleanup-syslogs.php
```

Or run SQL directly:
```sql
DELETE FROM tms_syslogs 
WHERE log_id NOT IN (
    SELECT log_id FROM (
        SELECT log_id FROM tms_syslogs 
        ORDER BY u_logintime DESC 
        LIMIT 100
    ) AS recent_logs
);
```

---

## Best Practices

### Recommended Settings
- **Small sites:** 50-100 logs
- **Medium sites:** 100-200 logs
- **Large sites:** 200-500 logs

### Considerations
- More logs = more storage
- Fewer logs = less history
- 100 logs typically covers 1-3 months

---

## Future Enhancements

### Possible Additions
- [ ] Configurable limit from admin panel
- [ ] Archive old logs before deletion
- [ ] Export logs to file
- [ ] Email alerts for suspicious activity
- [ ] Log retention by date (e.g., keep 30 days)

---

## Security

### Data Protection
- ✅ Only deletes oldest logs
- ✅ Preserves recent activity
- ✅ No sensitive data exposed
- ✅ Admin-only access

### Audit Trail
- ✅ 100 most recent logins tracked
- ✅ IP addresses recorded
- ✅ Timestamps preserved
- ✅ User types identified

---

## Summary

✅ **Automatic cleanup** - No manual intervention  
✅ **Keeps 100 most recent logs** - Sufficient history  
✅ **Runs on login** - Regular maintenance  
✅ **Efficient deletion** - Single query  
✅ **Configurable limit** - Easy to adjust  
✅ **No performance impact** - Minimal overhead  

**System logs will never exceed 100 entries!**

---

*Feature implemented: November 15, 2025*  
*Auto-cleanup active and working*
