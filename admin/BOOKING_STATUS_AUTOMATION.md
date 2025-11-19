# Booking Status Automation System

## Overview
The booking status is now **fully automated** based on technician assignment and service completion. Both customers and admins can see live status updates in real-time.

## Automated Status Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    BOOKING STATUS WORKFLOW                   │
└─────────────────────────────────────────────────────────────┘

1. NEW BOOKING (No Technician)
   Status: Pending ⏳
   ├─ Customer creates booking
   ├─ No technician assigned yet
   └─ Waiting for admin to assign technician

2. TECHNICIAN ASSIGNED
   Status: Approved ✅
   ├─ Admin assigns technician
   ├─ Status automatically changes to "Approved"
   └─ Technician can now start work

3. TECHNICIAN REJECTS / CAN'T COMPLETE
   Status: Not Completed ⚠️
   ├─ Technician rejects booking
   ├─ Technician removed from booking
   ├─ Status changes to "Not Completed"
   └─ Admin can reassign to another technician

4. TECHNICIAN REASSIGNED
   Status: Approved ✅ (again)
   ├─ Admin assigns new technician
   ├─ Status automatically changes back to "Approved"
   └─ New technician can complete the work

5. SERVICE COMPLETED
   Status: Completed ✓
   ├─ Technician marks as completed
   ├─ Status changes to "Completed"
   └─ Booking is finalized
```

## Status Definitions

### Pending ⏳
- **When**: New booking created, no technician assigned
- **Color**: Yellow/Warning
- **Icon**: Clock
- **Customer Sees**: "Awaiting Technician Assignment"
- **Admin Action**: Assign a technician

### Approved ✅
- **When**: Technician assigned to booking
- **Color**: Blue/Info
- **Icon**: Check Circle
- **Customer Sees**: "Technician Assigned"
- **Admin Action**: Monitor progress

### In Progress 🔧
- **When**: Technician actively working (optional status)
- **Color**: Blue/Primary
- **Icon**: Tools
- **Customer Sees**: "Work in Progress"
- **Admin Action**: Monitor completion

### Not Completed ⚠️
- **When**: Technician rejects or can't complete
- **Color**: Red/Danger
- **Icon**: Exclamation Triangle
- **Customer Sees**: "Service Not Completed - Reassigning"
- **Admin Action**: Reassign to another technician

### Completed ✓
- **When**: Service successfully completed
- **Color**: Green/Success
- **Icon**: Check Double
- **Customer Sees**: "Service Completed Successfully"
- **Admin Action**: None (finalized)

### Cancelled 🚫
- **When**: Booking cancelled by customer or admin
- **Color**: Gray/Secondary
- **Icon**: Ban
- **Customer Sees**: "Booking Cancelled"
- **Admin Action**: None (finalized)

## Automatic Triggers

### 1. When Booking is Created
```php
// New booking automatically gets "Pending" status
$sb_status = 'Pending';
```

### 2. When Technician is Assigned
```php
// Status automatically changes to "Approved"
$auto_status = $sb_technician_id > 0 ? 'Approved' : 'Pending';
```

### 3. When Technician Rejects
```php
// Status changes to "Not Completed"
// Technician is removed from booking
UPDATE tms_service_booking 
SET sb_status = 'Not Completed', 
    sb_technician_id = NULL
```

### 4. When Technician is Reassigned
```php
// Status automatically changes back to "Approved"
$auto_status = 'Approved';
```

### 5. When Service is Completed
```php
// Status changes to "Completed"
UPDATE tms_service_booking 
SET sb_status = 'Completed'
```

## Live Status for Customers

### Customer Status Page
**URL**: `usr/live-booking-status.php?booking_id=123&phone=1234567890`

**Features**:
- ✅ Real-time status updates (auto-refresh every 10 seconds)
- ✅ Visual status indicator with color and icon
- ✅ Booking details (service, date, time, price)
- ✅ Assigned technician info (name, phone, EZ ID)
- ✅ Timeline of booking events
- ✅ Cancel button (if applicable)

**Status Messages**:
```
Pending:
  "Awaiting Technician Assignment"
  "Your booking is confirmed. We are finding the best technician for you."

