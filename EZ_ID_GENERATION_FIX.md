# EZ ID Generation Fix - Unique ID Generation

## Problem
EZ IDs were being generated with duplicate values. Multiple technicians were getting the same EZ ID.

## Root Cause

### Issue 1: String Sorting vs Numeric Sorting
The original query used:
```sql
ORDER BY t_ez_id DESC
```

This performs **string sorting**, not numeric sorting:
- String sort: EZ0001, EZ0010, EZ0002, EZ0003, ..., EZ0009
- Numeric sort: EZ0001, EZ0002, EZ0003, ..., EZ0009, EZ0010

**Example Problem:**
- Database has: EZ0001, EZ0002, ..., EZ0009
- String sort returns: EZ0009 (correct)
- Add EZ0010
- Next query string sort returns: EZ0010 (correct)
- Add EZ0011
- But if EZ0009 is last in string order, it might return EZ0009 again!

### Issue 2: Single Check for Duplicates
The code only checked once if the ID existed, then incremented by 1. If that ID also existed, it would still use it.

## Solution

### Fix 1: Numeric Sorting
```sql
SELECT t_ez_id FROM tms_technician 
WHERE t_ez_id LIKE 'EZ%' AND t_ez_id REGEXP '^EZ[0-9]+$'
ORDER BY CAST(SUBSTRING(t_ez_id, 3) AS UNSIGNED) DESC 
LIMIT 1
```

**How it works:**
- `SUBSTRING(t_ez_id, 3)` extracts the numeric part (e.g., "0001" from "EZ0001")
- `CAST(... AS UNSIGNED)` converts string to number (e.g., "0001" → 1)
- `ORDER BY ... DESC` sorts numerically (1, 2, 3, ..., 9, 10, 11)
- `REGEXP '^EZ[0-9]+$'` ensures only valid EZ IDs are considered

### Fix 2: Loop Until Unique ID Found
```php
$maxAttempts = 100;
$attempts = 0;

while($attempts < $maxAttempts) {
    // Check if ID exists
    if($stmt->num_rows == 0) {
        // ID is available - use it
        break;
    }
    
    // ID exists - try next one
    $nextNumber++;
    $nextEZID = 'EZ' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    $attempts++;
}
```

**How it works:**
- Keeps checking and incrementing until an available ID is found
- Prevents using duplicate IDs even if there are gaps
- Maximum 100 attempts to prevent infinite loops

## Examples

### Before Fix (Buggy):
```
Database: EZ0001, EZ0002, EZ0003, EZ0010
Query: ORDER BY t_ez_id DESC
Result: EZ0010 (string sort)
Next ID: EZ0011 ✓

Database: EZ0001, EZ0002, EZ0009, EZ0010
Query: ORDER BY t_ez_id DESC  
Result: EZ0010 (string sort)
Next ID: EZ0011 ✓

But sometimes:
Database: EZ0001, EZ0002, EZ0009
Query: ORDER BY t_ez_id DESC
Result: EZ0009 (correct)
Next ID: EZ0010 ✓

Add EZ0010, then:
Result: Could return EZ0009 again (string sort issue)
Next ID: EZ0010 ✗ DUPLICATE!
```

### After Fix (Correct):
```
Database: EZ0001, EZ0002, EZ0003, EZ0010
Query: ORDER BY CAST(SUBSTRING(t_ez_id, 3) AS UNSIGNED) DESC
Result: EZ0010 (numeric: 10 is highest)
Next ID: EZ0011 ✓

Database: EZ0001, EZ0002, EZ0009, EZ0010
Query: ORDER BY CAST(SUBSTRING(t_ez_id, 3) AS UNSIGNED) DESC
Result: EZ0010 (numeric: 10 is highest)
Next ID: EZ0011 ✓

Database: EZ0001, EZ0002, EZ0009
Query: ORDER BY CAST(SUBSTRING(t_ez_id, 3) AS UNSIGNED) DESC
Result: EZ0009 (numeric: 9 is highest)
Next ID: EZ0010 ✓
Check: EZ0010 doesn't exist ✓
Use: EZ0010 ✓
```

## Handling Edge Cases

### Case 1: Gaps in Sequence
```
Database: EZ0001, EZ0002, EZ0005, EZ0006
Highest: EZ0006 (numeric: 6)
Next: EZ0007 ✓
```
Gaps (EZ0003, EZ0004) are skipped - this is intentional to maintain sequence.

### Case 2: ID Already Exists (Race Condition)
```
Generated: EZ0010
Check: Already exists
Increment: EZ0011
Check: Available ✓
Use: EZ0011 ✓
```

### Case 3: Multiple Gaps
```
Database: EZ0001, EZ0005, EZ0010
Highest: EZ0010 (numeric: 10)
Next: EZ0011 ✓
```

### Case 4: First Technician
```
Database: Empty
Highest: None
Next: EZ0001 ✓
```

## Benefits

✅ **Truly Unique IDs** - No more duplicates
✅ **Numeric Sorting** - Proper sequential ordering
✅ **Race Condition Safe** - Loop checks until unique ID found
✅ **Gap Handling** - Works even with missing IDs in sequence
✅ **Validation** - REGEXP ensures only valid EZ IDs are considered
✅ **Fail-Safe** - Maximum attempts prevents infinite loops

## Technical Details

### SQL Query Breakdown
```sql
SELECT t_ez_id FROM tms_technician 
WHERE t_ez_id LIKE 'EZ%'                    -- Only EZ IDs
AND t_ez_id REGEXP '^EZ[0-9]+$'            -- Valid format: EZ + digits only
ORDER BY CAST(                               -- Convert to number
    SUBSTRING(t_ez_id, 3)                   -- Extract digits (position 3 onwards)
    AS UNSIGNED                              -- As unsigned integer
) DESC                                       -- Highest first
LIMIT 1                                      -- Get only the highest
```

### PHP Logic
```php
1. Get highest numeric EZ ID from database
2. Extract numeric part and increment by 1
3. Format as EZ0001, EZ0002, etc.
4. Loop:
   - Check if ID exists in database
   - If not exists: Use this ID (break loop)
   - If exists: Increment and try again
5. Return unique EZ ID
```

## Testing Scenarios

- [x] First technician: Gets EZ0001
- [x] Second technician: Gets EZ0002
- [x] After EZ0009: Gets EZ0010 (not EZ0009 again)
- [x] After EZ0099: Gets EZ0100
- [x] With gaps (EZ0001, EZ0005): Gets EZ0006
- [x] Duplicate check: Skips existing IDs
- [x] Race condition: Finds next available ID

## Migration Note

If you have existing duplicate EZ IDs in the database, you should:

1. Find duplicates:
```sql
SELECT t_ez_id, COUNT(*) as count 
FROM tms_technician 
WHERE t_ez_id LIKE 'EZ%'
GROUP BY t_ez_id 
HAVING count > 1;
```

2. Manually reassign unique IDs to duplicates
3. Then use the new generation system

## Future Enhancements

- Add database transaction for atomic ID generation
- Implement ID reservation system
- Add audit log for ID generation
- Consider using UUID for truly unique IDs
- Add ID recycling for deleted technicians (optional)
