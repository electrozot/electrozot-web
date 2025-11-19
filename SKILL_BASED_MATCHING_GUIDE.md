# Skill-Based Technician Matching System

## Overview

The system now properly matches technicians based on:
1. **Service Skills** - From 43+ services in `tms_service` table
2. **Technician Skills** - Checkbox selections in `tms_technician_skills` table
3. **Concurrent Booking Limit** - `t_booking_limit` field
4. **Current Availability** - `t_current_bookings < t_booking_limit`

## How It Works

### Step 1: Service Selection
When a booking is created, it references a service from `tms_service` table:
- Service ID
- Service Name (e.g., "AC Repair", "Laptop Repair")
- Service Category (e.g., "Electronics", "Appliances")

### Step 2: Skill Matching
System searches for technicians in priority order:

#### Priority 1: Exact Match
```sql
SELECT * FROM tms_technician t
INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
WHERE ts.skill_name = 'AC Repair'  -- Exact service name
AND t.t_current_bookings < t.t_booking_limit
```

#### Priority 2: Partial Match
```sql
SELECT * FROM tms_technician t
INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
WHERE ts.skill_name LIKE '%AC%'  -- Partial service name
AND t.t_current_bookings < t.t_booking_limit
```

#### Priority 3: Category Match (Fallback)
```sql
SELECT * FROM tms_technician t
WHERE t.t_category = 'Electronics'  -- Service category
AND t.t_current_bookings < t.t_booking_limit
```

### Step 3: Capacity Check
For each matched technician, check:
```
Available = t_current_bookings < t_booking_limit

Example:
- Technician A: 2/5 bookings = 3 slots available ✓
- Technician B: 1/1 bookings = 0 slots available ✗
```

### Step 4: Sorting
Technicians are sorted by:
1. Available slots (DESC) - Most capacity first
2. Experience (DESC) - Most experienced first
3. Name (ASC) - Alphabetical

## Database Structure

### tms_service (43+ services)
```sql
CREATE TABLE tms_service (
    s_id INT PRIMARY KEY,
    s_name VARCHAR(255),      -- "AC Repair", "Laptop Repair", etc.
    s_category VARCHAR(100),  -- "Electronics", "Appliances", etc.
    s_subcategory VARCHAR(100),
    s_price DECIMAL(10,2),
    s_status VARCHAR(20)
);
```

### tms_technician
```sql
CREATE TABLE tms_technician (
    t_id INT PRIMARY KEY,
    t_name VARCHAR(255),
    t_phone VARCHAR(20),
    t_email VARCHAR(255),
    t_category VARCHAR(100),
    t_specialization VARCHAR(255),
    t_experience INT,
    t_booking_limit INT DEFAULT 1,      -- Max concurrent bookings
    t_current_bookings INT DEFAULT 0    -- Current active bookings
);
```

### tms_technician_skills (Checkbox selections)
```sql
CREATE TABLE tms_technician_skills (
    ts_id INT PRIMARY KEY AUTO_INCREMENT,
    t_id INT,                    -- Technician ID
    skill_name VARCHAR(255),     -- Service name from tms_service
    skill_level VARCHAR(50),     -- "Expert", "Intermediate", etc.
    created_at TIMESTAMP,
    FOREIGN KEY (t_id) REFERENCES tms_technician(t_id)
);
```

## Usage Examples

### Example 1: New Booking Assignment

**Booking Details:**
- Service: "AC Repair" (ID: 15)
- Category: "Appliances"

**System Process:**
1. Query service: `SELECT * FROM tms_service WHERE s_id = 15`
2. Find technicians with "AC Repair" skill
3. Check capacity: `t_current_bookings < t_booking_limit`
4. Return sorted list

**Result:**
```
✅ Perfect Match (2 technicians)
- John Doe (5 yrs exp, 2 slots free - AC Repair)
- Jane Smith (3 yrs exp, 1 slot free - AC Repair)

📋 Category Match (1 technician)
- Bob Wilson (Appliances, 2 yrs exp, 3 slots free)
```

### Example 2: Reassignment (Rejected Booking)

**Booking Details:**
- Service Name: "Laptop Repair"
- Category: "Electronics"
- Previous Technician: John (rejected)

**System Process:**
1. Find technicians with "Laptop Repair" skill
2. Exclude John (if needed)
3. Check capacity
4. Return available technicians

### Example 3: Change Technician

**Scenario:**
- Current: Technician A (not responding)
- Need: Find replacement with same skills

**System Process:**
1. Get booking service
2. Find technicians with matching skills
3. Exclude current technician
4. Show available alternatives

