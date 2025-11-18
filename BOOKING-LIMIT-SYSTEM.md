# Multi-Booking Capacity System

## Overview
Technicians can now handle multiple bookings simultaneously based on their **Maximum Concurrent Bookings** limit (1-5 bookings).

## How It Works

### Database Fields
- `t_booking_limit` - Maximum concurrent bookings (1-5)
- `t_current_bookings` - Current active bookings count
- `available_slots` - Calculated: limit - current

### Booking Assignment Logic

**Before (Old System):**
- ❌ Technician could only handle 1 booking at a time
- ❌ Had to complete/reject before accepting new booking
- ❌ Inefficient for experienced technicians

**After (New System):**
- ✅ Technician can handle 1-5 bookings simultaneously
- ✅ System checks: `current_bookings < booking_limit`
- ✅ Only shows technicians with available slots
- ✅ Auto-increments/decrements booking count

## Example Scenarios

### Scenario 1: Single Booking Technician
```
Technician: John
Limit: 1
Current: 0
Available: 1

Booking 1 assigned → Current: 1, Available: 0 ❌ At capacity
Booking 1 completed → Current: 0, Available: 1 ✅ Available again
```

### Scenario 2: Multi-Booking Technician
```
Technician: Sarah
Limit: 3
Current: 0
Available: 3

Booking 1 assigned → Current: 1, Available: 2 ✅ Still available
Booking 2 assigned → Current: 2, Available: 1 ✅ Still available
Booking 3 assigned → Current: 3, Available: 0 ❌ At capacity
Booking 1 completed → Current: 2, Available: 1 ✅ Available again
Booking 2 rejected → Current: 1, Available: 2 ✅ More slots free
```

### Scenario 3: High-Capacity Technician
```
Technician: Mike (Senior)
Limit: 5
Current: 2
Available: 3

Can accept 3 more bookings simultaneously ✅
Handles multiple customers efficiently ✅
Maximizes technician utilization ✅
```

## Updated Functions

### 1. checkTechnicianEngagement()
**Before:**
```php
// Checked if technician had ANY active booking
// Return: is_engaged (true/false)
```

**After:**
```php
// Checks if technician is at booking limit
// Returns:
[
    'is_at_limit' => bool,
    'current_bookings' => int,
    'booking_limit' => int,
    'available_slots' => int
]
```

### 2. getAvailableTechnicians()
**Before:**
```php
// Showed technicians with NO active bookings
WHERE technician has no bookings
```

**After:**
```php
// Shows technicians with available slots
WHERE t_current_bookings < t_booking_limit
ORDER BY available_slots DESC
```

## Assignment Flow

### When Admin Assigns Booking:
1. System queries: `WHERE t_current_bookings < t_booking_limit`
2. Shows only technicians with available slots
3. Displays: "2/5 bookings (3 slots available)"
4. Admin assigns booking
5. System increments: `t_current_bookings = t_current_bookings + 1`

### When Technician Completes:
1. Booking status → "Completed"
2. System decrements: `t_current_bookings = t_current_bookings - 1`
3. Technician becomes available for new bookings

### When Technician Rejects:
1. Booking status → "Rejected by Technician"
2. `sb_technician_id` → NULL
3. System decrements: `t_current_bookings = t_current_bookings - 1`
4. Technician freed up for new bookings

### When Marked "Not Done":
1. Booking status → "Not Done"
2. `sb_technician_id` → NULL
3. System decrements: `t_current_bookings = t_current_bookings - 1`
4. Technician available for reassignment

## Benefits

### For Business:
- ✅ **Higher Capacity**: Handle more bookings with same technicians
- ✅ **Better Utilization**: Senior technicians can handle multiple jobs
- ✅ **Flexible Scaling**: Adjust limits based on technician skill
- ✅ **Efficient Scheduling**: Maximize technician productivity

### For Technicians:
- ✅ **Fair Distribution**: Experienced techs get more work
- ✅ **Skill-Based Limits**: Beginners start with 1, experts handle 5
- ✅ **No Bottlenecks**: Can accept new booking while working on another
- ✅ **Better Earnings**: More bookings = more income

### For Customers:
- ✅ **Faster Service**: More technicians available
- ✅ **Less Waiting**: System has higher capacity
- ✅ **Better Matching**: Get the right technician faster

## Setting Booking Limits

### When Adding Technician:
```php
// In admin-add-technician.php
<select name="t_booking_limit">
    <option value="1">1 Booking at a time</option>
    <option value="2">2 Bookings at a time</option>
    <option value="3">3 Bookings at a time</option>
    <option value="4">4 Bookings at a time</option>
    <option value="5">5 Bookings at a time</option>
</select>
```

### Recommended Limits:
- **Beginner (0-1 year):** Limit = 1
- **Intermediate (1-3 years):** Limit = 2-3
- **Experienced (3-5 years):** Limit = 3-4
- **Expert (5+ years):** Limit = 4-5

## Database Queries

### Check Available Technicians:
```sql
SELECT t_id, t_name, t_booking_limit, t_current_bookings,
       (t_booking_limit - t_current_bookings) as available_slots
FROM tms_technician
WHERE t_current_bookings < t_booking_limit
ORDER BY available_slots DESC;
```

### Increment on Assignment:
```sql
UPDATE tms_technician 
SET t_current_bookings = t_current_bookings + 1
WHERE t_id = ?;
```

### Decrement on Completion/Rejection:
```sql
UPDATE tms_technician 
SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
WHERE t_id = ?;
```

## System Capacity Calculation

```
Total Capacity = SUM(all technicians' booking_limit)
Total Used = SUM(all technicians' current_bookings)
Total Available = Total Capacity - Total Used
Utilization % = (Total Used / Total Capacity) × 100
```

### Example:
```
5 Technicians:
- Tech 1: Limit=1, Current=1 → Available=0
- Tech 2: Limit=2, Current=1 → Available=1
- Tech 3: Limit=3, Current=2 → Available=1
- Tech 4: Limit=3, Current=0 → Available=3
- Tech 5: Limit=5, Current=3 → Available=2

Total Capacity: 14 bookings
Total Used: 7 bookings
Total Available: 7 slots
Utilization: 50%
```

## Testing

### Run Test File:
```
http://yoursite.com/admin/test-booking-limit-system.php
```

**Tests:**
- ✅ Booking limit columns exist
- ✅ Current bookings tracked correctly
- ✅ Available slots calculated properly
- ✅ System capacity displayed
- ✅ Utilization percentage shown

## Files Modified

1. **admin/check-technician-availability.php**
   - Updated `checkTechnicianEngagement()` to use booking limits
   - Updated `getAvailableTechnicians()` to filter by available slots
   - Added booking limit fields to queries

## Files Created

1. **admin/test-booking-limit-system.php** - Test and monitor system

## Status
✅ **COMPLETE** - Multi-booking capacity system fully implemented!

## Migration Notes

If upgrading from old system:
1. Run `admin/setup-booking-limits.php` to add columns
2. Set booking limits for all technicians (default: 1)
3. System will auto-calculate current bookings
4. Test with `admin/test-booking-limit-system.php`
