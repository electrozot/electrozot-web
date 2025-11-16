# Technician One-Booking-Per-Time Rule

## Overview

This system ensures that **a technician can only handle ONE booking at a time**, regardless of how that booking was assigned. This prevents technicians from being overloaded and ensures quality service delivery.

---

## 🎯 Core Rule

### A Technician Can Take Only ONE Service at a Time

**It doesn't matter how the booking comes:**
- ✅ Fresh assignment (new booking)
- ✅ Reassignment (rejected/cancelled booking)
- ✅ Change technician (admin switches technician)

**If a technician is engaged, they CANNOT receive another booking until they:**
- ✅ Complete the current booking (mark as "Done")
- ❌ Reject the current booking (mark as "Not Done")
- 🔄 Admin cancels or removes their assignment

---

## 📋 Implementation Details

### 1. Engagement Check System

**File:** `admin/check-technician-availability.php`

This file provides core functions to manage technician availability:

#### Key Functions:

```php
// Check if technician is engaged with any booking
checkTechnicianEngagement($technician_id, $mysqli)

// Get available technicians for a service category
getAvailableTechnicians($service_category, $mysqli, $exclude_booking_id)

// Mark technician as engaged
engageTechnician($technician_id, $booking_id, $mysqli)

// Free up technician
freeTechnician($technician_id, $mysqli)

// Get summary of all technicians
getTechnicianEngagementSummary($mysqli)
```

#### Engagement Status:

A technician is considered **ENGAGED** if they have any booking with status:
- `Pending`
- `Approved`
- `Assigned`
- `In Progress`

A technician is considered **AVAILABLE** if they have NO bookings with above statuses, or only bookings with:
- `Completed`
- `Rejected`
- `Cancelled`
- `Not Done`

---

### 2. Assignment Validation

**File:** `admin/admin-assign-technician.php`

When admin tries to assign a technician to a booking:

1. **Check if technician is engaged** with another booking
2. **If engaged:** Show error message with booking details
3. **If available:** Allow assignment and mark technician as "Booked"

**Example Error Message:**
```
Technician is currently engaged with Booking #123 (Status: In Progress). 
Please wait until they complete or reject that booking.
```

---

### 3. Technician Selection

When selecting a technician for assignment:

**Only shows technicians who are:**
- ✅ Match the service category
- ✅ NOT engaged with any other booking
- ✅ Available to take new work

**Special case:** If reassigning the same booking, the currently assigned technician will appear even if they're "engaged" (since they're engaged with THIS booking).

---

### 4. Automatic Status Updates

#### When Technician Completes Booking:

**File:** `tech/complete-booking.php`

```php
// Automatically updates:
UPDATE tms_technician 
SET t_status = 'Available', 
    t_is_available = 1, 
    t_current_booking_id = NULL 
WHERE t_id = ?
```

#### When Technician Rejects Booking:

**File:** `tech/complete-booking.php`

```php
// Automatically updates:
UPDATE tms_technician 
SET t_status = 'Available', 
    t_is_available = 1, 
    t_current_booking_id = NULL 
WHERE t_id = ?
```

#### When Admin Cancels Booking:

**File:** `admin/admin-cancel-service-booking.php`

```php
// Automatically frees up technician:
UPDATE tms_technician 
SET t_status = 'Available' 
WHERE t_id = ?
```

---

## 🔍 Testing & Monitoring

### Test Page

**URL:** `admin/test-technician-engagement.php`

This page shows:
- ✅ All technicians and their engagement status
- ✅ Current booking assignments
- ✅ Real-time availability
- ✅ Statistics (Total, Available, Engaged)

### AJAX API Endpoints

The availability checker can be called via AJAX:

```javascript
// Check if specific technician is engaged
fetch('check-technician-availability.php?action=check_engagement&technician_id=5')

// Get available technicians for a category
fetch('check-technician-availability.php?action=get_available&category=Electrical')

// Get summary of all technicians
fetch('check-technician-availability.php?action=get_summary')
```

---

## 📊 Database Fields

### Technician Table (`tms_technician`)

| Field | Type | Description |
|-------|------|-------------|
| `t_status` | VARCHAR | 'Available' or 'Booked' |
| `t_is_available` | TINYINT | 1 = available, 0 = engaged |
| `t_current_booking_id` | INT | Current booking ID (if engaged) |

