# Booking Limit Counter Fix

## Problem
Technicians with a booking limit of 1 were showing up as available for assignment even when they already had an active booking assigned to them.

## Root Cause
The `t_current_bookings` counter in the `tms_technician` table was not being properly incremented/decremented when:
1. A booking was assigned to a technician
2. A booking was completed, rejected, or cancelled
3. A technician was reassigned from one booking to another

## Solution Implemented

### 1. Updated `admin-assign-technician.php`
- **Increment counter** when assigning a new booking to a technician
- **Decrement counter** when reassigning from one technician to another
- **Decrement counter** when booking status changes to Completed/Cancelled/Rejected
- Prevents double-counting when reassigning to the same technician

### 2. Updated `admin-cancel-service-booking.php`
- **Decrement counter** when admin cancels a booking with an assigned technician

### 3. Existing Proper Implementation
The following files already handle the counter correctly:
- `tech/api-complete-booking.php` - Decrements on completion
- `tech/api-reject-booking.php` - Decrements on rejection
- `admin/BookingSystem.php` - Handles all operations correctly
- `admin/vendor/inc/booking-limit-helper.php` - Helper functions work correctly

### 4. Database Trigger (Optional)
Created a database trigger that automatically maintains the counter integrity:
- Auto-decrements when booking is completed/cancelled/rejected
- Auto-increments when technician is assigned
- Handles reassignments properly

## How It Works Now

### Assignment Flow
1. Admin assigns Booking #123 to Technician A (limit: 1)
   - `t_current_bookings` increases from 0 to 1
   - Technician A now shows as "At Capacity"

2. Technician A is no longer shown in available technicians list
   - Query checks: `t_current_bookings < t_booking_limit`
   - 1 < 1 = FALSE, so not shown

3. When Technician A completes/rejects the booking:
   - `t_current_bookings` decreases from 1 to 0
   - Technician A becomes available again

### Reassignment Flow
1. Booking #123 is assigned to Technician A
   - Technician A: `t_current_bookings` = 1

2. Admin reassigns Booking #123 to Technician B
   - Technician A: `t_current_bookings` decreases to 0
   - Technician B: `t_current_bookings` increases to 1

## Database Migration

Run the SQL script to fix existing data:
```bash
mysql -u your_user -p your_database < admin/fix-booking-limit-counter.sql
```

Or execute via PHP:
```php
php admin/setup-booking-limits.php
```

## Verification

Check technician availability:
```sql
SELECT 
    t_id,
    t_name,
    t_booking_limit,
    t_current_bookings,
    (t_booking_limit - t_current_bookings) as available_slots,
    (SELECT COUNT(*) 
     FROM tms_service_booking 
     WHERE sb_technician_id = t.t_id 
     AND sb_status NOT IN ('Completed', 'Cancelled', 'Rejected')
    ) as actual_active_bookings
FROM tms_technician t;
```

The `t_current_bookings` should match `actual_active_bookings`.

## Testing Checklist

- [x] Assign booking to technician with limit 1
- [x] Verify technician doesn't appear in available list
- [x] Complete booking and verify technician becomes available
- [x] Reject booking and verify technician becomes available
- [x] Cancel booking and verify technician becomes available
- [x] Reassign booking and verify both technicians' counters update
- [x] Assign same technician again (no double counting)

## Files Modified

1. `admin/admin-assign-technician.php` - Added counter increment/decrement logic
2. `admin/admin-cancel-service-booking.php` - Added counter decrement on cancellation
3. `admin/fix-booking-limit-counter.sql` - Database migration script

## Files Already Working Correctly

- `admin/vendor/inc/booking-limit-helper.php`
- `admin/check-technician-availability.php`
- `tech/api-complete-booking.php`
- `tech/api-reject-booking.php`
- `admin/BookingSystem.php`
- `admin/api-get-available-technicians.php`

## Notes

- The system uses `GREATEST(t_current_bookings - 1, 0)` to prevent negative values
- Database trigger provides automatic maintenance (optional but recommended)
- All existing helper functions in `booking-limit-helper.php` work correctly
- The issue was only in the manual assignment flow in `admin-assign-technician.php`
