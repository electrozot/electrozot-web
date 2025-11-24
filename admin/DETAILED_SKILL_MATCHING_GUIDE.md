# Detailed Service Skills Matching System

## Overview
The improved technician assignment system now matches technicians based on:
1. **Detailed Service Skills** - Exact match with the service required
2. **Time Slot Availability** - Checks if technician is free at the booking time
3. **Booking Capacity** - Ensures technician hasn't reached their booking limit

## How It Works

### 1. Skill Matching Algorithm

When assigning a technician to a booking, the system:

**Step 1: Get Booking Details**
- Service name (e.g., "AC (Split) - Repair")
- Booking date and time
- Service category

**Step 2: Find Technicians with Exact Skill Match**
- Searches the `t_skills` column in `tms_technician` table
- Uses `FIND_IN_SET()` to match the exact service name
- Only includes technicians who have capacity (current_bookings < booking_limit)

**Step 3: Check Time Slot Availability**
- For each matched technician, checks if they have conflicting bookings
- Looks for bookings within ±2 hours of the requested time
- Excludes completed, cancelled, or rejected bookings

**Step 4: Fallback to Category Match**
- If no exact skill matches found, searches by category
- Still checks time slot availability

### 2. Technician Display Priority

Technicians are displayed in this order:

1. **✅ Available Now - Has Required Skill** (BEST MATCH)
   - Has the exact detailed service skill
   - Available at the requested time slot
   - Has capacity for more bookings

2. **⚠️ Available Now - Category Match Only**
   - Matches the service category
   - Available at the requested time slot
   - Has capacity for more bookings

3. **🔴 Busy at This Time - Has Required Skill** (Disabled)
   - Has the exact skill but busy at this time
   - Shows what time they're busy
   - Cannot be selected

4. **🔴 Busy at This Time - Category Match** (Disabled)
   - Category match but busy at this time
   - Cannot be selected

## Files Modified

### 1. `admin/vendor/inc/improved-technician-matcher.php` (NEW)
Main matching logic with these functions:

- `getAvailableTechniciansWithSkillAndSlot()` - Main matching function
- `checkTechnicianTimeSlotAvailability()` - Checks time conflicts
- `formatTechniciansWithSkillAndSlot()` - Formats dropdown options

### 2. `admin/admin-assign-technician.php` (UPDATED)
- Now uses the improved matcher
- Shows booking time in the info section
- Displays availability status for each technician
- Shows count of available vs busy technicians

## Usage Example

### Scenario 1: Perfect Match Available
```
Booking: AC (Split) - Repair at 2:00 PM on Nov 21, 2024

Result:
✅ Available Now - Has Required Skill (2)
  - Rajesh Kumar (5 yrs, 2 slots free) - Available for this time slot
  - Amit Singh (3 yrs, 1 slot free) - Available for this time slot
```

### Scenario 2: Skill Match But Busy
```
Booking: Washing Machine - Repair at 10:00 AM on Nov 21, 2024

Result:
⚠️ Available Now - Category Match Only (1)
  - Suresh Patel (4 yrs, 1 slot free) - Available for this time slot

🔴 Busy at This Time - Has Required Skill (1)
  - Rajesh Kumar (5 yrs) - Busy at 10:30 AM on Nov 21 [DISABLED]
```

### Scenario 3: No Match
```
Booking: CCTV Camera - Installation at 3:00 PM

Result:
❌ No technicians with required skills found!

Solutions:
- Add the skill to an existing technician
- Add a new technician with this skill
- Change the booking time
```

## Benefits

### For Admin
1. **Clear Visibility** - See exactly which technicians have the required skill
2. **Time Conflict Prevention** - Avoid double-booking technicians
3. **Better Decision Making** - Know why a technician is unavailable

### For Technicians
1. **Skill-Based Assignment** - Only get jobs they're qualified for
2. **No Overlapping Bookings** - System prevents time conflicts
3. **Fair Distribution** - Considers booking capacity

### For Customers
1. **Qualified Technicians** - Get someone with the right skills
2. **Reliable Scheduling** - No last-minute cancellations due to conflicts
3. **Better Service Quality** - Technician knows the specific service

## Technical Details

### Time Slot Conflict Detection
```sql
-- Checks for bookings within ±2 hours window
WHERE sb_technician_id = ?
AND sb_booking_date = ?
AND sb_status NOT IN ('Completed', 'Cancelled', 'Rejected')
AND ABS(TIMESTAMPDIFF(MINUTE, ?, sb_booking_time)) <= 120
```

### Skill Matching Query
```sql
-- Exact skill match using FIND_IN_SET
WHERE FIND_IN_SET(?, t.t_skills) > 0
AND t.t_current_bookings < t.t_booking_limit
```

## Configuration

### Time Window for Conflicts
Default: ±2 hours (120 minutes)

To change, edit in `improved-technician-matcher.php`:
```php
AND ABS(TIMESTAMPDIFF(MINUTE, ?, sb_booking_time)) <= 120
//                                                      ^^^
//                                                Change this value
```

### Booking Capacity
Set per technician in `tms_technician` table:
- `t_booking_limit` - Maximum concurrent bookings
- `t_current_bookings` - Current active bookings

## Troubleshooting

### Issue: No technicians showing up
**Check:**
1. Do technicians have the exact service skill in their `t_skills` column?
2. Are all technicians at capacity (current_bookings >= booking_limit)?
3. Are all technicians busy at the requested time?

**Solution:**
- Add the skill to technicians via "Manage Technicians"
- Increase booking limits if needed
- Change booking time to a less busy slot

### Issue: Wrong technicians being matched
**Check:**
1. Service name in booking matches exactly with skill name
2. Skill names are comma-separated in `t_skills` column
3. No extra spaces in skill names

**Solution:**
- Standardize skill names across system
- Use exact names from the 43-service list

### Issue: Time conflicts not detected
**Check:**
1. Booking date and time are properly set
2. Existing bookings have correct status
3. Time window setting (default ±2 hours)

**Solution:**
- Verify booking data in `tms_service_booking` table
- Check that completed bookings are marked as 'Completed'

## Future Enhancements

Possible improvements:
1. **Dynamic Time Windows** - Different windows for different services
2. **Travel Time Consideration** - Factor in distance between bookings
3. **Skill Level Matching** - Match based on experience level required
4. **Priority Booking** - VIP customers get first choice of technicians
5. **Auto-Suggest Alternative Times** - If no one available, suggest better times

## Support

For issues or questions:
1. Check this guide first
2. Review the code comments in `improved-technician-matcher.php`
3. Test with sample bookings
4. Check database for data consistency

---

**Last Updated:** November 21, 2024
**Version:** 1.0
**Author:** Kiro AI Assistant