### Booking Table (`tms_service_booking`)

| Field | Type | Description |
|-------|------|-------------|
| `sb_technician_id` | INT | Assigned technician ID |
| `sb_status` | VARCHAR | Booking status |

---

## 🎬 User Flow Examples

### Example 1: Fresh Assignment

1. Customer books "Electrical Repair" service
2. Admin goes to assign technician
3. System shows ONLY available electrical technicians
4. Admin selects "John (Electrician) ✓ Available"
5. System marks John as "Booked"
6. John receives notification
7. **John is now ENGAGED** - won't appear for other bookings

### Example 2: Attempted Double Assignment

1. John is working on Booking #123 (Status: In Progress)
2. Admin tries to assign John to new Booking #456
3. System shows error:
   ```
   Technician is currently engaged with Booking #123 (Status: In Progress).
   Please wait until they complete or reject that booking.
   ```
4. Admin must choose a different technician

### Example 3: Completion & Re-availability

1. John completes Booking #123
2. Uploads service image and bill
3. Marks as "Done"
4. System automatically:
   - Updates booking status to "Completed"
   - Sets John's status to "Available"
   - Clears John's current booking ID
5. **John is now AVAILABLE** - can receive new bookings

### Example 4: Rejection & Re-availability

1. John arrives at customer location
2. Customer is not home / service cannot be completed
3. John marks booking as "Not Done" with reason
4. System automatically:
   - Updates booking status to "Not Done"
   - Sets John's status to "Available"
   - Notifies admin
5. **John is now AVAILABLE** - can receive new bookings
6. Admin can reassign the booking to another technician

### Example 5: Reassignment

1. Booking #789 was rejected by Sarah
2. Admin goes to reassign
3. System shows ONLY available technicians (Sarah is now available again)
4. Admin can assign to Sarah again OR choose different technician
5. Selected technician becomes engaged

---

## ⚠️ Important Notes

### For Admins:

1. **Check Engagement Status:** Use `test-technician-engagement.php` to see who's available
2. **Don't Force Assign:** System prevents assigning engaged technicians
3. **Monitor Workload:** Ensure fair distribution of bookings
4. **Handle Rejections Quickly:** Rejected bookings free up technicians immediately

### For Technicians:

1. **Complete or Reject Promptly:** This frees you up for next booking
2. **One Job at a Time:** Focus on quality service
3. **Update Status:** Always mark booking as done/not done when finished

### For Developers:

1. **Always Use Availability Checker:** Don't bypass the engagement check
2. **Update Both Fields:** When freeing technician, update both `t_status` and `t_is_available`
3. **Transaction Safety:** Use database transactions for assignment operations
4. **Test Edge Cases:** Test reassignment, cancellation, and concurrent assignments

---

## 🔧 Troubleshooting

### Issue: Technician shows as "Engaged" but has no active booking

**Solution:**
```sql
-- Check for orphaned status
SELECT t_id, t_name, t_status 
FROM tms_technician 
WHERE t_status = 'Booked' 
AND t_id NOT IN (
    SELECT DISTINCT sb_technician_id 
    FROM tms_service_booking 
    WHERE sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
    AND sb_technician_id IS NOT NULL
);

-- Fix orphaned status
UPDATE tms_technician 
SET t_status = 'Available', t_is_available = 1, t_current_booking_id = NULL 
WHERE t_status = 'Booked' 
AND t_id NOT IN (
    SELECT DISTINCT sb_technician_id 
    FROM tms_service_booking 
    WHERE sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
    AND sb_technician_id IS NOT NULL
);
```

### Issue: No technicians available for assignment

**Possible Causes:**
1. All technicians are engaged with active bookings
2. No technicians match the service category
3. Technician status not updated after completion

**Solutions:**
1. Wait for technicians to complete current bookings
2. Add more technicians for that category
3. Run the orphaned status fix query above

---

## 📝 Summary

✅ **One booking per technician at a time**  
✅ **Automatic status updates on completion/rejection**  
✅ **Prevents double assignments**  
✅ **Works for fresh, reassigned, and changed bookings**  
✅ **Real-time availability checking**  
✅ **Admin monitoring dashboard**  

This system ensures efficient technician utilization while maintaining service quality and preventing overload.
