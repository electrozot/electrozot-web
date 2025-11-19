# Technician Availability & Slots Auto-Update Fix

## Problem

When technicians rejected or completed bookings, their:
- ✗ `t_current_bookings` count was decremented
- ✗ But `t_status` (Available/Busy) was NOT updated automatically
- ✗ Technicians appeared "Busy" even when they had free slots
- ✗ System couldn't auto-assign new bookings to available technicians

## Root Cause

The `decrementTechnicianBookings()` and `incrementTechnicianBookings()` functions in `booking-limit-helper.php` only updated the booking count but didn't check/update the availability status.

## Solution Applied

### 1. Added Auto-Status Update Function

Created `updateTechnicianAvailabilityStatus()` that:
- Checks technician's current bookings vs limit
- Sets status to "Busy" if at capacity
- Sets status to "Available" if has free slots

```php
function updateTechnicianAvailabilityStatus($mysqli, $technician_id) {
    // Get current bookings and limit
    $stmt = $mysqli->prepare("SELECT t_current_bookings, t_booking_limit FROM tms_technician WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_object();
    
    $current = $tech->t_current_bookings ?? 0;
    $limit = $tech->t_booking_limit ?? 1;
    
    // Auto-determine status
    $new_status = ($current >= $limit) ? 'Busy' : 'Available';
    
    // Update status
    $update_stmt = $mysqli->prepare("UPDATE tms_technician SET t_status = ? WHERE t_id = ?");
    $update_stmt->bind_param('si', $new_status, $technician_id);
    return $update_stmt->execute();
}
```

### 2. Updated Increment Function

```php
function incrementTechnicianBookings($mysqli, $technician_id) {
    // Increment count
    $stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = t_current_bookings + 1 WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $result = $stmt->execute();
    
    // ✅ Auto-update availability status
    if ($result) {
        updateTechnicianAvailabilityStatus($mysqli, $technician_id);
    }
    
    return $result;
}
```

### 3. Updated Decrement Function

```php
function decrementTechnicianBookings($mysqli, $technician_id) {
    // Decrement count (minimum 0)
    $stmt = $mysqli->prepare("UPDATE tms_technician SET t_current_bookings = GREATEST(t_current_bookings - 1, 0) WHERE t_id = ?");
    $stmt->bind_param('i', $technician_id);
    $result = $stmt->execute();
    
    // ✅ Auto-update availability status
    if ($result) {
        updateTechnicianAvailabilityStatus($mysqli, $technician_id);
    }
    
    return $result;
}
```

### 4. Added Sync Functions

Created helper functions to fix any existing data inconsistencies:

```php
// Sync specific technician or all technicians
function syncTechnicianBookingCounts($mysqli, $technician_id = null)

// Get actual active booking count from database
function getTechnicianActiveBookingCount($mysqli, $technician_id)
```

## How It Works Now

### Booking Assignment Flow:
```
Admin assigns booking to technician
    ↓
incrementTechnicianBookings() called
    ↓
t_current_bookings = t_current_bookings + 1
    ↓
updateTechnicianAvailabilityStatus() called
    ↓
If t_current_bookings >= t_booking_limit:
    Set t_status = 'Busy'
Else:
    Set t_status = 'Available'
```

### Booking Rejection Flow:
```
Technician rejects booking
    ↓
tech/api-reject-booking.php executes
    ↓
decrementTechnicianBookings() called
    ↓
t_current_bookings = t_current_bookings - 1
    ↓
updateTechnicianAvailabilityStatus() called
    ↓
If t_current_bookings < t_booking_limit:
    Set t_status = 'Available'  ✅ Now available for new bookings!
```

### Booking Completion Flow:
```
Technician completes booking
    ↓
tech/api-complete-booking.php executes
    ↓
decrementTechnicianBookings() called
    ↓
t_current_bookings = t_current_bookings - 1
    ↓
updateTechnicianAvailabilityStatus() called
    ↓
If t_current_bookings < t_booking_limit:
    Set t_status = 'Available'  ✅ Ready for next booking!
```

## Status Logic

| Current Bookings | Booking Limit | Auto Status |
|-----------------|---------------|-------------|
| 0 | 5 | Available |
| 3 | 5 | Available |
| 5 | 5 | Busy |
| 7 | 5 | Busy |

## Files Modified

### 1. `admin/vendor/inc/booking-limit-helper.php`
- ✅ Added `updateTechnicianAvailabilityStatus()` function
- ✅ Updated `incrementTechnicianBookings()` to auto-update status
- ✅ Updated `decrementTechnicianBookings()` to auto-update status
- ✅ Added `syncTechnicianBookingCounts()` for data sync
- ✅ Added `getTechnicianActiveBookingCount()` helper