Approved:
  "Technician Assigned"
  "A technician has been assigned. They will contact you soon."

Not Completed:
  "Service Not Completed"
  "The technician was unable to complete the service. 
   We will assign another technician shortly."

Completed:
  "Service Completed"
  "Your service has been completed successfully. Thank you!"
```

### Customer API Endpoint
**URL**: `usr/api-get-booking-status.php`

**Parameters**:
- `booking_id` - Booking ID
- `phone` - Customer phone number (for verification)

**Response**:
```json
{
  "success": true,
  "booking": {
    "id": 123,
    "status": "Approved",
    "status_color": "info",
    "status_icon": "fa-check-circle",
    "status_message": "Technician Assigned",
    "status_description": "A technician has been assigned...",
    "service_name": "AC Repair",
    "booking_date": "Nov 20, 2025",
    "booking_time": "02:00 PM",
    "technician": {
      "name": "John Doe",
      "phone": "9876543210",
      "ez_id": "EZ0001"
    },
    "timeline": [...],
    "can_cancel": true
  }
}
```

## Live Status for Admin

### Admin API Endpoint
**URL**: `admin/api-get-live-booking-status.php`

**Parameters**:
- `booking_id` - Booking ID

**Response**:
```json
{
  "success": true,
  "booking": {
    "id": 123,
    "status": "Approved",
    "status_badge": "info",
    "status_icon": "fa-check-circle",
    "status_message": "Technician Assigned - Awaiting Service",
    "next_action": "Technician will complete the service",
    "service": {...},
    "schedule": {...},
    "customer": {...},
    "technician": {
      "id": 5,
      "name": "John Doe",
      "status": "Booked",
      "current_bookings": 2,
      "booking_limit": 3,
      "availability": "Available"
    },
    "timestamps": {...},
    "notes": {...}
  }
}
```

## Integration Points

### 1. Booking Creation
**File**: `process-guest-booking.php`
```php
// New bookings start as "Pending"
$sb_status = 'Pending';
```

### 2. Technician Assignment
**File**: `admin/admin-assign-technician.php`
```php
// Auto-set status to "Approved" when technician assigned
$auto_status = $sb_technician_id > 0 ? 'Approved' : 'Pending';
```

### 3. Technician Rejection
**File**: `tech/api-reject-booking.php`
```php
// Set status to "Not Completed" and remove technician
UPDATE tms_service_booking 
SET sb_status = 'Not Completed', 
    sb_technician_id = NULL
```

### 4. Service Completion
**File**: `tech/api-complete-booking.php`
```php
// Set status to "Completed"
UPDATE tms_service_booking 
SET sb_status = 'Completed'
```

### 5. Auto-Update Script
**File**: `admin/auto-update-booking-status.php`
```php
// Syncs all booking statuses based on technician assignment
include('auto-update-booking-status.php');
```

## Customer Experience

### Booking Journey
```
1. Customer creates booking
   → Sees: "Pending - Awaiting Technician"
   → Can: Cancel booking

2. Admin assigns technician
   → Sees: "Approved - Technician Assigned"
   → Gets: Technician name and phone
   → Can: Cancel booking

3. Technician rejects (if happens)
   → Sees: "Not Completed - Reassigning"
   → Gets: Notification about reassignment
   → Can: Wait for new technician

4. Admin reassigns technician
   → Sees: "Approved - New Technician Assigned"
   → Gets: New technician details
   → Can: Cancel booking

5. Technician completes service
   → Sees: "Completed - Service Done"
   → Gets: Completion confirmation
   → Can: Rate technician, view invoice
