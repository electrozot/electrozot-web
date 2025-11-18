# Real-Time Admin Notification System

## Overview
Admin receives instant notifications with sound alerts when technicians reject or complete bookings, regardless of which page is open.

## Features

### ✅ Real-Time Notifications
- Checks for new notifications every 5 seconds
- Works on ALL admin pages
- No page refresh needed

### ✅ Sound Alerts
- Plays double-beep sound when new notification arrives
- Uses Web Audio API (works without audio files)
- Frequency: 800Hz + 1000Hz beeps

### ✅ Browser Notifications
- Shows desktop/mobile notifications even when device is locked
- Requires user permission (requested automatically)
- Click notification to go directly to booking

### ✅ Visual Indicators
- Red badge on bell icon shows unread count
- Animated pulse effect
- Updates in real-time

## How It Works

### 1. Technician Actions Trigger Notifications

**When Technician Rejects Booking:**
```
Technician clicks Reject → API creates notification → Admin sees alert
```

**When Technician Completes Booking:**
```
Technician clicks Complete → API creates notification → Admin sees alert
```

### 2. Notification Flow
```
1. Technician action (reject/complete)
2. Insert into tms_admin_notifications table
3. Admin page checks API every 5 seconds
4. New notification detected
5. Play sound + Show browser notification
6. Update badge count
```

### 3. Database Table
```sql
CREATE TABLE tms_admin_notifications (
    an_id INT PRIMARY KEY AUTO_INCREMENT,
    an_type VARCHAR(50),           -- BOOKING_REJECTED, BOOKING_COMPLETED
    an_title VARCHAR(255),          -- "Booking Rejected by Technician"
    an_message TEXT,                -- "John Doe rejected Booking #123"
    an_booking_id INT,              -- Link to booking
    an_technician_id INT,           -- Who triggered it
    an_is_read TINYINT(1),         -- 0 = unread, 1 = read
    an_created_at TIMESTAMP
)
```

## Notification Types

### 🔴 BOOKING_REJECTED
- **Title:** "Booking Rejected by Technician"
- **Message:** "[Technician Name] rejected Booking #[ID]. Reason: [Reason]"
- **Icon:** ❌ Red X
- **Action:** Click to view booking details

### 🟢 BOOKING_COMPLETED
- **Title:** "Booking Completed"
- **Message:** "[Technician Name] completed Booking #[ID] successfully!"
- **Icon:** ✅ Green Check
- **Action:** Click to view booking details

## User Experience

### Admin Dashboard
```
[Bell Icon with Badge: 3]
```

### When New Notification Arrives:
1. **Sound:** Double beep (800Hz + 1000Hz)
2. **Visual:** Badge count increases
3. **Browser:** Desktop notification appears
4. **Badge:** Animated pulse effect

### Browser Notification (Even When Locked):
```
┌─────────────────────────────────┐
│ 🔔 Booking Rejected by Technician│
│                                  │
│ John Doe rejected Booking #123.  │
│ Reason: Customer not available    │
│                                  │
│ [Click to view]                  │
└─────────────────────────────────┘
```

## API Endpoints

### 1. Get Notifications
**File:** `admin/api-admin-notifications.php`
**Method:** GET
**Response:**
```json
{
    "success": true,
    "notifications": [
        {
            "an_id": 1,
            "an_type": "BOOKING_REJECTED",
            "an_title": "Booking Rejected",
            "an_message": "John rejected Booking #123",
            "an_booking_id": 123,
            "an_is_read": 0,
            "an_created_at": "2024-11-18 10:30:00"
        }
    ],
    "count": 1
}
```

### 2. Mark as Read
**File:** `admin/api-mark-notification-read.php`
**Method:** POST
**Parameters:**
- `notification_id` (optional) - Mark specific notification
- Empty body - Mark all as read

## Browser Notification Permissions

### First Time:
```
Browser asks: "Allow notifications from this site?"
User clicks: "Allow"
```

### Permissions:
- **Granted:** Shows desktop notifications
- **Denied:** Only shows in-app notifications
- **Default:** Asks for permission

## Sound Generation

Uses Web Audio API to generate beep sounds:
```javascript
Frequency 1: 800Hz (0.5 seconds)
Frequency 2: 1000Hz (0.3 seconds)
Delay: 200ms between beeps
```

## Works Even When:
✅ Admin is on any page
✅ Device is locked (if browser notifications enabled)
✅ Tab is in background
✅ Internet is connected
✅ Multiple tabs open (all get notified)

## Integration Points

### Files Modified:
1. **tech/api-reject-booking.php** - Creates notification on reject
2. **tech/api-complete-booking.php** - Creates notification on complete
3. **admin/vendor/inc/nav.php** - Notification system script
4. **admin/api-admin-notifications.php** - Get notifications API
5. **admin/api-mark-notification-read.php** - Mark read API

## Testing

### Test Rejection:
1. Login as technician
2. Reject a booking
3. Check admin panel
4. Should hear beep + see notification

### Test Completion:
1. Login as technician
2. Complete a booking
3. Check admin panel
4. Should hear beep + see notification

### Test Browser Notification:
1. Lock device
2. Have technician reject/complete booking
3. Should see notification on lock screen

## Future Enhancements
- SMS notifications
- Email notifications
- WhatsApp notifications
- Custom sound selection
- Notification history page
- Filter by type
- Snooze notifications
