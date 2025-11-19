# Simplified Technician Availability System

## Problem

Your system had **TWO redundant fields** for tracking technician availability:

1. `t_status` - VARCHAR field (e.g., "Available", "Booked", "On Leave")
2. `t_is_available` - TINYINT field (0 or 1)

This caused:
- ❌ Confusion about which field to check
- ❌ Inconsistency between the two fields
- ❌ Manual updates required
- ❌ Potential bugs when fields don't match

## Solution

**Use ONLY booking capacity to determine availability:**

```sql
-- Technician is AVAILABLE if:
t_current_bookings < t_booking_limit

-- Technician is AT CAPACITY if:
t_current_bookings >= t_booking_limit
```

## Benefits

✅ **Single source of truth** - Only one place to check
✅ **Automatically accurate** - Updates with booking counter
✅ **No manual updates** - System maintains it automatically
✅ **Simpler code** - Less complexity
✅ **No confusion** - Clear and straightforward

## How to Apply

### Option 1: Web Interface (Recommended)

1. Go to: `http://your-domain/admin/simplify-technician-availability.php`
2. Review current status
3. Click "Apply Simplification"
4. Done!

### Option 2: SQL Script

```bash
mysql -u root -p electrozot_db < admin/simplify-availability-system.sql
```

## What Changes

### Before (Redundant):
```php
// Had to check multiple fields
if ($tech['t_status'] == 'Available' && $tech['t_is_available'] == 1) {
    // Show technician
}

// Manual updates needed
UPDATE tms_technician 
SET t_status = 'Booked', t_is_available = 0 
WHERE t_id = ?;
```

### After (Simple):
```php
// Check only capacity
if ($tech['t_current_bookings'] < $tech['t_booking_limit']) {
    // Show technician - they have capacity!
}

// No manual updates needed - counter updates automatically
```

## Database Changes

### Columns Removed:
- ❌ `t_status` - No longer needed
- ❌ `t_is_available` - No longer needed

### Columns Used:
- ✅ `t_booking_limit` - Maximum concurrent bookings (e.g., 1, 2, 3)
- ✅ `t_current_bookings` - Current active bookings count

### View Created:
A compatibility view `v_technician_availability` is created for easier queries:

```sql
CREATE VIEW v_technician_availability AS
SELECT 
    t_id,
    t_name,
    t_phone,
    t_email,
    t_category,
    t_specialization,
    t_booking_limit,
    t_current_bookings,
    (t_booking_limit - t_current_bookings) as available_slots,
    CASE 
        WHEN t_current_bookings < t_booking_limit THEN 'Available'
        ELSE 'At Capacity'
    END as availability_status,
    CASE 
        WHEN t_current_bookings < t_booking_limit THEN 1
        ELSE 0
    END as is_available
FROM tms_technician;
```

## Query Examples

### Get All Available Technicians

**Direct Query:**
```sql
SELECT * FROM tms_technician
WHERE t_current_bookings < t_booking_limit
ORDER BY (t_booking_limit - t_current_bookings) DESC;
```

**Using View (Easier):**
```sql
SELECT * FROM v_technician_availability
WHERE is_available = 1;
```

### Get Available Technicians for Category

```sql
SELECT * FROM v_technician_availability
WHERE is_available = 1
AND t_category = 'AC Repair';
```

### Check Specific Technician

```sql
SELECT 
    t_name,
    availability_status,
    available_slots
FROM v_technician_availability
WHERE t_id = 1;
```

### Get Technicians with Most Capacity

```sql
SELECT * FROM v_technician_availability
WHERE is_available = 1
ORDER BY available_slots DESC
LIMIT 10;
```

## PHP Code Examples

### Check Availability

```php
// OLD WAY (Redundant)
$query = "SELECT * FROM tms_technician 
          WHERE t_status = 'Available' 
          AND t_is_available = 1";

// NEW WAY (Simple)
$query = "SELECT * FROM tms_technician 
          WHERE t_current_bookings < t_booking_limit";
```

### Get Available Technicians

```php
// Using the view
$query = "SELECT * FROM v_technician_availability 
          WHERE is_available = 1 
          ORDER BY available_slots DESC";

$result = $mysqli->query($query);
while ($tech = $result->fetch_assoc()) {
    echo "{$tech['t_name']} - {$tech['available_slots']} slots available<br>";
}
```

### Check if Technician Can Accept Booking

```php
function canAcceptBooking($technician_id, $mysqli) {
    $stmt = $mysqli->prepare("
        SELECT t_current_bookings, t_booking_limit 
        FROM tms_technician 
        WHERE t_id = ?
    ");
    $stmt->bind_param('i', $technician_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_assoc();
    
    return $tech['t_current_bookings'] < $tech['t_booking_limit'];
}
```

## Automatic Updates

The booking counter (`t_current_bookings`) is automatically maintained by:

1. **Database Trigger** - Updates on booking status changes
2. **Application Code** - Updates when assigning/completing/rejecting bookings

You don't need to manually update any status fields!

## Migration Notes

### Backup Created

A backup table `tms_technician_status_backup` is created with:
- Old `t_status` values
- Old `t_is_available` values
- Backup timestamp

If you need to restore, you can reference this table.

### Compatibility View

The view `v_technician_availability` provides:
- `availability_status` - "Available" or "At Capacity"
- `is_available` - 1 or 0 (for backward compatibility)
- `available_slots` - How many more bookings they can take

This makes migration easier for existing code.

## Testing

After applying the simplification:

1. **Check Technician List:**
   ```
   http://your-domain/admin/admin-manage-technician.php
   ```

2. **Verify Availability:**
   ```
   http://your-domain/admin/check-technician-booking-count.php
   ```

3. **Test Assignment:**
   - Assign booking to technician with limit 1
   - Verify they don't appear in available list
   - Complete booking
   - Verify they reappear in available list

## Troubleshooting

### Issue: Old code still references t_status

**Solution:** Update queries to use capacity check:
```php
// Replace this:
WHERE t_status = 'Available'

// With this:
WHERE t_current_bookings < t_booking_limit
```

### Issue: Need to show "Available" text

**Solution:** Use the view or calculate it:
```php
$status = ($tech['t_current_bookings'] < $tech['t_booking_limit']) 
    ? 'Available' 
    : 'At Capacity';
```

### Issue: Want to restore old fields

**Solution:** Use the backup table:
```sql
-- Restore from backup
ALTER TABLE tms_technician ADD COLUMN t_status VARCHAR(50);
ALTER TABLE tms_technician ADD COLUMN t_is_available TINYINT;

UPDATE tms_technician t
JOIN tms_technician_status_backup b ON t.t_id = b.t_id
SET t.t_status = b.t_status, t.t_is_available = b.t_is_available;
```

## Summary

### What You Had:
```
t_status = "Available" + t_is_available = 1 = Available ✓
t_status = "Booked" + t_is_available = 0 = Not Available ✗
(Two fields to maintain, potential for mismatch)
```

### What You Have Now:
```
t_current_bookings < t_booking_limit = Available ✓
t_current_bookings >= t_booking_limit = At Capacity ✗
(One simple check, always accurate)
```

### Files Created:
1. **admin/simplify-technician-availability.php** - Web interface to apply changes
2. **admin/simplify-availability-system.sql** - SQL script for direct execution
3. **SIMPLIFIED_AVAILABILITY_GUIDE.md** - This guide

### Result:
✅ Simpler system
✅ Single source of truth
✅ Automatically accurate
✅ Less code to maintain
✅ No confusion

Your availability system is now streamlined and efficient!