### 2. `admin/fix-technician-availability.php` (New)
- Shows current technician status
- Syncs booking counts with actual data
- Displays before/after comparison
- Lists active bookings per technician
- Tests the automatic update system

## Testing

### Test 1: Rejection Updates Availability
1. Find a technician with status "Busy" and full bookings
2. Have them reject one booking
3. Check their status → Should change to "Available"
4. Check `t_current_bookings` → Should decrease by 1

### Test 2: Completion Updates Availability
1. Find a technician with status "Busy"
2. Have them complete a booking
3. Check their status → Should change to "Available"
4. Check `t_current_bookings` → Should decrease by 1

### Test 3: Assignment Updates Availability
1. Find a technician with 1 slot left (e.g., 4/5 bookings)
2. Assign them a new booking
3. Check their status → Should change to "Busy"
4. Check `t_current_bookings` → Should be 5/5

### Test 4: Sync Existing Data
1. Run: `http://your-domain/admin/fix-technician-availability.php`
2. Check "Before Fix" section for mismatches
3. System syncs all technicians
4. Check "After Fix" section → All should be correct

## Verification Queries

### Check Technician Status:
```sql
SELECT 
    t_id,
    t_name,
    t_status,
    t_current_bookings,
    t_booking_limit,
    (t_booking_limit - t_current_bookings) as available_slots
FROM tms_technician
ORDER BY t_current_bookings DESC;
```

### Check Actual vs Recorded Bookings:
```sql
SELECT 
    t.t_id,
    t.t_name,
    t.t_current_bookings as recorded_bookings,
    COUNT(sb.sb_id) as actual_bookings,
    t.t_booking_limit,
    t.t_status
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id 
    AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
GROUP BY t.t_id
HAVING recorded_bookings != actual_bookings;
```

### Find Technicians with Wrong Status:
```sql
SELECT 
    t_id,
    t_name,
    t_status,
    t_current_bookings,
    t_booking_limit,
    CASE 
        WHEN t_current_bookings >= t_booking_limit THEN 'Should be Busy'
        ELSE 'Should be Available'
    END as correct_status
FROM tms_technician
WHERE (t_current_bookings >= t_booking_limit AND t_status = 'Available')
   OR (t_current_bookings < t_booking_limit AND t_status = 'Busy');
```

## Troubleshooting

### Issue: Technician still shows "Busy" after rejection

**Check 1:** Verify booking was actually rejected
```sql
SELECT sb_id, sb_status, sb_technician_id, sb_rejected_at 
FROM tms_service_booking 
WHERE sb_id = [booking_id];
```

**Check 2:** Verify technician booking count decreased
```sql
SELECT t_id, t_name, t_current_bookings, t_booking_limit, t_status 
FROM tms_technician 
WHERE t_id = [technician_id];
```

**Solution:** Run sync script
```
http://your-domain/admin/fix-technician-availability.php
```

### Issue: Booking count doesn't match actual bookings

**Cause:** Data inconsistency from previous operations

**Solution:** Use sync function
```php
syncTechnicianBookingCounts($mysqli, $technician_id);
```

Or run the fix script to sync all technicians.

### Issue: Status not updating automatically

**Check:** Verify the helper file is included
```php
require_once('vendor/inc/booking-limit-helper.php');
```

**Check:** Verify functions are being called
- In `tech/api-reject-booking.php` → `decrementTechnicianBookings()` should be called
- In `tech/api-complete-booking.php` → `decrementTechnicianBookings()` should be called
- In assignment APIs → `incrementTechnicianBookings()` should be called

## Benefits

✅ **Automatic Status Updates** - No manual intervention needed
✅ **Real-time Availability** - Status reflects actual capacity
✅ **Better Auto-Assignment** - System can find available technicians
✅ **Accurate Slot Tracking** - Booking counts stay in sync
✅ **Self-Healing** - Sync function fixes any inconsistencies
✅ **Transparent Logic** - Clear rules for status determination

## Summary

The system now automatically manages technician availability:

| Action | Booking Count | Status Update |
|--------|--------------|---------------|
| Assign booking | +1 | Check if full → Busy |
| Reject booking | -1 | Check if has slots → Available |
| Complete booking | -1 | Check if has slots → Available |
| Reassign booking | Old: -1, New: +1 | Both updated |

**Files to run:**
1. `admin/fix-technician-availability.php` - Sync existing data
2. Test rejection/completion to verify auto-updates work

Technician slots and availability now update automatically! 🎯
