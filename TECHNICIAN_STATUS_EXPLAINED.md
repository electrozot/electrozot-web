# Technician Status System - Complete Explanation

## What is "Free" Status?

**"Free" is NOT a valid status in your system!**

If you see "Free" in your database, it's likely:
1. Old/legacy data from before the system was standardized
2. Manually entered incorrect data
3. A typo or data corruption

## Valid Technician Status Values

Your system uses **TWO status values only**:

### 1. ✅ **"Available"**
**Meaning:** Technician is FREE and can accept new bookings

**When set:**
- When technician completes a booking
- When technician rejects a booking
- When admin cancels a booking
- When booking is marked "Not Done"
- When technician is first added to system

**Database value:** `t_status = 'Available'`

**Also sets:**
- `t_is_available = 1` (flag for quick checking)
- `t_current_booking_id = NULL` (no active booking)

### 2. 🔒 **"Booked"**
**Meaning:** Technician is BUSY with an active booking

**When set:**
- When admin assigns technician to a booking
- When booking status is: Pending, Approved, Assigned, In Progress

**Database value:** `t_status = 'Booked'`

**Also sets:**
- `t_is_available = 0` (flag showing not available)
- `t_current_booking_id = [booking_id]` (tracks which booking)

## Status Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    TECHNICIAN STATUS FLOW                    │
└─────────────────────────────────────────────────────────────┘

    ┌──────────────┐
    │  Available   │ ← Initial state when technician is added
    │  (Free)      │
    └──────┬───────┘
           │
           │ Admin assigns to booking
           ↓
    ┌──────────────┐
    │   Booked     │ ← Technician is working on a booking
    │   (Busy)     │
    └──────┬───────┘
           │
           │ Booking completed/cancelled/rejected
           ↓
    ┌──────────────┐
    │  Available   │ ← Back to free state
    │  (Free)      │
    └──────────────┘
```

## Database Structure

### tms_technician table columns:

```sql
t_status VARCHAR(20)           -- "Available" or "Booked"
t_is_available TINYINT(1)      -- 1 = available, 0 = busy
t_current_booking_id INT       -- NULL or booking ID
```

## Why Three Columns?

**Q: Why not just use t_status?**

**A: For performance and reliability:**

1. **t_status** - Human-readable status ("Available"/"Booked")
2. **t_is_available** - Fast boolean check (1/0) for queries
3. **t_current_booking_id** - Direct link to active booking

This redundancy ensures:
- Fast queries (checking integer is faster than string)
- Data integrity (can verify status matches booking)
- Easy debugging (can see which booking technician is on)

## How to Fix "Free" Status

If you have technicians with "Free" status, run this SQL:

```sql
-- Fix incorrect "Free" status to "Available"
UPDATE tms_technician 
SET t_status = 'Available',
    t_is_available = 1,
    t_current_booking_id = NULL
WHERE t_status = 'Free';
```

## How to Check Technician Status

### Method 1: Check t_status
```sql
SELECT * FROM tms_technician WHERE t_status = 'Available';
```

### Method 2: Check t_is_available (faster)
```sql
SELECT * FROM tms_technician WHERE t_is_available = 1;
```

### Method 3: Check all three (most reliable)
```sql
SELECT * FROM tms_technician 
WHERE t_status = 'Available' 
  AND t_is_available = 1 
  AND t_current_booking_id IS NULL;
```

## Common Queries

### Get all available technicians:
```sql
SELECT t_id, t_name, t_category, t_status 
FROM tms_technician 
WHERE t_status = 'Available' 
  AND t_is_available = 1;
```

### Get all busy technicians:
```sql
SELECT t_id, t_name, t_category, t_status, t_current_booking_id 
FROM tms_technician 
WHERE t_status = 'Booked' 
  AND t_is_available = 0;
```

### Get available technicians for specific category:
```sql
SELECT t_id, t_name, t_category 
FROM tms_technician 
WHERE t_category = 'Electrical' 
  AND t_status = 'Available' 
  AND t_is_available = 1;
```

### Find technicians with inconsistent status:
```sql
-- Find technicians marked as Available but have active bookings
SELECT t.*, sb.sb_id, sb.sb_status 
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
WHERE t.t_status = 'Available'
  AND sb.sb_status IN ('Pending', 'Approved', 'In Progress')
  AND sb.sb_id IS NOT NULL;
```

## Status Change Triggers

### Automatic Status Changes:

1. **Booking Assigned** → Status: Booked
2. **Booking Completed** → Status: Available
3. **Booking Cancelled** → Status: Available
4. **Booking Rejected** → Status: Available
5. **Booking Marked Not Done** → Status: Available

### Manual Status Changes:

Admin can manually change status in:
- Add Technician page
- Manage Technician page

**⚠️ Warning:** Manual changes should be avoided as they can cause inconsistencies!

## Best Practices

### ✅ DO:
- Let the system automatically manage status
- Use "Available" for free technicians
- Use "Booked" for busy technicians
- Check all three columns for reliability

### ❌ DON'T:
- Use "Free" (not a valid status)
- Manually change status unless necessary
- Set status without updating other columns
- Create custom status values

## Troubleshooting

### Problem: Technician stuck in "Booked" status
**Solution:**
```sql
-- Check if they have active bookings
SELECT * FROM tms_service_booking 
WHERE sb_technician_id = [TECH_ID] 
  AND sb_status NOT IN ('Completed', 'Cancelled', 'Rejected');

-- If no active bookings, free them up:
UPDATE tms_technician 
SET t_status = 'Available',
    t_is_available = 1,
    t_current_booking_id = NULL
WHERE t_id = [TECH_ID];
```

### Problem: Technician shows "Free" status
**Solution:**
```sql
-- Change "Free" to "Available"
UPDATE tms_technician 
SET t_status = 'Available'
WHERE t_status = 'Free';
```

### Problem: Status doesn't match booking
**Solution:** Run the consistency check script in `SETUP_ONE_BOOKING_RULE.md`

## Summary

| Status | Meaning | t_is_available | t_current_booking_id | Can Accept Bookings? |
|--------|---------|----------------|---------------------|---------------------|
| **Available** | Free/Ready | 1 | NULL | ✅ Yes |
| **Booked** | Busy/Working | 0 | [booking_id] | ❌ No |
| **Free** | ❌ INVALID | - | - | ⚠️ Error |

**Remember:** 
- "Available" = Free to work
- "Booked" = Currently working
- "Free" = Invalid status (should be "Available")
