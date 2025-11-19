# Quick Fix Guide - Booking Limit Counter Issue

## Problem
Technicians with booking limit of 1 were showing as available even when they had an active booking.

## Solution Applied

### What Was Fixed
The `t_current_bookings` counter wasn't being updated when:
- Assigning bookings to technicians
- Completing/rejecting/cancelling bookings
- Reassigning bookings between technicians

### Files Modified
1. **admin/admin-assign-technician.php** - Now increments/decrements counter properly
2. **admin/admin-cancel-service-booking.php** - Now decrements counter on cancellation

### How to Apply the Fix

#### Option 1: Run the PHP Script (Recommended)
1. Login as admin
2. Navigate to: `http://your-domain/admin/run-booking-limit-fix.php`
3. The script will:
   - Add missing columns
   - Set default limits
   - Recalculate all counters
   - Show verification report

#### Option 2: Run SQL Directly
```bash
mysql -u your_user -p your_database < admin/fix-booking-limit-counter.sql
```

### Verification

After applying the fix, check that technicians with active bookings don't appear in the assignment list:

1. Go to **Admin Dashboard** → **Manage Technicians**
2. Check each technician's current bookings vs their limit
3. Try to assign a new booking - technicians at capacity should NOT appear

### Expected Behavior After Fix

**Before Fix:**
- Technician A has limit: 1, active bookings: 1
- ❌ Technician A still shows in available list

**After Fix:**
- Technician A has limit: 1, active bookings: 1
- ✅ Technician A does NOT show in available list
- ✅ Only shows again after completing/rejecting the booking

### Testing Checklist

1. ✅ Assign booking to technician with limit 1
2. ✅ Verify technician disappears from available list
3. ✅ Complete the booking
4. ✅ Verify technician reappears in available list
5. ✅ Assign new booking
6. ✅ Reject the booking
7. ✅ Verify technician reappears in available list

### Troubleshooting

**Issue:** Counters still incorrect after running fix
**Solution:** 
- Check for ongoing transactions
- Run the fix script again
- Manually verify with SQL:
```sql
SELECT t_id, t_name, t_current_bookings,
       (SELECT COUNT(*) FROM tms_service_booking 
        WHERE sb_technician_id = t.t_id 
        AND sb_status NOT IN ('Completed', 'Cancelled', 'Rejected')) as actual
FROM tms_technician t;
```

**Issue:** Technician still showing as available
**Solution:**
- Clear browser cache
- Check if booking status is actually active
- Verify `t_booking_limit` is set correctly

### Support

For issues or questions, check:
- `BOOKING_LIMIT_COUNTER_FIX.md` - Detailed technical documentation
- `admin/fix-booking-limit-counter.sql` - SQL migration script
- `admin/run-booking-limit-fix.php` - PHP fix script with verification
