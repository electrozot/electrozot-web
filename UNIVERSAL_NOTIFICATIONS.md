# Universal Notification System

## Overview
Real-time notifications now work on **ALL admin pages** and track both **new bookings** and **status updates by technicians**.

---

## Features

### ✅ Works Everywhere
- Dashboard
- All Bookings
- Manage Technicians
- Manage Services
- Quick Booking
- **Every admin page!**

### ✅ Tracks Two Types of Events

#### 1. New Bookings
- Customer creates new booking
- Guest booking submitted
- Admin creates quick booking

#### 2. Status Updates
- Technician accepts booking
- Technician rejects booking
- Technician completes service
- Any status change by technician

---

## Implementation

### Files Created
1. **admin/vendor/inc/notification-system.php**
   - Universal notification script
   - Included in footer (loads on all pages)
   - Handles both new bookings and updates

### Files Modified
1. **admin/vendor/inc/footer.php**
   - Added notification system include
   - Now loads on every admin page

2. **admin/check-new-bookings.php**
   - Added status update tracking
   - Checks `sb_updated_at` column
   - Returns both new and updated bookings

---

## How It Works

### Automatic Checking
- Polls server every 10 seconds
- Checks for new bookings (sb_created_at)
- Checks for status updates (sb_updated_at)
- Compares timestamps with last check

### When New Booking
```
🔔 Sound: Beep... Beep
📱 Toast: Purple gradient "New Booking!"
🔴 Badge: Shows count on bell icon
🔔 Browser: Native notification
```

### When Status Update
```
🔔 Sound: Beep... Beep
📱 Toast: Pink gradient "Booking Updated!"
🔴 Badge: Shows total count
🔔 Browser: Native notification
```

---

## Database Columns

### Required Columns
Both columns auto-created if missing:

```sql
ALTER TABLE tms_service_booking 
ADD COLUMN sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE tms_service_booking 
ADD COLUMN sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
ON UPDATE CURRENT_TIMESTAMP;
```

### How They Work
- **sb_created_at**: Set once when booking created
- **sb_updated_at**: Auto-updates on any change
- System compares these with last check time

---

## Notification Types

### New Booking Notification
**Appearance:**
- Purple gradient background
- Bell icon
- "New Booking!" or "X New Bookings!"
- Shows customer, phone, service

**Triggers:**
- Guest booking submitted
- Admin quick booking
- Any new booking creation

### Status Update Notification
**Appearance:**
- Pink gradient background
- Sync icon
- "Booking Updated!" or "X Bookings Updated!"
- Shows customer, phone, service, status

**Triggers:**
- Technician accepts booking
- Technician rejects booking
- Technician completes service
- Status changed to In Progress
- Any status change

---

## Testing

### Test New Booking
1. Open admin page (any page)
2. Open another browser/tab
3. Create a booking
4. Wait 10 seconds
5. Should see purple notification

### Test Status Update
1. Open admin page (any page)
2. Login as technician (another tab)
3. Accept or reject a booking
4. Go back to admin page
5. Wait 10 seconds
6. Should see pink notification

---

## Console Output

### On Any Admin Page
```
✅ Notification system active on this page
```

### Every 10 Seconds
```
Checking for notifications...
```

### When New Booking
```
🔔 NEW BOOKINGS: 1
[Notification details]
```

### When Status Update
```
🔄 STATUS UPDATES: 1
[Update details]
```

---

## Customization

### Change Check Interval
Edit `notification-system.php`:
```javascript
setInterval(checkNotifications, 10000); // Change 10000 to desired ms
```

### Change Colors
**New Booking (Purple):**
```javascript
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)
```

**Status Update (Pink):**
```javascript
background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%)
```

### Disable on Specific Pages
Remove from footer or add condition:
```php
<?php if(basename($_SERVER['PHP_SELF']) != 'specific-page.php'): ?>
    <?php include('notification-system.php'); ?>
<?php endif; ?>
```

---

## Benefits

### For Admin
✅ **Never miss a booking** - Notified immediately  
✅ **Track technician activity** - Know when status changes  
✅ **Work anywhere** - Notifications on all pages  
✅ **Stay informed** - Real-time updates  

### For Business
✅ **Faster response** - Immediate awareness  
✅ **Better tracking** - Monitor all changes  
✅ **Improved service** - Quick action on bookings  
✅ **Complete visibility** - Nothing slips through  

---

## Troubleshooting

### No Notifications on Some Pages
**Check:**
1. Is footer included? `<?php include("vendor/inc/footer.php"); ?>`
2. Is jQuery loaded?
3. Check browser console for errors

### Only New Bookings, No Updates
**Check:**
1. Does `sb_updated_at` column exist?
2. Run SQL to add column (see above)
3. Check console for errors

### Notifications Work on Dashboard Only
**Check:**
1. Other pages include footer?
2. Check if notification-system.php exists
3. Verify file path in footer include

---

## Response Format

### JSON Response
```json
{
  "success": true,
  "new_count": 1,
  "has_new": true,
  "bookings": [{
    "id": 123,
    "customer": "John Doe",
    "phone": "1234567890",
    "service": "Electrical Repair",
    "status": "Pending"
  }],
  "update_count": 2,
  "has_updates": true,
  "updates": [{
    "id": 124,
    "customer": "Jane Smith",
    "phone": "0987654321",
    "service": "AC Repair",
    "status": "In Progress"
  }]
}
```

---

## Performance

### Server Load
- **Request frequency:** Every 10 seconds
- **Query complexity:** Simple COUNT and SELECT
- **Data transfer:** ~1-2 KB per request
- **Impact:** Minimal

### Client Load
- **Memory:** ~2-3 MB
- **CPU:** Idle most of the time
- **Network:** ~360 requests/hour
- **Battery:** Negligible

---

## Future Enhancements

### Possible Additions
- [ ] Notification history
- [ ] Mark as read
- [ ] Notification preferences
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Slack integration
- [ ] Custom sound selection
- [ ] Notification grouping
- [ ] Priority levels

---

## Summary

✅ **Works on all admin pages**  
✅ **Tracks new bookings**  
✅ **Tracks status updates**  
✅ **Sound + visual alerts**  
✅ **Browser notifications**  
✅ **Badge counter**  
✅ **Auto-updates every 10 seconds**  
✅ **Low resource usage**  

**Admin stays informed everywhere, always!** 🔔✨

---

*Feature implemented: November 15, 2025*  
*Universal notifications active*
