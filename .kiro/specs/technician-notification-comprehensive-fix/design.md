# Design Document

## Overview

This design establishes a reliable, event-driven notification system for technicians that captures all booking state changes and delivers them through visual and audio alerts. The system uses a polling-based architecture with server-side event tracking to prevent duplicates while ensuring every significant booking event generates a notification.

The core innovation is a dual-timestamp approach: `sb_assigned_at` tracks initial and reassignment events, while `sb_updated_at` tracks all state changes. Combined with a notification tracking table that records specific event types, this ensures technicians receive distinct notifications for assignments, reassignments, holds, unholds, and status changes without duplication.

## Architecture

### System Components

1. **Server-Side Notification Detector** (`check-technician-notifications.php`)
   - Queries booking table for recent state changes
   - Filters out already-shown notifications using tracking table
   - Returns new notification events with action types and details
   - Marks returned notifications as shown in tracking table

2. **Client-Side Notification Poller** (`notification-system.php`)
   - Polls server every 5 seconds for new notifications
   - Displays visual toast notifications with booking details
   - Triggers audio alerts for new notifications
   - Updates notification badge counts

3. **Booking Assignment APIs** (`api-assign-booking.php`, `api-auto-assign-booking.php`)
   - Updates booking assignments with proper timestamps
   - Sets `sb_assigned_at` to current time on assignment/reassignment
   - Updates `sb_updated_at` to trigger notification detection

4. **Notification Tracking Database** (`tms_technician_notification_tracking`)
   - Stores shown notification events by technician, booking, action type, and status
   - Prevents duplicate notifications for same event
   - Auto-purges records older than 24 hours

### Data Flow

```
Booking State Change (Admin Action)
  ↓
Update booking timestamps (sb_assigned_at, sb_updated_at)
  ↓
Client polls check-technician-notifications.php
  ↓
Server queries bookings with recent updates
  ↓
Server filters out already-shown events using tracking table
  ↓
Server returns new notification events
  ↓
Server marks events as shown in tracking table
  ↓
Client displays visual notification + plays audio
  ↓
Client updates badge counts
```

## Components and Interfaces

### Database Schema

#### Booking Table Enhancements
```sql
ALTER TABLE tms_service_booking 
ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS sb_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL DEFAULT NULL;
```

#### Notification Tracking Table
```sql
CREATE TABLE IF NOT EXISTS tms_technician_notification_tracking (
    tnt_id INT AUTO_INCREMENT PRIMARY KEY,
    tnt_technician_id INT NOT NULL,
    tnt_booking_id INT NOT NULL,
    tnt_action_type VARCHAR(50) NOT NULL,
    tnt_booking_status VARCHAR(50) NOT NULL,
    tnt_shown_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tech_booking_action (tnt_technician_id, tnt_booking_id, tnt_action_type, tnt_booking_status),
    INDEX(tnt_technician_id),
    INDEX(tnt_booking_id),
    INDEX(tnt_shown_at)
);
```

### API Interfaces

#### Notification Check Endpoint
**Endpoint:** `GET /tech/check-technician-notifications.php`

**Response:**
```json
{
  "success": true,
  "notification_count": 2,
  "has_notifications": true,
  "notifications": [
    {
      "id": 123,
      "customer": "John Doe",
      "phone": "1234567890",
      "address": "123 Main St",
      "service": "AC Repair",
      "status": "Approved",
      "deadline_date": "2024-12-10",
      "deadline_time": "14:00:00",
      "message": "✨ New booking assigned to you",
      "action": "assigned",
      "updated_at": "2024-12-07 10:30:00",
      "assigned_at": "2024-12-07 10:30:00",
      "is_on_hold": 0,
      "is_high_priority": 0
    }
  ],
  "last_check": "2024-12-07 10:25:00",
  "current_time": "2024-12-07 10:30:00",
  "technician_id": 5
}
```

#### Assignment API Enhancement
**Endpoint:** `POST /admin/api-assign-booking.php`

**Request:**
```json
{
  "booking_id": 123,
  "technician_id": 5
}
```

