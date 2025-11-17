# Foreign Key Constraint Fix for "Other" Services

## Error
```
Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child row: 
a foreign key constraint fails (`electrozot_db`.`tms_service_booking`, 
CONSTRAINT `tms_service_booking_ibfk_2` FOREIGN KEY (`sb_service_id`) 
REFERENCES `tms_service` (`s_id`) ON DELETE CASCADE)
```

## Root Cause

The `tms_service_booking` table has a foreign key constraint on `sb_service_id` that references `tms_service.s_id`. When we tried to insert a booking with `service_id = 0` for "Other" services, it failed because:

1. There is no service with `s_id = 0` in the `tms_service` table
2. The foreign key constraint requires `sb_service_id` to match an existing `s_id`
3. Setting it to `0` violates this constraint

## Solution

Instead of using `service_id = 0`, we now use `service_id = NULL` for custom "Other" services.

### Why NULL Works:
- Foreign key constraints allow NULL values by default
- NULL means "no reference" rather than "reference to 0"
- This is the standard SQL approach for optional foreign keys

## Changes Made

### 1. Modified Database Column
```sql
ALTER TABLE tms_service_booking MODIFY COLUMN sb_service_id INT NULL;
```
This ensures the column can accept NULL values.

### 2. Updated PHP Logic

#### admin/admin-quick-booking.php:
```php
// BEFORE (WRONG - caused foreign key error)
if($is_other_service) {
    $service_id = 0; // ❌ Violates foreign key
}

// AFTER (CORRECT)
if($is_other_service) {
    $service_id = null; // ✅ NULL is allowed
}
```

#### Separate INSERT statements:
```php
if($is_other_service) {
    // Insert with NULL service_id
    $insert_booking = "INSERT INTO tms_service_booking 
                      (..., sb_service_id, ...) 
                      VALUES (?, NULL, ...)";
    $stmt_booking->bind_param('isssssds', ...); // No service_id parameter
} else {
    // Insert with actual service_id
    $insert_booking = "INSERT INTO tms_service_booking 
                      (..., sb_service_id, ...) 
                      VALUES (?, ?, ...)";
    $stmt_booking->bind_param('iissssssd', ...); // Includes service_id
}
```

### 3. Updated process-guest-booking.php
Same changes applied to the guest booking form for consistency.

## Database Schema

### Before:
```sql
CREATE TABLE tms_service_booking (
    sb_id INT PRIMARY KEY AUTO_INCREMENT,
    sb_service_id INT NOT NULL,  -- ❌ NOT NULL prevents NULL values
    sb_custom_service VARCHAR(255) DEFAULT NULL,
    ...
    FOREIGN KEY (sb_service_id) REFERENCES tms_service(s_id) ON DELETE CASCADE
);
```

### After:
```sql
CREATE TABLE tms_service_booking (
    sb_id INT PRIMARY KEY AUTO_INCREMENT,
    sb_service_id INT NULL,  -- ✅ NULL allowed for custom services
    sb_custom_service VARCHAR(255) DEFAULT NULL,
    ...
    FOREIGN KEY (sb_service_id) REFERENCES tms_service(s_id) ON DELETE CASCADE
);
```

## How It Works Now

### Regular Service Booking:
```
Service Selected: "AC Installation" (s_id = 5)
Database Insert:
  sb_service_id: 5
  sb_custom_service: NULL
  sb_total_price: 500.00
```

### Custom "Other" Service Booking:
```
Service Selected: "Other - Specify your service"
Custom Service: "Solar panel installation"
Database Insert:
  sb_service_id: NULL  ✅ No foreign key violation
  sb_custom_service: "Solar panel installation"
  sb_total_price: 0.00
```

## Query Examples

### Find all custom service bookings:
```sql
SELECT * FROM tms_service_booking 
WHERE sb_service_id IS NULL;
```

### Find all regular service bookings:
```sql
SELECT * FROM tms_service_booking 
WHERE sb_service_id IS NOT NULL;
```

### Join with service details (handles NULL):
```sql
SELECT 
    sb.*,
    COALESCE(s.s_name, sb.sb_custom_service, 'Unknown') as service_name
FROM tms_service_booking sb
LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id;
```

## Benefits of Using NULL

### 1. **Database Integrity**
✅ No foreign key violations
✅ Follows SQL best practices
✅ Allows optional relationships

### 2. **Query Flexibility**
✅ Easy to identify custom services: `WHERE sb_service_id IS NULL`
✅ LEFT JOIN works naturally
✅ COALESCE for fallback values