```

## Admin Dashboard Integration

### Real-Time Monitoring
Admins can see live status of all bookings:

```
Dashboard Cards:
├─ Pending Bookings (Yellow) - Need technician assignment
├─ Approved Bookings (Blue) - Technician assigned, awaiting completion
├─ Not Completed (Red) - Need reassignment
└─ Completed Bookings (Green) - Finalized
```

### Status Filters
```
View All Bookings:
├─ Filter by: Pending
├─ Filter by: Approved
├─ Filter by: Not Completed
├─ Filter by: Completed
└─ Filter by: Cancelled
```

## Database Schema

### Required Columns
```sql
ALTER TABLE tms_service_booking
ADD COLUMN IF NOT EXISTS sb_status VARCHAR(50) DEFAULT 'Pending',
ADD COLUMN IF NOT EXISTS sb_technician_id INT NULL,
ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS sb_rejected_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS sb_completed_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS sb_cancelled_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS sb_rejection_reason TEXT NULL,
ADD COLUMN IF NOT EXISTS sb_completion_notes TEXT NULL;
```

### Status Values
```sql
-- Valid status values
'Pending'         -- No technician assigned
'Approved'        -- Technician assigned
'In Progress'     -- Work started (optional)
'Not Completed'   -- Technician rejected/couldn't complete
'Completed'       -- Service completed
'Cancelled'       -- Booking cancelled
'Rejected'        -- Booking rejected by admin
```

## Testing

### Test Scenario 1: New Booking
1. Create new booking
2. Check status → Should be "Pending"
3. Customer sees "Awaiting Technician Assignment"

### Test Scenario 2: Assign Technician
1. Admin assigns technician
2. Check status → Should be "Approved"
3. Customer sees "Technician Assigned" with tech details

### Test Scenario 3: Technician Rejects
1. Technician rejects booking
2. Check status → Should be "Not Completed"
3. Technician removed from booking
4. Customer sees "Service Not Completed - Reassigning"

### Test Scenario 4: Reassign Technician
1. Admin assigns new technician
2. Check status → Should be "Approved" again
3. Customer sees new technician details

### Test Scenario 5: Complete Service
1. Technician completes service
2. Check status → Should be "Completed"
3. Customer sees "Service Completed Successfully"

### Test Live Status Page
```
1. Open: usr/live-booking-status.php?booking_id=123&phone=1234567890
2. Verify: Status displays correctly
3. Verify: Auto-refresh works (every 10 seconds)
4. Verify: Timeline shows all events
5. Verify: Technician info displays (if assigned)
```

## Benefits

### For Customers
- ✅ Always know booking status
- ✅ Real-time updates without calling
- ✅ See assigned technician details
- ✅ Track booking progress
- ✅ Transparent communication

### For Admins
- ✅ No manual status updates
- ✅ Clear workflow visibility
- ✅ Easy to identify pending actions
- ✅ Automated status transitions
- ✅ Better booking management

### For Technicians
- ✅ Clear booking status
- ✅ Easy reject/complete actions
- ✅ Automatic availability updates
- ✅ Transparent workflow

## Troubleshooting

### Status Not Updating?
1. Check if auto-update script is included
2. Verify database columns exist
3. Check technician assignment
4. Review error logs

### Customer Can't See Status?
1. Verify booking ID and phone number
2. Check API endpoint response
3. Verify booking exists in database
4. Check session/authentication

### Manual Status Update (if needed)
```php
// Include auto-update script
include('admin/auto-update-booking-status.php');

// Or update specific booking
$booking_id = 123;
include('admin/auto-update-booking-status.php');
```

## Future Enhancements

Possible improvements:
- SMS notifications on status change
- Email notifications
- Push notifications (mobile app)
- Real-time WebSocket updates
- Customer rating after completion
- Automated follow-up messages

---

**Last Updated**: November 19, 2025
**Version**: 2.0 (Fully Automated)
