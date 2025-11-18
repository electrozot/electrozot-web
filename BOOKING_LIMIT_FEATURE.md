# 3 Active Bookings Limit Per User

## Overview
Users can have a maximum of **3 active bookings** at any time. This prevents system overload and ensures fair service distribution.

## Implementation

### What Counts as "Active Booking"?
A booking is considered active if its status is NOT:
- ❌ Rejected
- ❌ Cancelled  
- ❌ Completed

Active statuses include:
- ✅ Pending
- ✅ Approved
- ✅ Assigned
- ✅ In Progress
- ✅ Any other status

### Where is the Limit Applied?

#### 1. **Guest Booking (process-guest-booking.php)**
- Checks by phone number
- Applies to guest users booking from homepage
- Message: "You have reached the maximum limit of 3 active bookings. Please wait for one of your bookings to be completed."

#### 2. **Admin Quick Booking (admin/admin-quick-booking.php)**
- Checks by phone number
- Applies when admin creates booking for customer
- Message: "This customer has reached the maximum limit of 3 active bookings. Please wait for one booking to be completed."

#### 3. **User Dashboard Booking (usr/confirm-booking.php)**
- Checks by user ID
- Applies when logged-in user books service
- Message: "You have reached the maximum limit of 3 active bookings. Please wait for one of your bookings to be completed."

#### 4. **Custom Service Booking (usr/book-custom-service.php)**
- Checks by user ID
- Applies when user requests custom service
- Message: "You have reached the maximum limit of 3 active bookings. Please wait for one of your bookings to be completed."

## Database Query

```sql
SELECT COUNT(*) as active_count 
FROM tms_service_booking 
WHERE sb_phone = ? 
AND sb_status NOT IN ('Rejected', 'Cancelled', 'Completed')
```

Or for logged-in users:

```sql
SELECT COUNT(*) as active_count 
FROM tms_service_booking 
WHERE sb_user_id = ? 
AND sb_status NOT IN ('Rejected', 'Cancelled', 'Completed')
```

## Logic Flow

```
User tries to create booking
    ↓
Check active bookings count
    ↓
If count >= 3
    ↓
Show error message
Stop booking process
    ↓
If count < 3
    ↓
Allow booking
Create new booking
```

## User Experience

### Scenario 1: User with 2 Active Bookings
- ✅ Can create 1 more booking
- Total will be 3 active bookings

### Scenario 2: User with 3 Active Bookings
- ❌ Cannot create new booking
- Must wait for one to complete/cancel/reject

### Scenario 3: User with 3 Bookings (1 Completed)
- ✅ Can create new booking
- Only 2 are "active" (Pending/Approved)

## Benefits

✅ **Prevents System Overload** - Limits concurrent bookings per user
✅ **Fair Distribution** - Ensures all users get service
✅ **Better Management** - Easier for technicians to handle
✅ **Quality Service** - Focus on completing existing bookings
✅ **Prevents Abuse** - Stops users from booking excessively

## Error Messages

### For Users:
```
You have reached the maximum limit of 3 active bookings. 
Please wait for one of your bookings to be completed.
```

### For Admin:
```
This customer has reached the maximum limit of 3 active bookings. 
Please wait for one booking to be completed.
```

## How Users Can Create New Bookings

Users need to wait for at least one booking to reach any of these statuses:
1. **Completed** - Service finished successfully
2. **Cancelled** - User cancelled the booking
3. **Rejected** - Technician/Admin rejected the booking

Once a booking reaches any of these statuses, it no longer counts toward the 3-booking limit.

## Technical Details

### Check Timing
- Validation happens **before** creating user profile
- Validation happens **before** inserting booking
- Prevents unnecessary database operations

### Performance
- Single SQL query to check count
- Indexed on `sb_phone` and `sb_user_id`
- Fast execution even with many bookings

### Consistency
- Same logic across all booking entry points
- Consistent error messages
- Uniform user experience

## Testing Checklist

- [ ] Guest user with 3 active bookings cannot book
- [ ] Logged-in user with 3 active bookings cannot book
- [ ] User with 2 active bookings can book
- [ ] User with 3 bookings (1 completed) can book
- [ ] Admin sees appropriate error for customer at limit
- [ ] Error messages display correctly
- [ ] Booking count updates after status change

## Future Enhancements

- Make limit configurable (admin setting)
- Different limits for different user types
- Premium users with higher limits
- Display current booking count to users
- Warning when approaching limit