### 3. **Future-Proof**
✅ Can add more custom service types
✅ Can convert custom to regular services later
✅ Maintains referential integrity

## Testing

### Test Case 1: Regular Service
```php
// Input
service_id: 5 (AC Installation)
custom_service: NULL

// Database
sb_service_id: 5
sb_custom_service: NULL
✅ Success - Foreign key satisfied
```

### Test Case 2: Custom Service
```php
// Input
service_id: 'other'
custom_service: "Solar panel installation"

// Database
sb_service_id: NULL
sb_custom_service: "Solar panel installation"
✅ Success - NULL allowed, no foreign key check
```

### Test Case 3: Invalid Service ID
```php
// Input
service_id: 999 (doesn't exist)

// Database
❌ Error - Foreign key constraint fails
✅ This is correct behavior - prevents invalid references
```

## Admin Panel Updates Needed

When displaying bookings, handle NULL service_id:

```php
// Display service name
if($booking->sb_service_id === null) {
    // Custom service
    echo '<span class="badge badge-warning">Custom Service</span>';
    echo '<br>' . htmlspecialchars($booking->sb_custom_service);
} else {
    // Regular service
    echo htmlspecialchars($booking->service_name);
}
```

## Reports & Analytics

### Count bookings by type:
```sql
SELECT 
    CASE 
        WHEN sb_service_id IS NULL THEN 'Custom Service'
        ELSE 'Regular Service'
    END as service_type,
    COUNT(*) as booking_count
FROM tms_service_booking
GROUP BY service_type;
```

### Most requested custom services:
```sql
SELECT 
    sb_custom_service,
    COUNT(*) as request_count
FROM tms_service_booking
WHERE sb_service_id IS NULL
GROUP BY sb_custom_service
ORDER BY request_count DESC
LIMIT 10;
```

## Migration Script

If you have existing bookings with `service_id = 0`, run this:

```sql
-- Update existing bookings with service_id = 0 to NULL
UPDATE tms_service_booking 
SET sb_service_id = NULL 
WHERE sb_service_id = 0;

-- Ensure column allows NULL
ALTER TABLE tms_service_booking 
MODIFY COLUMN sb_service_id INT NULL;
```

## Validation Rules

### Frontend:
- Service category must be selected
- Either service OR "Other" must be selected
- If "Other", custom service name is required

### Backend:
- If `service_id === 'other'`, set to NULL
- If `service_id` is numeric, validate it exists in `tms_service`
- Custom service name required when service_id is NULL
- Price set to 0 for custom services (admin updates later)

## Error Handling

### Before Fix:
```
❌ Foreign key constraint fails
❌ Booking not created
❌ Customer frustrated
❌ Lost business
```

### After Fix:
```
✅ Booking created successfully
✅ Custom service stored
✅ Admin can set price
✅ Customer satisfied
```

## Best Practices

### 1. **Always Use NULL for Optional Foreign Keys**
```php
// ✅ GOOD
$service_id = null;

// ❌ BAD
$service_id = 0;
$service_id = -1;
$service_id = 999999;
```

### 2. **Check for NULL in Queries**
```sql
-- ✅ GOOD
WHERE sb_service_id IS NULL

-- ❌ BAD
WHERE sb_service_id = 0
WHERE sb_service_id = NULL  -- This doesn't work!
```

### 3. **Use COALESCE for Display**
```php
// ✅ GOOD
$service_name = $booking->service_name ?? $booking->sb_custom_service ?? 'Unknown';

// ❌ BAD
$service_name = $booking->service_name ?: 'Unknown';  // Doesn't check custom
```

## Files Modified

1. **admin/admin-quick-booking.php**
   - Changed `service_id = 0` to `service_id = null`
   - Split INSERT into two statements (with/without service_id)
   - Added ALTER TABLE to allow NULL
   - Fixed bind_param types

2. **process-guest-booking.php**
   - Changed `sb_service_id = 0` to `sb_service_id = null`
   - Split INSERT into two statements
   - Added ALTER TABLE to allow NULL
   - Fixed bind_param types

## Status

✅ **Foreign key constraint issue resolved**
✅ **Custom services can be booked**
✅ **Database integrity maintained**
✅ **Both booking forms working**
✅ **No data loss**
✅ **Backward compatible**

---

**Last Updated**: November 17, 2025
**Status**: ✅ Fixed
**Priority**: Critical
**Impact**: Booking functionality fully restored