## API Functions

### getAvailableTechniciansForService()
```php
/**
 * Get available technicians for a specific service
 * 
 * @param mysqli $mysqli Database connection
 * @param int $service_id Service ID from tms_service
 * @param int $exclude_booking_id Optional: Exclude current booking
 * @return array List of available technicians
 */
$technicians = getAvailableTechniciansForService($mysqli, 15);
```

### getAvailableTechniciansByServiceName()
```php
/**
 * Get available technicians by service name
 * 
 * @param mysqli $mysqli Database connection
 * @param string $service_name Service name
 * @param string $service_category Service category
 * @return array List of available technicians
 */
$technicians = getAvailableTechniciansByServiceName($mysqli, "AC Repair", "Appliances");
```

### canTechnicianAcceptBooking()
```php
/**
 * Check if technician can accept a booking
 * 
 * @param mysqli $mysqli Database connection
 * @param int $technician_id Technician ID
 * @return array ['can_accept' => bool, 'message' => string]
 */
$check = canTechnicianAcceptBooking($mysqli, 5);
if ($check['can_accept']) {
    // Assign booking
}
```

## Integration Points

### 1. Admin Assign Technician
File: `admin/admin-assign-technician.php`

**Before:**
```javascript
$.ajax({
    url: 'vendor/inc/get-technicians.php',
    data: { category: 'Electronics' }
});
```

**After:**
```javascript
$.ajax({
    url: 'vendor/inc/get-technicians.php',
    data: { service_id: 15 }  // Pass service ID
});
```

### 2. Rejected Bookings Reassignment
File: `admin/admin-rejected-bookings.php`

**Before:**
```javascript
$.ajax({
    url: 'vendor/inc/get-technicians.php',
    data: { 
        service_name: serviceName,
        category: category 
    }
});
```

**After:**
```javascript
$.ajax({
    url: 'vendor/inc/get-technicians.php',
    data: { 
        service_id: serviceId  // Best method
        // OR
        service_name: serviceName,
        category: category
    }
});
```

### 3. Quick Booking
File: `admin/admin-quick-booking.php`

Pass service ID when fetching technicians.

## Dropdown Display Format

### Perfect Match (Exact Skill)
```
✅ Perfect Match (2 technicians)
├─ John Doe (5 yrs exp, 2 slots free - AC Repair)
└─ Jane Smith (3 yrs exp, 1 slot free - AC Repair)
```

### Similar Skills (Partial Match)
```
⚠️ Similar Skills (1 technician)
└─ Mike Johnson (4 yrs exp, 1 slot free - AC Installation)
```

### Category Match (Fallback)
```
📋 Category Match (2 technicians)
├─ Bob Wilson (Appliances, 2 yrs exp, 3 slots free)
└─ Alice Brown (Appliances, 1 yr exp, 1 slot free)
```

### No Match
```
❌ No available technicians found
```

## Benefits

1. **Accurate Matching** - Only shows technicians with required skills
2. **Capacity Aware** - Respects booking limits
3. **Priority System** - Best matches shown first
4. **Fallback Options** - Category match if no skill match
5. **Clear Display** - Shows experience, capacity, and match type
6. **Scalable** - Works with 43+ services and unlimited technicians

## Testing

### Test 1: Exact Match
1. Create booking for "AC Repair"
2. Ensure technician has "AC Repair" in skills
3. Check capacity: current < limit
4. Should appear in "Perfect Match" group

### Test 2: No Match
1. Create booking for "Refrigerator Repair"
2. No technician has this skill
3. Should show category match or "No available"

### Test 3: At Capacity
1. Technician A: limit=1, current=1
2. Create new booking
3. Technician A should NOT appear
4. Only technicians with available slots shown

### Test 4: Reassignment
1. Booking rejected by Technician A
2. Find replacement
3. Should show other technicians with same skill
4. Technician A excluded (optional)

## Files Created/Modified

### New Files:
- `admin/vendor/inc/technician-matcher.php` - Core matching logic

### Modified Files:
- `admin/vendor/inc/get-technicians.php` - Uses new matcher

### Documentation:
- `SKILL_BASED_MATCHING_GUIDE.md` - This file

## Summary

The system now:
✅ Matches technicians by service skills (from 43+ services)
✅ Checks concurrent booking limits
✅ Shows only available technicians
✅ Prioritizes exact matches over category matches
✅ Displays capacity information clearly
✅ Works for assign, reassign, and change technician

All technician assignment operations now use proper skill-based matching with capacity awareness!
