# Notification Center - Rejected Filter Fix

## Problem
When clicking the "Rejected" filter button in the admin notification center, it was not showing rejected bookings.

## Root Cause
The rejected filter was only checking for:
- `'Rejected'`
- `'Cancelled'`

But technicians can reject bookings using different statuses:
- `'Not Done'` (via form rejection)
- `'Not Completed'` (via API rejection)
- `'Rejected by Technician'` (alternative status)

So the filter was missing most rejected bookings!

## Solution Applied

### 1. Updated Filter Query
**File:** `admin/admin-notifications.php`

**Before:**
```php
if($filter == 'Rejected') {
    $where_conditions[] = "(sb.sb_status = 'Rejected' OR sb.sb_status = 'Cancelled')";
}
```

**After:**
```php
if($filter == 'Rejected') {
    $where_conditions[] = "(sb.sb_status = 'Rejected' OR sb.sb_status = 'Rejected by Technician' OR sb.sb_status = 'Cancelled' OR sb.sb_status = 'Not Done' OR sb.sb_status = 'Not Completed')";
}
```

### 2. Updated Priority Order
Added `'Not Completed'` to the rejected priority group:

**Before:**
```php
WHEN sb.sb_status IN ('Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done') THEN 1
```

**After:**
```php
WHEN sb.sb_status IN ('Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done', 'Not Completed') THEN 1
```

### 3. Updated Icon Display
Added all rejection statuses to the icon switch case:

**Before:**
```php
case 'Rejected':
case 'Cancelled':
    $icon = 'fa-times-circle';
    $icon_bg = 'bg-danger';
    $status_badge = 'badge-danger';
    break;
```

**After:**
```php
case 'Rejected':
case 'Rejected by Technician':
case 'Cancelled':
case 'Not Done':
case 'Not Completed':
    $icon = 'fa-times-circle';
    $icon_bg = 'bg-danger';
    $status_badge = 'badge-danger';
    break;
```

## How It Works Now

### Rejected Filter Includes:
1. ✅ **Rejected** - Standard rejection
2. ✅ **Rejected by Technician** - Alternative rejection status
3. ✅ **Cancelled** - Cancelled bookings
4. ✅ **Not Done** - Technician marked as not done (form)
5. ✅ **Not Completed** - Technician cannot complete (API)

### Visual Display:
All rejected bookings now show with:
- 🔴 Red border
- ❌ X icon
- Red badge
- Red gradient background

## Testing

### Test Steps:
1. Go to admin notification center
2. Click "Rejected" filter button
3. Should see all rejected bookings including:
   - Bookings marked "Not Done" by technicians
   - Bookings marked "Not Completed" by technicians
   - Cancelled bookings
   - Standard rejected bookings

### Expected Results:
- ✅ All rejection types appear in the list
- ✅ Red styling applied to all
- ✅ Correct icon (X) displayed
- ✅ Proper sorting (rejected bookings at top)

## All Rejection Statuses

| Status | Source | Filter Shows? |
|--------|--------|--------------|
| Rejected | Admin action | ✅ Yes |
| Rejected by Technician | System status | ✅ Yes |
| Cancelled | Customer/Admin | ✅ Yes |
| Not Done | Technician form | ✅ Yes |
| Not Completed | Technician API | ✅ Yes |

## Files Modified

### Core Files
1. **admin/admin-notifications.php**
   - Updated rejected filter query
   - Updated priority order
   - Updated icon display logic

### Documentation
2. **NOTIFICATION_CENTER_REJECTED_FILTER_FIX.md** (NEW)
   - This documentation file

## Benefits

### For Admin:
- ✅ See all rejected bookings in one place
- ✅ No missed rejections
- ✅ Complete visibility of problem bookings
- ✅ Easy to identify bookings needing reassignment
- ✅ Better workflow management

### For System:
- ✅ Consistent filtering across all rejection types
- ✅ Complete data visibility
- ✅ Proper categorization
- ✅ Accurate reporting

## Related Features

This fix complements:
1. **Rejected Booking Notifications** - Real-time alerts for rejections
2. **Technician Dashboard** - Proper booking ordering
3. **Admin Dashboard** - Complete booking overview

## Troubleshooting

### Filter Still Not Showing Bookings?

**Check 1: Verify Booking Status**
```sql
SELECT sb_id, sb_status, sb_created_at 
FROM tms_service_booking 
WHERE sb_status IN ('Rejected', 'Rejected by Technician', 'Cancelled', 'Not Done', 'Not Completed')
ORDER BY sb_created_at DESC;
```

**Check 2: Clear Browser Cache**
```
Hard refresh: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
```

**Check 3: Check Filter Parameter**
```
URL should be: admin-notifications.php?filter=Rejected
```

**Check 4: Verify Database Values**
- Status names must match exactly
- Check for extra spaces or typos
- Case-sensitive in some databases

## Summary

The rejected filter in the notification center now shows **all types of rejected bookings**, including:
- Technician rejections (Not Done, Not Completed)
- Admin rejections (Rejected, Rejected by Technician)
- Cancellations (Cancelled)

This provides complete visibility of all bookings that need attention or reassignment.

**Status:** ✅ Fixed and Ready to Use
**Date:** November 21, 2024
