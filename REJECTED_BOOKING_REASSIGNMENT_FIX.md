# Rejected Booking Reassignment Fix

## Problem

When trying to reassign rejected or "Not Done" bookings, the available technicians list was not showing properly or showing technicians who were already at capacity.

## Root Cause

The `admin/vendor/inc/get-technicians.php` file was using the old redundant availability fields:
- `t_is_available` (0 or 1)
- `t_current_booking_id` (specific booking ID)

Instead of the simplified booking capacity logic:
- `t_current_bookings < t_booking_limit`

## Solution Applied

### 1. Updated `admin/vendor/inc/get-technicians.php`

**Changed availability check from:**
```php
// OLD (Redundant)
$availability_check = "(t_is_available = 1 OR t_status = 'Available') 
                       AND (t_current_booking_id IS NULL OR t_current_booking_id = 0)";
```

**To:**
```php
// NEW (Simplified)
$availability_check = "t_current_bookings < t_booking_limit";
```

**Added capacity information to all queries:**
- Shows available slots for each technician
- Sorts by most available slots first
- Displays capacity in dropdown: `[2 slot(s) free]`

### 2. Updated `admin/admin-rejected-bookings.php`

**Changed reassignment logic:**

**Before:**
```php
// Check old fields
if(!$tech['t_is_available'] && $tech['t_current_booking_id']) {
    throw new Exception("Already assigned");
}

// Update old fields
UPDATE tms_technician 
SET t_is_available = 0, t_current_booking_id = ?
```

**After:**
```php
// Check capacity
if($tech['t_current_bookings'] >= $tech['t_booking_limit']) {
    throw new Exception("At capacity");
}

// Update counter
UPDATE tms_technician 
SET t_current_bookings = t_current_bookings + 1
```

## What's Fixed

✅ **Available technicians now show correctly** when reassigning rejected bookings
✅ **Capacity-based filtering** - Only shows technicians with available slots
✅ **Accurate availability** - Based on actual booking count vs limit
✅ **Shows available slots** - Displays how many bookings each technician can take
✅ **Automatic counter updates** - Increments/decrements booking count properly

## How It Works Now

### Rejected Booking Reassignment Flow:

1. **Admin opens rejected booking**
   - Booking status: "Rejected" or "Not Done"
   - Previous technician's counter was already decremented

2. **Admin clicks "Reassign"**
   - Modal opens with available technicians

3. **System fetches available technicians**
   ```sql
   SELECT * FROM tms_technician
   WHERE t_current_bookings < t_booking_limit
   ORDER BY (t_booking_limit - t_current_bookings) DESC
   ```

4. **Dropdown shows:**
   ```
   ✅ Available Technicians (3)
   - John Doe [2 slot(s)] (✓ Free)
   - Jane Smith [1 slot(s)] (✓ Free)
   - Bob Wilson [3 slot(s)] (✓ Free)
   ```

5. **Admin selects technician and confirms**
   - Old technician counter: -1 (if exists)
   - New technician counter: +1
   - Booking status: Changed to "Pending"

6. **Technician sees booking in dashboard**
   - Can accept or reject again

## Testing

### Test Rejected Booking Reassignment:

1. **Create a rejected booking:**
   ```
   - Assign booking to technician
   - Technician rejects it
   - Booking status becomes "Rejected"
   ```

2. **Go to Rejected Bookings page:**
   ```
   http://your-domain/admin/admin-rejected-bookings.php
   ```

3. **Click "Reassign" button**
   - Modal should open
   - Should show available technicians with capacity

4. **Select technician and reassign**
   - Should succeed
   - New technician counter should increment
   - Old technician counter should decrement (if applicable)

### Test with Capacity Limits:

1. **Set technician limit to 1:**
   ```sql
   UPDATE tms_technician SET t_booking_limit = 1 WHERE t_id = 1;
   ```

2. **Assign one booking to that technician**
   - Counter becomes 1/1

3. **Try to reassign rejected booking**
   - That technician should NOT appear in list
   - Only technicians with available capacity should show

## Files Modified

1. **admin/vendor/inc/get-technicians.php**
   - Changed availability check to use booking capacity
   - Added available slots to all queries
   - Shows capacity information in dropdown

2. **admin/admin-rejected-bookings.php**
   - Updated capacity check logic
   - Changed counter increment/decrement
   - Removed old field references

## Verification Queries

### Check Available Technicians:
```sql
SELECT 
    t_id,
    t_name,
    t_booking_limit,
    t_current_bookings,
    (t_booking_limit - t_current_bookings) as available_slots,
    CASE 
        WHEN t_current_bookings < t_booking_limit THEN 'Available'
        ELSE 'At Capacity'
    END as status
FROM tms_technician
ORDER BY available_slots DESC;
```

### Check Rejected Bookings:
```sql
SELECT 
    sb_id,
    sb_status,
    sb_technician_id,
    sb_rejection_reason
FROM tms_service_booking
WHERE sb_status IN ('Rejected', 'Rejected by Technician', 'Not Done')
ORDER BY sb_rejected_at DESC;
```

### Verify Counter Accuracy:
```sql
SELECT 
    t.t_id,
    t.t_name,
    t.t_current_bookings as counter,
    (SELECT COUNT(*) 
     FROM tms_service_booking sb 
     WHERE sb.sb_technician_id = t.t_id 
     AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
    ) as actual_active,
    CASE 
        WHEN t.t_current_bookings = (
            SELECT COUNT(*) 
            FROM tms_service_booking sb 
            WHERE sb.sb_technician_id = t.t_id 
            AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
        ) THEN '✓ Match'
        ELSE '✗ Mismatch'
    END as status
FROM tms_technician t;
```

## Troubleshooting

### Issue: No technicians showing in reassignment dropdown

**Possible causes:**
1. All technicians are at capacity
2. No technicians match the service category
3. Database columns missing

**Solution:**
```sql
-- Check if any technicians are available
SELECT * FROM tms_technician 
WHERE t_current_bookings < t_booking_limit;

-- If empty, all are at capacity
-- Check individual technician status
SELECT t_name, t_booking_limit, t_current_bookings 
FROM tms_technician;
```

### Issue: Technician at capacity still showing

**Solution:**
```sql
-- Recalculate counters
UPDATE tms_technician t
SET t_current_bookings = (
    SELECT COUNT(*) 
    FROM tms_service_booking sb 
    WHERE sb.sb_technician_id = t.t_id 
    AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
);
```

### Issue: Error "Technician at capacity" but they look available

**Solution:**
- Check actual active bookings for that technician
- Verify booking limit is set correctly
- Run counter recalculation script

## Related Files

- `admin/admin-rejected-bookings.php` - Rejected bookings page
- `admin/vendor/inc/get-technicians.php` - Technician fetching logic
- `admin/check-technician-availability.php` - Availability checker
- `admin/test-rejected-booking-reassign.php` - Test script

## Summary

The rejected booking reassignment now works correctly by:

1. ✅ Using simplified capacity-based availability
2. ✅ Showing only technicians with available slots
3. ✅ Displaying capacity information clearly
4. ✅ Properly updating booking counters
5. ✅ Preventing assignment to technicians at capacity

The system is now consistent and reliable for reassigning rejected or not-done bookings!