**Response:**
```json
{
  "success": true,
  "message": "Booking assigned successfully",
  "booking_id": 123,
  "technician_id": 5
}
```

## Data Models

### Notification Event Model
```php
class NotificationEvent {
    public int $booking_id;
    public string $action_type;  // assigned, reassigned, hold, unhold, approved, in_progress, rejected
    public string $booking_status;
    public string $customer_name;
    public string $customer_phone;
    public string $customer_address;
    public string $service_name;
    public ?string $deadline_date;
    public ?string $deadline_time;
    public string $message;
    public string $updated_at;
    public ?string $assigned_at;
    public bool $is_on_hold;
    public bool $is_high_priority;
}
```

### Notification Tracking Record
```php
class NotificationTracking {
    public int $id;
    public int $technician_id;
    public int $booking_id;
    public string $action_type;
    public string $booking_status;
    public string $shown_at;
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Assignment creates notification event
*For any* booking assignment operation, when a booking is assigned to a technician, the system should update the sb_assigned_at timestamp to the current time and the sb_updated_at timestamp to the current time.
**Validates: Requirements 1.1, 10.1**

### Property 2: Reassignment creates distinct notification
*For any* booking that is reassigned from one technician to another, the system should create a notification event with action type "reassigned" for the new technician that is distinct from the original assignment notification.
**Validates: Requirements 2.1, 2.3, 10.3**

### Property 3: Hold status creates notification event
*For any* booking that is placed on hold, the system should update the sb_updated_at timestamp and create a notification event with action type "hold" that includes the hold reason.
**Validates: Requirements 3.1, 3.3, 10.4**

### Property 4: Unhold status creates notification event
*For any* booking that is removed from hold status, the system should update the sb_updated_at timestamp and create a notification event with action type "unhold".
**Validates: Requirements 4.1, 4.3, 10.4**

### Property 5: Status changes create notification events
*For any* booking status change to "Approved", "In Progress", or "Rejected", the system should update the sb_updated_at timestamp and create a notification event with the corresponding action type.
**Validates: Requirements 5.1, 5.2, 5.3, 10.2**

### Property 6: Notification tracking prevents duplicates
*For any* notification event that has been shown to a technician, when the notification polling system queries for new events, the system should exclude that specific event (matching booking ID, action type, and status) from the results.
**Validates: Requirements 6.2, 6.3**

### Property 7: Multiple state changes create separate notifications
*For any* booking that undergoes multiple different state changes, the system should create a separate notification event for each distinct change, where each event has a unique combination of action type and status.
**Validates: Requirements 6.5**

### Property 8: Notification display includes all required information
*For any* notification that is displayed to a technician, the notification should contain the booking ID, customer name, customer phone, service name, booking status, and deadline information if available.
**Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5**

### Property 9: Audio alert plays for new notifications
*For any* polling cycle that detects new notifications, if audio is enabled, the system should play the audio alert exactly once.
**Validates: Requirements 9.3**

### Property 10: Old tracking records are purged
*For any* notification tracking record with a shown_at timestamp older than 24 hours, the system should delete that record during the next notification check.
**Validates: Requirements 6.4**

## Error Handling

### Database Errors
- **Connection Failures**: Return JSON error response with success=false
- **Query Failures**: Log error to console, return empty notifications array
- **Transaction Failures**: Rollback changes, return error message to admin

### Client-Side Errors
- **AJAX Failures**: Log error to console, retry on next polling interval
- **JSON Parse Errors**: Log raw response, skip notification display
- **Audio Playback Failures**: Display visual prompt to enable sound, log error

### Edge Cases
- **Rapid State Changes**: Track each change separately with unique action type
- **Concurrent Assignments**: Use database transactions to ensure consistency
- **Browser Tab Hidden**: Continue polling at same frequency
- **Audio Blocked by Browser**: Display visual prompt, enable on user interaction

## Testing Strategy

### Unit Testing
Unit tests will verify specific examples and edge cases:

1. **Timestamp Update Tests**
   - Test that assignment sets sb_assigned_at to current time
   - Test that reassignment updates sb_assigned_at to new time
   - Test that all state changes update sb_updated_at

2. **Notification Detection Tests**
   - Test that new assignments are detected within 5 seconds
   - Test that reassignments are detected as separate events
   - Test that hold/unhold events are detected

3. **Duplicate Prevention Tests**
   - Test that shown notifications are not returned again
   - Test that same booking with different action types creates separate notifications
   - Test that tracking records older than 24 hours are deleted

### Property-Based Testing
Property-based tests will verify universal properties across all inputs using **fast-check** (JavaScript property testing library):

Each property-based test will run a minimum of 100 iterations to ensure comprehensive coverage.

Each property-based test will be tagged with a comment explicitly referencing the correctness property from this design document using the format: `**Feature: technician-notification-comprehensive-fix, Property {number}: {property_text}**`

1. **Property 1 Test: Assignment timestamp update**
   - Generate random booking IDs and technician IDs
   - Perform assignment operation
   - Verify sb_assigned_at and sb_updated_at are set to current time (within 1 second tolerance)

2. **Property 2 Test: Reassignment creates distinct notification**
   - Generate random booking with initial technician
   - Reassign to different technician
   - Verify new notification has action type "reassigned"
   - Verify notification is distinct from original assignment

3. **Property 3 Test: Hold creates notification**
   - Generate random booking IDs and hold reasons
   - Place booking on hold
   - Verify sb_updated_at is updated
   - Verify notification event has action type "hold" and includes reason

4. **Property 4 Test: Unhold creates notification**
   - Generate random booking IDs that are on hold
   - Remove hold status
   - Verify sb_updated_at is updated
   - Verify notification event has action type "unhold"

5. **Property 5 Test: Status changes create notifications**
   - Generate random booking IDs and status values (Approved, In Progress, Rejected)
   - Change booking status
   - Verify sb_updated_at is updated
   - Verify notification event has corresponding action type

6. **Property 6 Test: Tracking prevents duplicates**
   - Generate random notification events
   - Mark events as shown in tracking table
   - Query for new notifications
   - Verify shown events are excluded from results

7. **Property 7 Test: Multiple changes create separate notifications**
   - Generate random booking ID
   - Perform multiple different state changes
   - Verify each change creates a separate notification event
   - Verify each event has unique action type and status combination

8. **Property 8 Test: Notification contains required information**
   - Generate random notification events
   - Verify each notification contains booking ID, customer name, phone, service, status, and deadline

9. **Property 9 Test: Audio plays once per polling cycle**
   - Generate random number of new notifications
   - Simulate polling cycle
   - Verify audio play function is called exactly once

10. **Property 10 Test: Old records are purged**
    - Generate random tracking records with timestamps older than 24 hours
    - Run notification check
    - Verify old records are deleted from tracking table

### Integration Testing
Integration tests will verify the complete notification flow:

1. **End-to-End Assignment Flow**
   - Admin assigns booking to technician
   - Verify notification appears in technician portal within 5 seconds
   - Verify audio alert plays
   - Verify notification is marked as shown

2. **End-to-End Reassignment Flow**
   - Admin reassigns booking to different technician
   - Verify new technician receives "reassigned" notification
   - Verify original technician does not receive duplicate

3. **End-to-End Hold/Unhold Flow**
   - Admin places booking on hold
   - Verify technician receives hold notification with reason
   - Admin removes hold
   - Verify technician receives unhold notification

### Manual Testing Checklist
- [ ] Assign booking and verify notification appears
- [ ] Reassign booking and verify distinct notification
- [ ] Place booking on hold and verify notification with reason
- [ ] Remove hold and verify unhold notification
- [ ] Change booking status and verify notification
- [ ] Verify audio plays on new notifications
- [ ] Verify no duplicate notifications for same event
- [ ] Verify notification badge count updates correctly
- [ ] Verify system works across multiple browser tabs
- [ ] Verify system works when browser tab is hidden
