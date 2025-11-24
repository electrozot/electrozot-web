# Technician Status Auto-Update System

## Overview
The technician availability status is now **fully automated** based on active bookings. Admins no longer need to manually update technician status.

## How It Works

### Automatic Status Logic
```
Available: technician has capacity for more bookings
  - current_bookings < booking_limit
  - Example: 2 active bookings, limit is 5 → Available

Booked: technician is at full capacity
  - current_bookings >= booking_limit
  - Example: 3 active bookings, limit is 3 → Booked
```

### Active Bookings Count
Only bookings with these statuses count as "active":
- Pending
- Approved
- In Progress

Completed, Rejected, and Cancelled bookings do NOT count.

## Changes Made

### 1. Add Technician Form (`admin-add-technician.php`)
- ✅ **Removed** manual status dropdown
- ✅ **Added** read-only status field showing "Available (Auto-managed)"
- ✅ New technicians automatically start as "Available"
- ✅ Status updates automatically when bookings are assigned

### 2. Edit Technician Form (`admin-manage-single-technician.php`)
- ✅ **Removed** manual status dropdown
- ✅ **Added** read-only status display with current booking count
- ✅ Shows: "Current: X / Y bookings" (e.g., "2 / 5 bookings")
- ✅ Status syncs automatically after form submission

### 3. Auto-Update Scripts

#### `auto-update-technician-status.php` (NEW)
Main script that updates all technician statuses:
```php
// Updates current booking counts
// Updates status based on capacity
// Updates is_available flag
```

#### `auto-fix-technician-slots.php` (UPDATED)
Runs automatically on dashboard and manage pages:
- Syncs every 30 seconds (instead of once per session)
- Ensures real-time status updates

#### `api-update-technician-status.php` (NEW)
API endpoint for manual triggers:
```
GET /admin/api-update-technician-status.php
Returns: { success: true, stats: {...}, timestamp: "..." }
```

### 4. Automatic Triggers
Status updates automatically when:
- ✅ Booking is assigned to technician
- ✅ Booking status changes (Completed, Rejected, etc.)
- ✅ Technician is reassigned
- ✅ Admin views dashboard (every 30 seconds)
- ✅ Admin views manage technicians page (every 30 seconds)
- ✅ Technician form is submitted

## Benefits

### For Admins
- ✅ No manual status updates needed
- ✅ Always accurate availability information
- ✅ Prevents double-booking automatically
- ✅ Real-time status updates
- ✅ Less administrative work

### For System
- ✅ Consistent data across all pages
- ✅ Prevents human error
- ✅ Automatic capacity management
- ✅ Better booking assignment logic

## Examples

### Example 1: Single Booking Technician
```
Booking Limit: 1
Current Bookings: 0
Status: Available ✅

→ Assign 1 booking
Current Bookings: 1
Status: Booked 🔴

→ Complete booking
Current Bookings: 0
Status: Available ✅
```

### Example 2: Multi-Booking Technician
```
Booking Limit: 3
Current Bookings: 0
Status: Available ✅

→ Assign 1 booking
Current Bookings: 1
Status: Available ✅ (still has capacity)

→ Assign 2 more bookings
Current Bookings: 3
Status: Booked 🔴 (at capacity)

→ Complete 1 booking
Current Bookings: 2
Status: Available ✅ (has capacity again)
```

## Testing

### Test the Auto-Update
1. Go to **Manage Technicians**
2. Note a technician's status and booking count
3. Assign a booking to that technician
4. Return to Manage Technicians
5. Status should update automatically

### Test API Endpoint
```bash
# Call the API (requires admin login)
curl http://your-domain/admin/api-update-technician-status.php

# Response:
{
  "success": true,
  "message": "Technician statuses updated successfully",
  "stats": {
    "total": 10,
    "available": 7,
    "booked": 3,
    "pending": 0
  },
  "timestamp": "2025-11-19 10:30:45"
}
```

## Monitoring

### Check Status Sync
The system logs sync operations. Check:
- Dashboard loads → Status syncs
- Manage Technicians page → Status syncs
- Booking assignment → Status syncs immediately

### Verify Accuracy
Compare these values for each technician:
- `t_current_bookings` (database column)
- Active bookings count (query result)
- Status display (Available/Booked)

They should always match!

## Troubleshooting

### Status Not Updating?
1. Check if `auto-fix-technician-slots.php` is included in dashboard
2. Verify database columns exist:
   - `t_booking_limit`
   - `t_current_bookings`
   - `t_status`
3. Check session variable: `$_SESSION['technician_slots_synced_time']`

### Manual Sync (if needed)
```php
// Include this in any admin page
include('auto-update-technician-status.php');
```

Or call the API:
```javascript
fetch('api-update-technician-status.php')
  .then(r => r.json())
  .then(data => console.log('Synced:', data));
```

## Database Schema

### Required Columns
```sql
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_booking_limit INT NOT NULL DEFAULT 1,
ADD COLUMN IF NOT EXISTS t_current_bookings INT NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS t_status VARCHAR(50) DEFAULT 'Available',
ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1;
```

### Status Values
- `Available` - Can accept more bookings
- `Booked` - At full capacity
- `Pending` - Guest technician awaiting approval
- `Rejected` - Guest technician rejected

## Migration Notes

### Old System
- Manual status dropdown in forms
- Admin had to remember to update status
- Status could become out of sync
- Risk of double-booking

### New System
- Automatic status based on bookings
- No manual intervention needed
- Always accurate and in sync
- Prevents over-booking automatically

## Future Enhancements

Possible improvements:
- Real-time WebSocket updates
- Status change notifications
- Capacity planning dashboard
- Predictive availability forecasting
- Mobile app integration

## Support

If you encounter issues:
1. Check this documentation
2. Verify database schema
3. Test the API endpoint
4. Check browser console for errors
5. Review PHP error logs

---

**Last Updated:** November 19, 2025
**Version:** 2.0 (Fully Automated)
